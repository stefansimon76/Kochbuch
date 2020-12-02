<?php 
declare(strict_types=1);

define ('ROOT_DIR', __DIR__);
define ('LOG_DIR', ROOT_DIR.'/logs');

require (ROOT_DIR.'/autoload.php');

$logger = LogFactory::getLog('index.php');
$logger->info('Anwendung gestartet');

$benutzer = new Benutzer();
$benutzer->test();

echo "Kochbuch";

