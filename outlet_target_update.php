<?php
  require_once("config.php");
  
  error_reporting(1);
  
 /* $id = $_POST['id'];
  $value = $_POST['value'];
*/
  function jumlahHari($month,$year) {
	   return date("j", strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00'))));
	}
	
	function createDateRangeArray($strDateFrom,$strDateTo) {
  
   
      $aryRange=array();

       $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
       $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

       if ($iDateTo>=$iDateFrom) {
         array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry

         while ($iDateFrom<$iDateTo) {
           $iDateFrom+=86400; // add 24 hours
           array_push($aryRange,date('Y-m-d',$iDateFrom));
         }
       }
       return $aryRange;
   }
  
  
  foreach ($_POST as $k=>$v) {
	   $k=trim($k);
	   switch ($k) {
		   case 'value':
		     $omset=$v;
		     break;
		   case 'id':
		     $otl_periode=$v;
		     break;
		  
		     
	   }
	   
  } 
  
  $array_hari = array(1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat', 6=>'Sabtu',7=>'Minggu');
  $omset=preg_replace('/[,]/','',$omset);
  $data=split('#',$otl_periode);
  $kode_outlet=$data[0];
  $periode=$data[1].'-01';
  $datatgl=split('-',$data[1]);
  $tahun=$datatgl[0];
  $bulan=$datatgl[1];
  $jpb=jumlahHari($bulan,$tahun);
  //echo  $jpb;
  $list_hari=createDateRangeArray("$tahun-$bulan-01","$tahun-$bulan-$jpb");
  
  $sql="select trim(hari),presentasi from outlet_target_hitung where periode like '$periode%'";
  $res=mysql_query($sql) or die($sql);
  while(list($key,$value)=mysql_fetch_array($res)){
	$nilai[$key]=$value;
  }
  $hitung_hari=0;  
  foreach($list_hari as $tgl){	     
		  $data=split('-',$tgl);
		  $day = mktime(0, 0, 0,$data[1],$data[2],$data[0]);
		  $hari = $array_hari[date('N',$day)];		  
		  $hitung_hari+=$nilai[strtolower($hari)];
		  
		  /*
		  
		  if($hari=='Minggu'){
			  $hitung_hari+=3;
		  }elseif($hari=='Sabtu'){
			  $hitung_hari+=3;
		  }elseif($hari=='Jumat'){
			  $hitung_hari+=2;
		  }else{
			  $hitung_hari++;
		  }*/
			 
	}
  //echo "$hitung_hari; $hari;";
  //print_r($list_hari);
  
  $jpb=$hitung_hari;
  //$target_harian=$omset/;
  $target_harian=$omset/$jpb;
  
  //===update outlet_target_bulanan
  $sql="UPDATE outlet_target_bulanan SET target_bulanan='$omset',jpb='$jpb',target='$target_harian'
        WHERE id_outlet='$kode_outlet' AND periode='$periode'";

  $res=mysql_query($sql) or die($sql.' '.mysql_error());
  
  if(mysql_affected_rows()<=0){
	  $sql="INSERT INTO outlet_target_bulanan(id_outlet,periode, target_bulanan, jpb, target)
			VALUES ('$kode_outlet', '$periode','$omset', '$jpb','$target_harian');";  
	  $res=mysql_query($sql);
  };
  
  $sql="UPDATE rekap_target_outlet_bulanan
		SET target_bulanan='$omset',target_harian='$target_harian'
		WHERE kode_outlet='$kode_outlet' AND periode='$periode'";
		
  $res=mysql_query($sql) or die($sql.' '.mysql_error());
  //echo "Kode Outlet ='$kode_outlet' Omset=$omset Periode=$periode JumlahHari =$jpb Bulan/Tahun $bulan,$tahun";
 
  echo number_format($omset,2,'.',',');
 

?>