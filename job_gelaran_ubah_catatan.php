<?php 
include("koneksi_rian.php");
$no_po=$_POST['no_po'];
$no_co=$_POST['no_co'];
$catatan=trim(str_replace(" ","",$_POST['catatan']));

$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
$query_trans=mysql_query($sql_trans);

$sql_trans="START TRANSACTION;";
$query_trans=mysql_query($sql_trans);
$sql="update job_gelaran_detail_rian set catatan='$catatan' where no_po='$no_po' and no_co='$no_co'; -- 12  ".basename(__FILE__);
$query=mysql_query($sql)or die($sql);

$sql="update job_gelaran  set no_co_mapping='$catatan' where no_co='$no_co'; -- 15  ".basename(__FILE__);
$query=mysql_query($sql)or die($sql);
 
$sql_trans="COMMIT;";	
$query_trans=mysql_query($sql_trans);
 
 
// exec("nohup php  /home/goberan/tool/take_co_c.php");  
//hitung consumtion all dari no co yang sama 
try{
 	exec("echo '' > /home/goberan/tool/test.log");
	/*exec("nohup php  /home/goberan/tool/take_co_c.php > /home/goberan/tool/test.log &"); */
	exec("nohup php  /home/goberan/tool/take_co_c.php > /home/goberan/tool/test.log  &");  
	//$output = shell_exec('php  /home/goberan/tool/take_co_c.php'); 
	 
}catch(Exception $ex){
	$catatan ='Error '.$ex->getMessage();
}
 
#$catatan="nohup php  /home/goberan/tool/take_co_c.php > /dev/null &";
echo $catatan;
 
	
?>