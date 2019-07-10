<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_session"));

//如果登录状态不在就直接返回登录状态已过时
if (!isLogin('aid')) exit(ajaxReturn(0,"登录状态已失去，请刷新页面重新登录后再操作！"));

$db = new MysqliDb(getMysqlConfig());

if (!isset($_POST["op"])) exit(ajaxReturn(0,"缺失参数，无法操作！"));

if($_POST["op"]=="add"){

	if (empty($_POST["ch_name"])) exit(ajaxReturn(0,"栏目名称不能为空。"));
	if (!x_isint($_POST["ch_father_id"])) exit(ajaxReturn(0,"上级栏目选择错误。"));
	if (!x_isint($_POST["ch_order"])) exit(ajaxReturn(0,"排序值必须是整数。"));
	if ($_POST["ch_audit"]!=0 && $_POST["ch_audit"]!=1) exit(ajaxReturn(0,"是否需要审核的设定有问题。"));

	$data = Array (
		"ch_name"       => $_POST["ch_name"],
	    "ch_father_id"  => $_POST["ch_father_id"],
	    "ch_order"      => $_POST["ch_order"],
	    "ch_audit"      => $_POST["ch_audit"],
	    "ch_ext"        => $_POST["ch_ext"],
	    "ch_memo"       => $_POST["ch_memo"]
	);

	$id = $db->insert ('xt_channel', $data);
	if ($id) exit(ajaxReturn(1,"添加成功。"));
	else exit(ajaxReturn(0,"添加失败：".$db->getLastError()));

}elseif($_POST["op"]=="get"){
	if (empty($_POST["chid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["chid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));	
	$channel = $db->rawQuery('SELECT * from xt_channel where chid = ?', Array ($_POST["chid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要获取的数据不存在！"));
	exit(ajaxReturn(1,"数据获取成功!",$channel[0]));	

}elseif($_POST["op"]=="modify"){


	$data=array();

	if (empty($_POST["chid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["chid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$channel = $db->rawQuery('SELECT * from xt_channel where chid = ?', Array ($_POST["chid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要修改的数据已不存在！请刷新界面后再操作试试！"));

	if (empty($_POST["ch_name"])) exit(ajaxReturn(0,"请输入网站栏目的名称。"));
	if ($channel[0]['ch_name']!=$_POST["ch_name"]) $data['ch_name']=$_POST["ch_name"];  //数据库中的数据与提交过来的不一致再修改

	if ($_POST["ch_audit"]!=1 && $_POST["ch_audit"]!=0) exit(ajaxReturn(0,"网站资料的审核类型错误。"));
	if ($channel[0]['ch_audit'].' '!=$_POST["ch_audit"].' ') $data['ch_audit']=$_POST["ch_audit"];  //不一致再修改，类型强制转换成一致的

	if (!x_isint($_POST["ch_order"])) exit(ajaxReturn(0,"排序值必须是整数。"));
	if ($channel[0]['ch_order'].' '!=$_POST["ch_order"].' ') $data['ch_order']=$_POST["ch_order"];  //数据库中的数据与提交过来的不一致再修改

	if (!x_isint($_POST["ch_father_id"])) exit(ajaxReturn(0,"父栏目选择有问题。"));
	if ($_POST["ch_father_id"]==$_POST["chid"]) exit(ajaxReturn(0,"不能选择自己当做父栏目。"));
	if ($channel[0]['ch_father_id'].' '!=$_POST["ch_father_id"].' ') $data['ch_father_id']=$_POST["ch_father_id"];  //数据库中的数据与提交过来的不一致再修改

	if ($channel[0]['ch_memo']!=$_POST["ch_memo"]) $data['ch_memo']=$_POST["ch_memo"];  //数据库中的数据与提交过来的不一致再修改

	if ($channel[0]['ch_ext']!=$_POST["ch_ext"]) $data['ch_ext']=$_POST["ch_ext"];

	if (empty($data)) exit(ajaxReturn(0,"您未曾修改任何资料！"));
	
	$db->where ('chid', $_POST["chid"]);
	if ($db->update ('xt_channel', $data))
	{
		if ($db->count>0) exit(ajaxReturn(1,"数据更新成功！"));
		else exit(ajaxReturn(0,"要更新的数据已经不存在！请刷新界面后再试试！"));
	}
	else
		exit(ajaxReturn(0,"数据更新操作失败！"));
	    //echo 'update failed: ' . $db->getLastError();


}elseif($_POST["op"]=="delete"){

	if (empty($_POST["chid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["chid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));

	$channel1 = $db->rawQuery('SELECT count(*) as c from xt_channel where ch_father_id = ?', Array ($_POST["chid"]));
	if ($channel1[0]['c']>0) exit(ajaxReturn(0,"此栏目下还有子栏目，请先删除子栏目！"));

	$channel2 = $db->rawQuery('SELECT count(*) as c from xt_content where chid = ?', Array ($_POST["chid"]));
	if ($channel2[0]['c']>0) exit(ajaxReturn(0,"此栏目下的资料不为空，无法删除本栏目！"));

	$db->where('chid', $_POST["chid"]);
	if ($db->delete('xt_channel')) exit(ajaxReturn(1,"管理员账号删除成功！"));
	else  exit(ajaxReturn(0,"删除操作失败！数据库操作错误！"));

}else{
	 exit(ajaxReturn(0,"无此操作，无法继续！"));
}

