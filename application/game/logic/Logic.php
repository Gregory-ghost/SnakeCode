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
        if ( $options ) {
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
        if ( $options ) {
            $snake = $this->getSnake($options->id);
            if ( $snake ) {
                $body = $snake->body;
                $lastCoord = (object) array(
                    'x' => $options->x,
                    'y' => $options->y,
                );
                $tBody = [];
                foreach( $body as $bcoord ) {
                    // Сдвигаем все тело удава
                    $tBody[] = $lastCoord;
                    $lastCoord->x = $bcoord->x;
                    $lastCoord->y = $bcoord->y;
                }
                if ( $snake->eating > 0 ) {
                    // Увеличиваем змею, если она ест
                    $eating = $snake->eating;
                    $eating--;
                    $tBody[] = $lastCoord; // Добавляем к хвосту длину
                    $this->setSnakeProperty((object) array(
                        'id' => $snake->id,
                        'body' => $tBody,
                        'eating' => $eating,
                    ));
                } else {
                    // Не увеличиваем змею, подвигаем
                    $isSet = $this->setSnakeProperty((object) array(
                        'id' => $snake->id,
                        'body' => $tBody,
                    ));
                }
                return $isSet;
            }
        }
        return false;
    }

    // Может ли удав двигаться дальше
    private function isCanMove($options = null) {
        if ( $options ) {
            $mapSize = $this->getMapSize(); // Получаем размер карты
            $x = $options->x;
            $y = $options->y;
            $id = $options->id;

            // Проверяем выход за границы карты
            if ( $x > $mapSize or $x < 0 ) {
                return true;
            }
            if ( $y > $mapSize or $y < 0 ) {
                return true;
            }
        }
        return false;
    }

    // Проверяем врезался ли в другого удава
    private function isCrashedInSnake( $options = null ) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $snake) {
                if ( $snake->id != $options->id ) {
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
        $maps = $this->struct->map;
        $mapSize = count($maps);

        return $mapSize;
    }

    // Изменить направление удава
    public function changeDirection($options = null)
    {
        if ($options) {
            $snake = $this->getSnake($options->id);
            if ( $snake && $options->direction ) {
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
