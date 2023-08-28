<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Control Stok Opname Gudang Distribusi-Markas-Reshare "; ?>
<?php $lihat=1;
  error_reporting(1);
  /*
  v5 rekap ke seluruh jumlah stok awal pertgl bukan hanya yang dikoreksi saja tapi dengan yang belum dikoreksi
  v6 tambah kode dan nama tgl 02112016
  v7 tambah pilihan positif + negatif 28102020
  v8 tambah pernyaatann SO
  v9 togle hpp
  v10 stok awal ditambah khusus reshare dulu
  */
 if($lihat==1)
 { 
    include('header.php');
 } 
 
 $tgl1=$_POST['tgl1'];
 $jenis_gudang=$_POST['jenis_gudang'];
 if(empty($tgl1)){
	 $tgl1=date('Y-m-d');
 }
 
 
 #check privileges
 $sql="SELECT is_akses_stok_outlet,is_akses_stok_markas,is_akses_stok_distribusi FROM user_account_privileges_parameter WHERE username='$username';";
 $res=mysql_query($sql);
  if(!$res){
     if($username=='budi-it'){
	    echo "<h3>Error ".mysql_error()."</h3>";  
	 }	  
  }
  $is_akses_stok_outlet=0;
  $is_akses_stok_markas=0;
  $is_akses_stok_distribusi=0;
  
  list($is_akses_stok_outlet,$is_akses_stok_markas,$is_akses_stok_distribusi)=mysql_fetch_array($res);
 
 
 $arrayPilihan=array('all'=>'--All--','formReshare'=>'Outlet','formMarkas'=>'Markas','formDistribusi'=>'Distribusi');
 $arrayJenisSelisih=array('all'=>'--All--','+'=>'Positif','-'=>'Negatif');
 
 //cek apakah ini menggunakan paging
if(isset($_REQUEST['action'])){
	 $tgl1=$_POST['tgl1'];
     $tipe=$_POST['tipe'];
     $_SESSION['tgl1']=$tgl1;
     $_SESSION['tipe']=$tipe;
	 
	 $barcode=$_POST['barcode'];
	 $_SESSION['barcode']=$barcode;
	 $txt_nama=$_POST['txt_nama'];
	 $_SESSION['txt_nama']=$txt_nama;
	 
	 $jenis_selisih=$_POST['jenis_selisih'];
	 $_SESSION['jenis_selisih']=$jenis_selisih;
	
 }
 
 
 $cbMitra=$_POST['cbMitra'];
 
 if($cbMitra==1){
    $jns=2;	 
 }else{
	$jns=1; 
 }
 

  if($username=='budi-it'){
     $isCanEdit=1;	  
  }else{
     $isCanEdit=0;		  
  }
 //echo "Mitra $cbMitra";
 
 
 ?>  
 <script language="JavaScript" src="format.20110630-1100.min.js"></script>
 <script language="javascript" src="app_libs/rekap_stok_opname_v11.js?d=<?php echo date('YmdHis')?>"></script>

 <script src="jquery.iframe-post-form.js"></script>
 <script language="JavaScript" src="calendar_us.js"></script>
 <link rel="stylesheet" href="calendar.css">
 <style>
 .head,.head_hpp,.head_dt{
 background-image:url(images/footer.gif);
 text-align:center;
 }
 .body, .body_hpp{
 text-align:right;
 }
 .myLink{
   cursor:pointer,
   color:#03F
   	 
  }
  .hilang{
	 margin-left:5px;
	
	 <?php
	 if($isCanEdit==0){
	   echo  "display:none;";
	  }
	 
	 ?>
	 
  } 
  

  
 </style>

 <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>?action=search" name="f_rekap" >
 <table width="600" border="1">
  <tr>
    <td width="178"><strong>Type</strong></td>
    <td width="9">:</td>
    <td width="391"><select  name="tipe" id="tipe" >
                    <?php 
					   foreach($arrayPilihan as $id=>$key){
						   if($id==$tipe){
							   echo "<option value='$id' selected='selected'>$key</option>";
						   }else{
							   echo "<option value='$id'>$key</option>";
						   }
						    
					   }
					
					?>
      </select></td>
  </tr>
  <tr>
    <td><strong>Tanggal</strong></td>
    <td>:</td>
    <td><input type="text" name="tgl1" id="tgl1" value="<?php  echo $tgl1?>" size="10">
                    <script>
                    new tcal ({
                        'controlname': 'tgl1'
                     });
			        </script>&nbsp;
			&nbsp;</td>
  </tr>
  <tr>
    <td><strong>Barcode</strong></td>
    <td>:</td>
    <td><input type="text" name="barcode" id="barcode" value="<?php  echo $barcode ?>" /></td>
  </tr>
  <tr>
    <td><strong>Nama</strong></td>
    <td>:</td>
    <td><input type="text" name="txt_nama" id="txt_nama" value="<?php  echo $txt_nama; ?>" /></td>
  </tr>
  <tr>
    <td><strong>Jenis Selisih</strong></td>
    <td>:</td>
    <td><select  name="jenis_selisih" id="jenis_selisih" >
      <?php 
					   foreach($arrayJenisSelisih as $id=>$key){
						   if($id==$jenis_selisih){
							   echo "<option value='$id' selected='selected'>$key</option>";
						   }else{
							   echo "<option value='$id'>$key</option>";
						   }
						    
					   }
					
					?>
      </select></td>
  </tr>
  
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td><input type="submit" value="Lihat" />&nbsp;
      <input type="checkbox" name="cbMitra" id="checkbox"  value="1"   <?php  if($jns==2){ echo 'checked="checked"';} ?>/>
      Mitra</td>
  </tr>
