<?php

require_once dirname(__DIR__).'/../modules/DB.php';

class Logic {
    private $struct;
    private $db;

    public function __construct($struct) {
        $this->startSession();
        $this->struct = $struct;
        $this->db = new DB();
    }

    private function random_string($length = 64) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function startSession() {
        if ( session_id() ) return true;
        else return session_start();
    }
    public function destroySession() {
        if ( session_id() ) {
            // Если есть активная сессия, удаляем куки сессии,
            setcookie(session_name(), session_id(), time()-60*60*24);
            // и уничтожаем сессию
            session_unset();
            session_destroy();
        }
    }

    private function genericToken() {
        return $this->random_string(64);
    }

    private function updateUserToken($id) {
        if(!$id) return false;
        $token = $this->genericToken();
        $res3 = $this->db->createUserToken($id, $token);
        if(!$res3) return false;
        // Получение токена
        $res = $this->db->getUserById($id);
        if(!$res) return false;

        $this->startSession();
        $_SESSION['token_id'] = $res->token;
        // Сохранение пользователя для дальнейшней работы
        $res4 = $this->saveUserInStruct($res->token);
        return $res4;
    }

    private function saveUserInStruct($token) {
        if ( $token ) {
            $res = $this->db->getUserByToken($token);
            if(!$res) return false;
            $options = (object) array(
                'id'    => $res->id,
                'login' => $res->login,
                'name'  => $res->name,
            );
            $this->struct->myUser = $options;
            return true;
        }
        return false;
    }

    public function getCurrentUser() {
        if ( session_id() ) {
            $token = $_SESSION['token_id'];
            return $this->saveUserInStruct($token);
        }
        return false;
    }

    // Авторизация
    public function login($options = null) {
        if ( $options ) {
            if (isset($options->login)) {
                $login = $options->login;
                $res = $this->db->getUserByLogin($login);
                if($res) {
                    if(!isset($options->password)) return false;
                    $password = $options->password;
                    // Получение пользователя
                    $res = $this->db->getUser($login, $password);
                    if(!$res) return false;

                    // Получение токена по TokenId
                    $res2 = $this->db->getTokenByTokenId($res->token);
                    if($res2) {
                        $time = time();
                        if($time > $res2->expiredAt) {
                            return $this->updateUserToken($res->id);
                        } else {
                            $res3 = $this->startSession();
                            if(!$res3) {}
                            $_SESSION['token_id'] = $res->token;
                            // Сохранение пользователя для дальнейшней работы
                            return $this->saveUserInStruct($res->token);
                        }
                    } else {
                        return $this->updateUserToken($res->id);
                    }


                }
            }
        }
        return false;
    }
    // Регистрация
    public function register($options = null) {
        if ( $options ) {
            if (isset($options->login)) {
                $login = $options->login;
                $res = $this->db->getUserByLogin($login);
                if(!$res) {
                    if(isset($options->password) && isset($options->name)) {
                        $res = $this->db->saveUser($options);
                        if(!$res) return false;

                        // Получение пользователя
                        $res2 = $this->db->getUserByLogin($login);
                        if(!$res2) return false;

                        return $this->updateUserToken($res2->id);
                    }
                }
            }
        }
        return false;
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
        if ( $options and isset($options->id) ) {
            $snake = $this->getSnake($options->id);
            if ( $snake ) {
                // Координаты головы удава
                $x = $snake->body[0]->x;
                $y = $snake->body[0]->y;

                $sizeSnake = $this->struct->map->sizeSnake;
                // Направление
                switch ( $snake->direction ) {
                    case 'up':
                        $y -= $sizeSnake;
                        break;
                    case 'down':
                        $y += $sizeSnake;
                        break;
                    case 'left':
                        $x -= $sizeSnake;
                        break;
                    case 'right':
                        $x += $sizeSnake;
                        break;
                }

                $cOptions = (object) array(
                    'id' => $options->id,
                    'x' => $x,
                    'y' => $y,
                );

                $food = $this->triggerFood($options->id);
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
                $isMove = !$this->isCanMove($cOptions);
                if ( $isMove ) {
                    // Проверяем врезался ли в другого удава
                    $isCrashed = $this->isCrashedInSnake($cOptions);
                    if ( $isCrashed ) {
                        // Столкнулись, поэтому уничтожаем питона
                        $this->destroySnake($options->id);
                        $isMove = true;
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

                $isSet = false;
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
                $maps = $this->struct->map->sizeX*$this->struct->map->sizeSnake;
                return $maps;
            } else if($options == "y") {
                $maps = $this->struct->map->sizeY*$this->struct->map->sizeSnake;
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
                $res = $this->db->updateSnakeDirection($options->id, $options->direction);
                if($res) {
                    $snake->direction = $options->direction;
                    return true;
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
            $res = $this->db->createFood($options);
            if($res) {
                return true;
            }
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
