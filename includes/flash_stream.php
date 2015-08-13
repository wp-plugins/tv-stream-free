<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

error_reporting(0);


$sep_key=',';
$sep_key_=':';
if ($tv_stream->P('action')=="getMeta".$tv_stream_license) {
  if (get_option('tv_stream_safe_db') && !current_user_can('manage_options'))
      exit();
  header("Content-Type: text/plain");


  extract($tv_stream->RA());
  $flv=str_replace(WP_SITEURL.$tv_stream->getDirUpload().'/','',$flv);

  if ($seekpoints) {
    preg_match_all("/($sep_key\d+$sep_key_\d+)/",$seekpoints,$mm);
    $keyframes=implode('',array_reverse($mm[1]));
    $keyframes=ltrim($keyframes,$sep_key);
  }
  else {
    $arr_times=split(',',$times);
    $arr_filepos=split(',',$filepositions);
    $keyframes='';
    $i=0;
    while(isset($arr_times[$i])) {
      $time=round($arr_times[$i]);
      if (!$arr_keyframes[$time]) {
        $arr_keyframes[$time]=1;
        $keyframes.=$sep_key.$time.$sep_key_.$arr_filepos[$i];
      }
      $i++;
    }
    $keyframes=ltrim($keyframes,$sep_key);
  }

  $add_duration=2;          // add time correct to finish to can load next events from start
  if ($duration) {
    $query="INSERT INTO ".$wpdb->prefix."tvstream_schedule SET flv='%s', width='%d', height='%d', duration='%d', keyframes='$keyframes'";
    if (!$tv_stream->mysqlQuery($query, array($flv,$width,$height,round($duration+$add_duration))))
      $error='error=err: '.$tv_stream->mysql_err;
  }
  else
    $error='error=err: cannot find duration';

  echo "&loadphp=1&$error";

}
elseif ($tv_stream->P('action')=="getInfo".$tv_stream_license) {
  header("Content-Type: text/plain");

  $utc_diff=get_option('tv_stream_utc_diff');
  $min_end=2;          // ignore events which have end in less secconds than $min_end
  $max_msg=2;          // no message to events which start in less secconds than $max_msg
  $now=time();
  $query="SELECT time_start, flv, width, height, title FROM ".$wpdb->prefix."tvstream_schedule WHERE time_end>".($now+$min_end)." ORDER BY time_start LIMIT 1";
  $row=$tv_stream->mysqlRow($query);
  if (is_array($row)) {

    $upload_dir = realpath(ABSPATH.$tv_stream->getDirUpload());

    if (is_readable("$upload_dir/$row[1]")) {
      if ($row[0]<=$now) {
        $info="time_start=0&width=$row[2]&height=$row[3]";
      }
      elseif ($row[0]-$now<=$max_msg) {
        $info="time_start=".($row[0]-$now)."&width=$row[2]&height=$row[3]";
      }
      else {
        $gmt=$utc_diff/60;
        if (abs($gmt-round($gmt))>0.4) {
          if ($gmt>0)
            $gmt=floor($gmt)+0.5;
          else
            $gmt=floor($gmt)+1.5;
          $gmt=floor($gmt).':'.(60*($gmt-floor($gmt)));
        }
        else
          $gmt=round($gmt);
        if ($gmt==0)
          $gmt='';
        elseif ($gmt>0)
          $gmt=urlencode("+").$gmt;
        $left=round(($row[0]-$now)/60);
        if ($left>60) {
          $left_mn=$left%60;
          $left_mn= $left_mn>9 ? $left_mn : "0$left_mn";
          $left_txt=floor($left/60).":$left_mn hours left";
        }
        else
          $left_txt=$left==1 ? "$left minute left" : "$left minutes left";

        $info="time_start=".($row[0]-$now)."&width=$row[2]&height=$row[3]&msg=Next live event: $row[4]\n at ".date("m/d H:i:s", $row[0]+$utc_diff*60)." GMT$gmt, $left_txt";
      }
    }
    else
      $info='msg=err: stream file don\'t exist';

    $info.="&title=$row[4]";
  }
  else {
    $info='msg=no live event programmed';
  }

  echo "&loadphp=1&$info";
}
elseif ($tv_stream->R('action')=="doStream".$tv_stream_license) {

  $max_start=1;          // start events which have future start in less secconds than $max_start
  $now=time();
  $query="SELECT time_start, keyframes, flv, duration FROM ".$wpdb->prefix."tvstream_schedule WHERE time_start<=".($now+$max_start)." AND time_end>$now LIMIT 1";
  $row=$tv_stream->mysqlRow($query);
  if (!is_array($row)) {
    exit();
  }

  $last=0;
  $ftime=$now-$row[0];
  preg_match_all("/$sep_key(\d+)$sep_key_/",$sep_key.$row[1],$mm);
  foreach ($mm[1] as $time) {
    if ($time>$ftime) {
      $fseek=preg_replace("/.*$sep_key$last$sep_key_(\d+)$sep_key.*/","$1",$sep_key.$row[1].$sep_key);
      $fseek=is_numeric($fseek) ? $fseek:0;
      break;
    }
    $last=$time;
  }
  if (!$last)
    $fseek=0;

  $upload_dir = realpath(ABSPATH.$tv_stream->getDirUpload());

  $flv = realpath("$upload_dir/$row[2]");
  $fname = basename($row[2]);
  $fsize = filesize($flv);
  $bitrate=round($fsize/($row[3]*1024));
  $bitrate+=get_option('tv_stream_php_buffer');

  $bitr_min=60;
  $intv_max=0.6;

  $bitr_max=300;
  $intv_min=0.3;

  if ($bitrate<=$bitr_min) {
    $packet_interval=$intv_max;
  }
  elseif ($bitrate>=$bitr_max) {
    $packet_interval=$intv_min;
  }
  else {
    $packet_interval=$intv_max-(($intv_max-$intv_min)*($bitrate-$bitr_min)/($bitr_max-$bitr_min));
    $packet_interval=round($packet_interval*1000)/1000;
  }
  $packet_size=round($bitrate*$packet_interval)*1024;

  $fsize-=($fseek > 0) ? $fseek  + 1 : 0;

  set_time_limit(30+$fsize/($bitrate*1024));


  session_cache_limiter("nocache");
  header("Expires: Thu, 19 Nov 1981 08:52:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
  header("Pragma: no-cache");

  // Flash Video Header
  if (strpos($fname,'.flv'))
    header("Content-Type: video/x-flv");
  else
    header("Content-Type: video/mp4");   ////  video/x-mp4
  header("Content-Disposition: attachment; filename=\"".$fname."\"");
  header("Content-Length: ".$fsize);

  $fpr = fopen($flv, 'rb');

  ob_end_clean();

  if($fseek != 0) {
    echo 'FLV'.pack('C', 1).pack('C', 1).pack('N', 9).pack('N', 9);
  }

  // Seek to the file requested start
  fseek($fpr, $fseek);
  $first_bits=true;

  while(!feof($fpr) && connection_status() == 0) {
    // Bandwidth limiting
    if ($first_bits) {
      echo fread($fpr, $packet_size*2);
      $first_bits=false;
    }
    else
      echo fread($fpr, $packet_size);
    flush();
    usleep($packet_interval*1000000);
  }
  fclose($fpr);

}
elseif (isset($_GET[$tv_stream_license])) {
  echo 777;
}
else
  header('HTTP/1.0 404 Not Found');

?>