<?php

namespace lunity\utils;

class Logger {
  
  
  public function info($info) {
    echo "[ Info ]: " . $info;
  }
  
  public function debug($debug) {
    echo "[ Info ]: " . $debug;
  }
  
  public function error($error) {
    echo "[ Info ]: " . $error;
  }
}

?>
