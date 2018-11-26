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
        // Если есть переменная options, то выдает удавов из структуры
        if ( $options ) {
            $snakes = &$this->struct->snakes;
            if(!$snakes) return false;
            // Перебирает массив snakes
            // На каждой итерации значение текущего элемента присваивается переменной snake
            foreach ($snakes as $snake) {
                if ($snake->id == $options) {
                    // Возвращает удава
                    return $snake;
                }
            }
        }
        return false;
    }

    // Получить удава по id пользователя
    public function getSnakeByUserId($userId) {
        if ($userId) {
            // Если есть переменная userid, то выдает удава из структуры
            $snakes = &$this->struct->snakes;
            if(!$snakes) return false;
            // Перебирает массив snakes
            // На каждой итерации значение текущего элемента присваивается переменной snake
            foreach ($snakes as $snake) {
                // если id пользователя = userid
                if ($snake->user_id == $userId) {
                    // Возвращает удава
                    return $snake;
                }
            }
        }
        return false;
    }

    // Создать удава
    public function createSnake($options = null) {
        // Если есть переменная options, то создаст нового удава в струткуре удавов
        if ( $options ) {
            $this->struct->snakes[] = new Snake($options);
            return true;
        }
        return false;
    }

    // Уничтожить удава
    public function destroySnake($options = null) {
        // Если есть переменная options, то выдает удава из структуры
        if ( $options ) {
            $snake = $this->getSnake($options);
            if($snake) {
                // удалит удава в струтуре
                $snake->deleted_at = true;
                return true;
            }
        }
        return false;
    }

    // Изменить направление удава
    public function changeDirection($options = null) {
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

    // Перемещение удавов
    public function moveSnakes() {
        $snakes = $this->struct->snakes;
        // Перебирает массив snakes
        // На каждой итерации значение текущего элемента присваивается переменной snake
        foreach($snakes as $snake) {
            // Направление
            $x = $snake->body[0]->x;
            $y = $snake->body[0]->y;
            switch ($snake->direction) {
                case 'up'   : $y -= 1; break;
                case 'down' : $y += 1; break;
                case 'left' : $x -= 1; break;
                case 'right': $x += 1; break;
            }
            // добавить новое тело удаву
            array_unshift($snake->body, (object) array('snake_id' => $snake->id, 'x' => $x, 'y' => $y));


            // В данный момент ест
            if ( $snake->eating > 0 ) {
                // змея кушает, хвост на месте
                $snake->eating--;
            } else if ( $snake->eating < 0 ) {
                // змея уменьшается
                // удалить ячейку хвоста
                $snake->body[count($snake->body) - 1]->deleted_at = true;
                $snake->eating++;
            } else {
                // удалить ячейку хвоста
                $snake->body[count($snake->body) - 1]->deleted_at = true;
            }


            // Столкновение с едой
            $food = $this->triggerFood($snake->id);
            if ( $food ) {
                // Увеличиваем счетчик eating
                $snake->eating += $food->value;
                $snake->score += $food->value;
                $food->deleted_at = true;
                $this->newFood($snake->map_id);
            }

            if(!isset($snake->score)) { // нету очков пользователя
                $snake->score = 0;
            }

            if(!isset($snake->body[0])) {
                // змея погибла
                $snake->deleted_at = true;
            } else {
                $this->isEatOtherSnake($snake->id); // поедание другой змейки

                // Проверяем врезался ли в другого удава
                if ($this->isCrashedInSnake($snake->id)) {
                    // Столкнулись, поэтому уничтожаем питона
                    $snake->deleted_at = true;
                }

                // Проверяем позицию змеи на столкновение
                if(!$this->isCanMove($snake->id)) {
                    // Столкнулись, поэтому уничтожаем питона
                    $snake->deleted_at = true;
                }
            }

        }
        return true;
    }


    // проверка на существование удава
    public function checkUserSnake($map_id, $user_id) {
        if($map_id and $user_id) {
            $snake = $this->getSnakeByUserId($user_id);
            if($snake) { // если есть змiй
                if(!isset($snake->body)) {
                    $body = [];
                    $body[] = (object) array(
                        'x'  => 1,
                        'y' => 1,
                    );
                    $snake->body = $body;
                    return true;
                }
            } else { // если змiя нет
                $body = [];
                $body[] = (object) array(
                    'x'  => 1,
                    'y' => 1,
                );
                $optionsSnake = (object) array(
                    'user_id' => $user_id,
                    'map_id'  => $map_id,
                    'eating' => 0,
                    'direction' => 'right',
                    'body'  => $body,
                );
                return $this->createSnake($optionsSnake);
            }
        }
        return false;
    }
    // проверка на существование удава
    public function isDieUserSnake($map_id, $user_id) {
        if($map_id and $user_id) {
            $snake = $this->getSnakeByUserId($user_id);
            if(!$snake)  {
                return true;
            }
        }
        return false;
    }


    // Может ли удав двигаться дальше
    private function isCanMove($snake_id = null) {
        if ( $snake_id ) {
            $snake = $this->getSnake($snake_id);

            if($snake) {
                $x = $snake->body[0]->x;
                $y = $snake->body[0]->y;
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
                return true;
            }
        }
        return false;
    }

    // Проверяем врезался ли в другого удава
    private function isCrashedInSnake( $snake_id = null ) {
        // Если есть переменная options и существуют id удава, если существуют x и y,
        // то выдаст удава по id, тело и удавов из структуры
        if ( $snake_id ) {
            $snake = $this->getSnake($snake_id);
            if($snake) {
                $bodys = $snake->body;


                // столкновения в себя
                foreach($bodys as $key => $body) {
                    foreach($bodys as $key2 => $body2) {
                        if(isset($body->deleted_at) or isset($body2->deleted_at)) {

                        } else if($key != $key2) {
                            if(isset($body2->x) && isset($body2->y) && isset($body->x) && isset($body->y)) {
                                if($body2->x == $body->x and $body2->y == $body->y) {
                                    // Врезались в удава
                                    return true;
                                }
                            }
                        }
                    }
                }


                $snakes = $this->struct->snakes;
                // перебирает массив snakes на каждой итерации значение текущего
                // элемента присваивается переменной enemySnake
                foreach ($snakes as $enemySnake) {
                    // Если id враждебного удава не равен id удава, то перебирает массив enemySnake
                    // на каждой итерации значение текущего элемента присваивается переменной bodyEnemy
                    if($enemySnake->id == $snake->id) {
                        // наша змейка

                    } else {
                        // вражеская змейка

                        $bodys2 = $enemySnake->body;
                        foreach($bodys as $key => $body) {
                            foreach($bodys2 as $key2 => $body2) {
                                if(isset($body->deleted_at) or isset($body2->deleted_at)) {

                                } else {
                                    if(isset($body2->x) && isset($body2->y) && isset($body->x) && isset($body->y)) {
                                        if($body2->x == $body->x and $body2->y == $body->y) {
                                            // Врезались в удава
                                            return true;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }
    // Проверяем поедание другой змейки
    private function isEatOtherSnake( $snake_id = null ) {
        if ( $snake_id ) {
            $snake = $this->getSnake($snake_id);
            if($snake) {
                $bodys = $snake->body;
                $snakes = &$this->struct->snakes;
                foreach ($snakes as $enemySnake) {
                    if($enemySnake->id != $snake->id) {
                        $bodysEnemy = $enemySnake->body;
                        $body = $bodys[0]; // голова нашей змеи
                        $bodyE = $bodysEnemy[count($bodysEnemy) - 1]; // хвост вражеской змеи
                        if(isset($bodyE->x) && isset($bodyE->y) && isset($body->x) && isset($body->y)) {
                            if($bodyE->x == $body->x and $bodyE->y == $body->y) {
                                // Поедание другого удава
                                $snake->eating++;
                                $enemySnake->eating--;
                                return true;
                            }
                        }

                    }
                }
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
            if(!$foods) return false;
            // перебирает массив foods на каждой итерации значение текущего
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
    // Получить еду
    public function getFoodByMapId( $options = null ) {
        // Если есть переменная options,
        // то выдает еду из структуры
        if ( $options ) {
            $foods = $this->struct->foods;
            if(!$foods) return false;
            // перебирает массив foods на каждой итерации значение текущего
            // элемента присваивается переменной food
            foreach ( $foods as $food ) {
                // если id карты еды = options
                if ( $food->map_id == $options ) {
                    return $food;
                }
            }
        }
        return false;
    }
    // Змея наехала головой на еду
    public function triggerFood( $options = null ) {
        // Если есть переменная options,
        // то выдает удава и тело
        if ( $options ) {
            $snake = $this->getSnake($options);
            if($snake) {
                $body = $snake->body;
                // если есть тело, то выдаст голову из $body[0], еду из структуры
                if ( $body ) {
                    $head = $body[0];
                    $foods = &$this->struct->foods;
                    // Перебирает массив foods на каждой итерации
                    // присвоит ключ текущего элемента переменной $key
                    foreach ($foods as $key => $food) {
                        // Если координаты еды и головы совпадают, то возвращает еду
                        if ( $food->x == $head->x and $food->y == $head->y ) {
                            return $food;
                        }
                    }
                }
            }
        }
        return false;
    }
    // проверка на существование еды на карте
    public function checkMapFood($map_id) {
        if($map_id) {
            $food = $this->getFoodByMapId($map_id);
            if(!$food) {
                return $this->newFood($map_id);
            }
        }
        return false;
    }
    // создание новой еды на карте
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
            return $this->createFood($optionsFood);
        }
        return false;
    }


}
