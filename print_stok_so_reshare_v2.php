<?php
session_start();
 @$username=$_SESSION["username"];
  if(empty($username)){
        die('You can\'t see this page');
   }


  if(isset($_REQUEST['action'])=='export'){
	  
  }else{
     die("Failed acces");	  
  }


 set_time_limit(86400);// 24 jam
 require_once("config.php");

	
	
	
$tgl1=$_POST['tgl1'];
//$tahun1=$_POST['tahunl'];
$outlet=$_POST['outlet'];
$barcode=trim($_POST['barcode']);	

$tgl1=$_POST['tgl1'];
       
		

$isShowHpp=0;
if($username=='budi-it'||$username=='faipusat_yati'){//tambahan 23122015 request p yudi
  $isShowHpp=1;
}


$sql="SELECT COUNT(*) ada  FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
$res_show_hpp=mysql_query($sql);
list($check_hpp)=mysql_fetch_array($res_show_hpp);
if($check_hpp>=1){
 $isShowHpp=1;
}		
		
$filter='';


if(!empty($barcode)){
	$filter=" AND kso.kode_15 like '$barcode%'";			
}

if(!empty($txt_nama)){
	$filter.=" AND p.nama like '%$txt_nama%' ";
}
$tgl_now=$_SESSION['tgl1'];


$d=explode("-",$tgl1);		
$table_koreksi_stok='';
if($d[0].'-'.$d[1]!=date('Y-m')){
	 $table_koreksi_stok='koreksi_outlet_stok_'.$d[1].$d[0];  			 
}else{
	 $table_koreksi_stok='koreksi_outlet_stok';			
}

$hpp_field='0';
if($isShowHpp==1){
	$hpp_field='p.hargadasar';
}
$main_sql="SELECT SQL_CALC_FOUND_ROWS p.kode_grade_a,kso.kode_15,p.nama,kso.stok_awal,(kso.stok_awal* $hpp_field ) AS hpp_awal
,(kso.stok_awal*p.hargajual) AS hpj_awal,kso.stok_akhir,(kso.stok_akhir* $hpp_field ) AS hpp_SO
,(kso.stok_akhir*p.hargajual) AS hpj_SO 
,kso.qty AS qty_koreksi
,(kso.qty* $hpp_field ) as hpp_koreksi
,(kso.qty*p.hargajual) as hpj_koreksi       
,kso.kd_outlet,kso.update_date
FROM $table_koreksi_stok AS kso LEFT JOIN produk AS p  
ON (p.kode = kso.kode_15) WHERE kso.kd_outlet='$outlet' AND 
kso.tanggal='$tgl1' $filter order by kso.kode_15 ";	
	
$sql=$main_sql;	
	
$judul="SO_reshare_pertanggal_".$tgl1;


#$sql=$_SESSION['export_reshare'];
$sql=str_replace("\\","",$sql); 
#die($sql);
$result=mysql_query($sql);
if(!$result){
  #die("salah");
  
  echo "<html>";
  
  #echo "<!--  $sql -->";
  
  echo "</html>";
  die();
}


$x=2000;
$total=number_format($x,2,",","."); 

// Fungsi buat mengexpor ke xls support 2003 dan 2007 
function xlsBOF() { 
echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0); 
return; 
} 
function xlsEOF() { 
echo pack("ss", 0x0A, 0x00); 
return; 
} 
function xlsWriteNumber($Row, $Col, $Value) { 
echo pack("sssss", 0x203, 14, $Row, $Col, 0x0); 
echo pack("d", $Value); 
return; 
} 
function xlsWriteLabel($Row, $Col, $Value ) { 
$L = strlen($Value); 
echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L); 
echo $Value; 
return; 
} 
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=$judul".date('YmdHis').".xls "); 
header("Content-Transfer-Encoding: binary ");

xlsBOF();

// 1 menandakan baris dan perubahan angka menandakan kolom

