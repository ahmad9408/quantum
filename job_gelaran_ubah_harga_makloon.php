<?php 
include("koneksi_rian.php");
$no_po=$_POST['no_po'];
$catatan=$_POST['catatan'];
$bahan=$_POST['bahan'];

$sql_trans="SET autocommit = 0;";
$query_trans=mysql_query($sql_trans);

$sql_trans="START TRANSACTION;";
$query_trans=mysql_query($sql_trans);


$sql="update job_gelaran set harga_makloon='$bahan' where no_co_mapping='$catatan'";
$query=mysql_query($sql)or die($sql);

$sql_trans="COMMIT;";	
$query_trans=mysql_query($sql_trans);
echo $bahan;
	
?>