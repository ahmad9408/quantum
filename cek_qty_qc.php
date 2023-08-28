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
 
$sql="SELECT SUBSTRING(kd_produk,1,7) AS model,SUM(grade_a) AS qty,l.pabrik_dari AS pabrik,SUBSTRING(updatedate,1,10) AS tanggal ,SUM(grade_b) AS grade_b
FROM job_qc AS q
INNER JOIN job_qc_detail AS qd ON 
(q.no_qc=qd.no_qc)
LEFT JOIN job_gelaran AS g ON 
(g.no_co=q.no_co)
LEFT JOIN job_loading AS l ON 
(l.no_co=q.no_co)
WHERE SUBSTRING(updatedate,1,10) BETWEEN '$dari 00:00:00' AND '$sampai 00:00:00' AND (l.pindah_pabrik='0'
   OR l.pindah_pabrik IS NULL)  AND l.pabrik_dari!='P1000'    AND SUBSTRING(kd_produk,1,7) IN ($kode_in) 
GROUP BY SUBSTRING(kd_produk,1,7),SUBSTRING(updatedate,1,10),l.pabrik_dari";

$sql="SELECT
   REPLACE(`j`.`no_co`,'/','') AS co 
    , sum(`jd`.`grade_a`) as a 
    , sum(`jd`.`grade_b`) as b 
FROM 
    `job_qc_detail` AS `jd` 
    INNER JOIN  `job_qc` AS `j`  
        ON (`jd`.`no_qc` = `j`.`no_qc`) 
WHERE  `j`.`no_co` IN ($kode_in) group by j.no_co";


	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	 
	
?>