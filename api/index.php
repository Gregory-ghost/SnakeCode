<?php

error_reporting(-1);

require_once '../application/router/Router.php';

$router = new Router();

echo json_encode($router->answer((object) $_GET));
