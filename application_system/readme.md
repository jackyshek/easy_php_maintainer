You may read the file in http://dillinger.io/
# Little Framework 

This framework is for maintaining the website easily.

### Version 1.1

Framework include
  - config
  - sso config
  - sso manger
  - load view
  - database like CI format (query, result, row, tran_start, tran_complete) but not for multiple queries
      - database limitation
    ```php
           $query1 = $app_sys->db->query("select * from table1");
           $query2 = $app_sys->db->query("select * from table2");
           $result1 = $query1->reuslt(); // it will get query2 result
    ```
  - sso protector
  - enviroment control

### Installation
 ```php
 include "/home/usr/application_system/load_system.php";
 ```
### Enviroment Control
The Evniroment Control file is  `enviroment_setting.php` which control the `application_system/sso_config`, `application_system/config`  and `/home/[usr]/db_inc/`.
```php
define("ENVIROMENT", "uat"); //current enviroment
define("ENVIROMENTS", json_encode(array("uat","prod"))); //existing enviroments
```
In `application_system/sso_config`, all files name must be same as the `ENVIROMENTS` name

##### Example

Load the `uat` `ENVIROMENT`, the php file must be  `uat.php` in `application_system/sso_config`, `application_system/config` and `/home/[usr]/db_inc/`.

### Config
In `application_system/config` files (e.g. `uat.php` or `prod.php`)
##### Adding variables
```php
$config["base_url"] = "https://[domain]/[project_name]";
$config["testing_config_item"] = "Testing config item";
```
For loading config items:
##### Loading variables
```php
$app_sys->config->item("base_url");
$app_sys->config->item("testing_config_item");
```

### Database
#### Setting
Go to `application_system/db.php` to set `include` file path
```php
$db_inc_path = "/home/[usr]/db_inc/";
```
Also the `db_inc` file name must be same as the `ENVIROMENT` name.
##### Example

Load the `uat` `ENVIROMENT`, the php file must be  `uat.php` in `/home/[usr]/db_inc/`.

#### Load Query
```php
$app_sys->db->query("select * from table");
```
#### Insert Query
```php
$values = array(
                "col_name1"=>"value1",
                "col_name2"=>123456
                );
$app_sys->db->insert("table_name",$values);
```
#### Insert Batch Query
```php
$values = array(
                array(
                    "col_name1"=>"value1",
                    "col_name2"=>123456
                )
            ,    array(
                    "col_name1"=>"valueA",
                    "col_name2"=>123456789
                )
            );
$array_chunk_number = 4000; // default 6000 -> reason: the mysql setting of inserting is different
$app_sys->db->insert_batch("table_name",$values,$array_chunk_number);
```
#### Get Result
```php
$query->result();
```
#### Get Row
```php
$query->row();
```
##### Example
```php
$query = $app_sys->db->query("select * from table");
$result = $query->result();
$row = $query->row();
```

##### Single Query for 2 different results
```php
$query = $app_sys->db->query("select * from table");
$result = $query->result();
$row = $query->row();

$query = $app_sys->db->query("select * from table2");
$result = $query->result();
$row = $query->row();
```

##### Limitation
```php
//Cannot get the sql result
$query1 = $app_sys->db->query("select * from table1");
$query2 = $app_sys->db->query("select * from table2");
$result1 = count($query1->result()); // it will get query2 result
$result2 = count($query2->result()); // it will get query2 result

```
### Load View
##### Show
```php
$data["test"] = 'Show testing'
$app_sys->load->view('fullpath',$data);
```
##### Load In Parameter
```php
$data["test"] = 'Show testing'
$value = $app_sys->load->view('fullpath',$data,true);
```
##### View Control
```php
Meow: <?=$test?>
```
### Class inherit
```php
include '/home/path/application_system/load_system.php'
class Aclass extends App_sys{
    public function index(){
        echo $this->config->item("item_name");
    }
}
```
### Change Log
```
Version 1.0
2016-01-03 Create new framework version 1.0 
Version 1.1
2016-12-30 Create load view
2016-01-09 Support class inherited
2017-01-09 Add function db->insert();
2017-01-09 Add function db->insert_batch();
```
