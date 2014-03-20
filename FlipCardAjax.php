<?php

    require_once 'MemoryGame.php';

    session_start();
    
    //get 2 cards position for flip & compare
    $card1 = $_GET["card1"];
    $card2 = $_GET["card2"];
    
    $game = $_SESSION['memory_game'];
    $val = $game->flipCards($card1,$card2);
    
    /**
     * return as $val
     * 
     * check $val if game finished, reset session info to restart a new game
    */
    if ($val == 2) {
      session_unset();
}
  echo $val;
