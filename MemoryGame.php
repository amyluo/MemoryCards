<?php

class MemoryGame
{
    protected $cardDeck;
    private $pairedNum = 0; //finished matched pairs
    private $totalPairs;  //total pairs to match

    public function __construct($rows, $cols)
    {
        $this->cardDeck = new Deck($rows, $cols);
        $this->totalPairs = $cols * $rows / 2;
    }

    public function getCards()
    {
        return $this->cardDeck->getCards();
    }

    /**
     *
     * @param integer $pos1 the position of the first card. Position is
     *                      converted from 2-d array to 1-d array.
     * @param integer $pos2
     * @return int          0 - not match, 1 - match, 2 - finish the game.
     */
    public function flipCards($pos1, $pos2)
    {
        if ($this->cardDeck->isMatch($pos1, $pos2)) {
            $this->pairedNum += 1;
            if ($this->pairedNum == $this->totalPairs) {
                return 2;
            } else {
                return 1;
            }
        } else {
            return 0;
        }
    }

} //End class MemoryGame

class Deck
{
    var $cards = array();

    public function __construct($x, $y)
    {
        $numberOfCards = $x * $y;
        $pairs = $numberOfCards / 2;

        for ($i = 0; $i < $pairs; $i++) {
            $number = $i + 1;
            $suit = rand(0, 3);
            // $suit 1 mean diamond, $suit 3 mean heart
            // $suit 0 mean spade, $suit 2 mean club
            $this->cards[] = new card($number, $suit);
            $this->cards[] = new card($number, $suit);
            //get a pair of same suit, same number cards
        }
        $this->shuffleCards();
    }

    /**
     * shuffle cards
     */
    private function shuffleCards()
    {
        shuffle($this->cards);
    }

    /**
     * compare two clicked cards
     *
     * - match, set both card isFaceUp flag true
     * - not match, keep isFaceUp flag false
     *
     * @param Integer pos1 first clicked card position
     * @param Integer pos2 second clicked card position
     *
     * @return Boolean
     */
    function isMatch($pos1, $pos2)
    {
        if ($this->cards[$pos1]->compare($this->cards[$pos2])) {
            $this->cards[$pos1]->flip();
            $this->cards[$pos2]->flip();
            return true;
        } else {
            return false;
        }
    }

    function getCards()
    {
        return $this->cards;
    }
} // End Class Deck

class Card
{
    var $cardName;
    var $cardSuit;
    var $isFaceUp;

    function __construct($number, $suit)
    {
        $this->cardName = $number;
        $this->cardSuit = $suit;
        $this->isFaceUp = false;
    }

    /**
     * compare 2 cards indentical or not
     *
     * same cardName & suit mean match (Identical 2 cards)
     *
     * @param object 2nd card
     *
     * @return Boolean
     */
    public function compare($otherCard)
    {
        if ($this->cardName == $otherCard->getCardName()
            && $this->cardSuit == $otherCard->getCardSuit()
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * generate match card image name
     * $suit 1 mean diamond, $suit 3 mean heart
     * $suit 0 mean spade, $suit 2 mean club
     *
     * 1~9 append 0
     */
    public function getImgName()
    {
        if ($this->cardName < 10) {
            return $this->cardSuit . "0" . $this->cardName;
        } else {
            return $this->cardSuit . $this->cardName;
        }
    }

    public function getCardName()
    {
        return $this->cardName;
    }

    public function getCardSuit()
    {
        return $this->cardSuit;
    }

    /**
     * isFaceUp flag for mark currently card face up or down
     *
     * @return Boolean
     */
    Public function isFaceUp()
    {
        return $this->isFaceUp;
    }

    /**
     * change fliped card isFaceUp flag to true
     */
    public function flip()
    {
        $this->isFaceUp = !$this->isFaceUp;
    }
} //Class Card end
