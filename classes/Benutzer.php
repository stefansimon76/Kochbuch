<?php
declare(strict_types=1);

class Benutzer {
    public $pk = 0;
    public $realname = '';
    public $loginname = '';
    public $password_hash = '';
    public $new_password = '';
    public $email = '';
    public $vkey = '';
    public $verified = false;
    public $token = '';
    public $lastLogin;
    public $created;

    public static function createAccount(String $realname, String $loginname, String $password, String $email): Benutzer {
        $vkey = md5(time().$loginname);
        $sql = sprintf("INSERT INTO `tab_benutzer` "
            . "SET loginname='%s',"
            . "realname='%s',"
            . "email='%s',"
            . "password_hash='%s',"
            . "vkey='%s',"
            . "created=NOW();",
            escapeString($loginname),
            escapeString($realname),
            escapeString($email),
            self::hashPassword($password),
            escapeString($vkey)
        );

        query($sql);
        return self::findByLoginname($loginname);
    }

    public function sendActivationMail() {
        // Aktivierungs-Mail senden
        $to = $this->email;
        $realname = $this->realname;
        $vkey = $this->vkey;
        $subject = "Email verifizieren";
        $message = "Hallo $realname, \n\nKlicken Sie hier um Ihren Account zu aktivieren. <a href='https://kochbuch.taiskorgon.de/verify/$vkey'>https://kochbuch.taiskorgon.de/verify/$vkey</a>";
        Mail::send($to, $subject, $message);
    }

    public static function getBenutzerFromDatabase($sql):Benutzer {
        $result = query($sql);
        if ($row = $result->fetch_assoc()) {
            return Benutzer::get($row);
        }
        return new Benutzer();
    }
    public static function findByLoginname(string $loginname):Benutzer {
        $sql = sprintf("SELECT * FROM `tab_benutzer` where `loginname`= '%s';",
            escapeString($loginname)
        );
        return self::getBenutzerFromDatabase($sql);
    }

    public static function findByMail(string $email):Benutzer {
        $sql = sprintf("SELECT * FROM `tab_benutzer` where `email`= '%s';",
            escapeString($email)
        );
        return self::getBenutzerFromDatabase($sql);
    }

    public static function findByVKey(string $vkey):Benutzer {
        $sql = sprintf("SELECT * FROM `tab_benutzer` where `vkey`= '%s';",
            escapeString($vkey)
        );
        return self::getBenutzerFromDatabase($sql);
    }

    public static function findByUserID(int $userid):Benutzer {
        if ($userid > 0) {
            $sql = sprintf("SELECT * FROM `tab_benutzer` where `pk`= %d;"
                , $userid
            );
            return self::getBenutzerFromDatabase($sql);
        }
        return new Benutzer();
    }

    public static function findByToken():Benutzer {
        if (!isset($_COOKIE['rememberMeToken'])) {
            return new Benutzer();
        }
        $sql = sprintf("SELECT * FROM `tab_benutzer` where `token`= '%s';"
            , escapeString($_COOKIE['rememberMeToken'])
        );
        return self::getBenutzerFromDatabase($sql);
    }

    public function createRememberMeToken():string {
        $salt = microtime().rand();
        $rememberMeToken =  hash('sha256',$this->loginname.$salt);
        $sql = sprintf("UPDATE `tab_benutzer` SET `token` = '%s' WHERE `pk` = %d;"
            , escapeString($rememberMeToken)
            , $this->pk
        );
        if (query($sql)) {
            return $rememberMeToken;
        }
        return "";
    }

    public function verify():bool {
        $sql = sprintf("UPDATE `tab_benutzer` SET verified=1 WHERE pk=%d;"
            , $this->pk);
        return query($sql);
    }

    public function updateLastLogin():bool {
        $sql = sprintf("UPDATE `tab_benutzer` SET last_login=NOW() WHERE pk=%d;"
            , $this->pk);
        return query($sql);
    }

    public function setNewPassword($value) {
        $sql = sprintf("UPDATE `tab_benutzer` SET new_password='%s' WHERE pk=%d;"
            , $value
            , $this->pk);
        return query($sql);
    }

    public function changePasswort($newPasswort) {
        $this->new_password = $newPasswort;
        return $this->finalizePasswortChange();
    }

    public function finalizePasswortChange() {
        $this->password_hash = self::hashPassword($this->new_password);
        $this->new_password = '';
        $sql = sprintf("UPDATE `tab_benutzer` SET new_password='', password_hash='%s' WHERE pk=%d;"
            , escapeString($this->password_hash)
            , $this->pk);
        return query($sql);
    }

    static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function verifyPassword(string $password): bool {
        return password_verify($password, $this->password_hash);
    }

//    private function update() {
//
//    }

    private static function get($row) {
        $result = new Benutzer();
        $result->pk = $row['pk'];
        $result->realname = $row['realname'];
        $result->loginname = $row['loginname'];
        $result->password_hash = $row['password_hash'];
        $result->email = $row['email'];
        $result->vkey = $row['vkey'];
        $result->verified = $row['verified'] > 0;
        $result->token = $row['token'];
        $result->lastLogin = $row['last_login'];
        $result->new_password = $row['new_password'];
        return $result;
    }

    /** @noinspection PhpUnused */
    public function toArray() {
        return array (
            'pk' => $this->pk,
            'loginname' => $this->loginname,
            'password_hash' => $this->password_hash,
        );
    }

    // ++++++++++++++++++++++++++++++++++++++++++++++++
}