<?php
function x_html_to($str)
{
    $dist=str_replace("'","&acute;",$str);
    $dist=str_replace("<","&lt;",$dist);
    $dist=str_replace(">","&gt;",$dist);
    $dist=str_replace("\\\"","\"",$dist);
    $dist=str_replace("\"","&quot;",$dist);
    //$dist=str_replace(" ","&nbsp;",$dist);
return $dist;
}

function x_html_back($str)
{
    $dist=str_replace("&acute;","'",$str);
    $dist=str_replace("&lt;","<",$dist);
    $dist=str_replace("&gt;",">",$dist);
    $dist=str_replace("&quot;","\"",$dist);
    $dist=str_replace("&amp;","&",$dist);
	//$dist=str_replace("&nbsp;"," ",$dist);
return $dist;
}

function getDays($startDate,$endDate){
	if($startDate == "0000-00-00 00:00:00" || $endDate == "0000-00-00 00:00:00" || $startDate == "" || $endDate == ""){
		return 0;
	}
	$startdate=strtotime($startDate);
	$enddate=strtotime($endDate);
	$days=round(($enddate-$startdate)/3600/24) ;
	return $days;//days为得到的天数;
}

function x_getip()   //获取客户端IP地址
{
    static $realip;
    if(isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $realip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else if(isset($_SERVER['HTTP_CLIENT_IP'])){
            $realip=$_SERVER['HTTP_CLIENT_IP'];
        }
        else{
            $realip=$_SERVER['REMOTE_ADDR'];
        }
    }
    else{
        if(getenv('HTTP_X_FORWARDED_FOR')){
            $realip=getenv('HTTP_X_FORWARDED_FOR');
        }
        else if(getenv('HTTP_CLIENT_IP')){
            $realip=getenv('HTTP_CLIENT_IP');
        }
        else{
            $realip=getenv('REMOTE_ADDR');
        }
    }
    if ($realip=="::1") $realip="本机";
    return $realip;

}

function x_date()  //取出日常用的日期时间格式
{
	return date("Y-m-d H:i:s");
}

function shortDateTime($datetime){
	if (empty($datetime)) return '';
	$t=x_isdate($datetime);
	if (!$t) return false;
	return date("m-d H:i",$t);
}

function x_trim($str) //去除字串中的连续空白，将多个空白合并为一个，并将字串的前后空白去除。
{
	while(strstr($str,"  ")!=false)
	{
		$str=str_replace("  "," ",$str);
	}
	return trim($str);
}

function x_isint($inttmp) //判断值是否为正整数，包括0在内。
{
	if (trim($inttmp)=='') return false;
    if (strlen(floor($inttmp)) == strlen($inttmp)) {return true;}
    else {return false;}
}


function x_isdate($datetime_stamp) //判断日期格式是否正确，如果错误返回FALSE，如果正确则返回timestamp格式的日期，日期和日期时间格式支持两种：2006-03-13 （2006-03-13 12:12:12）和2005/03/13 （2006/03/13 12:12:12）
{
	$datetime_stamp=trim(Chop($datetime_stamp));  //去掉连续的空白和前后空白
	$datetime_stamp=str_replace("/","-",$datetime_stamp);  //如果日期中间有/则改成-
	$datetime_stamp2=str_replace(":","-",$datetime_stamp);  //生成$datetime_stamp2为临时替换用的字串，把所有日期中可能产生的非-字符，全部用-取代，这样的话就会让日期变成：2004-05-5-12-12-12这样的格式，然后就可以用-来做切割了。
	$datetime_stamp2=str_replace(" ","-",$datetime_stamp2);
	$datetime_stamp_tmp=str_replace("-","",$datetime_stamp2);  //做一个没有间隔字符的字串，用来判断其中是否还有除了数字外的其他字符
	$datetime_stamp_a=explode("-",$datetime_stamp2);  //以-切割成数组
	$datetime_stamp_sizeof=sizeof($datetime_stamp_a);  //取数组元素的多少值
	if (x_isint($datetime_stamp_tmp))
	{
		if (($datetime_stamp_sizeof>=3)&&($datetime_stamp_sizeof<=6))
		{
			if (checkdate($datetime_stamp_a[1],$datetime_stamp_a[2],$datetime_stamp_a[0])==false)
			{
				return false;
			}elseif (($datetime_stamp_sizeof>3)&&(($datetime_stamp_a[3]<0)||($datetime_stamp_a[3]>23)))
			{
				return false;
			}elseif (($datetime_stamp_sizeof>4)&&(($datetime_stamp_a[4]<0)||($datetime_stamp_a[4]>59)))
			{
				return false;
			}elseif (($datetime_stamp_sizeof>5)&&(($datetime_stamp_a[5]<0)||($datetime_stamp_a[5]>59)))
			{
				return false;
			}else
			{
				return strtotime($datetime_stamp);  //如果日期格式没有错误就返回timestamp格式的日期时间。
			}
			
		}else
		{
			return false;  //如果数组单元范围不在3到6之间，就说明日期时间格式不正确。
		}
	}else
	{
		return false;  //清理过的字串中还包含有其他特殊字符，所以无法判断，定为非日期字串。
	}
}

