<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="MONITORING PENJULAN PER-GUDANG "; ?>
<?php  include('header.php');
 /*
  if($_SESSION['username']!='rian-it'){
	 ?>
    <table width="100%">
	 	<tr>
			<td align="center"><img src="undercontruction.jpg" width="500" /><br />
			<strong>Maaf, halaman ini belum bisa di akses karena dalam tahap perbaikan</strong>
			</td>
		</tr>
		<tr>
			<td height="50"></td>
		</tr>
		<tr>
			<td align="right"><i><em>IT DEVELOPMENT MANAGER</em></i></td>
		</tr>
	 </table><br /><br /><br /><br />   
	 <?php   die;
}    
 
/*
edit tanggal 30 jun 2015 
by budi permintaan p agus untuk menyamakan nilai dengan nilai omset bukan nilai bruto
file js nya dipisah untuk kemudahan membaca
file aslinya adalah penjualan dan stok.php

v5 kunci produk pilihan berdasarkan user (14042016)
prefix area lain
*/

	

$kodeoutlet=$_SESSION['outlet'];
	$group=substr($kodeoutlet,5,5);
	  if($group=='O0000' ||$group=='o0000'){
		  $area=substr($kodeoutlet,0,4);
	  }else{
		  $area=$kodeoutlet;  
	  }
	
if(isset($_GET['ett'])){
   $_SESSION['prefix']=$_GET['ett'];	
   $prefix=$_SESSION['prefix'];
}else{  
  $prefix=$_SESSION['prefix'];
}
  
  $prefix='%';//prefix tidak berlaku untuk menu ini
  $sql_tambahan='';
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
  
    $sql_tambahan=" AND ( $data_tambahan ) AND o.id LIKE '$prefix%' ";
	$sql_tambahan_gudang=" AND ( $data_tambahan ) ";
	
	

$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$berdasar=$_POST['berdasar'];
$customer	= $_POST['customer'];
if($berdasar==""){
	$berdasar="kode";
	}
$cari=$_POST['cari'];
if($dari==""){  
	$dari=date("Y-m-d");  
	$sampai=date("Y-m-d");
}
$pilihan1=$_POST['pilihan1'];
 
 
 //===========14042016
  $sql_priv="SELECT COUNT(*) FROM user_account_produk_pilihan WHERE username='$username';";
  $res_priv=mysql_query($sql_priv);
  $jml_priv=0;
  list($jml_priv)=mysql_fetch_array($res_priv);
  $arrayProdukPilihan=array();
  if($jml_priv>0){
	  $sql="SELECT SQL_CACHE pp.pilihan  FROM produk_pilihan_mst pp inner join 
	  user_account_produk_pilihan up on up.produk_pilihan=pp.pilihan WHERE pp.aktif=1;"; 
  }else{//kosong
	  $arrayProdukPilihan['']='Semua';
	  $sql="SELECT SQL_CACHE pilihan FROM produk_pilihan_mst WHERE aktif=1;";
  }
  
 
  $query=mysql_query($sql)or die($sql);
  
  while(list($list_pilihan)=mysql_fetch_array($query)){
		$arrayProdukPilihan[$list_pilihan]=$list_pilihan;
  }
 //=====================
 

 $sql="SELECT SUBSTRING(id,1,4),warna FROM outlet_warna  WHERE SUBSTRING(id,1,4) LIKE 'M%'";
		$res1=mysql_query($sql);
		$arrayWarna=array();
		while(list($id,$warna)=mysql_fetch_array($res1)){
			$arrayWarna[$id]=$warna;
		} 
?>

