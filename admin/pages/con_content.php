<?php
/*初始化这个页面上的参数
  1. 基本页面关键字参数 k
  2. 分页页码 p
  3. 每页显示数据数量 pn
  4. 搜索关键字  kw
  5. 排序值 o
*/

if (!isset($par['p']))   $par['p']=1;
if (!isset($par['pn']))  $par['pn']=20;
if (!isset($par['kw']))  $par['kw']='';
if (!isset($par['o']))   $par['o']='c_audit,c_level desc,c_order desc,cid desc';

//用户对本栏目的操作权限
// 1. 是否只处理本人处理
// 2. 对本栏目有哪些权限
$isOneself=$auditCls->isOneself();
if ($isOneself===false) exit('用户权限错误，请找管理员处理问题。');
$limitChannel=$auditCls->limitChannel($par["chid"]);

?>

<link rel="stylesheet" href="../frontFrame/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="../frontFrame/kindeditor/kindeditor-all.js"></script>
<script charset="utf-8" src="../frontFrame/kindeditor/lang/zh-CN.js"></script>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
<section class="content">
      <!-- /.row -->
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">资料列表</h3>

              <form action="./" method="get" style="width:300px;" class="box-tools">
                <input type="hidden" name="k"  value="content">
                <input type="hidden" name="chid"  value="<?php echo $par["chid"];?>">
                <input type="hidden" name="p"  value="1">
                <input type="hidden" name="pn" value="<?php echo $par['pn'];?>">
                <input type="hidden" name="o"  value="<?php echo $par['o'];?>">
                <div class="input-group input-group-sm">
                  <input type="text" name="kw" value="<?php echo $par['kw'];?>" class="form-control pull-right" placeholder="关键字">
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default xcms-button1" style="margin:0px 5px;"><i class="fa fa-search"></i> 搜索</button> 
                    <?php
                    if ($limitChannel['w']){
                    ?>
                    <button type="button" onclick="showModal('modalAddContent');document.getElementById('resetButton').click();" class="btn btn-default xcms-button1"><i class="fa fa-plus"></i> 添加资料</button>
                    <?php
                    }
                    ?>
                  </div>
                </div>
              </form>

            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>

                <tr>
                  <th>编号</th>
                  <th>标题</th>
                  <th>副标题</th>
                  <th>作者</th>
                  <th>来源</th>
                  <th>等级</th>
                  <th>排序</th>
                  <th>审核</th>
                  <th>添加者</th>
                  <th>添加时间</th>
                  <th>修改者</th>
                  <th>修改时间</th>
                  <th>附件</th>
                  <th>操作</th>
                </tr>