</table>
</form>


<?php 
//paging
 //echo "Tanggal $tgl1";
 if($_GET['action']!='search'){
	 include_once('footer.php');
	 die();
 }
   
 
 $isShowHpp=0;
  $sql_show_hpp="select count(*) from user_account_showhpp where username='$username' and aktif=1";
  $res=mysql_query($sql_show_hpp);
  if(!$res){
     if($username=='budi-it'){
	    echo "<h3>Error ".mysql_error()."</h3>";  
	 }	  
  }
  list($ada)=mysql_fetch_array($res);
  if($ada>0){
	  $isShowHpp=1;
  }
	
?>
 <form name="export" method="post"  action="produk_list_export.php" onsubmit='$("#datatodisplay").val( $("<div>").append( $("#ReportTable").eq(0).clone() ).html() )'><input name="Submit" type="submit"  value="Export Data">
               <input type="hidden" id="datatodisplay" name="datatodisplay">  
            </form>

<?php
if($isShowHpp){
?>
 <input type="checkbox" name="cb_hpp" id="cb_hpp" /> HPP
<?php	
}
?>
 
  
 
 <div id="formReshare">
 <h2>Rekap stock Opname <?php echo $data_global['outlet_name'];?></h2>
 <span id="procReshare"></span>
 <span id="procReshareKoreksi"></span>
 <span onclick="reFreshReshare()">Refresh <?php echo $data_global['outlet_name'];?></span>
 <fieldset id="upload_design" style="display:none;">
        <legend>Upload File Pernyataan</legend>
        <form action="upload_bukti_so.php" method="post" enctype="multipart/form-data" name="form-upload" id="form-upload">
          <input type="text" name="tgl" id="v_tgl" value="" />
          <input type="text" name="gudang" id="v_gudang" value="" />
            <input type="text" name="jenis" id="v_jenis_gudang" value="" />
            <input type="file" name="fupload" id="fupload" />  <input type="button" id="tutup" value="Tutup" /> 
            <p id="up-result"></p>
        </form>
   </fieldset>
 <table border="1" width="100%" style="font-size: 8pt; width:auto;" cellpadding="0" cellspacing="0" id="ReportTable" class="sortable">
  <tr style="height:20">
    <td rowspan="2" class="head">No</td>
    <td rowspan="2" class="head">&nbsp;Kode <?php echo $data_global['outlet_name'];?></td>
    <td rowspan="2" class="head">&nbsp;Nama <?php echo $data_global['outlet_name'];?></td>
    <td colspan="3" class="head_dt">&nbsp;Data Stock Awal per <?php echo $tgl1;?> </td>
    <td colspan="3" class="head_dt">&nbsp;Data Stock Awal Yang Telah Di SO</td>
    <td colspan="3" class="head_dt">&nbsp;Stock Opname</td>
    <td colspan="3" class="head_dt">&nbsp;Selisih Plus/minus</td>
    <td rowspan="2" class="head">Pernyataan</td>
    <td rowspan="2" class="head">Update date</td>
   
  </tr>
	   
  <tr style="height:20">
    <td class="head">Qty</td>
    <td class="head_hpp">HPP</td>
    <td class="head">HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    
  </tr>