<style>
.datagrid table { border-collapse: collapse; text-align: left; width: 100%; } .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #36752D; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }.datagrid table td, 
.datagrid table th 
{ padding: 3px 10px;  }
.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420) );background:-moz-linear-gradient( center top, #36752D 5%, #275420 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');background-color:#36752D; color:#FFFFFF; font-size: 12px; font-weight: bold; border-left: 1px solid #36752D; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #275420; border-left: 1px solid #C6FFC2;font-size: 10px;font-weight: normal; }.datagrid table tbody .alt td { background: #DFFFDE; color: #275420; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }.datagrid table tfoot td div { border-top: 1px solid #36752D;background: #DFFFDE;} .datagrid table tfoot td { padding: 0; font-size: 12px } .datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }.datagrid table tfoot  li { display: inline; }.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #36752D;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420) );background:-moz-linear-gradient( center top, #36752D 5%, #275420 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');background-color:#36752D; }.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #275420; color: #FFFFFF; background: none; background-color:#36752D;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }

  .kelas_departemen {
	width: 80px;
	border: thin solid #06F;	
	position:static;
	margin-left:950px;
	margin-top:-15px;
	 position:inherit !important;
	 text-align:center;
	 cursor:pointer;

	  }
	  
	  .kelas_departemen:hover { 
	  background-color:#E2FBFC;

	  }
	  .mylink {
		 cursor:pointer; 
		 color:#0000FF;
		  }
</style>
<div class="datagrid">
<link rel="stylesheet" href="themes/base/jquery.ui.all.css">
<?php $jam=	date("His");?>
<script src="ui/jquery.ui.core.js"></script>
	<script src="ui/jquery.ui.widget.js"></script>
	<script src="ui/jquery.ui.datepicker.js"></script>
 <script src="app_libs/monitoring_penjualan_dan_stok_v5.js?id=<?php echo  $jam;?>"></script>

<!--script language="JavaScript" src="jquery.timer.js"></script-->     
	<?php if(isset($_POST['cari'])){?>
   
	<script>
	$(function() {
		$( "#bulan" ).datepicker({
			dateFormat:'yy-mm-dd',
            changeMonth: true,
            changeYear: true
		});
	});</script>
<?php }?>

    <style>
    fieldset { border:1px solid green }

legend {
  padding: 0.2em 0.5em;
  border:1px solid green;
  color:green;
  font-size:90%; 
  }
  

    </style>
    <form id="f1" name="f1" method="post" action="?action=search">
<fieldset> 
  Berdasar : <select id="berdasar" name="berdasar">
  				<option value="" <?php if($berdasar==""){echo"selected";}?>> </option>
  				<option value="kode" <?php if($berdasar=="kode"){echo"selected";}?>>Kode </option>
  				<option value="nama" <?php if($berdasar=="nama"){echo"selected";}?>>Nama</option>
            </select>&nbsp;&nbsp;Produk : 
            <input type="text" id="cari" name="cari" value="<?php echo $cari?>" />
Dari : 
<script language="JavaScript" src="calendar_us.js"></script>
              <link rel="stylesheet" href="calendar.css" />
              <!-- calendar attaches to existing form element -->
           
              <input type="text" name="dari" readonly id="dari" value="<?php echo $dari; ?>" size="10"/>
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
                  Sampai : <input type="text" name="sampai" readonly id="sampai" value="<?php echo $sampai; ?>" size="10"/>
            &nbsp;
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'sampai'
              }); 
            </script>
			&nbsp;
			&nbsp;
			Produk Pilihan :
			<select name="pilihan1" id="pilihan1" onchange="cek_data1()">
             <?php
			     foreach($arrayProdukPilihan as $key => $value){
				   ?>
                   <option value="<?php echo $key; ?>" <?php if($pilihan1==$key){echo"selected";}?>><?php echo $value; ?></option>
                 <?php  	 
				 }
			   ?>
            </select> 
		&nbsp;&nbsp;&nbsp;
		Type Customer : 
		<select id="customer" name="customer"  >
		<option value="">All</option>
		<?php 	$sql	= "SELECT id,`type` FROM customer_type WHERE show_mon_sell_cust=1 ORDER BY urutan";
				$resi	= mysql_query($sql)or die($sql);
				while(list($id,$type)	= mysql_fetch_array($resi)){
					?><option value="<?php echo $id?>" <?php if($id==$customer){echo"selected";}?>><?php echo $type?></option><?php
				}
		?>
		</select>	
			
  <input type="submit" id="sim" name="sim" value="Search" />  
  <span onclick="tampil()" id="sh" class="mylink">#</span>
               <span onclick="hilangkan()" id="hi" class="mylink">#</span> <sup>klik disini untuk lihat detail</sup><!--<br /><font color="#FF0000">Under Maintenance Link</font> -->
            
  </fieldset>  <input type="hidden" id="txtlocation" name="txtlocation" title="txtlocation" /> 
  <input type="hidden" id="tgl1" name="tgl1" title="tgl1" /> 
  <input type="hidden" id="tgl2" name="tgl2" title="tgl2" /> 
  
  <input type="hidden" id="txtbarcode" name="txtbarcode" title="txtbarcode" />  
  <input type="hidden" id="txt_nama" name="txt_nama" title="txt_nama" /> 
  <input type="hidden" id="txtrnama" name="txtrnama" title="txtrnama" /> 
  
  <input type="hidden" id="txtNama" name="txtNama" title="txtNama" />  
 
  <input type="hidden" id="txtjenis" name="txtjenis" value="1"  title="txtjenis"/> 
  <input type="hidden" id="txtjenisoutlet" name="txtjenisoutlet" value="2" title="txtjenisoutlet" /> 
  
  <input type="hidden" id="txtgudang" name="txtgudang" title="txtgudang" />  
  <input type="hidden" id="nama_produk" name="nama_produk" title="nama_produk" />  
  <input type="hidden" id="tipe" name="tipe" title="tipe" />  
  <input type="hidden" id="txt_pilihan" name="txt_pilihan" value="<?php echo $pilihan1;?>"/> 
  
 </form>  

  <?php
    if ( $_GET['action']=="search" ) {

	}else{
	   include_once("footer.php");	
	   die();	
	}
	
	
			
	 		
	 
  ?>

