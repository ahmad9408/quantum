<?php session_start(); 
/*
Last edit tanggal 18 05 2015
pemisanhan file js dan view
tambah link  untuk lihat detail by budi

*/
	$content_title="Stok Permodel"; 
	include_once "header.php" ;
	$cari=$_REQUEST['cari'];
	$berdasar=$_POST['berdasar'];
	 
	$cek=$_POST['cek'];
	$dari=$_POST['dari'];
	$sampai=$_POST['sampai'];
	$txt_pilihan=$_POST['txt_pilihan'];
	 
	 if($dari==""){
		 	$dari=date("Y-")."01-01";
			$sampai=date("Y-m-d");
		 }
	 
	if($cari=="" && empty($txt_pilihan)){
		$cari="";
		$berdasar="kode";	
		$terusan=" where f.kode_model like '%'";
	}else{
		if($berdasar=="kode"){
			$terusan=" where f.kode_model like '$cari%'";	
		}else{ 
			$terusan=" where f.nama_model like '%$cari%'";
		}	
	}
	if($cek=="oke"){
		$terusan_l=" and l.tanggal_launching between '$dari' and '$sampai'";	
	}else {
		$terusan_l="  ";
	}
	
	//===========15042016
  $sql_priv="SELECT COUNT(*) FROM user_account_produk_pilihan WHERE username='$username';";
  $res_priv=mysql_query($sql_priv);
  $jml_priv=0;
  list($jml_priv)=mysql_fetch_array($res_priv);
  $arrayProdukPilihan=array();
  if($jml_priv>0){
	  $sql="SELECT SQL_CACHE pp.pilihan  FROM produk_pilihan_mst pp inner join 
	  user_account_produk_pilihan up on up.produk_pilihan=pp.pilihan WHERE pp.aktif=1;"; 
  }else{//kosong
	  $arrayProdukPilihan['']='---ALL---';
	  $sql="SELECT SQL_CACHE pilihan FROM produk_pilihan_mst WHERE aktif=1;";
  }
  
 
  $query=mysql_query($sql)or die($sql);
  
  while(list($list_pilihan)=mysql_fetch_array($query)){
		$arrayProdukPilihan[$list_pilihan]=$list_pilihan;
  }
	 ?>
     <style>
     .myLink{
	    color:#03F;cursor:pointer;text-align:center;
     }
     </style>
