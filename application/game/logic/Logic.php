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

    /*
        * Авторизация
        * Описаны основные функции для входа, создания сессии
     */

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
            return true;
        }
        return false;
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
    // Выход
    public function logout($options) {
        return $this->destroySession();
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
            $res = $this->db->createSnake($options);
            if(!$res) return false;
            if(isset($options->user_id)) {
                $res = $this->db->getLastSnakeByUserId($options->user_id);
                if($res) {
                    $this->struct->snakes[] = new Snake($res);
                    $res = $this->createSnakeBody((object) array(
                        'snake_id' => $res->id,
                        'x' => 0,
                        'y' => 0,
                    ));
                    if($res) return true;
                }
            }
        }
        return false;
    }

    // Уничтожить удава
    public function destroySnake($options = null) {
        if ( $options ) {
            $snakes = $this->struct->snakes;
            foreach ($snakes as $key => $snake) {
                if ( $snake->id == $options ) {
                    $res = $this->db->deleteSnake($snake->id);
                    if($res) {
                        unset($this->struct->snakes[$key]);
                        return true;
                    }
                }
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
        if ( $options and isset($options->id) ) {
            $snake = $this->getSnake($options->id);
            $body = $this->getSnakeBody($options->id);
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
                    'id' => $options->id,
                    'x' => $x,
                    'y' => $y,
                );

                // Столкновение с едой
                $food = $this->triggerFood($options->id);
                if ( $food ) {
                    // Увеличиваем счетчик eating
                    $eating = $snake->eating;
                    $eating += $food->value;
                    $res = $this->updateSnakeEating((object) array(
                        'id'     => $snake->id,
                        'eating' => $eating,
                    ));
                    if(!$res) return false;

                    $res = $this->db->deleteFood($food->id);
                    if($res) {
                        // Съесть еду
                        $this->destroyFood($food->id);
                    }
                }


                // Проверяем позицию змеи на столкновение
                $isMove = $this->isCanMove($newPos);
                // Подвигаем змею
                if ( $isMove ) {
                    // Ни с чем не столкнулись, проверяем на состояние
                    $isUpdatePosition = $this->moveSnakePosition( $newPos );
                    if ( $isUpdatePosition ) {
                        // Координаты обновлены
                        return true;
                    }
                } else {
                    $res = $this->db->deleteSnake($options->id);
                    if($res) {
                        // Столкнулись, поэтому уничтожаем питона
                        $this->destroySnake($options->id);
                        return false;
                    }
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
                $res = $this->db->updateSnakeEating($options->id, $options->eating);
                if($res) {
                    $isSet = $this->setSnakeProperty((object) array(
                        'id' => $options->id,
                        'eating' => $options->eating,
                    ));
                    return true;
                }
            }
        }
        return false;
    }

    // Сдвиг удава
    private function moveSnakePosition( $options = null ) {
        if ( $options and isset($options->id) ) {
            $snake = $this->getSnake($options->id);
            if ( $snake and isset($options->x) and isset($options->y) ) {
                $head = (object) array(
                    'snake_id' => $options->id,
                    'x' => $options->x,
                    'y' => $options->y,
                );

                $res = $this->db->createSnakeBody($head);
                if(!$res) return false;
                // Добавляем в начало
                array_unshift($this->struct->snakesBody, $head);

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
                $snakesBody = $this->struct->snakesBody;
                foreach ($snakesBody as $item) {
                    if($item->snake_id == $options->id) {
                        foreach ($snakesBody as $itemEnemy) {
                            if($item->snake_id != $itemEnemy->snake_id and $item->id != $itemEnemy->id) {
                                if ( $itemEnemy->x == $options->x and $itemEnemy->y == $options->y ) {
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

    // Создать тело удава
    public function createSnakeBody($options = null) {
        if ( $options ) {
            $res = $this->db->createSnakeBody($options);
            if(!$res) return false;
            if(isset($options->id)) {
                $res = $this->db->getLastSnakeBodyBySnakeId($options->id);
                if($res) {
                    $this->struct->snakesBody[] = new SnakeBody($res);
                    return true;
                }
            }
        }
        return false;
    }

    // Получить тело удава
    public function getSnakeBody( $options = null ) {
        if ( $options ) {
            $snakes = $this->struct->snakesBody;
            $body = [];
            foreach ($snakes as $item) {
                if ($item->snake_id == $options) {
                    $body[] = $item;
                }
            }
            return $body;
        }
        return false;
    }

    // Уничтожить удава
    public function destroySnakeBodyById($options = null) {
        if ( $options ) {
            $snakes = $this->struct->snakesBody;
            foreach ($snakes as $key => $body) {
                if(isset($body->id)) {
                    if ( $body->id == $options ) {
                        $res = $this->db->deleteSnakeBody($body->id);
                        if($res) {
                            unset($this->struct->snakesBody[$key]);
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }

    // Убрать хвост
    public function destroySnakeBodyTail($options = null) {
        if($options) {
            $snakes = $this->struct->snakesBody;
            $body = [];
            foreach ($snakes as $key => $item) {
                if ( $item->snake_id == $options ) {
                    $body[] = $item;
                }
            }

            $lastElement = array_pop($body);
            if($lastElement) {
                $res = $this->destroySnakeBodyById($lastElement->id);
                if($res) {
                    return $lastElement;
                }
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
                $res = $this->db->updateSnakeDirection($options->id, $options->direction);
                if ($res) {
                    $isSet = $this->setSnakeProperty((object)array(
                        'id' => $options->id,
                        'direction' => $options->direction,
                    ));
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
            $res = $this->db->createFood($options);
            if($res) {
                $this->struct->foods[] = new Food($options);
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
    public function destroyFood( $options = null ) {
        if ( $options ) {
            $foods = $this->struct->foods;
            foreach ($foods as $key => $food) {
                if ( $food->id == $options ) {
                    $res = $this->db->deleteFood($food->id);
                    if($res) {
                        unset($this->struct->foods[$key]);
                        return true;
                    }
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
    // Обновление сцены
    public function updateScene( $options = null ) {
        if ( $options ) {
            return true;
        }
        return false;
    }

    // Получение информации о сцене
    public function getScene ( $options = null ){
        if ( $options ) {
            $this->struct->maps = $this->db->getMaps() ;
            $this->struct->foods = $this->db->getFoods() ;
            $this->struct->users = $this->db->getUsers() ;
            $this->struct->snakesbody = $this->db->getSnakesBody() ;
            $this->struct->system = $this->db->getSystem() ;

            if  ( session_id() ) {
                $token = $_SESSION['token id'];
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
                $next_time = 20; // 20 seconds
                if ($time > $map->last_updated + $next_time) {
                    // Показываем сцену
                    $this->moveSnake();
                    $this->db->updateMapLastUpdated($options->id, time());
                    return $this->struct;
                }
            }
        }
        return false;
    }
}
