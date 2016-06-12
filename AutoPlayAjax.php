<?php

require_once 'MemoryGame.php';
require_once 'AutoPlayer.php';

session_start();

$autoPlayer = $_SESSION['auto_player'];

echo $autoPlayer->getNextMove();
