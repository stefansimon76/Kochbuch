<?php
declare(strict_types=1);

class Benutzer {

    private ILogger $logger;

    public function __construct() {
        $this->logger = LogFactory::getLog(get_class($this), 'Benutzer.log');
        $this->logger->debug('Instanz der Klasse Benutzer erstellt');
    }

    public function test() {
        $this->logger->error("ein Fehler ist aufgetreten");
    }
}