jQuery(function($){

    // Modify existing custom page
	var modify_page = function ( action ) {

        // Send data to wordpress ajax
		var params = {
			action: action,
			nonce: ls_PHPVAR_custom_page.ls_pm_module_nonce,
			page_id: ls_PHPVAR_custom_page.ls_pm_page_id,
			page_title: ls_PHPVAR_custom_page.ls_pm_page_title,
			page_content: ls_PHPVAR_custom_page.ls_pm_page_content,
      module_name: ls_PHPVAR_custom_page.ls_pm_module_name
		};

		var init_module_xhr = $.post( ls_PHPVAR_livesite.ls_site_url + '/wp-admin/admin-ajax.php', params);

		init_module_xhr.done(function ( data ) {

			// Reload page after module has been updated
			if ( data )
				location.reload(false);

		});

    }

	var create_page = function ( e ) {

        e.preventDefault();

        modify_page( 'create-module-page' )

    };

    var remove_page = function ( e ) {

        e.preventDefault();

        if ( confirm('Are you sure?\nAll content will be permanantely deleted!') )
            modify_page( 'remove-module-page' );

    };

    $('#create-custom-page').click( create_page );
    $('#remove-custom-page').click( remove_page );

});
