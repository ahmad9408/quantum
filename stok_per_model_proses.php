<?php 
 session_start();
    @$username=$_SESSION["username"];
    if(empty($username)){
       die('You can\'t see this page');
    }

set_time_limit(86400);// 24 jam
require_once("config.php");//edit 19012016

$proses=$_POST['proses'];
if($proses=="cari_foto"){
	$kode=$_POST['foto'];
	$sql="SELECT nama_file FROM mst_model_foto WHERE kode_model='$kode'";
	$query=mysql_query($sql)or die($sql);
	list($foto)=mysql_fetch_array($query);
	echo $foto;die;
	}else{

$sql="SELECT nilai FROM config_system WHERE kode_config='ospd';";
	$res=mysql_query($sql);
   list($showOnlyPositif)=mysql_fetch_array($res);
   if($showOnlyPositif==1){
	   //echo "Data Stok minus tidak ikut dimunculkan;";
	   $sqlOnlyPositif= ' AND ps.stok>0 ';  
   }else{
	   $sqlOnlyPositif= '';
   }

$paramater=$_POST['paramater'];
$gudang=$_POST['gudang'];
$cari=$_POST['cari'];
$berdasar=$_POST['berdasar'];

//tambahan 16 jun 2014
$isByTglLaunching=$_POST['bt'];
$tgl1=$_POST['t1'];
$tgl2=$_POST['t2'];
$jg=$_POST['jg'];//jenis gudang 1 internal 2:mitra
$pilihan=$_POST['pp'];
if(empty($jg)){//default 1
   	$jg=1;
}

$inner_mst_model='';

if($berdasar=="kode"){
	$terusan_all=" and p.kode like '$cari%' ";	
}else if($berdasar=="nama"){
	$terusan_all=" and f.nama_model like '%$cari%'";	
	$inner_mst_model='left join mst_model_fix as f on f.kode_model=substring(p.kode,1,7)';//tambahan 25022016 by budi
}
$pecah=explode("-",$paramater);
$banyak=count($pecah);
for($i=0;$i<$banyak;$i++){
	if($i==($banyak-1)){
		$temp_code.= "'$pecah[$i]'";
	}else{
		$temp_code.= "'$pecah[$i]',";
	}
}
$ter="    (ps.kode_gudang LIKE 'GD.%' AND ps.kode_gudang NOT LIKE '%S%' )";
$terusan=" and SUBSTRING(ps.kode_produk,1,7) in ($temp_code)";
if($gudang=="gunas"){
	$sql_inner_launch='';
	if(!empty($pilihan)){
		$sql_inner_launch.='INNER JOIN produk_pilihan  as pp on pp.id_barang = `p`.`kode`';
		$terusan.=" AND pp.pilihan='$pilihan' ";
	}
	
  $sql="SELECT SQL_CACHE SUBSTRING(ps.kode_produk,1,7) AS kode, SUM(ps.stok) AS qty,SUM(ps.hargajual*ps.stok) as uang 
FROM `produk_stok` as ps 
   INNER JOIN  `produk` as p  
        ON (`ps`.`kode_produk` = `p`.`kode`) $sql_inner_launch WHERE   $ter
		$sqlOnlyPositif and length(kode_produk)>13 $terusan GROUP BY SUBSTRING(kode_produk,1,7)";
}else if($gudang=="markas"){
	$sql_inner_launch='';
	if(!empty($pilihan)){
		$sql_inner_launch.='INNER JOIN produk_pilihan  as pp on pp.id_barang = `p`.`kode`';
		$terusan.=" AND pp.pilihan='$pilihan' ";
	}
	
	$sql="SELECT SQL_CACHE  SUBSTRING(ps.kode_produk,1,7) AS kode,SUM(ps.stok) AS qty,sum(ps.harga_jual*ps.stok) as uang 
FROM
    `outlet` AS `o`
    INNER JOIN `outlet_stok` AS `ps`   ON (`o`.`id` = `ps`.`kode_outlet`)
    INNER JOIN `produk` AS `p` 
        ON (`p`.`kode` = `ps`.`kode_produk`) $sql_inner_launch  WHERE    `o`.`jenis`='1' $sqlOnlyPositif $terusan AND o.id NOT LIKE 'Ms%'
        and length(ps.kode_produk)=15 AND o.`type` =1 and o.aktif=1 and length(ps.kode_produk)>13 GROUP BY SUBSTRING(ps.kode_produk,1,7)";
}else if($gudang=="reshare"){
	$sql_inner_launch='';
	if(!empty($pilihan)){
		$sql_inner_launch.='INNER JOIN produk_pilihan  as pp on pp.id_barang = `p`.`kode`';
		$terusan.=" AND pp.pilihan='$pilihan' ";
	}
	 $sql="SELECT SQL_CACHE SUBSTRING(ps.kode_produk,1,7) AS kode,SUM(ps.stok) AS qty,SUM(ps.harga_jual*ps.stok) AS uang
	FROM `outlet` AS `o` INNER JOIN `outlet_stok` AS `ps` ON (`o`.`id` = `ps`.`kode_outlet`)
		INNER JOIN `produk` AS `p` 	ON (`p`.`kode` = `ps`.`kode_produk`) $sql_inner_launch
			 WHERE  `o`.`jenis`='1' $sqlOnlyPositif  $terusan AND o.id NOT LIKE 'Ms%' and 
			length(ps.kode_produk)='15'
			AND o.`type`=4 and o.aktif=1 GROUP BY SUBSTRING(ps.kode_produk,1,7)";//stok di set harus reshare aktif
	
}else if($gudang=="gunas_all"){
	$sql_inner_launch='';
	$sql_tbhn='';
	if($isByTglLaunching==1){
		$sql_inner_launch.=' INNER JOIN mst_model_launching  as mm on mm.kode_model = substring(`p`.`kode`,1,7) ';
	    $sql_tbhn.=" AND mm.tanggal_launching BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' ";	
	}
	
	//29092016
	if(!empty($pilihan)){
		$sql_inner_launch.='INNER JOIN produk_pilihan  as pp on pp.id_barang = `p`.`kode`';
		$sql_tbhn.=" AND pp.pilihan='$pilihan' ";
	}
$sql="SELECT SQL_CACHE SUM(ps.stok) AS qty,SUM(ps.hargajual*ps.stok) as uang 
FROM `produk_stok` as ps   INNER JOIN  `produk` as p      ON (`ps`.`kode_produk` = `p`.`kode`)
	$inner_mst_model	 $sql_inner_launch
		 WHERE   $ter $sqlOnlyPositif and length(kode_produk)>13 $terusan_all $sql_tbhn  ";
	if($username=='budi-it'){//trace 16 jun 2014
	  //sudah di baris paling bawah
	}
}else if($gudang=="markas_all"){
	
	$sql_inner_launch='';
	$sql_tbhn='';
	if($isByTglLaunching==1){
		$sql_inner_launch=' INNER JOIN mst_model_launching  as mm on mm.kode_model = substring(`p`.`kode`,1,7) ';
	    $sql_tbhn=" AND mm.tanggal_launching BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' ";	
	}
	//29092016
	if(!empty($pilihan)){
		$sql_inner_launch.='INNER JOIN produk_pilihan  as pp on pp.id_barang = `p`.`kode`';
		$sql_tbhn.=" AND pp.pilihan='$pilihan' ";
	}
	
$sql="SELECT SQL_CACHE SUM(ps.stok) AS qty,sum(ps.harga_jual*ps.stok) as uang 
FROM  `outlet` AS `o`  INNER JOIN `outlet_stok` AS `ps`   ON (`o`.`id` = `ps`.`kode_outlet`)
    INNER JOIN `produk` AS `p`  ON (`p`.`kode` = `ps`.`kode_produk`) 
	$inner_mst_model $sql_inner_launch
	WHERE   o.`type`=1 AND  `o`.`jenis`='$jg' $sqlOnlyPositif   AND o.id NOT LIKE 'ms%'
    and length(ps.kode_produk)=15 and o.aktif=1 and length(ps.kode_produk)>13 $terusan_all  $sql_tbhn ";
	if($username=='budi-it'){//trace 16 jun 2014
	  #die($sql);	
	}
}else if($gudang=="reshare_all"){
	$sql_inner_launch='';
	$sql_tbhn='';
	if($isByTglLaunching==1){
		$sql_inner_launch=' INNER JOIN mst_model_launching  as mm on mm.kode_model = substring(`p`.`kode`,1,7) ';
	    $sql_tbhn=" AND mm.tanggal_launching BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' ";	
	}
	//29092016
	if(!empty($pilihan)){
		$sql_inner_launch.='INNER JOIN produk_pilihan  as pp on pp.id_barang = `p`.`kode`';
		$sql_tbhn.=" AND pp.pilihan='$pilihan' ";
	}
 $sql="SELECT SQL_CACHE SUM(ps.stok) AS qty,SUM(ps.harga_jual*ps.stok) AS uang FROM`outlet` AS `o`
		INNER JOIN `outlet_stok` AS `ps` ON (`o`.`id` = `ps`.`kode_outlet`)
		INNER JOIN `produk` AS `p` ON (`p`.`kode` = `ps`.`kode_produk`)
		$inner_mst_model	 $sql_inner_launch
			 WHERE   o.type=4 AND `o`.`jenis`='$jg' $sqlOnlyPositif   AND o.id NOT LIKE 'ms%' and 
			length(ps.kode_produk)='15'	AND o.`type`=4 and o.aktif=1 $terusan_all $sql_tbhn ";
	if($username=='budi-it'){//trace 16 jun 2014
	 # die($sql);	
	}
	
}
 
   	  
    if($username=='budi-it'){//19012016
		$sql_debug=mysql_escape_string($sql);
		$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)."', '[ pilihan $gudang ] #$sql_debug');";
		mysql_query($sql_insert);
	}

		$res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res)){
	     array_push($result, $row);
	}
		 
	echo json_encode($result);	
	}
?>