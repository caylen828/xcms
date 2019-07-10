<?php

/**
 * TmongoDB Operation Class
 * TMONGODB IS A LIBRARY FOR MONGODB OPERATION. IT IS FAST AND EASY TO USE.
 *
 * @package     Uacool/TmongoDB
 * @author      thendfeel@gmail.com
 * @link        https://github.com/thendfeel/TmongoDB
 * @example     http://dev.uacool.com
 * @copyright   uacool.com
 * @site        http://www.uacool.com
 * @created     2013-12-13
 * 
 * Manual
 * http://us2.php.net/mongo
 * 
 * SQL to Mongo Mapping Chart
 * http://us2.php.net/manual/en/mongo.sqltomongo.php
 * 
 */
class TmongoDB
{

    private $db = 'mydb';

    private $collection = 'card';

    private $validate = array();

    private $mongoDB;
    
    private $mongo;

    /**
     * Config For MongDB
     *
     * @var array
     */
    private static $_config = array(
        //'host' => '127.0.0.1',//'127.0.0.1',
        //'port' => '27017',
        //'user' => '',
        //'password' => '',
        'str'=>'',
    );

    public function __construct($db = '', $collection = '' ,$config)
    {
        $this->db = $db;
        $this->collection = $collection;
        self::$_config = $config;
        self::init();
    }

    /**
     * Init The Class
     *
     * @param string $db            
     * @param string $collection            
     */
    public function init()
    {
        if (! $this->mongoDB) {
            $config = self::$_config;
            $this->mongo = new Mongo($config['str']);
        }
        $this->selectCollection();
        
    }
    /**
     * select collection
     * @param string $collection
     */
    public function selectCollection( $collection = '')
    {
        if (! $this->mongoDB) {            
            if ($this->db && $collection) {
                $this->mongoDB = $this->mongo->selectCollection($this->db, $collection);
            } else {
                $this->mongoDB = $this->mongo->selectCollection($this->db, $this->collection);
            }
        }
    }

    /**
     * Set Db & Collection
     *
     * @param string $db            
     * @param string $collection            
     */
    public function setDb($db = NULL, $collection = NULL)
    {
        if ($db && $collection) {
            $this->db = $db;
            $this->collection = $collection;
            $this->mongoDB = NULL;
        }
    }

    /**
     * Set Collection
     *
     * @param string $collection            
     */
    public function setCollection($collection = NULL)
    {
        if ($collection) {
            $this->collection = $collection;
            $this->mongoDB = NULL;
        }
    }

    /**
     * Fetch From Mongodb
     *
     * @param array $argv            
     * @param number $skip            
     * @param number $limit            
     * @param array $sort            
     * @return Ambigous <multitype:, multitype:>|boolean
     */
    public function find($argv = array(), $skip = 0, $limit = 30, $sort = array())
    {
        self::init();
        $argv = self::validate($argv);
        if ($argv) {
            $result = $this->mongoDB->find($argv)
                ->skip($skip)
                ->limit($limit)
                ->sort($sort);
            return self::toArray($result);
        }
        return array();
    }

    /**
     * Fetch By MongoId
     *
     * @param string $_id            
     * @return Ambigous <Ambigous, boolean, multitype:>
     */
    public function findById($_id = '')
    {
        if (is_string($_id)&&strlen($_id)==24) {
            $_id = new MongoId($_id);
        }
        return self::findOne(array(
                '_id' => $_id
            ));
    }

    /**
     * Fetch One From MongoDB
     *
     * @param array $argv            
     * @param array $fields            
     * @return multitype: boolean
     */
    public function findOne($argv = array(), $fields = array())
    {
        self::init();
        $argv = self::validate($argv);
        if ($argv) {
            return self::cleanId($this->mongoDB->findOne($argv, $fields));
        }
        return FALSE;
    }

    /**
     * Fetch All From MongoDB
     *
     * @param array $argv            
     * @param array $fields            
     * @return Ambigous <multitype:, multitype:>|boolean
     */
    public function findAll($argv = array(), $fields = array(), $sort = array() )
    {
        self::init();
        $argv = self::validate($argv);
        //if ($argv) {
            $result = $this->mongoDB->find($argv, $fields)->sort($sort);
            return self::toArray($result);
        //}
        //return FALSE;
    }

    /**
     * Find And Modify
     *
     * @param array $argv            
     * @param array $newData            
     * @param array|NULL $fields            
     * @param array $options            
     * @see http://us2.php.net/manual/en/mongocollection.findandmodify.php
     */
    public function findAndModify($argv = array(), $newData = array(), $fields = array(), $options = NULL)
    {
        self::init();
        $argv = self::validate($argv);
        $newData = self::validate($newData);
        return $this->mongoDB->findAndModify($argv, array(
            '$set' => $newData
        ), $fields, $options);
    }