<?php
   $jenis="Reshare";
   
   
   $sql="SELECT upper(kode_outlet) id,file_nama FROM koreksi_outlet_stok_bukti WHERE tanggal LIKE '$tgl1%'";
   $res=mysql_query($sql);//
   
   $arrayFile=array();
   while(list($kode,$file)=mysql_fetch_array($res)){
	   $arrayFile[$kode]=$file;
   }
   /*
   if($username=='budi-it'){
	  echo $sql."<br/>"; 
	  print_r($arrayFile);  
	}
	*/
	
	 $kodeoutlet=$_SESSION['outlet'];
     $group=substr($kodeoutlet,5,5);
	  if($group=='O0000' ||$group=='o0000'){
		  $area=substr($kodeoutlet,0,4);
	  }else{
		  $area=$kodeoutlet;  
	  }
	 
	  $data_tambahan=" o.id LIKE '$area%' ";
	  if(!empty($area_lain)){
		  $d=explode(';',$area_lain); 
		  $d_lain='';
		  foreach($d as $kd_lain){
			  $kd_lain=trim($kd_lain);
				if(!empty($kd_lain)){
					$d_lain.=" OR o.id LIKE '$kd_lain%' ";
				}
		  }
		   $data_tambahan.=$d_lain;
	  } 	  
	  $sql_tambahan=" AND ( $data_tambahan ) ";
	  if(!empty($prefix)){
		  $sql_tambahan.=" AND  o.id LIKE '$prefix%'  ";
	  }
	
   $sql="SELECT SQL_CACHE trim(id),trim(nama) from outlet o where o.type='4' and o.jenis='$jns' and o.is_show_omset=1 and o.id not like 'MS%'  $sql_tambahan order by o.id ASC";
   $res=mysql_query($sql) or die ("wrong syntax query");
	//echo("\n $query");
    $totalQtyAwal=0;
	$totalHppAwal=0;
	$totalHpjAwal=0;
	$totalQtySo=0;
	$totalHppSo=0;
	$totalHpjSo=0;
	$totalQtyKoreksi=0;
	$totalHppKoreksi=0;
	$totalHpjKoreksi=0;
	
 	while(list($kode_outlet,$nama,$qty_awal,$hpp_awal,$hpj_awal,$qty_so,$hpp_so,$hpj_so,
	            $qty_koreksi,$hpp_koreksi,$hpj_koreksi,$update_date)=mysql_fetch_array($res)){				    $hpp_awal=number_format($hpp_awal,2,'.',',');
    
    if($isShowHpp==1){//asal
	    	
	}else{
	    $hpp_koreksi=0;
		$hpp_so=0;
		$hpp_awal=0; 	
	}
	
	$totalQtyAwal+=$qty_awal;
	$totalHppAwal+=$hpp_awal;
	$totalHpjAwal+=$hpj_awal;
	$totalQtySo+=$qty_so;
	$totalHppSo+=$hpp_so;
	$totalHpjSo+=$hpj_so;
	$totalQtyKoreksi+=$qty_koreksi;
	$totalHppKoreksi+=$hpp_koreksi;
	$totalHpjKoreksi+=$hpj_koreksi;

   $no++;
   $file_view='<span id="pr_'.$kode_outlet.'"></span>';
   if(array_key_exists($kode_outlet,$arrayFile)){
	  $file_view='<span id="pr_'.$kode_outlet.'"><a href="bukti_koreksi/'. $arrayFile[$kode_outlet] .'" target="_blank"><img src="images/view-details.png" width="20" height="20"/></a></span>';
   }
?>
  		
  <tr style="height:20" id="r_<?php echo $kode_outlet;?>" class="row">
     <td><?php echo $no; ?></td>
    <td>&nbsp;<?php echo $kode_outlet;?></td>
    <td>&nbsp;<?php echo $nama;?></td>
    <td class="body" bgcolor="#9DB3D9" id="qtyrawal_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#9DB3D9" id="hpprawal_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="hpjrawal_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="qtyawal_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#9DB3D9" id="hppawal_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="hpjawal_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#FFCC99" id="qtyso_<?php echo $kode_outlet; ?>" ondblclick="showDetailSO('<?php echo $kode_outlet; ?>');" style="cursor:pointer;">0</td>
    <td class="body_hpp" bgcolor="#FFCC99" id="hppso_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#FFCC99" id="hpjso_<?php echo $kode_outlet; ?>">0</td>
    <td bgcolor="#E06A67" id="qtykoreksi_<?php echo $kode_outlet; ?>" class="qtyKoreksiReshare" align="right">0</td>
    <td bgcolor="#E06A67" id="hppkoreksi_<?php echo $kode_outlet; ?>" class="hppKoreksiReshare body_hpp" align="right">0</td>
    <td bgcolor="#E06A67" id="hpjkoreksi_<?php echo $kode_outlet; ?>" class="hpjKoreksiReshare" align="right">0</td>
    <td  align="center" id="pernyataan_<?php echo $kode_outlet; ?>"><?php echo $file_view;?>&nbsp; &nbsp; <span class="hilang" id='up_<?php echo $kode_outlet;?>' onclick="uploadPernyataan('<?php echo $kode_outlet; ?>','<?php echo $tgl1; ?>','outlet');"> <img src="images/b_edit.png" width="20" height="20"/></span></td>
    <td class="body" id="updatedate_<?php echo $kode_outlet; ?>"><?php echo $update_date;?>&nbsp;</td>
  </tr>
 
 <?php } ?> 
  <tr style="height:20">
    <td bgcolor="#00FF66">&nbsp;</td>
    <td bgcolor="#00FF66">&nbsp;</td>
    <td bgcolor="#00FF66">Total</td>
    <td class="body" bgcolor="#00FF66" id="totalRQtyAwalReshare">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalRHppAwalReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalRHpjAwalReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalQtyAwalReshare">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppAwalReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjAwalReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalQtySoReshare">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppSoReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjSoReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalQtyKoreksiReshare">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppKoreksiReshare">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjKoreksiReshare">0</td>
    <td bgcolor="#00FF66" class="body">&nbsp;</td>
    <td bgcolor="#00FF66" class="body">&nbsp;</td>
  </tr>
