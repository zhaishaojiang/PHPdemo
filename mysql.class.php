<?php
class db{
	public $con;	//保存数据库连接信息
	public static $sql;	//保存数据库查询语句
	public static $instance;	//保存类的唯一实例

	/**
	 * 构造函数，创建连接数据库的实例
	 */
	private function __construct(){
		include('db.config.php');
		$this->con = new mysqli($db['host'], $db['user'], $db['password']);
		if(!$this->con->select_db($db['database'])){
			echo "选择数据库失败!";
		}
		$this->con->query("set names utf8");
	}

	/**
	 * 返回唯一实例的一个引用
	 */
	public static function getInstance(){
		if(!(self::$instance instanceof self)){
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * 查询操作
	 */
	public function select($table, $field = array(), $condition = array()){
		$fieldstr = "";
		if(!empty($field)){
			foreach ($field as $key => $value) {
				$fieldstr = $fieldstr."$value".",";
			}
			$fieldstr = rtrim($fieldstr, ",");
		} else{
			$fieldstr = "*";
		}

		$where = "";
		if(!empty($condition)){
			foreach ($condition as $key => $value) {
				if(strpos($value, '<') !==false || strpos($value, '>') !== false){
					$where = $where.$key."$value"." and ";
				} else{
					$where = $where.$key."='".$value."' and ";
				}
			}
		}
		$where='where '.$where .'1=1';	//当查询条件为空时，默认为可以查询所有信息

		self::$sql = "select {$fieldstr} from {$table} {$where}";
		$result = $this->con->query(self::$sql);
		$resultArr = array();
		$i = 0;
		while($row = $result->fetch_assoc()){  
            foreach($row as $k => $v){  
                $resultArr[$i][$k] = $v;  
            }  
            $i++;
        }
		return $resultArr;
	}

	/**
	 * 增加数据
	 */
	public function insert($table, $values = array()){
		$data = '';  
        $datas = ''; 
        if(empty($values)){
        	return false;
        }
        foreach($values as $key => $value){  
            $data .= $key.',';  
            $datas .= "'$value'".',';  
        }  
        $data = rtrim($data, ',');
        $datas   = rtrim($datas, ',');
        self::$sql = "insert into  {$table} ({$data}) values ({$datas})";  
        if($this->con->query(self::$sql)){
            return $this->con->insert_id;
        }else{
            return false;
        }
	}

	/**
	 * 删除数据
	 */
	public function delete($table, $condition = array()){
		$where='';  
        if(!empty($condition)){  
            foreach ($condition as $key => $value) {
				if(strpos($value, '<') !== false || strpos($value, '>') !== false){
					$where = $where.$key."$value"." and ";
				} else{
					$where = $where.$key."='".$value."' and ";
				}
			}
			$where='where '.$where.'1=1';
        } else{
        	$where='where '.$where.'1>1';	//当删除条件为空时，防止将表中数据全部删除
        }
        self::$sql = "delete from {$table} {$where}"; 
        $this->con->query(self::$sql); 
        return $this->con->affected_rows;
	}

	/**
	 * 修改数据
	 */
	public function update($table, $condition = array(), $data = array()){
		$where='';  
        if(!empty($condition)){
            foreach ($condition as $key => $value) {
				if(strpos($value, '<') !==false || strpos($value, '>') !== false){
					$where = $where.$key."$value"." and ";
				} else{
					$where = $where.$key."='".$value."' and ";
				}
			}
            $where = 'where '.$where.'1=1';  
        } else{
        	$where = 'where '.$where.'1>1';
        }
        $updatastr = '';  
        if(!empty($data)){  
            foreach($data as $key => $value){  
                $updatastr.= $key."='".$value."',";  
            }  
            $updatastr = 'set '.rtrim($updatastr,',');  
        }
        self::$sql = "update {$table} {$updatastr} {$where}";
        $this->con->query(self::$sql);  
        return $this->con->affected_rows;
	}
}

$db = db::getInstance();
//$result = $db->select('test_table', array('id', 'name'));
//$result = $db->insert('test_table', array('name' => 'hello'));
//$result = $db->delete('test_table', array('id' => '>24'));
$result = $db->update('test_table', array('id' => 24), array('name' => 'hjsdafjhsdj'));
print_r($result);
print_r($db::$sql);