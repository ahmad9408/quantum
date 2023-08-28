<?php 
include("koneksi_rian.php");
$no_po=$_POST['no_po'];
$no_co=$_POST['no_co'];
$pabrik=$_POST['pabrik'];

$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
$query_trans=mysql_query($sql_trans);

$sql_trans="START TRANSACTION;";
$query_trans=mysql_query($sql_trans);

$sql="update job_gelaran_detail_rian set pabrik_sewing='$pabrik' where no_po='$no_po' and no_co='$no_co'";
$query=mysql_query($sql)or die($sql);

$sql_trans="COMMIT;";	
$query_trans=mysql_query($sql_trans);

$sql="select nama from pabrik where id='$pabrik' and status='1'";
$query=mysql_query($sql)or die($sql);
list($pabrik)=mysql_fetch_array($query);
echo $pabrik;
	
?>