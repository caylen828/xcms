<?php

function xlsToCsv($execFile,$xlsFile)  //excel文件转csv文件，一个是可执行文件路径，一个是XLS文件路径
{
	if (!file_exists($execFile) || !file_exists($xlsFile))
	{
		return false;  //传递参数错误！
	}
	
	if (strtolower(substr($execFile,-4))!=".exe")
	{
		return false;
	}
	
	if (strtolower(substr($xlsFile,-4))!=".xls")
	{
		return false;
	}
	
	//取到原始文件去掉扩展名部分
	$sourceFile1=substr($xlsFile,0,strlen($xlsFile)-4);
	$len=strlen($sourceFile1);
	//日志文件名
	$logFile=$sourceFile1.".status";
	file_put_contents($logFile,"0");  //开始处理
	
	$r=exec($execFile." ".$xlsFile,$out,$status);
	
	$fileDir=dirname($xlsFile);
	
	$d=scandir($fileDir);
	$csvFile="";
	foreach($d as $v)
	{
		$v=$fileDir."/".$v;
		if (substr($v,0,$len)==$sourceFile1 && substr($v,-4)==".csv")
		{
			$csvFile=$v;
			break;
		}
	}
	
	if ($status===0)
	{
		file_put_contents($logFile,"1");  //处理成功
		return array("sourceFile"=>$xlsFile,"logFile"=>$logFile,"csvFile"=>$csvFile);  //返回结果文件名和日志文件名
	}else
	{
		file_put_contents($logFile,"-1");  //处理失败
		return false;
	}
}

function csvToArray($csvFile)
{
	if (!file_exists($csvFile))
	{
		return false;  //传递参数错误！
	}
	
	if (strtolower(substr($csvFile,-4))!=".csv")
	{
		return false;
	}
	
	$a=file($csvFile);
	$ra=array();

	$tmpint="";
	$finished=1;  //结束标识,1已结束，0未结束
	for($i=1;$i<=count($a);$i++)
	{
		//echo trim(trim($a[$i]),'"')."<br><br>";
		$bstr=trim(trim($a[$i-1]));
		if (substr($bstr,-1)!='"')
		{
			$finished=0;
			$tmpint=trim($tmpint." ".$bstr);
		}else
		{
			if ($finished==0) 
			{
				$tmpint=trim($tmpint." ".$bstr);
			}else
			{
				$tmpint=$bstr;
			}
			$finished=1;
		}
		
		if ($finished==1)
		{
			$b=explode('";"',trim($tmpint,'"'));
			$ra[]=$b;
			$tmpint="";
		}
		
	}
	return $ra;
}

function csvArrayUnique($a)  //对CSV中的数据按邮箱去重，参数是多维数组
{
	$tmpa=array();
	$newa=array();
	$b=array();
	foreach($a as $k => $v)
	{
		$tmpa[$k]=$v[0];
	}
	$newa=array_unique($tmpa);

	foreach($a as $k => $v)
	{
		if (isset($newa[$k]))
		{
			$b[]=$v;
		}
	}
	return $b;
}

function linkUnique($a)  //结果返回一个数组，array('rep'=>$rep,'err'=>$err,'arr'=>$arr)
{
	$rep=$err=0;
	$b=array();
	$allCount=count($a);
	$uniqueArray=csvArrayUnique($a);
	$uniqueCount=count($uniqueArray);
	$rep=$allCount-$uniqueCount;
	foreach($uniqueArray as $k => $v)
	{
		if (is_email($v[0]))
		{
			$b[]=$v;
		}else
		{
			$err++;
		}
	}

	return array("rep"=>$rep,"err"=>$err,"arr"=>$b);
}

// 原函数
//function linkUnique($arr, $key) {
//    $rep = $err = 0;
//    $tmp_arr = array();
//    foreach ($arr as $k => $v) {
//        if (isset($v[$key])&&in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true
//            unset($arr[$k]);
//            $rep++;
//        } else if(isset($v[$key])&&is_email($v[$key])){
//            $tmp_arr[] = $v[$key];
//        }else{
//            unset($arr[$k]);
//            $err++;
//        }
//    }
//    
//    return array('rep'=>$rep,'err'=>$err,'arr'=>$arr);
//}

