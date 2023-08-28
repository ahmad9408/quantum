<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Rekap pengiriman pilihan produk";  ?>

<?php 
include("css_group.php");  
/*
v2 08062017 
tambah pencarian berdasarkan ukuran dan warna by budi order P Agus ke wa jam 14:20
*/ 
    include('header.php');
	$jenis=$_POST['jenis'];
	$cari=$_REQUEST['cari'];
  
	@$gudang=$_POST['gudang'];
	@$srcsize=$_POST['srcsize'];
	@$txt_warna=$_POST['txt_warna'];
	
	if($gudang!=""){
		$firstcode="dp.gudang='$gudang' and ";
	}else{
		$firstcode	= "";
	}
	$awal=$_REQUEST[awal];
	$akhir=$_REQUEST[akhir];
	if($awal==""){
		$awal=date("Y-m")."-01";
		$akhir=date("Y-m-d");
	}


$pilihan=$_REQUEST['pilihan'];
if($pilihan==""){
	$code_pilihan="  ";
}else if($pilihan=="bukan"){
	$code_pilihan=" and dpd.kd_produk not in (select id_barang from produk_pilihan) ";
}else{
	$code_pilihan=" and dpd.kd_produk in (select id_barang from produk_pilihan where pilihan='$pilihan') ";
}

//===========14042016
   $sql_priv="SELECT produk_pilihan FROM user_account_produk_pilihan WHERE username='$username';";
  $res_priv=mysql_query($sql_priv);
  $jml_priv=0;
  list($prod_pil)=mysql_fetch_array($res_priv);
  $arrayProdukPilihan=array();
  if(mysql_num_rows($res_priv)>0){
	  $sql="SELECT SQL_CACHE pp.pilihan  FROM produk_pilihan_mst pp inner join 
	  user_account_produk_pilihan up on up.produk_pilihan=pp.pilihan WHERE pp.aktif=1;";
	  $pc 		= explode(",",$prod_pil);
	  $banyak 	= count($pc);
	  $ter		= "";
	  foreach($pc as $key=>$value){
	  	$j++;
	  	if($j==$banyak){
			$ter 		.= "'".$value."'";
		}else{
			$ter 		.= "'".$value."',";
		}  
	  } 
	  $ter 			= "(".$ter.")";   
	 // $sql 		= "SELECT SQL_CACHE pp.pilihan  FROM produk_pilihan_mst pp WHERE pp.aktif=1 and pp.pilihan in () ";
	  $sql 		= "SELECT SQL_CACHE pp.pilihan  FROM produk_pilihan_mst pp WHERE pp.aktif=1 and  pp.pilihan in $ter ";
  }else{//kosong
	  $arrayProdukPilihan['']='Semua';
	  $sql="SELECT SQL_CACHE pilihan FROM produk_pilihan_mst WHERE aktif=1;";
  }
  
 
  $query=mysql_query($sql)or die($sql);
  
  while(list($list_pilihan)=mysql_fetch_array($query)){
		$arrayProdukPilihan[$list_pilihan]=$list_pilihan;
  }
 //=====================

 ?> 
 <style>
 .white {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-style: italic;
	font-weight: bold;
	color: #FFFFFF; 
 }
 
 </style>
 <link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
 <script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
 <script src="app_libs/rekap_pengiriman_produk_v3.js" type="text/javascript"></script>
 <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
