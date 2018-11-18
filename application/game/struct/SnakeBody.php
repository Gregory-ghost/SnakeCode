<?php

class SnakeBody {
    public $id;
    public $snake_id;
    public $x;
    public $y;
    public $deleted_at;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->x)) {
                $this->x = $options->x;

            }
            if(isset($options->y)) {
                $this->y = $options->y;

            }
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->snake_id)) {
                $this->snake_id = $options->snake_id;

            }
            if(isset($options->deleted_at)) {
                $this->deleted_at = $options->deleted_at;

            }
        }
    }
}
