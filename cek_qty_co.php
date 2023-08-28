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

$sql="SELECT SUM(gd.qty_produk) AS qty, SUBSTRING(kd_produk,1,7) AS model FROM job_gelaran AS g 
INNER JOIN job_gelaran_detail AS gd ON 
(g.no_co=gd.no_co) WHERE  SUBSTRING(kd_produk,1,7) in ($kode_in) and     g.updatedate BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'
GROUP BY SUBSTRING(kd_produk,1,7)";

	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>