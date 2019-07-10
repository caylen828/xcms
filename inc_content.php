<div class="row">
  <div class="col-xs-12 section-container-spacer">
<?php
if (x_isint($par['cid'])===true && $data=getContentById($par['cid'],$db))
{

	$atts=getAttachmentByCid($par['cid'],$db);  //获取所有附件，并以title为key放入一个数组
	$attArray=array();
    if ($atts){
        foreach($atts as $key => $value){
            $attArray[$value['ca_title']]=$value;
        }
    }

?>
    <h1 style="padding:0px 0px 20px 0px;"><?php echo $data['c_title'];?></h1>
    <p><?php
    $c=x_html_back($data['c_detail']);
    $preg= '/{{[\s\S]*?}}/i';
    preg_match_all($preg,$c,$match);
    //var_dump($match[0]);

    $rep=array();  //要输出的内容
    foreach($match[0] as $k => $v){
    	//记录$rep的key
    	$rep[$v]='';

        //判断内容，是否是imagesList，如果是就直接输出附件列表，以图片瀑布流的形式
        if($v=="{{imagesList}}"){
            imagesList($db,$par['cid']);
            continue;
        }

    	//解析内容
    	$result=(array)json_decode("{".trim(trim($v,"{"),"}")."}");

    	if (!$result || empty($result)){
    		$rep[$v]='<p><h3><font color=red>注意：</font>框架模板解析错误，请修改，'.$v.'</h3></p>';
    		continue;
    	}

    	//判断处理类型是否是audio，目前只支持audio类型的处理
    	if (!isset($result['type'])){
    		$rep[$v]='<p><h3><font color=red>注意：</font>框架模板类型值type没有设定，'.$v.'</h3></p>';
    		continue;
    	}

    	if ($result['type']!="audio"){
    		$rep[$v]='<p><h3><font color=red>注意：</font>框架模板类型值type目前只支持audio，'.$v.'</h3></p>';
    		continue;
    	}

       	//判断附件是否存在
    	if (!isset($result['name'])){
    		$rep[$v]='<p><h3><font color=red>注意：</font>框架模板类型值name没有设定，'.$v.'</h3></p>';
    		continue;
    	}

    	if (!isset($attArray[$result['name']])){
    		$rep[$v]='<p><h3><font color=red>注意：</font>框架模板类型值name的附件名找不到，无法定位对应附件，'.$v.'</h3></p>';
    		continue;
    	}

    	//判断附件是否是音乐
    	$extName=$attArray[$result['name']]['ca_type'];
    	if ($extName!='mp3'&&$extName!='wav'&&$extName!='ogg'){
    		$rep[$v]='<p><h3><font color=red>注意：</font>附件必须是mp3/wav/ogg类型的文件，目前对应的附件文件错误，'.$v.'</h3></p>';
    		continue;
    	}

    	$fileName=uploadUrlContentAtt.$attArray[$result['name']]['ca_detail'];
        //$fileName='./fileput.php?'.base64_encode(uploadDirContentAtt.$attArray[$result['name']]['ca_detail']);
    	$start=0;
    	$stop=-1;
    	$repeat=0;
    	if (isset($result['start']) || x_isint($result['start'])) $start=$result['start'];
    	if (isset($result['stop']) || x_isint($result['stop'])) $stop=$result['stop'];
    	if (isset($result['repeat']) || x_isint($result['repeat'])) $start=$result['repeat'];

    	$rep[$v]=showAudio($fileName,$attArray[$result['name']],$start,$stop,$repeat);


    }

    foreach($rep as $k=>$v){
    	$c=str_replace($k,$v,$c);
    }

    echo $c;
    ?></p>

<?php
}else{
  echo '<h1>参数错误。无法获取数据！</h1>';
}
?>
  </div>

</div>

<?php
function showAudio($fileName,$data,$start,$stop,$repeat=0){
    $type="";
    if ($data['ca_type']=='ogg') $type='<source src="'.$fileName.'" type="audio/ogg">';
    elseif ($data['ca_type']=='wav') $type='<source src="'.$fileName.'" type="audio/wav">';
    elseif ($data['ca_type']=='mp3') $type='<source src="'.$fileName.'" type="audio/mpeg">';
    else $type='<source src="'.$fileName.'" type="audio/mpeg">';

	return '<p><center><h4>音乐：'.$data['ca_title'].'</h4></center><p><center><h6>原文件名：'.$data['ca_filename'].'</h6></center><p><center><audio controls="controls">'.$type.'你的浏览器不支持此音频标签的输出。请更换浏览器。</audio></center></p></p>';
}

function imagesList($db,$cid){

    echo '<div class="hero-full-wrapper"><div class="grid"><div class="gutter-sizer"></div><div class="grid-sizer"></div>';

    $atts=getAttachmentByCid($cid,$db);
    if (!$atts) return false;
    foreach($atts as $k=>$v)
    {
        //如果附件不是图片类型就跳过，（回头可以处理下视频的）
        if (!($v['ca_type']=='jpg' || $v['ca_type']=='jpeg' || $v['ca_type']=='png' || $v['ca_type']=='gif' || $v['ca_type']=='bmp')) continue;

        //获取图片输出地址
        $pic=uploadUrlContentAtt.$v['ca_detail'];
        //获取链接地址
        $url='#';
        if (trim($v['ca_detail_url'])!='') $url=$v['ca_detail_url'];

        $str='<div class="grid-item"><img class="img-responsive" alt="" src="'.$pic.'"><a href="'.$url.'" class="project-description"><div class="project-text-holder"><div class="project-text-inner"><h3>'.$v['ca_title'].'</h3><p style="color:#333333;">'.x_html_back($v['ca_content']).'</p></div></div></a></div>';

        echo $str;

    }
    

    echo '</div></div>';

    echo '<script>document.addEventListener("DOMContentLoaded", function (event) {masonryBuild();});</script>';

}
?>
