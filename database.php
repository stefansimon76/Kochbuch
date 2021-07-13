<?php
declare(strict_types=1);

const DATABASE_CONNECTION = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '?r4V0T_#fa',
    'database' => 'kochbuch',
    'port' => 3306,
    'charset' => 'utf-8'
];

/**
 * @return mysqli | null
 */
function getDb():mysqli|null {
    /**
     * @var mysqli|null
     */
    static $mysqli = null;
    if ($mysqli instanceof mysqli) {
        return $mysqli;
    }
    list($host, $user, $password, $database, $port, $charset) = array_values(DATABASE_CONNECTION);
    $mysqli = mysqli_init();
    /**
     * we need to add @ for mysqli_real_connect because of MAMMP PRO 3.3.0 it shows a warning
     */
    $connectionResult = @mysqli_real_connect($mysqli, $host, $user, $password, $database, $port);

    if (!$connectionResult) {
        mysqli_close($mysqli);
        $mysqli = null;
        trigger_error(mysqli_connect_error(),E_USER_ERROR);
    }
    mysqli_set_charset($mysqli, $charset);

    return $mysqli;
}

/**
 * @param mysqli|null $db
 * @return string
 */
function getDBError(mysqli $db = null):string {
    if (is_null($db)) {
        $db = getDb();
    }

    return mysqli_error($db);
}

/**
 * @param string $sql
 * @param mysqli|null $db
 * @param int $resultMode
 * @return bool|mysqli_result
 */
function query(string $sql, mysqli $db = null, int $resultMode = MYSQLI_STORE_RESULT):bool|mysqli_result {
    if (is_null($db)) {
        $db = getDb();
    }

    $result = mysqli_query($db, $sql, $resultMode);
    if (!$result) {
        trigger_error(getDBError(), E_USER_ERROR);
    }

    return $result;
}

function insert(string $sql, mysqli $db = null, int $resultMode = MYSQLI_STORE_RESULT):int {
    if (is_null($db)) {
        $db = getDb();
    }

    $result = mysqli_query($db, $sql, $resultMode);
    if (!$result) {
        trigger_error(getDBError(), E_USER_ERROR);
    }

    return $db->insert_id;
}

/**
 * @param string $text
 * @param mysqli|null $db
 * @return string
 */
function escapeString(string $text, mysqli $db = null):string {
    if (is_null($db)) {
        $db = getDb();
    }

    return mysqli_real_escape_string($db, $text);
}
