<?php
//redis主类库,支持单例
class MyRedis {
	private static $instanceMap=array();
	public static function getInstance($ipAddr="192.168.20.189"){
		if(!isset(MyRedis::$instanceMap[$ipAddr])) MyRedis::$instanceMap[$ipAddr]=new MyRedis($ipAddr, 6379);
		return MyRedis::$instanceMap[$ipAddr];
	}
	
	//////////////////////////////////////////////////////////////////////////////////////
	// 分隔声明
	//////////////////////////////////////////////////////////////////////////////////////
	
    private $redis;
    /**
     * @param string $host
     * @param int $post
     */
    public function __construct($host = '127.0.0.1', $port = 6379) {
        $this->redis = new Redis();
        $this->redis->connect($host, $port);
        return $this->redis;
    }

    /**
     * 选择数据库
     * @param int $dbNumber 数据库编号
     */
    public function selectDb($dbNumber=0) {
        return $this->redis->select($dbNumber);
    }
	
    /**
     * 设置值  构建一个字符串
     * @param string $key KEY名称
     * @param string $value  设置值
     * @param int $timeOut 时间，秒数，代表多少秒后失效  0表示无过期时间
     */
    public function set($key, $value, $timeOut=0) {
        if ($timeOut > 0)
		{
			return $this->redis->setex($key, $timeOut,$value);
		}else
		{
			return $this->redis->set($key, $value);
		}
    }

    /*
     * 构建一个集合(无序集合)
     * @param string $key 集合Y名称
     * @param string|array $value  值
     */
    public function sadd($key,$value){
        return $this->redis->sadd($key,$value);
    }
    
    /*
     * 构建一个集合(有序集合)
     * @param string $key 集合名称
     * @param string|array $value  值
     */
    public function zadd($key,$value){
        return $this->redis->zadd($key,$value);
    }
    
    /**
     * 取集合对应元素
     * @param string $setName 集合名字
     */
    public function smembers($setName){
        return $this->redis->smembers($setName);
    }

    /**
     * 构建一个列表(先进后去，类似栈)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function lpush($key,$value){
        echo "$key - $value \n";
        return $this->redis->LPUSH($key,$value);
    }
    
      /**
     * 构建一个列表(先进先去，类似队列)
     * @param sting $key KEY名称
     * @param string $value 值
     */
    public function rpush($key,$value){
        echo "$key - $value \n";
        return $this->redis->rpush($key,$value);
    }
    /**
     * 获取所有列表数据（从头到尾取）
     * @param sting $key KEY名称
     * @param int $head  开始
     * @param int $tail     结束
     */
    public function lranges($key,$head,$tail){
        return $this->redis->lrange($key,$head,$tail);
    }
    
    /**
     * HASH类型
     * @param string $tableName  表名字key
     * @param string $key            字段名字
     * @param sting $value          值
     */
    public function hset($tableName,$field,$value){
        return $this->redis->hset($tableName,$field,$value);
    }
    
    public function hget($tableName,$field){
        return $this->redis->hget($tableName,$field);
    }
    
