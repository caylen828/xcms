<?php
/*初始化这个页面上的参数
  1. 基本页面关键字参数 k
  2. 分页页码 p
  3. 每页显示数据数量 pn
  4. 搜索关键字  kw
  5. 管理员类型 at
  6. 管理员状态 as
  7. 排序值 o
*/

  if (!isset($par['p']))   $par['p']=1;
  if (!isset($par['pn']))  $par['pn']=10;
  if (!isset($par['kw']))  $par['kw']='';
  if (!isset($par['at']))  $par['at']='';
  if (!isset($par['as']))  $par['as']='';
  if (!isset($par['o']))   $par['o']='aid desc';
  //反向排序处理
  if (strpos($par['o']," desc")!==false) {
    $order=trim(str_replace(" desc", "", $par['o']));
    $orderIcon="down";
  }else {
    $order=$par['o']." desc";
    $orderIcon="up";
  }

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
              <h3 class="box-title">管理员列表</h3>

              <form action="./" method="get" style="width:400px;" class="box-tools">
                <input type="hidden" name="k"  value="admin">
                <input type="hidden" name="p"  value="1">
                <input type="hidden" name="pn" value="<?php echo $par['pn'];?>">
                <input type="hidden" name="o"  value="<?php echo $par['o'];?>">
                <div class="input-group input-group-sm">
                  <input type="text" name="kw" value="<?php echo $par['kw'];?>" class="form-control pull-right" placeholder="关键字">
                  <div class="input-group-btn">
                    <select class="btn btn-default" name="at">
                      <option value="">所有类型</option>
                      <option <?php echo show_selected($par['at'],1);?> value=1>超级管理员</option>
                      <option <?php echo show_selected($par['at'],2);?> value=2>审核员</option>
                      <option <?php echo show_selected($par['at'],3);?> value=3>普通管理员</option>
                    </select>
                    <select class="btn btn-default" name="as">
                      <option value="">所有状态</option>
                      <option <?php echo show_selected($par['as'],0);?> value=0>停用</option>
                      <option <?php echo show_selected($par['as'],1);?> value=1>正常</option>
                    </select>
                    <button type="submit" class="btn btn-default xcms-button1" style="margin:0px 5px;"><i class="fa fa-search"></i> 搜索</button> 
                    <button type="button" onclick="showModal('modalAddAdmin');adminAddForm.reset();" class="btn btn-default xcms-button1"><i class="fa fa-plus"></i> 添加管理员</button>
                  </div>
                </div>
              </form>

            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>

                <tr>
                  <th>编号 <a href="./?<?php echo parUrl($par,array("o"=>$order));?>" title="点击后进行反向排序。"><i class="fa fa-long-arrow-<?php echo $orderIcon;?>"></i></a></th>
                  <th>姓名</th>
                  <th>账号</th>
                  <th>类型</th>
                  <th>状态</th>
                  <th>创建时间</th>
                  <th>备注</th>
                  <th>操作</th>
                </tr>
