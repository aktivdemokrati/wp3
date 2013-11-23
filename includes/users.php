<?php

////////////////////////////////////////////////////////////

/* Intended for not allowing username to be used as spam
 */

function ad_sanitize_user( $username )
{
  $username = preg_replace( '/\.\/:/', '', $username );
  $username = substr($username,0,30);
  return $username;
}
add_filter( 'sanitize_user', 'ad_sanitize_user');

////////////////////////////////////////////////////////////

function ad_user_contactmethods( $contactmethods )
{
  //Remove AIM, Yahoo IM
  unset($contactmethods['aim']);
  unset($contactmethods['yim']);
  unset($contactmethods['jabber']);

  //Add some fields
  $contactmethods['ad_email_extra']   = 'Extra e-postadress';
  $contactmethods['phone_home']       = 'Telefonnummer hem';
  $contactmethods['phone_work']       = 'Telefonnummer arbete';
  $contactmethods['phone_mobile']     = 'Telefonnummer mobil';
  $contactmethods['loc_address']      = 'Adress / Box';
  $contactmethods['loc_co']           = 'c/o';
  $contactmethods['loc_zip']          = 'Postnummer';
  $contactmethods['loc_city']         = 'Postadress';
  $contactmethods['jabber']           = 'Jabber / Google Talk';
  $contactmethods['msn']              = 'MSN Live';
  $contactmethods['twitter']          = 'Twitter';
  $contactmethods['skype']            = 'Skype';


  return $contactmethods;
}
add_filter( 'user_contactmethods', 'ad_user_contactmethods',10,1);

////////////////////////////////////////////////////////////

function ad_manage_users_columns( $columns )
{
  $columns['loc_city'] = 'Ort';
  $columns['registred'] = 'Registrerad';
  return $columns;
}
add_filter( 'manage_users_columns', 'ad_manage_users_columns' );

function ad_manage_users_custom_column( $value, $column_name, $user_id )
{
  if( $column_name == 'loc_city' )
    {
      return get_user_option( 'loc_city', $user_id, false );
    }
  elseif( $column_name == 'registred' )
    {
      return get_the_author_meta('user_registered',$user_id );
    }

  return $value;
}
add_filter( 'manage_users_custom_column', 'ad_manage_users_custom_column', 10, 3 );

////////////////////////////////////////////////////////////

function ad_ap_members()
{
  include( ADHOMESYS.'/ad-members.php' );
}

////////////////////////////////////////////////////////////

/*
function ad_ap_tools()
{
  $baseurl = admin_url( 'tools.php?page=AD-tools' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>AD Verktyg</h2>
</div>

<p><a href="<?php echo $baseurl ?>&amp;flush_rewrite_rules=true">Bygg om rewrite_rules</a></p>
<?php
}
*/

////////////////////////////////////////////////////////////

