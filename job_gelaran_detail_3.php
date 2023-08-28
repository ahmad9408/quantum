<?php $content_title="DETIL Cutting Order"; include_once "header.php" ?>
<?php include_once "clsaddrow.php";?>

<script src="jquery.js"></script>
	<script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

</script>
<style>
.mylink {
		color:#03F;
		cursor:pointer;	
	}
</style>
<?php
	$no_po=sanitasi($_GET["no_po"]);
	$no_co=sanitasi($_GET["no_co"]);
	$sql="select pabrik.id,pabrik.nama from pabrik,job_gelaran where pabrik.id=job_gelaran.pabrik and job_gelaran.no_co='$no_co' and job_gelaran.no_po='$no_po'";
	$query=mysql_query($sql)or die($sql);
	list($id_pabrik,$pabrik)=mysql_fetch_array($query);
	
	$sql="select bahan,model,c_produk,c_variasi,kode_produksi,catatan,pabrik.nama,catatan_kaki from job_gelaran_detail_rian,pabrik where no_po='$no_po' and no_co='$no_co' and pabrik.id=job_gelaran_detail_rian.pabrik_sewing";
	$query=mysql_query($sql)or die($sql);
	list($bahan,$model,$c_produk,$c_variasi,$kode_produksi,$catatan,$pabrik_sewing,$catatan_kaki)=mysql_fetch_array($query);
	if($pabrik_sewing==""){
		$pabrik_sewing="<font color='#FF0000'>".$pabrik."</font>";
	}
	if(($c_produk=="")||($c_produk=="0")){
		$c_produk="<font color='#FF0000'>0</font>";
	}
	
	if(($c_variasi=="")||($c_variasi=="0")){
		$c_variasi="<font color='#FF0000'>0</font>";
	}
	if($bahan==""){
		$bahan="<font color='#FF0000'>xxxx</font>";
		
	}
	
	if($kode_produksi==""){
		$kode_produksi="<font color='#FF0000'>xxxx</font>";
	}
	
	if($catatan==""){
		$catatan="<font color='#FF0000'>xxxx</font>";
	}
	if($catatan_kaki==""){
		$catatan_kaki="<font color='#FF0000'>xxxx</font>";
	}
	
	
	
	
	
	if(mysql_num_rows($query)==0){
		 $sql="select substring(kd_produk,1,7) from job_gelaran_detail where no_co='$no_co' and no_po='$no_po'"; 
		$query=mysql_query($sql)or die($sql);
		 list($kd)=mysql_fetch_array($query);
		 $sql="select model from mst_model where  CONCAT(kode_basic_item,kode_kategori,kode_kelas,kode_style,kode)='$kd'";
		 $query=mysql_query($sql)or die($sql);
		 list($model)=mysql_fetch_array($query);
		$sql="insert into job_gelaran_detail_rian (no_po,no_co,model,pabrik_sewing)values
		('$no_po','$no_co','$model','$id_pabrik')";
		$query=mysql_query($sql); 
		
	}
	$arrcount=array();
	$arrwarna=array();
	
	$sql="SELECT SUBSTRING(kd_produk,1,7),SUBSTRING(kd_produk,13,3),COUNT(*),mst_warna.warna AS kode FROM job_gelaran_detail,mst_warna WHERE 
no_po='$no_po' AND no_co='$no_co' AND mst_warna.kode=SUBSTRING(kd_produk,13,3) 
GROUP BY SUBSTRING(kd_produk,1,7),SUBSTRING(kd_produk,13,3) ";
$query=mysql_query($sql)or die($sql);
while(list($kode_tujuh,$kode_warna,$jumlah_row,$nama_warna)=mysql_fetch_array($query)){
	$arrcount[$kode_warna]=$jumlah_row;
	$arrwarna[$kode_warna]=$nama_warna;	
}
	
?>
<style type="text/css">
<!--
.style3 {font-size: 12px}

th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FFFFFF;
}

#supplier-border {
	border-bottom-color:#FFFFFF;
	border-bottom-width: thin;
	border-bottom-style: solid;
}
ol {
	margin-left:8px;
	padding-left:8px;
}

-->
</style>

	<fieldset>
