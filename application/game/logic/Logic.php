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
        // Если есть переменная options,
        // то выдает удавов из структуры
        if ( $options ) {
            $snakes = $this->struct->snakes;
            // Перебирает массив snakes
            // На каждой итерации значение текущего
            // элемента присваивается переменной snake
            foreach ($snakes as $snake) {
                // если id удава = options
                if ($snake->id == $options) {
                    // Возвращает удава
                    return $snake;
                }
            }
        }
        return false;
    }

    // Получить удава по id пользователя
    public function getSnakeByUserId( $options = null ) {
        if ( $options ) {
            // Если есть переменная options,
            // то выдает удава из структуры
            $snakes = $this->struct->snakes;
            // Перебирает массив snakes
            // На каждой итерации значение текущего
            // элемента присваивается переменной snake
            foreach ($snakes as $snake) {
                // если id пользователя = options
                if ($snake->user_id == $options) {
                    // Возвращает удава
                    return $snake;
                }
            }
        }
        return false;
    }

    // Получить удава
    public function getSnakeKey( $options = null ) {
        if ( $options ) {
            // Если есть переменная options,
            // то выдает удавов из структуры
            $snakes = $this->struct->snakes;
            // Перебирает массив snakes
            // На каждой итерации
            // присвоит ключ текущего элемента
            // переменной $key
            foreach ($snakes as $key => $snake) {
                // если id змейки = options
                if ($snake->id == $options) {
                    // Возвращает ключ
                    return $key;
                }
            }
        }
        return false;
    }

    // Создать удава
    public function createSnake($options = null) {
        // Если есть переменная options,
        // то создаст нового удава в струткуре удавов
        if ( $options ) {
            $this->struct->snakes[] = new Snake($options);
            return true;
        }
        return false;
    }

    // Уничтожить удава
    public function destroySnake($options = null) {
        // Если есть переменная options,
        // то выдает удавов из структуры
        if ( $options ) {
            $snakes = $this->struct->snakes;
            // Перебирает массив snakes
            // На каждой итерации
            // присвоит ключ текущего элемента
            // переменной $key
            foreach ($snakes as $key => $snake) {
                // если id удава = options
                if ( $snake->id == $options ) {
                    // удалит удава по ключу в струтуре
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
        // Если есть переменная options и
        // существует id пользователя, то
        // выдаст удава по id пользователя
        if ($options and isset($options->user_id)) {
            $snake = $this->getSnakeByUserId($options->user_id);
            // Если есть переменная snake и
            // существует направление,
            // то выдает направление из options
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
                // направление удава = направление из options
                $snake->direction = $options->direction;
                return true;
            }
        }
        return false;
    }

    // Перемещение удава
    public function moveSnakes() {
        // Выдаст удавов из струтуры
        $snakes = $this->struct->snakes;
        // Перебирает массив snakes
        // На каждой итерации значение текущего
        // элемента присваивается переменной snake
        foreach($snakes as $snake) {
            // выдает тело по ссылке на удава
            $body = &$snake->body;
            // если не существует тела ,
            // то присвоить координаты 0,0
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


            // Проверяем позицию удава на столкновение
            $isMove = $this->isCanMove($newPos);
            // Подвигаем удава
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
                // Столкнулись, поэтому уничтожаем удава
                $this->destroySnake($snake->id);
            }

        }
        return false;
    }

    // Установить значение удава
    private function setSnakeProperty( $options = null ) {
        // Если есть переменная options и
        // существует id , то
        // Выдаст удавов из струтуры и ключ из options
        if ( $options and isset($options->id) ) {
            $snakes = $this->struct->snakes;
            $snakeKey = $this->getSnakeKey($options->id);
            if ($snakeKey+1 > 0) {
                // Перебирает массив options
                // на каждой итерации
                // присвоит ключ текущего элемента
                // переменной $key
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
        // Если есть переменная options и
        // существуют x, y и id удава, то
        // выдаст x,y из options и удава по id
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
        // Если есть переменная options и
        // существуют id удава,
        // если существуют x и y,
        // то выдаст удава по id, тело и
        // удавов из структуры
        if ( $options and isset($options->snake_id) ) {
            if(isset($options->x) and isset($options->y)) {

                $snake = $this->getSnake($options->snake_id);
                $bodys = $snake->body;
                $snakes = $this->struct->snakes;
                // перебирает массив snakes
                // на каждой итерации значение текущего
                // элемента присваивается переменной enemySnake
                foreach ($snakes as $enemySnake) {
                    // Если id враждебного удава не равен id удава,
                    // то перебирает массив enemySnake
                    // на каждой итерации значение текущего
                    // элемента присваивается переменной bodyEnemy
                    if($enemySnake->id != $snake->id) {
                        foreach($enemySnake as $bodyEnemy) {
                            $body = $bodys[0];
                            // если сущесвуют координаты тела врага и координаты тела,
                            // то врезались в удава
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
        // Если есть переменная options,
        // то выдаст удава и тело, возвращает тело
        if ( $options ) {
            $snake = $this->getSnake($options);
            $body = $snake->body;
            return $body;
        }
        return false;
    }
    // Уничтожить только одну часть тела
    public function destroySnakeBody($options = null) {
        // Если есть переменная options,
        // если существует id удава и id,
        // то выдаст удавов из структуры
        if ( $options ) {
            if(isset($options->snake_id) && isset($options->id)) {
                $snakes = $this->struct->snakes;
                // Перебирает массив snakes
                // на каждой итерации
                // присвоит ключ текущего элемента
                // переменной $key
                foreach ($snakes as $key => $snake) {
                    // Если id удава = id удава из options,
                    // то перебирает массив $snake->body
                    // на каждой итерации
                    // присвоит ключ текущего элемента
                    // переменной $key2
                    if($snake->id == $options->snake_id) {
                        foreach($snake->body as $key2 => $item) {
                            // Если существует id предмета
                            // если id предмета = options->id,
                            // то уничтожает часть тела
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
        // Если есть переменная options,
        // если существует id удава,
        // то выдаст удавов из структуры
        if ( $options ) {
            if(isset($options->snake_id)) {
                $snakes = $this->struct->snakes;
                // Перебирает массив snakes
                // на каждой итерации
                // присвоит ключ текущего элемента
                // переменной $key
                foreach ($snakes as $key => $snake) {
                    // если id удава = options->id,
                    // то перебирает массив $snake->body
                    // на каждой итерации
                    // присвоит ключ текущего элемента
                    // переменной $key2
                    if($snake->id == $options->snake_id) {
                        foreach($snake->body as $key2 => $item) {
                            // уничтожает тело
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
        // Если есть переменная options,
        // то выдаст удава и хвост
        if($options) {
            $snake = $this->getSnake($options);
            $bodys = $snake->body;

            // последний элемент извлечение
            $last_elem = array_pop($bodys);
            array_push($bodys, $last_elem);
            // если существует id у последнего элемента,
            // то выдаст переменную options2 с массивом (id, snake_id)
            if(isset($last_elem->id)) {
                $options2 = (object)array(
                    'id' => $last_elem->id,
                    'snake_id' => $options,
                );
                // убирает хвост
                $this->destroySnakeBody($options2);
            }
        }
        return false;
    }

    // Получает размер карты
    private function getMap( $options = null ) {
        // Если есть переменная options,
        // то выдаст карту из структуры
        if ( $options ) {
            $maps = $this->struct->maps;
            // перебирает массив maps
            // на каждой итерации значение текущего
            // элемента присваивается переменной map
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
        // Если есть переменная options,
        // то создает новую еду в структуру
        if ( $options ) {
            $this->struct->foods[] = new Food($options);
            return true;
        }
        return false;
    }
    // Получить еду
    public function getFood( $options = null ) {
        // Если есть переменная options,
        // то выдает еду из структуры
        if ( $options ) {
            $foods = $this->struct->foods;
            // перебирает массив foods
            // на каждой итерации значение текущего
            // элемента присваивается переменной food
            foreach ( $foods as $food ) {
                // если id еды = options
                if ( $food->id == $options ) {
                    return $food;
                }
            }
        }
        return false;
    }
    // Съесть еду
    public function destroyFood( $options = null ) {
        // Если есть переменная options,
        // то выдает еду из структуры
        if ( $options ) {
            $foods = $this->struct->foods;
            // Перебирает массив foods
            // на каждой итерации
            // присвоит ключ текущего элемента
            // переменной $key
            foreach ($foods as $key => $food) {
                // если id еды = options,
                // то удаляет еду
                if ( $food->id == $options ) {
                    $this->struct->foods[$key]->deleted_at = true;
                    return true;
                }
            }
        }
        return false;
    }
    // Удав наехал головой на еду
    public function triggerFood( $options = null ) {
        // Если есть переменная options,
        // то выдает удава и тело
        if ( $options ) {
            $snake = $this->getSnake($options);
            $body = $snake->body;
            // если есть тело,
            // то выдаст голову из $body[0], еду из структуры
            if ( $body ) {
                $head = $body[0];
                $foods = $this->struct->foods;
                // Перебирает массив foods
                // на каждой итерации
                // присвоит ключ текущего элемента
                // переменной $key
                foreach ($foods as $key => $food) {
                    // Если координаты еды и головы совпадают,
                    // то возвращает еду
                    if ( $food->x == $head->x and $food->y == $head->y ) {
                        return $food;
                    }
                }
            }
        }
        return false;
    }
    public function newFood($map_id) {
        // Если есть переменная map_id,
        // то выдаст карту по id и optionsFood
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
        // Если есть переменная options,
        // если существует id пользователя и id карты,
        // то выдаст удавов и еду из структуры
        if($options) {
            if(isset($options->user_id) && isset($options->map_id)) {

                $snakes = &$this->struct->snakes;
                $foods = &$this->struct->foods;
                // Есть ли у пользователя созданные змейки


                // Перебирает массив snakes
                // на каждой итерации
                // присвоит ключ текущего элемента
                // переменной $key
                foreach($snakes as $key => $snake) {
                    if($snake->user_id == $options->user_id && $snake->map_id == $options->map_id) {
                        // Удаляем
                        $snake->deleted_at = true;
                    }
                }
                // Перебирает массив foods
                // на каждой итерации
                // присвоит ключ текущего элемента
                // переменной $key
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
        // Если есть переменная options,
        // если существуют id пользователя и id карты,
        // то выдаст удава по id
        if($options) {
            if(isset($options->user_id) && isset($options->map_id)) {
                $snake = $this->getSnakeByUserId($options->user_id);
                // если есть переменная удав,
                // то вернет уничтожение удава по id
                if ($snake) {
                    return $this->destroySnake($snake->id);
                }
            }

        }
        return false;
    }

}
