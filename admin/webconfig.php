<?php
session_start();

//定义常用路径
define("RootPath"              , "/home/www/sh-xuanji.com/xcms/");
define("RootUrl"               , "http://www.sh-xuanji.com/xcms/");
define("MmvcRootPath"          , RootPath ."mmvc/");       		//mmvc的根目录，后面需要加上/
define("uploadDir"             , RootPath ."uploads/");    		//所有上传的文件的存储路径
define("uploadUrl"             , RootUrl  ."uploads/");     	//上传地址的url
define("uploadDirAvata"        , uploadDir."avata/");     		//上传的头像存储路径
define("uploadUrlAvata"        , uploadUrl."avata/");     		//头像的网络访问地址
define("uploadDirContentPic"   , uploadDir."contentPic/");		//资料中的头图附件
define("uploadUrlContentPic"   , uploadUrl."contentPic/");		//头像的网络访问地址
define("uploadDirContentAtt"   , uploadDir."contentAtt/");		//附件
define("uploadUrlContentAtt"   , uploadUrl."contentAtt/");		//附件url

if (!defined('MmvcRootPath'))       exit("网站配置错误，请先设定常量MmvcRoot！");
if (!is_dir(MmvcRootPath))          exit("网站配置错误，设定的目录".MmvcRootPath."不存在！");
if (substr(MmvcRootPath, -1)!=="/") exit("网站配置错误，目录路径设定尾部需要/结尾。");


//防止注入处理
if(!empty($_POST)){foreach($_POST as $k => $v) {if(!is_array($v)) {$_POST[$k]=htmlspecialchars(urldecode($v));}}}
if(!empty($_GET)){foreach($_GET as $k => $v) {$_GET[$k]=htmlspecialchars(urldecode($v));}}
if(!empty($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING']=htmlspecialchars(urldecode($_SERVER['QUERY_STRING']));
//防注入处理结束


//将所有参数自动放入一个数组中，不过这样做可能会造成内存的翻倍使用。只是便于后面的程序操作。为了避免内存过多消耗，使用了引用方式
$par=array();
foreach ($_GET as $kk => $vv) {$par[$kk]=&$_GET[$kk];}
foreach ($_POST as $kk => $vv) {$par[$kk]=&$_POST[$kk];}

//通用的mmvc文件加载函数
function minclude($arr)
{
	$d1[]=MmvcRootPath."class/";
	$d1[]=MmvcRootPath."config/";
	$d1[]=MmvcRootPath."class/db/";
	$d1[]=MmvcRootPath."function/";

	$str="";
	foreach($arr as $value){
		foreach ($d1 as $d1one) {
			$f = $d1one.$value;
			if (strtolower(substr($value,-4))!==".php") $f=$f.".php"; //如果文件名的扩展名没写就自动添加.php
			if (file_exists($f)){
				include_once($f);
				$str=$str.$f.";";
				break;
			}
		}
	}
	return $str;
}

/* 管理员相关配置，此配置用于界面上的内容输出 */

$adminType=array(
	1=>"超级管理员",
	2=>"审核员",
	3=>"普通管理员"
);

$adminStatus=array(
	0=>"停用",
	1=>"正常"
);

$channelAudit=array(
	0=>"<span class='badge bg-light-blue'>无需审核</span>",
	1=>"<span class='badge bg-orange'>需要审核</span>"
);
$contentAudit=array(
	"-1"=>"<span class='badge bg-red'>审核未通过</span>",
	"0" =>"<span class='badge bg-yellow'>未审核</span>",
	"1" =>"<span class='badge bg-green'>审核通过</span>",
	"10"=>"<span class='badge bg-green'>自动通过</span>"
);
$contentLevel=array(
	"0" =>"<span class='badge bg-black'>普通</span>",
	"1" =>"<span class='badge bg-green'>推荐</span>",
	"2" =>"<span class='badge bg-light-blue'>置顶</span>"
);

/* 管理员相关配置，此配置用于界面上的内容输出 End---------- */
?>
