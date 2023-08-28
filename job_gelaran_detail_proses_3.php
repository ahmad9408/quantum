<?php 
include("connect.php"); 
$proses=$_POST['proses'];
$catatan=$_POST['catatan'];
$ck=$_POST['ck'];
$cv=$_POST['cv']; 
if($proses=="simpan_supplier_utama"){
	$kode_warna=$_POST['kode_warna'];
	$no_co=$_POST['no_co'];
	$jml=$_POST['jml_row_utama_'.$kode_warna];
	
	$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
	$query_trans=mysql_query($sql_trans);
	
	$sql_trans="START TRANSACTION;";
	$query_trans=mysql_query($sql_trans);
	for($i=1;$i<=$jml;$i++){
		$barcode=$_POST['utama_bar_'.$kode_warna."_".$i];
		$qty=$_POST['utama_qty_'.$kode_warna."_".$i];
		$roll=$_POST['utama_roll_'.$kode_warna."_".$i];		
		$sql="REPLACE into  `job_ppic_detail_kain` (`no_co`,   `seq`,  `id_barang`,`qty`,`roll`,`kode_warna`,no_co_c) values ('$no_co',    '$i',  '$barcode',  '$qty',  '$roll','$kode_warna','$catatan') ; -- 21  ".basename(__FILE__);
		$query=mysql_query($sql)or die($sql); 
	}
	
	$sql="SELECT SUM(jg.qty_produk)*$ck FROM job_gelaran_detail AS jg where no_co='$no_co'";
	$query=mysql_query($sql)or die($sql);
	list($plan_com)=mysql_fetch_array($query);
	
	$sql="SELECT SUM(jk.qty) FROM job_ppic_detail_kain AS jk WHERE jk.no_co_c='$catatan'";
	$query=mysql_query($sql)or die($sql);
	list($qty)=mysql_fetch_array($query);
	
	
	$sql="insert ignore job_gelaran_rekap_c(no_co_c)value('$catatan')";
	$query=mysql_query($sql)or die($sql);
	
	$sql="update job_gelaran_rekap_c set real_consumtion='$qty',produk_consumtion='$plan_com' where  no_co_c='$catatan'; -- 37  ".basename(__FILE__);
	$query=mysql_query($sql)or die($sql);
	
	$sql_trans="COMMIT;";	
	$query_trans=mysql_query($sql_trans);
	
	echo"berhasil";
}else 
if($proses=="simpan_supplier_variasi"){
	$kode_warna=$_POST['kode_warna'];
	$no_co=$_POST['no_co'];
	$jml=$_POST['jml_row_variasi_'.$kode_warna];
	
	$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
	$query_trans=mysql_query($sql_trans);
	
	$sql_trans="START TRANSACTION;";
	$query_trans=mysql_query($sql_trans);
	for($i=1;$i<=$jml;$i++){
		$barcode=$_POST['variasi_bar_'.$kode_warna."_".$i];
		$qty=$_POST['variasi_qty_'.$kode_warna."_".$i];
		$roll=$_POST['variasi_roll_'.$kode_warna."_".$i];		
		$sql="REPLACE into  `job_ppic_detail_variasi` (`no_co`,   `seq`,  `id_barang`,`qty`,`roll`,`kode_warna`,no_co_c) values ('$no_co',    '$i',  '$barcode',  '$qty',  '$roll','$kode_warna','$catatan')";
		 
		$query=mysql_query($sql)or die($sql); 
	}
	
	$sql="SELECT SUM(jk.qty) FROM job_ppic_detail_variasi AS jk WHERE jk.no_co_c='$catatan'";
	$query=mysql_query($sql)or die($sql);
	list($qty)=mysql_fetch_array($query);
	
	$sql="update job_gelaran_rekap_c set real_variation='$qty' where no_co_c='$catatan' ; -- 68  ".basename(__FILE__);
	$query=mysql_query($sql)or die($sql);
	
	$sql_trans="COMMIT;";	
	$query_trans=mysql_query($sql_trans);
	echo"berhasil";
	
}else 
if($proses=="ubah_gudang"){
 
	$gudang=$_POST['gudang'];
	$no_co=$_POST['no_co']; 
	$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
	$query_trans=mysql_query($sql_trans);
	
	$sql_trans="START TRANSACTION;";
	$query_trans=mysql_query($sql_trans);
	
	$sql="update job_gelaran set tujuan_pabrik='$gudang' where no_co='$no_co' ; -- 86  ".basename(__FILE__);
	 
	$query=mysql_query($sql)or die($sql);
	
	$sql_trans="COMMIT;";	
	$query_trans=mysql_query($sql_trans);
	
	echo "berhasil";
}else{
	$id_barang=$_POST['id_barang'];
	$qty=$_POST['qty'];
	$no_co=$_POST['no_co'];
	
	$sql_trans="SET autocommit = 0;"; //tambhan 21072022 13:37
	$query_trans=mysql_query($sql_trans);
	
	$sql_trans="START TRANSACTION;";
	$query_trans=mysql_query($sql_trans);
	
	$sql="UPDATE job_gelaran_detail SET qty_produk='$qty' WHERE no_co='$no_co' AND kd_produk='$id_barang'; -- 105  ".basename(__FILE__);
	 
	$query=mysql_query($sql)or die($sql);
	
	$sql_trans="COMMIT;";	
	$query_trans=mysql_query($sql_trans);
	echo"berhasil";
}
?>