<?php

////////////////////////////////////////////////////////////


function ad_section()
{
  global $ad_section_post;
  if( isset($ad_section_post) )
    return $ad_section_post;

  $locations = get_nav_menu_locations();
  $menu = wp_get_nav_menu_object( $locations[ 'primary' ] );
  $menu_items = wp_get_nav_menu_items( $menu->term_id );

  global $wp_query;
  $queried_object = $wp_query->get_queried_object();
  $queried_object_id = (int) $wp_query->queried_object_id;

    if( $queried_object_id == 0 )
      return; // Query not yet initiated

  $menu_item = '';
  //ad_log("QUERIED ITEM ". $queried_object_id);
  global $wp;
  $category_slug = $wp->query_vars['catname'];
  if(! isset($category_slug) )
    {
      // queried object should be the current post...
      $category = get_the_category();
      $category_slug = $category[0]->category_nicename;
    }
  //ad_log("CATEGORY ". $category_slug);

  $cid = get_the_category( $queried_object_id );
  if( $cid[0] )
    {
      $queried_object = $cid[0];
      $queried_object_id = $queried_object->term_id;
    }

  foreach ( $menu_items as $e) 
    {
      if( preg_match('#/senaste/([^/]+)/#', $e->url, $matches) )
	{
	  $item_category = get_category_by_slug($matches[1]);
	  //ad_log("SENASTE ".$matches[1]);
	  //ad_log("  QOI ".$queried_object_id);
	  //ad_log("  TERM ".$item_category->term_id);
	  if( $item_category->term_id == $queried_object_id )
	    $menu_item = $e;
	}
      elseif( preg_match('#/lista/([^/]+)#', $e->url, $matches) )
	{
	  if( $category_slug == $matches[1] )
	    $menu_item = $e;
	}

      $type = $e->type;
      if( $e->object_id == $queried_object_id &&
	  $e->object == $queried_object->$type )
	$menu_item = $e;
    }

  $menu_section = $menu_item;
  //ad_log("  SECTION ".$menu_section->object_id);

  while( $parent = $menu_section->menu_item_parent )
    {
      $menu_section = wp_setup_nav_menu_item(get_post($parent));
      //ad_log("  SECTION UP ".$menu_section->object_id);
    }

  if( $obj_id = $menu_section->object_id )
    {
      $ad_section_post = get_post($obj_id);
      //ad_log("  SECTION POST ".$obj_id);
    }
  else
    {
      $ad_section_post = '';
    }

  return $ad_section_post;
}

////////////////////////////////////////////////////////////

function ad_get_nav_menu_items($args)
{
  global $ad_section_post;
  if(! $ad_section_post )
    return $args;
  $ad_section_post_id = $ad_section_post->ID;

  //ad_log("LOOKING FOR ".$ad_section_post_id);

  $section_items = array();
  foreach($args as $item)
    {
      $menu_section = $item;
      while( $parent = $menu_section->menu_item_parent )
	{
	  $menu_section = wp_setup_nav_menu_item(get_post($parent));
	}

      //ad_log( "FOUND ".$menu_section->object_id);

      if( $menu_section->object_id == $ad_section_post_id )
	{
	  array_push( $section_items, $item );
	}
    }
  return $section_items;
}

////////////////////////////////////////////////////////////

function in_section($section2)
{
  $ad_section = ad_section();
  if( !$ad_section )
    return;

  //ad_log("Is this $section2?");
  $section = ad_section()->post_name;
  //ad_log("This section is $section");
  return( $section == $section2);
}

////////////////////////////////////////////////////////////

function is_section()
{
  /*
  if( ad_section() )
    {
      ad_log("Yes, this is ad_section ".ad_section()->post_name);
    }
  else
    {
      ad_log("No");
    }
  */

  return ad_section() ? TRUE : FALSE;
}


////////////////////////////////////////////////////////////

?>