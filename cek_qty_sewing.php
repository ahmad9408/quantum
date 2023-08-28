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
 
 
$sql="SELECT
   REPLACE(`j`.`no_co`,'/','') AS co 
    , sum(`jd`.`grade_a`) as a 
    , sum(`jd`.`grade_b`) as b 
FROM 
    `job_qc_detail` AS `jd` 
    INNER JOIN  `job_qc` AS `j`  
        ON (`jd`.`no_qc` = `j`.`no_qc`) 
WHERE  `j`.`no_co` IN ($kode_in) group by j.no_co";

$sql="SELECT
    REPLACE(`s`.`no_co`,'/','') AS co 
    , SUM(`sd`.`reject`) as reject
    , SUM(`sd`.`pending`) as pending
    , SUM(`sd`.`qty`-`sd`.`pending`-`sd`.`reject`) as bagus
FROM
   `job_sewing_detail` AS `sd`
    INNER JOIN `quantum`.`job_sewing` AS `s` 
        ON (`sd`.`no_sew` = `s`.`no_sew`) WHERE s.no_co IN ($kode_in) GROUP BY `s`.`no_co`";


	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	 
	
?>