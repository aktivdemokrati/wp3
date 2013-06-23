<?php
/**
 * Based on Plugin Like by Bottomless, blog.bottomlessinc.com
 *
 */

////////////////////////////////////////////////////////////

if (!defined('AD_FB_INIT')) define('AD_FB_INIT', 1);
else return;

$ad_fb_settings = array();

// Your Facebook ID to manage your Fans and send them updates
// If you have several, separate them with commas.
$ad_fb_settings['facebook_id'] = '754472392,1295336646,548263223';

$ad_fb_settings['facebook_app_id'] = '173560559320949';
$ad_fb_settings['facebook_page_id'] = '165221363500028';
$ad_fb_settings['language'] = 'sv_SE';

$ad_fb_settings['facebook_image'] = 
  'http://aktivdemokrati.se/apple-touch-icon.png';

$ad_fb_types =
  array(
	'Activities', 'activity', 'sport',
	'Businesses', 'bar', 'company', 'cafe', 'hotel', 'restaurant',
	'Groups', 'cause', 'sports_league', 'sports_team',
	'Organizations', 'band', 'government', 'non_profit', 'school', 'university',
	'People', 'actor', 'athlete', 'author', 'director', 'musician', 'politician', 'public_figure',
	'Places', 'city', 'country', 'landmark', 'state_province',
	'Products and Entertainment', 'album', 'book', 'drink', 'food', 'game', 'movie', 'product', 'song', 'tv_show',
	'Websites', 'article', 'blog', 'website'
);


function ad_fb_init()
{
  // FB button added by ad_sociable_html()
  //  add_filter('the_content', 'ad_fb_widget');

  // Not used for HTML5
  //  add_filter('language_attributes', 'ad_fb_schema');
  //add_filter('wp_admin_css', 'ad_fb_admin_css', 1, 2);

  add_filter('registration_redirect', 'ad_fb_registration_redirect');

  add_action('wp_head', 'ad_fb_widget_header_meta',1);
  add_action('wp_footer', 'ad_fb_widget_footer');
  add_action('wp_logout', 'ad_fb_on_logout');
  add_action('register_form', 'ad_fb_register_form');
  add_action('user_register', 'ad_fb_user_register', 1);
  add_action('login_footer', 'ad_fb_login_footer');
  add_action('login_enqueue_scripts', 'ad_fb_login_enqueue_scripts');
  add_action('login_head', 'ad_fb_login_head');

  global $ad_fb_settings;
  $config = array();
  $config['appId'] = $ad_fb_settings['facebook_app_id'];
  $config['secret'] = FACEBOOK_APP_SECRET;
  $config['fileUpload'] = false; // optional

  global $ad_fb;
  $ad_fb = new Facebook($config);

  //  ad_log(var_export($config,1));
  //  ad_log(var_export($ad_fb,1));

  ad_log($_SERVER['REQUEST_URI']);
  ad_fb_login();
}

function ad_fb_schema($attr)
{
  $attr .= "\n xmlns:og=\"http://opengraphprotocol.org/schema/\"";
  $attr .= "\n xmlns:fb=\"http://www.facebook.com/2008/fbml\"";
  return $attr;
}


function ad_fb_widget_header_meta()
{
  global $ad_fb_settings;

  $fbid = trim($ad_fb_settings['facebook_id']);
  $fbappid = trim($ad_fb_settings['facebook_app_id']);
  $fbpageid = trim($ad_fb_settings['facebook_page_id']);

  // Defaults
  $ad_fb_settings['og']['type'] = 'article';
  $ad_fb_settings['og']['image'] = trim($ad_fb_settings['facebook_image']);

  if( is_front_page() )
    {
      $ad_fb_settings['og']['type'] = 'non_profit';
      $ad_fb_settings['og']['country_name'] = 'Sweden';
      $ad_fb_settings['og']['email'] = 'kontakt@aktivdemokrati.se';
      $ad_fb_settings['og']['title'] = 'Aktiv Demokrati';
    }	  
  elseif( is_home() )
    {
      $ad_fb_settings['og']['type'] = 'blog';
    }
  elseif(is_single() || is_page())
    {
      $title = the_title('', '', false);
      $title = html_entity_decode($title,ENT_QUOTES,'UTF-8');
      $ad_fb_settings['og']['title'] = $title;
      $ad_fb_settings['og']['url'] = get_permalink();

      global $post;
      if( $tags = get_the_tags($post->ID) )
	{
	  global $ad_fb_types;
	  foreach( $tags as $tag )
	    {
	      if( in_array( $tag->name, $ad_fb_types ) )
		{
		  $ad_fb_settings['og']['type'] = $tag->name;
		  break;
		}
	    }
	}

      $args = array(
		    'post_type' => 'attachment',
		    'post_mime_type' => 'image',
		    'post_parent' => $post->ID,
		    );

      if( $thumb_id = get_post_thumbnail_id($post->ID) )
	{
	  $thumb = wp_get_attachment_image_src( $thumb_id, 'thumbnail' );
	  $ad_fb_settings['og']['image'] = $thumb[0];
	}
      elseif( $images = get_children( $args ) )
	{
	  //ad_log("TWIST Images");
	  foreach( $images as $image )
	    {
	      $ad_fb_settings['og']['image'] =
		array_shift(wp_get_attachment_image_src( $image->ID, 'thumbnail' ));
	      break;
	    }
	}

      if( $ad_fb_settings['og']['image'] )
	{
	  echo "<link rel=\"image_src\" href=\"".$ad_fb_settings['og']['image']."\" />\n";
	}
    }



  echo '<meta property="fb:admins" content="'.$fbid.'" />'."\n";
  echo '<meta property="fb:app_id" content="'.$fbappid.'" />'."\n";
  echo '<meta property="fb:page_id" content="'.$fbpageid.'" />'."\n";

  echo '<meta property="og:site_name" content="'.htmlspecialchars(get_bloginfo('name')).'" />'."\n";

  foreach($ad_fb_settings['og'] as $k => $v)
    {
      $v = trim($v);
      if($v!='')
	echo '<meta property="og:'.$k.'" content="'.htmlspecialchars($v).'" />'."\n";
    }

}

