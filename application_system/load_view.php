<?
if(!class_exists("load"))
{
	Class load{
		public function view($path='',$data=array(),$value_return=false){
			if($value_return)
			{
				ob_start();
				extract($data);
				require($path);
				return ob_get_clean();
			}
			else
			{
				extract($data);	
				include($path);
			}
			
		}
	}
}