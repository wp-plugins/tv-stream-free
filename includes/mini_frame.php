<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/


if (!current_user_can('manage_options'))
    exit();

$action=$tv_stream->R('action');
$confirm=$tv_stream->R('confirm');
$id=$tv_stream->R('id');

  if ($action=="del") {
    if ($confirm=="yes") {
      $query="DELETE FROM ".$wpdb->prefix."tvstream_schedule WHERE id='%d'";
      if ($tv_stream->mysqlQuery($query, array($id))) {
        $message="Delete from ".$wpdb->prefix."tvstream_schedule was made with success!";
      }
      else {
        $message="Can't delete from ".$wpdb->prefix."tvstream_schedule!";
      }
    }
    else
      $message="Confirm <a href=\"?confirm=yes&".$tv_stream->varsQuery()."\">here</a> if you really want to delete this data!";

    echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"".TV_STREAM_URL."style.css\">
    <div class=\"msg_mini\">$message<div>";
  }

?>