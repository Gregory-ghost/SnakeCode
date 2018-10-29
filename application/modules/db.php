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
        $query = 'SELECT * FROM user';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    public function getUser($login) {
        $field = $this->getField($login);
        $query = 'SELECT * FROM user WHERE login="'.$login.'"';
        return $this->db->query($query)->fetchObject('stdClass');
    }
    public function getSnakes() {
        $query = 'SELECT * FROM snake';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    public function getFoods() {
        $query = 'SELECT * FROM food';
        return $this->db->query($query)->fetchAll(PDO::FETCH_CLASS);
    }
    private function getField($field) {
        return "`".str_replace("`","``",$field)."`";
    }
}