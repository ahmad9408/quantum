<?php
    session_start();
    @$username=$_SESSION["username"];
    if(empty($username)){
        die('You can\'t see this page');
    }
/*tambah group by suppplier 10032023*/
 require_once("config.php");
 $jenis=$_POST['j']; 
 $markas=$_POST['m'];
 $tgl1=$_POST['t1'];
 $tgl2=$_POST['t2'];
 $outlet=$_POST['o'];
 $organization=$_POST['org'];
 $kode_wilayah=$_POST['w'];
 $asm=$_POST['asm'];
 $produk_pilihan=$_POST['pp'];
 $isApp2=$_POST['iap2'];
 $jenis_outlet=$_POST['jo'];

 $barcode_cari =$_POST['b'];
 $namaproduk =$_POST['np'];
 
 
 $now=date('Y-m-d');
 if($tgl2==$now){
	$expired_cache=900;
 }
 $expired_cache=1;
 //check hpp
 $isShowHpp=0;
  $sql="SELECT COUNT(*) ada  FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
 $res_show_hpp=mysql_query($sql);
 list($check_hpp)=mysql_fetch_array($res_show_hpp);
 if($check_hpp>=1){
	 $isShowHpp=1;
 }
 $isJsonData=1;
 
 
 if($jenis=='gbfob'){//get bayar FOB
	 $filter='';
	 $inner='';
	 


	 
	
	 
	 $sql="SELECT id_supplier as id,SUM(total_bayar) AS bayar,sum(total_harga) tagihan ,sum(total_bayar-total_harga) sisa FROM fob_receiving 
	      WHERE tgl_datang BETWEEN '$tgl1 00:00:00' and  '$tgl2 23:59:59' GROUP BY id_supplier";
	# die($sql);
 }elseif($jenis=='gtjtfob'){//get Tagihan jatuh tempo
	 $filter='';
	 $inner='';
	 


	 
	
	 
	 $sql="SELECT id_supplier as id,SUM(total_harga-total_bayar) AS tagihan FROM fob_receiving WHERE tgl_jatuhtempo BETWEEN '$tgl1 00:00:00' and  '$tgl2 23:59:59' GROUP BY id_supplier";
	# die($sql);
 }else{
   die('Tidak ada pilihan jenis');	 
}
 
  
 
 

 if($isJsonData==1){
	 
	if($username=='budi-it'){
		$sql_debug=mysql_escape_string($sql);
		$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -j $jenis' , '$sql_debug');";
		mysql_query($sql_insert);
	}
	$use_redis=1;//variable untuk menghindari fatal error jika tidak ada koneksi ke server
	require_once("redis_config.php");
	if(empty($expired_cache)){
		$expired_cache=86400;//default 1 hari;
	}
	try{
		$redis = new Redis(); 
		$connected=$redis->connect($host_redis, $port_redis);
		if(!$connected) {			
			$use_redis=0;
		}
		if($use_redis==1){
			$redis->auth($pass_redis);	
		}
	}catch (Exception $e) {
	  // exception is raised and it'll be handled here
	  $use_redis=0;
	 }
	$cache_key = 'f:'.basename(__FILE__).',j:'.$jenis.",".md5($sql);
	if($use_redis==1){
		 if ($redis->exists($cache_key)) {# echo "GET FROM REDIS ";		
			$data = unserialize($redis->get($cache_key));		
			die(json_encode($data));
		 }else{
			$is_usedb=1;
		}
	}else{
		$is_usedb=1;
	}
	
	if($is_usedb==1){
		if($username=='budi-it'){
			 $sql_trans = "SET autocommit = 0;";
			$query_trans = mysql_query($sql_trans);
			$sql_trans = "START TRANSACTION;";
			$query_trans = mysql_query($sql_trans);
			
			
			
			$sql_debug=mysql_escape_string($sql);
			$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)." -gudang $gudang   $j -u $username ', '$sql_debug');";
			mysql_query($sql_insert);
			
			$sql_trans = "COMMIT;";
			$query_trans = mysql_query($sql_trans);
		}
		
		
		$res=mysql_query($sql);// or die($sql.' # '.mysql_error());
		 $result = array();
		 while($row=mysql_fetch_object($res)){
			 array_push($result, $row);
		}
		 if($use_redis==1){
			$redis->set($cache_key, serialize($result));  
			$redis->expire($cache_key, $expired_cache); //hitungan detik 60 * 60 * 24 = 86400 //1 hari
		 }
		die(json_encode($result)); 
	}
 }
 $res=mysql_query($sql) or die(mysql_error().'# '.$sql);
 list($nilai)=mysql_fetch_array($res);
 
 //echo "-- $sql => $tgl";
 $nilai=!empty($nilai)?$nilai:'-';
 echo $nilai; 

?>