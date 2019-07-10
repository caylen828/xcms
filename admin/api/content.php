<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_content","fun_session"));

//如果登录状态不在就直接返回登录状态已过时
if (!isLogin('aid')) exit(ajaxReturn(0,"登录状态已失去，请刷新页面重新登录后再操作！"));

$db = new MysqliDb(getMysqlConfig());

//debug($_POST);

if (!isset($_POST["op"])) exit(ajaxReturn(0,"缺失参数，无法操作！"));

if($_POST["op"]=="add"){

	//debug($_POST);

	if (!isset($_POST["chid"])) exit(ajaxReturn(0,"栏目编号不存在，无法操作。"));
	if (!x_isint($_POST["chid"])) exit(ajaxReturn(0,"栏目编号错误，无法操作。"));
	if (empty($_POST["c_title"])) exit(ajaxReturn(0,"主标题不能为空。"));
	if (!x_isint($_POST["c_order"])) exit(ajaxReturn(0,"排序值必须是整数。"));

	$ch=getChannelById($_POST["chid"],$db);
	$audit=0;  //默认需要审核
	if (!$ch)  exit(ajaxReturn(0,"栏目错误，无法继续！"));
	if ($ch['ch_audit']==0) $audit=10;  //自动通过，无需审核

	//头图的处理，判断有图片数据再处理
	$filename='';
	if (isset($_FILES["c_pic"]["name"]) && trim($_FILES["c_pic"]["name"])!=''){
		//判断上传文件必须是图片
		if (strpos($_FILES["c_pic"]["type"],"image")===false) exit(ajaxReturn(0,"您上传的头图不是图片！"));
		//为文件重新命名
		$filename=substr(microtime(true),0,10).mt_rand(10000,99999).".".pathinfo($_FILES["c_pic"]["name"],PATHINFO_EXTENSION);
		$picFile=uploadDirContentPic.$filename;
		move_uploaded_file($_FILES["c_pic"]["tmp_name"], $picFile);
		//可以考虑压缩一下图片文件
	}

	//其他附件的处理
	$data_att=array();
	if(isset($_FILES["ca_detail"])){

		foreach($_FILES["ca_detail"]['tmp_name'] as $k=>$v){
			if ($_FILES["ca_detail"]['tmp_name'][$k]=='') exit(ajaxReturn(0,"添加的第".($k+1)."个附件未选择文件。"));
			if (empty($_POST['ca_title'][$k])) exit(ajaxReturn(0,"第".($k+1)."个附件的名称未填写。"));
			if (!x_isint($_POST['ca_order'][$k])) exit(ajaxReturn(0,"第".($k+1)."个附件的排序值错误，必须是大于0的整数。"));
			$ext=strtolower(pathinfo($_FILES["ca_detail"]["name"][$k],PATHINFO_EXTENSION));
			$filenameAtt=substr(microtime(true),0,10).mt_rand(10000,99999).".".$ext;
			$attFile=uploadDirContentAtt.$filenameAtt;
			move_uploaded_file($_FILES["ca_detail"]["tmp_name"][$k], $attFile);
			$data_att[$k]=array(
				"cid"           => '',  //下面再添加这个值，需要先等content表插入完成
				"chid"          => $_POST["chid"],
				"ca_type"       => $ext,
				"ca_detail"     => $filenameAtt,
				"ca_detail_url" => $_POST['ca_detail_url'][$k],
				"ca_title"      => $_POST['ca_title'][$k],
				"ca_content"    => $_POST['ca_content'][$k],
				"ca_order"      => $_POST['ca_order'][$k],
				"ca_filename"   => $_FILES["ca_detail"]["name"][$k],
				"ca_size"       => $_FILES["ca_detail"]["size"][$k]
			);
		}

	}

	$data = Array (
		"chid"            => $_POST["chid"],
	    "c_title"         => $_POST["c_title"],
	    "c_title2"        => $_POST["c_title2"],
	    "c_author"        => $_POST["c_author"],
	    "c_source"        => $_POST["c_source"],
	    "c_order"         => $_POST["c_order"],
	    "c_level"         => $_POST["c_level"],
	    "c_audit"         => $audit,
	    "c_aid_add"       => $_SESSION['aid'],
	    "c_dt_add"        => x_date(),
	    "c_summary"       => $_POST["c_summary"],
	    "c_detail"        => $_POST["c_detail"],
	    "c_pic"           => $filename,
	    "c_pic_url"       => $_POST["c_pic_url"],
	    "c_attachment"    => count($data_att)
	);

	$id = $db->insert ('xt_content', $data);
	if ($id){
		foreach($data_att as $k => $v){
			$data_att[$k]['cid']=$id;
		}

		$ids = $db->insertMulti('xt_content_attachment', $data_att);

		exit(ajaxReturn(1,"添加成功。"));
	} 
	else exit(ajaxReturn(0,"添加失败：".$db->getLastError()));

}elseif($_POST["op"]=="get"){
	if (empty($_POST["cid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["cid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));	
	$content = $db->rawQuery('SELECT * from xt_content where cid = ?', Array ($_POST["cid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要获取的数据不存在！"));

	$content[0]['attachments']=array();
	$contentAttachment = $db->rawQuery('SELECT caid,ca_detail,ca_detail_url,ca_title,ca_order,ca_content from xt_content_attachment where cid = ? order by ca_order desc,caid', Array ($_POST["cid"]));
	foreach($contentAttachment as $k=>$v){
		$contentAttachment[$k]['url']=uploadUrlContentAtt.$contentAttachment[$k]['ca_detail'];
	}
	if ($db->count>0) $content[0]['attachments']=$contentAttachment;

	//格式化一些数据
	$content[0]['c_detail']=trim($content[0]['c_detail']);  //如果返回值是null，前端的kindeditor控件会报错
	exit(ajaxReturn(1,"数据获取成功!",$content[0]));	

}elseif($_POST["op"]=="modify"){

	$data = Array();

	if (!isset($_POST["cid"])) exit(ajaxReturn(0,"栏目编号不存在，无法操作。"));
	if (!x_isint($_POST["cid"])) exit(ajaxReturn(0,"栏目编号错误，无法操作。"));
	if (empty($_POST["c_title"])) exit(ajaxReturn(0,"主标题不能为空。"));
	if (!x_isint($_POST["c_order"])) exit(ajaxReturn(0,"排序值必须是整数。"));

	$oldData=getContentById($_POST["cid"],$db);
	if (!$oldData)  exit(ajaxReturn(0,"资料错误，无法继续！"));

	//头图的处理，判断有图片数据再处理
	$filename='';
	if (isset($_FILES["c_pic"]["name"]) && trim($_FILES["c_pic"]["name"])!=''){
		//判断上传文件必须是图片
		if (strpos($_FILES["c_pic"]["type"],"image")===false) exit(ajaxReturn(0,"您上传的头图不是图片！"));
		//为文件重新命名
		$filename=substr(microtime(true),0,10).mt_rand(10000,99999).".".pathinfo($_FILES["c_pic"]["name"],PATHINFO_EXTENSION);
		$picFile=uploadDirContentPic.$filename;
		move_uploaded_file($_FILES["c_pic"]["tmp_name"], $picFile);
		//可以考虑压缩一下图片文件
	}

	//其他附件的处理
	$data_att=array();
	if(isset($_FILES["ca_detail"])){

		foreach($_FILES["ca_detail"]['tmp_name'] as $k=>$v){
			if ($_FILES["ca_detail"]['tmp_name'][$k]=='') exit(ajaxReturn(0,"添加的第".($k+1)."个附件未选择文件。"));
			if (empty($_POST['ca_title'][$k])) exit(ajaxReturn(0,"第".($k+1)."个附件的名称未填写。"));
			if (!x_isint($_POST['ca_order'][$k])) exit(ajaxReturn(0,"第".($k+1)."个附件的排序值错误，必须是大于0的整数。"));
			$ext=strtolower(pathinfo($_FILES["ca_detail"]["name"][$k],PATHINFO_EXTENSION));
			$filenameAtt=substr(microtime(true),0,10).mt_rand(10000,99999).".".$ext;
			$attFile=uploadDirContentAtt.$filenameAtt;
			move_uploaded_file($_FILES["ca_detail"]["tmp_name"][$k], $attFile);
			$data_att[$k]=array(
				"cid"           => $_POST["cid"],  //下面再添加这个值，需要先等content表插入完成
				"chid"          => $oldData["chid"],
				"ca_type"       => $ext,
				"ca_detail"     => $filenameAtt,
				"ca_detail_url" => $_POST['ca_detail_url'][$k],
				"ca_title"      => $_POST['ca_title'][$k],
				"ca_content"    => $_POST['ca_content'][$k],
				"ca_order"      => $_POST['ca_order'][$k],
				"ca_filename"   => $_FILES["ca_detail"]["name"][$k],
				"ca_size"       => $_FILES["ca_detail"]["size"][$k]
			);
		}

	}

	$data = Array (
	    "c_title"         => $_POST["c_title"],
	    "c_title2"        => $_POST["c_title2"],
	    "c_author"        => $_POST["c_author"],
	    "c_source"        => $_POST["c_source"],
	    "c_order"         => $_POST["c_order"],
	    "c_level"         => $_POST["c_level"],
	    "c_aid_update"    => $_SESSION['aid'],
	    "c_dt_update"     => x_date(),
	    "c_summary"       => $_POST["c_summary"],
	    "c_detail"        => $_POST["c_detail2"],
	    "c_pic_url"       => $_POST["c_pic_url"],
	    "c_attachment"    => count($data_att)+$oldData['c_attachment']
	);

	if ($filename!='') $data["c_pic"]= $filename;  //如果图片不为空，就覆盖原来的值
	//如果需要审核的话，将审核状态重新改回待审核
	$ch=getChannelById($oldData["chid"],$db);

	if (!$ch)  exit(ajaxReturn(0,"栏目错误，无法继续！"));
	if ($ch['ch_audit']==1) $data["c_audit"]=0;  //如果栏目需要审核，就重置审核状态

	$db->where('cid', $_POST["cid"]);
	$id = $db->update ('xt_content', $data);
	if ($id){

		$db->insertMulti('xt_content_attachment', $data_att);
		//判断是否要删除以前旧的头图文件
		if ($filename!==''){
			if(!empty($oldData['c_pic']))
			{
				$oldFilename=uploadDirContentPic.$oldData['c_pic'];
				if (file_exists($oldFilename)) unlink($oldFilename);
			}
		}

		exit(ajaxReturn(1,"添加成功。"));
	} 
	else exit(ajaxReturn(0,"添加失败：".$db->getLastError()));

}elseif($_POST["op"]=="delete"){

	if (empty($_POST["cid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["cid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));

	$content=getContentById($_POST["cid"],$db);
	$contentAttachment=getAttachmentByCid($_POST["cid"],$db);

	$db->where('cid', $_POST["cid"]);
	if ($db->delete('xt_content')) {

		//清除附件表数据及文件
		if (trim($content["c_pic"])!='')
		{
			$c_pic=uploadDirContentPic.$content["c_pic"];
			if (file_exists($c_pic)) unlink($c_pic);
		}

		if (!empty($contentAttachment))
		{
			$db->where('cid', $_POST["cid"]);
			$db->delete('xt_content_attachment');
			foreach ($contentAttachment as $key => $value) {
				if (trim($value['ca_detail'])!=''){
					$filename=uploadDirContentAtt.$value['ca_detail'];
					if (file_exists($filename)) unlink($filename);
				}
			}
		}

		exit(ajaxReturn(1,"资料删除成功！"));
	}
	else  exit(ajaxReturn(0,"删除操作失败！数据库操作错误！"));

}elseif($_POST["op"]=="attachmentModify"){

	if (empty($_POST["caid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["caid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	if (empty($_POST['ca_title'])) exit(ajaxReturn(0,"附件的名称未填写。"));
	if (!x_isint($_POST['ca_order'])) exit(ajaxReturn(0,"附件的排序值错误，必须是整数。"));

	$data=array(
		"ca_detail_url" => $_POST['ca_detail_url'],
		"ca_title"      => $_POST['ca_title'],
		"ca_content"    => $_POST['ca_content'],
		"ca_order"      => $_POST['ca_order']
	);

	$db->where('caid', $_POST["caid"]);
	if ($db->update ('xt_content_attachment', $data))
	{
		if ($db->count>0) exit(ajaxReturn(1,"数据更新成功！"));
		else exit(ajaxReturn(0,"要更新的数据已经不存在，可刷新后重新操作！"));
	}
	else
		exit(ajaxReturn(0,"数据更新操作失败！"));

}elseif($_POST["op"]=="attachmentDelete"){

	if (empty($_POST["caid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));	
	if (!x_isint($_POST["caid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));

	//要将文件也删除掉
	$cadata=getAttachmentById($_POST["caid"],$db);
	$filename=uploadDirContentAtt.$cadata['ca_detail'];

	$db->where('caid', $_POST["caid"]);
	if ($db->delete('xt_content_attachment')) {
		if (file_exists($filename)) unlink($filename);  //删除实际文件
		updateAttachmentNumber($cadata['cid'],$db);  //更新附件数量
		exit(ajaxReturn(1,"附件删除成功！"));
	}
	else  exit(ajaxReturn(0,"删除操作失败！数据库操作错误！"));

}elseif($_POST["op"]=="getAuditHistory"){
	if (empty($_POST["cid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));
	if (!x_isint($_POST["cid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$content = $db->rawQuery('SELECT cid,c_title,c_audit from xt_content where cid = ?', Array ($_POST["cid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要获取的数据不存在！"));

	//历史审核记录
	$content[0]['audits']=array();
	$contentAudits = $db->rawQuery('SELECT auid,aid,au_dt_add,au_result,au_memo from xt_audit where cid = ? order by auid desc', Array ($_POST["cid"]));
	if (count($contentAudits)>0){
		//获取管理员的账号和姓名，先将aid放入一个字串再批量获取
		$tmpArr=array();
		foreach ($contentAudits as $key => $value) {
			$tmpArr[$value['aid']]=$value['aid'];
		}
		$str="'".implode("','",$tmpArr)."'";
		$admins=$db->rawQuery('SELECT aid,a_name,a_account from xt_admin where aid in ('.$str.')');

		foreach ($contentAudits as $key => $value){
			$contentAudits[$key]['a_name']='';
			$contentAudits[$key]['a_account']='';
			foreach($admins as $k1=>$v1){
				if (isset($value['aid']) && $value['aid']==$v1['aid']){
					$contentAudits[$key]['a_name']=$v1['a_name'];
					$contentAudits[$key]['a_account']=$v1['a_account'];
					break;
				}
			}
		}

		$content[0]['audits']=$contentAudits;
	}

	exit(ajaxReturn(1,"数据获取成功!",$content[0]));

}else{
	 exit(ajaxReturn(0,"无此操作，无法继续！"));
}

