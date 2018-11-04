<?php

class Map {
    public $id;
    public $width;
    public $height;
    public $snake_size;
    public $last_updated;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->width)) {
                $this->width = $options->width;

            }
            if(isset($options->height)) {
                $this->height = $options->height;

            }
            if(isset($options->last_updated)) {
                $this->last_updated = $options->last_updated;

            }
            if(isset($options->snake_size)) {
                $this->snake_size = $options->snake_size;

            }
        }
    }
}
