jQuery(document).ready(function($){


	// Current Screen Size
	var total_width = 0;
	$(window).on('resize',function() {

		$('.device.current-screen .screen').width( $(window).width() ).height( $(window).height() );
		$('#current-size').text('('+ $(window).width() +' x '+ $(window).height() +')');

		// Container Size
		$('.device').each(function() {
			total_width = $(this).outerWidth(true) + total_width;
		});

	}).resize();

	$('#container').width(total_width);



	// FULL HEIGHT FRAMES
	$('.full-height iframe').on('load', function(){

		// Iframe content
		var iframe = $(this);
	    var iframeContent = iframe.contents();

	    var pageHeight = iframeContent.find('html').outerHeight();

	    iframe.height(pageHeight);

	});






	// TOGGLE OPTION BOX
	$('#optioner i').click(function(e) {

		var button = $(this).parent();

		if ( button.hasClass('inactive') ) {

			button.addClass('active').removeClass('inactive');

		} else {

			button.addClass('inactive').removeClass('active');

		}

		e.preventDefault();
		return false;

	});


	// LOCK THE SCREEN
	$('#stopper').click(function() {

		if ( $(this).hasClass('inactive') ) {
			$('body').css('overflow', 'hidden');
			$(this).addClass('active').removeClass('inactive');

		} else if ( $(this).hasClass('active') ) {

			$('body').css('overflow', 'visible');
			$(this).addClass('inactive').removeClass('active');

		}


	});


	// RELOAD ALL THE FRAMES
	$('#reloader').click(function() {

		var button = $(this);

		button.addClass('active').removeClass('inactive');
		$('.ind-reload').addClass('active').removeClass('inactive');

		$('iframe').each(function() {
			this.contentWindow.location.reload(true); //$('iframe').attr('src', $('iframe').attr('src'));
		});

		$('iframe').load(function(){
			button.addClass('inactive').removeClass('active');
			$('.ind-reload').addClass('inactive').removeClass('active');
		});

	});


	// RELOAD A DEVICE
	$('.ind-reload').click(function() {

		var button = $(this);
		var iframee = $(this).parent().parent().find('iframe');

		button.addClass('active').removeClass('inactive');

		iframee.each(function() {
			this.contentWindow.location.reload(true); //$('iframe').attr('src', $('iframe').attr('src'));
		});

		iframee.load(function(){
			button.addClass('inactive').removeClass('active');
		});

	});


	// FOCUS A DEVICE
	$('.ind-focus').click(function() {

		var button = $(this);
		var iframee = $(this).parent().parent().find('iframe');
		var windowSize;

		button.addClass('active').removeClass('inactive');

		$(window).resize(function() {
			windowSize = $(window).width();
		}).resize();

		$('html,body').animate({
				scrollTop: iframee.offset().top,
				scrollLeft: iframee.offset().left + ((iframee.width()-windowSize)/2)
			}, 'slow', function() {
				button.addClass('inactive').removeClass('active');
			}
		);

	});














	// OPTIONS WORK
	$(window).on('resize',function() {
		$('.current-size').text('('+ $(window).width() +' x '+ $(window).height() +')');
	}).resize();

	$('#device-custom').on('change', function() {

		if (this.checked) {
			$('#device-custom-width, #device-custom-height').prop( "disabled", false );
		} else {
			$('#device-custom-width, #device-custom-height').prop( "disabled", true );
		}

	});

	$('#device-custom-width, #device-custom-height').on('input', function() {
		$('#device-custom').prop( "checked", true );
	});

	$('#devices-list input[type="checkbox"]').on('change', function() {
		if ( $('#devices-list input[type="checkbox"]:checked').length ) {
			$('#resp-submit').prop( "disabled", false );
		} else {
			$('#resp-submit').prop( "disabled", true );
		}
	});

	$('#optioner-form').on('submit', function(){
		if ( $('#device-custom-width, #device-custom-height').val() == "" ) {

			$('#device-custom').prop( "checked", false );
			$('#device-custom-width, #device-custom-height').prop( "disabled", true );

		}
	});


	$('#resp_full_height_mode').on('change', function() {

		if ( $(this).prop("checked") ) $('#resp_show_devices').prop('checked', false).prop('disabled', true);
		else $('#resp_show_devices').prop('disabled', false);

	});


}); // document ready