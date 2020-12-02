<?php
declare(strict_types=1);

class FileLogger implements ILogger {

    private string $classname = '';
    public string $logfile;

    public function __construct(string $logfile, string $classname) {
        $this->classname = $classname;
        $this->logfile = $logfile;
    }

    public function log(string $tag, string $message) {
        if ($this->fileHandle == null) {
            $this->openLogfile($this->logfile);
        }
        $date = date('Y-m-d H:m:s');
        $this->writeLogfile('['.$date.'] '.$tag.' '.$this->classname.' - '.$message.PHP_EOL);
    }

    private function writeLogfile(string $message) {
        flock($this->fileHandle, LOCK_EX);
        fwrite($this->fileHandle, $message);
        flock($this->fileHandle, LOCK_UN);
    }

    public function debug(string $message) {
        $this->log('debug', $message);
    }

    public function info(string $message) {
        $this->log('info ', $message);
    }

    public function error(string $message) {
        $this->log('error', $message);
    }

    private $fileHandle = null;
    protected function openLogfile(string $logfile) {
        $this->closeLogfile();
        $filename = LOG_DIR.'/'.$logfile;
        if (!$this->fileHandle = fopen($filename, 'a')) {
            die ('Fehler beim Öffnen der Logdatei');
        }
    }

    protected function closeLogfile() {
        if ($this->fileHandle != null) {
            fclose($this->fileHandle);
            $this->fileHandle = null;
        }
    }
}