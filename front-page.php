<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 * Site Front Page
 *
 * Note: You can overwrite front-page.php as well as any other Template in Child Theme.
 * Create the same file (name) include in /responsive-child-theme/ and you're all set to go!
 * @see            http://codex.wordpress.org/Child_Themes and
 *                 http://themeid.com/forum/topic/505/child-theme-example/
 *
 * @file           front-page.php
 * @package        Responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2013 ThemeID
 * @license        license.txt
 * @version        Release: 1.0
 * @filesource     wp-content/themes/responsive/front-page.php
 * @link           http://codex.wordpress.org/Template_Hierarchy
 * @since          available since Release 1.0
 */

/**
 * Globalize Theme Options
 */
global $responsive_options;
$responsive_options = responsive_get_options();

/**
 * If front page is set to display the
 * blog posts index, include home.php;
 * otherwise, display static front page
 * content
 */
if ( 'posts' == get_option( 'show_on_front' ) ) {
	get_template_part( 'home' );
} else if ( 'default' != get_post_meta( get_option( 'page_on_front' ), '_wp_page_template', true )
and locate_template( get_post_meta( get_option( 'page_on_front' ), '_wp_page_template', true ), true ) ) {
  //  locate_template( get_post_meta( get_option( 'page_on_front' ), '_wp_page_template', true ), true );
} else { 
	get_header(); 
	?>

	<div id="featured" class="grid col-940" style="padding-bottom: 0;">
    	<a href="/senaste/partiprogram/"><div class="featured-image-front"><img src="/wp-content/themes/ad-3/images-ad/frontpage/valj-riksdagsplats.gif" /></div></a>
        <div class="featured-copy">
            <h3>Demokrati mellan valen</h3>
              <p> Aktiv Demokratis grundidé är att ge ansvaret och makten över det svenska samhället till medborgarna. Med dagens teknik blir möjligheterna för alla att påverka politiken allt större, och det är något Aktiv demokrati vill ge utrymme för att hända.</p>
              <p>Många tycker att det är svårt att välja vilket parti de ska rösta på när det är val.</p>
              <p>Vår vardag är oftast mer komplex än ett partiprogram, och även om vi håller med om en del av ett partis ståndpunkter så håller vi sällan med om allt.</p>
                
         </div>
        <div class="clear"></div> 
	
	</div><!-- end of #featured -->
               
	<?php 
	get_sidebar('home');
	get_footer(); 
}
?>
<?php

/*
<div class="grid col-460">

			<h1 class="featured-title"><?php echo $responsive_options['home_headline']; ?></h1>
			
			<h2 class="featured-subtitle"><?php echo $responsive_options['home_subheadline']; ?></h2>
			
			<p><?php echo $responsive_options['home_content_area']; ?></p>
			
			<?php if ($responsive_options['cta_button'] == 0): ?>  
   
				<div class="call-to-action">

					<a href="<?php echo $responsive_options['cta_url']; ?>" class="blue button">
						<?php echo $responsive_options['cta_text']; ?>
					</a>
				
				</div><!-- end of .call-to-action -->

			<?php endif; ?>         
			
		</div><!-- end of .col-460 -->

		<div id="featured-image" class="grid col-460 fit"> 
							
			<?php echo do_shortcode( $responsive_options['featured_content'] ); ?>
									
		</div><!-- end of #featured-image -->
*/ ?>
