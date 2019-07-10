<?php
session_start();
include("../../webconfig.php");
minclude(array("fun_safe","mysqliDb","fun_main","fun_ajaxReturn","fun_session"));

$db = new MysqliDb(getMysqlConfig());

/*
0. 先对参数做基础验证
1. 判断账户密码的正确性
2. 登录成功需要写入session
3. 返回登录结果
*/

if (empty($_POST["admin_account"])) exit(ajaxReturn(0,"账号不能为空。"));
if (empty($_POST["admin_password"])) exit(ajaxReturn(0,"密码不能为空。"));

$admin = $db->rawQuery('SELECT * from xt_admin where a_account = ?', Array ($_POST["admin_account"]));
if ($db->count==0) exit(ajaxReturn(0,"当前账号不存在！"));
if ($admin[0]['a_password']!=md5($_POST["admin_password"])) exit(ajaxReturn(0,"密码错误！"));
if ($admin[0]['a_status']!=1) exit(ajaxReturn(0,"账号停用，无法登录！"));

//写入session值
addSession($admin[0]);

echo ajaxReturn(1,"登录成功！",$_POST);

