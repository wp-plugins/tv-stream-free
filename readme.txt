=== TV Stream Free ===
Contributors: SaschArt
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=GAW2U83KESVUQ&lc=RO&item_name=SaschArt%20Software&currency_code=EUR&bn=PP%2dDonationsBF%3abut_donate%2egif%3aNonHosted
Tags: TV Stream, php streaming, pseudo streaming, live, player, flv, flv live
Requires at least: 4.2
Tested up to: 4.2.2
Stable tag: 1.0.0
License: GPLv2


== Description ==

<strong>TV Stream Free - SaschArt Plugin</strong> plugin for video streaming directly from your Wordpress. Does not require additional video streaming server. You can have now your own online TV with very little charges.

<strong>Features:</strong>
<li>Schedule page in admin area: you can schedule media when do you want from uploaded files</li>
<li>Easy scheduling to the next live event</li>
<li>Schedule list view with live now event</li>
<li>Edit/delete schedule list events</li>
<li>Options with time correct calculation and php buffer to optimize the php time process with media file download</li>
<li>Smart keyframes reader</li>
<li>Shortcode to implement video player where do you want</li>
<li>Autostart to next event</li>
<li>Cascade live events</li>

= Website =
http://soft.saschart.com

== How to use ==

1. Upload flv media with keyframes to each second to can seek the file well - not yet ready mp4
2. Insert [tv-stream] shortcode in any page to insert player
3. In the programing admin page your time must show, if not, correct adjust the value of $tv_stream_utc from tv_stream.php until is right, WP broke the time values sometimes.
4. Make programation from admin Tv Stream page and check if are online. In the programing admin page if is inserted available events, the script will auto set last end time to start time for next event to add. The script autodelete expired events.

== Screenshots ==

1. Add live event with browser from media uploads
2. Show live events list with "live now" event
3. Screenshot from video player with pending event
4. Screenshot from video player with live event


== Errors causes ==

err: no keyframes
- add keyframes to your video, files with no keyframes cannot be seekable

err: fail get php gate
- no crossdomain.xml or wrong configuration
- no php file or wrong encrypt

err: fail get video info
- errors in php gate
- php queries in database errors