<!-- .replace(/,/g, ''), 10 -->
$(document).ready(function(){
	$('#gudang').chosen({});
	$('#pilihan').chosen({});
	$('#srcsize').chosen({});
	
	
})
</script>
 <form method="post" name="f1" id="f1">
 <fieldset>
 	<table border="0" >
	<tr>
		<td width="146"   class="text-bold"><em>Pencarian
		  
		</em></td>
		<td width="17"   class="text-bold">&nbsp;</td>
		 
		<td width="449">&nbsp;&nbsp;</td>
		
		 
	</tr>
	<tr>
	  <td   class="text-bold"><em>
	    <select id="jenis" name="jenis">
	      <option value="">Pilih</option>
	      <option value="kode" <?php if($jenis=='kode'){echo"selected";}?>>Kode</option>
	      <option value="nama" <?php if($jenis=='nama'){echo"selected";}?>>Nama</option>
	      </select>
	  </em></td>
	  <td   class="text-bold">:</td>
	  <td><input type="text" name="cari"  size="30" value="<?php echo $cari?>" placeholder="Code Or Name" />
	    <input type="hidden" id="hal" name="hal" /></td>
	  </tr>
	<tr>
	  <td   class="text-bold">Size</td>
	  <td   class="text-bold">:</td>
	  <td><select name="srcsize" id="srcsize" style="width:300px;">
	    <option value="">-Size-</option>
	    <?php
											$sql="SELECT SQL_CACHE kode,size FROM mst_size";
											$hsl=mysql_query($sql);
											$arraySize=array();
											while(list($kode,$item)=mysql_fetch_array($hsl)){
												$arraySize[$kode]=$item;
										?>
	    <option value="<?php echo $kode; ?>" <?php if($kode==$srcsize){echo "selected";} ?>><?php echo $item; ?></option>
	    <?php
											}
										?>
	    </select></td>
	  </tr>
	<tr>
	  <td   class="text-bold">Warna</td>
	  <td   class="text-bold">:</td>
	  <td><input type="text" name="txt_warna"  size="30" value="<?php echo $txt_warna?>" placeholder="isi Dengan warna" /></td>
	  </tr>
	<tr>
	
	  <td class="text-bold"><em>Gudang</em></td>
	  <td class="text-bold">:</td>
	   
	  <td><select name="gudang" id="gudang" style="width:300px;">
			  <option value="">Pilih</option>
	    <?php
		  $sql="SELECT SQL_CACHE  id,nama from gudang_distribusi where jenis=1;";
		  $res=mysql_query($sql);
		  while(list($id,$nama_gudang)=mysql_fetch_array($res)){
		  ?>
	    <option value="<?php echo $id;?>" <?php if($gudang==$id){echo"selected";}?>><?php echo "[ $id ]".$nama_gudang; ?></option>
	    <?php
		  }
		?>
	    </select></td>
	  
	  </tr>
	  
	<tr>
	
	  <td class="text-bold"><em>Dari</em></td>
	  <td class="text-bold">:</td>
	   
	  <td><script language="JavaScript" src="calendar_us.js"></script>
              <link rel="stylesheet" href="calendar.css" />
              <!-- calendar attaches to existing form element -->
              
              <input type="text" name="awal" readonly id="awal" value="<?php echo $awal; ?>" size="10"/>
            &nbsp;
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'awal'
              });
        </script></td>
	   
	</tr>
	
	<tr>
	
	  <td class="text-bold"><em>Sampai</em></td>
	  <td class="text-bold">:</td>
	   
	  <td><input type="text" name="akhir" readonly id="akhir" value="<?php echo $akhir; ?>" size="10"/>
	  &nbsp;
	  <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'akhir'
              }); 
            </script></td>
	   
	</tr>
	
	
	<tr>
	  <td height="2" class="text-bold"><em>Pilihan Produk </em></td>
	  <td class="text-bold">:</td>
	   
	  <td><select name="pilihan" id="pilihan" style="width:400px;">
	    <?php
			     foreach($arrayProdukPilihan as $key => $value){
				   ?>
	    <option value="<?php echo $key; ?>" <?php if($pilihan==$key){echo"selected";}?>><?php echo $value; ?></option>
	    <?php  	 
				 }
			   ?>
	    </select></td>
	  
	  </tr>
	<tr>
	  <td height="2">&nbsp;</td>
	  <td>&nbsp;</td>
	 
	  <td style=" padding-top: 5px" ><input type="button" name="btn-cari" id="btn-cari" value="       Cari       " />
	   <!-- <input type="submit" name="submit" id="submit" value="       Cari       " /> 
	  		&nbsp;&nbsp;&nbsp;<input type="button" href="export_rekap_pengiriman_produk_v3.php" name="ekspor" id="ekspor" value="     Export     " /> -->
	  	</td>

	   
	  </tr>
	</table>
	
   
 <!-- </fieldset> -->
  </form>
   
	  		
  <!-- <form method="post" action="export_rekap_pengiriman_produk_v3.php" name="f2">
  	<input type="hidden" name="temp_jenis" value="<?php echo $jenis; ?>">
  	<input type="hidden" name="temp_cari" value="<?php echo $cari; ?>">
  	<input type="hidden" name="temp_srcsize" value="<?php echo $srcsize; ?>">
  	<input type="hidden" name="temp_txt_warna" value="<?php echo $txt_warna; ?>">
  	<input type="hidden" name="temp_gudang" value="<?php echo $gudang; ?>">
  	<input type="hidden" name="temp_awal" value="<?php echo $awal; ?>">
  	<input type="hidden" name="temp_akhir" value="<?php echo $akhir; ?>">
  	<input type="hidden" name="temp_pilihan" value="<?php echo $pilihan; ?>">
 	 
	<input type="submit" name="ekspor" id="ekspor" value="     Export     " /></td>
  </form> -->
  
  <?php
    if($_REQUEST[action]=='search'){
		
	}else{
	  include_once('footer.php');	
	   die();	
	}
  ?>
  <br>
  &nbsp;<input type="button" name="ekspor" id="ekspor" value="     Export     " />
  <br>&nbsp;<br>