    /**
     * Update MongoDB
     *
     * @param array $argv            
     * @param array $newData            
     * @param string $options            
     */
    public function update($argv = array(), $newData = array(), $options = 'multiple')
    {
        self::init();
        $argv = self::validate($argv);
        $newData = self::validate($newData);
        return $this->mongoDB->update($argv, array(
            '$set' => $newData
        ), array(
            "{$options}" => true
        ));
    }

    /**
     * Update MongoDB
     *
     * @param array $argv            
     * @param array $newData            
     * @param string $options            
     */
    public function updateinc($argv = array(), $newData = array())
    {
        self::init();
        $argv = self::validate($argv);
        $newData = self::validate($newData);
        return $this->mongoDB->update($argv, array(
            '$inc' => $newData
        ));
    }
    /**
     * Update MongoDB By Id
     *
     * @param string $_id            
     * @param array $newData            
     */
    public function updateById($_id, $newData = array())
    {
        $result = array();
        if (is_string($_id)) {
            $result = self::update(array(
                '_id' => new MongoId($_id)
            ), $newData);
        }
        return $result;
    }

    /**
     * Insert Into Mongodb
     *
     * @param array $data            
     */
    public function insert($data = array())
    {
        self::init();
        $data = self::validate($data);
        $s = '$id';
        $this->mongoDB->insert($data);
        if(is_object($data['_id'])){
        	$res = $data['_id']->$s;
        }else{
        	$res = $data['_id'];
        }
        return $res;
    }

    /**
     * Remove All From Mongodb
     *
     * @param array $argv            
     */
    public function remove($argv = array())
    {
        self::init();
        $argv = self::validate($argv);
        return $this->mongoDB->remove($argv);
    }

    /**
     * Remove By Id From Mongodb
     *
     * @param string $_id            
     * @return Ambigous <boolean, multitype:>
     */
    public function removeById($_id)
    {
        return self::removeOne(array(
            '_id' => new MongoId($_id)
        ));
    }

    /**
     * Remove One From Mongodb
     *
     * @param array $argv            
     */
    public function removeOne($argv = array())
    {
        self::init();
        $argv = self::validate($argv);
        return $this->mongoDB->remove($argv, array(
            "justOne" => true
        ));
    }

    /**
     * Remove Field From MongoDB
     *
     * @param string $_id            
     * @param array $field            
     */
    public function removeFieldById($_id, $field = array())
    {
        self::init();
        $unSetfield = array();
        foreach ($field as $key => $value) {
            if (is_int($key)) {
                $unSetfield[$value] = TRUE;
            } else {
                $unSetfield[$key] = $value;
            }
        }
        return $this->mongoDB->update(array(
            '_id' => new MongoId($_id)
        ), array(
            '$unset' => $unSetfield
        ));
    }

    /**
     * Count
     *
     * @param unknown $argv            
     */
    public function count($argv = array())
    {
        self::init();
        $argv = self::validate($argv);
        return $this->mongoDB->count($argv);
    }
    /**
     * 带distinct的count方法
     * @param type $collection 表名
     * @param type $key 字段名，对哪个字段做distinct
     * @param type $argv 查询条件
     * @return int
     */
    public function getDistinctCount($collection,$key,$argv = array())
    {
        self::init();
        $argv = self::validate($argv);
        $command['distinct'] = $collection;
        $command['key'] = $key;
        $command['query'] = $argv;
        $db = $this->mongo->selectDB($this->db);
        $arr = $db->command($command);
        if($arr['ok']==1)
        {
            $count = count($arr['values']);
        }else{
            $count = 0;
        }
        return $count;
    }
    /**
     * Mongodb Object To Array
     *
     * @param array $data
     * @return multitype:
     */
    private function toArray($data)
    {
        return self::cleanId(iterator_to_array($data));
    }

    /**
     * Clear Mongo _id
     *
     * @param array $data            
     * @return void unknown
     */
    private function cleanId($data)
    {
//        $s = '$id';
        if (isset($data['_id'])) {
            $data['_id'] = $data['_id'];//->$s;
            return $data;
        } elseif ($data) {
            foreach ($data as $key => $value) {
                $data[$key]['_id'] = $value['_id'];//->$s;
            }
        }
        return $data;
    }

