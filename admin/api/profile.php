<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_session"));

//如果登录状态不在就直接返回登录状态已过时
if (!isLogin('aid')) exit(ajaxReturn(0,"登录状态已失去，请刷新页面重新登录后再操作！"));

$db = new MysqliDb(getMysqlConfig());

if (!isset($_POST["op"])) exit(ajaxReturn(0,"缺失参数，无法操作！"));

if($_POST["op"]=="modify"){

	$data=array();

	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$admin = $db->rawQuery('SELECT * from xt_admin where aid = ?', Array ($_POST["aid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要修改的数据已不存在！请刷新界面后再操作试试！"));

	if (empty($_POST["a_name"])) exit(ajaxReturn(0,"请输入管理员的姓名。"));
	if ($admin[0]['a_name']!=$_POST["a_name"]) $data['a_name']=$_POST["a_name"];  //数据库中的数据与提交过来的不一致再修改

	if ($admin[0]['a_memo']!=$_POST["a_memo"]) $data['a_memo']=$_POST["a_memo"];  //数据库中的数据与提交过来的不一致再修改

	//头像图片如果有新的就处理
	$filename='';
	if (isset($_FILES["a_avata"]["name"]) && trim($_FILES["a_avata"]["name"])!=''){
		//判断上传文件必须是图片
		if (strpos($_FILES["a_avata"]["type"],"image")===false) exit(ajaxReturn(0,"您上传的头像文件不是图片！"));
		//为文件重新命名
		$filename=substr(microtime(true),0,10).mt_rand(10000,99999).".".pathinfo($_FILES["a_avata"]["name"],PATHINFO_EXTENSION);
		$avataFile=uploadDirAvata.$filename;
		move_uploaded_file($_FILES["a_avata"]["tmp_name"], $avataFile);
		//可以考虑压缩一下图片文件
	}
	if ($filename!='') $data['a_avata']=$filename;

	if (empty($data)) exit(ajaxReturn(0,"您未曾修改任何资料！"));

	$db->where ('aid', $_POST["aid"]);
	if ($db->update ('xt_admin', $data))
	{
		//如果头图也被更新了的话，就将历史文件删除
		if ($filename!='' && !empty($admin[0]['a_avata'])){
			$tf=uploadDirAvata.$admin[0]['a_avata'];
			if (file_exists($tf)) unlink($tf);
		} 

		if ($db->count>0){
			//重新获取管理员资料后，更新session
			$admin = $db->rawQuery('SELECT * from xt_admin where aid = ?', Array ($_POST["aid"]));
			addSession($admin[0]);
			exit(ajaxReturn(1,"数据更新成功！"));
		}
		else exit(ajaxReturn(0,"管理员数据没有被更新。"));
	}
	else
		exit(ajaxReturn(0,"数据更新操作失败！"));
	    //echo 'update failed: ' . $db->getLastError();

}elseif($_POST["op"]=="modifyPassword"){

	$data=array();

	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$admin = $db->rawQuery('SELECT * from xt_admin where aid = ?', Array ($_POST["aid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要修改的数据已不存在！请刷新界面后再操作试试！"));

	if ($_POST["a_password_old"]=='') exit(ajaxReturn(0,"请输入原始密码。"));
	if ($_POST["a_password"]=='') exit(ajaxReturn(0,"请输入新密码。"));
	if ($_POST["a_password2"]=='') exit(ajaxReturn(0,"请输入验证密码。"));

	if ($_POST["a_password"]!=$_POST["a_password2"]) exit(ajaxReturn(0,"两次输入的密码不相同。"));

	if (md5($_POST["a_password_old"])!=$admin[0]['a_password']) exit(ajaxReturn(0,"原始密码错误，请重新输入。"));

	$data['a_password']=md5($_POST["a_password"]);

	$db->where ('aid', $_POST["aid"]);
	if ($db->update ('xt_admin', $data))
	{
		if ($db->count>0) exit(ajaxReturn(1,"数据更新成功！"));
		else exit(ajaxReturn(0,"管理员数据没有被更新！"));
	}
	else
		exit(ajaxReturn(0,"数据更新操作失败！"));
	    //echo 'update failed: ' . $db->getLastError();

}else{
	 exit(ajaxReturn(0,"无此操作，无法继续！"));
}

