<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Memory Cards</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/jquery-1.10.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
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
        function formatTime(i){
            if (i<10){
                i="0" + i;
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
            timerTimeout = window.setInterval(function() {
                var now = new Date();
                var timeSpent = Math.floor((now.getTime() - startTime.getTime())/1000);
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
        function flipCard(pos){
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
        function onClickCard(pos) {
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
            }else {
                flipCard(pos);
                notAllowFlip = true;
                $.ajax({
                    url: "FlipCardAjax.php?"+"card1="+firstFlippedCard+"&card2="+pos,
                    /**
                     * determine the compare result for 2 flipped cards
                     * @param Integer data three status send from backend 0 - cards not match, flip them to face down
                     *                                                      1 - cards match, keep face up
                     *                                                      2 - cards match, all cards face up, game over
                     *                                                      
                     * @returns {undefined}
                     */
                    success: function(data){
                        setTimeout(function(){
                            switch (data) {
                            case '0':
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
                            };
                            notAllowFlip = false;
                        }, 500);
                    },
                    /**
                     * lost connection to server, flip cards to face down and allow to start again
                     * 
                     * @returns {undefined}
                     */
                    error: function(){
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
                 async:false,
                 success: function(data) {
                     firstFlippedCard = null;
                     notAllowFlip = false;
                     resetTimer();
                     $('div #cardgrid').html(data);
                     
                     $("div #cardgrid .cardimg").each(function(index){
                        $(this).bind("click", function(){
                            onClickCard(index);
                        });
                    });
                 }   
            });
        }
        
        /**
         * bind 2 ajax functions
         * 
         * - reset button
         * - game complete to show a congratulation popup window
         */        
        $(document).ready(function(){
            resetGame();
            
            // bind reset button
            $("button.resetGame").bind('click', function() {
                resetGame();
            });
            
            $('#gameCompleted').on('hidden.bs.modal', function (e) {
                resetGame();
            });
        });
    </script>
    
</head>
<body>
    <div class="container" style="padding: 0 160px;">
        <div class="row col-lg-12">
            <span style="font-size: 40px;">Memory Games</span>
            <button type="button" class="btn btn-danger col-md-offset-2 resetGame">Reset</button>
            <button type="button" class="btn btn-warning col-md-offset-3 timeSpent">00:00:00</button>
        </div> <!-- End Game control button -->
        <hr/>
        <div id="cardgrid" class="row"> <!-- div for ajax show cardgrid-->
    
        </div> <!-- End Cardgrid -->
    </div> <!-- End Container -->
    
  <!-- complete game dialog -->
  <div class="modal fade" id="gameCompleted" tabindex="-1" role="dialog" aria-labelledby="gameCompletedLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title" id="gameCompletedLabel">Congratulations!</h4>
            </div>
            <div class="modal-body">
              Congratulations, you have just completed the challenge.
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
    </div>
</body>
</html>
