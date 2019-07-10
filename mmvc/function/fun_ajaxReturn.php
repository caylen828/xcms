<?php
function ajaxReturn($code="",$msg="",$data=array(),$color=""){
	if ($code===""){
		$code=-100;
		$msg="系统错误，未正确提交code参数！";
	}

	$r=array(
		"code" =>$code,
		"msg"  =>$msg,
		"data" =>$data,
		"color"=>$color
	);
	
	return json_encode($r);
}

?>