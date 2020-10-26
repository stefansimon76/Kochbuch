<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["renderLogin"])) {
    $_SESSION["renderLogin"] = true;
    $_SESSION["renderLogin"] = false;
    $_SESSION["renderVerify"] = false;
    $_SESSION["renderUserinfo"] = false;
    $_SESSION["renderRegister"] = false;
    $_SESSION["renderPasswortVergessen"] = false;
    $_SESSION["renderPasswortChange"] = false;
    $_SESSION["renderThanksForRegister"] = false;
}

if (isset($_SESSION['userid'])) {
    $_SESSION["renderLogin"] = false;
    $_SESSION["renderUserinfo"] = true;
}

if (!isset($_SESSION["realname"])) {
    $_SESSION["realname"] = 'Gast';
}

if (!isset($_SESSION["loginname"])) {
    $_SESSION["loginname"] = '';
}


if (isset($_COOKIE['setCookieHinweis'])) {
    $_SESSION["showCookiePopup"] = false;
} else {
    $_SESSION["showCookiePopup"] = true;
}

//}

error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set('UTC');

//require('classes/Mustache/Autoloader.php');
require('autoload.php');
require('database.php');
require_once __DIR__ . '/helper/router.php';



$http404 = function() {
    echo '<p>404</p>';
    echo "<p>";
        print_r($_SESSION);
    echo "</p>";
    // Umleiten zur Startseite
    //header('location:/');
};

router('/', $http404,[BaseController::class,'indexAction']);
router('/login/([a-zA-Z0-9]+)', $http404,[AccountController_Login::class,'renderLoginForUser'], 'GET');
router('/login', $http404,[AccountController_Login::class,'renderLogin'], 'GET');
router('/logout', $http404,[AccountController_Base::class,'renderLogout'], 'GET');
router('/login', $http404,[AccountController_Login::class,'postLogin'], 'POST');
router('/pwchange', $http404,[AccountController_Passwort::class,'renderChangePasswort'], 'GET');
router('/pwchange', $http404,[AccountController_Passwort::class,'postChangePasswort'], 'POST');
router('/register', $http404,[AccountController_Register::class,'renderRegister'], 'GET');
router('/register', $http404,[AccountController_Register::class,'postRegister'], 'POST');
router('/password_forgotten', $http404,[AccountController_Passwort::class,'renderPasswortVergessen'], 'GET');
router('/password_forgotten', $http404,[AccountController_Passwort::class,'postPasswortVergessen'], 'POST');
router('/danke', $http404,[AccountController_Register::class,'renderThankYou']);
router('/terms', $http404,[BaseController::class,'termsAction']);
router('/privacy', $http404,[BaseController::class,'privacyAction']);
router('/verify/([a-f0-9]{32})', $http404,[AccountController_Register::class,'renderVerifyByVKey']);
router('/verify/(\d+)', $http404,[AccountController_Register::class,'renderVerifyByUserId']);

$requestUrl = $_SERVER['REQUEST_URI'];

$beforeIndexPosition = strpos($_SERVER['PHP_SELF'], '/index.php');
if (false !== $beforeIndexPosition && $beforeIndexPosition > 0) {
    $scriptUrl = substr($_SERVER['PHP_SELF'], 0, $beforeIndexPosition).'/';
    $requestUrl = str_replace(['/index.php', $scriptUrl], '/', $requestUrl);
}

$requestUrl = $_SERVER['REQUEST_METHOD'].'_'.$requestUrl;
//echo $requestUrl;
router($requestUrl,$http404);






