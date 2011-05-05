<?php
/*
Plugin Name: Fix Reversed Comments Pagination
Plugin URI: http://winkpress.com/articles/fix-reversed-comments-pagination/
Description: Fixes a problem with reversed comments pagination. See <a href="http://winkpress.com/articles/fix-reversed-comments-pagination/">this</a> for more details.
Author: WinkPress
Version: 1.0
Author URI: http://winkpress.com/
*/
function wink_good_discussion_settings()
{
  // If comments are not paginated
  if (!get_option('page_comments'))
    return false;

  // If comments are not newer to oldest
  if (get_option('comment_order') != 'desc')
    return false;

  // If last page not displayed first
  if (get_option('default_comments_page') != 'newest')
    return false;

  // Otherwise,
  return true;
}

if (wink_good_discussion_settings()):

add_action('comment_form', 'wink_comment_redirect');
add_filter('comment_post_redirect', 'wink_comment_redirect', 99, 2);

function wink_comment_redirect($location = '', $comment = null)
{
  if (!$comment)
  {
    $cpage = get_query_var('cpage');
    $link = get_comments_pagenum_link( $cpage );
    $link = str_replace('#comments', '', $link);
    echo "<input type='hidden' value='$link' name='redirect_to' />";
  }
  else
  {
    if ($comment->comment_parent == 0)
    {
      $location = get_permalink($comment->comment_post_ID);
      $location .= '#comment-' . $comment->comment_ID;
    }

    return $location;
  }
}

class Walker_Comment_Wink extends Walker_Comment
{
  /*
   This is copied from class-wp-walker.php and modified slightly.
   Thanks to jpowermacg5 from irc://irc.freenode.net/php for helping
   me figure this out!
  */
  function paged_walk( $elements, $max_depth, $page_num, $per_page ) {
    /* sanity check */
    if ( empty($elements) || $max_depth < -1 )
      return '';

    $args = array_slice( func_get_args(), 4 );
    $output = '';

    $id_field = $this->db_fields['id'];
    $parent_field = $this->db_fields['parent'];

    $count = -1;
    if ( -1 == $max_depth )
      $total_top = count( $elements );
    if ( $page_num < 1 || $per_page < 0  ) {
      // No paging
      $paging = false;
      $start = 0;
      if ( -1 == $max_depth )
        $end = $total_top;
      $this->max_pages = 1;
    } else {
      $paging = true;
      $start = ( (int)$page_num - 1 ) * (int)$per_page;
      $end   = $start + $per_page;
      if ( -1 == $max_depth )
        $this->max_pages = ceil($total_top / $per_page);
    }

    // flat display
    if ( -1 == $max_depth ) {
      if ( !empty($args[0]['reverse_top_level']) ) {
        $elements = array_reverse( $elements );

        $page_num = 1 + ceil($total_top / $per_page) - $page_num;

        $start = ($page_num - 1) * $per_page;
        $end = $start + $per_page;
      }

      $empty_array = array();
      foreach ( $elements as $e ) {
        $count++;
        if ( $count < $start )
          continue;
        if ( $count >= $end )
          break;
        $this->display_element( $e, $empty_array, 1, 0, $args, $output );
      }
      return $output;
    }

    /*
     * separate elements into two buckets: top level and children elements
     * children_elements is two dimensional array, eg.
     * children_elements[10][] contains all sub-elements whose parent is 10.
     */
    $top_level_elements = array();
    $children_elements  = array();
    foreach ( $elements as $e) {
      if ( 0 == $e->$parent_field )
        $top_level_elements[] = $e;
      else
        $children_elements[ $e->$parent_field ][] = $e;
    }

    $total_top = count( $top_level_elements );
    if ( $paging )
      $this->max_pages = ceil($total_top / $per_page);
    else
      $end = $total_top;

    if ( !empty($args[0]['reverse_top_level']) ) {
      $top_level_elements = array_reverse( $top_level_elements );

      $page_num = 1 + ceil($total_top / $per_page) - $page_num;

      $start = ($page_num - 1) * $per_page;
      $end = $start + $per_page;
    }

    if ( !empty($args[0]['reverse_children']) ) {
      foreach ( $children_elements as $parent => $children )
        $children_elements[$parent] = array_reverse( $children );
    }

    foreach ( $top_level_elements as $e ) {
      $count++;

      //for the last page, need to unset earlier children in order to keep track of orphans
      if ( $end >= $total_top && $count < $start )
          $this->unset_children( $e, $children_elements );

      if ( $count < $start )
        continue;

      if ( $count >= $end )
        break;

      $this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
    }

    if ( $end >= $total_top && count( $children_elements ) > 0 ) {
      $empty_array = array();
      foreach ( $children_elements as $orphans )
        foreach( $orphans as $op )
          $this->display_element( $op, $empty_array, 1, 0, $args, $output );
    }

    return $output;
  }
}
endif;
