<?php session_start();
 
  @$username=$_SESSION["username"];  
  if(empty($username)){ 
       die('You can\'t see this page, Please login First');
  } 
  require_once("config.php");
//include("koneksi_rian.php");
$sql_cache=' SQL_CACHE ';

@$parameter=$_POST['parameter'];
@$cari=$_POST['cari'];
@$gudang=$_POST['gudang'];
@$awal=$_POST['awal'];
@$akhir=$_POST['akhir'];
@$berdasar=$_POST['berdasar'];
@$pabrik=$_POST['p'];
$pecah=explode(",",$parameter);
$code='';
foreach($pecah as $isi){
	$code=$code."r.kd_produk like '$isi%' or ";
}
$code1=strlen($code);$lengt=$code1-4;
$tambahan=" and (".substr($code,0,$lengt).")   ";
 
if(empty($pabrik)){
	 $sql_tambahan='';
 
}else{
	$sql_tambahan=" AND d.keterangan ='$pabrik' ";
	
	
}

$sql_inner=' INNER JOIN do_produk d on d.no_do=r.no_do ';

if($berdasar==""){
	$terusan_berdasar="";
	}else{
		if($berdasar=="kode"){
		$terusan_berdasar=" and p.$berdasar like '$cari%' ";
		}else{
			$terusan_berdasar=" and p.$berdasar like '%$cari%' ";
			}
	}


if(@$_SESSION['id_group1']=='38'){
	if($gudang=='GD.002'){
			$tr_acc=" and pb.st_acc='1' ";}else{
			$tr_acc="  and pb.st_acc='0' ";
		} 
}else{
	$tr_acc=" ";
}


if($gudang=='GD.001'){
	if($cari!=""){
 
		$sql="SELECT $sql_cache SUM(r.qty) AS qty,SUM(r.qty*r.hpp) AS hpp,SUM(r.qty*r.hpj) as hpj  FROM retur_distribusi_rian  r  $sql_inner 
		INNER JOIN produk AS p ON 
(p.kode=r.kd_produk) 
		WHERE    
		r.tanggal BETWEEN '$awal' AND '$akhir' $tambahan   $sql_tambahan $terusan_berdasar AND  
		(r.no_do NOT LIKE '%smt%' OR r.no_do NOT LIKE '%btl%' OR r.no_do NOT LIKE '%test%')  and d.keterangan not like '%S%'  $tr_acc"; 
	}else{
		$sql="SELECT  $sql_cache SUM(r.qty) AS qty,SUM(r.qty*r.hpp) AS hpp,SUM(r.qty*r.hpj) as hpj  FROM retur_distribusi_rian r $sql_inner 
		INNER JOIN produk AS p ON 
(p.kode=r.kd_produk)
inner join pabrik as pb on 
(pb.id=substring(r.no_do,1,5)) 
		WHERE 
	r.tanggal BETWEEN '$awal' AND '$akhir' $tambahan $sql_tambahan $terusan_berdasar  AND 
	(r.no_do NOT LIKE '%smt%' OR r.no_do NOT LIKE '%btl%' OR r.no_do NOT LIKE '%test%') and d.keterangan not like '%S%'   $tr_acc";
	}

}else{ 
	$sql_tambahan.=" AND d.gudang  like '$gudang%' ";
 
	if($cari!=""){
		$sql="SELECT $sql_cache SUM(r.qty) AS qty,SUM(r.qty*r.hpp) AS hpp,SUM(r.qty*r.hpj) as hpj  
		
		FROM retur_distribusi_rian r $sql_inner 
		INNER JOIN produk AS p ON 
(p.kode=r.kd_produk)
		WHERE   
		r.tanggal BETWEEN '$awal' AND '$akhir' $tambahan $sql_tambahan $terusan_berdasar  AND 
		(r.no_do NOT LIKE '%smt%' OR r.no_do NOT LIKE '%btl%' OR r.no_do NOT LIKE '%test%') and d.keterangan not like '%S%'   ";
	
	}else{
		$sql="SELECT  $sql_cache SUM(r.qty) AS qty,SUM(r.qty*r.hpp) AS hpp,SUM(r.qty*r.hpj) as hpj  FROM retur_distribusi_rian r $sql_inner
		INNER JOIN produk AS p ON 
(p.kode=r.kd_produk)
inner join pabrik as pb on 
(pb.id=substring(r.no_do,1,5))
		 WHERE 
	r.tanggal BETWEEN '$awal' AND '$akhir' $tambahan  $sql_tambahan $terusan_berdasar AND 
	(r.no_do NOT LIKE '%smt%' OR r.no_do NOT LIKE '%btl%' OR r.no_do NOT LIKE '%test%') and d.keterangan not like '%S%'
	  $tr_acc";
	}
	
	
}

   #die($sql);
   if($username=='rian-it'){
	  die($sql);
   }
   
   if($username=='budi-it'){//debug 26042018
			$sql_debug=mysql_escape_string($sql);
			$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -j $jenis ' , '$sql_debug');";
			mysql_query($sql_insert);
	}
    
	 $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	
?>