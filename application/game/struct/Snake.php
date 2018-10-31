<?php

class Snake {
    public $id;
    public $name;
    public $direction;
    public $eating;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->name)) {
                $this->name = $options->name;

            }
            if(isset($options->direction)) {
                $this->direction = $options->direction;

            }
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->eating)) {
                $this->eating = $options->eating;

            }
        }
    }
}
