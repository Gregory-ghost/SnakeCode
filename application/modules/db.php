<?php


class DB {

    private $db;

    public function __construct() {
        $host = 'localhost';
        $dbName = 'snake_of_pi';
        $user = 'root';
        $pass = '';

        $this->db = new PDO('mysql:dbname='.$dbName.';host='.$host, $user, $pass);



        // соединение больше не нужно, закрываем
        $sth = null;
        $dbh = null;
    }

    public function getUsers() {
        $query = 'SELECT * FROM user ORDER BY id DESC';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    public function getUserByLogin($login) {
        $query = 'SELECT * FROM user WHERE login="'.$login.'" ORDER BY id DESC LIMIT 1';
        return $this->db->query($query)->fetchObject('stdClass');
    }
    public function getUserByToken($login) {
        $query = 'SELECT * FROM user WHERE token="'.$login.'" ORDER BY id DESC LIMIT 1';
        return $this->db->query($query)->fetchObject('stdClass');
    }
    public function getUser($login, $password) {
        $query = 'SELECT * FROM user WHERE login="'.$login.'" password="'.$password.'"';
        return $this->db->query($query)->fetchObject('stdClass');
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
    public function getFoods() {
        $query = 'SELECT * FROM food';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
}