</table>
</div>
<p>&nbsp;</p>

<div id="formMarkas">
<h2>Rekap stock Opname Markas</h2>

<span id="procMarkas"></span>
 <span id="procMarkasKoreksi"></span>
 <span onclick="reFreshMarkas()">Refresh Markas</span>
<table border="1" width="100%" style="font-size: 8pt; width:auto;" cellpadding="0" cellspacing="0" id="ReportTableMarkas">
  <tr style="height:20">
    <td rowspan="2" class="head">No</td>
    <td rowspan="2" class="head">&nbsp;Kode Markas</td>
    <td rowspan="2" class="head">&nbsp;Nama Markas</td>
    <td colspan="3" class="head_dt">Data Stock Awal per <?php echo $tgl1;?></td>
    <td colspan="3" class="head_dt">Data Stock Awal Yang Telah Di SO</td>
    <td colspan="3" class="head_dt">&nbsp;Stock Opname</td>
    <td colspan="3" class="head_dt">&nbsp;Selisih Plus/minus</td>
    <td rowspan="2" class="head">Pernyataan</td>
    <td rowspan="2" class="head">Update date</td>
   
  </tr>
	   
  <tr style="height:20">
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    
  </tr>
<?php
   $jenis="Markas";
   
    $sql="SELECT upper(kode_markas) id,file_nama FROM koreksi_markas_stok_bukti WHERE tanggal LIKE '$tgl1%'";
   $res=mysql_query($sql);//
   
   $arrayFile=array();
   while(list($kode,$file)=mysql_fetch_array($res)){
	   $arrayFile[$kode]=$file;
   }
   
   
   $query="SELECT SQL_CACHE trim(id),trim(nama) from outlet where type='1' and jenis='$jns' and is_show_omset=1 order by id ASC"; 
	
	// untuk pagging hitung jumlah data di tabel t_postingan
   $qpage="SELECT FOUND_ROWS() as jumData;";	 
   $res=mysql_query($query) or die ("wrong syntax query");
	//echo("\n $qpage");
    $totalQtyAwal=0;
	$totalHppAwal=0;
	$totalHpjAwal=0;
	$totalQtySo=0;
	$totalHppSo=0;
	$totalHpjSo=0;
	$totalQtyKoreksi=0;
	$totalHppKoreksi=0;
	$totalHpjKoreksi=0;
	
 	while(list($kode_outlet,$nama,$qty_awal,$hpp_awal,$hpj_awal,$qty_so,$hpp_so,$hpj_so,
	            $qty_koreksi,$hpp_koreksi,$hpj_koreksi,$update_date)=mysql_fetch_array($res)){				    $hpp_awal=number_format($hpp_awal,2,'.',',');
    
	if($isShowHpp==1){
		
	}else{
	    $hpp_koreksi=0;
		$hpp_so=0;
		$hpp_awal=0; 	
	}
	
	$totalQtyAwal+=$qty_awal;
	$totalHppAwal+=$hpp_awal;
	$totalHpjAwal+=$hpj_awal;
	$totalQtySo+=$qty_so;
	$totalHppSo+=$hpp_so;
	$totalHpjSo+=$hpj_so;
	$totalQtyKoreksi+=$qty_koreksi;
	$totalHppKoreksi+=$hpp_koreksi;
	$totalHpjKoreksi+=$hpj_koreksi;

   $no++;
   $file_view='<span id="pr_'.$kode_outlet.'"></span>';
   if(array_key_exists($kode_outlet,$arrayFile)){
	  $file_view='<span id="pr_'.$kode_outlet.'"><a href="bukti_koreksi/'. $arrayFile[$kode_outlet] .'" target="_blank"><img src="images/eye-open.png" width="20" height="20"/></a></span>';
   }
?>
  		
  <tr style="height:20"  id="r_<?php echo $kode_outlet;?>" class="row">
     <td><?php echo $no; ?></td>
    <td>&nbsp;<?php echo $kode_outlet;?></td>
    <td>&nbsp;<?php echo $nama;?></td>

    <td class="body" bgcolor="#9DB3D9" id="qtyrawalmarkas_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#9DB3D9" id="hpprawalmarkas_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="hpjrawalmarkas_<?php echo $kode_outlet; ?>">0</td>

    <td class="body" bgcolor="#9DB3D9" id="qtyawalmarkas_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#9DB3D9" id="hppawalmarkas_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="hpjawalmarkas_<?php echo $kode_outlet; ?>">0</td>
    
    <td class="body" bgcolor="#FFCC99" id="qtysomarkas_<?php echo $kode_outlet; ?>" style="cursor:pointer;">0</td>
    <td class="body_hpp" bgcolor="#FFCC99" id="hppsomarkas_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#FFCC99" id="hpjsomarkas_<?php echo $kode_outlet; ?>">0</td>
    
    <td bgcolor="#E06A67" id="qtykoreksimarkas_<?php echo $kode_outlet; ?>" class="qtyKoreksiMarkas" align="right">0</td>
    <td bgcolor="#E06A67" id="hppkoreksimarkas_<?php echo $kode_outlet; ?>" class="hppKoreksiMarkas body_hpp" align="right">0</td>
    <td bgcolor="#E06A67" id="hpjkoreksimarkas_<?php echo $kode_outlet; ?>" class="hpjKoreksiMarkas" align="right">0</td>
    
    <td  align="center" id="updatedate_<?php echo $kode_outlet; ?>"><?php echo $file_view;?>&nbsp; |&nbsp; <span class="hilang" id='up_<?php echo $kode_outlet;?>2' onclick="uploadPernyataan('<?php echo $kode_outlet; ?>','<?php echo $tgl1; ?>','markas');"> <img src="images/b_edit.png" width="20" height="20"/></span></td>
    <td class="body" id="updatedate_<?php echo $kode_outlet; ?>"><?php echo $update_date;?>&nbsp;</td>
  </tr>
 
 <?php } ?> 
  <tr style="height:20">
    <td bgcolor="#00FF66">&nbsp;</td>
    <td bgcolor="#00FF66">&nbsp;</td>
    <td bgcolor="#00FF66">Total</td>
    <td class="body" bgcolor="#00FF66" id="totalRQtyAwalMarkas">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalRHppAwalMarkas">0</td>
    <td class="body" bgcolor="#00FF66" id="totalRHpjAwalMarkas">0</td>

    <td class="body" bgcolor="#00FF66" id="totalQtyAwalMarkas">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppAwalMarkas">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjAwalMarkas">0</td>
    
    <td class="body" bgcolor="#00FF66" id="totalQtySoMarkas">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppSoMarkas">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjSoMarkas">0</td>
    
    <td class="body" bgcolor="#00FF66" id="totalQtyKoreksiMarkas">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppKoreksiMarkas">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjKoreksiMarkas">0</td>
    
    <td bgcolor="#00FF66" class="body">&nbsp;</td>
    <td bgcolor="#00FF66" class="body">&nbsp;</td>
  </tr>
