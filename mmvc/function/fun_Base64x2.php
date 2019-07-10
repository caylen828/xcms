<?php

function base64_string($str,$type="en")  //$type=en就说明是转码，$type=de就是解码
{
	$b=array("C","z","M","j","r","E","s","3","6","A","o","V","u","G","0","b","f","K","m","S","c","t","1","D","Y","N","q","g","p","7","a","k","8","l","e","P","x","B","Q","Z","w","R","F","O","H","J","I","y","4","5","L","9","2","i","_","/","d","v","X","n","T","h","W","U","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","0","1","2","3","4","5","6","7","8","9","+","/");
	$c="";
	
	if ($type=="en")
	{
		$a=array_slice($b,64);
		for($i=0;$i<strlen($str);$i++)
		{
			$tmp=substr($str,$i,1);
			foreach($a as $k => $v)
			{
				//echo $k.".".$v." ";
				if ($v==$tmp)
				{
					//echo $b[$k];
					
					$c=$c.$b[$k];
					break;
				}
			}
		}
	}else
	{
		for($i=0;$i<strlen($str);$i++)
		{
			$tmp=substr($str,$i,1);
			foreach($b as $k => $v)
			{
				if ($v==$tmp)
				{
					$c=$c.$b[$k+64];
					break;
				}
			}
		}
	}
	
	return $c;
}

function base64x2_encode($str)  //base64后转换编码
{
	return base64_string(base64_encode($str));
}

function base64x2_decode($str)  //base64后转换编码
{
	return base64_decode(base64_string($str,"de"));
}

function urlsafe_base64_encode($string) {
	$data = base64_encode($string);
	$data = str_replace(array('+','/','='),array('-','_',''),$data);
	return $data;
}

function urlsafe_base64_decode($string) {
	$data = str_replace(array('-','_'),array('+','/'),$string);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}

/*
$str='{"result":{"code":102,"time":1398502470,"message":"data not exist!"}}';
echo $str;
echo "<br>";
echo base64_encode($str);
echo "<br>";
echo $tmp=base64x2_encode($str);
echo "<br>";
echo base64x2_decode($tmp);
*/

/**
 * TBE曝光点击监控值解码
 */
class X2Code{
	private $codeFrom = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	private $codeTo   = 'CzMjrEs36AoVuG0bfKmSct1DYNqgp7ak8lePxBQZwRFOHJIy45L92i_/dvXnThWU';
	private $arrayEncode = array();
	private $arrayDecode = array();
	public function __construct() {
		$n = strlen($this->codeFrom);
		for($i=0; $i<$n; $i++){
			$cfrom = $this->codeFrom[$i];
			$cto   = $this->codeTo[$i];
			$this->arrayEncode[$cfrom]=$cto;
			$this->arrayDecode[$cto]=$cfrom;
		}
	}
	public function encode($str){
		return $this->code(base64_encode($str), $this->arrayEncode);
	}
	public function decode($str){
		return base64_decode($this->code($str, $this->arrayDecode));
	}
	private function code($str, &$arr){
		$rt='';
		$n = strlen($str);
		for($i=0; $i<$n; $i++){
			$c = $str[$i];
			if(isset($arr[$c])){
				$rt .= $arr[$c];
			}else{
				$rt .= $c;
			}
		}
		return $rt;
	}
}
/**
 * TBE曝光点击监控值解码
 */
class TbeCode{
	private $codeFrom = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	private $codeTo   = 'vqh0ecmfwxz4bs2i6kdg73t918apjoyl5runEFJZMWCKSXLNQHVBUYAGRPTOID';
	private $arrayEncode = array();
	private $arrayDecode = array();
	public function __construct() {
		$n = strlen($this->codeFrom);
		for($i=0; $i<$n; $i++){
			$cfrom = $this->codeFrom[$i];
			$cto   = $this->codeTo[$i];
			$this->arrayEncode[$cfrom]=$cto;
			$this->arrayDecode[$cto]=$cfrom;
		}
	}
	public function encode($str){
		return $this->code($str, $this->arrayEncode);
	}
	public function decode($str){
		return $this->code($str, $this->arrayDecode);
	}
	private function code($str, &$arr){
		$rt='';
		$n = strlen($str);
		for($i=0; $i<$n; $i++){
			$c = $str[$i];
			if(isset($arr[$c])){
				$rt .= $arr[$c];
			}else{
				$rt .= $c;
			}
		}
		return $rt;
	}
	public static function getDecode($str){
		$rt = null;
		if(empty($str)) return $rt;
		if($str[0]=='{'){
			$rt = json_decode($str, true);
		}else{
			$a = explode("\x01", $str);
			@$rt = array(
					'bid'        =>$a[0],
					'action'     =>(int)$a[1],
					'userId'     =>$a[2],
					'outerUserId'=>$a[3],
					'ip'         =>$a[4],
					'platform'   =>$a[5],
					'campaignId' =>$a[6],
					'adUnitId'   =>$a[7],
					'creativeId' =>$a[8],
					'url'        =>$a[9],
					'referer'    =>$a[10],
					'mtime'      =>$a[11],
					'pid'        =>$a[12],
					'size'       =>$a[13],
					'screen'     =>$a[14],
					'minPrice'   =>$a[15],
					'winPrice'   =>$a[16],
					'bidPrice'   =>$a[17],
					'ua'         =>$a[18],
			);
		}
		return $rt;
	}
	
}

?>