//在使用这两个函数前，要先将日期或日期时间转换成timestamp类型。
//如：
//$today=mktime(0,0,0,date("m"),date("d"),date("Y"));

/****模拟sqlserver中的dateadd函数*******
$part 类型：string
取值范围：year,month,day,hour,min,sec
表示：要增加的日期的哪个部分
$n 类型：数值
表示：要增加多少，根据$part决定增加哪个部分
可为负数
$datetime类型：timestamp
表示：增加的基数
返回 类型：timestamp
**************结束**************/
function x_dateadd($part,$n,$datetime){
$year=date("Y",$datetime);
$month=date("m",$datetime);
$day=date("d",$datetime);
$hour=date("H",$datetime);
$min=date("i",$datetime);
$sec=date("s",$datetime);
$part=strtolower($part);
$ret=0;
switch ($part) {
case "year":
$year+=$n;
break;
case "month":
$month+=$n;
break;
case "day":
$day+=$n;
break;
case "hour":
$hour+=$n;
break;
case "min":
$min+=$n;
break;
case "sec":
$sec+=$n;
break;
default:
return $ret;
break;
}
$ret=mktime($hour,$min,$sec,$month,$day,$year);
return $ret;
}

/****模拟sqlserver中的datediff函数*******
$part 类型：string
取值范围：year,month,day,hour,min,sec
表示：要增加的日期的哪个部分
$date1,$date2 类型：timestamp
表示：要比较的两个日期
返回 类型：数值
**************结束*(*************/
function x_datediff($part,$date1,$date2){
//$diff=$date2-$date1;
$year1=date("Y",$date1);
$year2=date("Y",$date2);
$month2=date("m",$date2);
$month1=date("m",$date1);
$day2=date("d",$date2);
$day1=date("d",$date1);
$hour2=date("d",$date2);
$hour1=date("d",$date1);
$min2=date("i",$date2);
$min1=date("i",$date1);
$sec2=date("s",$date2);
$sec1=date("s",$date1);

$part=strtolower($part);
$ret=0;
switch ($part) {
case "year":
$ret=$year2-$year1;
break;
case "month":
$ret=($year2-$year1)*12+$month2-$month1;
break;
case "day":
$ret=(mktime(0,0,0,$month2,$day2,$year2)-mktime(0,0,0,$month1,$day1,$year1))/(3600*24);
break;
case "hour":
$ret=(mktime($hour2,0,0,$month2,$day2,$year2)-mktime($hour1,0,0,$month1,$day1,$year1))/3600;
break;
case "min":
$ret=(mktime($hour2,$min2,0,$month2,$day2,$year2)-mktime($hour1,$min1,0,$month1,$day1,$year1))/60;
break;
case "sec":
$ret=$date2-$date1;
break;
default:
return $ret;
break;
}
return $ret;
}

function randomkeys($length){
	$pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ,./&amp;lt;&gt;?;#:@~[]{}-_=+)(*&amp;^%$￡"!';   
	//字符池 
	for($i=0;$i < $length;$i++){
		$key .= $pattern{mt_rand(0,35)};//生成php随机数
	}
	return $key;
}