    /**
     * Validate Data Callbak Function
     *
     * @param array $argv            
     */
    private function validate($data)
    {
        if ($this->validate) {
            foreach ($this->validate as $arg => $validate) {
                if (is_array($data) && array_key_exists(strval($arg), $data)) {
                    foreach ($validate as $key => $value) {
                        switch (strtolower($key)) {
                            case 'type':
                                if ($value == 'int') {
                                    $data[$arg] = (int) $data[$arg];
                                } elseif ($value == 'string') {
                                    $data[$arg] = (string) $data[$arg];
                                } elseif ($value == 'bool') {
                                    $data[$arg] = (bool) $data[$arg];
                                } elseif ($value == 'float') {
                                    $data[$arg] = (float) $data[$arg];
                                } elseif ($value == 'array') {
                                    $data[$arg] = (array) $data[$arg];
                                }
                                break;
                            case 'min':
                                if (strlen($data[$arg]) < $value) {
                                    exit('Error: The length of ' . $arg . ' is not matched');
                                }
                                break;
                            case 'max':
                                if (strlen($data[$arg]) > $value) {
                                    exit('Error: The length of ' . $arg . ' is not matched');
                                }
                                break;
                            case 'func':
                                $call = preg_split('/[\:]+|\-\>/i', $value);
                                if (count($call) == 1) {
                                    $data[$arg] = call_user_func($call['0'], $data[$arg]);
                                } else {
                                    $data[$arg] = call_user_func_array(array(
                                        $call['0'],
                                        $call['1']
                                    ), array(
                                        $data[$arg]
                                    ));
                                }
                                break;
                        }
                    }
                }
            }
        }
        return $data;
    }
    
