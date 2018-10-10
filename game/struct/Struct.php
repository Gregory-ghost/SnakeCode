<?php

require_once 'Snake.php';
require_once 'Food.php';

class Struct {
    public $map;
    public $snakes;
    public $foods;


    public function __construct($options = null)
    {
        if ($options) {
            $this->map = $options->map;
            $this->snakes = [];
            foreach ($options->snakes as $snake) {
                $this->snakes[] = new Snake($snake);
            }
            $this->foods = [];
            foreach ($options->foods as $foods) {
                $this->foods[] = new Food($foods);
            }
        }
     }
}
