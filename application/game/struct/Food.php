<?php

class Food {
    public $x;
    public $y;
    public $type;
    public $value;

    public function __construct($options = null)
    {
        if ($options and isset($options->x) and isset($options->y)
        and isset($options->type) and isset($options->value)) {
            $this->x = $options->x;
            $this->y = $options->y;
            $this->type = $options->type;
            $this->value = $options->value;
        }
    }
}
