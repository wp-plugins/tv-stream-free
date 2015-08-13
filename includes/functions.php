<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

class TVStream {
  function R($var,$escape=1) {
    if (isset($_POST[$var]))
      return $escape ? $this->doEscape($_POST[$var]):$this->delEscape($_POST[$var]);
    if (isset($_GET[$var]))
      return $escape ? $this->doEscape($_GET[$var]):$this->delEscape($_GET[$var]);
  }
  function P($var,$escape=1) {
    if ($escape) return $this->doEscape($_POST[$var]);
    else return $this->delEscape($_POST[$var]);
  }
  function RA($escape=1) {
    if ($escape) {
      $arr=array();
      foreach (array_merge($_GET,$_POST) as $var=>$val) {
        $arr[$var]=$this->doEscape($val);
      }
      return $arr;
    }
    else
      return array_merge($_GET,$_POST);
  }
  function RV($escape=1) {
    foreach (array_merge($_GET,$_POST) as $var=>$val) {
      global $$var;
      $$var=$this->R($var,$escape);
    }
  }
  function S($var) {
    return $_SESSION[$var];
  }
  function S_($var,$val) {
    $_SESSION[$var]=$val;
  }
  function SV($var,$escape=1) {
    return $escape ? $this->doEscape($_SERVER[$var]):$this->delEscape($_SERVER[$var]);
  }
  function GL($var,$escape=1) {
    if (ereg('\[',$var)) {
      $var=str_replace(array('"',"'"),'',$var);
      return $GLOBALS[preg_replace('/\[(.+)\]/','',$var)][preg_replace('/(.+)\[(.+)\]/','\\2',$var)];
    }
    elseif ($this->R($var)==$this->doEscape($GLOBALS[$var]))
      return $this->R($var,$escape);
    else return $GLOBALS[$var];
  }
  function GL_($var,$val) {
    $GLOBALS[$var]=$val;
  }
  function doEscape($str) {
    return get_magic_quotes_gpc() ? $str:addslashes($str);
  }
  function delEscape($str) {
    return get_magic_quotes_gpc() ? stripslashes($str):$str;
  }
function mysqlQuery($query, $pm=false) {
  global $wpdb;
  if ($pm!==false)
    $query=$wpdb->prepare($query,$pm);
  $res=$wpdb->query($query);
  $this->mysql_err=mysql_error();
  $this->mysqlError("mysqlQuery error: $query");
  return $res;
}
function mysqlRows($query, $pm=false) {
  global $wpdb;
  if ($pm!==false)
    $query=$wpdb->prepare($query,$pm);
  $res=$wpdb->get_results($query, ARRAY_N);
  $this->num_rows=$wpdb->num_rows;
  $this->mysql_err=mysql_error();
  $this->mysqlError("mysqlRows error: $query");
  return $res;
}
function mysqlRow($query, $pm=false) {
  global $wpdb;
  if ($pm!==false)
    $query=$wpdb->prepare($query,$pm);
  $res=$wpdb->get_row($query, ARRAY_N);
  $this->mysql_err=mysql_error();
  $this->mysqlError("mysqlRow error: $query");
  return $res;
}
  function mysqlError($msg) {
    if (!$this->mysql_err)
      return;
    $str_file=TV_STREAM_DIR.'errors_mysql.log';
    if (is_file($str_file))
      $contf=substr(file_get_contents($str_file),0,30000);
    $msg="\r\n\r\n -  -  -  -  -  -  - ".date("Y.m.d H:i:s")." -  -  -  -  -  -  - \r\n$msg\r\n";
    $msg.=$this->mysql_err;
    $msg.=", in file ".$this->SV('PHP_SELF');
    $handle=fopen($str_file, 'w');
    fwrite($handle, $msg.$contf);
    fclose($handle);
  }
  function varsQuery($vars='',$only=0) {
    $query=trim($this->SV('QUERY_STRING'),' ?&');
    if (!$vars || !$query) return $query;

    if (preg_match_all("/(\w+)=([^&]*)/",$query,$arr)) {
      $query='';
      $i=0;
      while($arr[1][$i]) {
        if ($only && ereg("&".$arr[1][$i]."&","&$vars&")) $query.=$arr[1][$i].'='.$arr[2][$i].'&';
        elseif (!$only && !ereg("&".$arr[1][$i]."&","&$vars&")) $query.=$arr[1][$i].'='.$arr[2][$i].'&';
        $i++;
      }
    }
    return rtrim($query,'&');
  }
  function getIp() {
    if ($this->SV('HTTP_X_FORWARDED_FOR'))
      $ip = $this->SV('HTTP_X_FORWARDED_FOR');
    elseif ($this->SV('HTTP_CLIENT_IP'))
      $ip = $this->SV('HTTP_CLIENT_IP');
    else
      $ip = $this->SV('REMOTE_ADDR');
    return preg_replace("/,.*/","",$ip);
  }
  function getDirUpload() {
    $option = defined('UPLOADS') ? UPLOADS : get_option('upload_path');
    $option = $option ? $option : WP_CONTENT_DIR.'/uploads';
    return str_replace("\\","/",str_replace(realpath(ABSPATH),'',realpath($option)));
  }
  function readDr($dir = "./",$base_path='./',$mp='') {
    if($listing = opendir($dir)){
      $return = array ();
      while(($entry = readdir($listing)) !== false) {
        if ($entry != "." && $entry != ".." && substr($entry,0,1) != '.') {
          $dir = preg_replace("/^(.*)(\/)+$/", "$1", $dir);
          $item = $dir . "/" . $entry;
          $isfile = is_file($item);
          $dirend = ($isfile)?'':'/';

          $path_to_file = $dir . "/" . $entry . $dirend;
          $path_to_file = str_replace($mp, $base_path, $path_to_file);

          $ext=preg_replace("/.+\./","",$entry);
          $link = '<a rel="'.$ext.'" href="'.$path_to_file.'">'.$entry.'</a>';
          if ($isfile && strpos('.flv',$ext)) {
            $return[] = $link;
          }
          elseif (is_dir($item)) {
            $return[$link] = $this->readDr($item,$base_path,$mp);
          }
        }
      }
      return $return;
    }
    else {
      die('Can\'t read directory.');
    }
  }
  function writeLi($array) {
    $return = "<ul>";
    if (is_array($array) && count($array) > 0) {
      foreach ($array as $k => $v) {
        if (is_array($v) && count($v) > 0) {
          $return .= "<li><span class=\"folder\">".$k."</span>". $this->writeLi($v) . "</li>";
          } else if(count($v)>0){
            $return .= "<li><span class=\"file\">".$v."</span></li>";
          }
        }
      }
      $return .= "</ul>";
      return $return;
    }
    function writeOption($arr, $value) {
      foreach ($arr as $var=>$val) {
        if ($arr[0])
          $var=$val;
        if ($var==$value) $sel='selected';
        else $sel='';
        $res.="<option value=\"$var\" $sel>$val</option>\r\n";
      }
      return $res;
    }
    function newDate($str,$tm) {
      return date($str,$tm+(get_option('tv_stream_utc_diff')*60));
    }
    function cryptStr($str) {
      $str_chrs="benau.d/_tv5zo1gqks4l08m29r6hy-jf3w:7ixcp";
      $crypt_chrs="oe_2rajhncty/:-dv6sb8lqfk7xzgm40.w951upi3";
      $arr=str_split($enc);
      $arr_chrs=str_split($str_chrs);
      $arr_crypt=str_split($crypt_chrs);

      $i=0;
      while (strlen(substr($str,$i,1))) {
        $str_chr=substr($str,$i,1);
        $chr_pos=array_search($str_chr,$arr_chrs);
        if ($chr_pos !== false) $res.=$arr_crypt[$chr_pos];
        else $res.=$str_chr;
        $i++;
      }
      return $res;
    }
    function checkSv($url) {
      $ch=curl_init($url);

      curl_setopt($ch,  CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

      $result=curl_exec($ch);
      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      if ($http_code >= 200 && $http_code < 300)
        return true;
      else
        return false;
    }
  }
  ?>