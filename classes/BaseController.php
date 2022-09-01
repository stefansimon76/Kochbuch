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
        Layout::setBodyRenderer(RENDER_BODY_WELCOME);
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
        $_SESSION["renderWelcome"] = true;
        AccountController_Login::renderLogin();
        //AccountController_Rezept::renderRandomRezept();
    }

    protected static function loginUser(Benutzer $user) {
        $data=[
            'realname'=> $user->realname,
            'last_login' => $user->lastLogin,
            'loginname' => $user->loginname,
            'userid' => $user->pk,
        ];
        $token = $user->createRememberMeToken();
        if (strlen($token) > 0) {
            $expires = date_modify(date_create(), '+30 days');
            setcookie('rememberMeToken',$token, date_timestamp_get($expires));
        }
        $user->updateLastLogin();
        $_SESSION['userid'] = $user->pk;
        $_SESSION["userdata"] = $data;
        $data = AccountController_Rezept::getRezeptById(0);
        AccountController_Base::renderUserinfo($data);
    }
}