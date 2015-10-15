jQuery( function($) {

    // toggle metaboxes
    $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
    postboxes.add_postbox_toggles( ls_PHPVAR_payments.ls_pm_page_hook_id );

    var handle_text_input = function () {

        var $this = $(this);

        shortcode_output( $this.data('shortcode-name'), $this.val() );

    };

    var handle_checkbox = function () {

        var $this = $(this);

        shortcode_output( $this.data('shortcode-name'), $this[0].checked );

    };

    var change_button_label = function () {

        var label = $(this).val();

        // Label is always a value or defaults to Pay Now
        if( label )
            label_value = label;
        else
            label_value = 'PAY NOW';


        $('.js-ls-payment-button').text( label_value );

    };

    var toggle_payment_icons = function () {

        $('.js-ls-payment-button-icons').toggle( $(this)[0].checked );

    };

    // Setup events on form
    $('.js-ls-payment-button-options')
        .on('keyup','[type="text"]', handle_text_input)
        .on('click','[type="checkbox"]', handle_checkbox)
        .on('keyup','.js-ls-button-label', change_button_label)
        .on('click','.js-ls-payment-icons', toggle_payment_icons)

    // Default values for shortcode template, get overwritten on first keyup
    var	template = {
        'label': 'Pay Now',
        'show-icons': true,
        'payment-amount': '',
        'title': '',
        'class': ''
    };

    // Process user input and set shortcode copy paste output
    var shortcode_output = function( attribute, value ){
        var $shortcode_output = $('#ls-shortcode-output'),
            label_value;

        // Overwrite default values for shortcode template
        template[ attribute ] = value;

        // Label is always a value or defaults to Pay Now
        if( template['label'] )
            label_value = template['label'];
        else
            label_value = 'Pay Now';

        // Concatanate string and set as shortcode copy paste text
        $shortcode_output.text([
            '[livesite-pay',
            ' label="'+ label_value +'"' ,
            template['payment-amount'] ? ' payment_amount="' + template['payment-amount'] + '"' : '',
            template['title'] ? ' title="' + template['title'] + '"' : '',
            template['class'] ? ' class="' + template.class + '"' : '',
            template['show-icons'] ? ' show_icons' : '',
            ']'
        ].join('') );

    }

});
