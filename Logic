<?php

class Logic {
    private $struct;

    public function __construct($struct)
    {
        $this->struct = $struct;
    }

    private function getSnake($id)
    {

        if ($id) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ($snake->id === $id) {
                    return $snake;
                }
            }
        }
    }

    public function ChangeDirection($id, $direction)
    {
        $snake = $this->getSnake($id);
        if ($snake && $direction) {
            $snake->direction = $direction;
            return true;
        }
    }
}