<table border="0">
<tr>
	<td valign="top">
 <fieldset>
  <legend>Reshare <span id="load_r"><font size="1" color="#FF0000">Loading Penjualan ....</font></span> 
  <span id="load_sr"><font size="1" color="#FF0000">Loading Stok ....</font></span>
  </legend>
  
  </fieldset>    
  <?php
   # echo "[ ".$_SESSION['prefix']." ]";
  ?>
<table>
<thead>
	<tr>
    	<th height="30">No </th>
        <th>Kode Reshare</th>
        <th>Nama Reshare</th> 
        <th class="hill">Penjualan</th>   
        <th class="hill">Nilai</th> 
        <th class="hill">Retur</th>   
        <th class="hill">Nilai</th> 
        <th>Qty Penjualan</th>   
        <th>Nilai</th> 
        <th>Stok</th>   
        <th>Nilai</th>
     </tr> 
</thead>
<tbody> 
<?php 



$sql="select o.id,o.nama,substring(o.id,1,4) from outlet as o where o.type='4' and o.jenis='1' and is_show_omset='1'  $sql_tambahan ";
$query=mysql_query($sql)or die($sql);
$banyak=mysql_num_rows($query);
?><input type="hidden" id="b_r" name="b_r" value="<?php echo $banyak?>" /><?php
while(list($kode_o,$nama_o,$markas)=mysql_fetch_array($query)){
	
		$no++;
     $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2; 
		$bgcolor=$arrayWarna[$markas];
	?>
	<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
        	<td height="25"><?php echo $no;?></td>
        	<td height="25" id="id_outlet<?php echo $no?>"><?php echo $kode_o;?></td>
        	<td><?php echo $nama_o;?></td>
        	<td align="right" id="r_qty<?php echo $kode_o?>" class="hill">0</td>
        	<td align="right" id="r_amount<?php echo $kode_o?>" class="hill">0</td>
            
        	<td align="right" id="rr_qty<?php echo $kode_o?>" class="hill">0</td>
        	<td align="right" id="rr_amount<?php echo $kode_o?>" class="hill">0</td>
        	
            <td align="right" id="net_qty<?php echo $kode_o?>" >0</td>
        	<td align="right" id="net_amount<?php echo $kode_o?>"  >0</td>
        	
            <td align="right"  id="sr_qty<?php echo $kode_o?>" >0</td>
        	<td align="right" id="sr_amount<?php echo $kode_o?>" >0</td>
        </tr>
	<?php
	}
