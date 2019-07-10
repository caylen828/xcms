<?php
//防止注入处理
if(!empty($_POST)){foreach($_POST as $k => $v) {if(!is_array($v)) {$_POST[$k]=htmlspecialchars(urldecode($v));}}}
if(!empty($_GET)){foreach($_GET as $k => $v) {$_GET[$k]=htmlspecialchars(urldecode($v));}}
if(!empty($_SERVER['QUERY_STRING'])) $_SERVER['QUERY_STRING']=htmlspecialchars(urldecode($_SERVER['QUERY_STRING']));
//防注入处理结束

?>