if($isShowHpp==1){
	xlsWriteLabel(0,0,"No");
	xlsWriteLabel(0,1,"Barcode13");
	xlsWriteLabel(0,2,"Barcode15");
	xlsWriteLabel(0,3,"Nama Produk");
	xlsWriteLabel(0,4,"Data Stok");
	xlsWriteLabel(0,7,"Stok Opname");
	xlsWriteLabel(0,10,"Selisih(Plus/Minus)");
	
}else{
	xlsWriteLabel(0,0,"No");
	xlsWriteLabel(0,1,"Barcode13");
	xlsWriteLabel(0,2,"Barcode15");
	xlsWriteLabel(0,3,"Nama Produk");
	xlsWriteLabel(0,4,"Data Stok");
	xlsWriteLabel(0,6,"Stok Opname");
	xlsWriteLabel(0,8,"Selisih(Plus/Minus)");
}



if($isShowHpp==1){
	xlsWriteLabel(1,4,"Qty");
	xlsWriteLabel(1,5,"HPP");
	xlsWriteLabel(1,6,"HPJ");
	xlsWriteLabel(1,7,"Qty");
	xlsWriteLabel(1,8,"HPP");
	xlsWriteLabel(1,9,"HPJ");
	xlsWriteLabel(1,10,"Qty");
	xlsWriteLabel(1,11,"HPP");
	xlsWriteLabel(1,12,"HPJ");
	
}else{
	xlsWriteLabel(1,4,"Qty");	
	xlsWriteLabel(1,5,"HPJ");
	xlsWriteLabel(1,6,"Qty");	
	xlsWriteLabel(1,7,"HPJ");
	xlsWriteLabel(1,8,"Qty");	
	xlsWriteLabel(1,9,"HPJ");
}


$xlsRow = 2;
$stoktot=0; $total=0;
while(list($barcode13,$kode,$nama,$stok_awal,$hpp_awal,$hpj_awal,$stok_akhir,$hpp_so,$hpj_so,$qty_koreksi,$hpp_koreksi,$hpj_koreksi,$kd_outlet,$update)=mysql_fetch_array($result)){
				$s++;
						 if($username=='budi-it'||$username=='faipusat_yati'||$isShowHpp==1 ){
	  
						 }else{
							 $hpp_awal=0;
							 $hpp_so=0;
							 $hpp_koreksi=0;
							 
						 }
						if($isShowHpp==1){
							xlsWriteLabel($xlsRow,0,$s);
							xlsWriteLabel($xlsRow,1,$barcode13);
							xlsWriteLabel($xlsRow,2,$kode);
							xlsWriteLabel($xlsRow,3,$nama);
							xlsWriteNumber($xlsRow,4,$stok_awal);
							xlsWriteNumber($xlsRow,5,$hpp_awal);
							xlsWriteNumber($xlsRow,6,$hpj_awal);
							xlsWriteNumber($xlsRow,7,$stok_akhir);
							xlsWriteNumber($xlsRow,8,$hpp_so);
							xlsWriteNumber($xlsRow,9,$hpj_so);
							xlsWriteNumber($xlsRow,10,$qty_koreksi);
							xlsWriteNumber($xlsRow,11,$hpp_koreksi);
							xlsWriteNumber($xlsRow,12,$hpj_koreksi);
						}else{
							xlsWriteLabel($xlsRow,0,$s);
							xlsWriteLabel($xlsRow,1,$barcode13);
							xlsWriteLabel($xlsRow,2,$kode);
							xlsWriteLabel($xlsRow,3,$nama);
							xlsWriteNumber($xlsRow,4,$stok_awal);							
							xlsWriteNumber($xlsRow,5,$hpj_awal);
							xlsWriteNumber($xlsRow,6,$stok_akhir);							
							xlsWriteNumber($xlsRow,7,$hpj_so);
							xlsWriteNumber($xlsRow,8,$qty_koreksi);							
							xlsWriteNumber($xlsRow,9,$hpj_koreksi);
						}
						 
						
						
						
						$xlsRow++;//penambahan baris pada excel
						
}
$xlsRow++;

xlsEOF(); //Ahkhiri
exit();
mysql_close();
?>