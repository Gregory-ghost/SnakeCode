<?php

class Snake {
    public $id;
    public $name;
    public $body;
    public $direction;

    public function __construct($options = null)
    {

        // TODO :: переделать запросы, вынести body


        if ($options and isset($options->id) and isset($options->name)
        and isset($options->body) and isset($options->direction) and isset($options->eating)) {
            $this->id = $options->id;
            $this->name = $options->name;
            $this->body = $options->body;
            $this->direction = $options->direction;
            $this->eating = $options->eating;
        }
    }
}
