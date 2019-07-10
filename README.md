# xcms
这是我开发的一个PHP的内容管理系统。使用比较便捷，便于理解和快速开发。小项目用用，上手很快。没什么限制。可以随便改。

## 关于配置，都在xcms根目录下的webconfig.php中
### 主要配置项1：define("RootPath", "/Users/jakcy/Documents/website/xcms/");  //请将RootPath，改成本地实际路径
### 主要配置项2：define("RootUrl", "http://localhost/xcms/");  //请将RootUrl，改成实际的网址url
### 主要配置项3：数据库配置，请对应修改：
··· function getMysqlConfig(){
··· 	return Array (
··· 	    'host'     => 'localhost',
··· 	    'username' => '数据库用户名', 
··· 	    'password' => '密码',
··· 	    'db'       => 'xcms',
··· 	    'port'     => '3306',
··· 	    'prefix'   => '',
··· 	    'charset'  => 'utf8'
··· 	);
··· }

## 数据库文件就是目录中的“数据库SQL文件->xcms.sql”

## 关于后台
### 后台登录地址：你自己网址后面加“/admin”
### 后台登录账号：administrator 密码：admin
