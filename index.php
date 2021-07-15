<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["realname"])) {
    $_SESSION["realname"] = 'Gast';
}

if (!isset($_SESSION["loginname"])) {
    $_SESSION["loginname"] = '';
}

if (!isset($_SESSION["userdata"])) {
    $_SESSION["userdata"] = [];
}

if (isset($_COOKIE['setCookieHinweis'])) {
    $_SESSION["showCookiePopup"] = false;
} else {
    $_SESSION["showCookiePopup"] = true;
}

error_reporting(-1);
ini_set('display_errors', 'On');

date_default_timezone_set('UTC');

//require('classes/Mustache/Autoloader.php');
require('autoload.php');
require('database.php');
require_once __DIR__ . '/helper/router.php';
require_once __DIR__ . '/helper/helper.php';



$http404 = function() {
    echo '<p>404</p>';
    echo "<p>";
        print_r($_SESSION);
    echo "</p>";
    // Umleiten zur Startseite
    header('location:/');
};

//Kategorie::saveDefaultKategorien();
//$kats = Kategorie::getAlleKategorien();
//foreach ($kats as $kat) {
//    echo $kat->name . "<br>";
//}

router('/', $http404,[BaseController::class,'indexAction']);
router('/login/([a-zA-Z0-9]+)', $http404,[AccountController_Login::class,'renderLoginForUser'], 'GET');
router('/login', $http404,[AccountController_Login::class,'renderLogin'], 'GET');
router('/logout', $http404,[AccountController_Base::class,'renderLogout'], 'GET');
router('/login', $http404,[AccountController_Login::class,'postLogin'], 'POST');
router('/pwchange', $http404,[AccountController_Passwort::class,'renderChangePasswort'], 'GET');
router('/pwchange', $http404,[AccountController_Passwort::class,'postChangePasswort'], 'POST');
router('/register', $http404,[AccountController_Register::class,'renderRegister'], 'GET');
router('/register', $http404,[AccountController_Register::class,'postRegister'], 'POST');
router('/rezepte/([0-9]+)', $http404,[AccountController_Rezept::class,'renderRezeptById'], 'GET');
router('/rezepte/([a-zA-Z0-9]+)', $http404,[AccountController_Rezept::class,'renderRezepteByLoginname'], 'GET');
router('/rezepte', $http404,[AccountController_Rezept::class, 'renderRezepte'], 'GET');
router('/saveRezept', $http404,[AccountController_Rezept::class,'saveRezept'], 'POST');
router('/deleteRezept/([0-9]+)', $http404,[AccountController_Rezept::class,'deleteRezept'], 'GET');
router('/editRezept/([0-9]+)', $http404,[AccountController_Rezept::class,'renderEditRezept'], 'GET');
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






