<?php
declare(strict_types=1);

interface ILogger {
    function debug(string $message);
    function info(string $message);
    function error(string $message);
}