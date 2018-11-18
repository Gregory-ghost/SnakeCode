<?php

class User {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function login($login, $password) {
        if ($login && $password) {
            $user = $this->db->getUser($login, $password);
            if ($user) {
                $token = md5($user->login . rand(0, 100000));
                if ($this->db->updateUserToken($user->id, $token)) {
                    return (object) [ 'name' => $user->name, 'token' => $token ];
                }
            }
        }
        return false;
    }

    public function logout($token) {
        if ($token) {
            $user = $this->db->getUserByToken($token);
            if ($user) {
                return $this->db->updateUserToken($user->id, '');
            }
        }
        return false;
    }

    public function checkToken($token) {
        if ($token) {
            $user = $this->db->getUserByToken($token);
            if ($user) {
                return $user->id;
            }
        }
        return false;
    }
}