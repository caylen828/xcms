<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
  	<div class="row">
		<div class="box">
			<div class="box-header with-border" style="padding:20px 0px;">
			    <center><h3 class="box-title" id="a_account"><?php echo $_SESSION['a_account'];?> 的个人资料</h3></center>
			</div>
			<!-- /.box-header -->
			<!-- form start -->
			<form role="form" class="form-horizontal" action="" method="post" onsubmit="return profileModify();" id="profileModifyForm" name="profileModifyForm" enctype="multipart/form-data">
				<input type="hidden" name="aid" value="<?php echo $_SESSION['aid'];?>">
				<input type="hidden" name="op" value="modify">
			  <div class="box-body">
			    <div class="form-group">
			      <label class="col-sm-2 control-label">姓名</label>
			      <div class="col-sm-4"><input type="text" value="<?php echo $_SESSION['a_name'];?>" class="form-control" name="a_name" id="a_name" placeholder="姓名"></div>
			    </div>
			    <div class="form-group">
			      <label class="col-sm-2 control-label">备注</label>
			      <div class="col-sm-8"><textarea name="a_memo" class="form-control" id="a_memo" rows="5" placeholder="备注内容"><?php echo $_SESSION['a_memo'];?></textarea></div>
			    </div>
			    <div class="form-group">
			      <label class="col-sm-2 control-label">头像文件</label>
			      <div class="col-sm-10">
			      	<input type="file" id="a_avata" name="a_avata">
			      	<span class="help-block">必须图片文件：jpg/jpeg/png/gif<br />建议图片大小：200px*200px</span>
			      </div>

			    </div>
			    <div class="form-group">
			      <label class="col-sm-2 control-label">现在头像</label>
			      <div class="col-sm-10">
			      	<img src="<?php echo $avataFilename;?>" width="160" height="160" id="historyAvata">
			      </div>
			    </div>
			  </div>
			  <!-- /.box-body -->

			  <div class="box-footer">
			  	<center>
				    <button type="submit" id="profileSub" class="btn btn-primary" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认提交</button>
				    <button type="button" class="btn btn-default xcms-margin-left10" onclick="showModal('modalModifyPassword');">修改密码</button>
			    </center>
			  </div>
			</form>
		</div>
	</div>
  </section>
  <!-- /.content -->

</div>
<!-- /.content-wrapper -->



<!-- 模态框（Modal） 修改密码-->
<div class="modal fade" id="modalModifyPassword" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
          &times;
        </button>
        <h4 class="modal-title" id="myModalLabel">
          修改密码
        </h4>
      </div>
      
      <div class="box box-default bg-white">
        <!-- /.box-header -->
        <!-- form start -->
        <form role="form" action="" method="post" onsubmit="return modifyPassword();" id="modifyPasswordForm" name="modifyPasswordForm" enctype="multipart/form-data">
          <input type="hidden" name="op" value="modifyPassword">
          <input type="hidden" name="aid" value="<?php echo $_SESSION['aid'];?>">
          <div class="box-body">
            <div class="form-group col-xs-12">
              <label>原始密码 <span class="xcms-font-red">*</span></label>
              <input type="password" class="form-control" name="a_password_old" placeholder="请输入原来的密码">
            </div>
            <div class="form-group col-xs-12">
              <label for="exampleInputPassword1">新密码 <span class="xcms-font-red">*</span></label>
              <input type="password" class="form-control" name="a_password" placeholder="请设定新密码">
            </div>
            <div class="form-group col-xs-12">
              <label for="exampleInputPassword1">密码验证 <span class="xcms-font-red">*</span></label>
              <input type="password" class="form-control" name="a_password2" placeholder="再一次输入新密码">
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer text-center">
            <button type="submit" id="modifyPasswordButton" class="btn btn-default xcms-button1" data-loading-text="数据提交中，请稍等..." autocomplete=“off”>确认修改</button>
            <button type="reset" id="resetButton" class="btn btn-default" >重置</button>
          </div>
        </form>
      </div>
      
      <!-- /.box-footer -->
            
    </div><!-- /.modal-content -->
  </div>
</div><!-- /.modal -->
