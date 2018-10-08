<?php

require_once 'Snake.php';
require_once 'Food.php';

class Struct {
    public $map;
    public $snakes;
    public $foods;


    public function __construct($opions = null)
    {
        if ($opions) {
            $this->map = $opions->map;
            $this->snakes = [];
            foreach ($opions->snakes as $snake) {
                $this->snakes[] = new Snake($snake);
            }
            $this->foods = [];
            foreach ($opions->foods as $foods) {
                $this->foods[] = new Food($foods);
            }
        }
     }
}
