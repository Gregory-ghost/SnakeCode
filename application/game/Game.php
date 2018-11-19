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

    public function getMaps() {
        $maps = $this->db->getMaps();
        if($maps) {
            return $maps;
        }
        return false;
    }
	// сформировать структуру
    public function init($map_id) {
        if ($map_id) {
            $map = $this->db->getMapById($map_id);
            if($map) {
                $snakes = $this->db->getSnakes($map_id);
                $this->struct->addSnakes($snakes);
                foreach($snakes as $snake) {
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
        if ($map_id) {
                $this->db->updateSnakes($map_id, $this->struct->snakes);
                $this->db->updateFoods($map_id, $this->struct->foods);
                $this->db->updateMapLastUpdated($map_id);
                return true;

        }
        return false;
    }

    // Получение информации о сцене
    public function getScene($map_id) {
        if ( $map_id ) {
            $this->init($map_id);
            $struct = new stdClass();
            $struct->snakes = $this->struct->snakes;
            $struct->foods = $this->struct->foods;

            $map = $this->db->getMapById($map_id);
            $time = $this->db->getServerTime(); // todo
            $next_time = 0.05; // время сравнения для движения
            if ($time > $map->last_updated + $next_time) {
                // Показываем сцену
                $this->logic->moveSnakes();
                $this->updateData($map_id);
            }
            return $this->init($map_id);
        }
        return false;
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
}
