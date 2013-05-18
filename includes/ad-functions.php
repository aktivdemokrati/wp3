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

add_filter( 'upload_mimes',"ad_upload_ext" );

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
    require_once(ADHOMESYS.'/includes/users2csv.php');
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

function ad_json_gov_path($default_path) { return TEMPLATEPATH .'/includes/json_gov.php'; }
add_filter( 'json_api_gov_controller_path', 'ad_json_gov_path');

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

function ad_bottom_javascript()
{
  echo "<script>ad_bottom_javascript()</script>";			
}
add_action('responsive_wrapper_bottom','ad_bottom_javascript');