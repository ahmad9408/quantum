<?php $content_title="Rekap pengiriman produksi v2"; ?>
 
<?php  
 

/*
edit by xtreme: 25 jan2014

*/
@$berdasar	= $_POST['berdasar'];
@$pilihan	= $_POST['pilihan'];
if($berdasar==""){
	$berdasar="kode";
}

$sql_cache=' SQL_CACHE ';



    include('header.php');
	
	
	@$cari	=$_REQUEST['cari'];
	@$action = $_GET['action'];
	if($action==""){
		$cari 	= "KAS";
	}
 
	if(@$_POST['gudang']=="0"){
		 $firstcode="";
		 $gudang="0";
	}else
			@$gudang	= $_POST['gudang'];
			 
			$firstcode	=  " dp.gudang like '$gudang%' and ";
           /* if($_POST[gudang]=='GD.002'){
                $firstcode="dp.gudang='GD.002' and  ";
                $gudang='GD.002';
            }else  if($_POST[gudang]=='GD.004'){
                $firstcode="dp.gudang='GD.004' and  ";
               $gudang='GD.004';
           }else  if($_POST[gudang]=='GD.005'){
                $firstcode="dp.gudang='GD.004' and  ";
               $gudang='GD.005';
           } else {
                $firstcode="dp.gudang='GD.001' and ";
                 $gudang='GD.001';
            } */
	@$awal=$_REQUEST[awal];
	@$akhir=$_REQUEST[akhir];
	if($awal==""){
		$awal=date("Y-m-d");
		$akhir=$awal;
	}
if($berdasar==""){
	$terusan="";
}else{
	if($berdasar=="kode"){
		$terusan=" and  dpd.kd_produk like '$cari%'";
	}else{
		$terusan=" and  p.nama like '%$cari%'";
	}
}	


$pabrik=$_POST['pabrik'];

if(empty($pabrik)){
	 $sql_tambahan='';
}else{
	$sql_tambahan=" AND dp.keterangan ='$pabrik' ";
}


 ?> 
 <style>
 .white {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-style: italic;
	font-weight: bold;
	color: #FFFFFF; 
 }
 .myLink{
	 text-decoration:underline;
	 color:#00F;
	 cursor:pointer;
	 }
 </style>
 <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
<!-- .replace(/,/g, ''), 10 -->

</script>
<?php 
if($_SESSION['username']=="rian-itu"){
	echo"<CENTER><img src='undercontruction.jpg' width='500'><br>
	<BR>MAAF HALAMAN INI SEMENTARA TIDAK BISA DI GUNAKAN, KARENA ADA PERBAIKAN. <BR>
	-- RIAN HIDAYAT --<BR>
	(IT DEVELOPMENT MANAGER)
	 </CENTER>
	";
	include_once "footer.php";die;	
	}
