<?php


class DB {

    private $db;

    public function __construct() {
        $host = 'localhost';
        $dbName = 'snake_of_pi';
        $user = 'root';
        $pass = '';

        $this->db = new PDO('mysql:dbname='.$dbName.';host='.$host, $user, $pass);
    }

    public function getUsers() {
        $query = 'SELECT * FROM user ORDER BY id DESC';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function getUserByLogin($login) {
        $query = 'SELECT * FROM user WHERE login="'.$login.'" ORDER BY id DESC LIMIT 1';
        return $this->db->query($query)->fetchObject('stdClass');
    }

    public function getUserByToken($token) {
        $query = 'SELECT * FROM user WHERE token="'.$token.'" ORDER BY id DESC LIMIT 1';
        return $this->db->query($query)->fetchObject('stdClass');
    }

    public function getUser($login, $password) {
        $query = 'SELECT * FROM user WHERE login="'.$login.'" password="'.$password.'"';
        return $this->db->query($query)->fetchObject('stdClass');
    }

    public function createUser($options) {
        $name = $options['name'];
        $login = $options['login'];
        $password = $options['password'];

        $stmt = $this->db->prepare("INSERT INTO user (name, login, password) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $login);
        $stmt->bindParam(3, $password);
        return $stmt->execute();
    }




    public function getSnakes() {
        $query = 'SELECT * FROM snake';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function getSnakesFromUser($user_id) {
        $query = 'SELECT * FROM snake, user WHERE user.id="'.$user_id.'" AND snake.user_id=user.id ORDER BY snake.id DESC';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }

    public function getSnakeById($id) {
        $query = 'SELECT * FROM snake WHERE id="'.$id.'"';
        return $this->db->query($query)->fetchObject('stdClass');
    }

    public function createSnake($options) {
        $user_id = $options['user_id'];
        $direction = $options['direction'];
        $body = $options['body'];

        $stmt = $this->db->prepare("INSERT INTO snake (user_id, direction, body) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $direction);
        $stmt->bindParam(3, $body);
        return $stmt->execute();
    }




    public function getFoods() {
        $query = 'SELECT * FROM food';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }


    public function getSystemByName($name) {
        $query = 'SELECT * FROM system WHERE name="'.$name.'"';
        return $this->db->query($query)->fetchObject('stdClass');
    }

    public function insertToSystem($name, $value) {
        $stmt = $this->db->prepare("INSERT INTO system (name, value) VALUES (?, ?)");
        $stmt->bindParam(1, $name);
        $stmt->bindParam(2, $value);
        return $stmt->execute();
    }
}