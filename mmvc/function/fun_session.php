<?php
//是否在登录状态
//$v 用来判断是否存在的某个session值，$v值不能为空，如果为空也返回false
function isLogin($v){
	if (isset($_SESSION[$v])&&!empty($_SESSION[$v])) return true;
	else return false;
}

//注销登录状态
function unLogin(){
	return session_destroy();
}


//用户登录后，将数组中的资料写入session
function addSession($adminArr){
	$_SESSION['aid']      	=	$adminArr['aid'];
	$_SESSION['a_name']   	=	$adminArr['a_name'];
	$_SESSION['a_account']	=	$adminArr['a_account'];
	$_SESSION['a_type']   	=	$adminArr['a_type'];
	$_SESSION['a_audits'] 	=	$adminArr['a_audits'];
	$_SESSION['a_avata']  	=	$adminArr['a_avata'];
	$_SESSION['a_memo']   	=	$adminArr['a_memo'];
}
?>