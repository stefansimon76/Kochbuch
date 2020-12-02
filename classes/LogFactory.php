<?php
declare(strict_types=1);

class LogFactory {

    protected static $logger = [];

    public static function getLog(string $classname, string $logfile = 'kochbuch.log') {
        if (!array_key_exists($logfile, static::$logger)) {
            static::$logger[$logfile] = new FileLogger($logfile, $classname);
        }
        return static::$logger[$logfile];
    }

}