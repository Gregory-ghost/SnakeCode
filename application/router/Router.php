<?php

require_once dirname(__DIR__).'/game/Game.php';
require_once dirname(__DIR__).'/modules/DB.php';

class Router {
	
	private $game;
	private $db;

	public function __construct() {
        $this->db = new DB();


        $options = new stdClass();

        // Получение из Базы данных
        $options->maps = $this->db->getMaps();
        $options->foods = $this->db->getFoods();
        $options->snakes = $this->db->getSnakes();
        $options->users = $this->db->getUsers();
        $options->snakesBody = $this->db->getSnakesBody();
        $options->system = $this->db->getSystem();

		$this->game = new Game($options);

        // TODO :: сделать проверку может ли отдать структуру по таймауту
        // TODO :: как из логики обратиться к запросам из бд






//        print_r($this->game);

        $optionUser = array(
            'width' => 14,
            'height' => 7,
        );
//        print_r($this->db->createMap($optionUser));

//		print_r($this->db->getUser('vasya'));
        /*$optionSnake = array(
            'user_id' => 1,
            'direction' => 'right',
        );
		print_r($this->db->createSnake($optionSnake));*/
        /*$optionUser = array(
            'name' => 'Петя',
            'login' => 'petya',
            'password' => '111',
        );
        print_r($this->db->saveUser($optionUser));*/

		//$COMMAND = $game->getCommand();
		//print_r($this->game->executeCommand($COMMAND->CHANGE_DIRECTION, (object) [ 'id' => 12, 'direction' => 'left']));
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