<?php
    $where='chid='.$par['chid'];
    if ($par['kw']!='') $where=$where." and (c_title like '%".$par['kw']."%' or c_title2 like '%".$par['kw']."%' or c_author like '%".$par['kw']."%' or c_source like '%".$par['kw']."%')";

    //判断用户是否只显示自己本人的数据，如果是就做处理
    if ($isOneself===1){
      $w="c_aid_add=".$_SESSION['aid'];
      $where=$where." and ".$w;
    }

    $plists=array(
      "table"   => "xt_content",        //表名
      "columns" => "cid,c_title,c_title2,c_author,c_source,c_order,c_level,c_audit,c_aid_add,c_aid_update,c_dt_add,c_dt_update,c_attachment",  //字段
      "where"   => $where,            //where条件
      "order"   => $par['o'],         //order by条件
      "fnumber" => $par['pn'],        //每页的数量
      "fid"     => $par['p']          //当前页码
    );

    $list=new xlist($db,$plists);       //分页取数据类
    $list->showPage(10);              //分页页码计算，参数10是显示的页码数，可以修改

    //debug($list);
    if (!empty($list->datas))
    {
      foreach($list->datas as $v)
      {
        if (x_isint($v["c_aid_add"])) {
          $a=getAdminById($v["c_aid_add"],$db);
          if (!$a) $aid_add=$v["c_aid_add"]." (不存在账号)";
          else $aid_add="<span rel='nofollow' data-toggle='tooltip' title='账号：".$a['a_account']."'>".$a['a_name']."</span>";
        }else $aid_add=$v["c_aid_add"];

        if (x_isint($v["c_aid_update"])) {
          $a=getAdminById($v["c_aid_update"],$db);
          if (!$a) $aid_update=$v["c_aid_update"]." (不存在账号)";
          else $aid_update="<span rel='nofollow' data-toggle='tooltip' title='账号：".$a['a_account']."'>".$a['a_name']."</span>";
        }else $aid_update=$v["c_aid_update"];
?>
                <tr>
                  <td><?php echo $v["cid"];?></td>
                  <?php
                  if ($limitChannel['r']){
                  ?>
                  <td onclick="showModal('modalShowContent');getShowContentData(<?php echo $v["cid"];?>);"><span rel="nofollow" data-toggle="tooltip" title="点击查看资料详情" class="xcms_td_title" ><?php echo $v["c_title"];?></span></td>
                  <?php
                  }else{
                  ?>
                  <td><?php echo $v["c_title"];?></td>
                  <?php
                  }
                  ?>
                  <td><?php echo $v["c_title2"];?></td>
                  <td><?php echo $v["c_author"];?></td>
                  <td><?php echo $v["c_source"];?></td>
                  <td><?php echo $contentLevel[$v["c_level"]];?></td>
                  <td><?php echo $v["c_order"];?></td>
                  <td onclick="showModal('modalAuditHistory');getContentAuditHistory(<?php echo $v["cid"];?>);" class="xcms-pointer"><span rel="nofollow" data-toggle="tooltip" title="点击查看审核记录"><?php echo $contentAudit[trim($v["c_audit"]." ")];?></span></td>
                  <td><?php echo $aid_add;?></td>
                  <td><span rel="nofollow" data-toggle="tooltip" title="<?php echo $v["c_dt_add"];?>"><?php echo shortDateTime($v["c_dt_add"]);?></span></td>
                  <td><?php echo $aid_update;?></td>
                  <td><span rel="nofollow" data-toggle="tooltip" title="<?php echo $v["c_dt_update"];?>"><?php echo shortDateTime($v["c_dt_update"]);?></span></td>
                  <td><?php echo $v["c_attachment"];?></td>
                  <td>
                    <?php
                    if ($limitChannel['w']){
                    ?>
                    <button class="btn btn-default btn-xs" onclick="showModal('modalModifyContent');getModifyContentData(<?php echo $v["cid"];?>);"><i class="fa fa-edit" rel="nofollow" data-toggle="tooltip" title="编辑"></i></button>
                    <?php
                    }
                    ?>

                    <?php
                    if ($limitChannel['d']){
                    ?>
                    <button class="btn btn-default btn-xs" id="delete_<?php echo $v["cid"];?>" onclick="contentDelete(<?php echo $v["cid"];?>,'<?php echo $v["c_title"];?>');"><i class="fa fa-remove" rel="nofollow" data-toggle="tooltip" title="删除"></i></button>
                    <?php
                    }
                    ?>
                  </td>
                </tr>
<?php
      }
    }

?>
                </tbody>
              </table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              <label>
                共<?php echo $list->count;?>条，<?php echo $list->pagesCount;?>页，每页
                  <select onchange="window.open(this.value,'_self')">
                    <option value="./?<?php echo parUrl($par,array("pn"=>5,"p"=>1));?>" <?php echo show_selected($par['pn'],5);?>>5</option>
                    <option value="./?<?php echo parUrl($par,array("pn"=>10,"p"=>1));?>" <?php echo show_selected($par['pn'],10);?>>10</option>
                    <option value="./?<?php echo parUrl($par,array("pn"=>20,"p"=>1));?>" <?php echo show_selected($par['pn'],20);?>>20</option>
                    <option value="./?<?php echo parUrl($par,array("pn"=>50,"p"=>1));?>" <?php echo show_selected($par['pn'],50);?>>50</option>
                    <option value="./?<?php echo parUrl($par,array("pn"=>100,"p"=>1));?>" <?php echo show_selected($par['pn'],100);?>>100</option>
                  </select>
                条
              </label>
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="./?<?php echo parUrl($par,array("p"=>1));?>">首页</a></li>
                <li><a href="./?<?php echo parUrl($par,array("p"=>$list->showPrev));?>">«</a></li>
                <?php
                for($i=$list->showStart;$i<=$list->showEnd;$i++)
                {
                ?>
                <li><a <?php if ($par['p']==$i) echo "style='background-color:purple;color:white;'";?> href="./?<?php echo parUrl($par,array("p"=>$i));?>"><?php echo $i;?></a></li>
                <?php
                }
                ?>
                <li><a href="./?<?php echo parUrl($par,array("p"=>$list->showNext));?>">»</a></li>
                <li><a href="./?<?php echo parUrl($par,array("p"=>$list->pagesCount));?>">尾页 <?php echo $list->pagesCount;?></a></li>
              </ul>
            </div>

          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
  <!-- /.content -->

