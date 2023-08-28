<?php 
include("koneksi_rian.php");
$kode_temp=$_POST['rekam'];
$dari=$_POST['dari'];
$sampai=$_POST['sampai']; 

if($kode_temp!=""){
	$kode_temp=explode(",",$kode_temp);
	foreach($kode_temp as $kode_id){
		$kode_in.="'".$kode_id."',";
	}
	$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}
 
 
	
	$sql="SELECT pm.no_manufaktur as no_po FROM po_manufaktur AS pm 
INNER JOIN po_manufaktur_detail AS pmd
ON (pm.no_manufaktur=pmd.no_manufaktur)
WHERE pm.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'
AND SUBSTRING(pmd.kd_produk,1,7) IN ($kode_in) and (pm.closeco IS NULL or pm.closeco='0' or pm.closeco='') group by pm.no_manufaktur";
 
	$res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>