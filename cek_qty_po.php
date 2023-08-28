<?php 
include("koneksi_rian.php");
$kode_temp=$_POST['code'];
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

$sql="SELECT SUM(pd.qty) as qty,SUBSTRING(pd.kd_produk,1,7) AS model FROM po_manufaktur_detail AS pd
INNER JOIN po_manufaktur AS p ON 
(p.no_manufaktur=pd.no_manufaktur)
WHERE SUBSTRING(kd_produk,1,7) IN ($kode_in)
AND p.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 00:00:00' GROUP BY SUBSTRING(pd.kd_produk,1,7)";

	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>