?>
 <form method="post" action="rekap_pengiriman_3.php?action=search" name="f1">
 <fieldset id="fieldfoto">
	<span id="sfoto"></span>
 </fieldset>
 <fieldset id="fieldsearch">
 <table border="0">
 	<tr>
    	<td>Pencarian</td>
    	<td width="2">:</td>
    	<td><select id="berdasar" name="berdasar">
		<option value="">Pilih</option>
		<option value="kode" <?php if($berdasar=='kode'){echo"selected";}?>>Kode</option>
		<option value="nama" <?php if($berdasar=='nama'){echo"selected";}?>>Nama</option>
		</select> <input type="text" name="cari" id="cari"  size="30" value="<?php echo $cari?>" /></td>
    </tr>
    <tr>
    	<td>Periode</td>
        <td>:</td>
    	<td><script language="JavaScript" src="calendar_us.js"></script>
              <link rel="stylesheet" href="calendar.css" />
              <!-- calendar attaches to existing form element -->
              
            Dari &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:  <input type="text" name="awal" readonly id="awal" value="<?php echo $awal; ?>" size="16"/>
            &nbsp;
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'awal'
              });
            </script>
            </td>
    </tr>
     <tr>
    	<td> </td>
        <td></td>
    	<td> 
              
           Sampai :
                   <input type="text" name="akhir" readonly id="akhir" value="<?php echo $akhir; ?>" size="16"/>
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
    	<td>Tujuan</td>
        <td>:</td>
    	<td>  <?php $sql="select   id,nama from gudang_distribusi where jenis=1 and id not in  ('GD.003')";
		 echo $gudang;
		 
		?>
		<select name="gudang">
		<option value="">Pilih All</option>
		<?php $resi	= mysql_query($sql)or die($sql);
		while(list($id,$nama)	= mysql_fetch_array($resi)){
			?><option value="<?php echo $id?>" <?php if($id==$gudang){echo"selected";}?>><?php echo $nama?></option><?php 
		}?>
		</select> </td>
    </tr>
    
    
     <tr>
    	<td>Pabrik</td>
        <td>:</td>
    	<td> <select name="pabrik">
       <option value="" >--select--</option>
      <?php
	     $sql="SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 ";
		 $res=mysql_query($sql);
		 while(list($id_pabrik,$nama_pabrik)=mysql_fetch_array($res)){
			 if($pabrik==$id_pabrik){
				 $selected='selected'; 
			 }else{
				 $selected=''; 
			 }
			 echo "<option value='$id_pabrik' $selected > [ $id_pabrik ] $nama_pabrik </option>";
		 }
	  
	  ?>
	    
	    
	    </select> </td>
    </tr>
    
    
    <tr>
    	<td>Pilihan</td>
        <td>:</td>
    	<td> <select name="pilihan" id="pilihan" >
                <option value="">Pilih</option>
             <?php
			      $sql="SELECT   pp.pilihan  FROM produk_pilihan_mst  AS pp  WHERE pp.aktif=1"; 
				  $res 		= mysql_query($sql)or die($sql);
				  while(list($pil)	= mysql_fetch_array($res)){
				   ?>
                   <option value="<?php echo $pil; ?>" <?php if($pilihan==$pil){echo"selected";}?>><?php echo $pil; ?></option>
                 <?php  	 
				 }
			   ?>
            </select>   </td>
    </tr>
    
    
     <tr>
    	<td> </td>
        <td></td>
    	<td>  <input type="submit" name="submit" value="Cari" /></td>
    </tr>
    
 </table>
 
 </fieldset>
<div id="loading"><font color="#FF0000"><blink>LOADING..............</blink></font></div>
  <table border="0" width="900" style="font-size: 8pt">
          <tr>
            <td background="images/footer.gif" align="center" width="20" height="30"><strong>NO</strong></td>
            <td background="images/footer.gif" align="center" width="80"><strong>Kode</strong></td>
			<td background="images/footer.gif" align="center" width="200"><strong>Nama</strong></td>
			<td background="images/footer.gif" align="center" width="50"><strong>Qty</strong></td>
			<td background="images/footer.gif" align="center" width="125"><strong>TotalHPP</strong></td>
			<td background="images/footer.gif" align="center" width="125"><strong>TotalHPJ</strong></td>
			<td background="images/footer.gif" width="250" align="center">Action </td>
          </tr>
	<?php
	//echo $_SESSION['id_group1'];
	 
	 if($_SESSION['id_group1']=='38'){
	 
		if($gudang==''||$gudang=='0'){
		 
			$terusan_dengan=" ";
		}else if($gudang=='GD.002'){
			$terusan_dengan=" and pb.st_acc='1' ";
		}
		else{
			$terusan_dengan="  and pb.st_acc='0' ";
		}
	}
	 
	if($pilihan!=""){
	$inner_qf 		= " inner join produk_pilihan as pp on (pp.id_barang=dpd.kd_produk) ";
	$terusan_qf 	= " and pp.pilihan='$pilihan' ";

	}
	 
	/*if(($_SESSION['username']=='cekppic')||($_SESSION['username']=='cekppicpusat')||($_SESSION['username']=='lina_ppic')||($_SESSION['username']=='nani_ppic')){
		$terusan_dengan=" and substring(dp.no_do,1,5)!='P1001' ";
		} */
	 $sql="SELECT SUBSTRING(dpd.kd_produk,1,7) AS model,p.nama,sum(dpd.qty),SUM(dpd.harga*dpd.qty) AS hpj,SUM(dpd.hpp*dpd.qty) AS hpp,
