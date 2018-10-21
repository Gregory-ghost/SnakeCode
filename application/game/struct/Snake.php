<?php

class Snake {
    public $id;
    public $name;
    public $body;
    public $direction;

    public function __construct($options = null)
    {
        if ($options) {
            $this->id = $options->id;
            $this->name = $options->name;
            $this->body = $options->body;
            $this->direction = $options->direction;
            $this->eating = $options->eating;
        }
    }
}
