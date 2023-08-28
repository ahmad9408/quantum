<?php $content_title="Uploading PO Manufaktur"; 
$data_global['is_overridesecurity_mode']='1';
$data_global['overridesecurity_mode']='0';
include_once "header.php"; ?>
<?php
/*  Edited  
    Time : 2010-12-23 
    Date : 09:48:08
    Oleh : goberan@yahoo.com 
	Last Edit by Budi 29 okt 2012    
	
	v3 ditambah dengan tujuan terdiri dari suho atau supplier   
	edit tgl29022016
*/
error_reporting(1);
include "excel_reader2.php";
include_once "clsaddrow.php";
include_once "po_manufaktur_approving_init_v2.php";//asal po_manufaktur_approving_init.php change by budiyantoro
//$no_manufaktur=no_po_rm('2012-01-02'); 


//echo($no_manufaktur);
//include_once "po_manufaktur_approving_init.php";//asal po_manufaktur_approving_init.php change by budiyantoro
//$no_manufaktur=no_po_rm(); 





if ( $_GET[menu] == "proses") {
$data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);
$baris = $data->rowcount($sheet_index=0);
$request_ke=$_POST['tujuan'];
$tanggal_po= trim($data->val(2, 2));// ambil data tanggal baris kedua column ke dua

$dt=explode("-",$tanggal_po);
$thn=(int)$dt[0];
$bln=(int)$dt[1];
$hr=(int)$dt[2];
if(checkdate($bln,$hr,$thn)){
  echo "Upload Po untuk tanggal  $thn - $bln - $hr  <br>";
}else{
   die("Cek Tanggal !!, karena tidak valid  format [Tahun -Bulan - Tanggal ] di data tercantum [ $thn - $bln - $hr ] <a href='uploadmanufaktur_v3.php'>Go Back </a>");	
}
$no_manufaktur=no_po_rm($tanggal_po); 

//Select dulu
$sql="SELECT COUNT(*) FROM po_manufaktur WHERE no_manufaktur='$no_manufaktur';";
$res=mysql_query($sql);
/*if($username=='budi-it'||$username=='merchandise_yati'){
			$sql_debug=mysql_escape_string($sql);
			$sql_insert="INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('".basename(__FILE__)."', '$sql_debug');";
			mysql_query($sql_insert);
		}*/
list($ada)=mysql_fetch_array($res);
if($ada>0){
   echo "<h2>No manufaktur $no_manufaktur  ini telah ada, silakan upload ulang </h2>";	
   include_once('footer.php');
   
   die();
   	
}

#die($no_manufaktur);

//die($no_manufaktur);
?>
<style>
.hilang {display:none;}
</style>
<script>
$(document).ready(function(){
   $('.hide').hide();	
})

</script>
Data Hasil Upload
<table border="1" width="85%" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" style="font-size: 10pt" height="67">
    <tr>
        <td align="center" width="43" height="23" bgcolor="#99CC00"><b>No</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="83"><b>Tanggal</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="116"><b>Barcode</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="280"><b>Item 
        Name</b></td>
        <td align="right" height="23" bgcolor="#99CC00" width="70">
        <p align="center"><b>Ukuran</b></td>
        <td align="center" height="23" bgcolor="#99CC00"><b>Qty</b></td>
        <td align="center" height="23" bgcolor="#99CC00"><b>Satuan</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="129"><b>Price</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="93"><b>Subtotal</b></td>
        <td align="center" bgcolor="#99CC00" width="93"><strong>Target Penjualan</strong></td>
        <td align="center" bgcolor="#99CC00" width="93" ><strong>Row Material Terpakai(rencana)</strong></td>
        <td align="center" bgcolor="#99CC00" width="93" >Satuan(Yard / kg/..)</td>
    </tr>
    
    


<?php
 
 echo "<h3>Try upload po $no_manufaktur </h3>";
  
 $sukses = 0; $gagal = 0;
 
 $sql="SET autocommit = 0;";
