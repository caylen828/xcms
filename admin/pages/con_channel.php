<?php
$where='';

$plists=array(
  "table"   => "xt_channel",            //表名
  "columns" => "chid,ch_name,ch_order,ch_father_id,ch_audit,ch_memo,ch_ext",  //字段
  "where"   => $where,                  //where条件
  "order"   => 'ch_order desc,chid',    //order by条件
  "fnumber" => 10000,                   //每页的数量
  "fid"     => 1                        //当前页码
);

$list=new xlist($db,$plists);           //分页取数据类
$list->showPage(10);                    //分页页码计算，参数10是显示的页码数，可以修改
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
<section class="content">
  <!-- /.row -->
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">网站栏目列表 （栏目总计：<?php echo $list->count;?>个）</h3>

            <div style="width:105px;" class="box-tools">
            <div class="input-group input-group-sm">
              <div class="input-group-btn">
                <button type="button" onclick="showModal('modalAddChannel');channelAddForm.reset();" class="btn btn-default xcms-button1"><i class="fa fa-plus"></i> 添加网站栏目</button>
              </div>
            </div>
            </div>

        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
          <table class="table table-hover">
            <tbody>

            <tr>
              <th>编号</th>
              <th>栏目名称</th>
              <th>外接功能</th>
              <th>是否需审核</th>
              <th>排序值</th>
              <th>备注</th>
              <th>操作</th>
            </tr>
<?php
    //debug($_SESSION);
    //debug($list);
    if (!empty($list->datas))
    {
      $re=getChild($list->datas);  //递归结构
      if (!empty($re))
      {
        foreach($re as $v)
        {
          $qianzhui=qianzhui($v['level']);
?>
            <tr>
              <td><?php echo $v["chid"];?></td>
              <td><?php echo $qianzhui.' | '.$v["ch_name"];?></td>
              <td><?php
              if (trim($v["ch_ext"])!=''){
                echo '<span rel="nofollow" data-toggle="tooltip" title="对应文件：pages/ext_'.$v["ch_ext"].'.php">'.$v["ch_ext"].'</span>';
              }
              ?></td>
              <td><?php if (isset($channelAudit[$v["ch_audit"]])) echo $channelAudit[$v["ch_audit"]]; else echo "审核值错误";?></td>
              <td><?php echo $v["ch_order"];?></td>
              <td><?php echo $v["ch_memo"];?></td>
              <td>
                <button class="btn btn-default btn-xs" onclick="showModal('modalModifyChannel');getModifyChannelData(<?php echo $v["chid"];?>);"><i class="fa fa-edit" rel="nofollow" data-toggle="tooltip" title="编辑"></i></button>
              </td>
            </tr>
<?php
        }
      }
    }

?>
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">

        </div>

      </div>
      <!-- /.box -->
    </div>
  </div>
</section>
<!-- /.content -->

</div>
<!-- /.content-wrapper -->




<!-- 模态框（Modal） 添加管理员-->
<div class="modal fade" id="modalAddChannel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          添加网站栏目
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return channelAdd();" id="channelAddForm" name="channelAddForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="add">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>选择上级栏目 （<span class="xcms-font-size11">只允许选择一项</span>）</label>
              <select multiple="" name="ch_father_id" class="form-control" style="height:280px;">
                <option value="0" selected="">==一级栏目==</option>
                <?php
                if (!empty($list->datas))
                {
                  foreach ($re as $key => $value) 
                  {
                    $qianzhui=qianzhui($value['level']);
                    echo '<option value="'.$value["chid"].'" >'.$qianzhui." | ".$value["ch_name"].'</option>';
                  }
                }

                ?>
              </select>
            </div>
            <div class="form-group col-xs-6">
              <label>栏目名称 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="ch_name" placeholder="请输入栏目名称">
            </div>
            <div class="form-group col-xs-6">
              <label>外接功能</label>
              <input type="text" class="form-control" name="ch_ext" value="" placeholder="输入功能名如：test，自动对应：pages/ext_test.php">
            </div>
            <div class="form-group col-xs-6">
              <label>排序值 <span class="xcms-font-red">*</span></label>
              <input type="number" class="form-control" name="ch_order" value="0" placeholder="必须是整数，越大排在越前面">
            </div>
            <div class="form-group  col-xs-6">
              <label>是否需要审核 <span class="xcms-font-red">*</span></label>
              <div class="radio">
                <label>
                  <input type="radio" name="ch_audit" value="1">
                  需要审核
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="ch_audit" value="0" checked="">
                  不需要审核
                </label>
              </div>
            </div>
            <div class="form-group  col-xs-12">
              <label>备注</label>
              <input type="text" class="form-control" name="ch_memo" placeholder="备注内容">
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="submit" id="channelAddButton" class="btn btn-default xcms-button1" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认添加</button>
            <button type="reset" id="resetButton" class="btn btn-default" >重置</button>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->



<!-- 模态框（Modal） 编辑网站栏目-->
<div class="modal fade" id="modalModifyChannel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          编辑网站栏目
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return channelModify();" id="channelModifyForm" name="channelModifyForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="modify" id="modifyop">
          <input type="hidden" name="chid" value="">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>选择上级栏目 （<span class="xcms-font-size11">只允许选择一项</span>）</label>
              <select multiple="" name="ch_father_id" class="form-control" style="height:280px;">
                <option value="0" selected="">==一级栏目==</option>
                <?php
                if (!empty($list->datas))
                {
                  foreach ($re as $key => $value) 
                  {
                    $qianzhui=qianzhui($value['level']);
                    echo '<option value="'.$value["chid"].'" >'.$qianzhui." | ".$value["ch_name"].'</option>';
                  }
                }

                ?>
              </select>
            </div>
            <div class="form-group col-xs-6">
              <label>栏目名称 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" id="ch_name" name="ch_name" placeholder="请输入栏目名称">
            </div>
            <div class="form-group col-xs-6">
              <label>外接功能</label>
              <input type="text" class="form-control" id="ch_ext" name="ch_ext" value="" placeholder="输入功能名如：test，自动对应：pages/ext_test.php">
            </div>
            <div class="form-group col-xs-6">
              <label>排序值 <span class="xcms-font-red">*</span></label>
              <input type="number" class="form-control" id="ch_order" name="ch_order" value="0" placeholder="必须是整数，越大排在越前面">
            </div>
            <div class="form-group  col-xs-6">
              <label>是否需要审核 <span class="xcms-font-red">*</span></label>
              <div class="radio">
                <label>
                  <input type="radio" name="ch_audit" value="1">
                  需要审核
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="ch_audit" value="0" checked="">
                  不需要审核
                </label>
              </div>
            </div>
            <div class="form-group  col-xs-12">
              <label>备注</label>
              <input type="text" class="form-control" name="ch_memo" placeholder="备注内容">
            </div>
            <div class="form-group  col-xs-12">
              <label>删除账号</label>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="a_delete" value="1" onchange="changeModifyOp(this.checked)">
                  我要删除本网站栏目(<span style="color:#999999;font-size:11px;">请谨慎勾选，删除后无法恢复，如果栏目下有资料将无法删除。</span>)
                </label>
              </div>
            </div>
          </div>
          <!-- /.box-body -->


          <div class="box-footer text-center">
            <button type="submit" id="channelModifyButton" class="btn btn-default xcms-button1" data-loading-text="数据加载中，请稍等..." autocomplete=“off”>确认提交</button>
            
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->