f.nama_file	 FROM do_produk AS dp INNER JOIN
do_produk_detail AS dpd ON 
(dpd.no_do=dp.no_do) 
INNER JOIN produk AS p ON 
(p.kode=dpd.kd_produk)
left join pabrik as pb on 
(pb.id=substring(dp.no_do,1,5))
left join mst_model_foto as f on 
(f.kode_model=SUBSTRING(dpd.kd_produk,1,7))
$inner_qf 
 WHERE  $firstcode
(dp.no_do NOT LIKE '%mst%' and dp.no_do NOT LIKE '%btl%' and dp.no_do NOT LIKE '%test%') 
AND dp.tanggal BETWEEN  '$awal 00:00:00' AND '$akhir 23:59:59' AND dp.keterangan!='P100S'   $terusan $sql_tambahan $terusan_dengan $terusan_qf 
GROUP BY SUBSTRING(dpd.kd_produk,1,7)";
 



if($username=='budi-it' || $username=='iwan-it'){
   echo $sql;	
}


$query=mysql_query($sql)or die($sql);
while(list($kode,$nama,$qty,$hpj,$hpp,$file)=mysql_fetch_array($query)){
$no++;
  $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
                $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
$sql="select model from  mst_model where kode_basic_item=substring('$kode',1,1) and kode_kategori=substring('$kode',2,1) and kode_kelas=substring('$kode',3,1) and kode_style=substring('$kode',4,2) and kode=substring('$kode',6,2)";
$resrian=mysql_query($sql)or die($sql);
list($nami)=mysql_fetch_array($resrian);
if($nami!=""){
	$nama=$nami;
}
?>
 <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
	<td align="center"><?php echo $no?></td>
	<td class="kode"><?php echo $kode?></td>
	<td><?php 
	if($file==""){
		echo $nama;
	}else {
		?><span class="mylink" onclick=getFetchFoto('<?php echo $file ?>')><?php echo $nama?></span><?php
	}?></td>
	<td align="right"><?php echo "<span class='mylink' onclick=getMove('$kode')>".number_format($qty)."</span>";$tqty+=$qty;?>&nbsp;</td>
	<td align="right" id="hpp<?php echo $kode?>"><?php echo number_format($hpp);?></td>
	<td align="right" id="hpj<?php echo $kode?>"><?php echo number_format($hpj)?></td>
	<td align="center"><span class="myLink" onclick="showDetail('<?php echo $kode?>','<?php echo $awal?>','<?php echo $akhir?>','<?php echo $pabrik ?>','<?php echo $gudang ?>')">Detail</span>
	 |
	<a href="do_produk_list.php?search=yes&tgl1=<?php echo $awal?>&tgl2=<?php echo $akhir?>&search_model=<?php echo $nama?>&kode=<?php echo $kode;?>" target="_blank">Detail Surat Jalan</a></td>
</tr><?php
$rekam_kode.=$kode.",";
}
$rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
?>
<tr bgcolor="#006600">
	<td colspan="3" height="25" class="white">Total</td>
	<td align="right" id="qtytotal" class="white"><?php echo number_format($tqty,"0",".",",");?></td>
	<td align="right" class="hpp">0</td>
	<td align="right" class="hpj">0</td>
	<td></td>
</tr>
<tr bgcolor="#006600">
	<td colspan="3" height="25" class="white">Retur</td>
	<td align="right" id="qty_retur" class="white">0</td>
	<td align="right" class="hpp_retur">0</td>
	<td align="right" class="hpj_retur">0</td>
	<td></td>
</tr>

<tr bgcolor="#006600">
	<td colspan="3" height="25" class="white">Total Keseluruhan</td>
	<td align="right" id="qty_bersih" class="white">0</td>
	<td align="right" class="hpp_bersih">0</td>
	<td align="right" class="hpj_bersih">0</td>
	<td></td>
</tr>
  </table>
