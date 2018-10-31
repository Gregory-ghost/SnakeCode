<?php

class Map {
    public $id;
    public $width;
    public $height;

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
        }
    }
}
