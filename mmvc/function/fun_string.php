<?php

function uuid($prefix = '')
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars,0,8) . '-';
    $uuid .= substr($chars,8,4) . '-';
    $uuid .= substr($chars,12,4) . '-';
    $uuid .= substr($chars,16,4) . '-';
    $uuid .= substr($chars,20,12);
    return $prefix . $uuid;
} 

function getCookieId()
{
    $chars = md5(uniqid(mt_rand(), true));
    $uuid = substr($chars,0,8);
    $uuid .= substr($chars,8,4);
    $uuid .= substr($chars,12,4);
    $uuid .= substr($chars,16,4);
    $uuid .= substr($chars,20,12);
    return strtoupper($uuid);
} 
function is_email($email){
    if(preg_match("/\w+([-+.']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email)){
        return TRUE;
    }else{
        return FALSE;
    }
}

function x_isint($inttmp) //判断值是否为正整数，包括0在内。
{
	$inttmp_len=strlen($inttmp);
	if ($inttmp_len==0)
	{
		$int_is=false;
	}else
	{
		$int_is=true;
	}
	for ($i=0;$i<=$inttmp_len;$i++)
	{
		$intsubstr=substr($inttmp,$i,1);
		switch ($intsubstr) {
		  case "0":
		    break;
		  case "1":
		    break;
		  case "2":
		    break;
		  case "3":
		    break;
		  case "4":
		    break;
		  case "5":
		    break;
		  case "6":
		    break;
		  case "7":
		    break;
		  case "8":
		    break;
		  case "9":
		    break;
		  default:
		    $int_is=false;
		    break;
		}//echo $intsubstr."<br>".$int_is."<br>";
		if ($int_is==false){break;}
	}
	return $int_is;
}
/**
 * 判断是否utf8编码
 * @param type $word
 * @return boolean
 */
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
?>