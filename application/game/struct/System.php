<?php

class System {
    public $id;
    public $name;
    public $value;
    public $login;
    public $password;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->name)) {
                $this->name = $options->name;

            }
            if(isset($options->value)) {
                $this->value = $options->value;

            }
        }
    }
}
