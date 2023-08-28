<?php
    session_start();
    @$username=$_SESSION["username"];
    if(empty($username)){
        die('You can\'t see this page');
    }
  include_once('config.php');
  
  
  $tgl1=$_POST['t1'];
  $tgl2=$_POST['t2'];
  $tgl=$_POST['t'];
  $kd=$_POST['kd'];
  $keterangan=$_POST['k'];
  $gudang=$_POST['g'];
  $id=$_POST['id'];
  $pilihan=$_POST['pl'];
  
  #print_r($_POST);
  
  
 
  $jenis=$_POST['j'];
  
  $isJson=false;
  
  
 
  if($jenis=='ip'){//insert Progress
     $isJson=true;
	 $kd="PJM-".date('YmdHis');
	 $beda=0;
	 
	 $d = explode(']', $gudang);
	 $kd_gudang=trim(str_replace('[','',$d[0]));
	 
	 //Cari kode Barangnya terlebih dahulu
	 $sql="SELECT nama FROM barang_aset WHERE kd_aset='$kd_gudang';";
	 $sql="SELECT * FROM (
SELECT id,nama FROM outlet WHERE  id='$kd_gudang'  
UNION SELECT id,nama FROM gudang_distribusi WHERE id='$kd_gudang') AS a ORDER BY a.id";
	 $res=mysql_query($sql) or die('error#'.$sql.' # '.mysql_error());
	 list($ada)=mysql_fetch_array($res);
	 if(empty($ada)){
	    die('error#Data Gudang tidak ditemukan dimaster, isi data dengan lengkap');
	 }
	 
	 // $sql="INSERT INTO pinjam(kode_pinjam, tgl, peminjam, kd_aset, app_pinjam_by) ";
  //    $sql.="VALUES ('$kd','$tgl1','$peminjam','".$kd_barang[0]."','$username');";

     $q="SET autocommit = 0;";
	 $qq=mysql_query($q);

	 $q2="START TRANSACTION;";
	 $qq2=mysql_query($q2);
	 
	 $sql="REPLACE INTO `koreksi_gudang_stok_izin` (id,`id_gudang`, `tanggal`, `keterangan`,`update_by`,`update_date`,tipe_so)";
	 $sql.="VALUES ('$kd','$kd_gudang', '$tgl1', '$keterangan', '$username', NOW(),'$pilihan');";
	 
	 if($username=='budi-it'){
		# die('error#'.print_r($_POST).' # '.mysql_error());
	   #die('error#'.$sql.' # '.mysql_error());	 
	 }
	 
	 $res=mysql_query($sql) or die('error#'.$sql.' # '.mysql_error());
	 
	 $sql="UPDATE akses_setuptable SET setup_value=1 WHERE setup_kode='AKM' and kode_outlet='$kd_gudang';";
	 $res=mysql_query($sql) or die('error#'.$sql.' # '.mysql_error());
	 
	 $c="COMMIT;";	
	 $qc=mysql_query($c);
 
	
	 die("ok#$id#$kd#$kd_barang[0]");
	 
    	
  }elseif($jenis=='biz'){//batal izin

  	  $q="SET autocommit = 0;";
	  $qq=mysql_query($q);

	  $q2="START TRANSACTION;";
	  $qq2=mysql_query($q2);

	  $sql="update koreksi_gudang_stok_izin set id='BTL.$kd' where id='$kd'";//hapus izin batal lalu
	  mysql_query($sql);
	  
	  $sql="UPDATE akses_setuptable SET setup_value=0 WHERE setup_kode='AKM' and kode_outlet='$gudang';";
	  $res=mysql_query($sql) or die('error#'.$sql.' # '.mysql_error());
	 
      $c="COMMIT;";	
	  $qc=mysql_query($c);
	  
	 die("ok#$id");
  }elseif($jenis=='cd'){//Cari Data     
	 
	 $sql2="SELECT   SQL_CALC_FOUND_ROWS p.kode_pinjam , DATE_FORMAT(p.tgl,'%d-%M-%Y') , TRIM(p.peminjam) , TRIM(p.kd_aset) , TRIM(b.nama) 
     , TRIM(p.app_pinjam_by) , DATE_FORMAT(p.tgl_kembali,'%d-%M-%Y'), TRIM(p.app_kembali_by)
FROM   barang_aset AS b RIGHT JOIN pinjam AS p  ON (b.kd_aset = p.kd_aset) order by p.tgl DESC LIMIT ".($page*$jmlHal).",".$jmlHal;
	 $res=mysql_query($sql2) or die('error#'.$sql2.' # '.mysql_error());
	 
	 die("ok#$id");
	 //die($sql);	
  }else{
     die('error#could--set');
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
  
  function intervalHari($strDateFrom,$strDateTo) {
        $hasil=1;
       //echo "($strDateFrom,$strDateTo)";
    

       $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
       $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

       if ($iDateTo>=$iDateFrom) {
         while ($iDateFrom<$iDateTo) {
		  $hasil++;
           $iDateFrom+=86400; // add 24 hours
          
         }
       }
       return  $hasil;
   }
  
  
  

?>