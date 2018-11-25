<?php

class Snake {
    public $id;
    public $name;
    public $direction;
    public $eating;
    public $map_id;
    public $user_id;
    public $body;
    public $score;
    public $deleted_at;

    public function __construct($options = null)
    {
        if ($options) {
            if(isset($options->name)) {
                $this->name = $options->name;
            }
            if(isset($options->direction)) {
                $this->direction = $options->direction;
            }
            if(isset($options->id)) {
                $this->id = $options->id;
            }
            if(isset($options->eating)) {
                $this->eating = $options->eating;
            }
            if(isset($options->map_id)) {
                $this->map_id = $options->map_id;
            }
            if(isset($options->user_id)) {
                $this->user_id = $options->user_id;
            }
            if(isset($options->score)) {
                $this->score = $options->score;
            }
            if(isset($options->body)) {
                $this->body = $options->body;
            }
            if(isset($options->deleted_at)) {
                $this->deleted_at = $options->deleted_at;
            }
        }
    }
}
