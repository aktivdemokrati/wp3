<?php

////////////////////////////////////////////////////////////

function ad_widgets_init() {
	register_widget('AD_Widget_Recent_Posts');
	register_widget('AD_Widget_Random_Posts');
	register_widget('AD_Widget_Text');
	register_widget('AD_Feeds');
	register_widget('AD_Widget_Register');
}
add_action( 'widgets_init', 'ad_widgets_init' );

////////////////////////////////////////////////////////////

/**
 * Recent_Posts widget class
 *
 * @since 2.8.0
 */
class AD_Widget_Recent_Posts extends WP_Widget {

	function AD_Widget_Recent_Posts() {
	  $widget_ops = array('classname' => 'widget_recent_entries_ad', 'description' => __( "The most recent posts on your site, AD style",'ad') );
		$this->WP_Widget('recent-posts-ad', __('AD Recent Posts','ad'), $widget_ops);
		$this->alt_option_name = 'widget_recent_entries_ad';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_posts_ad', 'widget');
                

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		global $wp_query;
                $original_query = $wp_query;

		ob_start();
		extract($args);

		$category_slug = $instance['category'];
		//ad_log("Getting category $category_slug");
		$category = get_category_by_slug($category_slug);
		//ad_log("Got ".var_export($category,1));
		if( !$category or !$category->cat_ID )
		  {
		    echo $before_widget;
		    echo "<h4>No such category: $category_slug</h4>";
		    echo $after_widget;
		    return;
		  }

		$category_name = $category->cat_name;

		$title = apply_filters('widget_title', empty($instance['title']) ? $category_name : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		// Must use the global $wp_query. It's used by plugins for formatting the_excerpt
		$wp_query = new WP_Query(array('cat' => $category->cat_ID, 'showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
		if ($wp_query->have_posts()) :
		  echo $before_widget;
		if ( $title ) echo $before_title . '<a href="'.get_category_link($category->cat_ID).'">'.$title .'</a>'.$after_title; ?>
		  <div>
		     <?php  while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
		     <h4><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h4>
                     <?php the_excerpt(); ?>
	    <?php endwhile; ?>
		</div>
		<?php echo $after_widget;
		// Reset the global $the_post as this query will have stomped on it
                $wp_query = $original_query;
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_posts_ad', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries_ad']) )
			delete_option('widget_recent_entries_ad');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_posts_ad', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
		$category = isset($instance['category']) ? esc_attr($instance['category']) : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category of posts to show:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo $category; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}


////////////////////////////////////////////////////////////

/**
 * Random_Posts with link to list widget class
 *
 */
class AD_Widget_Random_Posts extends WP_Widget {

	function AD_Widget_Random_Posts() {
	  $widget_ops = array('classname' => 'widget_random_entries_ad', 'description' => __( "Random posts on your site, AD style",'ad') );
		$this->WP_Widget('random-posts-ad', __('AD Random Posts','ad'), $widget_ops);
		$this->alt_option_name = 'widget_random_entries_ad';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_random_posts_ad', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		global $wp_query;

		ob_start();
		extract($args);

		$category_slug = $instance['category'];
		//ad_log("Getting category $category_slug");
		$category = get_category_by_slug($category_slug);
		//ad_log("Got ".var_export($category,1));
		if( !$category or !$category->cat_ID )
		  {
		    echo $before_widget;
		    echo "<h4>No such category: $category_slug</h4>";
		    echo $after_widget;
		    return;
		  }

		$category_name = $category->cat_name;

		$title = apply_filters('widget_title', empty($instance['title']) ? $category_name : $instance['title'], $instance, $this->id_base);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 15 )
			$number = 15;

