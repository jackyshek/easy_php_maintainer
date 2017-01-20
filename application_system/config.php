<?
include "enviroment_setting.php";
if(!class_exists("config"))
{
	class config{
		private $definded_config = null;
		public function __construct(){
			$config = null;
			$env = json_decode(ENVIROMENTS);
			if(in_array(ENVIROMENT,$env))
			{
				if(!file_exists(dirname(__FILE__)."/config/".ENVIROMENT.".php"))
				{
					print_r("config file name must be same as enivroment name");
					exit();
				}
				include dirname(__FILE__)."/config/".ENVIROMENT.".php";
			}
			else
			{
				print_r("please set the enviroment correctly");
				exit();
			}
			
			$this->definded_config = $config;
		}
		public function item($item_name){
			return $this->definded_config[$item_name];
		}
	}
}

