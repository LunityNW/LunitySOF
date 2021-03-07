<?php

namespace lunity\utils;

class Logger {
  
  
  public function inf($info) {
    echo "[ Info ]: " . $info;
  }
  
  public function debug($debug) {
    echo "[ Info ]: " . $debug;
  }
  
  public function inf($error) {
    echo "[ Info ]: " . $error;
  }
}

?>
