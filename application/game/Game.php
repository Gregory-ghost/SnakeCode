<?php

require_once 'struct\Struct.php';
require_once 'logic\Logic.php';
require_once 'input\Input.php';

class Game {

    private $struct;
    private $logic;
    private $input;

    public function __construct($db) {
        $this->db = $db;
        $this->struct = new Struct();
        $this->logic  = new Logic($this->struct);
        $this->input  = new Input($this->logic);
    }

    private function startGame($map_id, $user_id) {
        if($map_id && $user_id) {
            $this->getData($map_id);
            // проверка на существование змейки
            $this->logic->checkUserSnake($map_id, $user_id);
            // проверка на существование еды на карте
            $this->logic->checkMapFood($map_id);
            return true;
        }
        return false;
    }
    // Проверка на конец игры
    public function finishGame($map_id, $user_id) {
        if($map_id && $user_id) {
            $this->getData($map_id);
            // проверка на существование змейки
            if($this->logic->isDieUserSnake($map_id, $user_id)) {
                return (object) array(
                    'finish' => true,
                    'score' => 0,
                );
            }
        }
        return false;
    }

	// Сформировать структуру
    public function getData($map_id) {
        if ($map_id) {
            $map = $this->db->getMapById($map_id);
            if ($map) {
                $snakes = $this->db->getSnakes($map_id);
                $this->struct->addSnakes($snakes);
                foreach($snakes as $key => $snake) {
                    $snakeBody = $this->db->getSnakeBody($snake->id);
                    $this->struct->addSnakeBody($snakeBody);
                }
                $foods = $this->db->getFoods($map_id);
                $this->struct->addFoods($foods);
                $maps = $this->db->getMaps();
                $this->struct->addMaps($maps);
                return $this->struct;
            }
        }
        return false;
    }

    // записать измененные данные в БД
    public function updateData($map_id) {
        if ($map_id && $this->db->isTimeToUpdate($map_id, 1000)) {
            $this->logic->moveSnakes();
            $this->db->updateSnakes($map_id, $this->struct->snakes);
            //$this->db->updateFoods($map_id, $this->struct->foods);
            $this->db->updateMapLastUpdated($map_id);
            return true;
        }
        return false;
    }

    public function getCommand() {
        return (object) $this->input->getCommand();
    }

    public function executeCommand($name, $options = null) {
        $COMMAND = (object) $this->input->getCommand();
        switch ($name) {
            case $COMMAND->GET_MAPS  : return $this->db->getMaps();
            case $COMMAND->GET_SCENE : return $this->getData($options->map_id);
            case $COMMAND->START_GAME: return $this->startGame($options->map_id, $options->user_id);
            case $COMMAND->FINISH_GAME: return $this->finishGame($options->map_id, $options->user_id);
        }
        $this->getData($options->map_id);
        return $this->input->executeCommand($name, $options);
    }
	
	// Возвращает нашу структуру целиком
    public function getStruct() {
        return $this->struct;
    }
}
