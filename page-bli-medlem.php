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

<div class="do123">
<?php if( !is_user_logged_in() ){ ?>
<p><span>1.</span> <a href="/wp-login.php?redirect_to=/bli-medlem/"><span class="login">Logga in</span></a></p>
<p class="disabled"><span>2.</span> Betala <span class="coin">1kr</span></p>
<p class="disabled"><span>3.</span> Berätta om dig själv</p>
<?php } else {  ?>
<p><span>1.</span> <span class="done">✔</span><span class="login disabled">Logga in</span></p>
<p><span>2.</span> Betala <span class="coin">1kr</span></p>
<p><span>3.</span> Berätta om dig själv</p>
<?php } ?>
</div>

                
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
<?php endwhile; 
get_template_part( 'loop-nav' ); 
else : 
get_template_part( 'loop-no-posts' ); 
endif; 
?>  
</div><!-- end of #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
