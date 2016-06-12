/**
 * Created by Amy Luo on 2016-06-11.
 */

//this is a variable to hold first flipped card.
var firstFlippedCard;

//set flipcard flag
var notAllowFlip = false;

//set gameStarted flag for timer
var gameStarted = false;

var timerTimeout;
var startTime;

/**
 * Pad 0 for the number below 10.
 * @param Integer i
 * @returns Integer i Append 0 before min or second for digit <10
 */
function formatTime(i) {
    if (i < 10) {
        i = "0" + i;
    }
    return i;
}

/**
 * start Timer count up and show in timer button
 * @returns String formatedTime change timeSpent to a formatted string: " hh:mm:ss"
 */
function startTimer() {
    gameStarted = true;
    startTime = new Date();
    timerTimeout = window.setInterval(function () {
        var now = new Date();
        var timeSpent = Math.floor((now.getTime() - startTime.getTime()) / 1000);
        var hour = Math.floor(timeSpent / 3600);
        var min = Math.floor((timeSpent - hour * 60) / 60);
        var sec = timeSpent % 60;
        var formatedTime = formatTime(hour) + ":" + formatTime(min) + ":" + formatTime(sec);
        $('.timeSpent').html(formatedTime);
    }, 1000);
}

/**
 * Reset timer
 */
function resetTimer() {
    gameStarted = false;
    window.clearInterval(timerTimeout);
    $('.timeSpent').html("00:00:00");
}

/**
 * flip card at position pos
 * @param Integer pos position of card will be flipped.
 */
function flipCard(pos) {
    var imgEle = $('div #cardgrid .cardimg')[pos];
    var curImg = $(imgEle).attr('src');
    var temp = $(imgEle).attr('data-img-hide');
    $(imgEle).attr('src', temp);
    $(imgEle).attr('data-img-hide', curImg);
}
/**
 * check card FaceUp status
 * @param Integer pos card position of check faceUp status
 *
 * @returns Boolean
 */
function isFaceUp(pos) {
    var imgEle = $('div #cardgrid .cardimg')[pos];
    var curImg = $(imgEle).attr('src');
    if (curImg == 'images/999.png') {
        return false;
    } else {
        return true;
    }
}

/**
 * when card clicked:
 * or mark it isFaceUp flag true, and flip it
 * when 2 cards flip face up, mark notAllowFlip flag true and don't allow flip 3rd card
 * @param Integer pos position of current clicked card
 *
 * @returns {undefined}
 */
function onClickCard(pos, timeout) {
    if (timeout === undefined) {
        timeout = 500;
    }
    console.log(pos);
    if (!gameStarted) {
        startTimer();
    }
    if (isFaceUp(pos)) {
        return;
    }
    if (notAllowFlip) {
        return;
    }
    if (firstFlippedCard == null || firstFlippedCard == undefined) {
        flipCard(pos);
        firstFlippedCard = pos;
    } else {
        flipCard(pos);
        notAllowFlip = true;
        $.ajax({
            url: "FlipCardAjax.php?" + "card1=" + firstFlippedCard + "&card2=" + pos,
            /**
             * determine the compare result for 2 flipped cards
             * @param Integer data three status send from backend 0 - cards not match, flip them to face down
             *                                                      1 - cards match, keep face up
             *                                                      2 - cards match, all cards face up, game over
             *
             * @returns {undefined}
             */
            success: function (data) {
                setTimeout(function () {
                    switch (data) {
                        case '0':
                            //not match, so flip back both
                            flipCard(firstFlippedCard);
                            flipCard(pos);
                            firstFlippedCard = null;
                            break;
                        case '1':
                            firstFlippedCard = null;
                            break;
                        case '2':
                            // stop timer
                            resetTimer();

                            //popup Congrat!
                            $('#gameCompleted').modal('show');
                            break;
                        default:
                    }
                    ;
                    notAllowFlip = false;
                }, timeout);
            },
            /**
             * lost connection to server, flip cards to face down and allow to start again
             *
             * @returns {undefined}
             */
            error: function () {
                //popup error message
                alert("Oops, something wrong!");

                //reset the last pair cards
                flipCard(firstFlippedCard);
                flipCard(pos);
                firstFlippedCard = null;
            }
        });
    }
}

/**
 * Reset game button
 *
 * flip all cards to face down and genarate a new deck for restart a game
 *
 * @returns {undefined}
 */
function resetGame() {
    $.ajax({
        url: "ResetGameAjax.php",
        async: false,
        success: function (data) {
            firstFlippedCard = null;
            notAllowFlip = false;
            resetTimer();
            $('div #cardgrid').html(data);

            $("div #cardgrid .cardimg").each(function (index) {
                $(this).bind("click", function () {
                    onClickCard(index);
                });
            });
        }
    });
    setAutoPlayButtonName("Auto Play");
}

/**
 * bind 2 ajax functions
 *
 * - reset button
 * - game complete to show a congratulation popup window
 */
$(document).ready(function () {
    resetGame();

    // bind reset button
    $("button.resetGame").bind('click', function () {
        resetGame();
    });

    $('#gameCompleted').on('hidden.bs.modal', function (e) {
        resetGame();
    });

    $("button.autoPlay").bind('click', function () {
        autoPlay();
    })
});