$query_trans=mysql_query($sql);

$sql="START TRANSACTION;";
$query_trans=mysql_query($sql);
 
for ($i=2; $i<=$baris; $i++) { 
 // $no = $data->val($i, 1);

 
	    $no=$i-1;
 
  $tanggal = trim($data->val($i, 2));
  $barcode_upload = trim($data->val($i, 3));
  if(!empty($barcode_upload)){
	  
	  $itemname = $data->val($i, 4);
	  $ukuran = $data->val($i, 5);
	  $qty = $data->val($i, 6);
	  $disc = $data->val($i, 7);
	  $price = $data->val($i, 8);
	  $subtotal = $data->val($i, 9);
	  
	  $targetPenjualan=$data->val($i, 10);
	  $jumlahRowMaterial=$data->val($i, 11);
	  $satuan=trim($data->val($i, 12));
	  
	  
	 
	  /* Masukkan Data Ke po_manufaktur_detail */ 
	  
	  //cari barcode15nya
	  //if(strlen(trim($barcode))<>15){
		  $sql1="SELECT TRIM(kode),TRIM(nama),TRIM(kode_size),hargajual FROM produk WHERE kode_grade_a='$barcode_upload';";
		  $res=mysql_query($sql1) or die(mysql_error().' '.$sql1);
		  list($barcode,$nama,$size,$price)=mysql_fetch_array($res);
		 
		 if(empty($barcode)){
			   $sql2="SELECT TRIM(kode),TRIM(nama),TRIM(kode_size),hargajual FROM produk WHERE kode='$barcode_upload';";
			   $res2=mysql_query($sql2) or die(mysql_error().' '.$sql2);
			   list($barcode,$nama,$size,$price)=mysql_fetch_array($res2);
		 }
	  //}
	  if(empty($barcode)){
		 $barcode=$barcode_upload; 
		 $nama='-- Data ini tidak ada dimaster, Check  ...';
		 //echo "Barcode $barcode tidak Masuk ke Po manufaktur karena tidak ada dimaster produk ",PHP_EOL;	// permintaan smev 
		 //continue;// loncat for dan tidak perlu isi data ke
	  }
	   $model=substr($barcode,0,5);
	  if($jumlahRowMaterial>0){
		  $sql="INSERT INTO `produk_rm_consumtion`  (`kode`,`kode_rm`, `rm_consumtion`,`satuan`,`is_bahan_utama`, `updateby`,`updatedate`)
VALUES ('$barcode', '-','$jumlahRowMaterial','$satuan', '1', '$username', NOW())
ON DUPLICATE KEY UPDATE rm_consumtion='$jumlahRowMaterial', satuan='$satuan',updateby='$username',updatedate=NOW();   --  158 ".basename(__FILE__) ;
		 $res=mysql_query($sql) or die(mysql_error());
	  }else{
		  #$sql="SELECT rm_consumtion,satuan FROM produk_rm_consumtion WHERE  kode='$model'";
	  //echo $sql;
		 #$res2=mysql_query($sql) or die(mysql_error().' '.$sql);   
		  #list($rm,$stuan)=mysql_fetch_array($res2);
		  
		  #$jumlahRowMaterial=$rm;
		  #$satuan=$stuan;
		  
	  }
	  
	  $subtotal=$price * $qty;
	  $query="INSERT INTO po_manufaktur_detail (no_manufaktur,seqno,kd_produk,qty,hargajual,jumlah,targetjual,rm_terpakai,satuan) VALUES ";
	  $query.=" ('$no_manufaktur','$no','$barcode','$qty','$price','$subtotal','$targetPenjualan','$jumlahRowMaterial','$satuan');--  173 ".basename(__FILE__) ;
	  
	  //echo "$sql1 $sql2 $query";
	  $sql=mysql_query($query);
	  if(!$sql){
		  if(mysql_errno()==1062){
			   echo "<h2>Coba ulangi lagi proses Uploadnya [Ada Double (No_PO= $no_manufaktur, baris ke =$no Barcode =$barcode) ] <a href='uploadmanufaktur_v3.php'>Go Back </a> </h2> ";
			   $sql_insert="INSERT IGNORE INTO po_manufaktur(no_manufaktur, tanggal,totalqty, totalrp, closeco,closecoby,closedate,closedesc, approve,approveby,approvedate)
	VALUES ('$no_manufaktur', '$tanggal_po','0','0',1,'sys-input',NOW(),'data detail ada',1,'sys-input',NOW());"; 
				$res=mysql_query($sql_insert) or die(mysql_error()."\n".$sql_insert);
				if($username=='budi-it'){
					 echo $sql_insert."</br>";
				}
				echo " Try insert tmp $no_manufaktur ";
			  # include_once('footer.php');
			   die();
		  }else{
			  # include_once('footer.php');
			   die(mysql_error());   
				 
		  }
	  }
		  
	  $discount=($disc/100)*$subtotal;
	  $sukses++;
	  $totalqty+=$qty;
	  $totalrp_disc += $discount;
	  $totalsubtotal+=$subtotal;  
  
  
  ?>
    <tr>
        <td align="center" width="43" bgcolor="#FFFFFF"><?php echo $no?></td>
        <td align="center" bgcolor="#FFFFFF" width="83"><?php echo $tanggal?></td>
        <td align="left" bgcolor="#FFFFFF" width="116"><?php echo $barcode?></td>
        <td align="left" bgcolor="#FFFFFF" width="280"><?php echo $nama?></td>
        <td align="center" bgcolor="#FFFFFF" width="70"><?php echo $size?></td>
        <td align="center" bgcolor="#FFFFFF"><?php echo $qty?></td>
        <td align="center" bgcolor="#FFFFFF">PCS</td>
        <td align="center" bgcolor="#FFFFFF" width="129"><?php echo $price?></td>
        <td align="right" bgcolor="#FFFFFF" width="93"><?php echo $subtotal?></td>
        <td align="right" bgcolor="#FFFFFF" width="93"><?php echo $targetPenjualan;?></td>
        <td align="right" bgcolor="#FFFFFF" width="93" ><?php echo $jumlahRowMaterial;?></td>
        <td align="right" bgcolor="#FFFFFF" width="93" ><?php echo $satuan;?></td>
  </tr>
  
  <?php
   }//end if not emmpty tgl
}//end if for
/* Masukkan Ke Data Po Manufaktur */
    /* sampai sini */
    $sql="INSERT INTO po_manufaktur (no_manufaktur,tanggal,totalqty,totalrp,approve,approveby,approvedate,request_ke) ";
    $sql.="VALUES ('$no_manufaktur','$tanggal_po','$totalqty','$totalsubtotal','1','$username',NOW(),'$request_ke');--  225 ".basename(__FILE__) ;
    $res = mysql_query($sql,$db) or  die(mysql_error().' '.$sql);