function ad_fb_widget_footer()
{
  global $ad_fb_settings;
  $lang = $ad_fb_settings['language'];

  $appid = trim($ad_fb_settings['facebook_app_id']);
  //  $home = preg_replace('/^https?:/','',ADHOMEURL . "/facebook/channel.html");
  $home = ADHOMEURL . "/facebook/channel.html";

  echo <<<END
<div id="fb-root"></div>
<script>
window.fbAsyncInit = function() {
FB.init({
appId: '$appid',
channelURL: '$home',
status: true,
cookie: true,
oauth: true,
});

fb_init();
};
(function() {
var e = document.createElement('script'); e.async = true;
e.src = document.location.protocol +
'//connect.facebook.net/$lang/all.js';
document.getElementById('fb-root').appendChild(e);
}());
</script>
END;
}


/**
* Appends Like button to post content.
*/
function ad_fb_widget($content, $sidebar = false)
{
    if( !is_single() && !is_page() )
      return $content;

    global $ad_fb_settings;

    $purl = get_permalink();

    $button = "\n<!-- AD FB Like Button BEGIN -->\n";

    $button .= '<fb:like href="'.$purl.'" layout="standard" show_faces="true" width="450" action="recommend" colorscheme="light"'.$xfbml_font.'></fb:like>';

    if(0) // align right
      {
	$button = '<div style="float: right; clear: both; text-align: right">'.$button.'</div>';
      }

    $button .= "\n<!-- AD FB Like Button END -->\n";

    $content .= $button;
    return $content;
}


/**
* Redirect to the the right place after login
*/
function ad_login_redirect()
{
  if( $redirect_to = $_REQUEST['redirect_to'] )
    {
      wp_safe_redirect( $redirect_to );
    }
  elseif( preg_match('/\/wp-login\.php/',$_SERVER["REQUEST_URI"]) )
    {
      wp_safe_redirect( '/member/' );
    }
}


