<?php

require_once dirname(__DIR__).'/game/Game.php';

class Router {
	
	private $game;
	
	public function __construct() {
		$options = new stdClass();

		$options->map = [
			[0, 0, 0], [0, 0, 0], [0, 0, 0],
		];
		$options->snakes = [
			(object) array (
				'id' => 12,
				'name' => 'Vasya',
				'body' => [
					(object) array(
						'x' => 0,
						'y' => 0,
					),
				],
				'direction' => 'left',
				'eating' => 0,
			),
		];
		$options->foods = [
			(object) array( 'x' => 2, 'y' => 0, 'value' => 2 )
		];

		$this->game = new Game($options);

		//$COMMAND = $game->getCommand();
		//print_r($this->game->executeCommand($COMMAND->CHANGE_DIRECTION, (object) [ 'id' => 12, 'direction' => 'left']));
	}

	// Хороший ответ, возвращаем данные
	private function good($text) {
	    return [
	        'result' => false,
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