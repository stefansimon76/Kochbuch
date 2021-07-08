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
        $renderInfo = $this->getRenderer();
        $_data = array_merge($data, $renderInfo);
        return $e->render($index, $_data);
    }

    public static function setAccRenderer(string $renderer) {
        self::setRenderer($renderer, 'RENDER_ACC_');
    }

    public static function setBodyRenderer(string $renderer) {
        self::setRenderer($renderer, 'RENDER_BODY_');
    }

    private static function setRenderer(string $renderer, string $needle) {
        foreach (get_defined_constants() as $key=>$value) {
            if (is_string($key) && str_starts_with($key, $needle)) {
                $_SESSION[$value] = false;
                if ($value === $renderer) {
                    $_SESSION[$value] = true;
                }
            }
        }
    }

    private function getRenderer():array {
        $result = [];
        $defaultACC = RENDER_ACC_LOGIN;
        $defaultBody = RENDER_BODY_WELCOME;
        $setDefaultACC = true;
        $setDefaultBody = true;
        foreach (get_defined_constants() as $key=>$value) {
            if (is_string($key) && str_starts_with($key, 'RENDER_ACC_')) {
                if ($_SESSION[$value] === true ) {
                    $result[$value] = true;
                    $setDefaultACC = false;
                    break;
                }
            }
        }
        foreach (get_defined_constants() as $key=>$value) {
            if (is_string($key) && str_starts_with($key, 'RENDER_BODY_')) {
                if ($_SESSION[$value] === true ) {
                    $result[$value] = true;
                    $setDefaultBody = false;
                    break;
                }
            }
        }
        if ($setDefaultACC == true) {
            $result[$defaultACC] = true;
        }
        if ($setDefaultBody == true) {
            $result[$defaultBody] = true;
        }
        return $result;
    }

}