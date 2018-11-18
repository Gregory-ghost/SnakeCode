<?php

require_once dirname(__DIR__).'/game/Game.php';
require_once dirname(__DIR__) . '/modules/db/db.php';
require_once dirname(__DIR__) . '/modules/user/User.php';

class Router {

    private $db;
    private $user;
	private $game;

	public function __construct() {
        $this->db = new DB();
        $this->game = new Game($this->db);
        $this->user = new User($this->db);
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

    private function login($login, $password) {
        $token = $this->user->login($login, $password);
        return ($token) ?
            $this->good($token) :
            $this->bad('authorization fail');
    }

    private function logout($token) {
        return ($this->user->logout($token)) ?
            $this->good(true) :
            $this->bad('logout fail');
    }


	public function answer($options) {
	    if ( $options and isset($options->method) ) {
	        $method = $options->method;
            if ( $method ) {
                switch ($method) {
                    case 'login'  : return $this->login($options->login, $options->password); break;
                    case 'logout' : return $this->logout($options->token); break;
                }
                $userId = $this->user->checkToken($options->token);
                if ($userId) {
                    switch($method) {
                        case 'getMaps' :
                            $maps = $this->game->getMaps();
                            return ($maps)
                                ? $this->good($this->game->getStruct())
                                : $this->bad('maps not found');
                            break;
                        case 'startGame' :
                            $game = $this->game->startGame($userId, $options->map_id);
                            return ($game)
                                ? $this->good($game)
                                : $this->bad('start game not found');
                            break;
                    }
                    if (isset($options->map_id) and $this->game->init($options->map_id)) {
                        $COMMAND = $this->game->getCommand();
                        foreach ( $COMMAND as $command ) {
                            if ( $command === $method ) {
                                unset($options->method);
                                $result = $this->game->executeCommand($method, $options);
                                if ($result) {
                                    $this->game->updateData($options->map_id); // записать измененные данные в БД
                                }
                                return ($result) ?
                                    $this->good($this->game->getStruct()) :
                                    $this->bad('game method return false');
                            }
                        }
                    }
                }
                return $this->bad('Invalid token');
            } else {
                return $this->bad('The method ' . $method . ' has no exist');
            }
        }
		return $this->bad('You must set method param');
	}
}