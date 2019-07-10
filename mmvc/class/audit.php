<?php
class audit
{
	var $session='';

	var $error='';

	function __construct($session='')
	{
		if ($session=='') $this->session=$_SESSION;
		else  $this->session=$session;

		if (!isset($this->session['aid'])) {$this->error='管理员参数错误，无法继续。';return false;}
		if (!isset($this->session['a_type'])) {$this->error='管理员参数错误，无法继续。';return false;}
		if (!isset($this->session['a_audits'])) {$this->error='管理员参数错误，无法继续。';return false;}
		if (trim($this->session['a_audits'])!=''){
			if (!$this->session['a_audits']=(array)json_decode($this->session['a_audits'])) { $this->error='权限参数解析错误，无法继续。';return false;}
		}else $this->session['a_audits']='';
		
		return true;
	}

	//获取用户的栏目权限，不限制（只有超级管理员不限制）、限制（具体限制哪些栏目），直接返回sql的where部分，不限制就返回空条件
	function limitChannelsSql(){
		$where='';
		if ($this->session['a_type']==1) return $where;

		if (empty($this->session['a_audits']['r']) && empty($this->session['a_audits']['w']) && empty($this->session['a_audits']['d'])){
			$where="chid in ('-1')";  //获取一个不存在的值，这样就取不到结果了
			return $where;
		}

		$str='';
		$tmpStr='';
		if (!empty($this->session['a_audits']['r'])){
			$tmpStr=$this->session['a_audits']['r'];
		}
		if (!empty($this->session['a_audits']['w'])){
			if ($tmpStr!='') $tmpStr=$tmpStr.','.$this->session['a_audits']['w'];
			else $tmpStr=$this->session['a_audits']['w'];
		}
		if (!empty($this->session['a_audits']['d'])){
			if ($tmpStr!='') $tmpStr=$tmpStr.','.$this->session['a_audits']['d'];
			else $tmpStr=$this->session['a_audits']['d'];
		}

		$tmpArr=explode(',',$tmpStr);
		$tmpArr2=array();
		foreach ($tmpArr as $key => $value) {
			$tmpArr2[$value]=1;  //将栏目id合并到key
		}

		foreach ($tmpArr2 as $key => $value) {
			$str=$str.",".$key;  //将key取出放入字串中。
		}

		$where="chid in ('".str_replace(",", "','", trim($str,','))."')";
		return $where;

	}

	//获取用户对某个栏目的读取、编辑、删除的权限
	function limitChannel($chid){

		if ($this->session['a_type']==1){
			$arr=array("r"=>true,"w"=>true,"d"=>true);
			return $arr;
		}

		$arr=array(
			"r"=>false,
			"w"=>false,
			"d"=>false
		);

		if (strpos(','.$this->session['a_audits']['r'].',',','.$chid.',')!==false) $arr['r']=true;
		if (strpos(','.$this->session['a_audits']['w'].',',','.$chid.',')!==false) $arr['w']=true;
		if (strpos(','.$this->session['a_audits']['d'].',',','.$chid.',')!==false) $arr['d']=true;
		return $arr;
	}


	//获取用户是否只获取自己的数据，用于资料列表页。如果返回1就是只处理自己的数据，如果返回0是不限制。只限制普通管理员。判断返回值时请用===，强制等于
	function isOneself(){
		if ($this->session['a_type']==1) return 0;
		elseif($this->session['a_type']==3 || $this->session['a_type']==2){
			return $this->session['a_audits']['oneself'];
		}else{
			$this->error='管理员类型错误！';
			return false;
		}
	}

	//获取用户是否有审核权限，只要是超级管理员或者审核员就有审核功能。
	function isAuditer(){
		if ($this->session['a_type']==1 || $this->session['a_type']==2) return true;
	}

	//获取用户可以审核哪些栏目，$type=='string'返回字串格式，$type=='array'返回数组格式，$type=='sql'返回数据库语句
	function auditChannels($type='string'){
		if (empty($this->session['a_audits']['a'])){
			if ($type=='string') return '';
			elseif ($type=='array') return array();
			else return '';
		}else{
			if ($type=='string') return $this->session['a_audits']['a'];
			elseif ($type=='array') return explode(',',$this->session['a_audits']['a']);
			else {
				$s=" chid in ('".str_replace(",", "','", $this->session['a_audits']['a'])."')";
				return $s;
			}
		}
	}

}

?>