function get_months($startym_int,$stopym_int)
{
	$stopym_int=strtotime(date("Y-m-d 23:59:59",$stopym_int));
	
	$a=array();
	$this_ym=date("Y-m",$startym_int);
	$this_ymd=date("Y-m-d",$startym_int);
	$a['m'][]=array("year"=>date("Y",$startym_int),"month"=>date("m",$startym_int),"days"=>cal_days_in_month(CAL_GREGORIAN,date("m",$startym_int),date("Y",$startym_int)));
	$a['d'][]=array("day"=>date("Y-m-d",$startym_int));
	
	for($i=$startym_int;$i<=$stopym_int;$i=$i+86400)
	{
		$tmp=date("Y-m",$i);
		$tmp2=date("Y-m-d",$i);
		
		if ($this_ymd!=$tmp2)
		{
			$a['d'][]=array("day"=>date("Y-m-d",$i));
		}
		
		if ($this_ym!=$tmp)
		{
			$this_ym=date("Y-m",$i);
			$a['m'][]=array("year"=>date("Y",$i),"month"=>date("m",$i),"days"=>cal_days_in_month(CAL_GREGORIAN,date("m",$i),date("Y",$i)));
		}
	}
	
	return $a;
}

function show_checked($v1,$v2)  //$v2允许为数组
{
	if (is_array($v2))
	{
		foreach($v2 as $v)
		{
			if ($v1." "===$v." ")
			{
				echo 'checked="checked"';
				return ;
			}
		}
	}else
	{
		if ($v1." "===$v2." ") echo 'checked="checked"';
	}
}

function show_selected($v1,$v2)  //$v2允许为数组
{
	if (is_array($v2))
	{
		foreach($v2 as $v)
		{
			if ($v1." "===$v." ")
			{
				echo 'selected';
				break;
			}
		}
	}else
	{
		if ($v1." "===$v2." ") echo 'selected';
	}
}

function show_class($cls,$v1='',$v2=''){
	//如果存在v1和v2，那就得满足两个值相等才输出，否则就是直接输出
	if ($v1===$v2) echo $cls;
}

/**
 * 返回数组的维度
 * @param  [type] $arr [description]
 * @return [type]      [description]
 */
function arrayLevel($arr){ 
	$al = array(0); 
	function aL($arr,&$al,$level=0){ 
		if(is_array($arr)){ 
			$level++; 
			$al[] = $level; 
			foreach($arr as $v){ 
				aL($v,$al,$level); 
			} 
		} 
	} 
	aL($arr,$al); 
	return max($al); 
} 

//两个时间间隔，输出*天*时，参数传递时间格式，或者时间戳格式
function printDayHour($startTime,$stopTime)
{
	if (strpos($startTime,"-")!==false || strpos($startTime,":")!==false) $startTime=strtotime($startTime);
	if (strpos($stopTime,"-")!==false || strpos($stopTime,":")!==false) $stopTime=strtotime($stopTime);
	
	$str="余";
	$t=0;
	//判断，如果时间不到或者时间超出，要有区分
	if ($stopTime>=$startTime)
	{
		$t=$stopTime-$startTime;
	}else
	{
		$str="超";
		$t=$startTime-$stopTime;
	}
	$hour=floor(floor($t/3600) % 24);
	$day=floor($t/(3600*24));
	return $str.$day."天".$hour."时";
}

//判断是否是utf8
function is_utf8($word)
{
	if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/",$word) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/",$word) == true)
	{
	return true;
	}
	else
	{
	return false;
	}
}


//自定义函数递归的删除整个目录
function delDir($directory){
if(file_exists($directory)){      //如果不存在rmdir()函数会出错
  if($dir_handle = @opendir($directory)){       //打开目录，并判断是否能成功打开
    while($filename = readdir($dir_handle)){       //循环遍历目录下的所有文件
     if($filename != "."&& $filename != ".."){       //一定要排除两个特殊的目录
       $subFile = $directory."/".$filename;       //将目录下的子文件和当前目录相连
       if(is_dir($subFile))        //如果为目录则条件成立
       delDir($subFile);       //递归地调用自身函数，删除子目录
       if(is_file($subFile))      //如果是文件则条件成立
       unlink($subFile);           //直接删除这个文件
     }
    }
    closedir($dir_handle); //关闭文件资源
    rmdir($directory); //删除空目录
   }
  }
}

//提取网页中所有的超级链接
function match_links($document) { 
	preg_match_all("'<\s*a\s.*?href\s*=\s*([\"\'])?(?(1)(.*?)\\1|([^\s\>]+))[^>]*>?(.*?)</a>'isx",$document,$links); 
	while(list($key,$val) = each($links[2])) { 
	if(!empty($val)) 
	$match['link'][] = $val; 
	} 
	while(list($key,$val) = each($links[3])) { 
	if(!empty($val)) 
	$match['link'][] = $val; 
	} 
	while(list($key,$val) = each($links[4])) { 
	if(!empty($val)) 
	$match['content'][] = $val; 
	} 
	while(list($key,$val) = each($links[0])) { 
	if(!empty($val)) 
	$match['all'][] = $val; 
	} 
	return $match; 
}

