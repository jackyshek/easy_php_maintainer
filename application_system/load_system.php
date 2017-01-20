<?
	include "config.php";
	include "db.php";
	include "load_view.php";
	include "constant.php";
	$app_sys = new stdClass;
	$app_sys->config = new config;
	$app_sys->db = new db;
	$app_sys->load = new load;
	
if(!class_exists("App_sys"))
{
	abstract class App_sys{
			public $config;
			public $db;
			public $load;
		public function __construct(){
			$this->config = new config();
			$this->db = new db();
			$this->load = new load();
		}
	}
}