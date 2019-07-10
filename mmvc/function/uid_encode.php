<?php

function encUid($uid)  //对UID做转码
{
	//下面是转码表
	$code=array(
		0=>array('v','l','c'),
		1=>array('i','e','j'),
		2=>array('r','k'),
		3=>array('a','z','h'),
		4=>array('s','f'),
		5=>array('q','w','o'),
		6=>array('d','y'),
		7=>array('b','t','x'),
		8=>array('n','u'),
		9=>array('m','p','g'),
	);
	
	$str="";
	
	for($i=0;$i<strlen($uid);$i++)
	{
		$bit=substr($uid,$i,1);
		//$str=getbit();
		$tmpa=$code[$bit];
		$count=count($tmpa);
		$num=rand(0,$count-1);
		//echo $num;
		$str=$str.$tmpa[$num];
	}
	
	return $str;
}

function decUid($uid)  //将转码后的UID解出来
{
	//下面是解码表
	$code=array(
		'a'=>3,
		'b'=>7,
		'c'=>0,
		'd'=>6,
		'e'=>1,
		'f'=>4,
		'g'=>9,
		'h'=>3,
		'i'=>1,
		'j'=>1,
		'k'=>2,
		'l'=>0,
		'm'=>9,
		'n'=>8,
		'o'=>5,
		'p'=>9,
		'q'=>5,
		'r'=>2,
		's'=>4,
		't'=>7,
		'u'=>8,
		'v'=>0,
		'w'=>5,
		'x'=>7,
		'y'=>6,
		'z'=>3,
	);
	
	$str="";
	for($i=0;$i<strlen($uid);$i++)
	{
		$bit=substr($uid,$i,1);
		$str=$str.$code[$bit];
	}
	
	return $str;
}

//$uid='111222333444555666777888999000';
//echo $b=encUid($uid);
//
//echo "<br>";
//
//echo $c=decUid($b);
?>