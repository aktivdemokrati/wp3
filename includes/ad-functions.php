<?php
/**
 * Aktiv Demokrati functions and definitions
 */


////////////////////////////////////////////////////////////

function ad_upload_ext($mimes='')
{
  $mimes['svg'] ='image/svg+xml';
  $mimes['svgz']='image/svg+xml';
  $mimes['eps'] ='application/postscript';
  $mimes['sla'] ='application/x-scribus';
  $mimes['xcf'] ='application/x-xcf';
  return $mimes;
}

add_filter( 'upload_mimes',"add_upload_ext" );

////////////////////////////////////////////////////////////

function ad_redirect()
{
  //ad_log( "GOT ".$_SERVER['REQUEST_URI']);

  if( preg_match('#/senaste/([^/]+)/#', $_SERVER['REQUEST_URI'], $matches) )
    { 
      //ad_log( "  FOUND '".$matches[1]."'");
      global $ad_menu_current;
      $ad_menu_current = "/senaste/".$matches[1]."/";
      $category = get_category_by_slug($matches[1]);
      //ad_log( "  cat id ". $category->term_id);

      global $wp_query;
      $wp_query = new WP_Query(array('cat' => $category->term_id, 'showposts' => 1, 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
      $posts = $wp_query->get_posts();

      $wp_query = new WP_Query( 'p='.$posts[0]->ID );
      //wp_reset_query(); // do NOT reset query

      $requested_url  = is_ssl() ? 'https://' : 'http://';
      $requested_url .= $_SERVER['HTTP_HOST'];
      $redirect_url = $requested_url . '/?p=' . $posts[0]->ID .'&catname=' . $matches[1];
      ad_log("Senaste redirecting to " . $redirect_url);
      return($redirect_url);
    }
  return;
}

add_action( 'template_redirect', 'ad_redirect' );

////////////////////////////////////////////////////////////

function ad_log($row)
{
  error_log($row);
  //  error_log($row."\n",3,'/var/www/web/wse75376/ad-log/php.log');
}

////////////////////////////////////////////////////////////

function log_backtrace()
{
  ob_start();
  debug_print_backtrace();
  $backtrace = ob_get_contents();
  ob_end_clean();
  $rows = explode("\n",$backtrace);
  foreach($rows as $row)
    ad_log($row);
  ad_log('--------------------');
}


////////////////////////////////////////////////////////////


function ad_ap_menu()
{
  if( isset($_GET['csv']) && $_GET['csv'] == "true")
  {
    if ( !current_user_can('edit_users') )
      wpdie('No, that won\'t be working, sorry.');
    require_once(ADHOMESYS.'/include/users2csv.php');
    createcsv();
    exit;
  }

  add_users_page('Medlemsregister export', 'AD export', 'edit_users', 'AD-members-export', 'ad_ap_members_export');
}

add_action( 'admin_menu', 'ad_ap_menu');

////////////////////////////////////////////////////////////

/*
function ad_admin_setup()
{
  // Remove extra admin colour scheme
  global $_wp_admin_css_colors;
  unset( $_wp_admin_css_colors['classic'] );
  wp_admin_css_color('fresh', __('Gray'), ADHOMEURL.'/style-admin.css', array('#464646', '#6D6D6D', '#F1F1F1', '#DFDFDF'));
}

add_action( 'admin_init', 'ad_admin_setup');
*/

////////////////////////////////////////////////////////////

function ad_login_stylesheet() { ?>
    <link rel="stylesheet" id="custom_wp_admin_css"  href="<?php echo get_bloginfo( 'stylesheet_directory' ) . '/ad-style.css'; ?>" type="text/css" media="all" />
<?php }
add_action( 'login_enqueue_scripts', 'ad_login_stylesheet' );

function ad_login_logo_url() {
  return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'ad_login_logo_url' );

function ad_login_logo_url_title() {
  return "Aktiv Demokrati";
}
add_filter( 'login_headertitle', 'ad_login_logo_url_title' );


////////////////////////////////////////////////////////////

function ad_login( $login )
{
  if( $u = get_user_by('login',$login) )
    {
      global $wpdb;
      update_user_meta($u->ID, 'ad_login_timestamp', time());
    }
}
add_action( 'wp_login','ad_login');

////////////////////////////////////////////////////////////

function ad_login_form()
{
  global $rememberme;
  $rememberme = 1;
}
add_action( 'login_form','ad_login_form' );

////////////////////////////////////////////////////////////

function ad_register_form()
{
  ?>
  <p class="follow-wrapper"><label><input name="follow" type="checkbox" id="follow" value="yes" tabindex="90" checked="checked" /> Följ blogg och forum</label></p>
  <?php
}
add_action( 'register_form', 'ad_register_form');


////////////////////////////////////////////////////////////

function ad_follow_phpbb($u)
{
  ad_log("Follow BB digests?");
  if(! $u->user_login)
    return;
  if( $_POST['follow'] )
    {
      global $wpdb;
      $wpdb->query($wpdb->prepare("update phpbb3_users set user_digest_type='WEEK' where username='%s'",$u->user_login));
      ad_log(" + Yes");
    }
}

function ad_follow_blog( $wp_uid )
{
  ad_log("Follow MP Weekly?");
  if(! $wp_uid )
    return;

  if( $_POST['follow'] or $_GET['follow'] )
    {
      $user 	= get_userdata($wp_uid);
      if( class_exists('MP_Users') )
	{
	  $_POST['keep_newsletters']['weekly']=1;
	  $email 	= $user->user_email;
	  $mp_user_id	= MP_Users::get_id_by_email($email);
	  $object_terms = array( 'weekly' => 1 );
	  MP_Newsletters::set_object_terms( $mp_user_id, $object_terms );
	  ad_log(" + Yes");
	}

      /***********************************************
       * Trigger creation of phpBB user,
       * even then user created without FB connection,
       * meaning the login happens after registration.
       */
      try {
	$user = apply_filters('wp_authenticate_user', $user, 'dummy');
	ad_follow_phpbb($user); // <--- Turn on digest 
      } catch (Exception $e) {
	ad_log( $e->getMessage() );
	// Ignoring login exceptions. FB says ok ;)
      }
    }
}

add_action( 'user_register', 'ad_follow_blog',20);

////////////////////////////////////////////////////////////

function ad_insert_rewrite_rules( $rules )
{
    $newrules = array();
    $newrules['lista/(\w*)$'] = 'index.php?pagename=lista&catname=$matches[1]';
    $newrules['senaste/(\w*)$'] = 'index.php?p=1&catname=$matches[1]';

    return $newrules + $rules;
}

add_filter( 'rewrite_rules_array', 'ad_insert_rewrite_rules');


////////////////////////////////////////////////////////////

function ad_insert_query_vars( $vars )
{
  array_push($vars, 'catname');
  return $vars;
}

add_filter( 'query_vars', 'ad_insert_query_vars' );

////////////////////////////////////////////////////////////

  function ad_build_url( $url, $query )
  {
    $ret = "";
    foreach((array)$query as $k => $v)
      {
        $k    = urlencode($k);
        $ret .=  $k."=".urlencode($v);
      }

    if( strpos( $url, '?' ) )
      {
        return $url . '&' . $ret;
      }
    else
      {
        return $url . '?' . $ret;
      }
  }

////////////////////////////////////////////////////////////

function ad_json_controllers($controllers) { return array('core','GOV'); }
add_filter( 'json_api_controllers', 'ad_json_controllers' );

function ad_json_gov_path($default_path) { return TEMPLATEPATH .'/include/json_gov.php'; }
add_filter( 'json_api_gov_controller_path', 'ad_json_gov_path');

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
      $description = $sitename;
      if(isset($site['description']))
	$description = $site['description'];

      /**
       * Start building each list item. They're build up separately to
       * allow filtering by other plugins.
       */
      $link = '<li>';
      $link .= '<a ';
      $link .= 'rel="nofollow" ';
      if( isset($site['onClick']) )
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

function ad_setup()
{
  ad_fb_init();
}
add_action( 'after_setup_theme', 'ad_setup');

////////////////////////////////////////////////////////////

// Avoid loading two pages per request
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

////////////////////////////////////////////////////////////
