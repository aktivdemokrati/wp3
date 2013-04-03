<?php
/*
Controller name: GOV
Controller description: GOV communication methods
*/

class JSON_API_GOV_Controller
{
  public function get_user()
  {
    if(! gov_client_authorized() ) exit;
    $uid = $_GET['id'];
    if(!$uid > 0) exit;

    global $wpdb;
    $user = $wpdb->get_row($wpdb->prepare("
      SELECT ID,user_login,user_email,display_name from $wpdb->users
      WHERE ID=%s", $uid));

    return array(
		 'origin' => $_SERVER['REMOTE_ADDR'],
		 'user' => $user,
		 );
  }  

  public function users_by_meta()
  {
    if(! gov_client_authorized() ) exit;

    $meta_true = $_GET['true'];

    global $wpdb;
    $users = $wpdb->get_results($wpdb->prepare("
      SELECT ID, user_login,user_email,display_name
      FROM $wpdb->users AS u,
           $wpdb->usermeta AS m
      WHERE m.user_id = u.ID
        AND m.meta_key = '%s'
        AND m.meta_value IS TRUE
    ", $meta_true));

    return array(
		 'origin' => $_SERVER['REMOTE_ADDR'],
		 'count' => count($users),
		 'users' => $users,
		 );
  } 

}

function get_author_by_id($id) {
    $id = get_the_author_meta('ID', $id);
    if (!$id) {
      return null;
    }
    return new JSON_API_Author($id);
  }


function gov_client_authorized()
{
  if( $_SERVER['REMOTE_ADDR'] == GOV_SERVER_IP )
    {
      return 1;
    }

  echo '{"status":"denied"}';
  return 0;
}



?>