</div>
<!-- /.content-wrapper -->


<!-- 模态框（Modal） 添加资料-->
<div class="modal fade bs-example-modal-lg" id="modalAddContent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width:96%;">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel1">
          添加资料
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return contentAdd();" id="contentAddForm" name="contentAddForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="add">
          <input type="hidden" name="chid" value="<?php echo $par['chid'];?>">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>主标题 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="c_title" placeholder="资料的主标题，必须填写">
            </div>
            <div class="form-group col-xs-6">
              <label>副标题 </label>
              <input type="text" class="form-control" name="c_title2" placeholder="副标题，不是必填项">
            </div>
            <div class="form-group col-xs-3">
              <label>作者 </label>
              <input type="text" class="form-control" name="c_author" placeholder="作者，非必填项">
            </div>
            <div class="form-group col-xs-3">
              <label>资料来源</label>
              <input type="text" class="form-control" name="c_source" placeholder="来源，非必填项">
            </div>
            <div class="form-group  col-xs-3">
              <label>资料等级 <span class="xcms-font-red">*</span></label>
              <select name="c_level" class="form-control">
                <option value="0" selected="">普通</option>
                <option value="1">推荐</option>
                <option value="2">置顶</option>
              </select>
            </div>
            <div class="form-group col-xs-3">
              <label>排序号</label>
              <input type="number" class="form-control" name="c_order" value=0 placeholder="整数，越大排在越前面">
            </div>
            <div class="form-group col-xs-3">
                <label>资料头图</label>
                <input type="file" name="c_pic">
            </div>
            <div class="form-group col-xs-3" id="showpic">
                <img src="" style="max-width: 120px;max-height: 60px;" />
            </div>
            <div class="form-group col-xs-6">
              <label>头图相关网址</label>
              <input type="text" class="form-control" name="c_pic_url" placeholder="来源，非必填项，网址如：http://www.xxx.com/">
            </div>
            <div class="form-group  col-xs-12">
              <label>简介</label>
              <textarea class="form-control" rows="3" name="c_summary" placeholder="简介，是纯文本内容"></textarea>
            </div>
            <div class="form-group  col-xs-12">
              <label>资料详情 <span style="font-weight:normal;font-size:11px;">附件操作标签：瀑布流 {{imagesList}} | 音频文件 {{"type":"audio","name":"附件名","start":0,"stop":-1,"repeat":0}}</span></label>
              <textarea name="c_detail" style="width:100%;height:500px;visibility:hidden;"></textarea>
              <script>
                var editor;
                KindEditor.ready(function(K) {
                  editor = K.create('textarea[name="c_detail"]', {
                    allowFileManager : true,
                    filterMode:false,
                    designMode:true
                  });
                });
              </script>
            </div>

            <div class="form-group  col-xs-12">
              <label style="width:100%;">附件管理 
                <a href="#" onclick="resetAttachments();" title="清除附件" style="float:right;margin-left:15px;">清除附件</a>
                <a href="#" onclick="addAttachments();" title="追加附件" style="float:right;">添加附件</a>
              </label>
            </div>

            <div id="attachments"></div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="submit" id="contentAddButton" class="btn btn-default xcms-button1" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认添加</button>
            <button type="button" id="resetButton" onclick="document.getElementById('contentAddForm').reset();resetAttachments();editor.html('');" class="btn btn-default" >重置</button>
            <button type="button" onclick="hideModal('modalAddContent');" class="btn btn-default" >关闭</button>
            <br/><br/>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->



