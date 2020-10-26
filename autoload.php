<?php
declare(strict_types=1);

define('TEMPLATE_DIR', __DIR__.'/templates');
define('PARTIALS_DIR', __DIR__.'/partials');

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
