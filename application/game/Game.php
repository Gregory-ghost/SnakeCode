<?php

require_once 'struct\Struct.php';
require_once 'logic\Logic.php';
require_once 'input\Input.php';

class Game {

    private $struct;
    private $logic;
    private $input;

    public function __construct($options) {
        $this->struct = new Struct($options);
        $this->logic  = new Logic($this->struct);
        $this->input  = new Input($this->logic);
    }

    public function getCommand() {
        return  (object) $this->input->getCommand();
    }

    public function executeCommand($name, $options = null) {
        return $this->input->executeCommand($name, $options);
    }
	
	// Возвращает нашу структуру целиком
    public function getStruct() {
        return $this->struct;
    }
    public function startSession() {
        if ( session_id() ) return true;
        else return session_start();
    }
    public function destroySession() {
        if ( session_id() ) {
            // Если есть активная сессия, удаляем куки сессии,
            setcookie(session_name(), session_id(), time()-60*60*24);
            // и уничтожаем сессию
            session_unset();
            session_destroy();
        }
    }
}
