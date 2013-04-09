<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

get_header(); ?>


<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">
        


        
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>
        
        <?php get_template_part( 'loop-header' ); ?>
        
			<?php responsive_entry_before(); ?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>       
				<?php responsive_entry_top(); ?>
                <?php get_template_part( 'post-meta-page' ); ?>






  <?php if( !is_user_logged_in() ){ ?>
<div class="bubble" style="float:left">
<a href="http://aktivdemokrati.se/wp-login.php?action=register"><h2>Gå med i forumet!</h2
<p>I vårt forum hittar du tusentals med intressanta texter<br>
och det är givetvis kostnadsfritt att skapa en<br>
användare. Gillar du det du läser kan du enkelt bli<br>
medlem i partiet också!<br>
</p></div>
   <?php } ?>

<h3 style="margin-bottom:0;clear:left;padding-top:1em">Aktivitet på <a href="/forum/">forumet</a></h3>
<iframe class="iframe_autoresize" width="100%" height="600" src="http://aktivdemokrati.se/forum/search.php?search_id=active_topics&ad_inside_wp=1"></iframe>







                
                 <?php if ( has_post_thumbnail( $post->ID ) )
                 echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' ); ?>

                <div class="post-entry">
                    <?php the_content(__('Read more &#8250;', 'responsive')); ?>
                    <?php wp_link_pages(array('before' => '<div class="pagination">' . __('Pages:', 'responsive'), 'after' => '</div>')); ?>
                </div><!-- end of .post-entry -->
            
				<?php get_template_part( 'post-data' ); ?>
				               
				<?php responsive_entry_bottom(); ?>      
			</div><!-- end of #post-<?php the_ID(); ?> -->       
			<?php responsive_entry_after(); ?>
            
			<?php responsive_comments_before(); ?>
			<?php comments_template( '', true ); ?>
			<?php responsive_comments_after(); ?>
            
        <?php 
		endwhile; 

		get_template_part( 'loop-nav' ); 

	else : 

		get_template_part( 'loop-no-posts' ); 

	endif; 
	?>  
      
</div><!-- end of #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
