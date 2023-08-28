<?php
    session_start();
    @$username=$_SESSION["username"];
    if(empty($username)){
        die('You can\'t see this page');
    }
  include_once('config.php');
  
  
  $tgl1=$_POST['t1'];
  $tgl2=$_POST['t2'];
  $model=$_POST['m'];
  $jenis=$_POST['j'];
  
  $isJson=false;
 
  if($jenis=='so'){//sell reshare
     $isJson=true;
      $sql="SELECT mr.kode_model AS id,mr.periode as bulan,mr.qty AS qty FROM rekap_po_do_reshare_sell mr INNER JOIN mst_model m 
	      ON mr.kode_model=CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode) 
	       WHERE m.model  LIKE '%$model%' AND mr.periode BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
	 //die($sql);	
  }elseif($jenis=='ro'){
      $isJson=true;
     $sql="SELECT mr.kode_model AS id,mr.periode as bulan,mr.qty AS qty FROM rekap_po_do_reshare_retur mr INNER JOIN mst_model m 
	      ON mr.kode_model=CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode) 
	       WHERE m.model  LIKE '%$model%' AND mr.periode BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
		   //die($sql);
  }elseif($jenis=='sm'){
       $isJson=true;
       $sql="SELECT mr.kode_model AS id,mr.periode as bulan,mr.qty AS qty FROM rekap_po_do_markas_sell mr INNER JOIN mst_model m 
	      ON mr.kode_model=CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode) 
	       WHERE m.model  LIKE '%$model%' AND mr.periode BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
		  // die($sql);
  }elseif($jenis=='rm'){
      $isJson=true;
	  $sql="SELECT mr.kode_model AS id,mr.periode as bulan,mr.qty AS qty FROM rekap_po_do_markas_retur mr INNER JOIN mst_model m 
	      ON mr.kode_model=CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode) 
	       WHERE m.model  LIKE '%$model%' AND mr.periode BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
  
  }elseif($jenis=='sd'){
      $isJson=true;
      $sql="SELECT mr.kode_model AS id,mr.periode as bulan,mr.qty AS qty FROM rekap_po_do_distribusi_sell mr INNER JOIN mst_model m 
	      ON mr.kode_model=CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode) 
	       WHERE m.model  LIKE '%$model%' AND mr.periode BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
  }elseif($jenis=='rd'){
      $isJson=true;
	  $sql="SELECT mr.kode_model AS id,mr.periode as bulan,mr.qty AS qty FROM rekap_po_do_distribusi_retur mr INNER JOIN mst_model m 
	      ON mr.kode_model=CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode) 
	       WHERE m.model  LIKE '%$model%' AND mr.periode BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
  }elseif($jenis=='co'){
      $isJson=true;
	  $sql="SELECT   SUBSTR(jd.kd_produk,1,7) as id ,SUM(jd.qty_produk) as qty FROM job_gelaran_detail AS jd INNER JOIN job_gelaran AS j 
ON (jd.no_co = j.no_co) INNER JOIN po_manufaktur p ON p.no_manufaktur=j.no_po 
INNER JOIN mst_model m  ON SUBSTR(jd.kd_produk,1,7)=CONCAT(m.kode_basic_item,m.kode_kategori,m.kode_kelas,m.kode_style,m.kode) 
WHERE m.model LIKE '%$model%' AND   p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' GROUP BY SUBSTR(jd.kd_produk,1,7);";
     //die($sql);
  }elseif($jenis=='do'){
     $isJson=true;
	  $sql="SELECT   SUBSTR(jd.kd_produk,1,7) as id ,SUM(jd.qty_produk) as qty FROM job_gelaran_detail AS jd INNER JOIN job_gelaran AS j 
ON (jd.no_co = j.no_co) INNER JOIN po_manufaktur p ON p.no_manufaktur=j.no_po 
INNER JOIN mst_model m  ON SUBSTR(jd.kd_produk,1,7)=CONCAT(m.kode_basic_item,m.kode_kategori,m.kode_kelas,m.kode_style,m.kode) 
WHERE m.model LIKE '%$model%' AND   p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' GROUP BY SUBSTR(jd.kd_produk,1,7);";
  
  }else{
     die('could--set');
  }
  
  if($isJson==true){
     $res=mysql_query($sql) or die($sql.' # '.mysql_error());
	 $result = array();
	 while($row=mysql_fetch_object($res))
     {
	     array_push($result, $row);
	 }
		 
	echo json_encode($result);	
  } 
  
  
  
  
  

?>