<?php
ad_log("header.php start");

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/**
 * Header Template
 *
 *
 * @file           header.php
 * @package        Responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2013 ThemeID
 * @license        license.txt
 * @version        Release: 1.3
 * @filesource     wp-content/themes/responsive/header.php
 * @link           http://codex.wordpress.org/Theme_Development#Document_Head_.28header.php.29
 * @since          available since Release 1.0
 */
?>
<!doctype html>
<!--[if !IE]>      <html class="no-js non-ie" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9 ]>    <html class="no-js ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>

<meta charset="<?php bloginfo('charset'); ?>" />
<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">

<title><?php wp_title('&#124;', true, 'right'); ?></title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<link href="https://plus.google.com/117631766948561738723/" rel="publisher" />
<link rel="icon" type="image/vnd.microsoft.icon" href="http://aktivdemokrati.se/favicon.ico" />
<link rel="icon" type="image/png" href="<?php echo(ADHOMEURL) ?>/images-ad/favicon64.png"/>
<link rel="apple-touch-icon" href="http://aktivdemokrati.se/apple-touch-icon.png">

<?php wp_enqueue_style('responsive-style', get_stylesheet_uri(), false, '1.9.0');?>

<?php wp_head();?>
<script type="text/javascript" src="<?php echo(ADHOMEURL) ?>/js/jquery.ba-bbq.min.js"></script>
<script type="text/javascript">window.ad_wp_logged_in="<?PHP echo is_user_logged_in();?>";</script>
<script type="text/javascript" src="<?php echo(ADHOMEURL) ?>/js/ad.js?v=6"></script>
<script type="text/javascript" src="<?php echo(ADHOMEURL) ?>/js/iframe_resize.js"></script>
<?php 
/* AD2013
** Lade till ad-style.css och länkar till BBPress och BuddyPress CSS där. För att kunna ändra på ett smidigt sätt.
** CSS-en ligger i temamappen och länkar till ad-bbpress.css och ad-buddypress.css*/ ?>
<link rel="stylesheet" href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/ad-style.css'; ?>?v=3.2" type="text/css" media="screen" />
<?php
/* Google Analytics tracking code */
?>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20939045-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
</head>

<body <?php body_class(); ?>>
                 
<?php responsive_container(); // before container hook ?>
<div id="container" class="hfeed">
         
    <?php responsive_header(); // before header hook ?>
    <div id="header">
    
     <div class="top-icon-wrapper-responsive">
    	<!--a href="#"><div class="dagens-rost-responsive">Dagens röst</div></a-->    	
        <a href="/manifesto/"><div class="ad-in-english-responsive">In english</div></a>
        <?php // AD2013 Visa vid 980 resolution
			$user_ID = get_current_user_id();
			if ($user_ID == 0) {
				$login_url = '/medlem/anvandare/'. $user_ID . '/profile/edit/';
       			echo '<a href="'. wp_login_url () .'"><div class="ad-login-link-responsive">Logga in</div></a>';
			} else {
				$logout_url = '/du-har-loggat-ut/';
				echo '<a href="'. wp_logout_url() .'"><div class="ad-logout-link-responsive">Logga ut</div></a>';
			} 
		?>        
    </div>  

		<?php responsive_header_top(); // before header content hook ?>
    
        <?php if (has_nav_menu('top-menu', 'responsive')) { ?>
	        <?php wp_nav_menu(array(
				    'container'       => '',
					'fallback_cb'	  =>  false,
					'menu_class'      => 'top-menu',
					'theme_location'  => 'top-menu')
					); 
				?>
        <?php } ?>
        
    <?php responsive_in_header(); // header hook ?>
   
	<?php if ( get_header_image() != '' ) : ?>
               
        <div id="logo">
            <a href="<?php echo home_url('/'); ?>"><img src="<?php header_image(); ?>" width="<?php if(function_exists('get_custom_header')) { echo get_custom_header() -> width;} else { echo HEADER_IMAGE_WIDTH;} ?>" height="<?php if(function_exists('get_custom_header')) { echo get_custom_header() -> height;} else { echo HEADER_IMAGE_HEIGHT;} ?>" alt="<?php bloginfo('name'); ?>" /></a>
        </div><!-- end of #logo -->
        
    <?php endif; // header image was removed ?>

    <?php if ( !get_header_image() ) : ?>
                
        <div id="logo">
            <span class="site-name"><a href="<?php echo home_url('/'); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home"><?php bloginfo('name'); ?></a></span>
            <span class="site-description"><?php bloginfo('description'); ?></span>
        </div><!-- end of #logo -->  

    <?php endif; // header image was removed (again) ?>
    
    <?php $logout_url = '/du-har-loggat-ut/'; ?>
    
    <div class="ad-login-in-english hide-980">
        <a href="/manifesto/"><div class="ad-in-english"></div></a>
        <?php 
			$user_ID = get_current_user_id();
			if ($user_ID == 0) {
       			echo '<a href="'. wp_login_url () .'"><div class="ad-login-link"></div></a>';
			} else {
				$logout_url = '/du-har-loggat-ut/';
				echo '<a href="'. wp_logout_url() .'"><div class="ad-logout-link"></div></a>';
			} ?>
    </div>        
        <!-- a href="#"><div class="dagens-rost hide-980"></div></a -->
        
     
        
    
    <?php get_sidebar('top'); ?>
			    
				<?php wp_nav_menu(array(
				    'container'       => '',
					'theme_location'  => 'header-menu')
					); 
				?>
            <div id="search-in-menu" class="widget_search">	<form method="get" id="searchform" action="http://aktivdemokrati.se/">
                <input type="text" class="field menu-search-field" name="s" id="s" placeholder="Sök här &hellip;" />
                <input type="submit" class="submit menu-submit" name="submit" id="searchsubmit" value="Sök"  />
            </form>
            </div>
                
            <?php if (has_nav_menu('sub-header-menu', 'responsive')) { ?>
	            <?php wp_nav_menu(array(
				    'container'       => '',
					'menu_class'      => 'sub-header-menu',
					'theme_location'  => 'sub-header-menu')
					); 
				?>
            <?php } ?>

			<?php responsive_header_bottom(); // after header content hook ?>
 
    </div><!-- end of #header -->
    <?php responsive_header_end(); // after header container hook ?>
    
	<?php responsive_wrapper(); // before wrapper container hook ?>
    <div id="wrapper" class="clearfix">
		<?php responsive_wrapper_top(); // before wrapper content hook ?>
		<?php responsive_in_wrapper(); // wrapper hook ?>
<?php ad_log("header.php end"); ?>