function ad_ap_members_export()
{
  $baseurl = admin_url( 'users.php?page=AD-members-export' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Får det lov att vara en liten export kanske?
</h2>
</div>

<p><a href="<?php echo $baseurl ?>&amp;csv=true">Ja tack!</a></p>
<?php
}

////////////////////////////////////////////////////////////

/*
function ad_ap_forum_import()
{
  $baseurl = admin_url( 'users.php?page=AD-forum-import' );
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Importera hjältar från forumet
</h2>
</div>
<?php
  $login = $_POST['bb'];
  if( $login )
    {
      $hlogin = htmlspecialchars($login);
      $url = "/wp-admin/user-edit.php?user_id=";
      if( $u = get_user_by('login',$login) )
	{
	  echo "<p><a href='$url$u->ID'>$hlogin</a> finns redan</p>";
	}
      elseif( $u = get_user_by('email',$login) )
	{
	  echo "<p><a href='$url$u->ID'>$hlogin</a> finns redan</p>";
	}
      elseif( $u = get_user_by_email($login) )
	{
	  echo "<p><a href='$url$u->ID'>$hlogin</a> har importerats</p>";
	}
      elseif( $u = get_userdatabylogin($login) )
	{
	  echo "<p><a href='$url$u->ID'>$hlogin</a> har importerats</p>";
	}
      else
	{
	  echo "<p><em>$hlogin</em> existerar inte i forumet</p>";
	}
    }

?>
<form name="f" method="post" action="<?php echo $baseurl ?>">
<p><input name="bb" /><input type="submit" value="Import"/></p>
<p><input type="submit" name="bb_all" value="Importera hela bunten"/></p>


<?php
  if( $_POST['bb_all'] )
    {
      require_once(ADHOMESYS.'/inc/phpbb-import.php');
      import_all_users_from_phpbb();
    }
}
*/

////////////////////////////////////////////////////////////

function ad_user_profile_show(  $user )
{
  if( current_user_can('edit_users') )
    {
      include( ADHOMESYS.'/ad-user-profile-edit.php' );
    }
  else
    {
      include( ADHOMESYS.'/ad-user-profile-show.php' );
    }
}
add_action( 'show_user_profile', 'ad_user_profile_show');


////////////////////////////////////////////////////////////

function ad_user_profile_edit( $user )
{
  include( ADHOMESYS.'/ad-user-profile-edit.php' );
}
add_action( 'edit_user_profile', 'ad_user_profile_edit');

////////////////////////////////////////////////////////////

function ad_user_profile_update( $user_id )
{
  global $wpdb;

  $custom_fields = array('ad_region',
			 'ad_proficiency',
			 'ad_equipment',
			 'ad_contacts',
			 'ad_anonymity',
			 );
  foreach( $custom_fields as $i => $key )
    {
//      if( !$_POST[$key] ) continue;
      if( is_array($_POST[$key]) ) $_POST[$key] = implode(",", $_POST[$key]);
      //      update_user_meta($user_id, $key, $wpdb->prepare($_POST[$key]));
      update_user_meta($user_id, $key, $_POST[$key]);
    }

  if( !current_user_can('edit_users') )
    {
      ad_user_profile_update_notification( $user_id );
      return;
    }

  $custom_fields = array('ad_member_id',
			 'ad_member',
			 'ad_personnummer',
			 'ad_member_payed',
			 'ad_member_from',
			 'ad_member_to',
			 'ad_member_lifetime',
			 'ad_folkbokf',
			 'ad_email_invalid',
			 'ad_memadmin_notes',
			 'ad_memadmin_log',
			 'inactive',
			 );
  foreach( $custom_fields as $i => $key )
    {
//      if( !$_POST[$key] ) continue;
      if( is_array($_POST[$key]) ) $_POST[$key] = implode(",", $_POST[$key]);
//      error_log($key." = ".$_POST[$key]);
//      update_user_meta($user_id, $key, $wpdb->prepare($_POST[$key]));
      update_user_meta($user_id, $key, $_POST[$key]);
    }
}
add_action( 'profile_update', 'ad_user_profile_update');

////////////////////////////////////////////////////////////

function ad_input_label($key,$name)
{
  echo "<th><label for='$key'>", stripslashes($name), "</label></th>";
}

////////////////////////////////////////////////////////////

function ad_input_text($user,$key,$name,$desc='')
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td><input type='text' name='$key' id='$key' value='$value' class='regular-text' />";
  if( $desc )
    {
      echo "<br/><span class='description'>$desc</span>";
    }
  echo "</td>";
}

////////////////////////////////////////////////////////////

function ad_input_select($user,$key,$name,$options)
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td>";
  echo "<select name='$key' id='$key' style='width: 15em;'>";
  $custom_field_options = explode(",", $options);
  foreach( $custom_field_options as $custom_field_option )
    {
      echo "<option value=\"", stripslashes($custom_field_option), "\"";
      if( $value == stripslashes($custom_field_option) ) echo " selected='selected'";
      echo ">", stripslashes($custom_field_option), "</option>";
    }
  echo "</select>";
  echo "</td>";
}

////////////////////////////////////////////////////////////

function ad_input_checkbox($user,$key,$name,$options,$desc='')
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td>";
  $custom_field_options = explode(",", $options);
  $values = explode(",", $value);
  if( $desc )
    {
      echo " <span class='description'>$desc</span><br/>";
    }
  foreach( $custom_field_options as $custom_field_option )
    {
      echo "<label><input type='checkbox' name='$key"."[]' value=\"", stripslashes($custom_field_option), "\"";
      if( in_array(stripslashes($custom_field_option), $values) ) echo " checked='checked'";
      echo " />&nbsp;", stripslashes($custom_field_option), "</label><br />";
    }
  echo "</td>";
}

////////////////////////////////////////////////////////////

function ad_input_bool($user,$key,$name, $option)
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td>";
  echo "<label><input type='checkbox' name='$key' value=\"1\"";
  if( $value ) echo " checked='checked'";
  echo " />&nbsp;", stripslashes($option), "</label><br />";
  echo "</td>";
}

////////////////////////////////////////////////////////////

function ad_input_radio($user,$key,$name,$options)
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td>";
  $custom_field_options = explode(",", $options);
  foreach( $custom_field_options as $custom_field_option )
    {
      echo "<label><input type='radio' name='$key' value=\"", stripslashes($custom_field_option), "\"";
      if( $value == stripslashes($custom_field_option) ) echo " checked='checked'";
      echo " class='tog'>&nbsp;", stripslashes($custom_field_option), "</label><br />";
    }
  echo "</td>";
}

////////////////////////////////////////////////////////////

function ad_input_textarea($user,$key,$name,$desc='')
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td><textarea name='$key' id='$key' cols='25' rows='5'>", stripslashes($value), "</textarea>";
  if( $desc )
    {
      echo "<br/><span class='description'>$desc</span>";
    }
  echo "</td>";
}


////////////////////////////////////////////////////////////