</table>
</div>
<p>&nbsp;</p>

<div id="formDistribusi">
<h2>Rekap stock Opname Distribusi<h2>
<span onclick="reFreshDistribusi()">Refresh Distribusi</span> 
<table border="1" width="100%" style="font-size: 8pt; width:auto;" cellpadding="0" cellspacing="0" id="ReportTableDistribusi">
  <tr style="height:20">
    <td rowspan="2" class="head">No</td>
    <td rowspan="2" class="head">&nbsp;Kode Distribusi</td>
    <td rowspan="2" class="head">&nbsp;Nama Distribusi</td>
    <td colspan="3" class="head_dt">Data Stock Awal per <?php echo $tgl1;?></td>
    <td colspan="3" class="head_dt">&nbsp;Data Stock Awal Yang Telah Di SO</td>
    <td colspan="3" class="head_dt">&nbsp;Stock Opname</td>
    <td colspan="3" class="head_dt">&nbsp;Selisih Plus/minus</td>
    <td rowspan="2" class="head">Pernyataan</td>
    <td rowspan="2" class="head">Update date</td>
   
  </tr>
	   
  <tr style="height:20">
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    <td class="head">&nbsp;Qty</td>
    <td class="head_hpp">&nbsp;HPP</td>
    <td class="head">&nbsp;HPJ</td>
    
  </tr>
