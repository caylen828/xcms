<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_session"));

//如果登录状态不在就直接返回登录状态已过时
if (!isLogin('aid')) exit(ajaxReturn(0,"登录状态已失去，请刷新页面重新登录后再操作！"));

$db = new MysqliDb(getMysqlConfig());

/*
	本功能中包括了管理员的所有操作处理
	1. 添加管理员
	2. 编辑修改管理员基本资料
	3. 管理员的权限设定
	4. 删除管理员
*/

if (!isset($_POST["op"])) exit(ajaxReturn(0,"缺失参数，无法操作！"));

if($_POST["op"]=="add"){

	if (empty($_POST["a_account"])) exit(ajaxReturn(0,"账号不能为空。"));
	if (empty($_POST["a_password"])) exit(ajaxReturn(0,"密码不能为空。"));
	if ($_POST["a_password"]!=$_POST["a_password2"]) exit(ajaxReturn(0,"两次输入的密码不相同。"));
	if (empty($_POST["a_name"])) exit(ajaxReturn(0,"请输入管理员的姓名。"));
	if ($_POST["a_type"]!=1 && $_POST["a_type"]!=2 && $_POST["a_type"]!=3) exit(ajaxReturn(0,"管理员的类型选择错误！"));
	$admin = $db->rawQuery('SELECT * from xt_admin where a_account = ?', Array ($_POST["a_account"]));
	if ($db->count>0) exit(ajaxReturn(0,"当前账号已经创建过，请更换账号！"));

	//头像图片的处理，判断有图片数据再处理
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

	$data = Array (
		"a_name"     => $_POST["a_name"],
	    "a_account"  => $_POST["a_account"],
	    "a_password" => md5($_POST["a_password"]),
	    "a_type"     => $_POST["a_type"],
	    "a_memo"     => $_POST["a_memo"],
	    "a_status"   => 1,
	    "a_dt_add"   => x_date(),
	    "a_avata"    => $filename,
	    "a_audits"   => ''
	);

	$id = $db->insert ('xt_admin', $data);
	if ($id) exit(ajaxReturn(1,"添加成功。"));
	else exit(ajaxReturn(0,"添加失败：".$db->getLastError()));

}elseif($_POST["op"]=="get"){
	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));	
	$admin = $db->rawQuery('SELECT aid,a_name,a_account,a_type,a_status,a_memo,a_avata from xt_admin where aid = ?', Array ($_POST["aid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要获取的数据不存在！"));
	$admin[0]['a_type_name']=$adminType[$admin[0]['a_type']];
	$admin[0]['a_status_name']=$adminStatus[$admin[0]['a_status']];
	exit(ajaxReturn(1,"数据获取成功!",$admin[0]));	

}elseif($_POST["op"]=="getAudits"){
	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));	
	$admin = $db->rawQuery('SELECT aid,a_name,a_account,a_type,a_status,a_audits from xt_admin where aid = ?', Array ($_POST["aid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要获取的数据不存在！"));
	$admin[0]['a_type_name']=$adminType[$admin[0]['a_type']];
	$admin[0]['a_status_name']=$adminStatus[$admin[0]['a_status']];

	if ($admin[0]['a_audits']=='') $admin[0]['a_audits']=array("oneself"=>0,"r"=>'',"w"=>'',"d"=>'',"a"=>'');
	else$admin[0]['a_audits']=json_decode($admin[0]['a_audits']);

	exit(ajaxReturn(1,"数据获取成功!",$admin[0]));	

}elseif($_POST["op"]=="modify"){

	$data=array();

	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$admin = $db->rawQuery('SELECT * from xt_admin where aid = ?', Array ($_POST["aid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要修改的数据已不存在！请刷新界面后再操作试试！"));

	if ($_POST["a_password"]!='' || $_POST["a_password2"]!='')
	{
		if ($_POST["a_password"]!=$_POST["a_password2"]) exit(ajaxReturn(0,"两次输入的密码不相同。"));
		$data['a_password']=md5($_POST["a_password"]);
	}

	if (empty($_POST["a_name"])) exit(ajaxReturn(0,"请输入管理员的姓名。"));
	if ($admin[0]['a_name']!=$_POST["a_name"]) $data['a_name']=$_POST["a_name"];  //数据库中的数据与提交过来的不一致再修改

	if ($_POST["a_type"]!=1 && $_POST["a_type"]!=2 && $_POST["a_type"]!=3) exit(ajaxReturn(0,"管理员的类型选择错误！"));
	if ($admin[0]['a_type'].' '!=$_POST["a_type"].' ') $data['a_type']=$_POST["a_type"];  //不一致再修改，类型强制转换成一致的

	if ($_POST["a_status"]!=1 && $_POST["a_status"]!=0) exit(ajaxReturn(0,"管理员的状态选择错误！"));
	if ($admin[0]['a_status'].' '!=$_POST["a_status"].' ') $data['a_status']=$_POST["a_status"];  //不一致再修改，类型强制转换成一致的

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

		if ($db->count>0) exit(ajaxReturn(1,"数据更新成功！"));
		else exit(ajaxReturn(0,"要更新的管理员数据已经不存在！"));
	}
	else
		exit(ajaxReturn(0,"数据更新操作失败！"));
	    //echo 'update failed: ' . $db->getLastError();

}elseif($_POST["op"]=="audit"){

	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$admin = $db->rawQuery('SELECT * from xt_admin where aid = ?', Array ($_POST["aid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要设定权限的账户已不存在！请刷新界面后再操作试试！"));

	/*
	如果是超级管理员无需设定权限
	如果是普通管理员不允许设定审核权限
	如果是审核员就可以设定所有权限，但是不需要”只允许操作自己数据“这个选项
	*/

	$audits=array(
		"oneself"=>0,  //是否只操作自己的数据，1 只操作自己数据；0 不限制
		"r"=>"",       //读，写，删，审核
		"w"=>"",
		"d"=>"",
		"a"=>""
	);

	if ($admin[0]["a_type"]==1) exit(ajaxReturn(0,"超级管理员无需设定权限，请刷新界面后确认当前管理员是否已经是超级管理员。"));
	if ($admin[0]["a_type"]==3 || $admin[0]["a_type"]==2) {
		if (isset($_POST["oneself"]) && $_POST["oneself"]=="1"){
			$audits["oneself"]=1;
		}
	}

	if (isset($_POST["chid_r"])) $audits["r"]=implode($_POST["chid_r"], ",");
	if (isset($_POST["chid_w"])) $audits["w"]=implode($_POST["chid_w"], ",");
	if (isset($_POST["chid_d"])) $audits["d"]=implode($_POST["chid_d"], ",");
	if ($admin[0]["a_type"]==2){
		if (isset($_POST["chid_a"])) $audits["a"]=implode($_POST["chid_a"], ",");	
	}
	$auditsJson=json_encode($audits);

	$data=array("a_audits"=>$auditsJson);
	$db->where ('aid', $_POST["aid"]);
	if ($db->update ('xt_admin', $data))
	{
		if ($db->count>0) exit(ajaxReturn(1,"数据更新成功！"));
		else exit(ajaxReturn(1,"数据未曾修改。"));
	}
	else
		exit(ajaxReturn(0,"数据更新操作失败！"));


}elseif($_POST["op"]=="delete"){

	if (empty($_POST["aid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["aid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));

	$db->where('aid', $_POST["aid"]);
	if ($db->delete('xt_admin')) exit(ajaxReturn(1,"管理员账号删除成功！"));
	else  exit(ajaxReturn(0,"删除操作失败！数据库操作错误！"));

}else{
	 exit(ajaxReturn(0,"无此操作，无法继续！"));
}

