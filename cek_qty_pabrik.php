<?php 
include("koneksi_rian.php");
$kode_temp=$_POST['rekam'];
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$jenis_cek=$_POST['jenis_cek'];

if($kode_temp!=""){
	$kode_temp=explode(",",$kode_temp);
	foreach($kode_temp as $kode_id){
		$kode_in.="'".$kode_id."',";
	}
	$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}

 if($jenis_cek=='co'){
		$sql="SELECT SUBSTRING(jgd.kd_produk,1,7) AS model,SUM(jgd.qty_produk) as qty  FROM job_gelaran_detail AS jgd
	INNER JOIN job_gelaran AS jg ON 
	(jg.no_co=jgd.no_co) where  jg.no_po in ($kode_in)  GROUP BY SUBSTRING(jgd.kd_produk,1,7) ";
 }else  if($jenis_cek=='rc'){
 	$sql="SELECT SUBSTRING(jcd.kd_produk,1,7) AS model,SUM(jcd.qty) AS qty,sum(jcd.qty*p.hargajual) as hpj,sum(jcd.qty*p.hargadasar) as hpp  FROM job_cutting_detail AS jcd
INNER JOIN job_cutting AS jc ON 
(jc.no_co=jcd.no_co) 
inner join job_gelaran as jg on 
(jg.no_co=jc.no_co)
inner join produk as p on 
(p.kode=jcd.kd_produk)
where  jc.realcutting='1'  and jc.no_co in 
($kode_in)  GROUP BY SUBSTRING(jcd.kd_produk,1,7)";
 }else  if($jenis_cek=='sw'){
 	$sql="SELECT SUBSTRING(jsd.kd_produk,1,7) AS model,SUM(jsd.qty-jsd.pending) AS qty,sum(jsd.reject) as qty_reject,
	SUM(jsd.qty-jsd.pending-jsd.reject) AS bagus,
	sum(jsd.reject*p.hargajual) hpj,sum(jsd.reject*p.hargadasar) hpp FROM job_sewing AS js 
INNER JOIN job_sewing_detail AS jsd ON 
(js.no_sew=jsd.no_sew) 
inner join produk as p on 
(p.kode=jsd.kd_produk)
WHERE  js.no_co IN ($kode_in) AND  js.approve2='1'  GROUP BY SUBSTRING(jsd.kd_produk,1,7)";
 }else  if($jenis_cek=='qc'){
 	$sql="SELECT SUBSTRING(jqd.kd_produk,1,7) as model, SUM(grade_a)AS grade_a,SUM(grade_b) AS grade_b FROM job_qc_detail AS jqd
INNER JOIN job_qc AS jq ON 
(jq.no_qc=jqd.no_qc)
WHERE jq.no_co IN ($kode_in) GROUP BY SUBSTRING(jqd.kd_produk,1,7)";
 }else  if($jenis_cek=='do'){

 $sql="SELECT  SUM(IFNULL(k.jumlah_kirim,0)) AS qty,SUBSTRING(k.kd_produk,1,7) AS model FROM 
(SELECT pd.kd_produk, SUM(pd.qty) AS jumlah FROM po_manufaktur_detail AS pd 
INNER JOIN po_manufaktur AS p ON (pd.no_manufaktur = p.no_manufaktur)
 WHERE p.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' 
 AND p.closeco IS NULL AND  SUBSTRING(pd.kd_produk,1,7) IN ($kode_in) 
 GROUP BY pd.kd_produk) AS a LEFT JOIN (SELECT dd.kd_produk, SUM(dd.qty) AS 
 jumlah_kirim,d.tanggal,d.no_do FROM do_produk_detail AS dd INNER JOIN do_produk AS d ON 
 (dd.no_do = d.no_do) WHERE d.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'
 AND  SUBSTRING(dd.kd_produk,1,7) in  ($kode_in) AND d.no_do NOT LIKE 'BTL%' AND dd.kd_produk LIKE '%' 
 GROUP BY dd.kd_produk) AS k   ON k.kd_produk=a.kd_produk WHERE k.jumlah_kirim>0 GROUP BY SUBSTRING(k.kd_produk,1,7) ";
 
 $sql="SELECT  SUM(IFNULL(k.jumlah_kirim,0)) AS qty,SUBSTRING(k.kd_produk,1,7) AS model,SUM(IFNULL(k.jumlah_kirim,0)*p.hargajual) as hpj,SUM(IFNULL(k.jumlah_kirim,0)*p.hargadasar) as hpp FROM 
(SELECT pd.kd_produk, SUM(pd.qty) AS jumlah FROM po_manufaktur_detail AS pd 
INNER JOIN po_manufaktur AS p ON (pd.no_manufaktur = p.no_manufaktur)
 WHERE p.tanggal BETWEEN  '$dari 00:00:00' AND '$sampai 23:59:59' 
 AND p.closeco IS NULL AND  SUBSTRING(pd.kd_produk,1,7) IN ($kode_in) 
 GROUP BY pd.kd_produk) AS a  
 INNER JOIN produk AS p ON 
 (p.kode=a.kd_produk)
 LEFT JOIN (SELECT dd.kd_produk, SUM(dd.qty) AS 
 jumlah_kirim,d.tanggal,d.no_do FROM do_produk_detail AS dd INNER JOIN do_produk AS d ON 
 (dd.no_do = d.no_do) WHERE d.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' 
 AND  SUBSTRING(dd.kd_produk,1,7) IN  ($kode_in) AND d.no_do NOT LIKE 'BTL%' AND dd.kd_produk LIKE '%' 
 GROUP BY dd.kd_produk) AS k   ON k.kd_produk=a.kd_produk WHERE k.jumlah_kirim>0 GROUP BY SUBSTRING(k.kd_produk,1,7)";
 
 
 $sql="SELECT SUM(dpd.qty) AS qty ,SUBSTRING(dpd.kd_produk,1,7) AS model, 
 SUM(dpd.harga*dpd.qty) AS hpj,SUM(dpd.hpp*dpd.qty) AS hpp FROM do_produk AS dp INNER JOIN
do_produk_detail AS dpd ON 
(dpd.no_do=dp.no_do) 
INNER JOIN produk AS p ON 
(p.kode=dpd.kd_produk)
LEFT JOIN pabrik AS pb ON 
(pb.id=SUBSTRING(dp.no_do,1,5))
 WHERE  
(dp.no_do NOT LIKE '%mst%' AND dp.no_do NOT LIKE '%btl%' AND dp.no_do NOT LIKE '%test%') 
AND dp.tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'   AND dp.keterangan!='P100S' 
 AND SUBSTRING(dpd.kd_produk,1,7) IN  ($kode_in) 
GROUP BY SUBSTRING(dpd.kd_produk,1,7)";
 }

		$res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>