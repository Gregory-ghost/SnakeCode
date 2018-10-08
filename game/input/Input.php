<?php

require_once 'command.php';

class Input {

    const COMMAND = COMMAND;
    private $logic;

    public function __construct($logic) {
        $this->logic = $logic;
    }

    public function getCommand() {
        return (object) self::COMMAND;
    }

    public function executeCommand($name, $options = null) {
        if ($name) {
            return $this->logic->{$name}($options);
        }
        return false;
    }
}