/**
 * 解析xls/xlsx/csv文件
 */
class XlsAndCsv{
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// xls解析
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * 利用应用程序将xls解析成csv
	 * @param unknown_type $execFile
	 * @param unknown_type $xlsFile
	 * 返回解析后文件名，失败返回false
	 */
	public static function funXlsToCsv($execFile, $xlsFile){
		if (strtolower(substr($execFile,-4))!=".exe") return false;
		if (strtolower(substr($xlsFile,-4))!=".xls") return false;
		if (!file_exists($execFile) || !file_exists($xlsFile)) return false;
		
		// 开始解析($r和$out无意义)
		$r=exec($execFile." ".$xlsFile,$out,$status);
		
		if ($status!==0) return false; // 解析失败
		
		// 查找生成文件
		$csvFile = false;
		$sourceFile = substr(basename($xlsFile), 0, -4); // 光杆名，无路径，扩展名
		$sourceLen = strlen($sourceFile); // 光杆名长度
		$fileDir = dirname($xlsFile);
		$d=scandir($fileDir); // 遍历文件
		foreach($d as $v){
			if (substr($v,0,$sourceLen)==$sourceFile && strtolower(substr($v,-4))==".csv"){
				$csvFile=$fileDir."/".$v;
				break;
			}
		}
		return $csvFile;
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// xlsx解析
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * 解压并读取内容
	 * @param unknown_type $xlsxFile
	 * @param unknown_type $xlsxFileZipDir
	 */
	public static function funXlsxToArr($xlsxFile, $xlsxFileZipDir=null){
		$rt = array();
		if (strtolower(substr($xlsxFile,-5))!=".xlsx") return $rt; // 判定
		if (!file_exists($xlsxFile)) return $rt; // 判定
		if ($xlsxFileZipDir==null) $xlsxFileZipDir = substr($xlsxFile, 0, -5)."/";
		self::mkdirs($xlsxFileZipDir); // 不管文件夹有几层
		if(!is_dir($xlsxFileZipDir)) return $rt; // 判定
		
		// zip解压缩
		$fileNum = XlsAndCsv::zipArchiveDecode($xlsxFile, $xlsxFileZipDir);
		if($fileNum==0) return $rt; // 判定
		// 内容文件读取
		$ctArr = self::readStr1($xlsxFileZipDir."xl/sharedStrings.xml");
		// 读取到数组
		$dbArr = self::readStr2($xlsxFileZipDir."xl/worksheets/sheet1.xml", $ctArr);
		return $dbArr;
	}
	
	/**
	 * 读第一个文件
	 */
	private static function readStr1($fileName){
		$str1 = file_get_contents($fileName);
		$rt = explode("</si><si>", $str1);
		foreach ($rt as &$one) {
			$one = htmlspecialchars_decode(strip_tags($one));
		}
		// 去掉首行回车符号
		if(substr($rt[0], 0, 2)=="\r\n"){
			$rt[0] = substr($rt[0], 2);
		}
		return $rt;
	}
	
	/**
	 * 读第二个文件
	 * @param unknown_type $fileName
	 * @param unknown_type $arr      第一个文件返回的数组
	 */
	private static function readStr2($fileName, $arr){
		$a = ord('A');
		$rt = array();
		
		// 循环读取
		$length = 1024*1024*32;
		$part = '';
		$fp = fopen($fileName, 'r');
		while(!feof($fp)){
			// 将不整齐的数据放入下一次循环
			$part .= fread($fp, $length);
			$arr0 = explode('</row>', $part);
			if(!feof($fp)){
				$count = count($arr0)-1;
				$part = $arr0[$count];
				array_splice($arr0, $count);
			}
			// 开始循环
			foreach ($arr0 as $one0) {
				$rtItem = array(); 
				$arr1 = explode('</c>', $one0);
				$id=$a;
				foreach ($arr1 as $one1) {
					$pos0 = strpos($one1, '<c r="');
					if($pos0===false) break;
					$pos1 = strpos($one1, 't="s"', $pos0+8);
					// 空列补齐
					$current = ord($one1[$pos0+6]); // 当前列
					$dt = $current-$id;             // 上一列
					$id = $current+1;               // 下一列
					for ($i = 0; $i < $dt; $i++) {
						$rtItem[]='';
						$isEmpty = true;
					}
					// 填充内容
					$v = strip_tags($one1);
					if($pos1===false){
						$rtItem[]=$v;
					}else{
						$rtItem[]=$arr[intval($v)];
					}
				}
				if(count($rtItem)>0){
					$rt[] = $rtItem;
				}
			}
		}
		fclose($fp);
		return $rt;
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	// 数组读写
	/////////////////////////////////////////////////////////////////////////////////////////////////////
	
	/**
	 * 极速数组写入
	 * @param unknown_type $arr
	 * @param unknown_type $td
	 * @param unknown_type $tr
	 */
	public static function funPutAr($arr, $td=null, $tr=null){
		$td = $td==null ? chr(1):$td; // 默认值
		$tr = $tr==null ? chr(2):$tr; // 默认值
		$rt = '';
		foreach ($arr as $one) {
			$line = '';
			foreach ($one as $v) {
				if($line!=null) $line.=$td;
				$line.=$v;
			}
			if($rt!=null) $rt.=$tr;
			$rt.=$line;
		}
		return $rt;
	}
	
	/**
	 * 极速数组读取
	 * @param unknown_type $str
	 * @param unknown_type $td
	 * @param unknown_type $tr
	 */
	public static function funGetAr($str, $td=null, $tr=null){
		$td = $td==null ? chr(1):$td; // 默认值
		$tr = $tr==null ? chr(2):$tr; // 默认值
		$rt = array();
		$arr = explode($tr, $str);
		foreach ($arr as $one) {
			$rt[] = explode($td, $one);
		}
		return $rt;
	}
	
	/**
	 * 数组写入CSV(略逊极速)
	 * @param unknown_type $arr
	 * @param unknown_type $td
	 * @param unknown_type $tr
	 */
	public static function funPutCsv($arr, $td=",", $tr="\r\n"){
		$rt = '';
		foreach ($arr as $one) {
			$line = '';
			foreach ($one as $v) {
				if($line!=null) $line.=$td;
				if(false===strpos($v, '"') && false===strpos($v, $tr) && false===strpos($v, $td)){
					$line.=$v;
				}else{
					$line.='"'.str_replace('"', '""', $v).'"';
				}
			}
			$rt.=$line.$tr;
		}
		return $rt;
	}
	
	/**
	 * 数组读取CSV(略逊极速)
	 * @param unknown_type $str
	 * @param unknown_type $td
	 * @param unknown_type $tr
	 */
	public static function funGetCsv($str, $td=",", $tr="\r\n"){
		$rt = array();     // 最终结果
		$rtItem = array(); // 单行结果
		$rtStr = null;     // 单字段结果
		$findNum = 0;
		
		// 切割
		$lines = explode($tr, $str);
		$end = count($lines)-1;
		if($lines[$end]==null){
			unset($lines[$end]);
		}
		// 处理
		foreach ($lines as $line) {
			$items = explode($td, $line);
			$k=0; // 列数
			foreach ($items as $item) {
				if($findNum>0){
					$flg = $k==0 ? $tr:$td; // 分隔符
					$findNum += substr_count($item, '"');
					if($findNum%2==1){
						$rtStr .= $flg.$item;
					}else{
						$rtItem[] = str_replace('""', '"', $rtStr.$flg.substr($item, 0, -1));
						$rtStr=null; // 清理
						$findNum=0; // 清理
					}
				}else if($item!=null && $item[0]=='"'){
					$findNum += substr_count($item, '"');
					if($findNum%2==1){
						$rtStr = substr($item, 1);
					}else{
						$rtItem[] = str_replace('""', '"', substr($item, 1, -1));
						$rtStr=null; // 清理
						$findNum=0; // 清理
					}
				}else{
					$rtItem[] = $item;
				}
				$k++; // 列数
			}
			if($findNum==0){
				$rt[] = $rtItem;
				$rtItem = array();
			}
		}
		return $rt;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// zip压缩
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	public static function zipArchiveDecode($fromZip, $toFolder) {
		$i=0;
		if($toFolder != null && file_exists($fromZip)){
			// 判断Window和Linux
			$isSysLinux = substr(__FILE__,0,1)=='/';
			// 安全处理机制
			$toFolder = str_replace("\\", "/", trim($toFolder));
			if("/"!=substr($toFolder, -1)) $toFolder .= "/";
			self::mkdirs($toFolder); // 不管文件夹有几层
			// 解压缩程序
			$z = new ZipArchive();
			if($z->open($fromZip)){ // open对应close
				$n = $z->numFiles;
				for($i=0; $i<$n; $i++){
					$file_name = $z->getNameIndex($i);
					UsefulUtil::printLog('temp', 'asyncUpdateCustomers', $i.'=index='.$file_name);
					// 如果是Linux系统，对文件名进行转换
					if($isSysLinux){
						$ctype = mb_detect_encoding($file_name, array('ASCII','EUC-CN','UTF-8'));
						if($ctype=='EUC-CN') $file_name = iconv('EUC-CN', 'UTF-8', $file_name); // GB2312转UTF-8
					}
					// 如果是文件夹，则创建文件夹
					str_replace("\\", "/", $file_name);
					$pos = strrpos($file_name, "/");
					if($pos!==false){
						self::mkdirs($toFolder.substr($file_name, 0, $pos)); // 不管文件夹有几层
					}
					if("/"!=substr($file_name, -1)){
						file_put_contents($toFolder.$file_name, $z->getFromIndex($i));
					}
				}
				$z->close(); // open对应close
			}
		}
		return $i;
	}
	
	public static function zipArchiveEncode($fromFolder, $toZip, $isOverWrite=false) {
		if(is_dir($fromFolder) && ($isOverWrite || !file_exists($toZip))){
			// 判断Window和Linux
			$isSysLinux = substr(__FILE__,0,1)=='/';
			$z = new ZipArchive();
			if($z->open($toZip, $isOverWrite ? ZIPARCHIVE::OVERWRITE:ZIPARCHIVE::CREATE)){ // open对应close
				// 安全监测
				$fromFolder = str_replace("\\", "/", trim($fromFolder));
				if("/"==substr($fromFolder, -1)) $fromFolder=substr($fromFolder, 0, -1);
				// 初始化
				$arr = array();
				$len = 0;
				$arr[$len++] = $fromFolder;
				$exclusiveLength = strlen($fromFolder)+1; // 在zip里的相对路径
				// 循环调用
				while($len>0){
					$dir = $arr[--$len];
					$handle = opendir($dir);
					while ($f0 = readdir($handle)) {
						if($f0!='.' && $f0!='..'){
							$f = $dir.'/'.$f0;
							$l = substr($f, $exclusiveLength);
							// 如果是Linux系统，对文件名进行转换
							if($isSysLinux){
								$ctype = mb_detect_encoding($l, array('ASCII','EUC-CN','UTF-8'));
								if($ctype=='UTF-8') $l = iconv('UTF-8', 'EUC-CN', $l); // UTF-8转GB2312
							}
							if(is_dir($f)){
								$z->addEmptyDir($l);
								$arr[$len++] = $f;
							}else if(is_file($f)){
								$z->addFile($f, $l);
							}
						}
					}
					closedir($handle);
				}
				$z->close(); // open对应close
			}
		}
	}
	
	/**
	 * 创建目录
	 */
	public static function mkdirs($dir){
		if(!is_dir($dir)){
			$father = dirname($dir);
			if(!is_dir($father)){
				self::mkdirs($father);
			}
			mkdir($dir);
		}
	}
	
}