<?php
    $jenis="Gudang Distribusi";
	 $sql="SELECT upper(kode_distribusi) id,file_nama FROM koreksi_distribusi_stok_bukti WHERE tanggal LIKE '$tgl1%'";
   $res=mysql_query($sql);//
   
   $arrayFile=array();
   while(list($kode,$file)=mysql_fetch_array($res)){
	   $arrayFile[$kode]=$file;
   }
	
	 $query="SELECT trim(id),trim(nama) from gudang_distribusi where  id not like '%S%' and jenis=1 order by id ASC"; 
		// untuk pagging hitung jumlah data di tabel t_postingan
		$qpage="SELECT FOUND_ROWS() as jumData;";  
		$res=mysql_query($query) or die ("wrong syntax query");
	//echo("\n $qpage");
    $totalQtyAwal=0;
	$totalHppAwal=0;
	$totalHpjAwal=0;
	$totalQtySo=0;
	$totalHppSo=0;
	$totalHpjSo=0;
	$totalQtyKoreksi=0;
	$totalHppKoreksi=0;
	$totalHpjKoreksi=0;
	
 	while(list($kode_outlet,$nama,$qty_awal,$hpp_awal,$hpj_awal,$qty_so,$hpp_so,$hpj_so,
	            $qty_koreksi,$hpp_koreksi,$hpj_koreksi,$update_date)=mysql_fetch_array($res)){				    $hpp_awal=number_format($hpp_awal,2,'.',',');
    
     if($isShowHpp==1){//asal
	    	
	}else{
	    $hpp_koreksi=0;
		$hpp_so=0;
		$hpp_awal=0; 	
	}
	
	$totalQtyAwal+=$qty_awal;
	$totalHppAwal+=$hpp_awal;
	$totalHpjAwal+=$hpj_awal;
	$totalQtySo+=$qty_so;
	$totalHppSo+=$hpp_so;
	$totalHpjSo+=$hpj_so;
	$totalQtyKoreksi+=$qty_koreksi;
	$totalHppKoreksi+=$hpp_koreksi;
	$totalHpjKoreksi+=$hpj_koreksi;

   $no++;
   $file_view='<span id="pr_'.$kode_outlet.'"></span>';
   if(array_key_exists($kode_outlet,$arrayFile)){
	  $file_view='<span id="pr_'.$kode_outlet.'"><a href="bukti_koreksi/'. $arrayFile[$kode_outlet] .'" target="_blank"><img src="images/eye-open.png" width="20" height="20"/></a></span>';
   }
?>
  		
   <tr style="height:20"  id="r_<?php echo $kode_outlet;?>" class="row">
     <td><?php echo $no; ?></td>
    <td>&nbsp;<?php echo $kode_outlet;?></td>
    <td>&nbsp;<?php echo $nama;?></td>

    <td class="body" bgcolor="#9DB3D9" id="qtyrawaldistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#9DB3D9" id="hpprawaldistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="hpjrawaldistribusi_<?php echo $kode_outlet; ?>">0</td>
    
    <td class="body" bgcolor="#9DB3D9" id="qtyawaldistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#9DB3D9" id="hppawaldistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#9DB3D9" id="hpjawaldistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#FFCC99" id="qtysodistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body_hpp" bgcolor="#FFCC99" id="hppsodistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td class="body" bgcolor="#FFCC99" id="hpjsodistribusi_<?php echo $kode_outlet; ?>">0</td>
    <td bgcolor="#E06A67" id="qtykoreksidistribusi_<?php echo $kode_outlet; ?>" class="qtyKoreksiDistribusi" align="right">0</td>
    <td bgcolor="#E06A67" id="hppkoreksidistribusi_<?php echo $kode_outlet; ?>" class="hppKoreksiDistribusi body_hpp" align="right">0</td>
    <td bgcolor="#E06A67" id="hpjkoreksidistribusi_<?php echo $kode_outlet; ?>" class="hpjKoreksiDistribusi" align="right">0</td>
    <td  align="center" id="updatedate_<?php echo $kode_outlet; ?>"><?php echo $file_view;?>&nbsp; |&nbsp; <span class="hilang" id='up_<?php echo $kode_outlet;?>3' onclick="uploadPernyataan('<?php echo $kode_outlet; ?>','<?php echo $tgl1; ?>','distribusi');"> <img src="images/b_edit.png" width="20" height="20"/></span></td>
    <td class="body" id="updatedate_<?php echo $kode_outlet; ?>"><?php echo $update_date;?>&nbsp;</td>
  </tr>
 
 <?php } ?> 
  <tr style="height:20">
    <td bgcolor="#00FF66">&nbsp;</td>
    <td bgcolor="#00FF66">&nbsp;</td>
    <td bgcolor="#00FF66">Total</td>

    <td class="body" bgcolor="#00FF66" id="totalRQtyAwalDistribusi">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalRHppAwalDistribusi">0</td>
    <td class="body" bgcolor="#00FF66" id="totalRHpjAwalDistribusi">0</td>
    
    <td class="body" bgcolor="#00FF66" id="totalQtyAwalDistribusi">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppAwalDistribusi">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjAwalDistribusi">0</td>
    
    <td class="body" bgcolor="#00FF66" id="totalQtySoDistribusi">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppSoDistribusi">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjSoDistribusi">0</td>
    
    <td class="body" bgcolor="#00FF66" id="totalQtyKoreksiDistribusi">0</td>
    <td class="body_hpp" bgcolor="#00FF66" id="totalHppKoreksiDistribusi">0</td>
    <td class="body" bgcolor="#00FF66" id="totalHpjKoreksiDistribusi">0</td>
    
    <td bgcolor="#00FF66" class="body">&nbsp;</td>
    <td bgcolor="#00FF66" class="body">&nbsp;</td>
  </tr>
