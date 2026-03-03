<?php
namespace App\Models;
use App\Core\Database;

class BaseModel {
    public $db;
    protected $_tbl = '';
    protected $_tbl_key = 'id';

    public function __construct($table, $key = 'id') {
        $this->db = Database::getInstance();
        $this->_tbl = $table;
        $this->_tbl_key = $key;
    }

    /**
     * Dışarıdan gelen veriyi (POST verisi veya nesne) model özelliklerine bağlar.
     */
    public function bind($data) {
    $data = (array) $data;
    
    foreach ($data as $key => $value) {
        if (property_exists($this, $key)) {
            
            // 1. Değeri temizle (Başındaki/sonundaki boşlukları al)
            $cleanValue = is_string($value) ? trim($value) : $value;

            // 2. Boşluk Kontrolü: Eğer değer boş string, "NULL" kelimesi 
            // veya 1970 tarihi ise nesne özelliğini gerçek NULL yap.
            if ($cleanValue === '' || $cleanValue === 'NULL' || $cleanValue === '1970.01.01') {
                $this->$key = null;
            } 
            // 3. ID alanı özel kontrolü
            elseif (($key === $this->_tbl_key || $key === 'id') && empty($cleanValue)) {
                $this->$key = null;
            } 
            else {
                // Değer doluysa ata
                $this->$key = $cleanValue;
            }
        }
    }
}

    /**
     * Veriyi veritabanına kaydeder. 
     * ID varsa günceller, yoksa yeni kayıt açar.
     */
    public function store($updateNulls = false) {
        $k = $this->_tbl_key;

        if ($this->$k) {
            // ID varsa: Database.php içindeki modern updateObject'i kullan
            return $this->db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);
        } else {
            // ID yoksa: Database.php içindeki modern insertObject'i kullan
            return $this->db->insertObject($this->_tbl, $this, $this->_tbl_key);
        }
    }

    /**
     * bind ve store işlemlerini tek seferde yapar.
     */
    public function save($data) {
        $this->bind($data);
        return $this->store();
    }

    /**
     * Veritabanından belirli bir ID'ye göre veriyi çeker ve modele yükler.
     */
    public function load($id) {
        $id = (int) $id;
        $query = "SELECT * FROM {$this->_tbl} WHERE {$this->_tbl_key} = " . $this->db->quote($id);
        $res = $this->db->setQuery($query)->loadObject();

        if ($res) {
            $this->bind($res);
            return true;
        }
        return false;
    }

    /**
     * Mevcut kaydı siler.
     */
    public function delete($id = null) {
        $id = ($id !== null) ? (int)$id : (isset($this->{$this->_tbl_key}) ? (int)$this->{$this->_tbl_key} : null);

        if ($id) {
            $sql = "DELETE FROM {$this->_tbl} WHERE {$this->_tbl_key} = " . $this->db->quote($id);
            if ($this->db->setQuery($sql)->query()) {
                $this->reset(); // Nesneyi temizle
                return true;
            }
        }
        return false;
    }

    /**
     * Nesne içindeki verileri sıfırlar.
     */
    public function reset() {
        foreach (get_object_vars($this) as $k => $v) {
            if ($k[0] != '_' && $k != 'db') {
                $this->$k = null;
            }
        }
    }
}