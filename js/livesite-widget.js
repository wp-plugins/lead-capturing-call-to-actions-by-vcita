jQuery( function($) {

    var updating = false;

    var update_db_field = function ( e, active ) {

        var $this = $(this),
            show_livesite = active ? '1' : '0';

        // Make sure this box isn't checked
		if ( ! updating ) {

            updating = true;

			// Send data to wordpress ajax
			var params = {
				action: 'update-livesite-status',
				nonce: ls_PHPVAR_livesite_widget.ls_lw_module_nonce,
				show_livesite: show_livesite
			};

			var init_module_xhr = $.post( ls_PHPVAR_livesite.ls_site_url + '/wp-admin/admin-ajax.php', params);

			init_module_xhr.done(function ( data ) {

                // When ajax is returned reset updating flag
                updating = false;

			});

		}

    };


    var toggle_switch = $('.js-ls-toggle-switch').toggles();

    // Setup events on toggle switch
    toggle_switch.on('toggle', update_db_field);

});
