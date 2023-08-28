<?php
  session_start();
  @$username=$_SESSION["username"];
  if(empty($username)){
       die('You can\'t see this page');
  }

 require_once("config.php");
 
  @$kode_so=$_POST['k'];	
	@$jenis=$_POST['j'];

if ($jenis=='batal_so'){


  $q="SET autocommit = 0;";
  $qq=mysql_query($q);

  $q2="START TRANSACTION;";
  $qq2=mysql_query($q2);

  $sql="DELETE FROM so_finishing WHERE kode_so='$kode_so'";
  $query=mysql_query($sql);

  $sql2="DELETE FROM so_finishing_detail WHERE kode_so='$kode_so'";
  $query2=mysql_query($sql2);  

  $c="COMMIT;"; 
  $qc=mysql_query($c);

  if($query && $query2){
    echo"success";
  } else {
    echo $sql."-".$sql2;
  }
  
  
  //echo $sql; 
  
}


?>