?>
</tbody><thead>
<tr>
	<th colspan="3" height="30">Total Penjualan Reshare  </th>
	<th align="right" id="r_tqty" class="hill">0</th>
	<th align="right" id="r_tamount" class="hill">0</th>
    
	<th align="right" id="rr_tqty" class="hill">0</th>
	<th align="right" id="rr_tamount" class="hill">0</th>
    
	<th align="right" id="net_tqty">0</th>
	<th align="right" id="net_tamount">0</th>
    
	<th align="right" id="sr_tqty" >0</th>
	<th align="right"  id="sr_tamount" >0</th>
</tr> 
</thead>
</table></td>
<td width="10">&nbsp;</td>
<td valign="top">
		
 <fieldset>
  <legend>Markas <span id="load_m"><font size="1" color="#FF0000">Loading Penjualan....</font></span>
  <span id="load_sm"><font size="1" color="#FF0000">Loading Stok....</font></span>
  </legend>
  
  </fieldset>    		
                <table>
                <thead>
                    <tr>
                        <th height="25">No </th>
                      	<th>Kode  </th>
                        <th>Markas</th> 
                        <th class="hill">Penjualan</th>   
                        <th class="hill">Nilai</th>
                        <th class="hill">Retur</th>   
                        <th class="hill">Nilai</th> 
                        <th >Qty Penjualan</th>   
                        <th >Nilai</th> 
                        <th>Stok</th>   
                        <th>Nilai</th>
                     </tr> 
                </thead>
                <tbody> 
                	<?php $sql="SELECT o.id,o.nama,substring(o.id,1,4) FROM outlet AS o WHERE o.type='1' AND o.jenis='1'  $sql_tambahan ";
					$query=mysql_query($sql)or die($sql);
					$banyak=mysql_num_rows($query);
?><input type="hidden" id="b_m" name="b_m" value="<?php echo $banyak?>" /><?php
					$no=0;
					while(list($id,$nama,$markas)=mysql_fetch_array($query)){
							$no++;
     $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2; 
		$bgcolor=$arrayWarna[$markas];
							?>
						<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
                            <td align="center"><?php echo $no?></td>
                            <td height="25" id="id_markas<?php echo $no?>"><?php echo $id;?></td>
        					<td height="25"><?php echo $nama;?></td>
                            <td align="right" id="m_qty<?php echo $id?>" class="hill">0</td>
                            <td align="right" id="m_amount<?php echo $id?>" class="hill">0</td>
                            
                            
                        <td align="right" id="rm_qty<?php echo $id?>" class="hill">0</td>
                        <td align="right" id="rm_amount<?php echo $id?>" class="hill">0</td>
                        
                        <td align="right" id="net_m_qty<?php echo $id?>"   >0</td>
                        <td align="right" id="net_m_amount<?php echo $id?>"  >0</td>
                            
                            <td align="right"  id="sm_qty<?php echo $id?>" >0</td>
                            <td align="right"  id="sm_amount<?php echo $id?>" >0</td>
                        </tr>
						<?php
					}
					?>
                </tbody>
                <thead>
