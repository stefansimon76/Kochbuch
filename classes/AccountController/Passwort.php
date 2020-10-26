<?php
declare(strict_types=1);


class AccountController_Passwort extends AccountController_Base
{
    // ####################################################
    // ### Passwort vergessen
    // ####################################################

    // ####################################################
    // GET Passwort vergessen
    public static function renderPasswortVergessen() {
        self::unsetUserSection();
        $_SESSION["renderPasswortVergessen"] = true;
        $data=[
            'userinput'=> filter_input(INPUT_POST, 'userinput'),
        ];
        echo Layout::getInstance()->render('index', $data);
    }

    // ####################################################
    // Nutzer anhand seines Loginnamens oder seiner E-Mail Adresse ermitteln
    private static function findUserByUserinput(string $userinput): Benutzer {
        $user = Benutzer::findByLoginname($userinput);
        if ($user->pk > 0) {
            return $user;
        }

        return Benutzer::findByMail($userinput);
    }

    // ####################################################
    // POST Passwort vergessen
    public static function postPasswortVergessen() {
        $userinput = filter_input(INPUT_POST, 'loginname_or_email');
        $user = self::findUserByUserinput($userinput);
        if ($user->pk == 0) {
            $data=[
                'userinput'=> $userinput,
                'errors' => ['Die Eingabe konnte keinem vorhandenem Account zugeordnet werden'],
            ];
            echo Layout::getInstance()->render('pwforgotten', $data);
            return;
        }
        $newPassword = self::rand_passwd();
        if ($user->setNewPassword(md5($newPassword))) {
            $message = "Ihr Passwort wurde geändert. Wenn Sie die Passwortänderung nicht veranlasst haben, können Sie diese E-Mail ignorieren.";
            $message .="<br>Ihr Loginname lautet: ".$user->loginname;
            $message .="<br>Ihr neues Passwort lautet: ".$newPassword;
            Mail::send($user->email, 'Ihr neues Passwort', $message);
            $data=[
                'loginname'=> $user->loginname,
                'infos' => ['Ihr Password wurde geändert, bitte melden Sie sich mit Ihrem neuen Passwort an.'],
            ];
            echo Layout::getInstance()->render('index', $data);

        }
    }


    // ####################################################
    // ### Passwort ändern
    // ####################################################

    // ####################################################
    // GET Passwort ändern
    public static function renderChangePasswort() {
        self::unsetUserSection();
        $_SESSION["renderPasswortChange"] = true;
        echo Layout::getInstance()->render('index', []);
    }

    // ####################################################
    // POST Passwort ändern
    public static function postChangePasswort() {
        $pwAlt = filter_input(INPUT_POST, 'md5pwold');
        $pwNeu1 = filter_input(INPUT_POST, 'md5pw1');
        $pwNeu2 = filter_input(INPUT_POST, 'md5pw2');
        $user = Benutzer::findByUserID((int)$_SESSION['userid']);

        $errors = [];

        if (!$user->verifyPassword($pwAlt)) {
            $errors[] = 'Das Passwort passt nicht zum angegebenen Benutzer';
        }

        if ($pwNeu1 !== $pwNeu2) {
            $errors[] = 'Die Passwörter stimmen nicht überein';
        }

        if (count($errors) > 0) {
            $data=[
                'loginname'=> $_SESSION['loginname'],
                'errors' => $errors,
            ];
            self::unsetUserSection();
            $_SESSION["renderPasswortChange"] = true;
            echo Layout::getInstance()->render('index', $data);
            return;
        }
        $user->changePasswort($pwNeu1);
        self::renderUserinfo(['infos' => ['Sie haben Ihr Passwort erfolgreich geändert']]);
    }
}