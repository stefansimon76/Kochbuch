<?php
declare(strict_types=1);


class BaseController
{
    // ####################################################
    // Nutzungsbedingungen anzeigen
    public static function termsAction(){
        echo Layout::getInstance()->render('terms', []);
    }

    // ####################################################
    // Datenschutzerklärung anzeigen
    public static function privacyAction(){
        echo Layout::getInstance()->render('datenschutz', []);
    }

    // ####################################################
    // Startseite anzeigen
    public static function indexAction(){
        $user = Benutzer::findByToken();
        if ($user->pk > 0) {
            self::loginUser($user);
            return;
        }
        if (isset($_SESSION['userid'])) {
            $user = Benutzer::findByUserID((int) $_SESSION['userid']);
            if ($user->pk > 0) {
                self::loginUser($user);
                return;
            }
            return;
        }
        AccountController_Login::renderLogin();
    }

    protected static function loginUser($user) {
        $data=[
            'realname'=> $user->realname,
            'last_login' => $user->lastLogin,
        ];
        $token = $user->createRememberMeToken();
        if (strlen($token) > 0) {
            $expires = date_modify(date_create(), '+30 days');
            setcookie('rememberMeToken',$token, date_timestamp_get($expires));
        }
        $user->updateLastLogin();
        $_SESSION['userid'] = $user->pk;
        AccountController_Base::renderUserinfo($data);
    }
}