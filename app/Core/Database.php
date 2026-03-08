<?php
namespace App\Core;
use PDO;
use Exception;
use stdClass;

class Database {
    private static $instance = null;
    private $pdo;
    private $stmt = null;
    private $_sql = '';
    private $_limit = 0;
    private $_offset = 0;
    private $_table_prefix = 'esh_'; // Veritabanı tablo ön ekiniz
    
    // Eski sistemle tam uyum için hata ve log değişkenleri
    private $_errorNum = 0;
    private $_errorMsg = '';
    private $_log = [];
    private $_debug = 1; 

    private function __construct() {
        try {
            $dsn = "mysql:host=" . \DB_HOST . ";dbname=" . \DB_NAME . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, \DB_USER, \DB_PASS);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_turkish_ci");
        } catch (Exception $e) {
            $this->handleError($e, "Bağlantı Hatası");
            die("Kritik Veritabanı Hatası! Lütfen yöneticiye bildiriniz.");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) { self::$instance = new self(); }
        return self::$instance;
    }

    /**
     * Hata Yönetimi: Hem dosyaya yazar hem sınıf değişkenlerini doldurur.
     */
    private function handleError($e, $context = "Sorgu Hatası") {
        $this->_errorNum = $e->getCode();
        $this->_errorMsg = $e->getMessage();
        
        $logPath = __DIR__ . '/../../logs/'; 
        if (!is_dir($logPath)) { mkdir($logPath, 0777, true); }
        $logFile = $logPath . 'db_errors.log';
        
        $time = date('Y-m-d H:i:s');
        $logMessage = "[$time] $context: " . $this->_errorMsg . " | SQL: " . $this->_sql . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    private function clearErrors() {
        $this->_errorNum = 0;
        $this->_errorMsg = '';
    }

    // --- SORGUBALAMA VE ÇALIŞTIRMA ---

    public function setQuery($sql, $offset = 0, $limit = 0) {
        $this->_sql = $this->replacePrefix($sql);
        $this->_offset = (int)$offset;
        $this->_limit = (int)$limit;
        return $this;
    }

    public function query($sql = null) {
        $this->clearErrors();
        $querySql = $sql ? $this->replacePrefix($sql) : $this->applyLimit($this->_sql);
        if ($this->_debug) { $this->_log[] = $querySql; }
        try {
            $this->stmt = $this->pdo->query($querySql);
            return $this->stmt;
        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    // --- VERİ ÇEKME METOTLARI (Eski Dosyadaki Tüm Fonksiyonlar) ---

    public function loadObjectList() {
        $this->query();
        return $this->stmt ? $this->stmt->fetchAll(PDO::FETCH_OBJ) : [];
    }

    public function loadObject() {
        $this->query();
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_OBJ) : null;
    }

    public function loadAssocList($key = '') {
        $this->query();
        if (!$this->stmt) return [];
        $results = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($key)) return $results;
        $indexed = [];
        foreach ($results as $row) { $indexed[$row[$key]] = $row; }
        return $indexed;
    }

    public function loadAssoc() {
        $this->query();
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_ASSOC) : null;
    }

    public function loadRowList() {
        $this->query();
        return $this->stmt ? $this->stmt->fetchAll(PDO::FETCH_NUM) : [];
    }

    public function loadRow() {
        $this->query();
        return $this->stmt ? $this->stmt->fetch(PDO::FETCH_NUM) : null;
    }

    public function loadResult() {
        $this->query();
        return $this->stmt ? $this->stmt->fetchColumn() : null;
    }

    public function loadResultArray($colIndex = 0) {
        $this->query();
        return $this->stmt ? $this->stmt->fetchAll(PDO::FETCH_COLUMN, $colIndex) : [];
    }

    // --- NESNE KAYIT VE GÜNCELLEME (Legacy insertObject/updateObject) ---
    public function insertObject($table, $data, $keyName = null) {
        $fields = []; $values = []; $placeholders = [];
        
        foreach ($data as $k => $v) {
            if ($k[0] == '_') continue; // Gizli alanları atla
            $fields[] = "`$k`";
            $placeholders[] = "?";
            $values[] = $v;
        }
        
        if (empty($fields)) return false;

        $sql = "INSERT INTO " . $this->replacePrefix($table) . " (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            $this->handleError($e, "insertObject Hatası");
            return false;
        }
    }

    public function updateObject($table, $data, $keyName, $keyValue) {
        $sets = []; $values = [];
        
        foreach ($data as $k => $v) {
            if ($k[0] == '_') continue;
            $sets[] = "`$k` = ?";
            $values[] = $v;
        }
        
        if (empty($sets)) return false;

        $values[] = $keyValue; // WHERE şartı için ID değeri
        $sql = "UPDATE " . $this->replacePrefix($table) . " SET " . implode(',', $sets) . " WHERE `$keyName` = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            $this->handleError($e, "updateObject Hatası");
            return false;
        }
    }

    // --- YARDIMCI ARAÇLAR ---

    public function getNumRows() { return $this->stmt ? $this->stmt->rowCount() : 0; }
    public function insertid() { return $this->pdo->lastInsertId(); }
    public function affectedRows() { return $this->stmt ? $this->stmt->rowCount() : 0; }
    public function getAffectedRows() { return $this->affectedRows(); } // Alias
    
    public function quote($text) { return $this->pdo->quote($text ?? ''); }
    public function getEscaped($text) { return trim($this->quote($text), "'"); } // Eski sistem için escape
    
    public function replacePrefix($sql, $prefix = '#__') {
        return str_replace($prefix, $this->_table_prefix, trim($sql));
    }

    private function applyLimit($sql) {
        if ($this->_limit > 0) { // Limit mutlaka olmalı
            return $sql . " LIMIT " . (int)$this->_offset . ", " . (int)$this->_limit;
        }
        return $sql;
}

    public function stderr($showSQL = false) {
        return "DB Hatası ($this->_errorNum): <font color='red'>$this->_errorMsg</font>" . 
               ($showSQL ? "<br/>SQL: <pre>$this->_sql</pre>" : '');
    }

    public function getErrorMsg() { return $this->_errorMsg; }
    
    /**
     * Debug modu aktifse tüm sorgu günlüğünü döndürür
     */
    public function getQueryLog() {
        return $this->_log;
    }

    /**
     * Debug modunun durumunu kontrol eder
     */
    public function isDebug() {
        return $this->_debug == 1;
    }
}