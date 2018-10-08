<?php

error_reporting(-1);

require_once 'game/Game.php';

$options = new stdClass();

$options->map = [
	[0, 0, 0], [0, 0, 0], [0, 0, 0],
];
$options->snakes = [
    (object) array( 'id' => 12, 'name' => 'Vasya', 'body' => 1, 'direction' => 'left' )
];
$options->foods = [
    (object) array( 'x' => 2, 'y' => 0, 'value' => 2 )
];

$game = new Game($options);


$COMMAND = $game->getCommand();

print_r($game->executeCommand($COMMAND->CHANGE_DIRECTION, (object) [ 'id' => 12, 'direction' => 'left']));
