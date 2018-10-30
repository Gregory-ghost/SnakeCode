<?php


class DB {

    public $conn;

    public function __construct() {
        $host = 'localhost';
        $dbName = 'snake_of_pi';
        $user = 'root';
        $pass = '';

        $this->conn = new PDO('mysql:dbname='.$dbName.';host='.$host, $user, $pass);
    }

    /*User*/
    public function getUsers() {
        $query = 'SELECT * FROM user ORDER BY id DESC';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function getUserByLogin($login) {
        $sql = 'SELECT * FROM user WHERE login = :login ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':login', $login, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }

    public function getUserByToken($token) {
        $sql = 'SELECT * FROM user WHERE token = :token ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':token', $token, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }

    public function getUser($login, $password) {
        $sql = 'SELECT * FROM user WHERE login = :login and password = :password ORDER BY id DESC LIMIT 1';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':login', $login, PDO::PARAM_STR);
        $stm->bindValue(':password', $password, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }

    public function createUser($options) {
        $name = $options['name'];
        $login = $options['login'];
        $password = $options['password'];

        $sql = "INSERT INTO user (name, login, password) VALUES (:name, :login, :password)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':login', $login, PDO::PARAM_STR);
        $stmt->bindValue(':password', $password, PDO::PARAM_STR);
        return $stmt->execute();
    }



    /*Snake*/
    public function getSnakes() {
        $query = 'SELECT * FROM snake';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function getUserSnakes($user_id) {
        $sql = 'SELECT * FROM snake, user WHERE user.id = :user_id AND snake.user_id=user.id ORDER BY snake.id DESC';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_CLASS);
    }

    public function getSnakeById($id) {
        $sql = 'SELECT * FROM snake WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }

    public function createSnake($options) {
        $user_id = $options['user_id'];
        $direction = $options['direction'];

        $sql = "INSERT INTO snake (user_id, direction, body) VALUES (:user_id, :direction, :body)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':direction', $direction, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteSnake($id) {
        $sql = "DELETE FROM snake WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteUserSnakes($user_id) {
        $sql = "DELETE FROM snake WHERE user_id =  :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /*Snake_body*/
    public function getSnakeBody($id) {
        $sql = 'SELECT * FROM snake_body WHERE snake_id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchAll(PDO::FETCH_CLASS);
    }

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

    public function deleteSnakeBody($id) {
        $sql = "DELETE FROM snake_body WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteSnakeBodyFromSnake($id) {
        $sql = "DELETE FROM snake_body WHERE snake_id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }




    /*Food*/
    public function getFoods() {
        $query = 'SELECT * FROM food';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function deleteFood($id) {
        $sql = "DELETE FROM food WHERE id =  :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }



    /*System*/
    public function getSystemByName($name) {
        $sql = 'SELECT * FROM system WHERE name = :name';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':name', $name, PDO::PARAM_STR);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }

    public function createSystem($name, $value) {
        $stmt = $this->conn->prepare("INSERT INTO system (name, value) VALUES (:name, :value)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':value', $value, PDO::PARAM_STR);
        return $stmt->execute();
    }

    /*Map*/

    public function getMaps() {
        $query = 'SELECT * FROM map';
        return $this->conn->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function getMapById($id) {
        $sql = 'SELECT * FROM map WHERE id = :id';
        $stm = $this->conn->prepare($sql);
        $stm->bindValue(':id', $id, PDO::PARAM_INT);
        $stm->execute();
        return $stm->fetchObject('stdClass');
    }

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