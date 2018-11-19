<?php

class Map {
    public $id;
    public $name;
    public $last_updated;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->name)) {
                $this->name = $options->name;

            }
            if(isset($options->last_updated)) {
                $this->last_updated = $options->last_updated;

            }
        }
    }
}