<div id="attachment_sample" style="display: none;">
  <div class="form-group col-xs-2">
    <label class="xcms-font-size13">附件文件选择 <span class="xcms-font-red">*</span></label> 
    <input type="file" name="ca_detail[]">
  </div>
  <div class="form-group col-xs-2">
    <label class="xcms-font-size13">附件名称 <span class="xcms-font-red">*</span></label> 
    <input type="text" class="form-control" name="ca_title[]" placeholder="请填写附件名称">
  </div>
  <div class="form-group col-xs-2">
    <label class="xcms-font-size13">附件相关网址</label> 
    <input type="text" class="form-control" name="ca_detail_url[]" placeholder="此项一般用于图片">
  </div>
  <div class="form-group col-xs-1">
    <label class="xcms-font-size13">排序值 <span class="xcms-font-red">*</span></label> 
    <input type="number" class="form-control" name="ca_order[]" value="0" placeholder="排序值，从大到小排序，值必须大于0" title="排序值，从大到小排序，值必须大于0">
  </div>
  <div class="form-group col-xs-5">
    <label class="xcms-font-size13">附件详细介绍</label> 
    <textarea class="form-control" rows="1" name="ca_content[]" placeholder="对附件的详细介绍"></textarea>
  </div>
</div>


<!-- 模态框（Modal） 编辑资料-->
<div class="modal fade bs-example-modal-lg" id="modalModifyContent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width:96%;">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel2">
          编辑资料
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return contentModify();" id="contentModifyForm" name="contentModifyForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="modify">
          <input type="hidden" name="cid" value="<?php echo $par['cid'];?>">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>主标题 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="c_title" placeholder="资料的主标题，必须填写">
            </div>
            <div class="form-group col-xs-6">
              <label>副标题 </label>
              <input type="text" class="form-control" name="c_title2" placeholder="副标题，不是必填项">
            </div>
            <div class="form-group col-xs-3">
              <label>作者 </label>
              <input type="text" class="form-control" name="c_author" placeholder="作者，非必填项">
            </div>
            <div class="form-group col-xs-3">
              <label>资料来源</label>
              <input type="text" class="form-control" name="c_source" placeholder="来源，非必填项">
            </div>
            <div class="form-group  col-xs-3">
              <label>资料等级 <span class="xcms-font-red">*</span></label>
              <select name="c_level" class="form-control">
                <option value="0" selected="">普通</option>
                <option value="1">推荐</option>
                <option value="2">置顶</option>
              </select>
            </div>
            <div class="form-group col-xs-3">
              <label>排序号</label>
              <input type="number" class="form-control" name="c_order" value=0 placeholder="整数，越大排在越前面">
            </div>
            <div class="form-group col-xs-3">
                <label>资料头图</label>
                <input type="file" name="c_pic" id="c_pic">
            </div>
            <div class="form-group col-xs-3">
                <a href="" target="_blank" id="downloadPic"><img src="" data-url="<?php echo uploadUrlContentPic;?>" id="c_pic_image" style="max-width: 120px;max-height: 60px;" /></a>
            </div>
            <div class="form-group col-xs-6">
              <label>头图相关网址</label>
              <input type="text" class="form-control" name="c_pic_url" placeholder="来源，非必填项，网址如：http://www.xxx.com/">
            </div>
            <div class="form-group  col-xs-12">
              <label>简介</label>
              <textarea class="form-control" rows="3" name="c_summary" placeholder="简介，是纯文本内容"></textarea>
            </div>
            <div class="form-group  col-xs-12">
              <label>资料详情 <span style="font-weight:normal;font-size:11px;">附件操作标签：瀑布流 {{imagesList}} | 音频文件 {{"type":"audio","name":"附件名","start":0,"stop":-1,"repeat":0}}</span></label>
              <textarea name="c_detail2" id="c_detail2" style="width:100%;height:500px;visibility:hidden;"></textarea>
              <script>
                var editor2;
                KindEditor.ready(function(K) {
                  editor2 = K.create('textarea[name="c_detail2"]', {
                    allowFileManager : true,
                    filterMode:false,
                    designMode:true
                  });
                });

              </script>
            </div>

            <div class="form-group col-xs-12">
              <label style="width:100%;">附件管理 
                <a href="#" onclick="resetAttachments2();" title="清除附件" style="float:right;margin-left:15px;">清除新增附件</a>
                <a href="#" onclick="addAttachments2();" title="追加附件" style="float:right;">添加附件</a>
              </label>
            </div>

            <div id="attachmentsOld"></div>
            <div id="attachments2"></div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="submit" id="contentModifyButton" class="btn btn-default xcms-button1" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认修改</button>
            <button type="button" onclick="hideModal('modalModifyContent');" class="btn btn-default" >关闭</button>
            <br/><br/>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->


