<?php
include("webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","xlist","fun_content","audit"));
$db = new MysqliDb(getMysqlConfig());  //初始化数据库连接

$cls="style='color:#ff6360;'";

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta content="IE=edge" http-equiv="X-UA-Compatible">
  <meta content="width=device-width,initial-scale=1" name="viewport">
  <meta content="Page description" name="description">
  <meta name="google" content="notranslate" />
  <meta content="" name="author">

  <!-- Disable tap highlight on IE -->
  <meta name="msapplication-tap-highlight" content="no">
  
  <link href="./web/assets/apple-icon-180x180.png" rel="apple-touch-icon">
  <link href="./web/assets/favicon.ico" rel="icon">



  <title>心桥-心理剧-官网</title>  

<link href="./web/main.css" rel="stylesheet"></head>

<body>

<!-- Add your content of header -->
<header class="">
  <div class="navbar navbar-default visible-xs">
    <button type="button" class="navbar-toggle collapsed">
      <span class="sr-only"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a href="./" class="navbar-brand">心桥-心理剧场</a>
  </div>

  <nav class="sidebar">
    <div class="navbar-collapse" id="navbar-collapse">
      <div class="site-header hidden-xs">
          <a class="site-brand" href="./" title="">
            <img class="img-responsive site-logo" alt="" src="./web/assets/images/mashup-logo.svg">
            心桥
          </a>
        <p>心理剧广场</p>
      </div>
      <ul class="nav">
        <li><a href="./" title="" <?php if (!isset($par['cid'])) echo $cls;?>>首页</a></li>

        <hr>
        <h4>演出记录</h4>
        <?php
        $datas=getContentsByChid(75,$db,100,0,"cid,c_title");
        if ($datas){
          foreach ($datas as $k => $v) {
        ?>

          <li><a href="./?cid=<?php echo $v['cid'];?>" title="" <?php if (isset($par['cid']) && $par['cid']==$v['cid']) echo $cls;?>><?php echo $v['c_title'];?></a></li>

        <?php
          }
        }
        ?>

        <hr>
        <h4>常用剧本</h4>
        <?php
        $datas=getContentsByChid(74,$db,100,0,"cid,c_title");
        if ($datas){
          foreach ($datas as $k => $v) {
        ?>

          <li><a href="./?cid=<?php echo $v['cid'];?>" title="" <?php if (isset($par['cid']) && $par['cid']==$v['cid']) echo $cls;?>><?php echo $v['c_title'];?></a></li>

        <?php
          }
        }
        ?>

        <hr>
        <?php
        $datas=getContentsByChid(81,$db,100,0,"cid,c_title");
        if ($datas){
          foreach ($datas as $k => $v) {
        ?>

          <li><a href="./?cid=<?php echo $v['cid'];?>" title="" <?php if (isset($par['cid']) && $par['cid']==$v['cid']) echo $cls;?>><?php echo $v['c_title'];?></a></li>

        <?php
          }
        }
        ?>
      </ul>


      <!--
      <nav class="nav-footer">
        <p class="nav-footer-social-buttons">
          <a class="fa-icon" href="https://www.instagram.com/" title="">
            <i class="fa fa-instagram"></i>
          </a>
          <a class="fa-icon" href="#" title="">
            <i class="fa fa-dribbble"></i>
          </a>
          <a class="fa-icon" href="#" title="">
            <i class="fa fa-twitter"></i>
          </a>
        </p>
        <p>© 技术支持 ：<a href="http://www.sh-xuanji.com/" target="_blank" title="上海玄极信息科技有限公司">上海玄极</a></p>
      </nav>  
    -->

    </div> 
  </nav>
</header>
<main class="" id="main-collapse">

<!-- Add your site or app content here -->
<?php
if (!isset($par['cid'])) include("inc_index.php");
else include("inc_content.php");
?>

</main>
<script type="text/javascript" src="./web/main.js"></script></body>

<script>
document.addEventListener("DOMContentLoaded", function (event) {
  navbarToggleSidebar();
  navActivePage();
});
</script>
</html>