<tr>
	<th colspan="3" height="30">Total Penjualan Markas </th>
	<th align="right" id="m_tqty" class="hill">0</th>
	<th align="right" id="m_tamount" class="hill">0</th> 
    
    
	<th align="right" id="rm_tqty" class="hill">0</th>
	<th align="right" id="rm_tamount" class="hill">0</th>
    
    
    
	<th align="right" id="net_m_tqty"  >0</th>
	<th align="right" id="net_m_tamount"  >0</th>
    
	<th align="right"   id="sm_tqty">0</th>
	<th align="right"  id="sm_tamount">0</th> 
</tr> 
</thead>
                </table>
<br /> 


 <fieldset>
  <legend>Gunas <span id="load_g"><font size="1" color="#FF0000">Loading Penjualan....</font></span>
   <span id="load_sg"><font size="1" color="#FF0000">Loading Stok....</font></span>
  </legend>
  
  </fieldset>    
				<table>
                <thead>
                    <tr>
                        <th height="30">No </th>
                        <th>Kode </th>
                        <th>Nama Gudang Nasional</th> 
                        <th class="hill">Penjualan</th>   
                        <th class="hill">Nilai</th>
                        
                        <th class="hill">Retur</th>   
                        <th class="hill">Nilai</th> 
                        <th  >Qty Penjualan</th>   
                        <th  >Nilai</th> 
                        
                        <th>Stok</th>   
                        <th>Nilai</th>
                  </tr> 
                </thead>
                <?php $sql="SELECT replace(o.id,'.',''),o.nama FROM gudang_distribusi AS o WHERE o.jenis='1' $sql_tambahan_gudang ";
				$query=mysql_query($sql)or die($sql);
				$banyak=mysql_num_rows($query);
?><input type="hidden" id="b_g" name="b_g" value="<?php echo $banyak?>" /> <?php
				$no=0;
					while(list($id,$nama)=mysql_fetch_array($query)){
							$no++;
     $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2; 
						   
							?>
						<tr bgcolor="<?php echo $bgcolor?>">
                            <td align="center"><?php echo $no?></td>
                            <td height="25" id="id_gunas<?php echo $no?>"><?php echo $id;?></td>
        					<td height="25"><?php echo $nama;?></td>
                           
                            <td align="right" id="g_qty<?php echo $id?>" class="hill">0</td>
                            <td align="right" id="g_amount<?php echo $id?>" class="hill">0</td>
                            
                             <td align="right" id="rg_qty<?php echo $id?>" class="hill">0</td>
                             <td align="right" id="rg_amount<?php echo $id?>" class="hill">0</td>
                        
                        <td align="right" id="net_g_qty<?php echo $id?>"   >0</td>
                        <td align="right" id="net_g_amount<?php echo $id?>"  >0</td>
                            
                            <td align="right"  id="sg_qty<?php echo $id?>"  >0</td>
                            <td align="right" id="sg_amount<?php echo $id?>" >0</td>
                        </tr>
						<?php
					}
					?>
                <thead>
<tr>
	<th colspan="3" height="30">Total Penjualan Gunas </th>

	<th align="right" id="g_tqty" class="hill">0</th>
	<th align="right" id="g_tamount" class="hill">0</th> 
    
    
	<th align="right" id="rg_tqty" class="hill">0</th>
	<th align="right" id="rg_tamount" class="hill">0</th> 
	<th align="right" id="net_g_tqty"   >0</th>
	<th align="right" id="net_g_tamount"  >0</th>
    
	<th align="right" id="sg_tqty" >0</th>
	<th align="right"  id="sg_tamount" >0</th>
</tr> 
<tr>
	<td height="30">&nbsp;</td>
</tr>
<!-- 
<tr>
	<th colspan="3" height="30">Total  Semua Gudang</th>
	<th align="right" id="allqty">0</th>
	<th align="right" id="allamount">0</th>
</tr>  -->
</thead>
                </table>
