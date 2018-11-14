<?php

class Logic {
    private $struct;

    public function __construct($struct) {
        $this->struct = $struct;
    }

    /*
        * Удав
        * Описаны основные функции для удава
        * Получение, создание, удаление, передвижение
    */

    // Получить удава
    public function getSnake( $options = null ) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ($snake->id == $options) {
                    return $snake;
                }
            }
        }
        return false;
    }

    // Получить удава
    public function getSnakeKey( $options = null ) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $key => $snake) {
                if ($snake->id == $options) {
                    return $key;
                }
            }
        }
        return false;
    }

    // Создать удава
    public function createSnake($options = null) {
        if ( $options ) {
            $this->struct->snakes[] = new Snake($options);
            return true;
        }
        return false;
    }

    // Уничтожить удава
    public function destroySnake($options = null) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $key => $snake) {
                if ( $snake->id == $options ) {
                    unset($this->struct->snakes[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    // Изменить направление удава
    public function changeDirection($options = null)
    {
        if ($options and isset($options->id)) {
            $snake = $this->getSnake($options->id);
            if ( $snake && isset($options->direction) ) {
                $direction = $options->direction;
                // Проверка на противоположные направления
                switch($direction) {
                    case 'left':
                        if($snake->direction == 'right') {
                            return true;
                        }
                        break;
                    case 'right':
                        if($snake->direction == 'left') {
                            return true;
                        }
                        break;
                    case 'up':
                        if($snake->direction == 'down') {
                            return true;
                        }
                        break;
                    case 'down':
                        if($snake->direction == 'up') {
                            return true;
                        }
                        break;
                }
                $isSet = $this->setSnakeProperty((object)array(
                    'id' => $options->id,
                    'direction' => $options->direction,
                ));
                return true;
            }
        }
        return false;
    }
    public function moveSnakes() {
        $snakes = $this->struct->snakes;
        foreach($snakes as $snake) {
            $this->moveSnake($snake->id);
        }
    }

    // Подвинуть удава на 1 клетку
    public function moveSnake($options = null) {
        if ( $options ) {
            $id = $options;
            $snake = $this->getSnake($id);
            $body = $snake->body;
            if ( $body ) {
                // Координаты головы удава
                $x = $body[0]->x;
                $y = $body[0]->y;

                // Направление
                switch ( $snake->direction ) {
                    case 'up':
                        $y -= 1;
                        break;
                    case 'down':
                        $y += 1;
                        break;
                    case 'left':
                        $x -= 1;
                        break;
                    case 'right':
                        $x += 1;
                        break;
                }

                $newPos = (object) array(
                    'id' => $id,
                    'x' => $x,
                    'y' => $y,
                );

                // Столкновение с едой
                $food = $this->triggerFood($id);
                if ( $food ) {
                    // Увеличиваем счетчик eating
                    $eating = $snake->eating;
                    $eating += $food->value;
                    $res = $this->updateSnakeEating((object) array(
                        'id'     => $snake->id,
                        'eating' => $eating,
                    ));
                    if(!$res) return false;

                    $this->destroyFood($food->id);
                }


                // Проверяем позицию змеи на столкновение
                $isMove = $this->isCanMove($newPos);
                // Подвигаем змею
                if ( $isMove ) {
                    // Ни с чем не столкнулись, проверяем на состояние
                    $isUpdatePosition = $this->updateSnakePosition( $newPos );
                    if ( $isUpdatePosition ) {
                        // Координаты обновлены
                        return true;
                    }
                } else {
                    // Столкнулись, поэтому уничтожаем питона
                    $this->destroySnake($id);
                }
            }
        }
        return false;
    }

    // Установить значение змеи
    private function setSnakeProperty( $options = null ) {
        if ( $options and isset($options->id) ) {
            $snakes = $this->struct->snakes;
            $snakeKey = $this->getSnakeKey($options->id);
            if ($snakeKey+1 > 0) {
                foreach ($options as $key => $value) {
                    if ($key != 'id') {
                        $snakes[$snakeKey]->{$key} = $value;
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function updateSnakeEating($options) {
        if($options) {
            if(isset($options->id) and isset($options->eating)) {
                $isSet = $this->setSnakeProperty((object) array(
                    'id' => $options->id,
                    'eating' => $options->eating,
                ));
                return true;
            }
        }
        return false;
    }
    // Сдвиг удава
    private function updateSnakePosition( $options = null ) {
        if ( $options and isset($options->id) ) {
            $snake = $this->getSnake($options->id);
            if ( $snake and isset($options->x) and isset($options->y) ) {
                $head = (object) array(
                    'snake_id' => $options->id,
                    'x' => $options->x,
                    'y' => $options->y,
                );

                // Добавляем в начало
                $this->addSnakeBody($head);
                if(isset($snake->eating)) {
                    if ( $snake->eating > 0 ) {
                        // Увеличиваем змею, если она ест
                        $eating = $snake->eating;
                        $eating--;

                        $res = $this->updateSnakeEating((object) array(
                            'id'     => $snake->id,
                            'eating' => $eating,
                        ));
                        return $res;
                    } else {
                        // Не увеличиваем змею, подвигаем
                        // Убираем последний элемент
                        $lastElement = $this->destroySnakeBodyTail($options->id);
                        if($lastElement) return true;
                    }
                }
            }
        }
        return false;
    }

    // Может ли удав двигаться дальше
    private function isCanMove($options = null) {
        if ( $options and isset($options->x) and isset($options->y) and isset($options->id) ) {
            $x = $options->x;
            $y = $options->y;

            $snake = $this->getSnake($options->id);
            if($snake) {
                // Выход за границы карты
                $map = $this->getMap($snake->map_id);
                if($map) {
                    if($x > $map->width or $x < 0) {
                        return false;
                    }
                    if($y > $map->height or $y < 0) {
                        return false;
                    }
                }

                // Проверяем врезался ли в другого удава
                $isCrashed = $this->isCrashedInSnake($options);
                if ( $isCrashed ) {
                    return false;
                }
                return true;
            }
        }
        return false;
    }

    // Проверяем врезался ли в другого удава
    private function isCrashedInSnake( $options = null ) {
        if ( $options and isset($options->id) ) {
            if(isset($options->x) and isset($options->y)) {

                $snake = $this->getSnake($options->id);
                $bodys = $snake->body;
                $snakes = $this->struct->snakes;

                foreach ($snakes as $enemySnake) {
                    if($enemySnake->id != $snake->id) {
                        foreach($enemySnake as $bodyEnemy) {
                            $body = $bodys[0];
                            if($bodyEnemy->x == $body->x and $bodyEnemy->y == $body->y) {
                                // Врезались в удава
                                return true;
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

    // Создать тело удава
    public function addSnakeBody($options = null) {
        if ( $options ) {
            if(isset($options->snake_id)) {
                $snake = $this->getSnake($options->snake_id);
                $body = $snake->body;
                $body[] = $options;
                // добавляем в начало
                array_unshift($body, $options);
                $isSet = $this->setSnakeProperty((object)array(
                    'id' => $options->snake_id,
                    'body' => $body,
                ));
                return true;
            }
        }
        return false;
    }
    // Получить тело удава
    public function getSnakeBody( $options = null ) {
        if ( $options ) {
            $snake = $this->getSnake($options);
            $body = $snake->body;
            return $body;
        }
        return false;
    }
    // Уничтожить удава
    public function destroySnakeBody($options = null) {
        if ( $options ) {
            $bodys = $this->struct->snakes_body;
            foreach ($bodys as $key => $body) {
                if ( $body->id == $options ) {
                    unset($this->struct->snakes[$key]);
                    return true;
                }
            }
        }
        return false;
    }
    // Уничтожить удава
    public function destroySnakeBodyBySnakeId($options = null) {
        if ( $options ) {
            $isFound = false;
            $bodys = $this->struct->snakes_body;
            foreach ($bodys as $key => $body) {
                if ( $body->snake_id == $options ) {
                    unset($this->struct->snakes[$key]);
                    $isFound = true;
                }
            }
            return $isFound;
        }
        return false;
    }
    // Уничтожить удава
    public function destroySnakeBodyById($options = null) {
        if ( $options ) {
            $snake = $this->getSnake($options);
            $bodys = $snake->body;
            foreach ($bodys as $key => $body) {
                if(isset($body->id)) {
                    if ( $body->id == $options ) {
                        unset($bodys[$key]);
                        $isSet = $this->setSnakeProperty((object)array(
                            'id' => $options,
                            'body' => $bodys,
                        ));
                        return true;
                    }
                }
            }
        }
        return false;
    }
    // Убрать хвост
    public function destroySnakeBodyTail($options = null) {
        if($options) {
            $snake = $this->getSnake($options);
            $bodys = $snake->body;

            // последний элемент извлечение
            array_pop($bodys);
            $isSet = $this->setSnakeProperty((object)array(
                'id' => $options,
                'body' => $bodys,
            ));
        }
        return false;
    }

    // Получает размер карты
    private function getMap( $options = null ) {
        if ( $options ) {
            $maps = $this->struct->maps;
            foreach ($maps as $map) {
                if ($map->id == $options) {
                    return $map;
                }
            }
        }
        return false;
    }


    /*
        * Еда
        * Описаны основные функции с едой
        * Добавление, получение, съесть
    */

    // Добавить новую еду на карту
    public function addFood($options = null) {
        if ( $options ) {
            $this->struct->foods[] = new Food($options);
            return true;
        }
        return false;
    }
    // Получить еду
    public function getFood( $options = null ) {
        if ( $options ) {
            $foods = $this->struct->foods;
            foreach ( $foods as $food ) {
                if ( $food->id == $options ) {
                    return $food;
                }
            }
        }
        return false;
    }
    // Съесть еду
    public function destroyFood( $options = null ) {
        if ( $options ) {
            $foods = $this->struct->foods;
            foreach ($foods as $key => $food) {
                if ( $food->id == $options ) {
                    unset($this->struct->foods[$key]);
                    return true;
                }
            }
        }
        return false;
    }
    // Змея наехала головой на еду
    public function triggerFood( $options = null ) {
        if ( $options ) {
            $body = $this->getSnakeBody($options);
            if ( $body ) {
                $head = $body[0];
                $foods = $this->struct->foods;
                foreach ($foods as $key => $food) {
                    if ( $food->x == $head->x and $food->y == $head->y ) {
                        return $food;
                    }
                }
            }
        }
        return false;
    }

    /*
        * Сцена
    */

    // Получение информации о сцене
    public function getScene ( $options = null ){
        return false;
        if ( $options ) {
            $this->struct->maps = $this->db->getMaps();
            $this->struct->foods = $this->db->getFoods();
            $this->struct->users = $this->db->getUsers();
            $this->struct->snakes = $this->db->getSnakes();
            $this->struct->snakesBody = $this->db->getSnakesBody();
            $this->struct->system = $this->db->getSystem();

            if  ( session_id() ) {
                $token = $_SESSION['token_id'];
                $this->struct->myUser = $this->db->getUserByToken($token);
            } else {
                $this->struct->myUser = (object) array(
                    'id'    => 0,
                    'name'  => 'noname',
                    'login' => 'nologin',
                );
            }
            // Если существует (isset)
            if (isset($options->id)) {
                $map = $this->db->getMapById($options->id);
                $time = time();
                $next_time = 4; // 20 seconds
                if ($time > $map->last_updated + $next_time) {
                    // Показываем сцену
                    $this->moveSnakes();
                    //$this->db->updateMapLastUpdated($options->id, time());
                    return $this->struct;
                }
            }
        }
        return false;
    }
}
