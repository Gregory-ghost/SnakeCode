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
            $snakes = $this->struct->snake;
            foreach ($snakes as $key => $snake) {
                if ( $snake->id == $options ) {
                    unset($this->struct->snakes[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    // Подвинуть удава на 1 клетку
    public function moveSnake($options = null) {
        if ( $options ) {
            $snake = $this->getSnake($options);
            if ( $snake ) {
                // Координаты головы удава
                $x = $snake->body[0]->x;
                $y = $snake->body[0]->y;

                // Направление
                switch ( $snake->direction ) {
                    case 'up':
                        $y--;
                        break;
                    case 'down':
                        $y++;
                        break;
                    case 'left':
                        $x--;
                        break;
                    case 'right':
                        $x++;
                        break;
                }

                $cOptions = (object) array(
                    'id' => $options,
                    'x' => $x,
                    'y' => $y,
                );

                $food = $this->triggerFood($options);
                if ( $food ) {
                    // Проверяем тип еды с которой соприкаснулись
                    if ( $food->type == "poison" ) {
                        // Это яд
                    } else {
                        // Это обычная еда
                    }
                    // Увеличиваем счетчик eating
                    $eating = $snake->eating;
                    $eating += $food->value;
                    $isSet = $this->setSnakeProperty((object) array(
                        'id' => $snake->id,
                        'eating' => $eating,
                    ));
                    // Съесть еду
                    $this->eatFood($food->id);
                }


                // Проверяем позицию змеи на столкновение
                $isMove = $this->isCanMove($cOptions);
                if ( $isMove ) {
                    // Проверяем врезался ли в другого удава
                    $isCrashed = $this->isCrashedInSnake($cOptions);
                    if ( $isCrashed ) {
                        // Столкнулись, поэтому уничтожаем питона
                        $this->destroySnake($options);
                        $isMove = false;
                    }
                }
                // Подвигаем змею
                if ( $isMove ) {
                    // Ни с чем не столкнулись, проверяем на состояние
                    $isUpdatePosition = $this->setPositionSnake( $cOptions );
                    if ( $isUpdatePosition ) {
                        // Координаты обновлены
                        return true;
                    }

                }
                return false;


            }
        }
        return false;
    }

    // Установить значение змеи
    private function setSnakeProperty( $options = null ) {
        if ( $options and isset($options->id) ) {
            $snakes = $this->struct->snakes;
            $snakeKey = $this->getSnakeKey($options->id);
            if ($snakeKey) {
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

    // Установить координаты удава
    private function setPositionSnake( $options = null ) {
        if ( $options and isset($options->id) ) {
            $snake = $this->getSnake($options->id);
            if ( $snake and isset($options->x) and isset($options->y) ) {
                $body = $snake->body;
                $head = (object) array(
                    'x' => $options->x,
                    'y' => $options->y,
                );
                // Добавляем в начало
                array_unshift($body, $head);
                // Убираем последний элемент
                $lastElement = array_pop($body);

                if ( $snake->eating > 0 ) {
                    // Увеличиваем змею, если она ест
                    $eating = $snake->eating;
                    $eating--;
                    array_push($body, $lastElement);
                    $this->setSnakeProperty((object) array(
                        'id' => $snake->id,
                        'body' => $body,
                        'eating' => $eating,
                    ));
                } else {
                    // Не увеличиваем змею, подвигаем
                    $isSet = $this->setSnakeProperty((object) array(
                        'id' => $snake->id,
                        'body' => $body,
                    ));
                }
                return $isSet;
            }
        }
        return false;
    }

    // Может ли удав двигаться дальше
    private function isCanMove($options = null) {
        if ( $options and isset($options->x) and isset($options->y) ) {
            $mapSizeX = $this->getMapSize("x"); // Получаем размер карты X
            $mapSizeY = $this->getMapSize("y"); // Получаем размер карты Y
            $x = $options->x;
            $y = $options->y;

            // Проверяем выход за границы карты
            if ( $x > $mapSizeX or $x < 0 ) {
                return true;
            }
            if ( $y > $mapSizeY or $y < 0 ) {
                return true;
            }
        }
        return false;
    }

    // Проверяем врезался ли в другого удава
    private function isCrashedInSnake( $options = null ) {
        if ( $options and isset($options->id) ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ( $snake->id != $options->id and isset($options->x) and isset($options->y) ) {
                    // Это другой удав
                    $body = $snake->body;
                    foreach ($body as $bcoord) {
                        // Тело удава
                        if ( $bcoord->x == $options->x and $bcoord->y == $options->y ) {
                            // Врезались в удава
                            return true;
                        }
                    }

                }
            }
        }
        return false;
    }

    // Получает размер карты
    private function getMapSize( $options = null ) {
        if ($options) {
            if($options == "x") {
                $maps = $this->struct->map->sizeX;
                return $maps;
            } else if($options == "y") {
                $maps = $this->struct->map->sizeY;
                return $maps;
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
                $snake->direction = $options->direction;
                return true;
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
    public function eatFood( $options = null ) {
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

    // Змея наехала на еду
    public function triggerFood( $options = null ) {
        if ( $options ) {
            $snake = $this->getSnake($options);
            if ( $snake ) {
                $head = $snake->body[0];
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
    // Обновление сцены
    public function updateScene( $options = null ) {
        if ( $options ) {
            return true;
        }
        return false;
    }

    // Получение информации о сцене
    public function getScene ( $options = null ) {
        return $this->struct;
    }
}
