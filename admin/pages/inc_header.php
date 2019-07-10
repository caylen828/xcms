  <header class="main-header">
    <!-- Logo -->
    <a href="./" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>X</b>CMS</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>X</b>CMS</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
      <span class="xcms-header-title" >
        <?php echo $title;?>
      </span>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?php echo $avataFilename;?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $_SESSION['a_name'];?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="<?php echo $avataFilename;?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo $_SESSION['a_name']." (".$_SESSION['a_account'].")";?>
                  <small>类型：<?php echo $adminType[$_SESSION['a_type']];?> | 登录IP：<?php echo x_getip();?></small>
                </p>
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="./?k=profile" class="btn btn-default btn-flat">个人资料</a>
                </div>
                <div class="pull-right">
                  <a href="#" id="loginOutId2" onclick="javascript:unLogin('profile');" data-loading-text="开始退出登录，请稍等..." class="btn btn-default btn-flat">退出登录</a>
                </div>
              </li>
            </ul>
          </li>

        </ul>
      </div>
    </nav>
  </header>