<?php

require_once 'command.php';

class Input {

    const COMMAND = COMMAND;
    private $logic;

    public function __construct($logic) {
        $this->logic = $logic;
    }

	// Получить команду
    public function getCommand() {
        return (object) self::COMMAND;
    }

	// Выполнить команду
    public function executeCommand($name, $options = null) {
        if ($name) {
            return $this->logic->{$name}($options);
        }
        return false;
    }
}