    /**
     * 设置多个值
     * @param array $keyArray KEY名称
     * @param string|array $value 获取得到的数据
     * @param int $timeOut 时间
     */
    public function sets($keyArray, $timeout) {
        if (is_array($keyArray)) {
            $retRes = $this->redis->mset($keyArray);
            if ($timeout > 0) {
                foreach ($keyArray as $key => $value) {
                    $this->redis->expire($key, $timeout);
                }
            }
            return $retRes;
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }

    /**
     * 通过key获取数据
     * @param string $key KEY名称
     */
    public function get($key) {
        $result = $this->redis->get($key);
        return $result;
    }

    /**
     * 同时获取多个值
     * @param ayyay $keyArray 获key数值
     */
    public function gets($keyArray) {
        if (is_array($keyArray)) {
            return $this->redis->mget($keyArray);
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }
    
    /**
     * 从一个库移动到另一个库
     */
    public function move($key, $toDB) {
    	return $this->redis->move($key, $toDB);
    }
    
    /**
     * 获取所有key名，不是值
     */
    public function keys() {
        return $this->redis->keys('*');
    }

    /**
     * 获取所有匹配到的KEY名
     */
    public function keyLike($middle,$left="*",$right="*") {
        return $this->redis->keys($left.$middle.$right);
    }

    /**
     * 删除一条数据key
     * @param string $key 删除KEY的名称
     */
    public function del($key) {
        return $this->redis->delete($key);
    }

    /**
     * 同时删除多个key数据
     * @param array $keyArray KEY集合
     */
    public function dels($keyArray) {
        if (is_array($keyArray)) {
            return $this->redis->del($keyArray);
        } else {
            return "Call  " . __FUNCTION__ . " method  parameter  Error !";
        }
    }
    
    /**
     * 数据增加一个值
     * @param string $key KEY名称
     */
    public function incrby($key, $value) {
        return $this->redis->incrby($key, $value);
    }
    
    /**
     * 数据自增
     * @param string $key KEY名称
     */
    public function incr($key) {
        return $this->redis->incr($key);
    }
    
    /**
     * 数据自减
     * @param string $key KEY名称
     */
    public function decr($key) {
        return $this->redis->decr($key);
    }
   
    /**
     * 判断key是否存在
     * @param string $key KEY名称
     */
    public function isExists($key){
        return $this->redis->exists($key);
    }

    /**
     * 重命名- 当且仅当newkey不存在时，将key改为newkey ，当newkey存在时候会报错哦RENAME   
     *  和 rename不一样，它是直接更新（存在的值也会直接更新）
     * @param string $Key KEY名称
     * @param string $newKey 新key名称
     */
    public function updateName($key,$newKey){
        return $this->redis->RENAMENX($key,$newKey);
    }
    
   /**
    * 获取KEY存储的值类型
    * none(key不存在) int(0)  string(字符串) int(1)   list(列表) int(3)  set(集合) int(2)   zset(有序集) int(4)    hash(哈希表) int(5)
    * @param string $key KEY名称
    */
    public function dataType($key){
        return $this->redis->type($key);
    }

    /**
     * 清空数据
     */
    public function flushDb() {
        return $this->redis->flushDb();
    }

    /**
     * 返回redis对象
     * redis有非常多的操作方法，我们只封装了一部分
     * 拿着这个对象就可以直接调用redis自身方法
     * eg:$redis->redisOtherMethods()->keys('*a*')   keys方法没封
     */
    public function redisSelf() {
        return $this->redis;
    }

    /**
     * 查看现在数据库有多少key
     */
    public function dbSize() {
        return $this->redis->dbSize();
    }

    /**
     * 设置KEY的有效时间，秒数
	 * @param string $key KEY名称
	 * @param int $time 时间，秒数
     */
    public function setTimeOut($key,$time) {
        return $this->redis->setTimeout($key,$time);
    }

    /**
     * 设置KEY的失效时间
	 * @param string $key KEY名称
	 * @param int $time 时间戳
     */
    public function expireAt($key,$time) {
        return $this->redis->expireAt($key,$time);
    }

    /**
     * 设置key的失效时间
     * @param $key
     * @param $time 多少秒后失效
     */
    public function expire($key,$time) {
        return $this->redis->expire($key,$time);
    }

    /**
     * 获取一个KEY的生存时间
	 * @param string $key KEY名称
     */
    public function ttl($key) {
        return $this->redis->ttl($key);
    }

    /**
     * 强制对数据库永久化，此操作会消耗服务器性能，对磁盘有读写操作
     */
    public function dbAof() {
        return $this->redis->bgrewriteaof();
    }

    /**
     * 游标程序
     * @param unknown_type $c     游标，首次为(int)0,最后一次返回(int)0
     * @param unknown_type $match 匹配条件
     * @param unknown_type $count 返回数量
     */
    public function scan(&$c, $match=null, $count=null) {
    	return $this->redis->scan($c, $match, $count);
    }
}

/*

监控服务：10
广告主id列表：14
过滤：11~22
官网访问，超长网址记录：23
IP库：24
RealTime：25
百度新cookie整理（临时）：26,27,28,29,30,31,33,34,35
人群关系日统计：32
TimeLine缓存：33
转换人脉官网访问次数：34(按月累加，保留4个月)
工商银行ip统计库：36

"EAD_TRACE_cookie_email"                   =>11,
"EAD_TRACE_ip_agent_email"                 =>12,
"EDM_CLICK_cookie_uid_threeid_receiver"    =>13,
"EDM_CLICK_ip_agent_uid_threeid_receiver"  =>14,
"EDM_TRACE_cookie_uid_threeid_receiver"    =>15,
"EDM_TRACE_ip_agent_uid_threeid_receiver"  =>16,
"MAPPING_BAIDU_cookie"                     =>17,
"MAPPING_BAIDU_ip_agent"                   =>18,
"MAPPING_TANX_cookie"                      =>19,
"MAPPING_TANX_ip_agent"                    =>20,
"WEBSITE_cookie_url"                       =>21,
"WEBSITE_ip_agent_user_id"                 =>22,

 */
?>
