<?php


namespace lunity\network;


use lunity\LunitySof;

class SessionManager {

    /** @var SessionManager $instance */
    public static $instance;
    /** @var LunitySof $main */
    public $main;
    /** @var array $sessions */
    protected $sessions = [];

    public function __construct(LunitySof $main) {
        self::$instance = $this;
        $this->main = $main;
    }

    public static function getInstance(): SessionManager {
        return self::$instance;
    }

    public function isSession($addres, $port) {
        return isset($this->sessions["{$addres}:{$port}"]);
    }

    public function getSession($addres, $port) {
        return $this->sessions["{$addres}:{$port}"];
    }

    /**
     * @return array
     */
    public function getSessions(): array {
        return $this->sessions;
    }

    public function removeSession($addres, $port) {
        if ($this->isSession($addres, $port)) {
            unset($this->sessions["{$addres}:{$port}"]);
        }
    }

    public function addSession($addres, $port) {
        //$this->sessions["{$addres}:{$port}"] = new Session();
    }

}