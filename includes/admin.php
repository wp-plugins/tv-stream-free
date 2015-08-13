<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

add_action('admin_menu', 'adminMenuTVs');

function adminMenuTVs() {
  add_menu_page('TV Stream - SaschArt', 'TV Stream', 'manage_options', 'tv_stream_show', 'TVStreamShow', TV_STREAM_URL.'images/icon.ico', '25.77777771');
}
function TVStreamShow() {
  global $tv_stream,$wpdb;
  if (!current_user_can('manage_options')) {
    wp_die( __( 'You do not have sufficient permissions to access this page.'));
  }

  $utc_diff=get_option('tv_stream_utc_diff');
  extract($tv_stream->RA());
  if (!$action) $action="show";

  $url_imgs=TV_STREAM_URL.'images';
  $g_n='&nbsp;';

  $content="<br><table>
  <tr>
    <td class=\"button\"><a href=\"?".$tv_stream->varsQuery("page",1)."&action=add\"><img src=\"$url_imgs/add.gif\" border=0 alt=\"".TV_STREAM_NAME."\"></a>$g_n<a href=\"?".$tv_stream->varsQuery("page",1)."&action=add\">Add live event</a></td>
    <td class=\"button\"><a href=\"?".$tv_stream->varsQuery("page",1)."&action=show\"><img src=\"$url_imgs/show.gif\" border=0 alt=\"".TV_STREAM_NAME."\"></a>$g_n<a href=\"?".$tv_stream->varsQuery("page",1)."&action=show\">Show live events</a></td>
  </tr></table><br>";

  if ($action=="add") {
    if ($submit=='Add Event') {
      $flv=str_replace(WP_SITEURL.$tv_stream->getDirUpload().'/','',$flv);

      if ($flv && $title) {
        $time_start=mktime($start_hour,$start_min,$start_sec,$start_month,$start_day,$start_year)-($utc_diff*60);
        if ($id) {
          $query="UPDATE ".$wpdb->prefix."tvstream_schedule SET title='%s', time_start='$time_start', time_end=duration+$time_start, flv='%s' WHERE id='%d'";
          if ($tv_stream->mysqlQuery($query, array(trim($title),$flv,$id)))
            $message='Success edit live event';
          else
            $message='err: '.$tv_stream->mysql_err;
        }
        else {
          $query="UPDATE ".$wpdb->prefix."tvstream_schedule SET title='%s', time_start='$time_start', time_end=duration+$time_start WHERE flv='%s' AND time_start=0 LIMIT 1";
          if ($tv_stream->mysqlQuery($query, array(trim($title),$flv)))
            $message='Success store live event';
          elseif ($tv_stream->mysql_err)
            $message='err: '.$tv_stream->mysql_err;
          else
            $message='err: cannot get flv meta';
        }
        $query="DELETE FROM ".$wpdb->prefix."tvstream_schedule WHERE time_end<".time();
        $tv_stream->mysqlQuery($query);
      }
      else
        $message='err: title and media play item cannot be empty';

      $content.="<div class=\"msg\">$message<div>";
    }
    else {

      $dir_upload = $tv_stream->getDirUpload();
      $upload_url = WP_SITEURL.$dir_upload;
      $upload_dir = realpath(ABSPATH.$dir_upload);

      if ($id) {
        $query="SELECT title, flv, time_start FROM ".$wpdb->prefix."tvstream_schedule WHERE id='%d'";
        $row=$tv_stream->mysqlRow($query, array($id));
        $title=$row[0];
        $flv=$row[1];
        $tm=$row[2];
      }
      else {
        $now=time();
        $query="SELECT time_end FROM ".$wpdb->prefix."tvstream_schedule WHERE time_end>$now ORDER BY time_end DESC LIMIT 1";
        $row=$tv_stream->mysqlRow($query);
        $tm=$row[0];
      }
      if (!$tm)
        $tm=time();

      $now_year=$tv_stream->newDate("Y",$tm);
      $arr_years[]=$now_year;
      $arr_years[]=$now_year+1;
      $arr_months=array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
      for ($i=1; $i<32; $i++) {
        $i_= $i<10 ? "0$i" : $i;
        $arr_days[$i_]=$i_;
      }
      for ($i=0; $i<24; $i++) {
        $i_= $i<10 ? "0$i" : $i;
        $arr_hour[$i_]=$i_;
      }
      for ($i=0; $i<60; $i++) {
        $i_= $i<10 ? "0$i" : $i;
        $arr_min[$i_]=$i_;
      }
      $arr_sec=$arr_min;

      if ($flv) $fname=preg_replace("/.+\//","",$flv);

      $site_url=$tv_stream->cryptStr(WP_SITEURL);
      $content.="<div class=\"add\">
      <ul id=\"browser\" class=\"filetree\">
      <li><span class=\"folder\" id=\"uplds\"><a href=\"$upload_url\">Media uploads</a></span>
      ".$tv_stream->writeLi($tv_stream->readDr($upload_dir,$upload_url,$upload_dir))."
      </ul>
      <form name=\"form\" action=\"\" method=\"post\">
        <table>
          <tr>
            <td align=\"right\"><b>Title event:</b></td>
            <td><input name=\"title\" value=\"$title\" type=\"text\" style=\"width:100%\"></td>
          </tr>
          <tr>
            <td align=\"right\"><b>Media to play:</b></td>
            <td><input name=\"fname\" value=\"$fname\" type=\"text\" style=\"width:100%\"></td>
          </tr>
          <tr>
            <td></td>
            <td align=\"left\"><div id=\"id_metadata\"></div><input name=\"flv\" value=\"$flv\" type=\"hidden\"></td>
          </tr>
          <tr>
            <td align=\"right\"><b>Start live:</b></td>
            <td align=\"left\">
              <select size=\"1\" name=\"start_year\">".$tv_stream->writeOption($arr_years, $now_year)."</select>
              <select size=\"1\" name=\"start_month\">".$tv_stream->writeOption($arr_months, $tv_stream->newDate("m",$tm))."</select>
              <select size=\"1\" name=\"start_day\">".$tv_stream->writeOption($arr_days, $tv_stream->newDate("d",$tm))."</select>
            </td>
          </tr>
          <tr>
            <td align=\"right\"><b>Hour start:</b></td>
            <td align=\"left\">
              <select size=\"1\" name=\"start_hour\">".$tv_stream->writeOption($arr_hour, $tv_stream->newDate("H",$tm))."</select>
              <select size=\"1\" name=\"start_min\">".$tv_stream->writeOption($arr_min, $tv_stream->newDate("i",$tm))."</select> min
              <select size=\"1\" name=\"start_sec\">".$tv_stream->writeOption($arr_sec, $tv_stream->newDate("s",$tm))."</select> sec
            </td>
          </tr>
        </tr>
        <tr>
          <td></td>
          <td align=\"left\"><br>
            <input name=\"id\" value=\"$id\" type=\"hidden\">
            <input type=\"submit\" name=\"submit\" value=\"Add Event\" class=\"button\"></td>
          </tr>
        </table>
      </form>
    </div>
    <div></div>
    <script type=\"text/javascript\">
      function addMedia(f) {
        f_=f.substring(f.lastIndexOf('/')+1);
        if (f_.indexOf('.')>-1) {
          document.form.fname.value=f_;
          document.form.flv.value=f;
          f_=f_.replace(/\.[^\.]+/g, '');
          f_=f_.replace(/[^\w]+/g, ' ');
          f_=f_.replace(/_+/g, ' ');
          document.form.title.value=f_.charAt(0).toUpperCase()+f_.slice(1);
          var flashvars = {};
          var params = {
            allowscriptaccess: 'none'
          };
          swfobject.embedSWF('".TV_STREAM_URL."flash/metadata.swf?sv=$site_url&flv=' + f, 'id_metadata', '205', '22', '9.0.0', 'NULL', flashvars, params);
        }
      };
    </script>";

  }
}
elseif ($action=="show") {
  $site_url=rtrim(get_option("siteurl"),'\\\/ ');
  $now=time();
  $query="SELECT id, time_start, time_end, flv, title FROM ".$wpdb->prefix."tvstream_schedule WHERE time_end>$now ORDER BY time_start";
  $result=$tv_stream->mysqlRows($query);
  foreach($result as $row) {
    if ($cell=='cell1') $cell='cell2';
    else $cell='cell1';
    if ($row[1]<=$now) $cell='live';

    $row[1]+=$utc_diff*60;
    $row[2]+=$utc_diff*60;
    $retval.="<tr $live>
    <td class=\"$cell\">$row[4]</td>
    <td class=\"$cell\">".date("Y/m",$row[1])."</td>
    <td class=\"$cell\">".date("d D H:i:s",$row[1])."</td>
    <td class=\"$cell\">".date("d D H:i:s",$row[2])."</td>
    <td class=\"$cell\">$row[3]</td>";
    if ($cell=='live')
      $retval.="<td class=\"$cell\" colspan=\"2\">live now</td>";
    else
      $retval.="
    <td class=\"$cell\"><a href=\"?".$tv_stream->varsQuery("page",1)."&action=add&id=$row[0]\" title=\"Edit $row[3] entry\"><img src=\"$url_imgs/edit.gif\" border=0></a></td>
    <td class=\"$cell\"><a href=\"javascript:showMini('$site_url/?tv_stream_mini_frame&action=del&id=$row[0]')\" title=\"Delete $row[3] entry\"><img src=\"$url_imgs/del.gif\" border=0></a></td>";
    $retval.="</tr>";
  }
  if ($retval) {
    $content.="<table class=\"show\">
    <tr>
      <td class=\"head\"><b>Title</b></td><td class=\"head\"><b>Month</b></td><td class=\"head\"><b>Start</b></td><td class=\"head\"><b>End</b></td><td class=\"head\"><b>Media to play</b></td><td class=\"head\"><b>Edit</b></td><td class=\"head\"><b>Delete</b></td>
      <tr>
        $retval</table>
        <div></div>";
      }
      else
        $content.="<div class=\"msg\">No live events are programmed, add live events to have online streaming.<div>";
    }
    elseif ($action=="opt") {
      if ($submit=='Set Options') {
        if (isset($utc_diff)) {
          update_option('tv_stream_utc_diff', $utc_diff);
          update_option('tv_stream_php_buffer', $php_buffer);

        }
        else {
          add_option('tv_stream_utc_diff', $utc_diff);
          add_option('tv_stream_php_buffer', $php_buffer);
        }
        $content.="<div class=\"msg\">Options successfully stored<div>";
      }
      else {
        $php_buffer=get_option('tv_stream_php_buffer');
        $nr_sv=$i;
        $tm=time();
        $content.="<form name=\"form\" action=\"\" method=\"post\">
        <table class=\"opt\">
          <tr>
            <td style=\"width:90px\" class=\"cell_right\"><b>Time adjust:</b></td>
            <td>
              <input name=\"utc_diff\" value=\"$utc_diff\" type=\"text\" style=\"width:100%\">
              <span style=\"float:left\">Time difference between your time and PHP time in minutes</span>
              <a href=\"javascript:getDiff()\" title=\"Get time difference\" style=\"float:right\">get</a>
            </td>
          </tr>
          <tr>
            <td style=\"width:90px\" class=\"cell_right\"><b>PHP buffer:</b></td>
            <td>
              <input name=\"php_buffer\" value=\"$php_buffer\" type=\"text\" style=\"width:100%\">
              <span style=\"float:left\">Hold video data in KB, to much require more CPU and RAM.</span>
            </td>
          </tr>
          <tr>
            <td></td>
            <td class=\"cell_left\">
              <input type=\"submit\" name=\"submit\" value=\"Set Options\" class=\"button\">
            </td>
          </tr>
        </table>
      </form>
      <script type=\"text/javascript\">
        var dt = new Date();
        function getDiff() {
          var g = dt.getTimezoneOffset();
          var err=(dt.getTime()/1000)-$tm-0.7;
          if (Math.abs(err)<1) err=0;
          g-=err/60;
          document.form.utc_diff.value=Math.round(-100*g)/100;
        }";
        if (!isset($utc_diff))
          $content.="getDiff();";
        $content.="</script>";
    }
  }

    $show_time='Server time '.date("Y-m-d H:i:s").'; Your local time '.date("Y-m-d H:i:s", ($utc_diff*60)+time());

    $content="<div style=\"position:absolute;top:100px;width:100%;display:none;z-index:999\" id=\"mini_win\" align=\"center\">
      <table>
        <tr>
          <td class=\"head\" align=\"right\" style=\"padding:2px\"><a href=\"javascript:hideMini()\"><img src=\"$url_imgs/close.gif\" alt=\"Close\" width=\"18\" height=\"18\" border=\"0\"></a></td>
        </tr>
        <tr>
          <td class=\"cell1\">
            <iframe id=\"mini_frame\" src=\"about:blank\" width=\"350\" height=\"150\" name=\"mini_frame\" frameborder=\"0\"></iframe>
          </td>
        </tr>
      </table>
    </div>
    <script language=\"JavaScript\" type=\"text/javascript\">
      function showMini(url,w,h) {
        miniframe=document.getElementById('mini_frame')
        if (url) miniframe.src=url;
        if (w) miniframe.width=w;
        if (h) miniframe.height=h;
        miniwin=document.getElementById('mini_win');
        miniwin.style.top= document.body.scrollTop ? document.body.scrollTop+100 : document.documentElement.scrollTop+100;
        miniwin.style.display='';
      }
      function hideMini() {
        document.getElementById('mini_win').style.display='none';
        document.getElementById('mini_frame').src='about:blank';
        self.location.href=self.location.href;
      }
    </script>
    <table width=\"100%\">
      <tr><td align=\"center\">$content</td></tr>
      <tr><td class=\"line_down\"><br>$show_time</td></tr>
    </table>
    <div style=\"float:left\"><a href=\"http://www.saschart.com\">SaschArt TV Stream</a></div>
    <div style=\"float:right;margin-right:7px\"><a href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=GAW2U83KESVUQ&lc=RO&item_name=SaschArt%20Software&currency_code=EUR&bn=PP%2dDonationsBF%3abut_donate%2egif%3aNonHosted\">Make donation here</a></div>";

    echo $content;
  }

  ?>