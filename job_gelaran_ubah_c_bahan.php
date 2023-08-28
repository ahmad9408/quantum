<?php 
include("koneksi_rian.php");
$no_po=$_POST['no_po'];
$no_co=$_POST['no_co'];
$bahan=$_POST['bahan'];

$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
$query_trans=mysql_query($sql_trans);

$sql_trans="START TRANSACTION;";
$query_trans=mysql_query($sql_trans);


$sql="update job_gelaran_detail_rian set c_produk='$bahan' where no_po='$no_po' and no_co='$no_co'";
$query=mysql_query($sql)or die($sql);

$sql_trans="COMMIT;";	
$query_trans=mysql_query($sql_trans);
echo $bahan;
	
?>