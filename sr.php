<?php
/*
Plugin Name: SR MINIFY HTML
Plugin URI: http://superrishi.com/plugin/sr-minify-html/
Description: SR Minify HTML helps you reduce your front end page size by removing unwanted newlines, extra spaces, html, css and javascript comments. It basically minifies the rendered html and delivers only one line or effective html code.
Version: 2.1
Author: superrishi
Author URI: http://superrishi.com/
License: GPL2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt

Tags: minify html, compress html, wp minify html, wp compress html, html minify, html compress

Text Domain: sr-minify-html

This plugin is licensed under Proprietary license.
*/
if ( ! defined( 'ABSPATH' ) ) exit;
require_once 'const.php';
require_once( SR_MINIFY_HTML_PLUGIN_DIR . 'class.php' );
$SR_MINIFY_HTML = new SR_MINIFY_HTML();