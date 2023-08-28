<?php 
include("koneksi_rian.php");
$no_po=$_POST['no_po'];
$no_co=$_POST['no_co'];
$catatan=$_POST['catatan_kaki'];

$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
$query_trans=mysql_query($sql_trans);

$sql_trans="START TRANSACTION;";
$query_trans=mysql_query($sql_trans);
$sql="update job_gelaran_detail_rian set catatan_kaki='$catatan' where no_po='$no_po' and no_co='$no_co'";
$query=mysql_query($sql)or die($sql);
$catatan=str_replace(".","<br>",$catatan);

$sql_trans="COMMIT;";	
$query_trans=mysql_query($sql_trans);
echo $catatan;
	
?>