$(function() {
	$(document).foundation();
    console.log( "ready!" );
    $('.burger').click(function() {
    	$('.navigation').addClass('active');
    });
    $('.navigation .close-button').click(function() {
    	$('.navigation').removeClass('active');
    });
    $("a#go-to-form").on('click', function() {
        $.smoothScroll({
            offset:-194,
            speed: 800,
            easing: 'swing',
            scrollTarget: '#form-section',
            afterScroll: function () {
                fbq('track', 'go-to-form');
            }
        });
        return false;
    });
});
