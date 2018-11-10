<?php
// Список основных команд для обращения api

const COMMAND = [
    'CHANGE_DIRECTION' => 'changeDirection', // изменить направление змеи
    'CREATE_SNAKE' => 'createSnake', // добавить змею
    'DESTROY_SNAKE' => 'destroySnake', // убрать змею
    'GET_SCENE' => 'getScene', // получить информацию о сцене
    'LOGIN' => 'login', // авторизация
    'REGISTER' => 'register', // регистарция
    'LOGOUT' => 'logout', // выход
    'GET_CURRENT_USER' => 'getCurrentUser', // текущий пользователь
];