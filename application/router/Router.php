<?php

require_once dirname(__DIR__).'/game/Game.php';
require_once dirname(__DIR__).'/modules/DB.php';

class Router {
	
	private $game;
	private $db;

	public function __construct() {
		$options = new stdClass();

		$options->map = (object) array (
		    'sizeX' => 14,
            'sizeY' => 7,
            'sizeSnake' => 64,
        );
		$options->snakes = [
			(object) array (
				'id' => 12,
				'name' => 'Vasya',
				'body' => [
					(object) array(
                        'x' => 128,
                        'y' => 0,
                    ),
                    (object) array(
                        'x' => 64,
                        'y' => 0,
                    ),
                    (object) array(
                        'x' => 0,
                        'y' => 0,
                    ),
				],
				'direction' => 'right',
				'eating' => 0,
			),
		];
		$options->foods = [
			(object) array( 'x' => 2, 'y' => 0, 'value' => 2 )
		];

		$this->db = new DB();
		$this->game = new Game($options);

//		print_r($this->db->getUser('vasya'));
        /*$optionSnake = array(
            'user_id' => 1,
            'direction' => 'right',
            'body' => '[2,0]',
        );
		print_r($this->db->createSnake($optionSnake));*/
        /*$optionUser = array(
            'name' => 'Петя',
            'login' => 'petya',
            'password' => '111',
        );
        print_r($this->db->createUser($optionUser));*/

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