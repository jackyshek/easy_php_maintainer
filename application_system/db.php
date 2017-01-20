<?

include "enviroment_setting.php";
		$db_inc_path = "/path/to/db/config/folder";
		$env = json_decode(ENVIROMENTS);
		if(in_array(ENVIROMENT,$env))
		{
			if(!file_exists($db_inc_path.ENVIROMENT.".php"))
			{
				print_r("db_inc file name must be same as enivroment name");
				exit();
			}
			include $db_inc_path.ENVIROMENT.".php";
		}
		else
		{
			print_r("please set the enviroment correctly");
			exit();
		}
if(!class_exists("db"))
{
	Class db{
		private $db_stmt = null;
		private $db_conn = null;
		public $has_trans = null;
		private $db_result = null;
		private $mysqli_not_support_preparestmt_in_transaction = false;
		public function __construct()
		{
			$trans_no_prepared_version = array("5.5","5.2","5.1");
			if($db_conn == null)
			{
				$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
				if ($conn->connect_error) {
					die("Connection failed: " . $conn->connect_error);
				} 
				$conn->query("SET NAMES 'utf8'");
				$this->db_conn = $conn;
				$version_str = str_replace("-logrollback","",mysqli_get_server_info($this->db_conn));
				$version_str = explode(".",$version_str);
				unset($version_str[count($version_str)-1]);
				$version_str = implode(".",$version_str);

			}
			
		}
		private function close_db()
		{
			if($this->db_stmt!=null)
			{
				//print_r("close statment");
				$this->db_stmt->close();
			}
			if($this->has_trans == null)
			{
				//print_r("close db connection");
				$this->db_conn->close();
			}
			
		}
		
		public function tran_start()
		{
			//print_r("start_transaction\n\n");
			$this->db_conn->query("SET autocommit = 0; START TRANSACTION;");
			$this->has_trans = true;
		}
		public function tran_complete()
		{
			//print_r($this->db_conn->error);
			//$this->db_conn->query("ROLLBACK; SET autocommit = 1;");
			
			if($this->db_conn->error != '' && $this->db_conn->error != null)
			{
				//print_r("rollback\n\n");
				$this->db_conn->query("ROLLBACK; SET autocommit = 1;");
			}
			else
			{
				//print_r("commit\n\n");
				$this->db_conn->query("COMMIT;SET autocommit = 1;");
				
			}
			
			$this->has_trans = null;
		}
		
		public function query($sql,$array=array())
		{
			
			try
			{
					
					$db_conn = $this->db_conn;
					$stmt = $db_conn->stmt_init();
					if($this->db_stmt != null)
					{
						$this->db_stmt->free_result();
						$this->db_stmt->close();
					}
					if(!$stmt->prepare($sql))
					{
						throw new Exception($stmt->error);
					}
					$value_type=array();
					
					foreach($array as $ele)
					{
						if(is_string($ele))
						{
							$value_type[] = 's';
						}
						else if(is_int($ele))
						{
							$value_type[] = 'i';
						}
					}
					$value_types = implode('',$value_type);
					if(count($array)>0)
					{
						array_unshift($array,$value_types);
						if (!call_user_func_array(array($stmt,'bind_param'),$this->value_reference($array)))
						{
							throw new Exception($stmt->error);
						}
					}
					
					if (!$stmt->execute()) {
						throw new Exception($stmt->error);
					}
					
					$this->db_stmt = $stmt;
				
				
				
				
				return $this;
				//return $this->db_stmt;
			}
			catch(Exception $e)
			{
				 echo $e->getMessage()."\n";
				  if($this->has_trans != null)
				 {
					//print_r("rollback start query_part \n\n");
					$this->db_conn->query("ROLLBACK; SET autocommit = 1;");
					$this->has_trans = null;
				 }
				 exit();
			}
			
		}
		
		public function insert($table_name,$array=array())
		{
			
			try
			{
					$columns = array_keys($array);
					$binding_elem = array_map(function($col){return'?';}, $columns);
					$sql = 'insert into '.$table_name.' ('.implode(",",$columns).') VALUES('.implode(',',$binding_elem).');';
					$db_conn = $this->db_conn;
					$stmt = $db_conn->stmt_init();
					if($this->db_stmt != null)
					{
						$this->db_stmt->free_result();
						$this->db_stmt->close();
					}
					if(!$stmt->prepare($sql))
					{
						throw new Exception($stmt->error);
					}
					$value_type=array();
					
					foreach($array as $ele)
					{
						if(is_string($ele))
						{
							$value_type[] = 's';
						}
						else if(is_int($ele))
						{
							$value_type[] = 'i';
						}
					}
					$value_types = implode('',$value_type);
					if(count($array)>0)
					{
						array_unshift($array,$value_types);
						if (!call_user_func_array(array($stmt,'bind_param'),$this->value_reference($array)))
						{
							throw new Exception($stmt->error);
						}
					}
					
					if (!$stmt->execute()) {
						throw new Exception($stmt->error);
					}
					
					$this->db_stmt = $stmt;
				
				
				
				
				return $this;
				//return $this->db_stmt;
			}
			catch(Exception $e)
			{
				 echo $e->getMessage()."\n";
				  if($this->has_trans != null)
				 {
					//print_r("rollback start query_part \n\n");
					$this->db_conn->query("ROLLBACK; SET autocommit = 1;");
					$this->has_trans = null;
				 }
				 exit();
			}
			
		}
		
		
		public function insert_batch($table_name,$array_list=array(),$chunk_size = 6000)
		{
			
			try
			{
				$db_conn = $this->db_conn;
				$chunked_array = array_chunk($array_list,$chunk_size);
				
				foreach($chunked_array as $chunked_array_list)
				{
					$value_types = '';
					$values_array = array();
					$columns = "";
					$binding_elems_sql = array();
					

					$columns = array_keys($chunked_array_list[0]);
					$binding_elem = array_map(function($col){return'?';}, $columns);
					foreach($chunked_array_list as $array)
					{
						$binding_elems_sql[] = '('.implode(',',$binding_elem).')';
						$value_type=array();
						$values = array();
						foreach($array as $ele)
						{
							if(is_string($ele))
							{
								$value_type[] = 's';
							}
							else if(is_int($ele))
							{
								$value_type[] = 'i';
							}
							$values_array[] = $ele;
						}
						$value_types .= implode('',$value_type);
					}
					

					
					$sql = 'insert into '.$table_name.' ('.implode(",",$columns).') VALUES '.implode(',',$binding_elems_sql).';';
					
					$stmt = $db_conn->stmt_init();
					if($this->db_stmt != null)
					{
						$this->db_stmt->free_result();
						$this->db_stmt->close();
					}
					if(!$stmt->prepare($sql))
					{
						throw new Exception($stmt->error);
					}

					array_unshift($values_array,$value_types);
					if (!call_user_func_array(array($stmt,'bind_param'),$this->value_reference($values_array)))
					{
						throw new Exception($stmt->error);
					}
					if (!$stmt->execute()) {
						throw new Exception($stmt->error);
					}
					
					$this->db_stmt = $stmt;
				}
				
				return $this;
				//return $this->db_stmt;
			}
			catch(Exception $e)
			{
				 echo $e->getMessage()."\n";
				  if($this->has_trans != null)
				 {
					//print_r("rollback start query_part \n\n");
					$this->db_conn->query("ROLLBACK; SET autocommit = 1;");
					$this->has_trans = null;
				 }
				 exit();
			}
			
		}
		private function value_reference($arr)
		{
			$refs = array();
			foreach($arr as $key => $value)
				$refs[$key] = &$arr[$key];
			return $refs;

		}
		public function result()
		{
			try
			{
				$results = array();
				
					
					$metadata = $this->db_stmt->result_metadata();
					 while ($field = $metadata->fetch_field()) { 
						$var = $field->name; 
						$$var = null; 
						$fields[$var] = &$$var;
					}
					
					if(!call_user_func_array(array($this->db_stmt,'bind_result'),$this->value_reference($fields)))
					{
						throw new Exception($this->stmt->error);
					}
					
					$this->db_result = call_user_func_array(array($this->db_stmt,'bind_result'),$this->value_reference($fields));
					$i=0;
					$this->db_stmt->store_result();
					$this->db_stmt->data_seek(0);
					
					
					while($this->db_stmt->fetch()) {
						$results[$i] = new stdClass;
						foreach($fields as $k => $v)
						{
							 $results[$i]->$k = $v;
						}
						$i++;
					}
				
				
				
				return $results;

			}
			catch(Exception $e)
			{
				 echo $e->getMessage()."\n";
				 if($this->has_trans != null)
				 {
					  //print_r("rollback start result_part \n\n");
					 $this->db_conn->query("ROLLBACK; SET autocommit = 1;");
					 $this->has_trans = null;
				 }
				 exit();
			}
			
		}
		public function row($specific_row = 0){
			try
			{
				
				$results = array();
				
					$metadata = $this->db_stmt->result_metadata();
					
					 while ($field = $metadata->fetch_field()) { 
						$var = $field->name; 
						$$var = null; 
						$fields[$var] = &$$var;
					}
					
					if(!call_user_func_array(array($this->db_stmt,'bind_result'),$this->value_reference($fields)))
					{
						throw new Exception($this->stmt->error);
					}
					$i=0;
					
					$this->db_stmt->store_result();
					$this->db_stmt->data_seek(0);
					
					while($this->db_stmt->fetch()) {
						$results[$i] = new stdClass;
						foreach($fields as $k => $v)
						{
							 $results[$i]->$k = $v;
						}
						$i++;
					}
				if(count($results)>0)
				{
					if(isset($results[$specific_row]))
					{
						return $results[$specific_row];
					}
					else
					{
						return null;
					}
					
				}
				else
				{
					return $results;
				}

			}
			catch(Exception $e)
			{
				 echo $e->getMessage()."\n";
				 if($this->has_trans != null)
				 {
					  //print_r("rollback start result_part \n\n");
					 $this->db_conn->query("ROLLBACK; SET autocommit = 1;");
					 $this->has_trans = null;
				 }
				 exit();
			}
		}
		
		public function __destruct()
		{
		   $this->close_db();
		}
	}
}
?>