<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;

get_header(); ?>
<div id="content" class="<?php echo implode( ' ', responsive_get_content_classes() ); ?>">
        <?php get_template_part( 'loop-header' ); ?>
        <?php responsive_entry_before(); ?>
	<?php
  global $wp;
  $category_slug = $wp->query_vars['catname'];
//  $category_slug = $_GET['catname'];
$category = get_category_by_slug($category_slug);
if( !$category or !$category->cat_ID )
  {
    echo $before_widget;
    echo "<h4>No such category: $category_slug</h4>";
    echo $after_widget;
    return;
  }
$category_name = $category->cat_name;
query_posts(array( 'cat' => $category->cat_ID,
		   'showposts' => 100,
		   'orderby' => 'title',
		   'order' => 'ASC',
		   'nopaging' => 0,
		   'post_status' => 'publish',
		   'caller_get_posts' => 1));
 if (have_posts()) the_post(); ?>
        
<h1 class="page-title archive-title">
  Lista: <span><?php echo $category_name?></span>
</h1>

<?php
$category_description = category_description();
if ( ! empty( $category_description ) )
  echo '<div class="archive-meta">' . $category_description . '</div>';
?>

<ol id="ad-list">
<?php
rewind_posts();
while ( have_posts() ) :
  the_post();
?>
<li><h2><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute( 'echo=0' ); ?>"><?php the_title(); ?></a></h2></li>
<?php endwhile; // End the loop. Whew. ?>
</ol>
      
</div><!-- end of #content -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
