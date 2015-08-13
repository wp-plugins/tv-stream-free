<?php
/*
Plugin Name: TV Stream Free
Plugin URI:
Download:
Description: <strong>TV Stream Free</strong>. TV video playing based on php pseudo streaming.
Version: 1.0.0
Author: SaschArt
Author URI: http://www.saschart.com

Copyright (c) 2015 SaschArt

SaschArt hereby gives you a non-exclusive license to use the Plugin ONLY to WordPress platform.

This program is free software; you can redistribute it and/or modify
it under the limitations and the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

$tv_stream_license='';

if (!defined('WP_SITEURL')) define('WP_SITEURL', rtrim(get_option("siteurl"),'\\\/ '));
define('TV_STREAM_URL', plugin_dir_url(__FILE__));
define('TV_STREAM_DIR', dirname(__FILE__).'/');
define('TV_STREAM_NAME', 'TV Stream');


require_once TV_STREAM_DIR.'includes/functions.php';
$tv_stream=new TVStream();
require_once TV_STREAM_DIR.'includes/admin.php';
require_once TV_STREAM_DIR.'includes/install.php';
register_activation_hook(__FILE__,'installTVStream');

if (get_option('tv_stream_safe_db')) {
  function includeTVPages() {
    global $wpdb;
    if (isset($_GET['tv_stream_flash_stream'])) {
      global $tv_stream, $tv_stream_license;
      require_once TV_STREAM_DIR.'includes/flash_stream.php';
      exit;
    }
    elseif (isset($_GET['tv_stream_mini_frame'])) {
      global $tv_stream;
      require_once TV_STREAM_DIR.'includes/mini_frame.php';
      exit;
    }
  }
  add_action('plugins_loaded', 'includeTVPages');
}
else {
  if (isset($_GET['tv_stream_flash_stream']))
    require_once TV_STREAM_DIR.'includes/flash_stream.php';
  elseif (isset($_GET['tv_stream_mini_frame']))
    require_once TV_STREAM_DIR.'includes/mini_frame.php';
  exit;
}
function enqueueTVPages($hook) {
  if (strpos($hook,'tv_stream_show')) {
    wp_enqueue_script('tv_stream_script', TV_STREAM_URL.'media_tree/treeview.js');
    wp_enqueue_script('tv_stream_script1', TV_STREAM_URL.'flash/swfobject.js');
    wp_enqueue_style('tv_stream_style', TV_STREAM_URL.'media_tree/treeview.css');
    wp_enqueue_style('tv_stream_style1', TV_STREAM_URL.'style.css');
  }
}
add_action('admin_enqueue_scripts', 'enqueueTVPages');

function tvStream($attr) {
  global $tv_stream;

  $res="<div id=\"tv_stream\"></div>
  <script type=\"text/javascript\" src=\"".TV_STREAM_URL."flash/swfobject.js\"></script>
  <script type=\"text/javascript\">
    var flashvars = {};
    var params = {
      allowfullscreen: 'true',
      allowscriptaccess: 'always'
    };
    ratio = window.devicePixelRatio || 1;
    w_screen = screen.width * ratio;
    if (w_screen<640) {
      w_stream = w_screen;
      h_stream = 360/640 * w_stream;
    }
    else {
      w_stream = 640;
      h_stream = 360;
    }
    swfobject.embedSWF('".TV_STREAM_URL."flash/tv_stream.swf?sv=".$tv_stream->cryptStr(WP_SITEURL)."', 'tv_stream', w_stream, h_stream, '9.0.0', 'NULL', flashvars, params);
  </script>";

  return $res;
}

add_shortcode('tv-stream', 'tvStream');
?>