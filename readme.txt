=== Fix Reversed Comments Pagination ===
Contributors: msafi
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7663346
Tags: comments, pagination
Requires at least: 3.1
Tested up to: 3.1.1
Stable tag: trunk

Fixes WordPress reversed comments pagination issue where it doesn't fill the default page with comments.

== Description ==
### What Problem Does This Plugin Fix? ###

If you have 21 comments on a certain post, and your WordPress discussion settings are like [this](http://winkpress.com/wp-content/uploads/2011/04/discussion-settings.png):

1. Comments are paginated
2. Last page displayed by default
3. Newer comments displayed at the top of each page

Then here's how WordPress will display the comments:

Default page will show only one comment, #21

The second page will show comments 20 to 1.

What WordPress should do instead is display comments 21 to 2 on the default page, and 1 on the second page. And that's what this plugin does.

More details [here](http://winkpress.com/articles/fix-reversed-comments-pagination/).

### Installation ###

After you upload and activate, you must make a minor modification to your theme.

Look for `wp_list_comments` in your theme. It's probably in `comments.php`.

Before you call `wp_list_comments`, you must create a new PHP class to display the comments. Then add it to the `wp_list_comments` call -- like this:

`
if (class_exists('Walker_Comment_Wink'))
  $walker = new Walker_Comment_Wink();
else
  $walker = '';
 
wp_list_comments(array('walker' => $walker));
`

Note that the plugin will only take effect if your comments display settings are exactly like the red circles in [this screenshot](http://winkpress.com/wp-content/uploads/2011/04/discussion-settings.png). If your settings are different, the plugin won't interfere with how your comments are displayed.

More details [here](http://winkpress.com/articles/fix-reversed-comments-pagination/).

### One Thing to Consider ###

With this implementation of pagination, some times comments move from one page to another. So, in some cases, a permalink that leads to one particular comment might no longer work. For example, if you have a comment that appears on page one, and another blogger links to that comment directly:

`http://example.com/controversial-post/comment-page-1/#comment-134`

The comment could after awhile move to page 2 of comments and the link will no longer lead to the right comment.

### Bug Reports & Issues ###

I'm using this plugin on my site and have tested it else where. It works great. But your setup might be different and you may notice things I missed. If that's the case, tell me in the [comments of the plugin's page](http://winkpress.com/articles/fix-reversed-comments-pagination/).

Developed by [WinkPress](http://winkpress.com/).
== Changelog ==

= 0.5 =
* Initial release