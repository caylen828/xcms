<?php
/*
	根据网站栏目编号获取栏目资料
	$chid是栏目编号
	$db是数据库连接变量
*/
function getChannelById($chid,$db){
	$ch = $db->rawQuery('select * from xt_channel where chid = ?', Array ($chid));
	if ($db->count>0) return $ch[0];
	else return false;
}

/*
	判断下方是否还有子栏目，返回子栏目数据
*/
function getSunChannelById($chid,$db){
	$ch = $db->rawQuery('select * from xt_channel where ch_father_id = ?', Array ($chid));
	if ($db->count>0) return $ch[0];
	else false;
}


/*
	根据aid获取相关资料
*/
function getAdminById($aid,$db){
	$a = $db->rawQuery('select * from xt_admin where aid = ?', Array ($aid));
	if ($db->count>0) return $a[0];
	else false;
}

/*
	根据cid获取资料内容
*/
function getContentById($cid,$db){
	$a = $db->rawQuery('select * from xt_content where cid = ?', Array ($cid));
	if ($db->count>0) return $a[0];
	else false;
}

/*
	根据cid获取审核资料
*/
function getAuditById($cid,$db){
	$a = $db->rawQuery('select * from xt_audit where cid = ? order by auid desc', Array ($cid));
	if ($db->count>0) return $a;
	else false;
}

/*
	根据caid获取附件资料，只有单条数据
*/
function getAttachmentById($caid,$db){
	$a = $db->rawQuery('select * from xt_content_attachment where caid = ?', Array ($caid));
	if ($db->count>0) return $a[0];
	else false;
}

/*
	根据cid获取所有附件资料，可能有多条数据
*/
function getAttachmentByCid($cid,$db){
	$a = $db->rawQuery('select * from xt_content_attachment where cid = ? order by ca_order desc,caid', Array ($cid));
	if ($db->count>0) return $a;
	else false;
}

/*
	更新附件数量
*/
function updateAttachmentNumber($cid,$db){
	$a = $db->rawQuery('update xt_content set c_attachment=(select count(*) from xt_content_attachment where cid=?)  where cid = ?', Array ($cid,$cid));
	return $db->count;  //返回更新的数量
}

/*
	根据contentId获取附件数量
*/
function getAttachmentNumber($cid,$db){
	$a = $db->rawQuery('select count(*) as c from xt_content_attachment where cid=?', Array ($cid));
	return $a[0]['c'];
}


/*
	获取可审核栏目清单，参数为sql的where条件，返回值需要输出chid，ch_name,ch_father_id，count
*/
function getAuditChannels($whereSql,$db){
	if ($whereSql!='') $whereSql=' where '.$whereSql;
	$a = $db->rawQuery('select * from xt_channel '.$whereSql.' order by ch_father_id,ch_order desc,chid');
	if ($whereSql=='') $b = $db->rawQuery('select chid,count(*) as c from xt_content where c_audit<=0 group by chid');
	else $b = $db->rawQuery('select chid,count(*) as c from xt_content '.$whereSql.' and c_audit<=0 group by chid');

	$kcount=array();
	foreach ($b as $k => $v){
		$kcount[$v['chid']]=$v['c'];
	}

	foreach ($a as $key => $value) {
		if (!isset($kcount[$value['chid']])) $a[$key]['count']=0;
		else $a[$key]['count']=$kcount[$value['chid']];
	}
	return $a;
}

/*
	根据chid获取content，返回值要包含附件，用来取列表
	获取结果为多条结果
	$startNumber是从第几条开始
	$number是获取几条数据
	$column，取哪些字段，一般列表页来说，取标题、副标、时间（更新时间优于创建时间）、头图和头图地址、简介就行了。如果是详情页，就直接按照cid取就好了，用对应函数
	附件不处理
*/

function getContentsByChid($chid,$db,$number=1,$startNumber=0,$column='cid,c_title,c_title2,c_author,c_source,c_level,c_dt_add,c_dt_update,c_summary,c_pic,c_pic_url,c_attachment'){
	$a = $db->rawQuery('select '.$column.' from xt_content where chid = ? and c_audit>0 order by c_level desc,c_order desc,cid desc limit ?,?', Array ($chid,$startNumber,$number));

	if ($db->count>0) return $a;
	else return false;
}
?>