<div id="loading"><font color="#FF0000"><blink>LOADING..............</blink></font></div>
  <table border="0" width="1200" style="font-size: 8pt">
          <tr>
            <td background="images/footer.gif" align="center" width="20" height="30"><strong>NO</strong></td>
            <td background="images/footer.gif" align="center" width="102"><strong>Kode</strong></td>
			<td background="images/footer.gif" align="center" width="268"><strong>Nama</strong></td>
			<td width="176" align="center" background="images/footer.gif"><strong>Warna</strong></td>
			<td width="300" align="center" background="images/footer.gif"><strong>Ukuran</strong></td>
			<td background="images/footer.gif" align="center" width="50"><strong>Qty</strong></td>
			<td background="images/footer.gif" align="center" width="125"><strong>TotalHPP</strong></td>
			<td background="images/footer.gif" align="center" width="125"><strong>TotalHPJ</strong></td>
          </tr>
	<?php 
	$sql_tbhn='';
	
	if($jenis=='nama'){
		$code_jenis="  and  p.nama like '%$cari%'";
	}else if($jenis=='kode'){
		$code_jenis="  and  p.kode like '$cari%'";
	}else{
		$code_jenis="";
	}
	
	
	if(!empty($txt_warna)){
		$sql_tbhn.=" AND w.warna like '%$txt_warna%'";
	}
	if(!empty($srcsize)){
		$sql_tbhn.=" AND p.kode_size = '$srcsize'";
	}
	
	
	$sql2="SELECT dpd.kd_produk AS model,p.nama,sum(dpd.qty),s.size,w.warna FROM do_produk AS dp INNER JOIN
do_produk_detail AS dpd ON (dpd.no_do=dp.no_do) INNER JOIN produk AS p ON (p.kode=dpd.kd_produk)
left join mst_size as s on (s.kode=p.kode_size) left join mst_warna as w on (w.kode=p.kode_warna)
 WHERE  $firstcode
(dp.no_do NOT LIKE '%mst%' and dp.no_do NOT LIKE '%btl%' and dp.no_do NOT LIKE '%test%') 
AND dp.tanggal BETWEEN  '$awal 00:00:00' AND '$akhir 23:59:59' AND dp.keterangan!='P100S' 
  $code_jenis $code_pilihan  $sql_tbhn
GROUP BY dpd.kd_produk order by w.kode,p.kode_size ";

// die($sql2);
 
$query=mysql_query($sql2)or die($sql2);
$jmlData=mysql_num_rows($query);
$hal=$_REQUEST['hal'];
if($hal==""){
	$hal="0";
}
$jmlHal=3000;
$awal1=$hal*$jmlHal;
$tothal=ceil($jmlData/$jmlHal);
$sql=$sql2." limit $awal1,$jmlHal";

