<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;


define('ADHOMESYS', get_template_directory());
define('ADHOMEURL', get_template_directory_uri());




/**
 *
 * WARNING: Please do not edit this file in any way
 *
 * load the theme function files
 */
 
require ( ADHOMESYS . '/includes/functions.php' );
require ( ADHOMESYS . '/includes/theme-options.php' );
require ( ADHOMESYS . '/includes/post-custom-meta.php' );
require ( ADHOMESYS . '/includes/tha-theme-hooks.php' );
require ( ADHOMESYS . '/includes/hooks.php' );
require ( ADHOMESYS . '/includes/version.php' );


// AD2013
require ( ADHOMESYS . '/includes/ad-functions.php' );
require ( ADHOMESYS . '/includes/sections.php' );
