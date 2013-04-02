<?php

// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

/**
 * Footer Template
 *
 *
 * @file           footer.php
 * @package        Responsive 
 * @author         Emil Uzelac 
 * @copyright      2003 - 2013 ThemeID
 * @license        license.txt
 * @version        Release: 1.2
 * @filesource     wp-content/themes/responsive/footer.php
 * @link           http://codex.wordpress.org/Theme_Development#Footer_.28footer.php.29
 * @since          available since Release 1.0
 */

/* 
 * Globalize Theme options
 */
global $responsive_options;
$responsive_options = responsive_get_options();
?>
		<?php responsive_wrapper_bottom(); // after wrapper content hook ?>
    </div><!-- end of #wrapper -->
    <?php responsive_wrapper_end(); // after wrapper hook ?>
</div><!-- end of #container -->
<?php responsive_container_end(); // after container hook ?>



<div id="footer" class="clearfix">
	<?php responsive_footer_top(); ?>

    <div id="footer-wrapper">
    
     
  
        <div class="grid col-940">
 <?php // AD2013 ?>       
        <!-- <div class="grid col-220">Col 1</div>
        <div class="grid col-220">Col 2</div>
        <div class="grid col-220">Col 3</div>
        <div class="grid col-220 fit">Col 4</div> -->
<?php // End AD2013 ?>          
         
         
         </div><!-- end of col-940 -->
         <?php get_sidebar('colophon'); ?>
                
        <div class="grid col-300 copyright">
            <?php esc_attr_e('&copy;', 'responsive'); ?> <?php _e(date('Y')); ?><a href="<?php echo home_url('/') ?>" title="<?php // echo esc_attr(get_bloginfo('name', 'display')); ?>Aktiv Demokrati">
                <?php // bloginfo('name'); ?>Aktiv Demokrati
            </a>
        </div><!-- end of .copyright -->
        
        <div class="grid col-300 scroll-top"><a href="#scroll-top" title="<?php esc_attr_e( 'scroll to top', 'responsive' ); ?>"><?php _e( '&uarr;', 'responsive' ); ?></a></div>
<?php // AD2013 Tog bort Powered by ?>
        <div class="grid col-300 fit">
            
        </div><!-- end -->
<?php // End AD2013 ?>
        
    </div><!-- end #footer-wrapper -->
    
	<?php responsive_footer_bottom(); ?>
</div><!-- end #footer -->
<?php responsive_footer_after(); ?>

<?php wp_footer(); ?>
</body>
</html>
