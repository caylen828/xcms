<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_content","fun_session"));

//如果登录状态不在就直接返回登录状态已过时
if (!isLogin('aid')) exit(ajaxReturn(0,"登录状态已失去，请刷新页面重新登录后再操作！"));

$db = new MysqliDb(getMysqlConfig());

//debug($_POST);

if (!isset($_POST["op"])) exit(ajaxReturn(0,"缺失参数，无法操作！"));

if($_POST["op"]=="get"){
	if (empty($_POST["cid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));
	if (!x_isint($_POST["cid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$content = $db->rawQuery('SELECT * from xt_content where cid = ?', Array ($_POST["cid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要获取的数据不存在！"));

	//附件处理
	$content[0]['attachments']=array();
	$contentAttachment = $db->rawQuery('SELECT caid,ca_detail,ca_detail_url,ca_title,ca_order,ca_content from xt_content_attachment where cid = ? order by ca_order desc,caid', Array ($_POST["cid"]));
	foreach($contentAttachment as $k=>$v){
		$contentAttachment[$k]['url']=uploadUrlContentAtt.$contentAttachment[$k]['ca_detail'];
	}
	if ($db->count>0) $content[0]['attachments']=$contentAttachment;

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


	//格式化一些数据
	$content[0]['c_detail']=trim($content[0]['c_detail']);  //如果返回值是null，前端的kindeditor控件会报错
	exit(ajaxReturn(1,"数据获取成功!",$content[0]));

}elseif($_POST["op"]=="audit"){
	if (!isset($_POST["au_result"])) exit(ajaxReturn(0,"请选择审核结果，审核通过 或者 审核不通过！"));

	if (empty($_POST["cid"])) exit(ajaxReturn(0,"缺失参数，无法继续！"));
	if (!x_isint($_POST["cid"])) exit(ajaxReturn(0,"参数格式错误，无法继续！"));
	$content = $db->rawQuery('SELECT * from xt_content where cid = ?', Array ($_POST["cid"]));
	if ($db->count<1) exit(ajaxReturn(0,"要审核的数据已不存在！"));

	$data = Array (
		"aid"        => $_SESSION["aid"],
	    "au_dt_add"  => x_date(),
	    "au_result"  => $_POST["au_result"],
	    "au_memo"    => $_POST["au_memo"],
	    "cid"        => $_POST["cid"]
	);

	$id = $db->insert ('xt_audit', $data);
	if ($id){
		//更改content表中的记录
		$data2=array(
			"c_audit"=>$_POST["au_result"]
		);
		$db->where ('cid', $_POST["cid"]);
		if ($db->update ('xt_content', $data2)) exit(ajaxReturn(1,"审核成功。"));
		else exit(ajaxReturn(0,"审核记录已插入，不过审核结果更改失败，请找技术人员检查原因。"));
	}else exit(ajaxReturn(0,"审核失败：".$db->getLastError()));

}else{
	 exit(ajaxReturn(0,"无此操作，无法继续！"));
}

