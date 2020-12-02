<?php
declare(strict_types=1);

$rootDir = ROOT_DIR.'/classes/';
$autoload = function($classname) use ($rootDir) {
    $filename = $rootDir.$classname.'.php';
    if (is_file($filename)) {
        require_once($filename);
    }
};

spl_autoload_register($autoload);