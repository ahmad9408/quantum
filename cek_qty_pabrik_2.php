<?php 
include("koneksi_rian.php");
$kode_temp=$_POST['rekam'];
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$jenis_cek=$_POST['jenis_cek'];
$no_po=$_POST['no_po'];

if($kode_temp!=""){
	$kode_temp=explode(",",$kode_temp);
	foreach($kode_temp as $kode_id){
		$kode_in.="'".$kode_id."',";
	}
	$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}

 if($jenis_cek=='rc'){
 	$sql="SELECT jcd.kd_produk AS model,SUM(jcd.qty) AS qty  FROM job_cutting_detail AS jcd
INNER JOIN job_cutting AS jc ON 
(jc.no_co=jcd.no_co) 
inner join job_gelaran as jg on 
(jg.no_co=jc.no_co)
where  jc.realcutting='1'  and jc.no_co in 
($kode_in)  GROUP BY  jcd.kd_produk ";
 }else  if($jenis_cek=='sw'){
 	$sql="SELECT  jsd.kd_produk  AS model,sum(jsd.reject) as qty_reject FROM job_sewing AS js 
INNER JOIN job_sewing_detail AS jsd ON 
(js.no_sew=jsd.no_sew) WHERE  js.no_co IN ($kode_in)  GROUP BY  jsd.kd_produk ";
 }else  if($jenis_cek=='do'){
$sql="SELECT  SUM(IFNULL(k.jumlah_kirim,0)) AS qty,k.kd_produk AS model FROM 
(SELECT pd.kd_produk, SUM(pd.qty) AS jumlah FROM po_manufaktur_detail AS pd 
INNER JOIN po_manufaktur AS p ON (pd.no_manufaktur = p.no_manufaktur) 
WHERE p.no_manufaktur ='$no_po'
AND p.closeco IS NULL and pd.kd_produk IN ($kode_in) GROUP BY pd.kd_produk) AS a 
LEFT JOIN 

(SELECT dd.kd_produk, SUM(dd.qty) AS jumlah_kirim,d.tanggal,d.no_do FROM 
do_produk_detail AS dd INNER JOIN do_produk AS d ON (dd.no_do = d.no_do) WHERE 
d.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' AND dd.kd_produk
 in ($kode_in) AND d.no_do NOT LIKE 'BTL%' AND dd.kd_produk LIKE '%' GROUP BY dd.kd_produk) AS k 
 
 ON k.kd_produk=a.kd_produk WHERE k.jumlah_kirim>0
 GROUP BY k.kd_produk";
  
 }

		$res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>