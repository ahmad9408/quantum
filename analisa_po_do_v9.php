<?php $content_title="Evaluasi PO DAN DO" ; ?>
<?php  
 /*
 tgl 2014-08-18 by budi
 last change rubah hanya po yang telah di approve saja yang akan muncul disini
 tgl 02082017 by budi
 link detail ganti ke versi2 berdsarkan app2
 
 */ 
 $lihat=1;
 if($lihat==1){ 
    include('header.php');
 }

 $thisPage=$_SERVER['PHP_SELF'];
 /*
  Versi 2 pengambilan data do berdasarkan data barcode di po
   versi 3 ditambah tangga
   versi 4 pengambilan data dengan ajax dan jquery
   versi 5 edit rian
   versi 6 tambah FPO
   ver 7 tambah berdasarkan MD
   ver 8 tambahan limit page
   v9 untuk paging tidak mengunakan session karena user tertentu tidak bisa menyimpan session
   
   edit last 21 jun 2014  (budi)
   tambah pencarian berdasarkan kode model 
 */

 if($_POST['ubah_gambar']){
	$kode_gambar=$_POST['kode_gambar'];
	$foto=$_FILES['foto']['name'];
	$format=strstr($foto,".");
	$gambar=$kode_gambar.$format;
	
	$sql="select nama_file from mst_model_foto where kode_model='$kode_gambar'";
	$query=mysql_query($sql)or die($sql);
	list($foto_sebelumnya)=mysql_fetch_array($query);
	
	$sql="SET autocommit = 0;";
	$query_trans=mysql_query($sql);
	
	$sql="START TRANSACTION;";
	$query_trans=mysql_query($sql);
	
	if($username=='budi-it'){
		echo ("delete file ym_it_rabbani/temp/$foto_sebelumnya");
	}
	unlink("ym_it_rabbani/temp/$foto_sebelumnya");
	
	
	move_uploaded_file($_FILES['foto']['tmp_name'],"ym_it_rabbani/temp/$gambar");
	
	if($username=='budi-it'){
		echo ("move to ym_it_rabbani/temp/$foto_sebelumnya");
	}
	
	$sql="update mst_model_foto set nama_file='$gambar' where kode_model='$kode_gambar'";
	
	if($username=='budi-it'){
		echo ("$sql");
	}
	
	$query=mysql_query($sql)or die($sql);
	
	if($username=='budi-it'){
		#die($sql);
	}
	$sql="COMMIT;";	
	$query_trans=mysql_query($sql);
	
}
    
	
	function jumlahHari($month,$year) {
	   return date("j", strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00'))));
	}
	
	function dateMysql($number){
	   if($number<10){
		  return '0'.$number; 
	   }else{
		  return $number;   
	   }
	  
	}
	
	
	function createMonthRangeArray($strDateFrom,$strDateTo) {
  
   
      $aryRange=array();

       $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
       $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

       if ($iDateTo>=$iDateFrom) {
         array_push($aryRange,date('Y-m-01',$iDateFrom)); // first entry
         $month_before=trim(date('Y-m-01',$iDateFrom));
         while ($iDateFrom<$iDateTo) {
           $iDateFrom+=86400; // add 24 hours
		   if($month_before!=trim(date('Y-m-01',$iDateFrom))){
			   array_push($aryRange,date('Y-m-01',$iDateFrom));
			   $month_before=trim(date('Y-m-01',$iDateFrom));
		   }
           
         }
       }
       return $aryRange;
   }
   
   $array_bulan = array('01'=>'Januari','02'=>'Februari','03'=>'Maret', '04'=>'April', '05'=>'Mei',
 	'06'=> 'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober', '11'=>'November','12'=>'Desember');
	$tahun_skrg=date('Y');
	$bulan_skrg=date('m');
	
	
	
   if (isset($_GET['hal'])) {
	   #echo "TEST HERE";
	   $tambah="action=search";
		   $tgl1=$_POST['tgl1'];
		 $tgl2=$_POST['tgl2'];		  
		  $bulan1=$_POST['bulan1'];
		  $tahun1=$_POST['tahunl'];
		  $tujuan=$_POST['tujuan'];
		  $txt_cari=trim($_POST['txt_cari']);
		  $barcode_model=trim($_POST['barcode_model']);
		  $txt_md_mngr=$_POST['txt_md_mngr'];
		  $txt_limit=$_POST['txt_limit'];
			       
	}elseif(isset($_GET['action'])) { 
	
		$tambah="action=search";
       
		 $tgl1=$_POST['tgl1'];
		 $tgl2=$_POST['tgl2'];		  
		  $bulan1=$_POST['bulan1'];
		  $tahun1=$_POST['tahunl'];
		  $tujuan=$_POST['tujuan'];
		   $txt_cari=trim($_POST['txt_cari']);
		  $barcode_model=trim($_POST['barcode_model']);
		  $txt_md_mngr=$_POST['txt_md_mngr'];
		  $txt_limit=$_POST['txt_limit'];
			
       
	} else {
		/*
		unset($_SESSION['tgl1']);
        unset($_SESSION['tgl2']);
		unset($_SESSION['bulan1']);
		unset($_SESSION['tahunl']);
		unset($_SESSION['tujuan']);
		*/
		$bulan1=$bulan_skrg;
	    $tahun1=$tahun_skrg;
		$tujuan='suho';//default
	}
	
  if(empty($tgl1)){
    $tgl1=date("Y-m-01");
	$h=jumlahHari(date('m'),date('Y'));		
    $tgl2=date("Y-m-").$h;
        
  } 
  
  if(empty($txt_limit)){
	  $txt_limit=100;
  }

  $data_periode=split('-',$tgl1);
  $filter_periode=$data_periode[0].'-'.$data_periode[1];
	
    ?>
    <style>
    .launch{display:none;}
	.myLink{color:#03F;cursor:pointer}
	img {
		image-orientation: from-image;
	}
    </style>
    <link rel="stylesheet" type="text/css" href="selectbox.css" />
    
<script type="text/javascript" src="jquery.balloon.min.js"></script>
<script type="text/javascript" src="date.js"></script>
<script src="jquery.jeditable.js" type="text/javascript"></script>
<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="sortable.js"></script> 
<script language="JavaScript" src="calendar_us.js"></script>
<script type="text/javascript" src="jquery.selectbox-0.5.js"></script>
<!-- atasan -->
<script>
var tgl1='<?php echo $tgl1 ?>';
var tgl2='<?php echo $tgl2 ?>';
var model='<?php echo $model; ?>';
var barcode_model='<?php echo $barcode_model; ?>';
var tujuan='<?php echo $tujuan; ?>'; 
var format_tgl_javascript='yyyy-MM-dd';


</script>
<script src="app_libs/analisa_po_do_v9.js?d=<?php date('YmdHis');?>"></script>
<span class="hilang"></span>
<script>$(".hilang").hide();</script>
<link rel="stylesheet" href="calendar.css">

<?php if($_POST[simpan1]){
   
$foto=$_REQUEST['code_edit'];
$format=strstr($_FILES['foto']['name'],".");
$gambar=$foto."".$format;
$sql="SET autocommit = 0;";
$query_trans=mysql_query($sql);

$sql="START TRANSACTION;";
$query_trans=mysql_query($sql);


	$sql="INSERT INTO `mst_model_foto`
            (`kode_model`,
             `nama_file`,
             `status`)
VALUES ('$foto',
        '$gambar',
        '0') "; 
$res=mysql_query($sql);
	unlink("ym_it_rabbani/temp/$gambar");
	move_uploaded_file($_FILES['foto']['tmp_name'],"ym_it_rabbani/temp/$gambar")or die("gagal");

$sql="COMMIT;";	
$query_trans=mysql_query($sql);

}
?>
<fieldset> 
<form id="edit_gambar" method="post" action="<?php echo $thisPage; ?>?action=search" enctype="multipart/form-data">
<table>
<tr>
	<td width="150">Foto</td>
	<td width="2" align="center">:</td>
	<td><input type="file" name="foto" /><input type="hidden" name="kode_gambar" id="kode_gambar" /> <input type="submit" name="ubah_gambar" value="Ubah" /></td>
</tr>
</table>
<span style="display:none" >
<input name="bulan1" type="text" value="<?php echo $bulan1;?>" />
<input name="tahun1" type="text" value="<?php echo $tahun1;?>" />
<input name="tgl1" type="text" value="<?php echo $tgl1;?>" />
<input name="tgl2" type="text" value="<?php echo $tgl2;?>" />
<input name="txt_cari" type="text" value="<?php echo $txt_cari;?>" />
<input name="barcode_model" type="text" value="<?php echo $barcode_model;?>" />
<input name="tujuan" type="text" value="<?php echo $tujuan;?>" />

</span>
</form>

<form method="POST" action="<?php echo $PHP_SELF; ?>?action=search" name="outlet" id="frmMain">
  <!-- tengah -->
    <table id="zoom">
	<tr>
		<td id="zoom1"></td>
    </tr>
    </table>
  
	<table class="hide">
	<tr>
		<td id="gambar"></td>
	</tr>
  </table>
    
    
		<table class="otomatis">
			<tr>
			  <td width="117" valign="top">Bulan</td>
			  <td width="7" valign="top">:</td>
			  <td width="552" valign="top"><select name="bulan1" id="bulan1">
					  <?php 
						 foreach($array_bulan as $key => $value){
							 if($key==$bulan1){
								 echo  "<option value='$key' selected>$value</option>";
							 }else{
								 echo  "<option value='$key'>$value</option>";
							 }							
						 }
              
              ?>
            </select></td>
	      </tr>
			<tr>
			  <td valign="top">Tahun </td>
			  <td valign="top">:</td>
			  <td valign="top"><select name="tahunl" id="tahun1">
                      <?php 
						 $tahun=1996;
						 
						 for($i=1;$i<100;$i++){
							 $tahun++; 
							 if($tahun==$tahun1){
								 echo  "<option value='$tahun' selected>$tahun</option>";
							 }else{
								 echo  "<option value='$tahun'>$tahun</option>";
							 }
						 }
              		?>
              </select></td>
	      </tr>
			<tr>
				
              <td valign="top"> Periode Dari </td>
              <td valign="top">: </td>
              <td valign="top"><script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
            <!-- calendar attaches to existing form element -->
            <input type="text" name="tgl1" readonly id="tgl1" value="<?php echo $tgl1; ?>" size="16"/> &nbsp;
			 
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl1'
              });
            </script>
			 &nbsp;
			 &nbsp;&nbsp;&nbsp;&nbsp;
			 
			 <input type="text" name="tgl2" readonly id="tgl2" value="<?php echo $tgl2; ?>" size="16"/> &nbsp;
			
            <script language="JavaScript">
              new tcal ({                                                         
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl2'
              }); 
            </script></td>
              
			</tr>
			<tr>
			  <td valign="top">Nama Model              </td>
			  <td valign="top">&nbsp;</td>
			  <td valign="top"><input name="txt_cari" id="txt_cari" type="text" value="<?php echo $txt_cari?>" /></td>
	      </tr>
			<tr>
			  <td valign="top">Kode Model</td>
			  <td valign="top">:</td>
			  <td valign="top"><input name="barcode_model" id="barcode_model" type="text" value="<?php echo $barcode_model?>" /></td>
		  </tr>
			<tr>
			  <td valign="top">Dikirim Ke Gudang</td>
			  <td valign="top">:</td>
			  <td valign="top"><select name="tujuan" size="1" id="tujuan">
			    <?php  
				   $sql="SELECT SQL_CACHE LOWER(IFNULL(nama_po,nama)) as nama_po,nama FROM gudang_distribusi WHERE jenis=1";
				   $res_gdg=mysql_query($sql);
				  # $arrayJenis=array(''=>'ALL','suho'=>'Suho','supplier'=>'Supplier' );
				    $arrayJenis=array(''=>'ALL');
				   while(list($id,$nama)=mysql_fetch_array($res_gdg)){
					   $arrayJenis[$id]=$nama;
				   }
				   
				   
				   foreach($arrayJenis as $key=>$value){
					   if($key==$tujuan){
						    echo "<option value='$key' selected='selected'>$value</option>";
					   }else{
						    echo "<option value='$key'>$value</option>";
					   }
					   
				   }
				
				?>
		      </select></td>
		  </tr>
			<tr>
			  <td valign="top"><?php echo $title_global['md-produk']; ?></td>
			  <td valign="top">:</td>
			  <td valign="top"><select  name="txt_md_mngr" id="txt_md_mngr"  style="width:300px;" >
			    <option value="" >--select--</option>
			    <?php 
				$sql="SELECT SQL_CACHE id,nama FROM md_produk ORDER BY seq asc";
				$res=mysql_query($sql);
				while(list($id,$value)=mysql_fetch_array($res)){
                
                ?>
			    <option value="<?php   echo $id; ?>" <?php if($id==$txt_md_mngr){echo "selected";} ?>><?php echo "[ $id ]".$value; ?></option>
			    <?php
                } 
				 
                ?>
		      </select></td>
		  </tr>
			<tr>
			  <td valign="top">Jumlah Data Tampil</td>
			  <td valign="top">:</td>
			  <td valign="top"><input name="txt_limit" id="txt_limit" type="text" value="<?php echo $txt_limit?>" />
                <input name="txt_hal" id="txt_hal" type="hidden" value="0" /> </td>
		  </tr>
			<tr>
			  <td valign="top">&nbsp;</td>
			  <td valign="top">&nbsp;</td>
			  <td valign="top"><input type="submit" value="Cari"/></td>
		  </tr>
		</table>
