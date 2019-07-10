<?php
//传递过来的string为: base64_encode(a广告主ID+"||"+b广告计划ID+"||"+c广告素材ID+"||"+最终到达URL);
//a2||b1||c11||http%3A%2F%2Fwww.maketion.com%2F%3Futm_source%3Dmdsp%26utm_medium%3DCPM%26utm_campaign%3Dcpmtest
function c_encode($aid,$bid,$cid,$url) //转成参数的函数
{
	return base64_encode("a".$aid."||b".$bid."||c".$cid."||".urlencode($url));
}

function c_decode($string) //转出参数的函数
{
	$s=base64_decode($string);
	$a=explode("||",$s);
	$a["a"]=str_replace("a","",$a[0]);
	$a["b"]=str_replace("b","",$a[1]);
	$a["c"]=str_replace("c","",$a[2]);
	$a["url"]=urldecode($a[3]);
	
	return $a;
}