		// Must use the global $wp_query. It's used by plugins for formatting the_excerpt
		$wp_query = new WP_Query(array('cat' => $category->cat_ID, 'showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1, 'orderby' => 'rand' ));
		if ($wp_query->have_posts()) :
		  echo $before_widget;
		if ( $title ) echo $before_title . '<a href="/lista/?catname='.$category_slug.'">'.$title .'</a>'.$after_title; ?>
		  <div>
		     <?php  while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
		     <h4><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></h4>
                     <?php the_excerpt(); ?>
	    <?php endwhile; ?>
		</div>
		<?php echo $after_widget;
		// Reset the global $the_post as this query will have stomped on it
                $wp_query = $original_query;
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_random_posts_ad', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_random_entries_ad']) )
			delete_option('widget_random_entries_ad');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_random_posts_ad', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
		$category = isset($instance['category']) ? esc_attr($instance['category']) : '';
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category of posts to show:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo $category; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}
add_shortcode('random-post-box', 'ad_random_post_box');


////////////////////////////////////////////////////////////

/**
 * AD Register widget class
 */

class AD_Widget_Register extends WP_Widget
{
  function AD_Widget_Register()
  {
    $widget_ops = array('classname' => 'ad_widget_register',
			'description' => "AD-Anpassat registreringsformulär" );
    $this->WP_Widget('ad-register', 'AD Register', $widget_ops);
  }

  function widget($args, $instance)
  {
    extract($args);

    echo $before_widget;
    echo $before_title;
    echo $instance['title'];
    echo $after_title;
    $this->ad_widget_register_show();
    echo $after_widget;

  }

  function update( $new_instance, $old_instance )
  {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    return $instance;
  }

  function form( $instance )
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
    $title = strip_tags($instance['title']);
    ?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
         <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
      <?php
  }

  function ad_widget_register_show()
  {
    if( !is_user_logged_in() )
      {
	echo "
<form name='registerform' id='registerform' action='http://aktivdemokrati.se/wp-login.php?action=register' method='post'>

<p><label><span>Namn</span><input type='text' name='user_login' id='user_login' class='input' value='' tabindex='10' /></label></p>

<p><label><span>E-post</span><input type='text' name='user_email' id='user_email' class='input' value='' tabindex='20' /></label></p>
<p class='submit'>&nbsp;<input type='submit' name='wp-submit' id='wp-submit' class='button-primary' value='Gå med!' tabindex='100' /></p>

<input type='hidden' name='redirect_to' value='' />


</form>
";
      }
  }
}


////////////////////////////////////////////////////////////


/**
 * Text with links widget class
 *
 * @since 2.8.0
 */
class AD_Widget_Text extends WP_Widget {

