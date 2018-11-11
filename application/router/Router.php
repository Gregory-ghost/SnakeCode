<?php

require_once dirname(__DIR__).'/game/Game.php';
require_once dirname(__DIR__).'/modules/DB.php';

class Router {
	
	private $game;
	private $db;

	public function __construct() {
        $this->startSession();
        $this->db = new DB();

        $options = new stdClass();

        // Получение из Базы данных
        $options->maps = $this->db->getMaps();
        $options->foods = $this->db->getFoods();
        $options->snakes = $this->db->getSnakes();
        $options->users = $this->db->getUsers();
        $options->snakesBody = $this->db->getSnakesBody();
        $options->system = $this->db->getSystem();

        // Текущий пользователь из сессии
        if  ( session_id() ) {
            $token = $_SESSION['token_id'];
            $options->myUser = $this->db->getUserByToken($token);
        } else {
            $options->myUser = (object) array(
                'id'    => 0,
                'name'  => 'noname',
                'login' => 'nologin',
            );
        }

		$this->game = new Game($options);

        // TODO :: сделать проверку может ли отдать структуру по таймауту
        // TODO :: как из логики обратиться к запросам из бд

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

	// Хороший ответ, возвращаем данные
	private function good($text) {
	    return [
	        'result' => true,
            'data' => $text,
        ];
    }

    // Плохой ответ, возвращаем ошибку
    private function bad($text) {
	    return [
	        'result' => false,
            'error' => $text,
	        ];
    }
	
	public function answer($options) {
	    if ( $options and isset($options->method) ) {
	        $method = $options->method;
            if ( $method ) {
                $COMMAND = $this->game->getCommand();
                foreach ( $COMMAND as $command ) {
                    if ( $command === $method ) {
                        unset($options->method);
                        $result = $this->game->executeCommand($method, $options);
                        return ($result) ?
                            $this->good($this->game->getStruct()) :
                            $this->bad('method wrong execute');
                    }
                }
                return $this->bad('The method ' . $method . ' has no exist');
            }
        }
		return $this->bad('You must set method param');
	}	

}