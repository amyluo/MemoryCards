/**
 * Created by Amy luo on 2016-06-11.
 */

function setAutoPlayButtonName(name) {
    $('.autoPlay').text(name);
}

/**
 * Ask Smart Player to recommend next move
 */
function recommendNextMove() {
    var nextPos = -1;

    $.ajax({
        url: "AutoPlayAjax.php",
        async: false,
        success: function (data) {
            nextPos = data;
        }
    });
    return nextPos;
}

function autoPlay() {
    setAutoPlayButtonName('Next Move');
    //unbind onclick on cards to prevent user manual flip cards
    $('div #cardgrid .cardimg').each(function (index) {
        $(this).unbind('click');
    });
    var pos = recommendNextMove();
    onClickCard(pos, 200);

}