<!-- 模态框（Modal） 查看资料-->
<div class="modal fade bs-example-modal-lg" id="modalShowContent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width:96%;">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel3">
          查看资料
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return false;" id="contentShowForm" name="contentShowForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="show">
          <input type="hidden" name="cid" value="<?php echo $par['cid'];?>">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>主标题 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="c_title" disabled="disabled" placeholder="资料的主标题，必须填写">
            </div>
            <div class="form-group col-xs-6">
              <label>副标题 </label>
              <input type="text" class="form-control" name="c_title2" disabled="disabled" placeholder="副标题，不是必填项">
            </div>
            <div class="form-group col-xs-3">
              <label>作者 </label>
              <input type="text" class="form-control" name="c_author" disabled="disabled" placeholder="作者，非必填项">
            </div>
            <div class="form-group col-xs-3">
              <label>资料来源</label>
              <input type="text" class="form-control" name="c_source" disabled="disabled" placeholder="来源，非必填项">
            </div>
            <div class="form-group  col-xs-3">
              <label>资料等级 <span class="xcms-font-red">*</span></label>
              <select name="c_level" class="form-control" disabled="disabled">
                <option value="0" selected="">普通</option>
                <option value="1">推荐</option>
                <option value="2">置顶</option>
              </select>
            </div>
            <div class="form-group col-xs-3">
              <label>排序号</label>
              <input type="number" class="form-control" name="c_order" value=0 placeholder="整数，越大排在越前面" disabled="disabled">
            </div>
            <div class="form-group col-xs-3">
                <label>资料头图</label>
            </div>
            <div class="form-group col-xs-3">
                <a href="" target="_blank" id="downloadPic2"><img src="" data-url="<?php echo uploadUrlContentPic;?>" id="c_pic_image2" style="max-width: 120px;max-height: 60px;" /></a>
            </div>
            <div class="form-group col-xs-6">
              <label>头图相关网址</label>
              <input type="text" class="form-control" name="c_pic_url" placeholder="来源，非必填项，网址如：http://www.xxx.com/" disabled="disabled">
            </div>
            <div class="form-group  col-xs-12">
              <label>简介</label>
              <textarea class="form-control" rows="3" name="c_summary" placeholder="简介，是纯文本内容" disabled="disabled"></textarea>
            </div>
            <div class="form-group  col-xs-12">
              <label>资料详情</label>
              <textarea name="c_detail3" id="c_detail3" style="width:100%;height:500px;visibility:hidden;" disabled="disabled"></textarea>
              <script>
                var editor3;
                KindEditor.ready(function(K) {
                  editor3 = K.create('textarea[name="c_detail3"]', {
                    allowFileManager : false,
                    filterMode:false,
                    allowUpload:false,
                    designMode:true
                  });
                });

              </script>
            </div>

            <div id="attachmentsOld2"></div>

          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="button" id="closeButtonShowContent"  data-loading-text="数据处理中，请稍等..." autocomplete=“off” onclick="hideModal('modalShowContent');" class="btn btn-default" >关闭</button>
            <br/><br/>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->




<!-- 模态框（Modal） 查看历史审核记录-->
<div class="modal fade bs-example-modal-lg" id="modalAuditHistory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel4" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width:96%;">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel4">
          历史审核记录 | <span class="xcms-font-size16" id="c_title_history"></span>
        </h4>
      </div>
      
      <div class="box box-default bg-white">

          <div class="box-body">
            <div class="box-group" id="showAuditHistory2">

            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="button" id="closeButtonAudit" onclick="hideModal('modalAuditHistory');" data-loading-text="数据处理中，请稍等..." autocomplete=“off” class="btn btn-default" >关闭</button>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->
