<?php 
include("koneksi_rian.php");
$proses=$_POST['proses'];
if($proses=="cancel"){
	$no_co=$_POST['no_co'];
	$warna=$_POST['kode_warna'];
	$sql="delete from job_ppic_detail_kain where no_co='$no_co' and kode_warna='$warna'";
	$query=mysql_query($sql)or die($sql);
	echo"berhasil";die;
	
}else{
	$no_po=$_POST['no_po'];
	$no_co=$_POST['no_co'];
	$warna=$_POST['warna'];
	$isi_value=$_POST['isi_value'];
	$variasi=$_POST['variasi'];
	
	$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
	$query_trans=mysql_query($sql_trans);
	
	$sql_trans="START TRANSACTION;";
	$query_trans=mysql_query($sql_trans);
	
	if($variasi!='variasi'){
	$sql="update job_gelaran_detail_rian_detail set kode_produksi_bahan='$isi_value' where no_po='$no_po' and no_co='$no_co' and kode_warna='$warna'";
	}else{
	$sql="update job_gelaran_detail_rian_detail set kode_produksi_variasi='$isi_value' where no_po='$no_po' and no_co='$no_co' and kode_warna='$warna'"; }
	$query=mysql_query($sql)or die($sql);
	
	$sql_trans="COMMIT;";	
	$query_trans=mysql_query($sql_trans);
	echo $isi_value;
 

}	
?>