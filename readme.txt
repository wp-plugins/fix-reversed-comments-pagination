=== Fix Reversed Comments Pagination ===
Contributors: msafi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7663346
Tags: comments, pagination
Requires at least: 3.1
Tested up to: 3.1.2
Stable tag: trunk

Fixes WordPress reversed comments pagination issue where it doesn't fill the default page with comments.

== Description ==
### What Problem Does This Plugin Fix? ###

If you have 21 comments on a certain post, and your WordPress discussion settings are:

1. Comments are paginated
2. 20 comments per page
3. Last page displayed by default
4. Newer comments displayed at the top of each page

Then the default page will show only one comment, #21. The second page will show comments 20 to 1.

What WordPress should do instead is display comments 21 to 2 on the default page, and 1 on the second page. With the help of this plugin, WordPress will do that.

### Installation ###

After you upload and activate, you must make a minor modification to your theme.

In `[wp_list_comments](http://codex.wordpress.org/Function_Reference/wp_list_comments)` of your theme, pass Walker_Comment_Wink class as the "walker" -- like this:

`
if (class_exists('Walker_Comment_Wink'))
  $walker = new Walker_Comment_Wink();
else
  $walker = '';
 
wp_list_comments(array('walker' => $walker));
`

### Bug Reports & Issues ###

I'm using this plugin on my site and have tested it else where. It works great. But your setup might be different and you may notice things I missed. If that's the case, tell me in the [comments of the plugin's page](http://winkpress.com/articles/fix-reversed-comments-pagination/).

Developed by [WinkPress](http://winkpress.com/).
== Changelog ==

= 1.0 =
* The plugin has been used and tested by several people with no problems, so it graduates to 1.0
* Improved readme.txt

= 0.5 =
* Initial release