if(($username=='budi-it')||($username=='rian-it')){
    //echo $sql;	
}
$query=mysql_query($sql)or die($sql);
$no=$awal1;
while(list($kode,$nama,$qty,$ukuran,$warna)=mysql_fetch_array($query)){
$no++;
  $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
                $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
$sql="select model from  mst_model where kode_basic_item=substring('$kode',1,1) and kode_kategori=substring('$kode',2,1) and kode_kelas=substring('$kode',3,1) and kode_style=substring('$kode',4,2) and kode=substring('$kode',6,2)";
$resrian=mysql_query($sql)or die($sql);
list($nami)=mysql_fetch_array($resrian);
list($nami)=mysql_fetch_array($resrian);
if($nami!=""){
	$nama=$nami;
}
?>
 <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
	<td align="center" height="25"><?php echo $no?></td>
	<td class="kode"><?php echo $kode?></td>
	<td><?php echo $nama?></td>
	<td align="center"><?php echo $warna?></td>
	<td align="center"><?php echo $ukuran?></td>
	<td align="right"><?php echo $qty;$tqty+=$qty;?>&nbsp;</td>
	<td align="right" id="hpp<?php echo $kode?>">0</td>
	<td align="right" id="hpj<?php echo $kode?>">0</td>
	
</tr><?php
$rekam_kode.=$kode.",";
}
$rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
?>
<tr bgcolor="#006600">
	<td colspan="5" height="25" class="white">Subtotal</td>
	<td align="right" id="qtytotal" class="white"><?php echo number_format($tqty,"0",".",",");?></td>
	<td align="right" class="hpp">0</td>
	<td align="right" class="hpj">0</td>
	
</tr>
<tr bgcolor="#006600">
	<td colspan="5" height="25" class="white">Total</td>
	<td align="right" class="white" id="totalsemua">0</td>
	<td align="right" id="hppsemua" >0</td>
	<td align="right" id="hpjsemua">0</td>
	
</tr>
<tr bgcolor="#006600">
	<td colspan="5" height="25" class="white">Retur</td>
	<td align="right" id="qty_retur" class="white">0</td>
	<td align="right" class="hpp_retur">0</td>
	<td align="right" class="hpj_retur">0</td>
	
</tr>

<tr bgcolor="#006600">
	<td colspan="5" height="25" class="white">Total Keseluruhan</td>
	<td align="right" id="qty_bersih" class="white">0</td>
	<td align="right" class="hpp_bersih">0</td>
	<td align="right" class="hpj_bersih">0</td>
	
</tr>
  </table>

</fieldset>
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
                <span class="hal" onclick="pindah('<?php echo $tothal-1?>')">Last </span>
&nbsp;&nbsp;  Total Data   <?php echo $jmlData?>        </td>
        </tr>
		
</table>

<script>
function stok(awal,akhir,parameter,gudang){

			try{
			  $.ajax({
			  type: 'POST',
			  url: 'stok_do_produk1.php',
			  data: {awal:awal,akhir:akhir,parameter:parameter,gudang:gudang},
			  dataType: 'json',
			  success: function(data){
				$.each(data, function(key, val) 
             		{
						
							$("#hpp"+val.kode).text(parseFloat(val.hpp).formatMoney(0, '.', ','));
							$("#hpj"+val.kode).text(parseFloat(val.hpj).formatMoney(0, '.', ','));
            		});	
					sukses();
			  }
			});	
			   
			}catch(err){alert(err.message);}
}



