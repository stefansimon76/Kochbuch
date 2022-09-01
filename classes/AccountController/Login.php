<?php
declare(strict_types=1);


class AccountController_Login extends AccountController_Base
{
    // ####################################################
    // GET Anmeldeseite
    public static function renderLogin() {
        if (isset($_SESSION['loginname'])) {
            self::renderLoginForUser($_SESSION['loginname']);
            return;
        }
        self::renderLoginForUser('gast');
    }

    // ####################################################
    // GET Anmeldeseite für angegebenen Benutzer anzeigen
    public static function renderLoginForUser($loginname) {
        $data=[
            'loginname'=> $loginname,
        ];
        Layout::setAccRenderer(RENDER_ACC_LOGIN);
        $data = AccountController_Rezept::getRezeptById(0);
        echo Layout::getInstance()->render('index', $data);
    }

    // ####################################################
    // POST Anmeldeseite
    public static function postLogin() {
        $loginname = filter_input(INPUT_POST, 'loginname', FILTER_SANITIZE_STRING);
        $pw1 = filter_input(INPUT_POST, 'md5pw1');
        $user = Benutzer::findByLoginname($loginname);
        if ($user->pk == 0) {
            $data=[
                'loginname'=> $loginname,
                'errors' => ['Der Anmeldename "'.$loginname.'" ist unbekannt'],
            ];
            echo Layout::getInstance()->render('index', $data);
            return;
        }

        if (!$user->verified) {
            $_SESSION['realname'] = $user->realname;
            $_SESSION['loginname'] = $user->loginname;
            $data = [
                'userid' => $user->pk,
            ];
            Layout::setAccRenderer(RENDER_ACC_VERIFY);
            echo Layout::getInstance()->render('index', $data);
            return;
        }

        if ($user->new_password === $pw1) {
            // Das Passwort wurde vor kurzem geändert. Dieses ist der erste Login nach der Passwortänderung.
            $user->finalizePasswortChange();
        }

        if (!$user->verifyPassword($pw1)) {
            $data=[
                'loginname'=> $loginname,
                'errors' => ['Das Passwort passt nicht zum angegebenen Benutzer'],
            ];
            echo Layout::getInstance()->render('index', $data);
            return;
        }
        self::loginUser($user);
    }
}