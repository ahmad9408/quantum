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
$sql="SELECT SUBSTRING(kd_produk,1,7) as model,SUM(qty) AS qty,SUM(qty*hpp) AS hpp,SUM(qty*hpj) as hpj  FROM retur_distribusi_rian WHERE 
  SUBSTRING(kd_produk,1,7) in ($kode_in) AND 
	tanggal BETWEEN '$dari' AND '$sampai'  AND 
	(no_do NOT LIKE '%smt%' OR no_do NOT LIKE '%btl%' OR no_do NOT LIKE '%test%') group by  SUBSTRING(kd_produk,1,7) ";

	$res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>