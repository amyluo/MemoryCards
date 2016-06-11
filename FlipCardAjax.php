<?php

require_once 'MemoryGame.php';
require_once 'AutoPlayer.php';

session_start();

//get 2 cards position for flip & compare
$pos1 = $_GET["card1"];
$pos2 = $_GET["card2"];

$game = $_SESSION['memory_game'];
$autoPlayer = $_SESSION['auto_player'];
$val = $game->flipCards($pos1, $pos2);

/**
 * return as $val
 *
 * check $val if game finished, reset session info to restart a new game
 */
if ($val == 0) {
    $autoPlayer->rememberUnmatchedCards($pos1, $pos2);
} elseif ($val == 2) {
    session_unset();
}

echo $val;
