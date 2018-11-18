<?php

class Food {
    public $x;
    public $y;
    public $type;
    public $value;
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
