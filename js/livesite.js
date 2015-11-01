jQuery( function($) {

	// Generate connection url for vcita
	var vcita_connect = function ( e ) {

		e.preventDefault();

		var callbackURL = ls_PHPVAR_livesite.ls_admin_url + 'admin.php?page=live-site-parse-vcita-callback',
			connect_email = $('#connect-email').val();

		var new_location = [
			"//www.vcita.com/integrations/wordpress/new",
			"?callback=" + encodeURIComponent(callbackURL),
			"&invite=WP-v-ae",
			"&lang=" + ls_PHPVAR_livesite.ls_locale,
			"&email=" + connect_email
		].join('');

		window.location = new_location;

	};

    $('.js-vcita-connect')
    	.click( vcita_connect );


	// Activates a module via ajax
	var init_module = function ( e ) {

		e.preventDefault();

		// When click is on button or slide container
		var $this = $(this).find('[data-module-name]');

		// If click is directly on button
		if ( $this.length === 0 )
			$this = $(this);

		// Send data to wordpress ajax
		var params = {
			action: 'activate-module',
			nonce: ls_PHPVAR_livesite.ls_module_nonce,
			module_name: $this.data('module-name')
		};

		var init_module_xhr = $.post( ls_PHPVAR_livesite.ls_site_url + '/wp-admin/admin-ajax.php', params);

		init_module_xhr.done(function ( data ) {

			// Reload page after module has been updated
			if ( data.status )
				location.href = ls_PHPVAR_livesite.ls_admin_url + 'admin.php?page=' + data.module_slug;

		});

	}

	// Ask the user if he is sure he wants to disconnect his vcita account
    var confirm_disconnect = function ( e ) {

        return confirm('Are you sure? This will reset all plugin settings!');

    };

	var go_to_page = function ( e ) {

		// Get module cta button
		$element = $(this).find('[data-href], [href]');

		// Get module url from button
		if ( $element.attr('href') )
			url = $element.attr('href');
		// If there is no button
		else
			url = $element.data('href');

		if ( url !== '' && url !== undefined )
			location.href = url;

	};

	var open_popup_window = function ( e ) {

		e.preventDefault();

	    var leftPosition, topPosition,
			width = 800,
			height = 600,
			url = this.href;

	    //Allow for borders.
	    leftPosition = (window.screen.width / 2) - ((width / 2));
	    //Allow for title and status bars.
	    topPosition = (window.screen.height / 2) - ((height / 2));
	    //Open the window.
	    window.open(url, "Window2", "status=no,height=" + height + ",width=" + width + ",resizable=yes,left=" + leftPosition + ",top=" + topPosition + ",screenX=" + leftPosition + ",screenY=" + topPosition + ",toolbar=no,menubar=no,scrollbars=no,location=no,directories=no");
	}

	$(document)
        .on('click', '.js-ls-modules__module-button, .js-ls-modules-slim__rack--disabled', init_module)
        .on('click', '.js-ls-modules__module, .js-ls-modules-slim__rack--active', go_to_page)
        .on('click', '.js-vcita-disconnect', confirm_disconnect)
        .on('click', '[data-open-popup]', open_popup_window);

	// Resize iframe on module pages
	var resize_iframe = function () {

		// For setting the iframe height
		var wprap_height = $('#wpwrap').height();

		// Set iframe height
		$('.js-iframe').height( wprap_height );

	};

	resize_iframe();

});
