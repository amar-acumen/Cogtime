$(document).ready(function () {
    // set the time for the beeper to be displayed as 5000 milli seconds (5 seconds)
    var timerId, delay = 5000;
    var a = $("#BeeperBox"),
        b = $("a.control");;
    //function to destroy the timeout


    function stopHide() {
        clearTimeout(timerId);
    }
    //function to display the beeper and hide it after 5 seconds


    function showTip() {
        a.show();
        timerId = setTimeout(function () {
            a.hide();
        }, delay);

    }
    //function to hide the beeper after five seconds


    function startHide() {
        timerId = setTimeout(function () {
            a.hide();
        }, delay);
    }
    //display the beeper on cliking the "show beeper" button
    b.click(showTip);
    //Clear timeout to hide beeper on mouseover
    //start timeout to hide beeper on mouseout
    a.mouseenter(stopHide).mouseleave(startHide);

    $('.beeper_x').click(function () {
        //hide the beeper when the close button on the beeper is clicked
        $("#BeeperBox").hide();
    });
	
	

});