//获取网页中所有图片地址
function get_attached($str)
{
	$pattern="/<[img|IMG].*?src=[\'|\"|\ ](.*?(?:[\.gif|\.GIF|\.jpg|\.JPG|\.png|\.PNG|\.jpeg|\.JPEG|\.bmp|\.BMP]))[\'|\"|\ ].*?[\/]?>/"; 
	$pattern="/[src=|href=][\'|\"|\ ]\/.*?[\'|\"|\ ]/";
	
	$arr=array();
	
	preg_match_all($pattern,$str,$match); 
	if (!empty($match))
	{
		foreach($match[0] as $v)
		{
			$v=trim($v);
			$v=trim($v,"=");
			$v=trim($v,"\"");
			$v=trim($v,"\'");
			
			$s=explode("/attached/",$v);
			if (count($s)>0)
			{
				$arr[$v]=$s[1];
			}
		}
	}
	
	return $arr;
	
}

//复制，$child=1就复制子目录，0不复制子目录
function xCopy($source, $destination, $child)
{
	if (!is_dir($source))
	{
		return false;
	}
	
	if (!is_dir($destination))
	{
		mkdir($destination,0777);
	}
	
	$handle=dir($source); 
	while($entry=$handle->read())
	{
		if (($entry!=".") && ($entry!=".."))
		{
			if (is_dir($source."/".$entry))
			{
				if ($child==1)
				{
					xCopy($source."/".$entry,$destination."/".$entry,$child); 
				}
			}
			else
			{
				copy($source."/".$entry,$destination."/".$entry); 
			} 
		}
	} 
	
	return true; 
} 

function return_code($type,$msg,$filename='')
{
	if ($filename=='') return json_encode(array("code"=>$type,"msg"=>$msg));
	else  return json_encode(array("code"=>$type,"msg"=>$msg,"filename"=>$filename));
}


