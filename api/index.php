<?php

error_reporting(-1);

require_once '../application/router/Router.php';

$router = new Router();

header('Content-type: application/json');
echo json_encode($router->answer((object) $_GET));
