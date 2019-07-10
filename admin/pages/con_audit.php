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
if (!isset($par['o']))   $par['o']='c_audit desc,cid';

//获取可以审核的栏目清单
$whereSql=$auditCls->auditChannels('sql');
$auditArr=getAuditChannels($whereSql,$db);
$auditArr=getChild($auditArr,0);  //增加一个level的值

//设定一个以栏目编号为key的数组
$channelName=array();
foreach ($auditArr as $key => $value) {
  $channelName[$value['chid']]=$value['ch_name'];
}

$chid='';
if (!isset($par["chid"]) || trim($par["chid"])==''){
  $chid='';
  $chidSql=$whereSql;
}else{
  if (isset($channelName[$par['chid']])){
    $chidSql='chid='.$par['chid'];
    $chid=$par['chid'];
  }
  else{
    $chidSql='1=0';  //不输出内容，给一个错误的条件
    echo "<center><h1>栏目编号错误或者此栏目当前管理员没有审核权限。</h1></center>";
    $chid='';
  }
}

//debug($auditArr);
?>

<link rel="stylesheet" href="../frontFrame/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="../frontFrame/kindeditor/kindeditor-all-min.js"></script>
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
              <h3 class="box-title">待审核资料列表</h3>

              <form action="./" method="get" style="width:400px;" class="box-tools">
                <input type="hidden" name="k"  value="audit">
                <input type="hidden" name="p"  value="1">
                <input type="hidden" name="pn" value="<?php echo $par['pn'];?>">
                <input type="hidden" name="o"  value="<?php echo $par['o'];?>">
                <div class="input-group input-group-sm">
                  <input type="text" name="kw" value="<?php echo $par['kw'];?>" class="form-control pull-right" placeholder="关键字">
                  <div class="input-group-btn">

                    <select class="btn btn-default" name="chid" onchange='this.form.submit();'>
                      <option value="">所有栏目</option>
                      <?php
                      foreach ($auditArr as $key => $value)
                      {
                        $selected='';
                        if ($value['chid'].' '==$chid.' ') $selected='selected';
                        echo '<option '.$selected.' value="'.$value['chid'].'">'.qianzhui($value['level']).'| '.$value['ch_name'].'('.$value['count'].')'.'</option>';
                      }
                      ?>
                    </select>

                    <button type="submit" class="btn btn-default xcms-button1" style="margin:0px 5px;"><i class="fa fa-search"></i> 搜索</button> 
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
                  <th>栏目名</th>
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
    $where='c_audit<=0';
    if ($par['kw']!='') $where=$where." and (c_title like '%".$par['kw']."%' or c_title2 like '%".$par['kw']."%' or c_author like '%".$par['kw']."%' or c_source like '%".$par['kw']."%')";

    if ($chidSql!='') $where=$where." and ".$chidSql;

    $plists=array(
      "table"   => "xt_content",        //表名
      "columns" => "chid,cid,c_title,c_title2,c_author,c_source,c_order,c_level,c_audit,c_aid_add,c_aid_update,c_dt_add,c_dt_update,c_attachment",  //字段
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
                  <td><?php echo $channelName[$v["chid"]];?></td>
                  <td onclick="showModal('modalAudit');getAuditData(<?php echo $v["cid"];?>,'<?php echo $channelName[$v["chid"]]." -> ".$v["c_title"];?>');"><span rel="nofollow" data-toggle="tooltip" title="点击审核资料" class="xcms_td_title" ><?php echo $v["c_title"];?></span></td>
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
                    <button class="btn btn-default btn-xs" onclick="showModal('modalAudit');getAuditData(<?php echo $v["cid"];?>,'<?php echo $channelName[$v["chid"]]." -> ".$v["c_title"];?>');"><i class="fa fa-gavel" rel="nofollow" data-toggle="tooltip" title="审核"></i></button>
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


<!-- 模态框（Modal） 查看资料-->
<div class="modal fade bs-example-modal-lg" id="modalAudit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width:96%;">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          审核资料 <label class="xcms-font-size14" id="ch_name"></label>
        </h4>
      </div>
      
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_1" data-toggle="tab" id="mainTab" aria-expanded="true">审核当前资料</a></li>
          <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false">查看历史审核记录</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">

            <div class="box box-default bg-white">
              <!-- /.box-header -->
              <!-- form start -->
              <form role="form" action="" method="post" onsubmit="return false;" id="contentForm" name="contentForm">
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
                    <input type="number" class="form-control" name="c_order" value=0 placeholder="数值，越大排在越前面" disabled="disabled">
                  </div>
                  <div class="form-group col-xs-3">
                      <label>资料头图</label>
                  </div>
                  <div class="form-group col-xs-3">
                      <a href="" target="_blank" id="downloadPic"><img src="" data-url="<?php echo uploadUrlContentPic;?>" id="c_pic_image" style="max-width: 120px;max-height: 60px;" /></a>
                  </div>
                  <div class="form-group col-xs-6">
                    <label>头图相关网址</label>
                    <input type="text" class="form-control" name="c_pic_url" placeholder="来源，非必填项，网址如：http://www.xxx.com/" disabled="disabled">
                  </div>
                  <div class="form-group  col-xs-12">
                    <label>简介</label>
                    <textarea class="form-control" rows="3" name="c_summary" id="c_summary" placeholder="简介，是纯文本内容" disabled="disabled"></textarea>
                  </div>
                  <div class="form-group  col-xs-12">
                    <label>资料详情</label>
                    <textarea name="c_detail" id="c_detail" style="width:100%;height:350px;visibility:hidden;" disabled="disabled"></textarea>
                    <script>
                      var editor;
                      KindEditor.ready(function(K) {
                        editor = K.create('textarea[name="c_detail"]', {
                          allowFileManager : true,
                          readonlyMode : true
                        });
                      });

                    </script>
                  </div>

                  <div id="attachmentsOld"></div>

                </div>
                <!-- /.box-body -->
              </form>

              <form role="form" action="" method="post" onsubmit="return subAudit();" id="auditForm" name="auditForm" enctype="multipart/form-data">
                <input type="hidden" name="op" value="audit">
                <input type="hidden" name="cid" id="cid" value="">

                <div class="box-body" style="border: 1px solid #aaaaaa;background-color:#ededed;">

                  <div class="form-group col-xs-12">
                    <center><h3 id="c_title_audit">审核操作</h3></center>
                  </div>

                  <div class="form-group col-xs-6">
                      <label class="xcms_right">
                        <input type="radio" name="au_result" value="-1">  <span class="xcms-padding-left10 xcms-font-size18">审核不通过</span>
                      </label>
                  </div>

                  <div class="form-group col-xs-6">
                      <label>
                        <input type="radio" name="au_result" value="1">  <span class="xcms-padding-left10 xcms-font-size18">审核通过</span>
                      </label>
                  </div>

                  <div class="form-group col-xs-12">
                    <label>审核备注</label>
                    <textarea name="au_memo" id="au_memo" style="width:100%;height:200px;visibility:hidden;"></textarea>
                    <script>
                      var editor2;
                      KindEditor.ready(function(K) {
                        editor2 = K.create('textarea[name="au_memo"]', {
                          allowFileManager : true
                        });
                      });

                    </script>
                  </div>


                  <div class="form-group col-xs-12 text-center">
                    <button type="submit" id="subButton" class="btn btn-primary" data-loading-text="数据处理中，请稍等..." autocomplete=“off”>提交审核结果</button>
                    <button type="reset" id="resetButton"  class="btn bg bg-gray" >重置</button>
                    <button type="button" id="closeButton" onclick="hideModal('modalAudit');" class="btn bg bg-gray" >取消</button>
                  </div>

                </div>

              </form>
            </div>

          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_2">


            <div class="box-body">
              <div class="box-group" id="showAuditHistory">
                <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->

                <div class="panel box box-primary">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1" class="collapsed" aria-expanded="false">
                        2019年5月10日 审核通过 <span class="xcms-font-size14" title="账号：">审核员：administrator</span>
                      </a>
                    </h4>
                  </div>
                  <div id="collapse1" class="panel-collapse collapse" aria-expanded="false" style="">
                    <div class="box-body">
                      审核备注，通过
                    </div>
                  </div>
                </div>

                <div class="panel box box-danger">
                  <div class="box-header with-border">
                    <h4 class="box-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse2" class="collapsed" aria-expanded="false">
                        2019年5月10日 审核不通过
                      </a>
                    </h4>
                  </div>
                  <div id="collapse2" class="panel-collapse collapse" aria-expanded="false" style="">
                    <div class="box-body">
                      审核备注，不通过
                    </div>
                  </div>
                </div>

              </div>
            </div>

            <div class="box-footer text-center">
              <button type="button" id="closeButton2" onclick="hideModal('modalAudit');" class="btn btn-default" >关闭</button>
            </div>


          </div>
          <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
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