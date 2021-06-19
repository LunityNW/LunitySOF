<?php

/** 
 * 
 * LunityNW © 2021 - 2023 
 * 
 */

namespace lunity\utils;

use lunity\utils\Terminal;


class Logger {
  
  
  public function info($info) {
    echo Terminal::YELLOW . "[ Info ] " . Terminal::WHITE . "» " . Terminal::YELLOW . $info . PHP_EOL;
  }
  
  public function debug($debug) {
    echo Terminal::GREEN . "[ Debug ] " . Terminal::WHITE . "» " . Terminal::YELLOW . $debug . PHP_EOL;
  }
  
  public function error($error) {
    echo Terminal::RED . "[ Error ] " . Terminal::WHITE . "» " . Terminal::YELLOW . $error . PHP_EOL;
  }
}

?>
