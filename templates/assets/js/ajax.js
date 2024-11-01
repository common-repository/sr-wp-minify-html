function sr_toggle_html_minify(template) {
    var data = {
            action: sr_minify_html_obj.action,
            template: template
    };
    jQuery.post(sr_minify_html_obj.ajax_url, data, function(response) {
            alert('html minification '+ response+'!');
            window.location.reload();
    });
}