</form>
<script>
function stok(awal,akhir,parameter,gudang,pabrik){
var vpilihan 		= $("#pilihan").val();
			try{
			  $.ajax({
			  type: 'POST',
			  url: 'stok_do_produk_3.php',
			  data: {awal:awal,akhir:akhir,parameter:parameter,gudang:gudang,p:pabrik,pilihan:vpilihan},
			  dataType: 'json',
			  success: function(data){
				  // alert(data);
				$.each(data, function(key, val) 
             		{
						
							//$("#hpp"+val.kode).text(parseFloat(val.hpp).formatMoney(0, '.', ','));
							//$("#hpj"+val.kode).text(parseFloat(val.hpj).formatMoney(0, '.', ','));
            		});	
					sukses();
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

function retur(awal,akhir,parameter,cari,gudang,pabrik){
var berdasar=$("#berdasar").val();
var vpilihan 		= $("#pilihan").val();
 
			try{
			  $.ajax({
			  type: 'POST',
			  url: 'retur_do_produk_3.php',
			  data: {awal:awal,akhir:akhir,parameter:parameter,cari:cari,gudang:gudang,p:pabrik,berdasar:berdasar,pilihan:vpilihan},
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
	if(ini==2){
		var sisaqty=parseFloat($("#qtytotal").text().replace(/,/g, ''), 10)-parseFloat($("#qty_retur").text().replace(/,/g, ''), 10);
		var sisahpp=parseFloat($(".hpp").text().replace(/,/g, ''), 10)-parseFloat($(".hpp_retur").text().replace(/,/g, ''), 10);
		
		var sisahpj=parseFloat($(".hpj").text().replace(/,/g, ''), 10)-parseFloat($(".hpj_retur").text().replace(/,/g, ''), 10);
		$("#qty_bersih").text((sisaqty).formatMoney(0, '.', ',')).addClass('white');
		$(".hpp_bersih").text((sisahpp).formatMoney(0, '.', ',')).addClass('white');
		$(".hpj_bersih").text((sisahpj).formatMoney(0, '.', ',')).addClass('white');
		
		$(document).ready(function(){
			$("#loading").hide();
		});
	}
	
	
}

function showDetail(kode,awal,akhir,pabrik,gudang){
	$('#f_kode').val(kode);
	$('#f_awal').val(awal);
	$('#f_akhir').val(akhir);
	$('#f_pabrik').val(pabrik);
	$('#f_gudang').val(gudang);	
	$('#frmDetail').submit();

}

function getFetchFoto(foto){
 
	$("#fieldsearch").hide();
	$("#fieldfoto").show();
	$("#sfoto").html("<img src='ym_it_rabbani/temp/"+foto+"' width='250'><br><span class='mylink' onclick=kembalikan();>Hide</span>");
}

function kembalikan(){
	$("#fieldsearch").show();
	$("#fieldfoto").hide();
}



<?php
  if($no>0){
?>

stok('<?php echo $awal?>','<?php echo $akhir?>','<?php echo $rekam_kode?>','<?php echo $gudang?>','<?php echo $pabrik?>');
retur('<?php echo $awal?>','<?php echo $akhir?>','<?php echo $rekam_kode?>','<?php echo $cari?>','<?php echo $gudang?>','<?php echo $pabrik?>');

<?php
  }else{
	  echo '$("#loading").hide()';
  }//end if no
?>
$("#fieldsearch").show();
$("#fieldfoto").hide();

function getMove(model){
	$("#f_jenis").val($("#berdasar").val());
	$("#f_cari").val(model);
	$("#f_awal").val($("#awal").val());
	$("#f_akhir").val($("#akhir").val());
	$("#frmDetail").attr("action","rekap_pengiriman_produk_v2.php?action=search");
	$("#frmDetail").submit();
	$("#frmDetail").attr("action","isi_detail_rekap_3.php");
}

</script>
<form action="isi_detail_rekap_3.php" method="POST" target="_blank" id="frmDetail" style="display:none"	>
<input name="kode" id="f_kode" type="text" />
<input name="awal" id="f_awal" type="text" />
<input name="akhir" id="f_akhir" type="text" />
<input name="pabrik" id="f_pabrik" type="text" />
<input name="gudang" id="f_gudang" type="text" />
<input name="jenis" id="f_jenis" type="text" />
<input name="cari" id="f_cari" type="text" />

</form>

<?php include_once "footer.php"; ?>