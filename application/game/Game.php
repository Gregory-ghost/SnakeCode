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

    // Получить все карты
    public function getMaps() {
        $maps = $this->db->getMaps();
        if($maps) {
            return $maps;
        }
        return false;
    }
	// Сформировать структуру
    public function getData($map_id) {
        if ($map_id) {
            $map = $this->db->getMapById($map_id);
            if($map) {
                $snakes = $this->db->getSnakes($map_id);
                $this->struct->addSnakes($snakes);
                foreach($snakes as $kes => $snake) {
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
            $this->getData($map_id);
            $struct = new stdClass();
            $struct->snakes = $this->struct->snakes;
            $struct->foods = $this->struct->foods;

            $map = $this->db->getMapById($map_id);
            $getTime = $this->db->getServerTime(); // todo
            $time = $getTime->time;
            $next_time = 0.05; // время сравнения для движения
            if(isset($map->last_updated)) {
                if ($time > $map->last_updated + $next_time) {
                    // Показываем сцену
                    $this->logic->moveSnakes();
                    $this->updateData($map_id);
                }
            }
            $data = $this->getData($map_id);
//            print_r($data);
            return $data;
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