<script type="text/javascript" src="sortable.js"></script> 
<script type="text/javascript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="app_libs/stok_per_model_ng_v2.js"></script> 
 <script language="JavaScript" src="calendar_us.js"></script>
     
    	  <link rel="stylesheet" href="calendar.css">
    <form method="post" action="stok_per_model_ng_v2.php" id="f1" name="f1">
    <table border="0" class="table_gambar">
    <tr>
    	<td id="tdfoto"></td>
    </tr>
    <tr>
    	<td><a href="#" onclick="hilangkan()">Hide</a></td>
    </tr>
    </table>
    <table border="0" class="form_action">
    <tr>
    	<td> Cari :
   	    <input type="text" id="cari" name="cari" size="25" value="<?php echo $cari?>" /> 
   	    &nbsp;&nbsp;&nbsp;
        Berdasar : 
        <select id="berdasar" name="berdasar">
          <option value="kode" <?php if($berdasar=="kode"){echo"selected";}?>>Kode Model</option>
          <option value="nama" <?php if($berdasar=="nama"){echo"selected";}?>>Nama </option>
        </select>
        
        &nbsp; <input type="checkbox" id="cek" name="cek" value="oke" <?php if($cek=="oke"){echo"checked";}?> onclick="cek_status()" />Cek Tanggal &nbsp;
        &nbsp;&nbsp;<span id="tgll">
        Tgl Launching 
			Dari :	
       
         
			<!-- calendar attaches to existing form element -->
			<?php $awal=$dari;$akhir=$sampai;?><input type="hidden" id="hal" name="hal" />
            <input type="text" name="dari"  id="dari" value="<?php echo $dari; ?>" size="16"/> 
            &nbsp;
			 
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'dari'
              });
            </script>
			 &nbsp;
			 &nbsp;&nbsp;&nbsp;&nbsp;
			 
			 <input type="text" name="sampai"  id="sampai" value="<?php echo $sampai; ?>" size="16"/> 
			 &nbsp;
			
             <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'sampai'
              }); 
             </script>
    </span></td>
    </tr>
    <tr>
      <td>Pilihan : <select name="txt_pilihan" id="txt_pilihan" style="width:200px;">
          <?php
		 
          foreach($arrayProdukPilihan as $key => $value){
				   ?>
	    <option value="<?php echo $key; ?>" <?php if($txt_pilihan==$key){echo"selected";}?>><?php echo $value; ?></option>
	    <?php  	 
				 }
			   ?>
          </select></td>
    </tr>
    <tr>
      <td><input type="submit" id="submit" name="submit" value="Cari" /></td>
    </tr>
    </table>
    <span id="proc_reshare"></span>
    <span id="proc_markas"></span>
    <span id="proc_distribusi"></span>
	<table border="1" cellspacing="1" cellpadding="1" bgcolor="#FFFFFF" class="sortable">
	 
      <tr>
        <td height="30"><strong>No</strong></td>
        <td height="30" ><strong>Kode Model</strong></td>
        <td height="30"><strong>Nama</strong></td> 
        <td><strong>Stok Dist</strong></td>
        <td><strong>Stok Markas</strong></td>
        <td><strong>Stok Reshare</strong></td>
        <td bgcolor="#00CC00" ><strong>Total Stok</strong></td>
        <td bgcolor="#00CC00"  ><strong>Total Hpj</strong></td>
        <td ><strong>Tanggal Launching</strong></td>
      </tr> 
      <tbody>
      <?php 
	  $sql_inner='';
	  $sql_tbhn='';
	  if(!empty($txt_pilihan)){
		  $sql_inner.="INNER JOIN (SELECT DISTINCT SUBSTRING(id_barang,1,7)id_barang FROM produk_pilihan WHERE pilihan='$txt_pilihan' AND `status`=1) AS pp ON pp.id_barang=f.kode_model ";
	  }
	  $sql="SELECT REPLACE(f.kode_model,' ','') kode_model,f.nama_model,l.tanggal_launching,mf.nama_file
	   FROM mst_model_fix as f  $sql_inner
	  left join mst_model_launching as l on 
	  (l.kode_model=f.kode_model)
	  left join mst_model_foto as mf on 
	 (mf.kode_model=f.kode_model)
	  $terusan $terusan_launching $terusan_l order by f.kode_model ";
	 
	  
$query=mysql_query($sql)or die($sql);
$jmlData=mysql_num_rows($query);
$hal=$_REQUEST[hal];
if($hal==""){
	$hal="0";
}
$jmlHal=500;
$awal=$hal*$jmlHal;
$tothal=ceil($jmlData/$jmlHal);
 
	$sql=$sql." limit $awal,$jmlHal";
 
if($username=='budi-it'){
   echo "<h3>$sql</h3>";	
}

$query=mysql_query($sql)or die($sql);
$banyak=mysql_num_rows($query);
$i=$awal;  