 /**
  * 获取表的最大id序列号 
  * @param string $name 获取表的表名
  */
    function getMaxId($name){
    	
    	$command['findAndModify'] = 'counters';//表名 存储表id最大值的
    	$command['query'] = array('_id' => $name);//查询条件  $name指的是哪个表的id自增
    	$command['update'] = array('$inc' => array('seq' => 1));
    	$command['upsert'] = true;//若是第一次创建，upsert一定要写上，否则，不会出现自增id
    	$command['new'] = true;
		
    	$db = $this->mongo->selectDB($this->db);
    	$data = $db->command($command);

    	if (isset($data['ok']) && $data['value']['seq']) {
    		return $data['value']['seq'];
    	}
    	return false;

    }
    /**
     * 获取秒，毫秒的MongoDate对象
     * @return \MongoDate
     */
    function getMongoDate($s=-1){
        $us=0;
        if($s<0){
                $microtimeTrue = microtime(true);
                $s = intval($microtimeTrue);
                $us = intval(($microtimeTrue-$s)*1e6);
        }
        return new MongoDate($s+28800, $us);
    }
     /*
     * mongo的get/set方法
     * 调用get 返回mongo
     */
    function setMongo($mongo){
    	$this->mongo = $mongo;
    }
    function getMongo(){
    	return $this->mongo;
    }
    /**
     * 统计最近$days天的每天点击广告点击数
     * @param int $uid 广告主id
     * @param int $cid 广告计划id
     * @param int $days 需要统计的天数
     * @return array
     */
    function clickStati($uid, $cid, $days = 7){
        $return = array();
        $date = strtotime(date('Y-m-d', strtotime("-$days day")));
        $return = $this->mongo->selectCollection($this->db, 'mt_advertis_click')->aggregate(
                array('$match' => array("user_id" => $uid, "campaign_id" => $cid, "create_dt" => array('$gt' =>$this->getMongoDate($date)))), array('$project' => array("user_id" => '$user_id', "campaign_id" => '$campaign_id',
                "cyear" => array('$year' => '$create_dt'),
                "cmonth" => array('$month' => '$create_dt'),
                "cday" => array('$dayOfMonth' => '$create_dt'))), array('$group' => array('_id' =>
                array("user_id" => '$user_id', "campaign_id" => '$campaign_id', "cyear" => '$cyear', "cmonth" => '$cmonth', "cday" => '$cday'),
                "tot_qty" => array('$sum' => 1))), array('$sort' => array("user_id" => 1, "campaign_id" => 1))
        );
        return $return;
    }
    /**
     * 统计最近$days天的每天广告展示数
     * @param int $uid 广告主id
     * @param int $cid 广告计划id
     * @param int $days 需要统计的天数
     * @return array
     */
    function showStati($uid,$cid='',$days = 7){
        $return = array();
        $date = strtotime(date('Y-m-d', strtotime("-$days day")));
        if(empty($cid)){
            $match = array("user_id" => $uid, "create_dt" => array('$gt' =>$this->getMongoDate($date)));
        }else{
            $match = array("user_id" => $uid, "campaign_id" => $cid, "create_dt" => array('$gt' =>$this->getMongoDate($date)));
        }
        $return = $this->mongo->selectCollection($this->db, 'mt_advertis_show')->aggregate(
                array('$match' => $match), array('$project' => array("user_id" => '$user_id', "campaign_id" => '$campaign_id',
                "cyear" => array('$year' => '$create_dt'),
                "cmonth" => array('$month' => '$create_dt'),
                "cday" => array('$dayOfMonth' => '$create_dt'))), array('$group' => array('_id' =>
                array("user_id" => '$user_id', "campaign_id" => '$campaign_id', "cyear" => '$cyear', "cmonth" => '$cmonth', "cday" => '$cday'),
                "tot_qty" => array('$sum' => 1))), array('$sort' => array("user_id" => 1, "campaign_id" => 1))
        );
        return $return;
    }
    /**
     * 按广告计划统计某计划最近$days天的每天新增人脉数
     * @param int $uid 广告主id
     * @param int $campid 计划id
     * @param int $days 需要统计的天数
     * @return array
     */
    function peopleStati($uid,$campid, $days = 7){
        $return = array();
        $date = strtotime(date('Y-m-d', strtotime("-$days day")));
        $return = $this->mongo->selectCollection($this->db, 'mt_campaign_audience')->aggregate(
                array('$match' => array("user_id" => $uid,"campaign_id"=>$campid, "date.c_modify" => array('$gt' =>$this->getMongoDate($date)))), array('$project' => array("user_id" => '$user_id', "campaign_id" => '$campaign_id',
                "cyear" => array('$year' => '$date.c_modify'),
                "cmonth" => array('$month' => '$date.c_modify'),
                "cday" => array('$dayOfMonth' => '$date.c_modify'))), array('$group' => array('_id' =>
                array("user_id" => '$user_id', "campaign_id" => '$campaign_id', "cyear" => '$cyear', "cmonth" => '$cmonth', "cday" => '$cday'),
                "tot_qty" => array('$sum' => 1))), array('$sort' => array("user_id" => 1, "campaign_id" => 1))
        );
        return $return;
    }
    /**
     * 按广告主统计网站人脉最近$days天的每天新增人脉数
     * @param int $uid 广告主id
     * @param string $type 是查网站的（website）、邮件的（mail）、还是所有的（total） 
     * @param int $days 需要统计的天数
     * @return array
     */
    function mappingStati($uid,$type = 'total',$days = 7){
        $return = array();
        $date = strtotime(date('Y-m-d', strtotime("-$days day")));
        $match = array('$match' => array("user_id" => $uid,"date.c_create" => array('$gt' =>$this->getMongoDate($date))));
        if($type=='website'){
            $match = array('$match' => array("user_id" => $uid,"date.c_create" => array('$gt' =>$this->getMongoDate($date))));
        }else if($type=='mail'){
            $match = array('$match' => array("user_id" => $uid,"date.c_create" => array('$gt' =>$this->getMongoDate($date))));
        }
        $return = $this->mongo->selectCollection($this->db, 'mt_cookie_mapping')->aggregate(
                $match, array('$project' => array("user_id" => '$user_id',
                "cyear" => array('$year' => '$date.c_modify'),
                "cmonth" => array('$month' => '$date.c_modify'),
                "cday" => array('$dayOfMonth' => '$date.c_modify'))), array('$group' => array('_id' =>
                array("user_id" => '$user_id', "cyear" => '$cyear', "cmonth" => '$cmonth', "cday" => '$cday'),
                "tot_qty" => array('$sum' => 1))), array('$sort' => array("user_id" => 1, "campaign_id" => 1))
        );
        return $return;
    }
    /**
     * 按广告主统计邮件人脉最近$days天的每天新增人脉数
     * @param int $uid 广告主id
     * @param string $type 是查网站的（website）、邮件的（mail）、还是所有的（total） 
     * @param int $days 需要统计的天数
     * @return array
     */
    function mailMappingStati($uid,$days = 7){
        $return = array();
        $date = strtotime(date('Y-m-d', strtotime("-$days day")));
        $match = array('$match' => array("user_id" => $uid,"date" => array('$gt' =>$this->getMongoDate($date))));        
        $return = $this->mongo->selectCollection($this->db, 'mt_email_cookie_stat')->aggregate(
                $match, array('$project' => array("user_id" => '$user_id',
                "cyear" => array('$year' => '$date'),
                "cmonth" => array('$month' => '$date.c_modify'),
                "cday" => array('$dayOfMonth' => '$date.c_modify'))), array('$group' => array('_id' =>
                array("user_id" => '$user_id', "cyear" => '$cyear', "cmonth" => '$cmonth', "cday" => '$cday'),
                "tot_qty" => array('$sum' => 1))), array('$sort' => array("user_id" => 1, "campaign_id" => 1))
        );
        return $return;
    }

}