<?php

header ( "content-Type: text/html; charset=utf-8" );
// 设定数据库信息
$host=getenv('OPENSHIFT_MYSQL_DB_HOST');  //数据库地址
$user=getenv('OPENSHIFT_MYSQL_DB_USERNAME');// 数据库账号
$password=getenv('OPENSHIFT_MYSQL_DB_PASSWORD');// 数据库密码
$dbname=getenv('OPENSHIFT_APP_NAME');// 数据库名称
// 开始备份
mysql_connect($host,$user,$password);
mysql_select_db($dbname);
mysql_query("set names 'utf8'");
$mysql= "set charset utf8;\r\n";  
$q1=mysql_query("show tables");
while($t=mysql_fetch_array($q1)){
    $table=$t[0];
    $q2=mysql_query("show create table `$table`");
    $sql=mysql_fetch_array($q2);
    $mysql.=$sql['Create Table'].";\r\n";
    $q3=mysql_query("select * from `$table`");
    while($data=mysql_fetch_assoc($q3)){
        $keys=array_keys($data);
        $keys=array_map('addslashes',$keys);
        $keys=join('`,`',$keys);
        $keys="`".$keys."`";
        $vals=array_values($data);
        $vals=array_map('addslashes',$vals);
        $vals=join("','",$vals);
        $vals="'".$vals."'";
        $mysql.="insert into `$table`($keys) values($vals);\r\n";
    }
} 

$filename=$dbname.date('Ymd').".sql";  // 存放路径
$fp = fopen('sql/'.$filename,'w');
fputs($fp,$mysql);
fclose($fp);
// 清理旧的备份文件
$f1 = $dbname.date("Ymd", mktime(0,0,0,date("m"),date("d")-1,date("Y"))).".sql";
$f2 = $dbname.date("Ymd", mktime(0,0,0,date("m"),date("d")-2,date("Y"))).".sql";
$f3 = $dbname.date("Ymd", mktime(0,0,0,date("m"),date("d")-3,date("Y"))).".sql";

rename("sql/".$filename,'./'.$filename);
rename("sql/".$f1,'./'.$f1);
rename("sql/".$f2,'./'.$f2);
rename("sql/".$f3,'./'.$f3);
deldir(sql);
mkdir("sql");
rename('./'.$filename,"sql/".$filename);
rename('./'.$f1,"sql/".$f1);
rename('./'.$f2,"sql/".$f2);
rename('./'.$f3,"sql/".$f3);

function deldir($dir) {
  //先删除目录下的文件：
  $dh=opendir($dir);
  while ($file=readdir($dh)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }
 
  closedir($dh);
  //删除当前文件夹：
  if(rmdir($dir)) {
    return true;
  } else {
    return false;
  }
}

?>