<script>
function ambilkilo(qty,kode_war){

	$(document).ready(function(){
	var qty_bahan=parseFloat($(".c_bahan").text())*parseFloat(qty);
		$("#bahan_baku_"+kode_war).text((qty_bahan).formatMoney(1, '.', ','));
		var roll=qty_bahan/25;
		$("#roll_baku_"+kode_war).text((roll).formatMoney(1, '.', ','));
		
	var qty_variasi=parseFloat($(".c_variasi").text())*parseFloat(qty);
		$("#variasi_"+kode_war).text((qty_variasi).formatMoney(0, '.', ','));
		var roll_variasi=qty_variasi/25;
		$("#roll_variasi_"+kode_war).text((roll_variasi).formatMoney(1, '.', ','));
		
	});
}

function ubah_supplier(warna,klu){

	if(klu=='bahan'){
	var ing=$("#supp_"+warna);
	}else{
	var ing=$("#svariasi_"+warna);
	}
	ing.html("<select name='supplier' id='supplier'>"+
	"<?php $sql="select id,nama from supplier_rm where nama!='-' order by nama ";$res=mysql_query($sql)or die($sql);
	while(list($id,$supp)=mysql_fetch_array($res)){
		echo"<option value='$supp'>$supp</option>";
	}?></select><input type='hidden' name='jenis' value='"+klu+"' id='klu'><input type='button' name='submit' value='Ubah'  onclick=proses_ubah("+warna+")>");
}

function proses_ubah(warna){
var jenis=$("#klu").val();
var supp=$("#supplier").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	
	var data="warna="+warna;
		data+="&supplier="+supp;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&jenis="+jenis;
	
	$.post('job_gelaran_ubah_supplier.php',data,function(response){
			if(jenis=='bahan'){
			$("#supp_"+warna).text(response);}else{
			$("#svariasi_"+warna).text(response);
			}
	});	
	
	
	
}

function kode_produksi(){
	var kode_produksi=$("#kode_produksi").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="kode_produksi="+kode_produksi;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_kode_produksi.php',data,function(response){
		$(".kode_produksi").text(response);
	});
}

function bahan(){
	var bahan=$("#bahan").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="bahan="+bahan;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_bahan.php',data,function(response){
		$(".bahan").text(response);
	});
}

function c_bahan(){
	var bahan=$("#c_bahan").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="bahan="+bahan;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_c_bahan.php',data,function(response){
		$(".c_bahan").text(response);
		document.location.reload();
	});
}

function c_variasi(){
	var variasi=$("#c_variasi").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="bahan="+variasi;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_c_variasi.php',data,function(response){
		$(".c_variasi").text(response);
		document.location.reload();
	});
}


function pabrik(){
	var pabrik1=$("#pabrik1").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="pabrik="+pabrik1;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_pabrik.php',data,function(response){
	
		$(".sewing").text(response).addClass('style3');
	});
}



function catatan(){
	var catatan=$("#catatan").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="catatan="+catatan;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_catatan.php',data,function(response){
		document.location.reload();
	});
}

function simpan_catatan_kaki(){
	var catatan_kaki=$("#catatan_kaki").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="catatan_kaki="+catatan_kaki;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_catatan_kaki.php',data,function(response){
		document.location.reload();
	});
}