/**
* Sync WP login with FB login
*/
function ad_fb_login()
{
  global $ad_fb;
  $fb_uid = ad_fbuid();
  $current_user = wp_get_current_user();
 
  if(! $fb_uid )
    {
      if( $current_user->ID and isset($_GET['autologin']) )
	{
	  // Redo login actions in order to refresh associated login services
	  try {
	    apply_filters('wp_authenticate_user', $current_user, 'dummy');
	    ad_log("wp_authenticate_user filter applied");
	  } catch (Exception $e) {
	    ad_log( $e->getMessage() );
	    // Ignoring login exceptions. FB says ok ;)
	  }
	  do_action('wp_login', $current_user->user_login);
	  ad_login_redirect();
	}
      
      return;
    }

  if( $current_user->ID )
    {
      ad_log("WP logged in as ".$current_user->user_login);
      if( isset($_GET['autologin']) )
	{
	  ad_fb_connect($current_user->ID, $fb_uid );
	  
	  // Redo login actions in order to refresh associated login services
	  try {
	    apply_filters('wp_authenticate_user', $current_user, 'dummy');
	    ad_log("wp_authenticate_user filter applied");
	  } catch (Exception $e) {
	    ad_log( $e->getMessage() );
	    // Ignoring login exceptions. FB says ok ;)
	  }
	  do_action('wp_login', $current_user->user_login);
	  ad_login_redirect();
	}
    }
  else
    {
      ad_log("WP logged out");
      $user = ad_fb_lookup_user();
      if( $user )
        {
          ad_log("Found connected WP user");

          if( isset($_GET['autologin']) )
            {
              ad_log("Turning on autologin");
              update_user_meta($user->ID, 'facebook_autologin', 'yes');
            }

          if( get_user_meta($user->ID, 'facebook_autologin', true) == 'yes' )
            {
              ad_log("AUTOLOGIN");
	      try {
		$user = apply_filters('wp_authenticate_user', $user, 'dummy');
		ad_log("wp_authenticate_user filter applied");
	      } catch (Exception $e) {
		ad_log( $e->getMessage() );
		// Ignoring login exceptions. FB says ok ;)
	      }
              wp_set_auth_cookie( $user->ID, true );
              do_action('wp_login', $user->user_login);
	      wp_set_current_user($user->ID);
	      ad_login_redirect();
           }
          else
            {
              ad_log("No autologin");
            }
        }
      else
        {
          if( isset($_GET['autologin']) ) // Explicit login
            {
              $redirect = home_url('/wp-login.php?action=register');

              if(! preg_match('/\/wp-login\.php/',$_SERVER["REQUEST_URI"]) )
                {
                  $redirect .= '&redirect_to=' . urlencode($_SERVER["REQUEST_URI"]);
                }
              

              ad_log("Redirecting to ".$redirect);
              wp_safe_redirect($redirect);
              //header("Location: " . $redirect);
              exit;
            }

          ad_log("No connected WP user found");
        }
    }
}