</table>
</div>
 <script>
 $(document).ready(function(){
	// $('#formMarkas').hide();
    // $('#formDistribusi').hide();

 
 <?php
    # if($username=='budi-it'||$username=='mngr-it'||$username=='faipusat_yati'){
	?>
    $('#formMarkas').show();
    $('#formDistribusi').show();
	<?php	 
	#}
	
	if($isShowHpp==1){
		
	}else{
		echo "$('.body_hpp').hide();";
		echo "$('.head_hpp').hide();";
		echo "$('.hppKoreksiReshare').hide();";
		echo "$('.head_dt').attr('colspan','2');";
	}
	?>
	
	
 });
 
 function showDetailSO(outlet){
	 
	// alert(outlet + $('#tgl1').val());
	 $('#tglDetail').val($('#tgl1').val());
	 $('#outletDetail').val(outlet);
	 $('#frmDetail').attr('action','laporan_stok_opname_pereshare_pertanggal_v2.php?action=searchxjgsx');
	 // return;
	 $('#frmDetail').submit();
 }
 
try{
	
  var barcode='<?php  echo $barcode;?>';
  var nama='<?php  echo $txt_nama;?>';	
  var jenis_selisih='<?php echo $jenis_selisih;?>';
  
  
  
   <?php
    # if($username=='budi-it'||$username=='mngr-it'||$username=='faipusat_yati'){
   $script='';	
   $showTable='';	
   if ($tipe=='formReshare'){	  
  	   $script.='getDataAwalReshare();getDataSoOutlet();getRekapDataAwalReshare();';
  	   $showTable.="$('#formReshare').show();";
   }  else if($tipe=='formMarkas'){
    	  if ($is_akses_stok_markas==1){
    	     $script.='getDataAwalMarkas();getDataSoMarkas();getRekapDataAwalMarkas();';
    	     $showTable.="$('#formMarkas').show();";
    	  }
   }  else if($tipe=='formDistribusi'){
  	   if($is_akses_stok_distribusi==1){
  			   $script.='getDataAwalDistribusi();getDataSoDistribusi();getRekapDataAwalDistribusi();';
  	       $showTable.="$('#formDistribusi').show();";
  		 }
	  
   } else{
	  
	  $script.='getDataAwalReshare();getDataSoOutlet();getRekapDataAwalReshare();';  
	  $showTable.="$('#formReshare').show();";
	 if($is_akses_stok_markas==1){
	    $script.='getDataAwalMarkas();getDataSoMarkas();getRekapDataAwalMarkas();';
	    $showTable.="$('#formMarkas').show();";
	  }
	  if($is_akses_stok_distribusi==1){
			 $script.='getDataAwalDistribusi();getDataSoDistribusi();getRekapDataAwalDistribusi();';
	         $showTable.="$('#formDistribusi').show();";
		}
	  
   }		
	  
   ?>
   $(document).ready(function(e) {
      $('#formReshare').hide();
	  $('#formMarkas').hide();
	  $('#formDistribusi').hide();
	  $('.row').hide();
	  <?php 
	  echo $script; 
	  echo $showTable;
	  ?>
 });
   
   
}catch(e){
  //alert(e.message);	
  $('#formReshare').text(e.message);
}

</script>
<form id="frmDetail" name="frmDetail" method="post" action="" target="_blank" style="display:none;">
<input name="outlet"  id="outletDetail" type="text" />
<input type="hidden" name="txt_nama" id="txt_nama" value="<?php  echo $txt_nama; ?>" />
<input type="hidden" name="barcode" id="barcode" value="<?php  echo $barcode ?>" />
<input name="tgl1" id="tglDetail" type="text" />
</form>
<?php include_once "footer.php"; ?>