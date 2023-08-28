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
/*
$sql="SELECT SUM(pd.qty) as qty,SUBSTRING(pd.kd_produk,1,7) AS model FROM po_manufaktur_detail AS pd
INNER JOIN po_manufaktur AS p ON 
(p.no_manufaktur=pd.no_manufaktur)
WHERE SUBSTRING(kd_produk,1,7) IN ($kode_in)
AND p.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 00:00:00' GROUP BY SUBSTRING(pd.kd_produk,1,7)";
*/
$sql="SELECT SUBSTRING(dpd.kd_produk,1,7) AS model,SUM(dpd.qty) as qty FROM do_produk AS dp INNER JOIN
do_produk_detail AS dpd ON 
(dpd.no_do=dp.no_do) 
INNER JOIN produk AS p ON 
(p.kode=dpd.kd_produk)
 WHERE  SUBSTRING(dpd.kd_produk,1,7) IN ($kode_in) and 
(dp.no_do NOT LIKE '%mst%' AND dp.no_do NOT LIKE '%btl%' AND dp.no_do NOT LIKE '%test%') 
AND dp.tanggal BETWEEN  '$dari 00:00:00' AND '$sampai 23:59:59' AND dp.keterangan!='P100S' 
AND (dpd.kd_produk LIKE '%' OR p.nama LIKE '%%')
GROUP BY SUBSTRING(dpd.kd_produk,1,7)";

	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>