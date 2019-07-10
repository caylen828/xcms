<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_session"));

unLogin();

echo ajaxReturn(1,"已退出登录!");

