jQuery(document).ready(function () {
    jQuery('.url_redirect_rewrite_delete_form').submit(function(e) {
        if ((!confirm('Are you sure you want to delete the field?'))) {
            return false;
        }
    });
    
    jQuery('#url_redirect_rewrite_name').keyup(function() {
        var val = jQuery('#url_redirect__rewritename').val();
        jQuery('#url_redirect_rewrite_name').val( val.replace(" ","-") );

    });

    
    jQuery('.url_redirect_rewrite_reset_form').submit(function(e) {
        if ((!confirm('Are you sure you want to reset the counter?'))) {
            return false;
        }
    });

    jQuery('#url_redirect_rewrite_name').change(function () {

        jQuery.each(aName, function (key, value) {
            if (value.name == jQuery('#url_redirect_rewrite_name').val()) {
                jQuery('#url_redirect_rewrite_link').val(value.link);
                jQuery('#url_redirect_rewrite_type').val(value.type);

                jQuery('#url_redirect_rewrite_submit').val('Modify');
                jQuery('#url_redirect_rewrite_name').prop('readonly', true);
                jQuery('#url_redirect_rewrite_cancel').show();
            }
        })
    })

    jQuery('.edit').click(function () {
        jQuery(this).parent().parent().css('border', 'solid 1x red');
        name = jQuery(this).attr('data-name');
        link = jQuery(this).attr('data-link');
        type = jQuery(this).attr('data-link2');

        jQuery('#url_redirect_rewrite_name').val(name);
        jQuery('#url_redirect_rewrite_link').val(link);
        jQuery('#url_redirect_rewrite_type').val(type);

        jQuery('#url_redirect_rewrite_submit').val('Modify');
        jQuery('#url_redirect_rewrite_name').prop('readonly', true);
        jQuery('#url_redirect_rewrite_cancel').show();
    });

    jQuery('#url_redirect_rewrite_cancel').click(function () {
        jQuery('#url_redirect_rewrite_name').val('');
        jQuery('#url_redirect_rewrite_link').val('');
        jQuery('#url_redirect_rewrite_type').val('');
        jQuery('#url_redirect_rewrite_submit').val('Save');

        jQuery('#url_redirect_rewrite_name').prop('readonly', false);
        jQuery('#url_redirect_rewrite_cancel').hide();
    });
})