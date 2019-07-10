<?php 
//分页类
/*
	用于mysql数据库的分页，使用mysqli的类库。
	目前使用的类库只适用于mmvc中的类库
	作者：Jacky 郁桦杰
*/
class xlist
{
	
	var $table="";	        //数据表名
	var $columns="";    	//所有要列出的字段，格式如： name1,name2,name3
	var $where="";      	//查询用条件，格式如：(name1=20)and(name2 like '%你%')
	var $order="";      	//排序方式，默认可以为空
	
	var $count=0;	        //一共有多少条记录
	
	var $fnumber=10;     	//每页列出多少条记录，如果小于0就列出所有数据
	
	var $db;		        //数据库连接，必须要初始化，使用mmvc中的mysqli专用类库
	
	var $fid=1;		        //当前第几页
	var $pagesCount=0;   	//分页用，一共有多少页
	var $fromNumber=1;   	//分页用，从第几条开始取记录
	var $nowPageCount=0;  	//当前页有多少条记录
	
	var $error; 	        //错误信息
	
	var $sql="";	     	//生成的SQL语句
	var $countSql="";	    //生成的统计SQL语句
	var $datas;		        //$datas[0][]第0行为字段名，$datas[0][1]从第1行开始为数据
	
	//-----------------分页相关-----------------------

	var $showCount    =10;  //要显示的页码数量
	var $showStart    =1 ;  //起始分页码
	var $showEnd      =0 ;  //结束分页码，需要计算和初始化
	var $showPrev     =0 ;  //上一页页码
	var $showNext     =0 ;  //下一页页码

	//-----------------分页结束-----------------------



	/*  __construct函数的参数说明
 		初始化资料 $db为数据库连接
 		$pars=array(
			"table"=>"",       //表名
			"columns" => "*",  //字段
			"where"=>"",       //where条件
			"order"=>"",       //order by条件
			"fnumber"=>10,     //每页的数量
			"fid"=>1           //当前页码

		);
	*/
	function __construct($db,$pars)
	{

		if (!$db)
		{
			$this->error="数据库连接参数有问题，请确认。";
			return false;
		}else
		{
			$this->db=$db;
		}
		
		if ($pars["table"]=='')  
		{
			$this->error="数据表名为空。";
			return false;
		}else
		{
			$this->table=$pars["table"];
		}
		
		if ($pars["columns"]=='')
		{
			$this->error="此数据表字段名没有填写，无法建立正确的SQL语句。";
			return false;
		}else
		{
			$this->columns=$pars["columns"];
		}
		$this->sql="select ".$this->columns." from ".$this->table;	//把SQL连接起来，这是第一次连接。
		$this->countSql="select count(*) as c from ".$this->table;	//这是统计语句
		
		if ($pars["where"]!='')
		{
			$this->where=$pars["where"];
			$this->sql=$this->sql." where ".$this->where;	//把SQL连接起来，这是第二次连接。如果条件存在的话。
			$this->countSql=$this->countSql." where ".$this->where;  //统计语句加条件
		}
		
		if ($pars["order"]!='')
		{
			$this->order=$pars["order"];
			$this->sql=$this->sql." order by ".$this->order;	//把SQL连接起来，这是第三次连接。如果排序条件存在的话。
		}
		
		if (x_isint($pars["fid"])==false || $pars["fid"]<0)
		{
			$this->error="页码必须是大于0的整数。";
			return false;
		}

		$this->fid=$pars["fid"];

		if (x_isint($pars["fnumber"])==false || $pars["fnumber"]<0)
		{
			$this->error="每页显示的数量必须是大于0的整数。";
			return false;
		}
		$this->fnumber=$pars["fnumber"];

		//mysql拥有limit方法，计算limit的相关数据，可以根据当前页码和每页取多少条的数量，来计算limit
		$fromNumber=($this->fid-1)*$this->fnumber;  //mysql中的limit从0开始

		$countRe = $db->rawQuery($this->countSql);
		$this->count=$countRe[0]["c"];
		if ($this->count==0)
		{
			$this->error="数据为空。";
			return false;
		}

		if ($fromNumber>$this->count) $fromNumber=0;
		/*
		if ($fromNumber>$this->count)
		{
			$this->error="要获取的分页数据不存在。";
			return false;
		}
		*/

		//计算总共有多少页
		$this->pagesCount=ceil($this->count / $this->fnumber);

		//获取实际数据
		$this->sql=$this->sql." limit ".$fromNumber.",".$this->fnumber;
		$this->datas = $db->rawQuery($this->sql);
		
		if (!$this->datas)
		{
			$this->error="数据获取失败！";
			return false;
		}

		$this->nowPageCount=$db->count;

		return true;
	}
	

	/*
		计算分页码，$showCount是要显示的页码数量
	*/
	function showPage($showCount=10)
	{

		//当前页码是$this->fid;如果页码本身大于总页数，就将最后一页页码赋值给要显示的当前页码
		if ($this->fid>$this->pagesCount) $this->fid=$this->pagesCount;

		$this->showCount=$showCount;

		if ($this->showCount>$this->pagesCount) {
			 $this->showCount=$this->pagesCount;  //如果要显示的页码数大于总页数就直接赋值，不用计算了
			 $this->showStart=1;
			 $this->showEnd=$this->showCount;
		}else{
			/*
				如果总的实际页码数量大于要显示的页码数量，那就按照fid值计算要输出的页码
				有三种处理情况。
				1. 前面少
				2. 后面少
				3. 前后都充足
			*/
			$a=floor($showCount/2);
			$b=ceil($showCount/2);

			$this->showStart=$this->fid-$a+1;  //这个时候计算出来的起始页码有可能是小于1的。
			$this->showEnd=$this->fid+$b;      //这个时候尾页有可能大于总页码数

			if ($this->showStart<1) {
				//起始页码如果小于1，就将前面部分往后挪
				$this->showEnd=$this->showEnd+(1-$this->showStart);
				$this->showStart=1;
			}elseif ($this->showEnd>$this->pagesCount) {
				//如果结束值大于总页码数，就往前挪
				$this->showStart=$this->showStart-($this->showEnd-$this->pagesCount);
				$this->showEnd=$this->pagesCount;
			}
		}

		$this->showPrev=$this->fid-1;
		if ($this->showPrev<1) $this->showPrev=1;
		
		$this->showNext=$this->fid+1;
		if ($this->showNext>$this->pagesCount) $this->showNext=$this->pagesCount;

	}

}


?>