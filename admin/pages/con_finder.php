<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" id="contentId">

    <iframe src="./plugins/finder/index.php" style="width:100%;"  id="iframeId"></iframe>

</div>
<!-- /.content-wrapper -->

<script>
function resizeIframe(){
  $("#iframeId").height($("#contentId").height()-10);
  $("#iframeId").width($("#contentId").width());
  //alert($("#iframeId").height()+' | '+$("#iframeId").width());
}


window.onload=function(){
  resizeIframe();
}

window.onresize=function(){
  resizeIframe();
}

</script>