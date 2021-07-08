<?php
declare(strict_types=1);

use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

function getCurrentUserID():int {
    if (isset($_SESSION['userid'])) {
        return (int)$_SESSION['userid'];
    }
    return 0;
}

#[Pure]
function isLoggedIn(): bool {
    return getCurrentUserID() > 0;
}

function server($variableName) {
    return isset($_SERVER[$variableName]) ? $_SERVER[$variableName] : null;
}

#[Pure]
function isPost():bool {
    return server('REQUEST_METHOD') === 'POST';
}

#[Pure]
function isGet():bool {
    return server('REQUEST_METHOD') === 'GET';
}

#[NoReturn]
function redirect(string $path) {
    header('Location:' . $path);
    exit;
}

function strrstr($h, $n, $before = false):bool|String {
    $rpos = strrpos($h, $n);
    if($rpos === false) return false;
    if($before == false) return substr($h, $rpos);
    else return substr($h, 0, $rpos);
}

function normalizeFile(array $fileinfo):array {
    if ($fileinfo['name'] === "")
        return [];

    $result = [];
    foreach ($fileinfo as $keyName => $values) {
        if ($values[0] === "")
            break;
        foreach ($values as $index => $value) {
            $result[$index][$keyName] = $value;
        }
    }
    $typeToExtensionMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    foreach ($result as $index => $file) {
        $tmpPath = $result[$index]['tmp_name'];
        $type = finfo_file($finfo, $tmpPath);
        $result[$index]['type'] = $type;
        $result[$index]['size'] = filesize($tmpPath);
        if (isset($typeToExtensionMap[$type])) {
            $result[$index]['extension'] = $typeToExtensionMap[$type];
        }
    }
    return $result;
}

function uploadPicture(String $subdir, array $imageInfo):bool {
    $picturePath = STORAGE_DIR.'/pictures/'.$subdir.'1/';
    if (!is_dir($picturePath)) {
        mkdir($picturePath, 0777, true);
    }
    $filesToCheck = [];
    if (is_dir($picturePath)) {
        $filename = $picturePath . '1.' . $imageInfo['extension'];
        $filesToCheck[] = $filename;
        copy($imageInfo['tmp_name'], $filename);
        unlink($imageInfo['tmp_name']);
    }
    foreach ($filesToCheck as $file) {
        if (false === is_file($file)) {
            return false;
        }
    }
    return true;
}