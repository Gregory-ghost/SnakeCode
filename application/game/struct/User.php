<?php

class User {
    public $id;
    public $name;
    public $token;
    public $login;
    public $password;
    public $score;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->id)) {
                $this->id = $options->id;

            }
            if(isset($options->name)) {
                $this->name = $options->name;

            }
            if(isset($options->token)) {
                $this->token = $options->token;

            }
            if(isset($options->login)) {
                $this->login = $options->login;

            }
            if(isset($options->password)) {
                $this->password = $options->password;

            }
            if(isset($options->score)) {
                $this->score = $options->score;

            }
        }
    }
}