<br />&nbsp;

 <fieldset>
  <legend><strong><font size="2">Total Semua masing-masing gudang</font></strong> <span id="load_g"> </span></legend>
  
  </fieldset> 
  <table>
                <thead>
                    <tr> 
                        <th height="30">Nama Gudang </th>
                        <th class="hill">Penjualan</th> 
                        <th class="hill">Nilai</th>    
						<th class="hill">Retur</th>   
                        <th class="hill">Nilai</th> 
                        <th >Qty Penjualan</th>   
                        <th >Nilai</th> <strong></strong>
                        <th>Stok</th> 
                        <th>Nilai</th>
                  </tr> 
                </thead>
				<tbody>
					<tr>
						<td><strong>Reshare All</strong></td>
						<td  height="30"  id="qty_reshare_all" align="right" class="hill">0</td>
						<td id="amount_reshare_all" align="right" class="hill">0</td>
						
						<td  height="30"  id="qty_retur_reshare_all" align="right" class="hill">0</td>
						<td id="amount_retur_reshare_all" align="right" class="hill">0</td>
						
						<td  height="30"  id="qty_net_reshare_all" align="right">0</td>
						<td id="amount_net_reshare_all" align="right">0</td>
						
						<td  height="30"  id="sqty_reshare_all"  align="right">0</td>
						<td   align="right" id="samount_reshare_all">0</td>
					</tr>
					<tr class="alt">
						<td  height="30" ><strong>Markas All</strong></td>
						<td id="qty_markas_all" align="right" class="hill">0</td>
						<td id="amount_markas_all" align="right" class="hill"> 0</td>
						
						
						
						<td id="qty_retur_markas_all" align="right" class="hill">0</td>
						<td id="amount_retur_markas_all" align="right" class="hill"> 0</td>
						
						
						<td id="qty_net_markas_all" align="right">0</td>
						<td id="amount_net_markas_all" align="right"> 0</td>
						
						<td  align="right" id="sqty_markas_all"  >0</td>
						<td  align="right"  id="samount_markas_all" > 0</td>
					</tr>
					<tr>
						<td><strong>Gunas All</strong></td>
						<td   height="30"  id="qty_gunas_all" align="right" class="hill">0</td>
						<td id="amount_gunas_all" align="right" class="hill">0</td>
						
						
						<td   height="30"  id="qty_retur_gunas_all" align="right" class="hill">0</td>
						<td id="amount_retur_gunas_all" align="right" class="hill">0</td>
						
						
						<td   height="30"  id="qty_net_gunas_all" align="right">0</td>
						<td id="amount_net_gunas_all" align="right">0</td>
						
						
                        <td align="right"   id="sqty_gunas_all"  >0</td>
						<td align="right" id="samount_gunas_all"  > 0</td>
					</tr>
					 
				</tbody>
				 <thead>
                    <tr> 
                        <th height="30">Total Semua Gudang</th>
                        <th id="clsqty" align="right" class="hill">0</th> 
                        <th id="clsamount" align="right" class="hill">0</th>  
						
						
                        <th id="clsreturqty" align="right" class="hill">0</th> 
                        <th id="clsreturamount" align="right" class="hill">0</th> 
						
						
                        <th id="clsnetqty" align="right">0</th> 
                        <th id="clsnetamount" align="right">0</th> 
						
                        <th align="right"  id="clsqtys" >0</th> 
                        <th align="right" id="clsamounts"  >0</th>   
                  </tr> 
                </thead>
	</table>

</td>
</tr>

</table>



<script>


	
ambil_penjualan("r"); 
 ambil_penjualan("m"); 
ambil_penjualan("g"); 
 
ambil_retur("rr");
ambil_retur("rm");
ambil_retur("rg");
 


// stok 

 ambil_stok("sr"); 
ambil_stok("sm"); 
ambil_stok("sg"); 
$(".hill").hide();
$("#hi").hide();
//txtlocation      tgl2 txtbarcode txt_nama
</script>
<?php include_once "footer.php"; ?>