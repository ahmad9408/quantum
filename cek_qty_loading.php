<?php 
include("koneksi_rian.php");
$co_rep=$_POST['co_rep'];
 
$kode_temp=$_POST['code'];
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
if($co_rep!=""){
	$kode_temp=explode(",",$co_rep);
	$banyak=count($kode_temp);
	for($i=0;$i<$banyak-1;$i++){
		if($i==($banyak-2)){
			$kode_in.="'".$kode_temp[$i]."'";
		}else{
			$kode_in.="'".$kode_temp[$i]."',";
		}
	}
	  
	//$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}
 
	
	 $sql="SELECT SUBSTRING(kd_produk,1,7) AS model,SUM(qty_produk) AS qty,l.pabrik_dari as pabrik,SUBSTRING(updatedate,1,10) as tanggal FROM job_loading AS l
INNER JOIN job_loading_detail AS ld ON 
(ld.no_load=l.no_load)
left join job_gelaran as g on 
(g.no_co=l.no_co)
WHERE SUBSTRING(updatedate,1,10) BETWEEN '$dari 00:00:00' AND '$sampai 00:00:00' and (l.pindah_pabrik='0'
   OR l.pindah_pabrik IS NULL)  AND l.pabrik_dari!='P1000' and SUBSTRING(kd_produk,1,7) in ($kode_in) 
GROUP BY SUBSTRING(kd_produk,1,7),SUBSTRING(updatedate,1,10),l.pabrik_dari";

$sql="SELECT
     REPLACE(`j`.`no_co`,'/','') AS co 
    , SUM(`jl`.`qty_produk`) as qty
FROM
    `job_loading_detail` AS `jl`
    INNER JOIN `quantum`.`job_loading` AS `j` 
        ON (`jl`.`no_load` = `j`.`no_load`) 
	WHERE  `j`.`no_co` IN ($kode_in) and (j.pindah_pabrik='0'
   OR j.pindah_pabrik IS NULL ) and j.approve2='1' group by j.no_co";	
		 


	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>