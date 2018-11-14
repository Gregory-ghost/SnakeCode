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
            }

            /*$game = $this->db->getGame($gameId);
            if ($game) {
                // заполнить игроков
                $gamers = $this->db->getGamers($gameId);
                $this->struct->fillGamers($gamers);
                // заполнить карту
                $map = $this->db->getMap($game->map_id);
                $this->struct->fillMap($map);
                // заполнить героев
                $heroes = $this->db->getHeroes($gameId);
                $this->struct->fillHeroes($heroes);
                // заполнить предметы
                // заполнить артефакты
                $artifacts = $this->db->getArtifacts($gameId);
                $this->struct->fillArtifacts($artifacts);
                // заполнить строения
                $mapBuildings = $this->db->getMapBuildings($gameId);
                $this->struct->fillMapBuildings($mapBuildings);
                // заполнить города
                $towns = $this->db->getTowns($gameId);
                $this->struct->fillTowns($towns);
                // заполнить итемы
                $items = $this->db->getItems($gameId);
                $this->struct->fillItems($items);
                return true;
            }*/
        }
        return false;
    }

    // записать измененные данные в БД
    public function updateData($gameId) {
        if ($gameId) {
            $game = $this->db->getGame($gameId);
            if ($game) {
                // записать игроков
                $this->db->updateGamers($gameId, $this->struct->gamers);
                // записать героев
                $this->db->updateHeroes($gameId, $this->struct->heroes);
                // записать артефакты
                $this->db->updateArtifacts($gameId, $this->struct->artifacts);
                // записать строения
                $this->db->updateMapBuildings($gameId, $this->struct->mapBuildings);
                // записать города
                $this->db->updateTowns($gameId, $this->struct->towns);
                // записать предметы
                $this->db->updateItems($gameId, $this->struct->items);
                return true;
            }
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
