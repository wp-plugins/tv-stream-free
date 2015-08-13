<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

if (!defined('WP_UNINSTALL_PLUGIN')) exit ();

$arr_sql=array(
  "DROP TABLE IF EXISTS ".$wpdb->prefix."tvstream_schedule"
);

global $wpdb;
foreach ($arr_sql as $sql)
  $wpdb->query($sql);

?>