if($cek=='oke'){
   $bytgl='1';
}else{
    $bytgl='0';	
}
$c 	= "0";
	  while(list($kode,$nama,$tanggal_l,$nf)=mysql_fetch_array($query)){
			$no++;
			$i++;
			
			$c++;
			
			?>
				<tr class="row<?php echo $i?>" id="row_"<?php echo $kode; ?> >
                	<td><?php echo $i?></td>
                	<td id="nomor<?php echo $no?>">
                    <?php
						echo $kode;
						
					if($no==$banyak){
						$temp_code.="$kode";
						}else{
							$temp_code.="$kode-";
						}
					?></td>
                	<td><?php  if($nf!=""){?>
                  <a href="#" onclick="gambar('<?php echo $kode?>')"><?php echo $nama; ?></a><?php }else{
						echo $nama;
						}?></td>
                    <td id="g<?php echo $kode?>" align="center">0</td>
                    <td id="gn<?php echo $kode?>" class="hilang" align="center">0</td>
                    <td id="m<?php echo $kode?>" align="center">0</td>
                    <td id="mn<?php echo $kode?>" class="hilang" align="center">0</td>
                    <td id="r<?php echo $kode?>" align="center">0</td>
                    <td id="rn<?php echo $kode?>" class="hilang" align="center">0</td>
                    <td id="ts<?php echo $kode?>" class="myLink" bgcolor="#00CC00" onclick="showDetail('<?php echo $kode; ?>')">0</td>
                    <td id="tu<?php echo $kode?>" align="center" bgcolor="#00CC00">0</td>
                    <td align="center"><?php echo $tanggal_l;?></td>
                </tr>
			<?php 
			
			if($c==500){
						
			}	
			 
		}
	  ?>
      </tbody>
      <tfoot>
      <tr>
      	<td colspan="3"><em><strong>Total Perhalaman</strong></em></td>
        <td align="center" id="t_g">0</td>
        <td align="center" id="t_g_u" class="hilang">0</td>
        <td align="center" id="t_m">0</td>
        <td align="center" id="t_m_u" class="hilang">0</td>
        <td align="center" id="t_r">0</td>
        <td align="center" id="t_r_u" class="hilang">0</td>
        <td align="center" id="t_s" bgcolor="#00CC00" ></td>
        <td align="center" id="t_s_u" bgcolor="#00CC00" ></td>
        <td align="center" bgcolor="#000000" rowspan="2">&nbsp;</td>
      </tr>
      <tr>
      	<td colspan="3"><em><strong>Total All</strong></em></td>
        <td align="center" id="t_g_a">0</td>
        <td align="center" id="t_g_a_u" class="hilang">0</td>
        <td align="center" id="t_m_a">0</td>
        <td align="center" id="t_m_a_u" class="hilang">0</td>
        <td align="center" id="t_r_a">0</td>
        <td align="center" id="t_r_a_u" class="hilang">0</td>
        <td align="center" id="t_s_a" bgcolor="#00CC00" ></td>
        <td align="center" id="t_s_a_u" bgcolor="#00CC00" ></td> 
      </tr>
</tfoot>
</table>
<input type="hidden" id="hitung1" name="hitung1" value="<?php echo $banyak?>" />
<span id="temp_cod" class="hilang"><?php echo $temp_code;?></span>

 <table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
			<span class="hal" onclick="pindah('0')">First</span>
                <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="pindah('<?php echo $i?>')"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData/$jmlHal)){
						if($i>=(($jmlData/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="pindah('<?php echo $i?>')"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="pindah('<?php echo $i?>')"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="pindah('<?php echo $tothal?>')">Last</span>
&nbsp;&nbsp;            </td>
        </tr>
		
</table>

</form>

<script>
  noAwal=<?php echo ($awal); ?> ;
  ceking();
 
 
 ambil_stok_all("gunas_all",'<?php echo $bytgl; ?>','<?php echo $dari; ?>','<?php echo $sampai; ?>');
 ambil_stok_all("markas_all",'<?php echo $bytgl; ?>','<?php echo $dari; ?>','<?php echo $sampai; ?>');
 ambil_stok_all("reshare_all",'<?php echo $bytgl; ?>','<?php echo $dari; ?>','<?php echo $sampai; ?>');
 
 ambil_stok("gunas");
 ambil_stok("markas");
 ambil_stok("reshare");
 
  $(".hilang").hide();

$(".table_gambar").hide();

</script>
<form name='frmDetail' id='frmDetail' action="monitoring_stok_produk_pilihan_pergudang_v6.php?action=search" method="post"
 target="_blank" style="display:none">
<input name="txt_barcode" id="f_barcode" value=""  type="text" />
<input name="rdPilihan" type="text" value="model" />
<input name="pilihan" type="text" value="kode" />
</form>


<span id="debug">...</span>
 <?php
include_once "footer.php" ?>
