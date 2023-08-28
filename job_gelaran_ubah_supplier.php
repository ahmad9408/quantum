<?php 
include("koneksi_rian.php");
$warna=$_POST['warna'];
$supplier=$_POST['supplier'];
$no_po=$_POST['no_po'];
$no_co=$_POST['no_co'];
$jenis=$_POST['jenis'];
$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
$query_trans=mysql_query($sql_trans);

$sql_trans="START TRANSACTION;";
$query_trans=mysql_query($sql_trans);
if($jenis=='bahan'){
$sql="update job_gelaran_detail_rian_detail set s_bahan='$supplier' where no_co='$no_co' and no_po='$no_po' and kode_warna='$warna'";}else{
$sql="update job_gelaran_detail_rian_detail set s_variasi='$supplier' where no_co='$no_co' and no_po='$no_po' and kode_warna='$warna'";
}
$query=mysql_query($sql)or die($sql);

$sql_trans="COMMIT;";	
$query_trans=mysql_query($sql_trans);
echo $supplier;
	
?>