function ad_fb_lookup_user()
{
  global $ad_fb;

  $fb_uid = ad_fbuid();

  global $wpdb;
  if( $fb_uid )
    {
      $wp_uids = $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'facebook_uid' AND meta_value ='%d' ", $fb_uid));
      if( $wp_uids[0] )
	{
	  ad_log("Found user by facebook_uid: ".$wp_uids[0]);
	  return get_userdata($wp_uids[0]);
	}
    }

  $fbuser = ad_fbuser($fb_uid);
  if( !$fbuser['id'] )
    return;

  // Lookup user by email
  if( preg_match( '/@/', $fbuser['email'] ) )
    {
      if( $wp_user = get_user_by('email', $fbuser['email']) )
	{
	  ad_log("Found user by email: ".$wp_user->ID);
	  ad_fb_connect( $wp_user->ID, $fb_uid );
	  return $wp_user;
	}

      $wp_uids = $wpdb->get_col( $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'ad_email_extra' AND meta_value ='%s' ", $fbuser['email']));
      if( $wp_uids[0] )
	{
	  ad_log("Found user by ad_email_extra: ".$wp_uids[0]);
	  ad_fb_connect( $wp_uids[0], $fb_uid );
	  return get_userdata($wp_uids[0]);
	}
    }
  return;
}

function ad_fb_connect($wp_uid, $fb_uid )
{
  ad_log("Connecting WP user with FB");
  update_user_meta($wp_uid, 'facebook_uid', $fb_uid);

  if( get_user_meta($wp_uid, 'facebook_autologin', true) == 'no' )
    return;
  update_user_meta($wp_uid, 'facebook_autologin', 'yes', '');
}

function ad_fb_on_logout()
{
  ad_log("Turning off autologin");
  $current_user = wp_get_current_user();
  update_user_meta($current_user->ID, 'facebook_autologin', 'no');
}

function ad_fbuid()
{
  global $ad_fb;
  if ( !session_id() )
    session_start();

  if( isset($_GET['fb']) )
    {
        $_SESSION["$fb_uid/me"] = null;
        $ad_fb->setAccessToken($_GET['fb'] );
    }

  return $ad_fb->getUser();
}

function ad_fbuser($fb_uid=null)
{
  global $ad_fb;
  if( !$fb_uid )
    $fb_uid = ad_fbuid();

  if ( !session_id() )
    session_start();
  $fbuser = $_SESSION["$fb_uid/me"];
    
  if(! $fbuser )
    {
      ad_log("Get user info from FB");
      try
	{
	  $fbuser = $ad_fb->api('/me');
	  $_SESSION["$fb_uid/me"] = $fbuser;
	}
      catch( Exception $e )
	{
	  ad_log( $e->getMessage() );
	}
    }
  return $fbuser;
}

function ad_fb_available_login($fbuser)
{
  preg_match('/(.*?)@/', $fbuser['email'], $ematch);

  foreach(array($fbuser['username'], $fbuser['first_name'], $ematch[1], $fbuser['name']) as $login_in )
    {
      if(!$login_in)
        continue;
      $login = strtolower(sanitize_user($login_in));
      if(! username_exists($login) )
        return $login;
    }
  
  $login = $fbuser['username'] or
    $login = $fbuser['first_name'] or
    $login = $fbuser['name'];
  $login = strtolower(sanitize_user($login));
  for( $i=1; $i<=10; $i++)
    {
      $suf = rand(1,99)*$i;
      if(! username_exists($login . $suf) )
        return $login . $suf;
    }
  return "";
}

function ad_fb_user_register( $wp_uid )
{
  $current_user = wp_get_current_user();
  if( $current_user->ID and $current_user->ID != $wp_uid )
    {
      ad_log("Current user is ".$current_user->ID);
      ad_log("WP user registering is ".$wp_uid);
      return;
    }
  $fbuser = ad_fbuser();
  if( !$fbuser['id'] )
    {
      ad_log("No fb user found");
      return;
    }

  ad_log("REGISTER USER WITH FB");

  ad_fb_connect($wp_uid, $fbuser['id'] );

  if( $first_name = $fbuser['first_name'] )
    update_user_meta( $wp_uid, 'first_name', $first_name );

  if( $last_name = $fbuser['last_name'] )
    update_user_meta( $wp_uid, 'last_name', $last_name );

  if( $description = $fbuser['bio'] )
    update_user_meta( $wp_uid, 'description', $description );

  if( $fbuser['location'] )
    $location = $fbuser['location']['name'];
  if( !$location and $fbuser['hometown'] )
    $location = $fbuser['hometown']['name'];
  if( $location )
    {
      $loc_city = preg_replace('/,.*/','', $location);
      update_user_meta( $wp_uid, 'loc_city', $loc_city );
      update_user_meta( $wp_uid, 'ad_region', $loc_city );
      
    }


  $url = $fbuser['website'];
  if( !$url and $fbuser['username'] )
    $url = "http://www.facebook.com/".$fbuser['username'];
  if( !$url )
    $url = "http://www.facebook.com/profile.php?id=".$fbuser['id'];

  $name = $fbuser['name'];

  wp_update_user( array( 'ID' => $wp_uid,
			 'display_name' => $name,
			 'user_url' => $url,
			 )) ;

  ad_log("Registration done");
  ad_fb_login();
}

function ad_fb_login_footer()
{
?>
<div id="dim"></div>
<img id="spinner-center" alt="" src="<?php echo(ADHOMEURL) ?>/images/loading-spinner.gif">
  <script>
  $ = jQuery;
<?php
  if( $fbuser = ad_fbuser() ):
    $name = ad_fb_available_login($fbuser);
  $email = $fbuser['email'];
?>
  if( $('#registerform').size() )
    {
	$('#user_login').val('<?php echo $name ?>');
	$('#user_email').val('<?php echo $email ?>');
	$('#registerform p').show();
	$('#reg_passmail').hide();
    }
    else if( $('#loginform').size() )
    {
	$('#loginform').append('<p style="clear:both;margin-top:3em">eller</p><p><a id="fb-auth"><span class="pluginFaviconButtonBorder"><span class="pluginFaviconButtonText">Logga in utan lösenord</span></span></a></p>');
	$('#loginform').css('padding-bottom','2em');

	function fb_init_local()
	{
            FB.getLoginStatus(ad_fb_register_form);
	}
    }
  
</script>
<?php
else:
?>
    function fb_init_local()
    {
        FB.getLoginStatus(ad_fb_register_form);
    }
</script>
<?php
endif;
ad_fb_widget_footer();
}

function ad_fb_registration_redirect($url='')
{
  if($url)
    return $url;
  return "/member/medlemsguide/";
}

function ad_fb_login_enqueue_scripts()
{
    wp_enqueue_script( 'jquery' );
}

function ad_fb_login_head()
{
?>
<script type="text/javascript" src="<?php echo(ADHOMEURL) ?>/js/jquery.ba-bbq.min.js"></script>
<script type="text/javascript" src="<?php echo(ADHOMEURL) ?>/js/ad.js?v=3"></script>
<?php
}

function ad_fb_register_form()
{
  if( $fbuser = ad_fbuser() ):
?>
<p><img src="https://graph.facebook.com/<?php echo $fbuser['id']; ?>/picture" style="float: left; width: 32px; margin-right: 0.7em">
<strong><?php echo $fbuser['name']; ?></strong><br>
Koppla till din Facebook</p>
<?php
else:
?>
<div id="fb-register">
<p><a id="fb-auth" class="fb_button fb_button_medium"><span class="fb_button_text">Registrera med Facebook</span></a></p>
<p>eller</p>
<p><a id="wp-auth" class="wp_button"><span class="wp_button_text">Registrera direkt hos AD</span></a></p>
</div>
<?php
endif;
}