	function AD_Widget_Text() {
		$widget_ops = array('classname' => 'ad_widget_text', 'description' => __('Arbitrary text or HTML. Accepts html in title'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('ad_text','AD Text', $widget_ops, $control_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = $instance['title'];
		$text = apply_filters( 'ad_widget_text', $instance['text'], $instance );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $instance['filter'] ? wpautop($text) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
		$title = $instance['title'];
		$text = format_to_edit($instance['text']);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs'); ?></label></p>
<?php
	}
}


////////////////////////////////////////////////////////////


/**
 * AD feeds (sidebar footer)
 */
class AD_Feeds extends WP_Widget {

	function AD_Feeds() {
	  $widget_ops = array('classname' => 'ad_feeds', 'description' => __( "List of feeds for the sidebar footer",'ad') );
	  $this->WP_Widget('ad-feeds', __('AD Feeds','ad'), $widget_ops);
		$this->alt_option_name = 'ad_feeds';
	}

	function widget($args, $instance) {
?>
<li id="feeds">
<h3 class="widget-title">Kanaler</h3>
<ul>
<li><a class="rss" title="Prenumerera på den här webbplatsen via RSS 2.0" href="http://aktivdemokrati.se/feed/">Inlägg i bloggen</a></li>
<li><a class="rss" title="De senaste kommentarerna till alla inlägg via RSS" href="http://aktivdemokrati.se/comments/feed/">Blogg-kommentarer</a></li>
<li><a class="rss" title="Prenumerera på forumet via RSS" href="http://aktivdemokrati.se/forum/feed.php?mode=news">Inlägg i forumet</a></li>
<li><a class="rss" title="De senaste kommentarerna på forumet via RSS" href="http://aktivdemokrati.se/forum/feed.php">Forum-kommentarer</a></li>
<li><a class="fb" title="Följ oss på Facebook" href="http://www.facebook.com/aktivdemokrati">AD på Facebook</a></li>
<li><a class="yt" title="Följ oss på YouTube" href="http://www.youtube.com/user/AktivDemokrati">AD på YouTube</a></li>
<li><a class="gplus" title="Följ oss på Google+" href="https://plus.google.com/117631766948561738723/" rel="publisher">AD på Google+</a></li>
<li><a class="twitter" title="Följ oss på Twitter" href="https://twitter.com/Aktiv_Demokrati">AD på Twitter</a></li>
</ul>
</li>

<?php
	}
}

////////////////////////////////////////////////////////////

function ad_random_post_box()
{
  $code = '<div id="random-post-box-frame"><div style="display: block;" id="random-post-box">';

  //global $wp_query;
  $wp_query = new WP_Query(array('orderby' => 'rand',
				 'category_name' => 'citat',
				 'posts_per_page' => 1));
  if( $wp_query->have_posts() )
    {
      global $post;
      //$old_post = $post;
      $wp_query->the_post();
      //ad_log("item ".var_export($post,1));

      if( has_post_thumbnail( $post->ID ) )
	{
	  $code .= sprintf('<a title="%s" href="%s">',
		       htmlspecialchars($post->post_title),
		       get_permalink($post->ID)
		       );
	  $code .= get_the_post_thumbnail( $post->ID, 'thumbnail', array('class'=>'alignleft') );
	  $code .= '</a>';
	}


      $code .= sprintf( '<h3><a href="%s" title="%s">%s</a></h3>',
			get_permalink($post->ID),
			htmlspecialchars($post->post_title),
			esc_attr( $post->post_title ) );
      $code .= get_the_content(); 
      $code .= sprintf('</div><a id="random-link" title="%s" href="%s"><em>Läs mer</em></a></div>',
		       htmlspecialchars($post->post_title),
		       get_permalink($post->ID)
		       );
      //$post = $old_post;
      wp_reset_postdata();
    } 
  else
    {
      $code .= "</div></div>";
    }
  
  return $code;
}

////////////////////////////////////////////////////////////

function ad_add_sociable($content)
{
  if( is_page() or is_single() )
    return $content . ad_sociable_html();
  
  return $content;
}
add_filter( 'the_content', 'ad_add_sociable');

////////////////////////////////////////////////////////////

function ad_sociable_html()
{
  global $post; 
  
  /*
  global $ad_trace;
  $e = new Exception();
  $ad_trace .= "\n----------\n\n".$e->getTraceAsString();
  ad_log("ad_sociable_html ".$post->ID);
  */

  $imagepath = ADHOMEURL."/images-ad/16/";

  $blogname 	= urlencode(get_bloginfo('name')." ".get_bloginfo('description'));

  $excerpt_in = ad_get_excerpt($post->post_excerpt); // Avoid loop...
  $excerpt	= urlencode($excerpt_in);
  $excerpt	= str_replace('+','%20',$excerpt);

  //  $permalink 	= urlencode(get_permalink($post->ID));
  $permalink 	= get_permalink($post->ID);
  $shortlink    = urlencode(wp_get_shortlink());
  $title_in = $post->post_title;
  if(!$title_in) $title_in = $blogname;
  $title        = str_replace('+','%20',urlencode($title_in));
  $html = '';

  // Add facebook button
  $html .= '<div class="fb-wrapper">';
  $html .= '<fb:like href="'.$permalink.'" layout="standard" show_faces="true" width="450" action="recommend" colorscheme="light"></fb:like>';
  $html .= '</div>';


  // Start preparing the output
  $html .= "\n<div class=\"sociable\">\n";
  $html .= "<div class=\"sociable_tagline\">\n";
  $html .= "Dela med dig:";
  $html .= "\n</div>";

  /**
   * Start the list of links
   */
  $html .= "\n<ul>\n";

  $display = Array(
		   'Twitter' => Array
		   (
		    'favicon' => 'twitter.png',
		    'url' => 'http://twitter.com/home?status=%40Aktiv_Demokrati%3A%20TITLE%20-%20SHORTLINK%20#demokrati',
		    'supportsIframe' => false,
		    ),
		   
		   'Digg' => Array
		   (
		    'favicon' => 'digg.png',
		    'url' => 'http://digg.com/submit?phase=2&amp;url=PERMALINK&amp;title=TITLE&amp;bodytext=EXCERPT',
		    ),

		   'MySpace' => Array
		   (
		    'favicon' => 'myspace.png',
		    'url' => 'http://www.myspace.com/Modules/PostTo/Pages/?u=PERMALINK&amp;t=TITLE',
		    'supportsIframe' => false,
		    ),

		   'print' => Array
		   (
		    'favicon' => 'printer.png',
		    'url' => '#',
		    'description' => "Skriv ut sidan",
		    'onClick' => "window.print();return false",
		    ),

		   'PDF' => Array
		   (
		    'favicon' => 'pdf.png',
		    'url' => 'http://www.printfriendly.com/print?url=PERMALINK&amp;partner=sociable',
		    ),

		   'RSS' => Array
		   (
		    'favicon' => 'bg_feed.gif',
		    'url' => get_post_comments_feed_link(),
		    'supportsIframe' => false,
		    'description' => 'Kommentarer till '.$post->post_title,
		    'display' => comments_open($post->ID),
		    ),
		   );

  foreach( $display as $sitename => $site)
    {
      if(isset($site['display']) and !$site['display']) continue;

      $url = $site['url'];
      $url = str_replace('TITLE', $title, $url);
      $url = str_replace('BLOGNAME', $blogname, $url);
      $url = str_replace('EXCERPT', $excerpt, $url);
      $url = str_replace('PERMALINK', $permalink, $url);		
      $url = str_replace('SHORTLINK', $shortlink, $url);		
      $description = $site['description'];
      if(!$description) $description = $sitename;

      /**
       * Start building each list item. They're build up separately to
       * allow filtering by other plugins.
       */
      $link = '<li>';
      $link .= '<a ';
      $link .= 'rel="nofollow" ';
      if( $site['onClick'] )
	{
	  $link .= sprintf('onClick="%s" ', $site['onClick']);
	}
      $link .= " href=\"$url\" title=\"$description\">";			
      
      $imgsrc = $imagepath.$site['favicon'];
      $link .= "<img src=\"".$imgsrc."\" title=\"$description\" alt=\"$description\"";
      $link .= " class=\"sociable-hovers\"";
      $link .= " />";
      $link .= "</a></li>";
		
      /**
       * Add the list item to the output HTML, but allow other plugins
       * to filter the content first.  This is used for instance in
       * the Google Analytics for WordPress plugin to track clicks on
       * Sociable links.
       */
      $html .= "\t".apply_filters('sociable_link',$link)."\n";
      $i++;
    }

  $html .= "</ul>\n</div>\n";
  
  return $html;
}

///////////////////////////////////////////////////////////

/*
 * The default filter wp_trim_excerpt() will call the filter for
 * 'the_content' if no excerpt exist. It then applys the filters for
 * wp_trim_excerpt on the result. We don't want the added content
 * given by the_content filters. Thus, we must add our own default
 * excerpt here. And do it smarter!
 */

function ad_get_excerpt($content)
{
  if( $content != '' )
    return $content;

  global $post, $id; // Initialize $post if needed. (If called from header)
  if( !$id ) setup_postdata($post);

  $text = strip_tags(strip_shortcodes(get_the_content('')));
  $text = trim(preg_replace('/\s+/',' ',$text));

  //$len1 = strlen($text);
  if( strlen($text) > 298 )
    {
      $text = substr( $text, 0, 295 );
      $text = preg_replace('/(.*)\..*/', '$1.', $text) . ' …';
    }
  //$len2 = strlen($text);
  //echo "<tt>($len1/$len2) $text</tt>";
  //global $id, $post;
  //$pid = $post->ID;
  //ad_log("ad_get_excerpt $id,$pid ($len1/$len2)");

  return $text;
}
 add_filter( 'get_the_excerpt', 'ad_get_excerpt',1); // run before wp_trim_excerpt

///////////////////////////////////////////////////////////

?>
