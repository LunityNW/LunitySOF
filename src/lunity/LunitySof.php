<?php

namespace lunity;

use lunity\utils\Logger;

class LunitySof {

    public $work = false;
    public $logger;
  
    public function __construct() {
        $this->logger = new logger();
        $this->init();
    }

    public function init() {
        $this->work = true;
        while($this->work){
            
        }
    }

    public function getLogger(): Logger {
        return ($this->logger instanceof Logger) ? $this->logger : $this->logger = new Logger();
    }
  
}

?>
