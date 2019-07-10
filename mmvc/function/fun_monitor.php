<?php

//通过记录日志文件来监控运行情况
//参数传递一个要记录的文件名
//当有请求的时候触发本函数进行记录

function monitor_write($filename)
{
	return @file_put_contents($filename,time(),LOCK_EX);
}

function monitor_read($filename)
{
	if (file_exists($filename))
	{
		return file_get_contents($filename);
	}else
	{
		return false;
	}
}

?>