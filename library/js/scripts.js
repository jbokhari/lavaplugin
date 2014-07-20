function updateViewportDimensions() {
    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth,
        y = w.innerHeight || e.clientHeight || g.clientHeight;
    return {
        width: x,
        height: y
    }
}
var viewport = updateViewportDimensions();


/*
 * Throttle Resize-triggered Events
 * Wrap your actions in this function to throttle the frequency of firing them off, for better performance, esp. on mobile.
 * ( source: http://stackoverflow.com/questions/2854407/javascript-jquery-window-resize-how-to-fire-after-the-resize-is-completed )
 */
var waitForFinalEvent = (function() {
    var timers = {};
    return function(callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout(timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

// how long to wait before deciding the resize has stopped, in ms. Around 50-100 should work ok.
var timeToWaitForLast = 100;


/*
 * Here's an example so you can see how we're using the above function
 *
 * This is commented out so it won't work, but you can copy it and
 * remove the comments.
 *
 *
 *
 * If we want to only do it on a certain page, we can setup checks so we do it
 * as efficient as possible.
 */
/*
 * This once checks to see if you're on the home page based on the body class
 * We can then use that check to perform actions on the home page only
 *
 * When the window is resized, we perform this function
 */
var $bc, $box, $boxTitle;

$(window).resize(function() {
    // if we're on the home page, we wait the set amount (in function above) then fire the function
    if (true) {
        waitForFinalEvent(function() {
            // if we're above or equal to 768 fire this off
            viewport = updateViewportDimensions();
            if (viewport.width >= 768) {
                adjustBoxes("reset");
            } else {
                adjustBoxes();
            }
        }, timeToWaitForLast, "a");
    }
});

function adjustBoxes(override) {
    if (override === "reset") {
        // $boxTitle.find(".inner-title").attr('style', '');
        // $boxTitle.attr('style', '');
        $boxTitle.css({
            height: "249px"
        });
        $bc.css({
            fontSize: "27px"
        });

    } else {
        // var maxFS = 40;
        // var minFS = 12;
        var height = $box.height();
        $boxTitle.height(height);
        $bc.css({
            fontSize: (height / 10) + 2
        });
    }
}



/*
 * Put all your regular jQuery in here.
 */
jQuery(document).ready(function($) {

    $bc = $(".bigcontent"); // cache boxes
    $box = $(".box", $bc); // cache boxes
    $boxTitle = $(".box-title", $bc);
    adjustBoxes();

    $d = $(document);
    $b = $("body,html");

    var pos;
    // background();
    $d.scroll(function() {
        // background()
        // Maybe later if there is time, this can be made smooth
    });

    var $btn = $(".plusbtn", $box);
    var lastSelector;
    $btn.on("click", function(e) {
        e.preventDefault();
        $this = $(this);
        if (lastSelector) {
            $(lastSelector).removeClass("show-back");
        }
        selector = $this.attr("href");
        id = selector.replace("#", "");

        $(selector).addClass("show-back");

        lastSelector = selector;

        console.log(id);
    });

}); /* end of as page load scripts */

function background() {
    pos = ($d.scrollTop() / 5) - 250;
    pos = pos + "px";
    console.log(pos);
    $bc.css({
        backgroundPosition: "0 " + pos
    });
}