function ad_input_date($user,$key,$name,$desc='')
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td><input type='text' name='$key' id='$key' class='datepicker' value='$value' />";
  if( $desc )
    {
      echo "<br/><span class='description'>$desc</span>";
    }
  echo "</td>";
}

////////////////////////////////////////////////////////////

function ad_input_disabled($user,$key,$name)
{
  ad_input_label($key,$name);
  $value = $user->$key;
  echo "<td><input type='text' disabled='disabled' name='$key' id='$key' value='$value' /></td>";
}

////////////////////////////////////////////////////////////

function ad_user_profile_head()
{
  wp_enqueue_script("jquery");
?>
<link type="text/css" rel="stylesheet" href="<?php echo ADHOMEURL."/js/theme/jquery.ui.all.css"; ?>" />
<script type="text/javascript" src="<?php echo ADHOMEURL."/js/jquery.ui.core.min.js"; ?>"></script>
<script type="text/javascript" src="<?php echo ADHOMEURL."/js/jquery.ui.datepicker.min.js"; ?>"></script>
<script type="text/javascript" src="<?php echo ADHOMEURL."/js/jquery.ui.datepicker-sv.js"; ?>"></script>
<script type="text/javascript">
jQuery(function()
       {
	   jQuery(".datepicker").datepicker();
       });
</script>
<?php
}
add_action( 'admin_head-profile.php', 'ad_user_profile_head');
add_action( 'admin_head-user-edit.php', 'ad_user_profile_head');

////////////////////////////////////////////////////////////

function ad_user_profile_update_notification( $user_id )
{
  $user = new WP_User($user_id);

  $user_login = stripslashes($user->user_login);
  $user_email = stripslashes($user->user_email);

  // The blogname option is escaped with esc_html on the way into the database in sanitize_option
  // we want to reverse this for the plain text arena of emails.
  $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

  $message  = sprintf('%s har uppdaterat sin profil på %s:', $user_login, $blogname) . "\r\n\r\n";
  $message .= sprintf("http://aktivdemokrati.se/wp-admin/user-edit.php?user_id=%d\r\n",$user_id);
  $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
  $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

  @wp_mail(get_option('admin_email'), sprintf(__('[%s] User profile update: %s'), $blogname, $user_login), $message);
}

////////////////////////////////////////////////////////////

function ad_personal_options($profileuser)
{
?>
<tr>
  <th scope="row">Anonymitet</th>
  <td><label for="ad_anonymity"><input type="checkbox" name="ad_anonymity" id="ad_anonymity" value="true" <?php if ( !empty($profileuser->ad_anonymity) ) checked('true', $profileuser->ad_anonymity); ?> /> Behandla telefon och postadress konfidentiellt</label></td>
</tr>
<?php
  return;
?>
<tr>
  <th scope="row">E-posta nyheter</th>
  <td><label for="ad_newsmail_level"><select name="ad_newsmail_level" id="ad_newsmail_level">
<?php
  $levels = array( 1 => 'Ibland',
		   2 => 'varje månad',
		   4 => 'varje vecka',
		   6 => 'varje dag',
		   8 => 'varje timme',
		   9 => 'varje nyhet',
		   );
  $adnml = $profileuser->ad_newsmail_level;
  if(!isset($adnml)) $adnml = 4;
  foreach($levels as $id => $label )
    {
      $selected = selected( $adnml, $id, 0);
      echo "<option value='$id'$selected>$label</option>";
    }
?>
</select></td></tr>
<tr>
  <th scope="row">Format på nyhetsbrevet</th>
  <td><label for="ad_newsmail_format"><input type="radio" name="ad_newsmail_format" value="plain" <?php if ( !empty($profileuser->ad_newsmail_format) ) checked('plain', $profileuser->ad_newsmail_format); ?> /> Text</label><br/>
<label for="ad_newsmail_format"><input type="radio" name="ad_newsmail_format" value="html" <?php if ( !empty($profileuser->ad_newsmail_format) ) checked('html', $profileuser->ad_newsmail_format); ?> /> HTML</label>
</td>
</tr>
<tr>
  <th scope="row">Prenumerera på</th>
  <td>
<?php
  $cats = array(
		'blog_all_post'     => 'Alla inlägg i bloggen',
		'blog_all_comment'  => 'Alla blogg-kommentarer',
		'forum_all_post'    => 'Alla inlägg i forumet',
		'forum_all_comment' => 'Alla forum-kommentarer',
		);
  $adnmc = explode(",", $profileuser->ad_newsmail_category);
  if(!isset($profileuser->ad_newsmail_category)) $adnmc = array('blog_all_post','forum_all_post');

  foreach( $cats as $id => $label )
    {
      echo "<label><input type='checkbox' name='ad_newsmail_category[]' value='$id'";
      if( in_array($id, $adnmc) ) echo " checked='checked'";
      echo "/> $label<br/>";
    }
  echo "</td></tr>";
}
add_action( 'personal_options', 'ad_personal_options',10,1);

////////////////////////////////////////////////////////////

?>