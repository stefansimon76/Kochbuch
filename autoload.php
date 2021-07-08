<?php
declare(strict_types=1);

define('TEMPLATE_DIR', __DIR__.'/templates');
define('PARTIALS_DIR', __DIR__.'/partials');
define('STORAGE_DIR', __DIR__.'/storage');

define('RENDER_ACC_LOGIN', 'renderLogin'); // default in Layout.php
define('RENDER_ACC_VERIFY', 'renderVerify');
define('RENDER_ACC_USERINFO', 'renderUserinfo');
define('RENDER_ACC_REGISTER', 'renderRegister');
define('RENDER_ACC_PW_FORGOTTEN', 'renderPasswortVergessen');
define('RENDER_ACC_PW_CHANGE', 'renderPasswortChange');
define('RENDER_ACC_THANKS', 'renderThanksForRegister');

define('RENDER_BODY_WELCOME', 'renderWelcome'); // default in Layout.php
define('RENDER_BODY_CREATE_REZEPT', 'renderCreateRezept');
define('RENDER_BODY_EDIT_REZEPT', 'renderEditRezept');
define('RENDER_BODY_LISTE_REZEPTE', 'renderListeRezepte');

$rootDir = __DIR__.'/classes/';
$autoload = function($className) use($rootDir){

    $fileName = '';
    if($lastNameSpacePosition = strpos($className,'\\')){
        $namespace = substr($className, 0,$lastNameSpacePosition);
        $className = substr($className,$lastNameSpacePosition+1);
        $fileName = str_replace('\\',DIRECTORY_SEPARATOR,$namespace).DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className);
    if(is_file($rootDir.$fileName.'.php')){
        require_once $rootDir.$fileName.'.php';
    }
};

spl_autoload_register($autoload);