//mime
function get_mime($mime)
{
	$arr=array(
	'acx' => 'application/internet-property-stream',
	'ai' => 'application/postscript',
	'aif' => 'audio/x-aiff',
	'aifc' => 'audio/x-aiff',
	'aiff' => 'audio/x-aiff',
	'asp' => 'text/plain',
	'aspx' => 'text/plain',
	'asf' => 'video/x-ms-asf',
	'asr' => 'video/x-ms-asf',
	'asx' => 'video/x-ms-asf',
	'au' => 'audio/basic',
	'avi' => 'video/x-msvideo',
	'axs' => 'application/olescript',
	'bas' => 'text/plain',
	'bcpio' => 'application/x-bcpio',
	'bin' => 'application/octet-stream',
	'bmp' => 'image/bmp',
	'c' => 'text/plain',
	'cat' => 'application/vnd.ms-pkiseccat',
	'cdf' => 'application/x-cdf',
	'cer' => 'application/x-x509-ca-cert',
	'class' => 'application/octet-stream',
	'clp' => 'application/x-msclip',
	'cmx' => 'image/x-cmx',
	'cod' => 'image/cis-cod',
	'cpio' => 'application/x-cpio',
	'crd' => 'application/x-mscardfile',
	'crl' => 'application/pkix-crl',
	'crt' => 'application/x-x509-ca-cert',
	'csh' => 'application/x-csh',
	'css' => 'text/css',
	'dcr' => 'application/x-director',
	'der' => 'application/x-x509-ca-cert',
	'dir' => 'application/x-director',
	'dll' => 'application/x-msdownload',
	'dms' => 'application/octet-stream',
	'doc' => 'application/msword',
	'dot' => 'application/msword',
	'dvi' => 'application/x-dvi',
	'dxr' => 'application/x-director',
	'eps' => 'application/postscript',
	'etx' => 'text/x-setext',
	'evy' => 'application/envoy',
	'exe' => 'application/octet-stream',
	'fif' => 'application/fractals',
	'flr' => 'x-world/x-vrml',
	'flv' => 'video/x-flv',
	'gif' => 'image/gif',
	'gtar' => 'application/x-gtar',
	'gz' => 'application/x-gzip',
	'h' => 'text/plain',
	'hdf' => 'application/x-hdf',
	'hlp' => 'application/winhlp',
	'hqx' => 'application/mac-binhex40',
	'hta' => 'application/hta',
	'htc' => 'text/x-component',
	'htm' => 'text/html',
	'html' => 'text/html',
	'htt' => 'text/webviewhtml',
	'ico' => 'image/x-icon',
	'ief' => 'image/ief',
	'iii' => 'application/x-iphone',
	'ins' => 'application/x-internet-signup',
	'isp' => 'application/x-internet-signup',
	'jfif' => 'image/pipeg',
	'jpe' => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'jpg' => 'image/jpeg',
	'js' => 'application/x-javascript',
	'latex' => 'application/x-latex',
	'lha' => 'application/octet-stream',
	'lsf' => 'video/x-la-asf',
	'lsx' => 'video/x-la-asf',
	'lzh' => 'application/octet-stream',
	'm13' => 'application/x-msmediaview',
	'm14' => 'application/x-msmediaview',
	'm3u' => 'audio/x-mpegurl',
	'man' => 'application/x-troff-man',
	'mdb' => 'application/x-msaccess',
	'me' => 'application/x-troff-me',
	'mht' => 'message/rfc822',
	'mhtml' => 'message/rfc822',
	'mid' => 'audio/mid',
	'mny' => 'application/x-msmoney',
	'mov' => 'video/quicktime',
	'movie' => 'video/x-sgi-movie',
	'mp2' => 'video/mpeg',
	'mp3' => 'audio/mpeg',
	'mpa' => 'video/mpeg',
	'mpe' => 'video/mpeg',
	'mpeg' => 'video/mpeg',
	'mpg' => 'video/mpeg',
	'mpp' => 'application/vnd.ms-project',
	'mpv2' => 'video/mpeg',
	'ms' => 'application/x-troff-ms',
	'mvb' => 'application/x-msmediaview',
	'nws' => 'message/rfc822',
	'oda' => 'application/oda',
	'p10' => 'application/pkcs10',
	'p12' => 'application/x-pkcs12',
	'p7b' => 'application/x-pkcs7-certificates',
	'p7c' => 'application/x-pkcs7-mime',
	'p7m' => 'application/x-pkcs7-mime',
	'p7r' => 'application/x-pkcs7-certreqresp',
	'p7s' => 'application/x-pkcs7-signature',
	'pbm' => 'image/x-portable-bitmap',
	'pdf' => 'application/pdf',
	'pfx' => 'application/x-pkcs12',
	'pgm' => 'image/x-portable-graymap',
	'php' => 'text/plain',
	'pko' => 'application/ynd.ms-pkipko',
	'pma' => 'application/x-perfmon',
	'pmc' => 'application/x-perfmon',
	'pml' => 'application/x-perfmon',
	'pmr' => 'application/x-perfmon',
	'pmw' => 'application/x-perfmon',
	'png' => 'image/png',
	'pnm' => 'image/x-portable-anymap',
	'pot,' => 'application/vnd.ms-powerpoint',
	'ppm' => 'image/x-portable-pixmap',
	'pps' => 'application/vnd.ms-powerpoint',
	'ppt' => 'application/vnd.ms-powerpoint',
	'prf' => 'application/pics-rules',
	'ps' => 'application/postscript',
	'pub' => 'application/x-mspublisher',
	'qt' => 'video/quicktime',
	'ra' => 'audio/x-pn-realaudio',
	'ram' => 'audio/x-pn-realaudio',
	'ras' => 'image/x-cmu-raster',
	'rgb' => 'image/x-rgb',
	'rmi' => 'audio/mid',
	'roff' => 'application/x-troff',
	'rtf' => 'application/rtf',
	'rtx' => 'text/richtext',
	'scd' => 'application/x-msschedule',
	'sct' => 'text/scriptlet',
	'setpay' => 'application/set-payment-initiation',
	'setreg' => 'application/set-registration-initiation',
	'sh' => 'application/x-sh',
	'shar' => 'application/x-shar',
	'sit' => 'application/x-stuffit',
	'snd' => 'audio/basic',
	'spc' => 'application/x-pkcs7-certificates',
	'spl' => 'application/futuresplash',
	'src' => 'application/x-wais-source',
	'sst' => 'application/vnd.ms-pkicertstore',
	'stl' => 'application/vnd.ms-pkistl',
	'stm' => 'text/html',
	'svg' => 'image/svg+xml',
	'sv4cpio' => 'application/x-sv4cpio',
	'sv4crc' => 'application/x-sv4crc',
	'swf' => 'application/x-shockwave-flash',
	't' => 'application/x-troff',
	'tar' => 'application/x-tar',
	'tcl' => 'application/x-tcl',
	'tex' => 'application/x-tex',
	'texi' => 'application/x-texinfo',
	'texinfo' => 'application/x-texinfo',
	'tgz' => 'application/x-compressed',
	'tif' => 'image/tiff',
	'tiff' => 'image/tiff',
	'tr' => 'application/x-troff',
	'trm' => 'application/x-msterminal',
	'tsv' => 'text/tab-separated-values',
	'txt' => 'text/plain',
	'uls' => 'text/iuls',
	'ustar' => 'application/x-ustar',
	'vcf' => 'text/x-vcard',
	'vrml' => 'x-world/x-vrml',
	'wav' => 'audio/x-wav',
	'wcm' => 'application/vnd.ms-works',
	'wdb' => 'application/vnd.ms-works',
	'wks' => 'application/vnd.ms-works',
	'wmf' => 'application/x-msmetafile',
	'wmv' => 'video/x-ms-wmv',
	'wps' => 'application/vnd.ms-works',
	'wri' => 'application/x-mswrite',
	'wrl' => 'x-world/x-vrml',
	'wrz' => 'x-world/x-vrml',
	'xaf' => 'x-world/x-vrml',
	'xbm' => 'image/x-xbitmap',
	'xla' => 'application/vnd.ms-excel',
	'xlc' => 'application/vnd.ms-excel',
	'xlm' => 'application/vnd.ms-excel',
	'xls' => 'application/vnd.ms-excel',
	'xlt' => 'application/vnd.ms-excel',
	'xlw' => 'application/vnd.ms-excel',
	'xof' => 'x-world/x-vrml',
	'xpm' => 'image/x-xpixmap',
	'xwd' => 'image/x-xwindowdump',
	'z' => 'application/x-compress',
	'zip' => 'application/zip'
	);
	
	if (!isset($arr[$mime])) return "application/octet-stream";
	else return $arr[$mime];
}

