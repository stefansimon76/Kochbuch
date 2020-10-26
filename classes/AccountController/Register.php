<?php
declare(strict_types=1);


class AccountController_Register extends AccountController_Base
{
    // ####################################################
    // GET Registrierungsseite
    public static function renderRegister(array $errors=[]) {
        self::unsetUserSection();
        $_SESSION["renderRegister"] = true;
        $data=[
            'loginname'=> filter_input(INPUT_POST, 'loginname', FILTER_SANITIZE_STRING),
            'realname'=> filter_input(INPUT_POST, 'realname'),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'errors' => $errors,
        ];
        echo Layout::getInstance()->render('index', $data);
    }

    // ####################################################
    // POST Registrierungsseite
    public static function postRegister() {
        $loginname = filter_input(INPUT_POST, 'loginname', FILTER_SANITIZE_STRING);
        $realname = filter_input(INPUT_POST, 'realname');
        $pw1 = filter_input(INPUT_POST, 'md5pw1');
        $pw2 = filter_input(INPUT_POST, 'md5pw2');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $terms = filter_input(INPUT_POST, 'terms');

        $user = Benutzer::findByLoginname($loginname);
        $errors=[];

        if ($user->pk > 0) {
            $errors[] = 'Der Anmeldename ist bereits vergeben';
        }

        if (strlen($loginname) < 5) {
            $errors[] = "Der Anmeldename muss mindestens 5 Zeichen lang sein";
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $loginname))
        {
            $errors[] = "Der Anmeldename darf nur Buchstaben und Ziffern enthalten (keine Sonderzeichen und kein Leerzeichen)";
        }

        if ($pw1 !== $pw2) {
            $errors[] = 'Die Passwörter stimmen nicht überein';
        }

        if (!$email) {
            $errors[] = 'Bitte geben Sie eine korrekte E-Mail Adresse ein';
        }

        if ($terms !== 'on') {
            $errors[] = 'Bitte akzeptieren Sie die Nutzungsbedingungen';
        }

        $user = Benutzer::findByMail($email);
        if ($user->pk > 0) {
            $errors[] = 'Die E-Mail Adresse ist bereits einem anderen Benutzer zugeordnet, bitte wählen Sie eine andere.';
        }

        if (count($errors) == 0) {
            if (empty($realname)) {
                $realname = $loginname;
            }
            $user = Benutzer::createAccount($realname, $loginname, $pw1, $email);
            if ($user->pk > 0) {
                $_SESSION["realname"] = $realname;
                $_SESSION["loginname"] = $loginname;
                $user->sendActivationMail();
                self::renderThankYou();
            }
        } else {
            self::renderRegister($errors);
        }
    }

    // ####################################################
    // GET Danke für die Registrierung
    public static function renderThankYou() {
        self::unsetUserSection();
        $_SESSION["renderThanksForRegister"] = true;
        $data = [
            'realname' => $_SESSION["realname"],
            'loginname' => $_SESSION["loginname"],
        ];
        echo Layout::getInstance()->render('index', $data);
    }

    // ####################################################
    // GET Der Benutzer hat auf seinen Aktivierungslink geklickt
    public static function renderVerifyByVKey($vkey) {
        $user = Benutzer::findByVKey($vkey);
        if ($user->verify()) {
            AccountController_Login::renderLoginForUser($user->loginname);
        }
    }

    // ####################################################
    // GET Aktivierungsmail erneut versenden und Anmeldeseiteanzeigen
    public static function renderVerifyByUserId($userid) {
        $user = Benutzer::findByUserID((int)$userid);
        if ($user->pk > 0) {
            $user->sendActivationMail();
            $data=[
                'loginname'=> $user->loginname,
                'infos' => ['Die E-Mail mit dem Aktivierungslink wurde verschickt. Bitte aktivieren Sie Ihren Account und melden Sie sich anschließend an.'],
            ];
            self::unsetUserSection();
            $_SESSION["renderLogin"] = true;
            echo Layout::getInstance()->render('index', $data);
        }
    }
}