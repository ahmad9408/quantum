<?php
    session_start();
    @$username=$_SESSION["username"];
    if(empty($username)){
     die('You can\'t see this page');
		//die('<a href="login.php">Relogin (session is timeoute)</a>');
    }
  include_once('config.php');
  
  
  $q = strtolower($_GET["q"]);
  $kodeoutlet=$_SESSION['outlet'];
  $area_lain=$_SESSION['area_lain']; 
  $group=substr($kodeoutlet,5,5);
  if($group=='O0000' ||$group=='o0000'){
	  $area=substr($kodeoutlet,0,4);
  }else{
	  $area=$kodeoutlet;  
  }
	 
//   $data_tambahan=" o.id LIKE '$area%' ";
//   if(!empty($area_lain)){
// 	  $d=explode(';',$area_lain); 
// 	  $d_lain='';
// 	  foreach($d as $kd_lain){
// 		  $kd_lain=trim($kd_lain);
// 			if(!empty($kd_lain)){
// 				$d_lain.=" OR o.id LIKE '$kd_lain%' ";
// 			}
// 	  }
// 	   $data_tambahan.=$d_lain;
//   } 	  
//   $sql_tambahan=" AND ( $data_tambahan ) ";
//   if(!empty($prefix)){
// 	  $sql_tambahan.=" AND  o.id LIKE '$prefix%'  ";
//   }
  
  
// if (!$q) return;
// $sql="SELECT  trim(kd_aset),trim(nama) FROM barang_aset WHERE nama LIKE '%$q%' and state=0 ORDER BY nama";
// $sql_tbhn='';
// if($username=='budi-it'){
// 	$sql_tbhn = " OR (id like '%S%' )";
// }
$sql="SELECT $sql_cache id, nama from pabrik where status='1'";

$res = mysql_query($sql);
				               
  while(list($kode,$nama)=mysql_fetch_array($res)){
	echo "[ $kode ] $nama \n";
   } 
   
?>
