<?php 
session_start();
include("../webconfig.php");
minclude(array("fun_safe","fun_session","mysqliDb","fun_main","fun_ajaxReturn","xlist","fun_content","audit"));

//如果登录状态没有了，就回到登录页
if (!isLogin("aid")) header("Location: login.html");

$db = new MysqliDb(getMysqlConfig());  //初始化数据库连接
$auditCls = new audit();//权限类库

//用户头像文件
$avataFilename=uploadUrlAvata.'default.jpg';
if (trim($_SESSION['a_avata'])!='') {
  if (file_exists(uploadDirAvata.$_SESSION['a_avata'])) $avataFilename=uploadUrlAvata.$_SESSION['a_avata'];
}

//初始化页面的菜单和标题关系
$k='';
if (isset($par['k'])) $k=$par['k'];

$title='首页';
$file='con_index.php';

if ($k=='admin') {
  $title='管理员设定';
  $file='con_admin.php';
}
elseif ($k=='channel') {
  $title='网站栏目设定';
  $file='con_channel.php';
}
elseif ($k=='profile') {
  $title='个人资料';
  $file='con_profile.php';
}
elseif ($k=='audit') {
  $title='待审核数据处理';
  $file='con_audit.php';
}
elseif ($k=='adminer') {
  $title='网站数据库管理';
  $file='con_adminer.php';
}
elseif ($k=='finder') {
  $title='网站代码资源管理';
  $file='con_finder.php';
}
elseif ($k=='content') {

  if (!isset($par['chid'])) {$title='网站栏目 -> 栏目页错误';$file='inc_404.php';}
  elseif (!x_isint($par['chid'])) {$title='网站栏目 -> 栏目页错误';$file='inc_404.php';}
  else 
  {
    $ch=getChannelById($par['chid'],$db);
    if ($ch){
      $title='网站栏目 -> '.$ch['ch_name'].' <span style="font-size:12px;">（ '.$channelAudit[$ch['ch_audit']].' ）</span>';
      if(trim($ch['ch_ext'])=='') $file='con_content.php';
      else $file='ext_'.$ch['ch_ext'].'.php';
    }else{
      $title='网站栏目 -> 栏目页错误';
      $file='inc_404.php';
    }
  }

}

$tmpFile="pages/".$file;
if (!file_exists($tmpFile)){
    $title='文件不存在：'.$tmpFile;
    $tmpFile="pages/inc_404.php";
}

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>XCMS 后台管理 | <?php echo $title;?></title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="../frontFrame/xcms/js/html5shiv.min.js"></script>
  <script src="../frontFrame/xcms/js/respond.min.js"></script>
  <![endif]-->
  <link rel="stylesheet" href="../frontFrame/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../frontFrame/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="../frontFrame/Ionicons/css/ionicons.min.css">
  <link rel="stylesheet" href="../frontFrame/adminLte/css/AdminLTE.css">
  <link rel="stylesheet" href="../frontFrame/adminLte/css/skins/_all-skins.css">
  <link rel="stylesheet" href="../frontFrame/plugins/toastr/toastr.css">
  <link rel="stylesheet" href="../frontFrame/xcms/css/xcms.css">

  <!-- js -->
  <script src="../frontFrame/jquery/jquery.min.js"></script>
  <script src="../frontFrame/bootstrap/js/bootstrap.min.js"></script>
  <script src="../frontFrame/adminLte/js/adminlte.min.js"></script>
  <script src="../frontFrame/plugins/fastclick/fastclick.js"></script>
  <script src="../frontFrame/plugins/bootbox/bootbox.all.min.js"></script>
  <script src="../frontFrame/plugins/toastr/toastr.min.js"></script>
  <script src="../frontFrame/xcms/js/ajax.js"></script>
  <script src="../frontFrame/xcms/js/main.js"></script>

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>

<body class="sidebar-mini skin-purple-light">
<div class="wrapper">

  <?php
  include("pages/inc_header.php");
  include("pages/inc_leftMenu.php");

  //下面是内容页部分的加载，需要做一些处理
  include($tmpFile);

  include("pages/inc_foot.php");
  ?>

</div>
<!-- ./wrapper -->
</body>

</html>
