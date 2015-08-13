<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

function installTVStream() {
  global $wpdb;

  $version='1.0.0';
  $get_version=get_option('tv_stream_version');

  if (isset($get_version)) {
    update_option('tv_stream_version', $version);
  }
  else
    add_option('tv_stream_version', $version);

  //add default values of options
  add_option('tv_stream_php_buffer', 20);
  add_option('tv_stream_safe_db', 1);

  $arr_sql=array(
    "CREATE TABLE ".$wpdb->prefix."tvstream_schedule (
      id int(11) NOT NULL AUTO_INCREMENT,
      id_parent int(11) NOT NULL DEFAULT '0',
      title varchar(50) NOT NULL DEFAULT '',
      flv varchar(100) NOT NULL DEFAULT '',
      time_start int(10) NOT NULL DEFAULT '0',
      duration int(5) NOT NULL DEFAULT '0',
      time_end int(10) NOT NULL DEFAULT '0',
      width int(4) NOT NULL DEFAULT '0',
      height int(4) NOT NULL DEFAULT '0',
      keyframes text NOT NULL DEFAULT '',
      PRIMARY KEY (id)
    ) ENGINE=MyISAM;"
  );


    foreach ($arr_sql as $sql)
      $wpdb->query($sql);
}
?>