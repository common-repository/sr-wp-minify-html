<?php

class SR_MINIFY_HTML {

    private static $active_theme;
    private static $minify;
    private static $theme_template;

    function __construct() {
        SR_MINIFY_HTML::$active_theme = wp_get_theme();
        SR_MINIFY_HTML::$theme_template = SR_MINIFY_HTML::$active_theme->template;
        SR_MINIFY_HTML::$minify = get_option('sr_minify_html_' . SR_MINIFY_HTML::$theme_template);
        add_action('admin_init', array('SR_MINIFY_HTML', 'sr_init'));
        add_action('init', array('SR_MINIFY_HTML', 'sr_output_minify_html'));
        add_action('admin_menu', array('SR_MINIFY_HTML', 'sr_minify_html_option_page'));
    }

    static function sr_init() {
        if (empty(SR_MINIFY_HTML::$minify) && SR_MINIFY_HTML::$minify != 1) {
            add_action('admin_notices', array('SR_MINIFY_HTML', 'sr_minify_html_update_notice'));
        }
        add_action('admin_enqueue_scripts', array('SR_MINIFY_HTML', 'sr_enqueue_scripts'));
        add_action('wp_ajax_sr_minify_html_theme', array('SR_MINIFY_HTML', 'sr_minify_html_theme'));
    }

    static function sr_output_minify_html() {
        if (!empty(SR_MINIFY_HTML::$minify) && SR_MINIFY_HTML::$minify == 1) {
            add_action('get_header', array('SR_MINIFY_HTML', 'sr_start_buffer'), 1);
            add_action('wp_footer', array('SR_MINIFY_HTML', 'sr_clean_ouput_buffer'), 1000000);
        }
    }

    static function sr_minify_html_theme() {
        $template = sanitize_text_field($_POST['template']);
        $minify = get_option('sr_minify_html_' . $template) ? 0 : 1;
        update_option('sr_minify_html_' . $template, $minify);
        echo $minify ? 'enabled' : 'disabled';
        wp_die();
    }

    static function sr_enqueue_scripts($hook) {
        global $sr_update_check;
        if ($hook == 'settings_page_sr-minify-html') {
            wp_register_script('sr-minify-html-ajax-script', plugins_url('/templates/assets/js/ajax.js', __FILE__), array('jquery'));
            wp_register_style('sr-minify-html-style-css', plugins_url('/templates/assets/css/style.css', __FILE__), array(), false, 'all');
            wp_enqueue_script('sr-minify-html-ajax-script');
            wp_enqueue_style('sr-minify-html-style-css');
            wp_localize_script('sr-minify-html-ajax-script', 'sr_minify_html_obj', array('ajax_url' => admin_url('admin-ajax.php'), 'action' => 'sr_minify_html_theme'));
        }
    }

    static function sr_minify_html_option_page() {
        add_options_page(
                'SR MINIFY HTML', 'Sr MINIFY HTML', 'manage_options', 'sr-minify-html', array('SR_MINIFY_HTML', 'sr_minify_html_settings_page')
        );
    }

    static function sr_minify_html_settings_page() {
        require_once 'templates/settings-page';
    }

    static function sr_minify_html_update_notice() {
        echo '<div class="update-nag notice" style="display:block;"><p>' .
        __('HTML minification not enabled for the current active theme. Enable compression: <a href="' . admin_url('options-general.php?page=sr-minify-html') . '">click here</a>', 'sr-minify-html')
        . '</p></div>';
    }

    static function sr_start_buffer() {
        ob_start();
    }

    static function sr_clean_ouput_buffer() {
        $buffer = ob_get_contents();
        ob_end_clean();
        echo SR_MINIFY_HTML::sanitize_output($buffer) . '</body></html>';
        exit;
    }

    static function sanitize_output($buffer) {

        $search = array('#^\s*//.+$#m', '!/\*.*?\*/!s', '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/', '/\s+/', '/(\>)\s*(\<)/m');

        $replace = array('', '', '>', '<', '\\1', '', ' ', '><');

        $buffer = preg_replace($search, $replace, $buffer);

        return $buffer;
    }

}
