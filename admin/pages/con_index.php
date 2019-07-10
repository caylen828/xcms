<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <center style="padding-top:100px;">
    	<h2>欢迎<?php echo '" '.$_SESSION['a_name'].' "';?>使用XCMS</h2>
    	<div style="padding:20px 0px;">
    	<h4>当前时间为：<?php echo x_date();?></h4>
    	<h4>客户端IP为：<?php echo x_getip();?></h4>
    	</div>
    </center>
  </section>
  <!-- /.content -->

</div>
<!-- /.content-wrapper -->