$(document).ready(function(){
	$(".kode_produksi").dblclick(function(){
		var kode_produksi=$(this).text();
		$(this).html("<input type='text' name='kode_produksi' id='kode_produksi' value='"+kode_produksi+"'> <input type='button' name='button' value='Ubah' onclick='kode_produksi();'>");
	});
	
	
	$(".catatan").dblclick(function(){
		var catatan=$("#catatanvalue").val();
		$(this).html("<textarea id='catatan' cols='40' rows='2'>"+catatan+"</textarea> <input type='button' name='button' value='Ubah' onclick='catatan();'>");
	});
	
	$(".bahan").dblclick(function(){
		var bahan=$(this).text();
		$(this).html("<input type='text' name='bahan' id='bahan' value='"+bahan+"'> <input type='button' name='button' value='Ubah' onclick='bahan();'>");
	});
	
	$(".c_bahan").dblclick(function(){
		var c_bahan=$(this).text();
		$(this).html("<input type='text' name='c_bahan' id='c_bahan' value='"+c_bahan+"'> <input type='button' name='button' value='Ubah' onclick='c_bahan();'>");
	});
	
	
	$(".c_variasi").dblclick(function(){
		var c_variasi=$(this).text();
		$(this).html("<input type='text' name='c_variasi' id='c_variasi' value='"+c_variasi+"'> <input type='button' name='button' value='Ubah' onclick='c_variasi();'>");
	});
	
	$(".catatan_kaki").dblclick(function(){
		var catatan_kaki=$("#kakivalue").val();
		$(this).html("<textarea name='catatan_kaki' id='catatan_kaki' cols='40' rows='2'>"+catatan_kaki+"</textarea> <input type='button' name='button' value='Ubah' onclick='simpan_catatan_kaki();'>");
	});
	
	//rian ini
	
	$(".sewing").dblclick(function(){
		var c_variasi=$(this).text();
		$(this).html("<select name='pabrik1' id='pabrik1'>"+
	"<?php $sql="select id,nama from pabrik where nama not like '%simulasi%' and status='1' order by nama ";$res=mysql_query($sql)or die($sql);
	while(list($id_pabrik,$nama_pabrik)=mysql_fetch_array($res)){
		echo"<option value='$id_pabrik'>$id_pabrik-$nama_pabrik</option>";
	}?></select> <input type='button' name='button' value='Ubah' onclick='pabrik();'><input type='button' name='refresh' value='Cancel' onclick='document.location.reload();'");
	});
	
	
	
	
	
});

function ass_landing(url,parameter,no_co,no_po){

		try{
			  $.ajax({
			  type: 'POST',
			  url: url,
			  data: {parameter:parameter,no_co:no_co,no_po:no_po},
			  dataType: 'json',
			  success: function(data){
							$.each(data, function(key, val) 
             				{
								
							var qty=parseFloat(val.qty);
								$("#ass_"+val.kode).text((qty).formatMoney(0, '.', ','));
            				});			
			  }
			});	
			   
			}catch(err){alert(err.message);}
}

function ubah_real_bahan(warna,jenis){ 
if(jenis=='bahan'){
real=$("#roll_bahan_real_"+warna).text();
	$("#roll_bahan_real_"+warna).html("<input type='text' name='real_roll' id='real_roll' value='"+real+"'> <input type='button' name='button' value='Ubah' onclick='real_bahan("+warna+");'>"); }else{
	
	real=$("#roll_variasi_real_"+warna).text();
	$("#roll_variasi_real_"+warna).html("<input type='text' name='variasi_roll' id='variasi_roll' value='"+real+"'> <input type='button' name='button' value='Ubah' onclick='variasi_bahan("+warna+");'>");

}
}


function ubah_real_bahan_kg(warna,jenis){ 
if(jenis=='bahan'){
real=$("#kg_bahan_real_"+warna).text();
	$("#kg_bahan_real_"+warna).html("<input type='text' name='real_kg' id='real_kg' value='"+real+"'> <input type='button' name='button' value='Ubah' onclick='real_bahan_kg("+warna+");'>"); }else{
	
	real=$("#kg_variasi_real_"+warna).text();
	$("#kg_variasi_real_"+warna).html("<input type='text' name='variasi_kg' id='variasi_kg' value='"+real+"'> <input type='button' name='button' value='Ubah' onclick='real_variasi_kg("+warna+");'>");

}
}


function variasi_bahan(warna){
if(warna=='0'){
warna='000';
}
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var isi_value=$("#variasi_roll").val();

	var data="warna="+warna;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&isi_value="+isi_value;
		data+="&variasi=variasi";	
	$.post('job_gelaran_ubah_real_bahan.php',data,function(response){
		document.location.reload();
	}); 

		
		
}

function real_bahan(warna){

if(warna=='0'){
warna='000';
}
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var isi_value=$("#real_roll").val();

	var data="warna="+warna;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&isi_value="+isi_value;
			
	$.post('job_gelaran_ubah_real_bahan.php',data,function(response){
		document.location.reload();
	}); 

		
		
}


function real_bahan_kg(warna){

if(warna=='0'){
warna='000';
}
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var isi_value=$("#real_kg").val();

	var data="warna="+warna;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&isi_value="+isi_value;
			
	$.post('job_gelaran_ubah_real_bahan_kg.php',data,function(response){
		document.location.reload();
	}); 
		
		
}


function real_variasi_kg(warna){

if(warna=='0'){
warna='000';
}
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var isi_value=$("#variasi_kg").val();

	var data="warna="+warna;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&isi_value="+isi_value;
		data+="&variasi=variasi";		
	$.post('job_gelaran_ubah_real_bahan_kg.php',data,function(response){
		document.location.reload();
	}); 
		
		
}

function ubah_kode_produksi_warna(warna,jenis){
	if(jenis=='bahan'){
real=$("#kode_produksi_bahan_"+warna).text();
	$("#kode_produksi_bahan_"+warna).html("<input type='text' name='kode_produksi_bahan' id='kode_produksi_bahan' value='"+real+"'> <input type='button' name='button' value='Ubah' onclick='kode_produksi_bahan("+warna+");'>"); }else{
	
	real=$("#kode_produksi_variasi_"+warna).text();
	$("#kode_produksi_variasi_"+warna).html("<input type='text' name='kode_produksi_variasi' id='kode_produksi_variasi' value='"+real+"'> <input type='button' name='button' value='Ubah' onclick='kode_produksi_variasi("+warna+");'>");

}
}



function simpan_kode_produksi_bahan(warna){

if(warna=='0'){
warna='000';
}


	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var isi_value=$("#kode_produksi_bahan").val();

	var data="warna="+warna;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&isi_value="+isi_value;
			
	$.post('job_gelaran_ubah_kode_produksi_bahan.php',data,function(response){
	
		 document.location.reload(); 
	}); 		
		
}


function simpan_kode_produksi_variasi(warna){

if(warna=='0'){
warna='000';
}


	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var isi_value=$("#kode_produksi_variasi").val();

	var data="warna="+warna;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
		data+="&isi_value="+isi_value;
		data+="&variasi=variasi";		
	$.post('job_gelaran_ubah_kode_produksi_bahan.php',data,function(response){
	
	 document.location.reload();  
	}); 		
		
}
</script>
<table border="0"  id="ReportTable">
<tr>
<td>
	<table>
      <tr>
        <td><span class="style3">Nomor  Manufaktur</span></td>
        <td><span class="style3">:</span></td>
        <td id="no_po"><span class="style3"><?php echo $no_po; ?></span></td>
        <td width="150"><span class="style3"></span></td>
        <td width="200"><span class="style3">Model</span></td>
        <td><span class="style3">:</span></td>
        <td class="model"><?php echo $model?><span class="hilang" id="kunci"><?php echo $_GET['real'];?></span></td>
      </tr>
      <tr>
        <td><span class="style3">No CO</span></td>
        <td><span class="style3">:</span></td>
        <td id="no_co"><span class="style3" id="span_no_co"><?php echo $no_co; ?></span></td>
        <td width="150"><span class="style3"></span></td>
        <td><span class="style3">Bahan</span></td>
        <td><span class="style3">:</span></td>
        <td class="bahan"><?php echo $bahan?></td>
      </tr>
      <tr>
        <td><span class="style3">Pabrik Tujuan </span></td>
        <td><span class="style3">:</span></td>
        <td><span class="style3"><?php echo $pabrik?></span></td>
        <td width="150"><span class="style3"></span></td>
        <td><span class="style3">Cumsumsion Bahan Baku</span></td>
        <td><span class="style3">:</span></td>
        <td ><span class="c_bahan"><?php echo $c_produk?></span>&nbsp;&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td><span class="style3">Pabrik Sewing </span></td>
        <td><span class="style3">:</span></td>
        <td class="sewing"><span class="style3"><font color="#FF0000"><?php echo $pabrik_sewing;?></font></span></td>
        <td width="150"><span class="style3"></span></td>
        <td><span class="style3">Comsumsion Variasi</span></td>
        <td><span class="style3">:</span></td>
        <td><span  class="c_variasi"><?php echo $c_variasi?></span> &nbsp;&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td colspan="10" height="25"><span class="style3"></span></td>
      </tr>
     <input type="hidden" name="catatanvalue" value="<?php echo $catatan?>" id="catatanvalue">
      <tr>
        <td valign="top"><span class="style3">Catatan</span></td>
        <td valign="top"><span class="style3">:</span></td>
        <td class="catatan" valign="top"><?php $catatan=str_replace(".","<br>",$catatan);
		echo $catatan;
		?></td>
        <input type="hidden" name="kakivalue" id="kakivalue" value="<?php echo $catatan_kaki?>" />
		 <td width="150"><span class="style3"></span></td>
        <td valign="top"><span class="style3">Catatan Kaki</span></td>
        <td valign="top"><span class="style3">:</span></td>
        <td valign="top"><span  class="catatan_kaki"><?php $catatan_kaki=str_replace(".","<br>",$catatan_kaki);
		echo $catatan_kaki;
		?></span>&nbsp;&nbsp;&nbsp;</td>
      </tr>
	   <tr>
        <td colspan="7" ><blink><font color="#FF0000">untuk bisa menjadi lebih dari 1baris pemisahnya "<strong>titik ( . )</strong>"</font> </blink></td>
      </tr>
    </table>
	<table border="0" width="1000" class="border_tabel" bgcolor="#000000" cellspacing="1" cellpadding="2">
			<tr>
				<th width="20" align="center" rowspan="2" height="30"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">No</font></th>
				<th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Nama Produk</font></b></th>
				<th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif"  size="2">Warna</font></b></th>
				<th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Ukuran</font></b></th>
				<th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">Ass</font></b></th>
				<th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">QTY</font></b></th>
				<th colspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Bahan Baku<br />
				(Realisasi Kg/Roll)</font></b></th>
				<th id="supplier-border"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Supplier</font></b></th>
				<th colspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Variasi<br />
				(Realisasi Kg/Roll)</font></b></th>
				<th id="supplier-border" ><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Supplier</font></b></th>
			</tr>
			<tr>
				<th width="100" height="20"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">Kg/Yard</font></th>
				<th width="100"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">ROll</font></th>
				<th ><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Kode Produksi</font></th>
				<th width="100"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Kg/Yard</font></th>
				<th width="100"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Roll</font></th>
				<th><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Kode Produksi</font></th>
			</tr>	
	<?php 
	$sql="select * from job_gelaran_detail_rian_detail where no_co='$no_co' and no_po='$no_po'";
	$query=mysql_query($sql)or die($sql);
	if(($s_bahan=="")||($s_bahan=="0")){
			$s_bahan="<font color='#FF0000'>xxxx</font>";
		}
		if(($s_variasi=="")||($s_variasi=="0")){
			$s_variasi="<font color='#FF0000'>xxxx</font>";
		}
		if(($roll_real=="")||($roll_real=="0")){
			$roll_real="<font color='#FF0000'>0</font>";
		}
		
		if(($variasi_real=="")||($variasi_real=="0")){
			$variasi_real="<font color='#FF0000'>0</font>";
		}
		
		if($kode_produksi_bahan==""){
			$kode_produksi_bahan="<font color='#FF0000'>xxxx</font>";
		}
		
		if($kode_produksi_variasi==""){
			$kode_produksi_variasi="<font color='#FF0000'>xxxx</font>";
		}
		
		if($catatan_kaki==""){
			$catatan_kaki="<font color='#FF0000'>xxxx</font>";
		}
		
		if(($kg_bahan_real=="")||($kg_bahan_real=="0")){
			$kg_bahan_real="<font color='#FF0000'>0</font>";
		}
		if(($kg_variasi_real=="")||($kg_variasi_real=="0")){
			$kg_variasi_real="<font color='#FF0000'>0</font>";
		}
		
		
	if(mysql_num_rows($query)>0){
		$benar=true;
		
		
	}else{
		$benar=false;
		
	}
	$sql="SELECT job_gelaran_detail.kd_produk,produk.nama,mst_size.size,substring(job_gelaran_detail.kd_produk,13,3),qty_produk FROM job_gelaran_detail,mst_size,produk WHERE 
no_po='$no_po' AND no_co='$no_co' AND mst_size.kode=SUBSTRING(job_gelaran_detail.kd_produk,8,2) AND 
job_gelaran_detail.kd_produk=produk.kode order  by substring(job_gelaran_detail.kd_produk,13,3),SUBSTRING(job_gelaran_detail.kd_produk,1,12)  ";
$query=mysql_query($sql)or die($sql);

while(list($kd_produk,$nama,$ukuran,$kode_war,$qty)=mysql_fetch_array($query)){
$no++;
$j++;
if($j==1){
	if(!$benar){
				$sql="INSERT INTO `quantum`.`job_gelaran_detail_rian_detail`
				(`no_co`,
				 `no_po`,
				 `kode_warna`,
				 `s_bahan`,
				 `s_variasi`)
					VALUES ('$no_co',
					'$no_po',
					'$kode_war',
					'',
					'')";
					$res=mysql_query($sql);
	}else{
		$sql="SELECT s_bahan,s_variasi,roll_bahan_real,roll_variasi_real,kode_produksi_bahan,kode_produksi_variasi,kg_bahan_real,kg_variasi_real FROM job_gelaran_detail_rian_detail where no_co='$no_co' and no_po='$no_po' and kode_warna='$kode_war' ";
		$res=mysql_query($sql)or die($sql);
		list($s_bahan,$s_variasi,$roll_real,$variasi_real,$kode_produksi_bahan,$kode_produksi_variasi,$kg_bahan_real,$kg_variasi_real)=mysql_fetch_array($res);	
		if(($s_bahan=="")||($s_bahan=="0")){
			$s_bahan="<font color='#FF0000'>xxxx</font>";
		}
		if(($s_variasi=="")||($s_variasi=="0")){
			$s_variasi="<font color='#FF0000'>xxxx</font>";
		}
		if(($roll_real=="")||($roll_real=="0")){
			$roll_real="<font color='#FF0000'>0</font>";
		}
		
		if(($variasi_real=="")||($variasi_real=="0")){
			$variasi_real="<font color='#FF0000'>0</font>";
		}
		
		if($kode_produksi_bahan==""){
			$kode_produksi_bahan="<font color='#FF0000'>xxxx</font>";
		}
		
		if($kode_produksi_variasi==""){
			$kode_produksi_variasi="<font color='#FF0000'>xxxx</font>";
		}
		
		if(($kg_bahan_real=="")||($kg_bahan_real=="0")){
			$kg_bahan_real="<font color='#FF0000'>0</font>";
		}
		if(($kg_variasi_real=="")||($kg_variasi_real=="0")){
			$kg_variasi_real="<font color='#FF0000'>0</font>";
		}
	}
}
?><tr bgcolor="#FFFFFF">
	<td align="center"><?php echo $no?></td>
	<td><?php echo $nama;$rekam_kode.=$kd_produk."-";?><span id="id_barang<?php echo $no?>" class="hilang"><?php echo $kd_produk?></span></td>
	<?php if($j==1){
	
	?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center"><?php 
	echo $arrwarna[$kode_war]	?></td>
	<?php }?>
	<td align="center"><?php echo $ukuran?></td>
	<td id="ass_<?php echo $kd_produk?>" align="center">0</td>
	<td align="center" id="qty<?php echo $no;?>" ondblclick="ubah_qty('<?php echo $no?>')"><?php echo $qty;?></td>
    <?php $tqty+=$qty;
	?>
	<?php if($j==1){?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "bahan_baku_".$kode_war?>">&nbsp;</td>
	<?php }?>
	<?php if($j==1){?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "roll_baku_".$kode_war?>">&nbsp;</td>
	<?php }?>
	<?php if($j==1){?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="supp_<?php echo $kode_war?>" ondblclick="ubah_supplier('<?php echo $kode_war?>','bahan')"><?php echo $s_bahan?>&nbsp;</td>
	<?php }?>
	<?php if($j==1){?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "variasi_".$kode_war?>">&nbsp;</td>
	<?php }?>
	<?php if($j==1){?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "roll_variasi_".$kode_war?>">&nbsp;</td>
	<?php }?>
	<?php if($j==1){?>
	<td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="svariasi_<?php echo $kode_war?>" ondblclick="ubah_supplier('<?php echo $kode_war?>','variasi')"><?php echo  $s_variasi?></td>
	<?php }?>
</tr><?php
	if($j==$arrcount[$kode_war]){
		$j=0;
		$rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
		$eksekusi_rekam_ass.="ass_landing('ass.php','$rekam_kode','$no_co','$no_po');";
		$rekam_kode="";
		?>
<tr bgcolor="#666666">
	<td colspan="5" height="25"><font color="#FFFFFF">Jumlah</font></td>
	<td align="center"><font color="#FFFFFF"><?php echo $tqty?></font></td>
	<td align="center" id="kg_bahan_real_<?php echo $kode_war?>" ondblclick="ubah_real_bahan_kg('<?php echo $kode_war?>','bahan')" ><font color="#FFFFFF"><?php echo $kg_bahan_real?></font></td>
	<td  align="center" id="roll_bahan_real_<?php echo $kode_war?>" ondblclick="ubah_real_bahan('<?php echo $kode_war?>','bahan')"><font color="#FFFFFF"><?php echo $roll_real ?></font></td>
	<td  align="center" id="kode_produksi_bahan_<?php echo $kode_war?>" ondblclick="ubah_kode_produksi_warna('<?php echo $kode_war?>','bahan')"><font color="#FFFFFF"><?php echo $kode_produksi_bahan?></font></td>
	
	<td align="center" id="kg_variasi_real_<?php echo $kode_war?>" ondblclick="ubah_real_bahan_kg('<?php echo $kode_war?>','variasi')" ><font color="#FFFFFF"><?php echo $kg_variasi_real?></font></td>
	
	<td  align="center" id="roll_variasi_real_<?php echo $kode_war?>" ondblclick="ubah_real_bahan('<?php echo $kode_war?>','variasi')"><font color="#FFFFFF"><?php echo $variasi_real ?></font></td>
	<td  align="center" id="kode_produksi_variasi_<?php echo $kode_war?>" ondblclick="ubah_kode_produksi_warna('<?php echo $kode_war?>','variasi')"><font color="#FFFFFF"><?php echo $kode_produksi_variasi?></font></td>
</tr>
<script>
ambilkilo('<?php echo  $tqty;?>','<?php echo $kode_war?>');
</script>
		<?php $tqty=0;
	}

}

$sql="select approve,approveby from job_gelaran where no_co='$no_co' and no_po='$no_po'";
$query=mysql_query($sql)or die($sql);
list($approved,$approveby)=mysql_fetch_array($query);

?>	

<script>
function ubah_qty(no){
	var kunci=$("#kunci").text(); 
	if(kunci==1){
			alert('Maaf co udah di real cutting, Operasi gagal');exit();
		}
	var id_barang=$("#id_barang"+no).text().trim();
	var qty=$("#qty"+no).text();
	$("#qty"+no).html("<input type='text' id='tqty"+no+"' name='tqty"+no+"' value='"+qty+"' size='5'><span onclick=simpan_proses('"+no+"') class='mylink'>Simpan</span>");
}

function simpan_proses(no){
	var id_barang=$("#id_barang"+no).text().trim();
	var qty=$("#tqty"+no).val();
	var no_co=$("#span_no_co").text().trim();
	var data="id_barang="+id_barang+"&qty="+qty+"&no_co="+no_co;
	
	$.post("job_gelaran_detail_proses_3.php",data,function(response){
		if(response.trim()=='berhasil'){
			
			$("#qty"+no).text(qty);
			alert('berhasil');	
		}
	});
	
	
}

$(".hilang").hide();

</script>

<script type="text/javascript">
<?php echo $eksekusi_rekam_ass;?>
</script>			
	</table>
   
   </td>
</tr></table>		
<form name="export" method="post"  action="produk_list_export.php" onsubmit='$("#datatodisplay").val( $("<div>").append( $("#ReportTable").eq(0).clone() ).html() )'>
		<table width="100%">
			<tr>
				<td align="center">
					<?php 
						if($approved){
							echo "<b>(APPROVED BY $approveby)</b>";
						}else{
					?>
						<input type="button" value="Approve" onclick="if(confirm('Approving PPIC <?php echo $no_co; ?>?')){window.location='job_gelaran_approving.php?no_po=<?php echo $no_po; ?>&no_co=<?php echo $no_co; ?>';}">
					<?php
						}
					?>
					<?php
						if($approved){
							?><input type="button" value="Print Mode" onclick="window.open('job_gelaran_print_3.php?no_po=<?php echo $no_po; ?>&no_co=<?php echo $no_co; ?>','job_gelaran_print','width=800,height=400,menubar=yes,scrollbars=yes');"><?php
						}
					?>
					<input type="button" value="Kembali" onclick="window.location='job_gelaran_list.php';">
					<input type="button" value="Export To Barcode" onclick="window.open('job_gelaran_barcode.php?no_po=<?php echo $no_po; ?>&no_co=<?php echo $no_co; ?>','job_gelaran_barcode','width=1,height=1,menubar=yes,scrollbars=yes');"> 
					<?php if($approved){?>
					<input name="Submit" type="submit"  value="Print Lewat Excel"> <?php }?>
               <input type="hidden" id="datatodisplay" name="datatodisplay">  
            
				</td>
			</tr>
		</table>
		</form>
Keterangan: <br />
<ol>
	<li>Untuk merubah Data silahkan anda double klik dari kolom yang akan di ubah </li>
	<li>jika jumlah data lebih dari 32 item produk maka sistem meminta 2x print dimana print pertama untuk halaman pertama dan print ke dua untuk ngeprint halaman ke dua</li>
</ol>
	</fieldset>
<?php 

include_once "footer.php" ?>
