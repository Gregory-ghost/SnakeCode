<?php

class Food {
    public $x;
    public $y;
    public $id;
    public $type;
    public $value;
    public $map_id;
    public $deleted_at;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->map_id)) {
                $this->map_id = $options->map_id;

            }
            if(isset($options->x)) {
                $this->x = $options->x;

            }
            if(isset($options->y)) {
                $this->y = $options->y;

            }
            if(isset($options->type)) {
                $this->type = $options->type;

            }
            if(isset($options->value)) {
                $this->value = $options->value;

            }
            if(isset($options->deleted_at)) {
                $this->deleted_at = $options->deleted_at;

            }
        }
    }
}
