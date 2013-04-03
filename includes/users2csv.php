<?php
/*
Plugin Name: Users to CSV
Plugin URI: http://yoast.com/wordpress/users-to-csv/
Description: This plugin adds an administration screen which allows you to dump your users and/or unique commenters to a csv file.<br/> Built with code borrowed from <a href="http://www.mt-soft.com.ar/2007/06/19/csv-dump/">IAM CSV dump</a>.
Author: Joost de Valk
Version: 1.4.5
Author URI: http://yoast.com/

Copyright 2008-2010 Joost de Valk (email: joost@yoast.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

function _valToCsvHelper($val, $separator, $trimFunction)
{
  if ($trimFunction) $val = $trimFunction($val);
  //If there is a separator (;) or a quote (") or a linebreak in the string, we need to quote it.
  $needQuote = FALSE;
  do {
    if( strpos($val, '"') !== FALSE)
      {
	$val = str_replace('"', '""', $val);
	$needQuote = TRUE;
	break;
      }

    if( strpos($val, $separator) !== FALSE)
      {
	$needQuote = TRUE;
	break;
      }

    if((strpos($val, "\n") !== FALSE) || (strpos($val, "\r") !== FALSE)) // \r is for mac
      {
	$needQuote = TRUE;
	break;
      }
  } while (FALSE);

  if ($needQuote)
    {
      $val = '"' . $val . '"';
    }
  return $val;
}


function arrayToCsvString($array, $separator=';', $trim='both', $removeEmptyLines=TRUE)
{
  if (!is_array($array) || empty($array)) return '';
  switch ($trim)
    {
    case 'none':
      $trimFunction = FALSE;
      break;
    case 'left':
      $trimFunction = 'ltrim';
      break;
    case 'right':
      $trimFunction = 'rtrim';
      break;
    default: //'both':
      $trimFunction = 'trim';
      break;
    }

  $ret = array();
  reset($array);
  if( is_array(current($array)))
    {
      while( list(,$lineArr) = each($array))
	{
	  if( !is_array($lineArr))
	    {
	      //Could issue a warning ...
	      $ret[] = array();
	    }
	  else
	    {
	      $subArr = array();
	      while( list(,$val) = each($lineArr))
		{
		  $val      = _valToCsvHelper($val, $separator, $trimFunction);
		  $subArr[] = $val;
		}
	    }
	  $ret[] = join($separator, $subArr);
	}
      $crlf = _define_newline();
      return join($crlf, $ret);
    }
  else 
    {
      while( list(,$val) = each($array))
      {
	$val   = _valToCsvHelper($val, $separator, $trimFunction);
	$ret[] = $val;
      }
      return join($separator, $ret);
    }
}

function _define_newline()
{
  $unewline = "\r\n";
  if( strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win'))
    {
      $unewline = "\r\n";
    }
  else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac'))
    {
      $unewline = "\r";
    }
  else
    {
      $unewline = "\n";
    }
  return $unewline;
}

function _get_browser_type()
{
  $USER_BROWSER_AGENT="";

  if (ereg('OPERA(/| )([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version))
    {
      $USER_BROWSER_AGENT='OPERA';
    }
  else if (ereg('MSIE ([0-9].[0-9]{1,2})',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version))
    {
      $USER_BROWSER_AGENT='IE';
    }
  else if (ereg('OMNIWEB/([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version))
    {
      $USER_BROWSER_AGENT='OMNIWEB';
    }
  else if (ereg('MOZILLA/([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version))
    {
      $USER_BROWSER_AGENT='MOZILLA';
    }
  else if (ereg('KONQUEROR/([0-9].[0-9]{1,2})', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version))
    {
      $USER_BROWSER_AGENT='KONQUEROR';
    }
  else
    {
      $USER_BROWSER_AGENT='OTHER';
    }
	
  return $USER_BROWSER_AGENT;
}

function _get_mime_type()
{
  $USER_BROWSER_AGENT= _get_browser_type();
  
  $mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
    ? 'application/octetstream'
    : 'application/octet-stream';
  return $mime_type;
}

function createcsv()
{
  global $wpdb;
  $sep = ';';

  $mfields = array('ad_member','ad_anonymity','first_name','last_name','nickname','ad_email_extra','ad_email_invalid','ad_folkbokf','ad_member_id','ad_member_payed','ad_member_from','ad_member_to','ad_member_lifetime','ad_personnummer','ad_region','loc_address','loc_co','loc_zip','loc_city','jabber','msn','twitter','phone_home','phone_mobile','phone_work','ad_proficiency','ad_equipment','ad_contacts');

  // Get the columns and create the first row of the CSV
  $fields = array_merge( array('uid','email','url','username','registred'), $mfields);
  $csv = arrayToCsvString($fields, $sep);
  $csv .= _define_newline();

  // Query the entire contents from the Users table and put it into the CSV
  $query = "SELECT ID as UID, user_email, user_url, user_nicename, user_registered FROM $wpdb->users";
  $results = $wpdb->get_results($query,ARRAY_A);
  $i=0;

  for( $i=0;  $i<count($results); $i++)
    {
      $query = "SELECT meta_value FROM ".$wpdb->prefix."usermeta WHERE user_id = ".$results[$i]['UID']." AND meta_key = ";

      foreach( $mfields as $mfield )
	{
	  $fnquery = $query . "'$mfield'";
	  $results[$i][$mfield] = $wpdb->get_var($fnquery);
	}
    }
  $csv .= arrayToCsvString($results, $sep);
  $csv .= _define_newline();

  $now = gmdate('D, d M Y H:i:s') . ' GMT';

  header('Content-Type: ' . _get_mime_type());
  header('Expires: ' . $now);

  $now_sv = gmdate('Y-m-d');

  header('Content-Disposition: attachment; filename="ad-users-'.$now_sv.'.csv"');
  header('Pragma: no-cache');

  echo $csv;
}

$baseurl = admin_url( 'users.php?page=AD-members-export' );
?>
