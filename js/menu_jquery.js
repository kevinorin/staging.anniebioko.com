var jk =jQuery.noConflict();
jk( document ).ready(function() {
jk('#cssmenu').prepend('<div id="indicatorContainer"><div id="pIndicator"><div id="cIndicator"></div></div></div>');
    var activeElement = jk('#cssmenu>ul>li:first');

    jk('#cssmenu>ul>li').each(function() {
        if (jk(this).hasClass('active')) {
            activeElement = jk(this);
        }
    });


	var posLeft = activeElement.position().left;
	var elementWidth = activeElement.width();
	posLeft = posLeft + elementWidth/2 -6;
	if (activeElement.hasClass('has-sub')) {
		posLeft -= 6;
	}

	jk('#cssmenu #pIndicator').css('left', posLeft);
	var element, leftPos, indicator = jk('#cssmenu pIndicator');
	
	jk("#cssmenu>ul>li").click(function() {
        element = jk(this);
        var w = element.width();
        if (jk(this).hasClass('has-sub'))
        {
        	leftPos = element.position().left + w/2 - 12;
        }
        else {
        	leftPos = element.position().left + w/2 - 6;
        }

        jk('#cssmenu #pIndicator').css('left', leftPos);
    }
    , function() {
    	jk('#cssmenu #pIndicator').css('left', posLeft);
    });


	jk('#cssmenu>ul>.has-sub>ul').append('<div class="submenuArrow"></div>');
	jk('#cssmenu>ul').children('.has-sub').each(function() {
		var posLeftArrow = jk(this).width();
		posLeftArrow /= 2;
		posLeftArrow -= 12;
		jk(this).find('.submenuArrow').css('left', posLeftArrow);

	});

	jk('#cssmenu>ul').prepend('<li id="menu-button"><a>Menu</a></li>');
	jk( "#menu-button" ).click(function(){
    		if (jk(this).parent().hasClass('open')) {
    			jk(this).parent().removeClass('open');
    		}
    		else {
    			jk(this).parent().addClass('open');
    		}
    	});
});