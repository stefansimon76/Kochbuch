<?php
declare(strict_types=1);

class AccountController_Base extends BaseController
{
    // ####################################################
    // zufälliges Passwort generieren
    protected static function rand_passwd( $length = 8, $chars = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789' ) {
        return substr( str_shuffle( $chars ), 0, $length );
    }

    // ####################################################
    // Benutzer abmelden
    public static function renderLogout() {
        // Löschen aller Session-Variablen.
        $_SESSION = array();

        // Falls die Session gelöscht werden soll, löschen Sie auch das
        // Session-Cookie.
        // Achtung: Damit wird die Session gelöscht, nicht nur die Session-Daten!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"],
                $params["domain"], $params["secure"], $params["httponly"]
            );
        }
        setcookie('rememberMeToken','',-1);
        // Zum Schluss, löschen der Session.
        session_destroy();
        //echo Layout::getInstance()->render('index', ['infos' => ['Sie haben Ihr Passwort erfolgreich geändert']]);
        header('location:/');
    }

    // ####################################################
    // Benutzerdaten anzeigen
    public static function renderUserinfo($data) {
        Layout::setAccRenderer(RENDER_ACC_USERINFO);
        echo Layout::getInstance()->render('index', $data);
    }

}