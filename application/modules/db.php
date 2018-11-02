<?php


class DB {

    public $conn;

    public function __construct() {
        $host = 'localhost';
        $dbName = 'snake_of_pi';
        $user = 'mysql';
        $pass = 'mysql';

        $this->conn = new PDO('mysql:dbname='.$dbName.';host='.$host, $user, $pass);
    }

    // TODO :: вынести пользователя в отдельный модуль

    /*User*/
    // Получение пользователя
    public function getUsers() {
        $query = 'SELECT id, name, login FROM user ORDER BY id DESC';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    //Получить пользователя по логину
    public function getUserByLogin($login) {
        $sql = 'SELECT name, login, token FROM user WHERE login = :login ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':login', $login, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить пользоваетеля по токену
    public function getUserByToken($token) {
        $sql = 'SELECT * FROM user WHERE token = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить пользователя по id
    public function getUserById($id) {
        $sql = 'SELECT id, login, name FROM user WHERE id = :id ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить пользователя
    public function getUser($login, $password) {
        $sql = 'SELECT * FROM user WHERE login = :login and password = :password ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':login', $login, PDO::PARAM_STR);
        $stm->bindValue(':password', $password, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Изменить токен пользователя
    public function updateUserToken($id, $token) {
        $sql = "UPDATE user SET token = :token WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->fetchObject('stdClass');
    }
    //Создать пользователя
    public function saveUser($options) {
        $name = $options['name'];
        $login = $options['login'];
        $password = $options['password'];

        $sql = "INSERT INTO user (name, login, password) VALUES (:name, :login, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        $res =  $stmt->fetchObject('stdClass');

        return $res;
    }
    public function createUserToken($id, $token) {
        $expiredAt = time() + (7 * 24 * 60 * 60); // Week
        $options = array(
            'user_id' => $id,
            'token' => $token,
            'expiredAt' => $expiredAt,
        );
        $res = $this->createToken($options);
        if(!$res) return false;

        $res2 = $this->updateUserToken($id, $res->id);
        return $res2;
    }


    /*User_access_token*/
    //Создать токен пользователя
    public function createToken($options) {
        if(!$options) return false;

        $user_id = $options['user_id'];
        $token = $options['token'];
        $expiredAt = $options['expiredAt'];
        $createdAt = time();

        $sql = "INSERT INTO user_access_token (user_id, token, expiredAt, createdAt) VALUES (:user_id, :token, :expiredAt, :createdAt)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $stmt->bindValue(':token', $token, PDO::PARAM_STR);
        $stmt->bindValue(':expiredAt', $expiredAt, PDO::PARAM_STR);
        $stmt->bindValue(':createdAt', $createdAt, PDO::PARAM_STR);
        return $stmt->fetchObject('stdClass');
    }
    //Получить токен пользователя по token
    public function getTokenByToken($token) {
        $sql = 'SELECT * FROM user_access_token WHERE token = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить токен пользователя по tokenId
    public function getTokenByTokenId($token) {
        $sql = 'SELECT * FROM user_access_token WHERE id = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    //Получить токен пользователя по id
    public function getTokenById($id) {
        $sql = 'SELECT * FROM user_access_token WHERE id = :id ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }



    /*Snake*/
    // Получение питона
    public function getSnakes() {
        $query = 'SELECT * FROM snake';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить питона пользователя
    public function getUserSnakes($user_id) {
        $sql = 'SELECT * FROM snake, user WHERE user.id = :user_id AND snake.user_id=user.id ORDER BY snake.id DESC';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить питона по id
    public function getSnakeById($id) {
        $sql = 'SELECT * FROM snake WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Создать питона
    public function createSnake($options) {
        $user_id = $options['user_id'];
        $direction = $options['direction'];

        $sql = "INSERT INTO snake (user_id, direction, body) VALUES (:user_id, :direction, :body)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_STR);
        return $stmt->execute();
    }
    // Удалить питона
    public function deleteSnake($id) {
        $sql = "DELETE FROM snake WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Удалить питона пользователя
    public function deleteUserSnakes($user_id) {
        $sql = "DELETE FROM snake WHERE user_id =  :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /*Snake_body*/
    public function getSnakesBody() {
        $query = 'SELECT * FROM snake_body';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить тело питона
    public function getSnakeBody($id) {
        $sql = 'SELECT * FROM snake_body WHERE snake_id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_CLASS);
    }
    // Создать тело питона
    public function createSnakeBody($options) {
        $snake_id = $options['snake_id'];
        $x = $options['x'];
        $y = $options['y'];

        $sql = "INSERT INTO snake_body (snake_id, x, y) VALUES (:snake_id, :x, :y)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':snake_id', $snake_id, PDO::PARAM_INT);
        $stmt->bindParam(':x', $x, PDO::PARAM_INT);
        $stmt->bindParam(':y', $y, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Удалить тело питона
    public function deleteSnakeBody($id) {
        $sql = "DELETE FROM snake_body WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Удалить часть тела питона
    public function deleteSnakeBodyFromSnake($id) {
        $sql = "DELETE FROM snake_body WHERE snake_id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }




    /*Food*/
    // Получить еду
    public function getFoods() {
        $query = 'SELECT * FROM food';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Удалить еду
    public function deleteFood($id) {
        $sql = "DELETE FROM food WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }



    /*System*/
    public function getSystem() {
        $query = 'SELECT * FROM system';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить систему по имени
    public function getSystemByName($name) {
        $sql = 'SELECT * FROM system WHERE name = :name';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':name', $name, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Создать систему
    public function createSystem($name, $value) {
        $stmt = $this->conn->prepare("INSERT INTO system (name, value) VALUES (:name, :value)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /*Map*/
    // Получение карты
    public function getMaps() {
        $query = 'SELECT * FROM map';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    // Получить карту по id
    public function getMapById($id) {
        $sql = 'SELECT * FROM map WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }
    // Создать карту
    public function createMap($options) {
        $width = $options['width'];
        $height = $options['height'];

        $sql = "INSERT INTO map (width, height) VALUES (:width, :height)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':width', $width, PDO::PARAM_INT);
        $stmt->bindValue(':height', $height, PDO::PARAM_INT);
        return $stmt->execute();
    }



}