// 说明：获取完整URL
 
function curPageURL() 
{
  $pageURL = 'http';
 
  if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") 
  {
    $pageURL .= "s";
  }
  $pageURL .= "://";
 
  if ($_SERVER["SERVER_PORT"] != "80") 
  {
    $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
  } 
  else
  {
    $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
  }
  return $pageURL;
}

function debug($a)
{
	echo "<pre>";
	var_dump($a);
	echo "</pre>";
}

if ( !function_exists('htmlspecialchars_decode') )
{
    function htmlspecialchars_decode($text)
    {
        return strtr($text, array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
    }
}

function is_https()
{
	if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
	return TRUE;
	} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	return TRUE;
	} elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
	return TRUE;
	}
	
	return FALSE;
}

//参数替换并拼接成url，两个参数都是数组
function parUrl($pSource,$pReplace=array()){
	//因为用了引用，所以需要复制一套数组，避免影响被引用数据。不能直接赋值，直接赋值将会把引用复制过来，只能用循环复制。
	$arr=array();

	foreach($pSource as $key => $value){
	  $arr[$key]=$value;
	}

	if (!empty($pReplace))
	{
	  foreach ($pReplace as $key => $value) {
	    $arr[$key]=$value;
	  }
	}

	//拼接成链接
	$key="";
	$value="";
	$urlPars="";
	foreach ($arr as $key => $value) {
	  $urlPars=$urlPars."&".$key."=".urlencode($value);
	}
	return trim($urlPars,"&");
}

/**
 * 用递归获取子类信息，用于栏目的处理
 * $data 所有分类
 * $parent_id 父级id
 * $level 层级
 * $result 分好类的数组
*/
function getChild($data,$parent_id = 0,$level = 0){
    //声明静态数组,避免递归调用时,多次声明导致数组覆盖
    static  $result;
    foreach ($data as $key => $info){
        //第一次遍历,找到父节点为根节点的节点 也就是parent_id=0的节点
        if($info['ch_father_id'] == $parent_id){
            $info['level'] = $level;
            $result[] = $info;
            //把这个节点从数组中移除,减少后续递归消耗
            unset($data[$key]);
            //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
            getChild($data,$info['chid'],$level+1);
        }
    }
    return $result;
}

function qianzhui($level){
	$a="";
	for($i=0;$i<$level;$i++){
		$a=$a." —— ";
	}
	return $a;
}


?>