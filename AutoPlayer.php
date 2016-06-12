<?php

class AutoPlayer
{
    protected $cards;

    //deck position (key) => card (value)
    private $cardsRemembered = [];

    //deck position(key) => card (value)
    private $suggested = [];

    private $nextNewPosition = 0;

    public function __construct(&$cards)
    {
        $this->cards = $cards;
    }

    public function getNextMove()
    {
        if (!empty($this->suggested)) {
            reset($this->suggested);
            $pos = key($this->suggested);
            unset($this->suggested[$pos]);
        } else {
            $pos = $this->nextNewPosition++;
        }

        return $pos;
    }

    public function rememberUnmatchedCards($pos1, $pos2)
    {
        $this->createSuggestion($pos1);
        $this->createSuggestion($pos2);
    }

    private function createSuggestion($pos1)
    {
        $card = $this->cards[$pos1];

        //to indicate if we can find a match card from remembered cards
        $flag = false;
        foreach ($this->cardsRemembered as $key => $value) {
            if ($this->compareCards($value, $card)) {
                $this->suggested[$key] = $value;
                $this->suggested[$pos1] = &$card;
                unset($this->cardsRemembered[$key]);
                $flag = true;
                break;
            }
        }

        //no matched card found from rememebered cards, then add this new card into remembered.
        if (!$flag) {
            $this->cardsRemembered[$pos1] = $card;
        }
    }

    /**
     * @param Card $card1
     * @param Card $card2
     * @return bool
     */
    private function compareCards($card1, $card2)
    {

        if ($card1->getCardName() == $card2->getCardName()
            && $card1->getCardSuit() == $card2->getCardSuit()
        ) {
            return true;
        } else {
            return false;
        }
    }

} //End class AutoPlayer

