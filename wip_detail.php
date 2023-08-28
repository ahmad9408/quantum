<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="WIP CO Detail"; ?>
<?php  include('header.php'); 
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$kode_temp=$_POST['temp_co'];
$kode_temp1=$kode_temp;
$temp_po=$_POST['temp_po'];
$kode_model=$_POST['kode_model'];
$lose=$_POST['lose'];
if($lose==""){
	$lose=0;
}



if($kode_temp!=""){
	$kode_temp=explode(",",$kode_temp);
	foreach($kode_temp as $kode_id){
		$kode_in.="'".$kode_id."',";
	}
	$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}

$sql="SELECT pm.no_manufaktur,SUM(pmd.qty)  FROM po_manufaktur_detail AS pmd
INNER JOIN po_manufaktur AS pm ON 
(pm.no_manufaktur=pmd.no_manufaktur)
INNER JOIN mst_model_fix AS m ON 
(m.kode_model=SUBSTRING(pmd.kd_produk,1,7))
WHERE  LENGTH(TRIM(SUBSTRING(pmd.kd_produk,1,7)))='7'  AND pm.tanggal	 BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' 
AND SUBSTRING(pmd.kd_produk,1,7) LIKE '$kode_model%' 
AND closeco IS NULL  GROUP BY pm.no_manufaktur";
$query=mysql_query($sql)or die($sql);
$banyak=mysql_num_rows($query);
if(($banyak>1)&&($lose=='0')){
	//echo"lebih dari satu PO";
	include("lihat_data_po.php");
}else{
//satu 
?>
 <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
<!-- .replace(/,/g, ''), 10 -->

</script>
<form name="f1" id="f1">
<?php
if($lose==1){
	$no_manufaktur=$_POST['no_po'];
}else{
list($no_manufaktur,$qty)=mysql_fetch_array($query);
}
?>
<input type="hidden" id="no_po" name="no_po" value="<?php echo $no_manufaktur?>" /><?php
$sql="SELECT no_co FROM job_gelaran where no_po='$no_manufaktur'";
$query=mysql_query($sql)or die($sql);
while(list($no_co)=mysql_fetch_array($query)){
	$rekam_kode.=$no_co.",";
}
$rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
?>

<input type="hidden" id="temp_co" name="temp_co" value="<?php echo $rekam_kode?>" />
<input type="hidden" id="dari" name="dari" value="<?php echo $dari?>" />
<input type="hidden" id="sampai" name="sampai" value="<?php echo $sampai?>" />
<?php

$sql="SELECT  m.nama_model,p.nama AS pabrik  FROM job_loading AS l
INNER JOIN job_loading_detail AS ld ON 
(ld.no_load=l.no_load)
LEFT JOIN job_gelaran AS g ON 
(g.no_co=l.no_co)
left join mst_model_fix as m on 
(m.kode_model=substring(ld.kd_produk,1,7))
inner join pabrik as p on 
(l.pabrik_dari=p.id)
WHERE  substring(ld.kd_produk,1,7)='$kode_model' and g.no_co in ($kode_in)";
$query=mysql_query($sql)or die($sql);
list($nama_model,$pabrik)=mysql_fetch_array($query);
?><table border="0">
<tr>
	<td><strong>Kode Mode</strong>l</td>
	<td width="2"><strong>:</strong></td>
	<td><strong><?php echo $kode_model?></strong></td>
</tr>
<tr>
	<td><strong>Nama Model</strong></td>
	<td><strong>:</strong></td>
	<td><strong><?php echo $nama_model?></strong></td>
</tr>
<tr>
	<td><strong>Pabrik</strong></td>
	<td><strong>:</strong></td>
	<td><strong><?php echo $pabrik?></strong></td>
</tr>
</table>
<table border="0" width="1000" style="font-size: 8pt">
  <tr>
    <td background="images/footer.gif" align="center" width="20" height="30" ><strong>NO</strong></td>
    <td background="images/footer.gif" align="center" width="120" ><strong>Barcode</strong></td>
	 <td background="images/footer.gif" align="center" width="300" ><strong>Nama</strong></td>
    <td background="images/footer.gif" align="center" width="70" ><strong>Ukuran</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Warna</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Qty RC</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Qty Reject</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Qty DO</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>HPP</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>HPJ</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>WIP</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Total HPP</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Total HPJ</strong></td> 
  </tr>
  <?php $sql="SELECT pmd.kd_produk,s.size,w.warna,pmd.hargajual,p.hargadasar,p.nama FROM po_manufaktur_detail AS pmd 
INNER JOIN mst_size AS s ON 
(s.kode=SUBSTRING(pmd.kd_produk,8,2))
INNER JOIN mst_warna AS w ON 
(w.kode=SUBSTRING(pmd.kd_produk,13,3))
left join produk as p on 
(p.kode=pmd.kd_produk)
WHERE pmd.no_manufaktur='$no_manufaktur' AND pmd.kd_produk LIKE '$kode_model%' group by pmd.kd_produk";
$query=mysql_query($sql)or die($sql);
$banyak=mysql_num_rows($query);
?><input type="hidden" id="banyak" value="<?php echo $banyak?>" name="banyak" /><?php
$rekam_kod="";
while(list($id_barang,$ukuran,$warna,$hargajual,$hargadasar,$nama)=mysql_fetch_array($query)){
$no++;
    $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
    $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
	?>
  <tr id="child-content" onmouseover="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
    <td><?php echo $no?></td>
    <td id="model<?php echo $no?>"><?php echo $id_barang?></td>
	<td width="300"><?php echo $nama?></td>
    <td align="center"><?php echo $ukuran?></td>
    <td ><?php echo $warna?></td>
	<td align="center" id="rc<?php echo $id_barang?>">0</td>
	<td align="center" id="reject<?php echo $id_barang?>">0</td>
	<td align="center" id="do<?php echo $id_barang?>">0</td>
	<td align="right" id="hpp<?php echo $id_barang?>"><?php echo $hargadasar?></td>
	<td align="right" id="hpj<?php echo $id_barang?>"><?php echo $hargajual?></td>
	<td align="center" id="wip<?php echo $id_barang?>">0</td>
	<td align="right" id="tothpp<?php echo $id_barang?>">0</td>
	<td align="right" id="tothpj<?php echo $id_barang?>">0</td> 
	
  </tr>
  <?php
	$rekam_kode1.=$id_barang.",";
	$tqty+=$qty;
}
$rekam_kode1=substr($rekam_kode1,0,strlen($rekam_kode1)-1);

?>

<input type="hidden" id="temp_kode" name="temp_kode" value="<?php echo $rekam_kode1?>" />
  <tr bgcolor="#006600">
    <td colspan="10" height="30"><em><b><font color="#FFFFFF">Total</font> </b></em></td>
    <td id="atotwip" align="right"><em><b><font color="#FFFFFF"><?php echo $tqty;?></font> </b></em></td>
	<td align="right" id="atothpp"><em><b><font color="#FFFFFF"><?php echo $tqty;?></font> </b></em></td>
	<td align="right" id="atothpj"><em><b><font color="#FFFFFF"><?php echo $tqty;?></font> </b></em></td> 
  </tr>
</table>
</form>
<script src="jquery.js"></script>
<script>
function cek_qty_produksi(jenis_cek){
	//alert(jenis_cek);
	var dari=$("#dari").val();
	var sampai=$("#sampai").val();
	
		if(jenis_cek=='do'){
			var no_po=$("#no_po").val()
			var rekam=$("#temp_kode").val();
		}else{
			var rekam=$("#temp_co").val();
		}
	
		try{
			  $.ajax({
			  type: 'POST',
			  url: 'cek_qty_pabrik_2.php',
			  data: {rekam:rekam,dari:dari,sampai:sampai,jenis_cek:jenis_cek,no_po:no_po},
			  dataType: 'json',
			  success: function(data){ 
			 /*if(jenis_cek=='do'){
			 	alert(data);
			 }*/
			 
				$.each(data, function(key, val) 
             	{
					 
				 
					if(jenis_cek=='rc'){
						$("#rc"+val.model).html(val.qty);
					}if(jenis_cek=='sw'){
						$("#reject"+val.model).text(val.qty_reject);
					} if(jenis_cek=='do'){ 
						$("#do"+val.model).html(val.qty);
					}
            	});	 	
				if(jenis_cek=='do'){
					hitung_wip();
				}else{
					ambil_qty_berikut(jenis_cek);
				}
			  }
			});	
			   
		}catch(err){alert(err.message);}
	}

function ambil_qty_berikut(jenis_cek){
	 
	if(jenis_cek=='rc'){
		cek_qty_produksi('sw');
		
	}else 
	if(jenis_cek=='sw'){
		cek_qty_produksi('do');
	}
}

var atothpp=0;
var atothpj=0;
var atotwip=0;
function hitung_wip(){
	var banyak=$("#banyak").val();
	for(var i=1;i<=banyak;i++){
		var model=$("#model"+i).text();
		var wip=parseFloat($("#rc"+model).text())-parseFloat($("#reject"+model).text())-parseFloat($("#do"+model).text());
		var hpp=wip*parseFloat($("#hpp"+model).text());
		var hpj=wip*parseFloat($("#hpj"+model).text());
		$("#wip"+model).text(wip);
		atotwip+=wip;
		$("#tothpp"+model).text(hpp);
		atothpp+=hpp;
		$("#tothpj"+model).text(hpj);
		atothpj+=hpj;
	}
	$("#atotwip").html("<em><b><font color=#FFFFFF>"+parseFloat(atotwip).formatMoney(0, '.', ',')+"</font></b></em>");
	$("#atothpp").html("<em><b><font color=#FFFFFF>"+parseFloat(atothpp).formatMoney(0, '.', ',')+"</font></b></em>");
	$("#atothpj").html("<em><b><font color=#FFFFFF>"+parseFloat(atothpj).formatMoney(0, '.', ',')+"</font></b></em>");
	

}


cek_qty_produksi('rc');
</script>


<?php //end satu
}
?>
<?php include_once "footer.php"; ?>
