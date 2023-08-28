<?php 
include("koneksi_rian.php");
$kode_temp=$_POST['rekam']; 

if($kode_temp!=""){
	$kode_temp=explode(",",$kode_temp);
	foreach($kode_temp as $kode_id){
		$kode_in.="'".$kode_id."',";
	}
	$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}
 
	
	$sql="SELECT  SUBSTRING(ld.kd_produk,1,7) AS model,p.nama AS pabrik  FROM job_loading AS l
INNER JOIN job_loading_detail AS ld ON 
(ld.no_load=l.no_load)
LEFT JOIN job_gelaran AS g ON 
(g.no_co=l.no_co)
LEFT JOIN mst_model_fix AS m ON 
(m.kode_model=SUBSTRING(ld.kd_produk,1,7))
INNER JOIN pabrik AS p ON 
(l.pabrik_dari=p.id)
WHERE    g.no_co 
IN ($kode_in)
GROUP BY SUBSTRING(ld.kd_produk,1,7), p.nama";
	$res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>