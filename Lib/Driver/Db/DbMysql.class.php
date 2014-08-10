<?php
!defined(START_PATH)||_404();
/*--------------------------------------------------------------------------------------
*  StartPHP Version1.0  
*---------------------------------------------------------------------------------------
*  Copyright (c) 2013-2015 All rights reserved.
*---------------------------------------------------------------------------------------
*  Web: www.startphp.cn
*---------------------------------------------------------------------------------------
*  Data:2013-11-6
*---------------------------------------------------------------------------------------
*  Author: StartPHP (shuliangfu@sina.cn)
*---------------------------------------------------------------------------------------
*/
 
/**
 * Mysql引擎处理类
 * @author 舒良府
 *
 */
class DbMysql extends Db{
	static protected $db_link = null; 	//是否连接
	static private $cache=array();	// 数据缓存
	
	
	/**
	 * 获取连接资源
	 * @access public
	 * @see DbIinterface::link()
	 * @return resource;
	 */
    public function link() {
        if (is_null(self::$db_link)) {
            $link = mysql_connect(C("DB_HOST"), C("DB_USER"), C("DB_PWD"));
            if (!$link) {
                error(mysqli_connect_error()); //数据库连接出错了请检查配置文件中的参数
            } else {
                self::$db_link = $link;
                self::setCharts();
            }
        }
        $this->link = self::$db_link;
        mysql_select_db(C("DB_NAME"), self::$db_link);
        return self::$db_link;
    }

    /**
     * 设置字符集
     */
    static private function setCharts() {
        $character = C("DB_CHARSET");
        $sql = "SET character_set_connection=$character,character_set_results=$character,character_set_client=binary";
        mysql_query($sql, self::$db_link);
    }
    
    /**
     * 发送执行SQL语句
     * @access public
     * @param string $sql SQL语句
     * @return boolean or insertId
     */
    public function exe($query){
//     	exit($query);
    	//查询参数初始化
    	$this->optInit();
    	//将SQL添加到调试DEBUG
    	$this->debug($query);
    	is_resource(self::$db_link) or $this->connect($this->table);
    	$this->lastquery = mysql_query($query, self::$db_link);
    	if(mysql_errno()){
    		throw new ExceptionStart(mysql_error());
    	}
    	if ($this->lastquery) {
    		//自增id
    		$insert_id = mysql_insert_id(self::$db_link);
    		return $insert_id ? $insert_id : true;
    	} else {
    		$this->error(mysql_errno(self::$db_link) . "\t" . $query);
    		return false;
    	}
    }
    
    /**
     * 发送查寻SQL语句
     * @access public
     * @param string $sql SQL语句
     * @return array()
     */
    public function query($query){
    	$this->optInit();
    	$name=sha1($query);
    	if(isset(self::$cache[$name])){
    		return self::$cache[$name];
    	}
    	$result=mysql_query($query,self::$db_link);
    	if(mysql_errno()){
    		if(DEBUG){
    			$this->error(mysql_error());
    		}else{
    			Log::write(mysql_error());
    		}
    	}
    	$data=$this->fetch($result);
    	mysql_free_result($result);
    	self::$cache[$name]=$data;
    	return $data;
    }
    
    // 循环结果集，获取数据
    private function fetch($result){
    	if(is_null($result)||!is_resource($result))return null;
    	$data=array();
    	if(strtolower(C('DB_DATA_TYPE'))=='array'){
    		while (!!$row=mysql_fetch_assoc($result)){
    			$data[]=$row;
    		}
    	}elseif(strtolower(C('DB_DATA_TYPE'))=='object'){
    		while (!!$row=mysql_fetch_object($result)){
    			$data[]=$row;
    		}
    	}else{
    		$this->error('数据库返回数据的类型设置错误，请检查Config配置项！');
    	}
    	return filter($data,'html_d,slashes_d');
    }
    
	/**
	 * 获取当前数据插入的主键
	 * @see DbIinterface::insertId()
	 */
    public function insertId(){
    	return mysql_insert_id(self::$db_link);
    }
    
    /**
     * 获取受影响的行数
     * @see DbIinterface::affectedRows()
     */
    public function affectedRows(){
    	return mysql_affected_rows(self::$db_link);
    }
    
    /**
     * 获取Mysql版本号
     * @see DbIinterface::version()
     */
    public function version(){
    	return mysql_get_server_info(self::$db_link);
    }
    
    /**
     * 开启事务
     * @see DbIinterface::beginTrans()
     */
    public function begin(){
    	mysql_query('START TRANSACTION',self::$db_link);
    }

    /**
     * 提交一个事务
     * @see DbIinterface::commit()
     */
    public function commit(){
    	mysql_query('COMMIT',self::$db_link);
    }
    
    /**
     * 回滚事务
     * @see DbIinterface::rollback()
     */
    public function rollback(){
    	mysql_query('ROLLBACK',self::$db_link);
    }
    
    public function close(){
//     	mysql_close(self::$db_link);
    	self::$db_link=null;
    	self::$cache=array();
    }
    
    public function __destruct(){
    	$this->close();
    }
}

?>