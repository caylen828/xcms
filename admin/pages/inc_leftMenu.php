  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li><a href="./"><i class="fa fa-home"></i> <span class="<?php show_class("text-danger",$k,'');?>">后台首页</span></a></li>
        <li class="header">网站栏目</li>
<?php
    $where='';

    $tmpWhere=$auditCls->limitChannelsSql();
    if ($tmpWhere!='') $where=$tmpWhere;

    //debug($auditCls);
    //echo $tmpWhere;

    $menulists=array(
      "table"   => "xt_channel",            //表名
      "columns" => "chid,ch_name,ch_order,ch_father_id,ch_audit,ch_memo",  //字段
      "where"   => $where,                  //where条件
      "order"   => 'ch_order desc,chid',    //order by条件
      "fnumber" => 10000,                   //每页的数量
      "fid"     => 1                        //当前页码
    );

    $menulist=new xlist($db,$menulists);    //分页取数据类
    $menulist->showPage(10);                //分页页码计算，参数10是显示的页码数，可以修改

    //debug($menulist);
    if (!empty($menulist->datas))
    {
      //判断$chid的下级数据是否存在
      function isHaveData($data,$chid) {
        foreach ($data as $key => $value) {
          if ($value['ch_father_id']==$chid) return true;
        }
        return false;
      }

      //取出当前$chid的所有上级栏目
      function getAllFather($data,$chid){
        $arr=array();
        if (empty($data)) return false;
        $count=0;  //避免死循环
        $father=1;
        $tmpChid=$chid;

        while($father!=0 && $count<100){
          foreach($data as $k => $v){
            if ($v['chid']==$tmpChid){
              $father=$v['ch_father_id'];
              $tmpChid=$v['ch_father_id'];
              if ($v['chid']==$chid) $arr[$v['chid']]=0;
              else $arr[$v['chid']]=1;
              break;
            }
          }
          $count++;  //最多循环100次
        }
        return $arr;
      }

      $thisFathers=array();
      if(isset($par['chid'])) $thisFathers=getAllFather($menulist->datas,$par['chid']);
      //debug($thisFathers);

      function showMenu($data,$parent_id = 0,$level = 0){
          //声明静态数组,避免递归调用时,多次声明导致数组覆盖
          global  $par;  //将par参数设定成全局，就可以获取了
          global  $thisFathers;  //当前选中栏目和父栏目的id
          static  $result;
          foreach ($data as $key => $info){
              //第一次遍历,找到父节点为根节点的节点 也就是parent_id=0的节点

              if($info['ch_father_id'] == $parent_id){
                  $info['level'] = $level;
                  $result[] = $info;
                  $ishavedata=isHaveData($data,$info['chid']);
                  $class="";

                  if (isset($par["chid"])&&$par["chid"].' '==$info['chid'].' '&&$par['k']=='content') $class="bg bg-purple";
                  if (isset($thisFathers[$info['chid']]) && $thisFathers[$info['chid']]==1){
                    $openCls="menu-open";
                    $openSty='style="display:block;"';
                  }else{
                    $openCls="";
                    $openSty='';
                  }

                  echo '<li class="treeview '.$openCls.'">';
                  if ($ishavedata) echo '<a href="./?k=content&chid='.$info['chid'].'"><i class="fa fa-folder"></i> <span>'.$info['ch_name'].'</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>';
                  else echo '<li><a href="./?k=content&chid='.$info['chid'].'" class="'.$class.'"><i class="fa fa-file-o"></i><span>'.$info['ch_name'].'</span></a></li>';
                  
                  //把这个节点从数组中移除,减少后续递归消耗
                  unset($data[$key]);
                  //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1


                  if ($ishavedata) echo '<ul class="treeview-menu" '.$openSty.'>';
                  showMenu($data,$info['chid'],$level+1);
                  if ($ishavedata) echo '</ul>';
                  echo '</li>';

              }

          }
          return $result;
      }

      showMenu($menulist->datas);
    }

?>

        <li class="header">系统功能</li>
        <?php
        if ($_SESSION['a_type']==1 || $_SESSION['a_type']==2){  //这是超级管理员和审核员的功能
        ?>
        <li><a href="./?k=audit" class="<?php show_class("bg bg-purple",$k,"audit");?>"><i class="fa fa-pencil-square-o"></i><span>待审核数据处理</span></a></li>
        <?php
        }
        ?>

        <?php
        if ($_SESSION['a_type']==1){  //这是超级管理员的功能
        ?>
        <li><a href="./?k=admin" class="<?php show_class("bg bg-purple",$k,"admin");?>"><i class="fa fa-user"></i><span>管理员设定</span></a></li>
        <li><a href="./?k=channel" class="<?php show_class("bg bg-purple",$k,"channel");?>"><i class="fa fa-columns"></i><span>网站栏目设定</span></a></li>
        <li><a href="./?k=adminer" class="<?php show_class("bg bg-purple",$k,"adminer");?>"><i class="fa fa-user"></i><span rel="nofollow" data-toggle="tooltip" title="需要单独用数据库管理账号登录">网站数据库管理</span></a></li>
        <li><a href="./?k=finder" class="<?php show_class("bg bg-purple",$k,"finder");?>"><i class="fa fa-user"></i><span rel="nofollow" data-toggle="tooltip" title="注意：小心误操作会造成网站奔溃">网站代码资源管理</span></a></li>
        <?php
        }
        ?>
        <li><a href="./?k=profile" class="<?php show_class("bg bg-purple",$k,"profile");?>"><i class="fa fa-user"></i><span>个人资料</span></a></li>
        <li><a href="#" onclick="javascript:unLogin('');"><i class="fa fa-sign-out"></i> <span id="loginOutId" data-loading-text="开始退出登录，请稍等...">退出登录</span></a></li>


      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>