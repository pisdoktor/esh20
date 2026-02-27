<?php
namespace App\Models;

class User extends BaseModel {
    public $id = null;
    public $username = null;
    public $password = null;
    public $name = null;
    public $tckimlikno = null;
    public $email = null;
    public $image = null;
    public $nowvisit = null;
    public $lastvisit = null;
    public $registerDate = null;
    public $activated = 0;
    public $activation = null;
    public $isadmin = 0;

    public function __construct() {
        parent::__construct('esh_users', 'id');
    }

    /**
     * Kullanıcıyı kullanıcı adına göre yükler (Login için kritik)
     */
    public function loadByUsername($username) {
        $query = "SELECT * FROM esh_users WHERE username = " . $this->db->quote($username);
        $data = $this->db->setQuery($query)->loadAssoc();
        if ($data) {
            $this->bind($data);
            return true;
        }
        return false;
    }
    /**
     * Profil bilgilerini günceller
     */
    public function updateProfile($id, $data) {
        if ($this->load($id)) {
            $this->bind($data);
            return $this->store();
        }
        return false;
    }

    /**
     * Şifreyi güvenli bir şekilde hashleyerek günceller
     */
    public function updatePassword($id, $newPassword) {
        if ($this->load($id)) {
            $this->password = password_hash($newPassword, PASSWORD_DEFAULT);
            return $this->store();
        }
        return false;
    }

    /**
     * Kullanıcı fotoğraf yolunu günceller
     */
    public function updatePhoto($id, $path) {
        if ($this->load($id)) {
            $this->image = $path; // Özellik ismiyle uyumlu hale getirildi
            return $this->store();
        }
        return false;
    }

    /**
     * Kullanıcı giriş yaptığında ziyaret tarihlerini günceller
     */
    public function updateVisitDate($id) {
        if ($this->load($id)) {
            $this->lastvisit = $this->nowvisit;
            $this->nowvisit = date('Y-m-d H:i:s');
            return $this->store();
        }
        return false;
    }
    
    /**
     * E-posta adresine göre kullanıcıyı bulur
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM esh_users WHERE email = " . $this->db->quote($email);
        return $this->db->setQuery($query)->loadObject();
    }

    /**
     * Şifre sıfırlama kodu oluşturur ve kullanıcıya atar
     */
    public function createResetToken($id) {
        if ($this->load($id)) {
            // Güvenli, rastgele 12 haneli bir kod üretir
            $token = substr(bin2hex(random_bytes(6)), 0, 12);
            $this->activation = $token;
            return $this->store() ? $token : false;
        }
        return false;
    }

    /**
     * Token ile kullanıcıyı doğrular ve şifreyi günceller
     */
    public function resetPasswordWithToken($token, $newPassword) {
        $query = "SELECT id FROM esh_users WHERE activation = " . $this->db->quote($token);
        $userId = $this->db->setQuery($query)->loadResult();

        if ($userId) {
            $this->load($userId);
            $this->password = password_hash($newPassword, PASSWORD_DEFAULT); // Şifreyi hashleyerek kaydeder
            $this->activation = null; // Token'ı kullanıldıktan sonra temizler
            return $this->store();
        }
        return false;
    }
    
    /**
     * Tüm kullanıcıları (personelleri) listeler
     * Admin panelindeki kullanıcı yönetimi için kullanılır.
     */
    public function getList() {
        // Güvenlik için şifre (password) alanını çekmiyoruz
        $query = "SELECT * FROM esh_users ORDER BY name ASC";
        return $this->db->setQuery($query)->loadObjectList();
    }
}