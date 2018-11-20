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

    // Получить удава по id пользователя
    public function getSnakeByUserId( $options = null ) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ($snake->user_id == $options) {
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
                    $this->struct->snakes[$key]->deleted_at = true;
                    return true;
                }
            }
        }
        return false;
    }

    // Изменить направление удава
    public function changeDirection($options = null)
    {
        if ($options and isset($options->user_id)) {
            $snake = $this->getSnakeByUserId($options->user_id);
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
                $snake->direction = $options->direction;
                return true;
            }
        }
        return false;
    }
    public function moveSnakes() {
        $snakes = $this->struct->snakes;
        foreach($snakes as $snake) {
            $body = &$snake->body;
            if ( !isset($body) ) {
                $body[] = (object) array(
                    "x" => 0,
                    "y" => 0,
                );
            }
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
                'snake_id' => $snake->id,
                'x' => $x,
                'y' => $y,
            );

            // Столкновение с едой
            $food = $this->triggerFood($snake->id);
            if ( $food ) {
                // Увеличиваем счетчик eating
                $snake->eating += $food->value;
                $this->destroyFood($food->id);
                $this->newFood($snake->map_id);
            }


            // Проверяем позицию змеи на столкновение
            $isMove = $this->isCanMove($newPos);
            // Подвигаем змею
            if ( $isMove ) {
                // Ни с чем не столкнулись, проверяем на состояние
                // Добавляем голову
                array_unshift($body, $newPos);

                if(!isset($snake->eating)) {
                    $snake->eating = 0;
                }
                if ( $snake->eating > 0 ) {
                    $snake->eating--;
                    return true;
                } else {
                    // Убираем последний элемент
                    $body[count($body) - 1]->deleted_at = true;
                    return true;
                }
            } else {
                // Столкнулись, поэтому уничтожаем питона
                $this->destroySnake($snake->id);
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


    // Может ли удав двигаться дальше
    private function isCanMove($options = null) {
        if ( $options and isset($options->x) and isset($options->y) and isset($options->snake_id) ) {
            $x = $options->x;
            $y = $options->y;

            $snake = $this->getSnake($options->snake_id);
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
        if ( $options and isset($options->snake_id) ) {
            if(isset($options->x) and isset($options->y)) {

                $snake = $this->getSnake($options->snake_id);
                $bodys = $snake->body;
                $snakes = $this->struct->snakes;

                foreach ($snakes as $enemySnake) {
                    if($enemySnake->id != $snake->id) {
                        foreach($enemySnake as $bodyEnemy) {
                            $body = $bodys[0];
                            if(isset($bodyEnemy->x) && isset($bodyEnemy->y) && isset($body->x) && isset($body->y)) {
                                if($bodyEnemy->x == $body->x and $bodyEnemy->y == $body->y) {
                                    // Врезались в удава
                                    return true;
                                }
                            }
                        }
                    }
                }
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
    // Уничтожить только одну часть тела
    public function destroySnakeBody($options = null) {
        if ( $options ) {
            if(isset($options->snake_id) && isset($options->id)) {
                $snakes = $this->struct->snakes;
                foreach ($snakes as $key => $snake) {
                    if($snake->id == $options->snake_id) {
                        foreach($snake->body as $key2 => $item) {
                            if(isset($item->id)) {
                                if ($item->id == $options->id) {
                                    $this->struct->snakes[$key]->body[$key2]->deleted_at = true;
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
    // Уничтожить все тело
    public function destroySnakeBodyBySnakeId($options = null) {
        if ( $options ) {
            if(isset($options->snake_id)) {
                $snakes = $this->struct->snakes;
                foreach ($snakes as $key => $snake) {
                    if($snake->id == $options->snake_id) {
                        foreach($snake->body as $key2 => $item) {
                            $this->struct->snakes[$key]->body[$key2]->deleted_at = true;
                        }
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
            $last_elem = array_pop($bodys);
            array_push($bodys, $last_elem);

            if(isset($last_elem->id)) {
                $options2 = (object)array(
                    'id' => $last_elem->id,
                    'snake_id' => $options,
                );
                $this->destroySnakeBody($options2);
            }
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
    public function createFood($options = null) {
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
                    $this->struct->foods[$key]->deleted_at = true;
                    return true;
                }
            }
        }
        return false;
    }
    // Змея наехала головой на еду
    public function triggerFood( $options = null ) {
        if ( $options ) {
            $snake = $this->getSnake($options);
            $body = $snake->body;
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
    public function newFood($map_id) {
        if($map_id) {

            $map = $this->getMap($map_id);
            $optionsFood = (object) array(
                'x' => rand(1, $map->width-1),
                'y'  => rand(1, $map->height-1),
                'map_id'  => $map_id,
                'type'  => 0,
                'value'  => 1,
            );
            $res = $this->createFood($optionsFood);
        }
        return false;
    }

    // Начать игру
    public function startGame($options) {
        if($options) {
            if(isset($options->user_id) && isset($options->map_id)) {

                $snakes = &$this->struct->snakes;
                $foods = &$this->struct->foods;
                // Есть ли у пользователя созданные змейки
                foreach($snakes as $key => $snake) {
                    if($snake->user_id == $options->user_id && $snake->map_id == $options->map_id) {
                        // Удаляем
                        $snake->deleted_at = true;
                    }
                }
                foreach($foods as $key => $food) {
                    if($food->map_id == $options->map_id) {
                        // Удаляем
                        $food->deleted_at = true;
                    }
                }
                $body = [];
                $body[] = (object) array(
                    'x'  => 1,
                    'y' => 1,
                );

                $this->newFood($options->map_id);
                $optionsSnake = (object) array(
                    'user_id' => $options->user_id,
                    'map_id'  => $options->map_id,
                    'eating' => 2,
                    'direction' => 'right',
                    'body'  => $body,
                );
                return $this->createSnake($optionsSnake);
            }
        }
        return false;
    }

    // Закончить игру
    public function finishGame($options) {
        if($options) {
            if(isset($options->user_id) && isset($options->map_id)) {
                $snake = $this->getSnakeByUserId($options->user_id);
                if ($snake) {
                    return $this->destroySnake($snake->id);
                }
            }

        }
        return false;
    }

}