$sql="COMMIT;";	
$query_trans=mysql_query($sql);


    echo "<p>Jumlah data yang sukses diimport dengan no po = $no_manufaktur  : <blink><font color=red><b>".$sukses."</b></font><blink> Baris<br>"; 

 ?>
</table> 
<?php 
  }
?>
<br>
<form action="<?php echo "?menu=proses"; ?>" method="POST" enctype="multipart/form-data">   
# Sample Data Excel

<table border="1" width="85%" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" style="font-size: 10pt" height="67">
  <tr>
        <td align="center" width="43" height="23" bgcolor="#99CC00"><b>No</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="83"><b>Tanggal</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="116"><b>Barcode</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="280"><b>Item Name</b></td>
        <td align="right" height="23" bgcolor="#99CC00" width="70"><b>Ukuran</b></td>
        <td align="center" height="23" bgcolor="#99CC00"><b>Qty</b></td>
        <td align="center" height="23" bgcolor="#99CC00"><b>Satuan</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="129"><b>Price</b></td>
        <td align="center" height="23" bgcolor="#99CC00" width="93"><b>Subtotal</b></td>
        <td align="center" bgcolor="#99CC00" width="93"><strong>Target Penjualan</strong></td>
        <td align="center" bgcolor="#99CC00" width="93"><strong>Row Material perpieces(yard / kilo)</strong></td>
        <td align="center" bgcolor="#99CC00" width="93">Satuan</td> 
    </tr>
    <tr>
        <td align="center" width="43" bgcolor="#FFFFCC" height="22">1</td>
        <td align="center" bgcolor="#FFFFCC" height="22" width="83">&nbsp;2010-12-20</td>
        <td align="left" bgcolor="#FFFFCC" height="22" width="116">&nbsp;BAI010590999000
        </td>
        <td align="left" bgcolor="#FFFFCC" height="22" width="280">&nbsp;Over 
        All Auli Stp XS</td>
        <td align="center" bgcolor="#FFFFCC" height="22" width="70">XL</td>
        <td align="center" bgcolor="#FFFFCC" height="22">5</td>
        <td align="center" bgcolor="#FFFFCC" height="22">PCS</td>
        <td align="center" bgcolor="#FFFFCC" height="22" width="129">boleh kosong</td>
        <td align="right" bgcolor="#FFFFCC" height="22" width="93">boleh kosong</td>
        <td align="right" bgcolor="#FFFFCC" width="93">400000</td>
        <td align="right" bgcolor="#FFFFCC" width="93">20 </td>
        <td  bgcolor="#FFFFCC" width="93" >KG</td>
    </tr>
    <tr>
        <td align="center" width="43" bgcolor="#E0FF9F">2</td>
        <td align="center" bgcolor="#E0FF9F" width="83">&nbsp;2010-12-20</td>
        <td align="left" bgcolor="#E0FF9F" width="116">&nbsp;CAA020100997602</td>
        <td align="left" bgcolor="#E0FF9F" width="280">&nbsp;Kaxit 
        Tanggung</td>
        <td align="center" bgcolor="#E0FF9F" width="70">L</td>
        <td align="center" bgcolor="#E0FF9F">6</td>
        <td align="center" bgcolor="#E0FF9F">PCS</td>
        <td align="center" bgcolor="#E0FF9F" width="129">boleh kosong</td>
        <td align="right" bgcolor="#E0FF9F" width="93">boleh kosong</td>
        <td align="right" bgcolor="#E0FF9F" width="93" >400000</td>
        <td align="right" bgcolor="#E0FF9F" width="93" >50</td>
        <td  bgcolor="#E0FF9F" width="93" >YARD</td>
    </tr>
</table>
</br></br>
<table width="400" border="1" bgcolor="#FFFFFF">
  <tr>
    <td width="93">Tujuan PO</td>
    <td width="8">:</td>
    <td width="282"><select name="tujuan" size="1" id="select">
      <?php  
	            $sql="SELECT SQL_CACHE LOWER(IFNULL(nama_po,nama)) as nama_po,nama FROM gudang_distribusi WHERE jenis=1";
				   $res_gdg=mysql_query($sql);
				
				    $arrayJenis=array();
				   while(list($id,$nama)=mysql_fetch_array($res_gdg)){
					   $arrayJenis[$id]=$nama;
				   }
				  // $arrayJenis=array('suho'=>'Suho','supplier'=>'Supplier' );
				   foreach($arrayJenis as $key=>$value){
					   echo "<option value='$key'>$value</option>";
				   }
				
				?>
    </select></td>
  </tr>
  <tr>
    <td>File</td> 
    <td>:</td>
    <td><input type="file" name="userfile" size="20" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" value="upload" name="upload" /></td>
  </tr>
</table>
<br>
</form>
<br>
<br>
<?php include('footer.php');?>