function stok1(awal,akhir,parameter,gudang,pilihan,cari,jenis,warna,size){

			try{
			  $.ajax({
			  type: 'POST',
			  url: 'stok_do_produk2.php',
			  data: {awal:awal,akhir:akhir,parameter:parameter,gudang:gudang,pilihan:pilihan,cari:cari,jenis:jenis,w:warna,sz:size},
			  dataType: 'json',
			  success: function(data){
				  
 
				$.each(data, function(key, val) 
             		{
						$("#totalsemua").text(parseFloat(val.qty).formatMoney(0, '.', ','));
						$("#hppsemua").html("<font color='#FFFFFF'><i><b>"+parseFloat(val.hpp).formatMoney(0, '.', ',')+"</b></i></font>");
						$("#hpjsemua").html("<font color='#FFFFFF'><i><b>"+parseFloat(val.hpj).formatMoney(0, '.', ',')+"</b></i></font>");
            		});	
					akhiri();
			  }
			});	
			   
			}catch(err){alert(err.message);}
}

var hpp=0;
var hpj=0;
function sukses(){
	$(".kode").each(function(){
		hpp+=parseFloat(($("#hpp"+$(this).text()).text()).replace(/,/g, ''), 10);
		hpj+=parseFloat(($("#hpj"+$(this).text()).text()).replace(/,/g, ''), 10);
	});
	$(".hpp").text((hpp).formatMoney(0, '.', ',')).addClass('white');
	$(".hpj").text((hpj).formatMoney(0, '.', ',')).addClass('white');
	akhiri();
	
}

function pindah(hal){
	$("#hal").val(hal);
	$("#submit").click();
	
}

function retur(awal,akhir,parameter,cari,gudang,pilihan,jenis,warna,size){

			try{
			  $.ajax({
			  type: 'POST',
			  url: 'retur_do_produk1.php',
			  data: {awal:awal,akhir:akhir,parameter:parameter,cari:cari,gudang:gudang,pilihan:pilihan,w:warna,sz:size,jenis:jenis},
			  dataType: 'json',
			  success: function(data){
			 
				$.each(data, function(key, val) 
             		{
					
							$("#qty_retur").text(parseFloat(val.qty).formatMoney(0, '.', ',')).addClass('white');
							$(".hpp_retur").text(parseFloat(val.hpp).formatMoney(0, '.', ',')).addClass('white');
							$(".hpj_retur").text(parseFloat(val.hpj).formatMoney(0, '.', ',')).addClass('white');
            		});	
					
					akhiri();
					
			  }
			});	
			   
			}catch(err){alert(err.message);}
}
var ini=0;
function akhiri(){
	ini++;
	if(ini==3){
		var sisaqty=parseFloat($("#totalsemua").text().replace(/,/g, ''), 10)-parseFloat($("#qty_retur").text().replace(/,/g, ''), 10);
		var sisahpp=parseFloat($("#hppsemua").text().replace(/,/g, ''), 10)-parseFloat($(".hpp_retur").text().replace(/,/g, ''), 10);
		
		var sisahpj=parseFloat($("#hpjsemua").text().replace(/,/g, ''), 10)-parseFloat($(".hpj_retur").text().replace(/,/g, ''), 10);
		$("#qty_bersih").text((sisaqty).formatMoney(0, '.', ',')).addClass('white');
		$(".hpp_bersih").text((sisahpp).formatMoney(0, '.', ',')).addClass('white');
		$(".hpj_bersih").text((sisahpj).formatMoney(0, '.', ',')).addClass('white');
		
		$(document).ready(function(){
			$("#loading").hide();
		});
	}
	
	
}

stok('<?php echo $awal?>','<?php echo $akhir?>','<?php echo $rekam_kode?>','<?php echo $gudang?>');
stok1('<?php echo $awal?>','<?php echo $akhir?>','<?php echo $rekam_kode?>','<?php echo $gudang?>','<?php echo $pilihan?>','<?php echo $cari?>','<?php echo $jenis?>','<?php echo $txt_warna;?>','<?php echo $srcsize;?>');
retur('<?php echo $awal?>','<?php echo $akhir?>','<?php echo $rekam_kode?>','<?php echo $cari?>','<?php echo $gudang?>','<?php echo $pilihan?>','<?php echo $jenis?>','<?php echo $txt_warna;?>','<?php echo $srcsize;?>');
</script>
<?php 
#mysql_close(); ?>
<?php include_once "footer.php"; ?>