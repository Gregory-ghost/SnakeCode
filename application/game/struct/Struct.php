<?php

require_once 'Map.php';
require_once 'Snake.php';
require_once 'Food.php';
require_once 'User.php';
require_once 'SnakeBody.php';
require_once 'System.php';
require_once 'MyUser.php';

class Struct {
    public $maps;
    public $snakes;
    public $foods;
    public $users;
    public $snakesBody;
    public $system;
    public $myUser;

    // TODO :: добавить таблицы system, user, snake_body, map


    public function __construct($options = null)
    {
        if ($options) {
            if (isset($options->maps)) {
                $this->maps = [];
                foreach ($options->maps as $item) {
                    $this->maps[] = new Map($item);
                }
            }
            if (isset($options->snakes)) {
                $this->snakes = [];
                foreach ($options->snakes as $snake) {
                    $this->snakes[] = new Snake($snake);
                }
            }
            if (isset($options->foods)) {
                $this->foods = [];
                foreach ($options->foods as $food) {
                    $this->foods[] = new Food($food);
                }
            }
            if (isset($options->users)) {
                $this->users = [];
                foreach ($options->users as $user) {
                    $this->users[] = new User($user);
                }
            }
            if (isset($options->snakesBody)) {
                $this->snakesBody = [];
                foreach ($options->snakesBody as $body) {
                    $this->snakesBody[] = new SnakeBody($body);
                }
            }
            if (isset($options->system)) {
                $this->system = [];
                foreach ($options->system as $system) {
                    $this->system[] = new System($system);
                }
            }
            if (isset($options->myUser)) {
                $this->myUser = new MyUser($options->myUser);
            }
        }
     }
}
