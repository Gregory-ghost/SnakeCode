<?php

class Snake {
    public $id;
    public $name;
    public $body;
    public $direction;

    public function __construct($opions = null)
    {
        if ($opions) {
            $this->id = $opions->id;
            $this->name = $opions->name;
            $this->body = $opions->body;
            $this->direction = $opions->direction;
        }
    }
}
