<?php
    require_once 'MemoryGame.php';
    
    //Initial game grid
    define("CARD_COLUMN",6);  //columns of the cards
    define("CARD_ROW",4);     //rows of the cards

    session_start();
    //memory_game is a session name to keep game info
    $game = new MemoryGame(CARD_ROW,CARD_COLUMN);
    $_SESSION['memory_game'] = $game;

     $cards = $game->getCards();

     //check each card status, flip card face down.
     foreach ($cards as $card){
        if ($card->isFaceUp()){
                   $imgShow = $card->getImgName().".png";
                   $imgHide = "999.png";
               } else {
                   $imgShow = "999.png";
                   $imgHide = $card->getImgName().".png";
         }

         echo ('<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">');
         echo ('<img class="cardimg" src="images/'.$imgShow.'" data-img-hide="images/'.$imgHide.'" style="display: block; margin-bottom: 5px"/></div>');
      }