<?php
    $where='';
    if ($par['kw']!='') $where="(a_name like '%".$par['kw']."%' or a_account like '%".$par['kw']."%' or a_memo like '%".$par['kw']."%')";
    if ($par['at']!='') {
      if ($where!='') $where = $where." and ";
      $where=$where."a_type=".$par['at'];
    }
    if ($par['as']!='') {
      if ($where!='') $where = $where." and ";
      $where=$where."a_status=".$par['as'];
    }

    $plists=array(
      "table"   => "xt_admin",        //表名
      "columns" => "aid,a_name,a_account,a_type,a_status,a_memo,a_dt_add",  //字段
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
?>
                <tr>
                  <td><?php echo $v["aid"];?></td>
                  <td><?php echo $v["a_name"];?></td>
                  <td><?php echo $v["a_account"];?></td>
                  <td><?php if (isset($adminType[$v["a_type"]])) echo $adminType[$v["a_type"]]; else echo "错误类型";?></td>
                  <td><span class="badge <?php if ($v["a_status"]==0) echo "bg-red"; else echo "bg-green";?>"><?php echo $adminStatus[$v["a_status"]];?></span></td>
                  <td><?php echo $v["a_dt_add"];?></td>
                  <td><?php echo $v["a_memo"];?></td>
                  <td>
                    <button class="btn btn-default btn-xs" onclick="showModal('modalModifyAdmin');getModifyAdminData(<?php echo $v["aid"];?>);"><i class="fa fa-edit" rel="nofollow" data-toggle="tooltip" title="编辑"></i></button>
                    <?php if ($v["a_type"]!==1) {?> <!--超级管理员不需要显示权限-->
                    <button class="btn btn-default btn-xs" onclick="showModal('modalAuditAdmin');getAuditAdminData(<?php echo $v["aid"];?>);"><i class="fa fa-gavel" rel="nofollow" data-toggle="tooltip" title="权限设定"></i></button>
                    <?php }?>
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


<!-- 模态框（Modal） 添加管理员-->
<div class="modal fade" id="modalAddAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          添加管理员
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return adminAdd();" id="adminAddForm" name="adminAddForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="add">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>账号 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="a_account" placeholder="请输入管理员的账号">
            </div>
            <div class="form-group col-xs-6">
              <label>姓名 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" id="a_name" name="a_name" placeholder="请输入管理员的姓名">
            </div>
            <div class="form-group col-xs-6">
              <label for="exampleInputPassword1">密码 <span class="xcms-font-red">*</span></label>
              <input type="password" class="form-control" name="a_password" placeholder="请设定管理员密码">
            </div>
            <div class="form-group col-xs-6">
              <label for="exampleInputPassword1">密码验证 <span class="xcms-font-red">*</span></label>
              <input type="password" class="form-control" name="a_password2" placeholder="管理员密码验证">
            </div>
            <div class="form-group  col-xs-6">
              <label>管理员类型 <span class="xcms-font-red">*</span></label>
              <div class="radio">
                <label>
                  <input type="radio" name="a_type" value="1">
                  超级管理员
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="a_type" value="2">
                  审核员
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="a_type" value="3" checked="">
                  普通管理员
                </label>
              </div>
            </div>
            <div class="form-group col-xs-6">
              <label>头像图片</label>
              <input type="file" name="a_avata">
              <p class="help-block">256*256[正方形]</p>
            </div>
            <div class="form-group  col-xs-12">
              <label>备注</label>
              <input type="text" class="form-control" name="a_memo" placeholder="备注内容">
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="submit" id="adminAddButton" class="btn btn-default xcms-button1" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认添加</button>
            <button type="reset" id="resetButton" class="btn btn-default" >重置</button>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->


<!-- 模态框（Modal） 编辑管理员-->
<div class="modal fade" id="modalModifyAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          编辑管理员
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return adminModify();" id="adminModifyForm" name="adminModifyForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="modify" id="modifyop">
          <input type="hidden" name="aid" value="">
          <div class="box-body">
            <div class="form-group col-xs-6">
              <label>账号 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="a_account" disabled="disabled" placeholder="请输入管理员的账号">
            </div>
            <div class="form-group col-xs-6">
              <label>姓名 <span class="xcms-font-red">*</span></label>
              <input type="text" class="form-control" name="a_name" placeholder="请输入管理员的姓名">
            </div>
            <div class="form-group col-xs-6">
              <label for="exampleInputPassword1">密码</label>
              <input type="password" class="form-control" name="a_password" placeholder="设定管理员密码，不修改的话就留空">
            </div>
            <div class="form-group col-xs-6">
              <label for="exampleInputPassword1">密码验证</label>
              <input type="password" class="form-control" name="a_password2" placeholder="管理员密码验证">
            </div>
            <div class="form-group  col-xs-3">
              <label>管理员类型 <span class="xcms-font-red">*</span></label>
              <div class="radio">
                <label>
                  <input type="radio" name="a_type" value="1">
                  超级管理员
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="a_type" value="2">
                  审核员
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="a_type" value="3" checked="">
                  普通管理员
                </label>
              </div>
            </div>
            <div class="form-group  col-xs-3">
              <label>管理员状态 <span class="xcms-font-red">*</span></label>
              <div class="radio">
                <label>
                  <input type="radio" name="a_status" value="1">
                  正常
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" name="a_status" value="0">
                  停用
                </label>
              </div>
            </div>
            <div class="form-group col-xs-3">
              <label>头像图片</label>
              <input type="file" name="a_avata">
              <p class="help-block">256*256[正方形]</p>
            </div>
            <div class="form-group col-xs-3">
              <img src="" data-url="<?php echo uploadUrlAvata;?>" height="100" id="avataImage" />
            </div>
            <div class="form-group  col-xs-12">
              <label>备注</label>
              <input type="text" class="form-control" name="a_memo" placeholder="备注内容">
            </div>
            <div class="form-group  col-xs-12">
              <label>删除账号</label>
              <div class="checkbox">
                <label>
                  <input type="checkbox" name="a_delete" value="1" onchange="changeModifyOp(this.checked)">
                  我要删除本账号(<span style="color:#999999;font-size:11px;">请谨慎勾选，删除后无法恢复。</span>)
                </label>
              </div>
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="submit" id="adminModifyButton" class="btn btn-default xcms-button1" data-loading-text="数据加载中，请稍等..." autocomplete=“off”>确认提交</button>
            
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->



<!-- 模态框（Modal） 权限管理-->
<div class="modal fade" id="modalAuditAdmin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          权限设定
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return adminAudit();" id="adminAuditForm" name="adminAuditForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="audit">
          <input type="hidden" name="aid" value="">
          <div class="box-body">

            <div class="form-group col-xs-6">
              <label id="show_a_account">Lucy账号 (Lucy姓名)</label>
              <label id="show_a_type" class="xcms-padding-left10">普通管理员</label>
            </div>

            <div class="form-group col-xs-6">
                <label id="opMyData">
                  <input type="checkbox" name="oneself" value="1">  <span class="xcms-padding-left5">只允许操作自己的数据 (网站栏目)</span>
                </label>
            </div>

            <div class="form-group col-xs-12">

              <table class="table table-hover table-bordered">
                
                <thead>
                <tr style="background-color: #eeeeee;">
                  <th>栏目名称</th>
                  <th>是否审核</th>
                  <th onclick="checkAll('chid_r[]');" class="xcms-pointer xcms-font-purple" title="点击反向选择">查看</th>
                  <th onclick="checkAll('chid_w[]');" class="xcms-pointer xcms-font-purple" title="点击反向选择">编辑</th>
                  <th onclick="checkAll('chid_d[]');" class="xcms-pointer xcms-font-purple" title="点击反向选择">删除</th>
                  <th onclick="checkAll('chid_a[]');" id="allAudit" class="xcms-pointer xcms-font-purple" title="点击反向选择">审核</th>
                </tr>
                </thead>
                <tbody>
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

    //debug($list);
    if (!empty($list->datas))
    {
      $re=getChild($list->datas);  //递归结构

      foreach($re as $v)
      {
        $qianzhui=qianzhui($v['level']);
?>
                <tr>
                  <td onclick="ck('<?php echo $v["chid"];?>');"><?php echo $qianzhui.' | '.$v["ch_name"];?></td>
                  <td onclick="ck('<?php echo $v["chid"];?>');"><?php echo $channelAudit[$v["ch_audit"]];?></td>
                  <td><input type="checkbox" id="chidr<?php echo $v["chid"];?>" name="chid_r[]" value="<?php echo $v["chid"];?>"></td>
                  <td><input type="checkbox" id="chidw<?php echo $v["chid"];?>" name="chid_w[]" value="<?php echo $v["chid"];?>"></td>
                  <td><input type="checkbox" id="chidd<?php echo $v["chid"];?>" name="chid_d[]" value="<?php echo $v["chid"];?>"></td>
                  <td><?php if (trim($v["ch_ext"])=='') {?><input type="checkbox" id="chida<?php echo $v["chid"];?>" name="chid_a[]" value="<?php echo $v["chid"];?>"><?php }else echo "外接功能";?></td>
                </tr>
<?php
      }
    }

?>
                </tbody>
              </table>

            </div>


          </div>
          <!-- /.box-body -->
<div id="show"></div>
          <div class="box-footer text-center">
            <button type="submit" id="adminAuditButton" class="btn btn-default xcms-button1" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认修改</button>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->