</form>
</fieldset>
<?php
if($_GET['action']=='search'){
	
}else{
   include_once('footer.php');	
   die();	
}
if (isset($_GET['action'])) { include("progress_bar.php"); }
if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=$txt_limit;
	$page=$hal;
	if (isset($_GET['action'])) { 
	
	  $sql_tbhn='';
	  if(!empty($barcode_model)){
		  $sql_tbhn.=" AND pd.kd_produk like '$barcode_model%' ";
	  }
		
		if(!empty($txt_md_mngr)){
			 $sql_tbhn.=" AND i.wil_md='$txt_md_mngr'";
		}
       $sql2=" SELECT   SQL_CACHE SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`,sum(qty_prod)    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.model like '%$txt_cari%'  AND p.closeco IS NULL 
					and approve2=1 and p.request_ke like '$tujuan%' $sql_tbhn
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`
                     order by m.model ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
		$sql3="SELECT FOUND_ROWS()";
		
	}
	if($username=='budi-it'){
	   echo $sql2;	
	}
	

	
	
?>

    <span id="launch" style="color:#00F;cursor:pointer">#</span>
        <table border="0" width="100%" class="table_q table_q-striped table_q-hover sortable">
          <thead class="header_table_q">
          <tr> 
            <td align="center" width="40"><strong>NO</strong></td>
            <td align="center" width="634"><strong>Model</strong></td>
            <td align="center" width="69" class="launch"><strong>Launching</strong></td>
            <td align="center" width="67" class="sorttable_numeric"><strong> Upload PO</strong></td>
            <td align="center" width="75" class="sorttable_numeric"><strong>Batas Pengiriman</strong></td>
            <td align="center" width="60" height="22" class="sorttable_numeric"><b>Qty PO </b></td>
            <td align="center" width="60" class="sorttable_numeric" style="display:none;"><strong>Qty FPO</strong></td>
            <td align="center" width="60" height="22" class="sorttable_numeric"><b>Qty DO (App2 Gudang)</b></td>
            <td align="center" width="59" class="sorttable_numeric" id="headPersenDO"><strong>% DO</strong></td>
            <td align="center" width="63" class="sorttable_numeric"><strong>Awal DO (App1 Manufaktur)</strong></td>
            <td align="center" width="80" class="sorttable_numeric"><strong> Terakhir DO (App1 Manufaktur)</strong></td>
            <td align="center" width="80" class="sorttable_numeric"><strong> Keterangan</strong></td>
          </tr>
          </thead>
          <?php
	$hsltemp2=mysql_query($sql2);// or die ('<h1>Error #'.mysql_error()."#$sql2".'</h1>');
    $hsltmp12=mysql_query($sql3);// or die ($sql3);
	list($jmlData[0])=mysql_fetch_array($hsltmp12);
	$no=($hal*$jmlHal);
	$stok_persub=0;
	$co_persub=0;
	$pengiriman_persub=0;
	$distribusi_qty_persub=0;
	$markas_qty_persub=0;
	$script_otl='';
	$script_mark='';
	$script_dist='';
	$total_poawal=0;
	$total_target_jual=0;
	$script_tgl_upload_po='';
	$script_tgl_po='';
	$script_tgl_awal_do='';
	$script_tgl_akhir_do='';
	
	
	if($jmlData[0]<$jmlHal){
	   $jmlPage=1;	
	}else{
	   $jmlPage=2;//lebih dari 1	
	}
	
	
	#echo "<h3>$jmlData[0]</h3>";
	
	
	$persentasi=0;
    while ( list($model,$po,$kode_model,$kode_style,$kode_kelas,$kode_kategori,$kode_basic_item,$fpo)=mysql_fetch_array($hsltemp2)) {
        $no++;
		
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
		$km="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%";//kode model
		$id_mod="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model";
		
		$sql1="select nama_file from mst_model_foto where kode_model='$id_mod'";
        $resi=mysql_query($sql1);
		list($filename)=mysql_fetch_array($resi);
		$str_model='';
		if(empty($filename)){
                //penambahan
			$sub=substr("$id_mod",1,2);
			if ($sub=='SZ'){$str_model.="<blink><font color='red'>[$id_mod]</font></blink>$model";}else{$str_model.="[$id_mod]$model";} 
			$str_model.="<span align='right' id='$id_mod'><a href='#' onclick=\"tambahkan('$id_mod')\" >Tambah</a></span> ";
		} else{
			list($gambar)=  mysql_fetch_array($resi);
			$str_model.="<a href='#' onclick=\"lihat('$filename?d=".date('YmdHis')."','$id_mod');\">[$id_mod]$model</a>";
		}
		
		$sql="select SQL_CACHE date_format(tanggal_launching,'%Y-%m-%d') from mst_model_launching where kode_model='$id_mod';";
		$resri=mysql_query($sql)or die("Error");
		list($tanggal_launching)=  mysql_fetch_array($resri);
	   if($tanggal_launching=="0000-00-00"){$tanggal_launching='';}
		
         ?>
          <tr > 
              <td><?php echo $no; ?></td>
               <td><?php echo $str_model; ?></td>
                 <td align="right" id="tgllaunch_<?php echo $id_mod; ?>" class="launch"><?php echo $tanggal_launching;?>&nbsp;</td>
           <td align="right" id="tgluploadpo_<?php echo $id_mod; ?>">&nbsp;</td>
            <td align="right" id="tglpo_<?php echo $id_mod; ?>">-</td>
            <td height="20" align="right"><span id="po_<?php echo $id_mod; ?>" onclick="showPODetail('<?php  echo $id_mod; ?>')" class="myLink"><?php echo number_format($po);$po_persub+=$po; ?></span ></td>
            <td align="right" style="display:none;"><a href="po_do_detail_v2.php?t1=<?php echo $tgl1?>&t2=<?php echo $tgl2?>&m=<?php echo $id_mod?>&j=p&nm=<?php echo $model?>" target="_blank" ><span id="fpo_<?php echo $id_mod; ?>" ><?php echo number_format($fpo);$fpo_persub+=$fpo; ?></span></a></td>           
            <td height="20" align="right"><a href="po_do_detail_v2.php?t1=<?php echo $tgl1?>&t2=<?php echo $tgl2?>&m=<?php echo $id_mod?>&j=d&nm=<?php echo $model?>" target="_blank"><span id="do_<?php echo $id_mod; ?>">0</span></a></td>
            <td align="right" id="persen_<?php echo $id_mod; ?>"><?php echo number_format($do_persen,1,'.',',').' %';?></td>            
            <td align="center" id="taw_do_model_<?php echo $id_mod; ?>">-</td>
            <td align="center" id="tak_do_model_<?php echo $id_mod; ?>">-</td>
			<td>ket</td>
          </tr>
          <?php
	}
   
    ?>
         <tfoot  class="footer_table_q">
          <tr> 
            <td >&nbsp;</td>
            <td height="23"  align="center"><i><b>SUBTOTAL</b></i></td>
            <td  align="right" class="launch">&nbsp;</td>
             <td  align="right">&nbsp;</td>
            <td  align="right">&nbsp;</td>
            <td height="23"  align="right" id="po_persub"><?php echo number_format($po_persub,0,'.',','); ?></td>
            <td  align="right" style="display:none;"><?php echo number_format($fpo_persub,0,'.',','); ?></td>
            <td height="23"  align="right" id="total_pengiriman"><?php echo number_format($pengiriman_persub,2,'.',','); ?></td>
            <td  align="right" id="persen_sub"><?php
               $persentasi=($pengiriman_persub/$po_persub)*100;
			   echo number_format($persentasi,2,'.',',');
			?></td>
            <td  align="right">&nbsp;</td>
            <td  align="right">&nbsp;</td>
            <td  align="right">1</td>
           </tr>
          <tr> 
            <td>&nbsp;</td>
            <td height="25" align="center">&nbsp;</td>
            <td class="launch">&nbsp;</td>
             <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
            <td height="25" id="totalPO" align="right">0</td>
            <td id="totalFPO" align="right" style="display:none;">0</td>
            <td height="25" id="totalDO" align="right">0</td>
            <td id="totalPersen" align="right" >0</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>2</td>
           </tr>
          </tfoot>
        </table>

<span id="debug" style="display:none"></span>
<br />
<table width="200" border="1" id="myTable" style="display:none">
<thead>
  <tr>
    <td>kode</td>
    <td>Nama</td>
    <td>DO</td>
  </tr>
</thead>
<tbody>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </tbody>
  <tfoot>
  <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
  </tfoot>
</table>

<table style="margin-left:10px; margin-top:10px;" id="page">
<tr>
						<td class="text_standard">
							Page : 
							<span class="hal" onClick="gotoPage(0);">First</span>
							<?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
								if($hal<=0){ ?>
									<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="gotoPage(<?php echo $i; ?>);"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
									if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="gotoPage(<?php echo $i; ?>);"><?php echo ($i+1); ?></span>
  <?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="gotoPage(<?php echo $i; ?>);"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
							<span class="hal" onClick="gotoPage(<?php echo intval(($jmlData[0]/$jmlHal)); ?>);">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>						</td>
				</tr>
</table>

<!--span id="debug" style="display:none"></span-->
<script language="JavaScript"> 
    var jmlHalaman='0';
	var txt_md='<?php echo $txt_md_mngr;?>';
<?php
    if (isset($_GET['action'])) { 
   
?>

    jmlHalaman='<?php echo $jmlPage ?>';
  jmlProcess=14;
  getTglUpload();
  getTglPo();
  getTglAwalDo();
  getTglAkhirDo();
  console.log('getDo start up');
  getDo();
  getTotalPO();
  getTotalDO();
  <?php
	}
  
  ?>
  
  function showPODetail(barcode){
	  $('#txtbarcode').val(barcode);
	  $('#myform').submit();
  }
</script>

<form action="monitoring_po_detail_v2.php?action=search" method="post" name="myform" id="myform"  target="_blank">
<input type="text" name="txtbarcode" id="txtbarcode" value="<?php echo $kode_produk; ?>" size="25">
<input type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1?>" size="10">
<input type="text" name="tgl2" id="tgl2" value="<?php echo $tgl2?>" size="10">

</form>
<?php include_once "footer.php"; ?>