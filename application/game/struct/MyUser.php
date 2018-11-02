<?php

class MyUser {
    public $id;
    public $login;
    public $name;
//    public $token;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->login)) {
                $this->login = $options->login;

            }
            if(isset($options->name)) {
                $this->name = $options->name;

            }
           /* if(isset($options->token)) {
                $this->token = $options->token;

            }*/
        }
    }
}
