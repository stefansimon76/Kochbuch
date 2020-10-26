<?php
declare(strict_types=1);

function getCurrentUserID():int {
    if (isset($_SESSION['userid'])) {
        return (int)$_SESSION['userid'];
    }
    return 0;
}

function isLoggedIn(): bool {
    return getCurrentUserID() > 0;
}

function server($variableName) {
    return isset($_SERVER[$variableName]) ? $_SERVER[$variableName] : null;
}
function isPost():bool {
    return server('REQUEST_METHOD') === 'POST';
}

function isGet():bool {
    return server('REQUEST_METHOD') === 'GET';
}

function redirect(string $path) {
    header('Location:' . $path);
    exit;
}
