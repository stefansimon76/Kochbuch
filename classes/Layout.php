<?php
declare(strict_types=1);

class Layout {

    private static $instance = null;
    private $engine = null;

    private function __construct() { }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getEngine(): Mustache_Engine {
        if ($this->engine === null) {
            $dirPartials = __DIR__.'/partials';
            if (defined('PARTIALS_DIR')) {
                $dirPartials = PARTIALS_DIR.DIRECTORY_SEPARATOR;
            }
            $dirTemplates = __DIR__.'/templates';
            if (defined('TEMPLATE_DIR')) {
                $dirTemplates = TEMPLATE_DIR.DIRECTORY_SEPARATOR;
            }
            $loader = new Mustache_Loader_FilesystemLoader($dirTemplates);
            $partials_loader = new Mustache_Loader_FilesystemLoader($dirPartials);
            $this->engine = new Mustache_Engine(
                ['loader' => $loader,
                 'partials_loader' => $partials_loader,
                 'helpers' => array('_SESSION' => $_SESSION),
                ]
            );
        }
        return $this->engine;
    }

    public function render(string $index, array $data):string {
        $e= $this->getEngine();
        return $e->render($index, $data);
    }

}