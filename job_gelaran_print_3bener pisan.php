<?php include("koneksi_rian.php"); ?>

<script src="jquery.js"></script>
	<script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

</script>
<?php
	$no_po=$_GET["no_po"];
	$no_co=$_GET["no_co"];
	$sql="select pabrik.id,pabrik.nama from pabrik,job_gelaran where pabrik.id=job_gelaran.pabrik and job_gelaran.no_co='$no_co' and job_gelaran.no_po='$no_po'";
	$query=mysql_query($sql)or die($sql);
	list($id_pabrik,$pabrik)=mysql_fetch_array($query);
	
	$sql="select bahan,model,c_produk,c_variasi,kode_produksi,catatan,pabrik.nama from job_gelaran_detail_rian,pabrik where no_po='$no_po' and no_co='$no_co' and pabrik.id=job_gelaran_detail_rian.pabrik_sewing";
	$query=mysql_query($sql)or die($sql);
	list($bahan,$model,$c_produk,$c_variasi,$kode_produksi,$catatan,$pabrik_sewing)=mysql_fetch_array($query);
	if($pabrik_sewing==""){
		$pabrik_sewing=$pabrik;
	}
	if($c_produk==""){
		$c_produk="0";
	}
	
	if($c_variasi==""){
		$c_variasi="0";
	}
	if($bahan==""){
		$bahan="xxxx";
	}
	
	if($kode_produksi==""){
		$kode_produksi="xxxx";
	}
	
	if($catatan==""){
		$catatan="xxxx";
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
.style2 {
	font-size: 12px;
}
.style3 {font-size: 16px}

th {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #FFFFFF;
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

function simpan_kode_produksi(){
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

function simpan_bahan(){
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

function simpan_c_bahan(){
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

function simpan_c_variasi(){
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


function simpan_pabrik(){
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



function simpan_catatan(){
	var catatan=$("#catatan").val();
	var no_co=$("#no_co").text();
	var no_po=$("#no_po").text();
	var data="catatan="+catatan;
		data+="&no_po="+no_po;
		data+="&no_co="+no_co;
			
	$.post('job_gelaran_ubah_catatan.php',data,function(response){
		$(".catatan").text(response);
	});
}

$(document).ready(function(){
	$(".kode_produksi").dblclick(function(){
		var kode_produksi=$(this).text();
		$(this).html("<input type='text' name='kode_produksi' id='kode_produksi' value='"+kode_produksi+"'> <input type='button' name='button' value='Ubah' onclick='simpan_kode_produksi();'>");
	});
	
	
	$(".catatan").dblclick(function(){
		var catatan=$(this).text();
		$(this).html("<textarea id='catatan' cols='40' rows='2'>"+catatan+"</textarea> <input type='button' name='button' value='Ubah' onclick='simpan_catatan();'>");
	});
	
	$(".bahan").dblclick(function(){
		var bahan=$(this).text();
		$(this).html("<input type='text' name='bahan' id='bahan' value='"+bahan+"'> <input type='button' name='button' value='Ubah' onclick='simpan_bahan();'>");
	});
	
	$(".c_bahan").dblclick(function(){
		var c_bahan=$(this).text();
		$(this).html("<input type='text' name='c_bahan' id='c_bahan' value='"+c_bahan+"'> <input type='button' name='button' value='Ubah' onclick='simpan_c_bahan();'>");
	});
	
	
	$(".c_variasi").dblclick(function(){
		var c_variasi=$(this).text();
		$(this).html("<input type='text' name='c_variasi' id='c_variasi' value='"+c_variasi+"'> <input type='button' name='button' value='Ubah' onclick='simpan_c_variasi();'>");
	});
	
	$(".sewing").dblclick(function(){
		var c_variasi=$(this).text();
		$(this).html("<select name='pabrik1' id='pabrik1'>"+
	"<?php $sql="select id,nama from pabrik where nama not like '%simulasi%' order by nama ";$res=mysql_query($sql)or die($sql);
	while(list($id_pabrik,$nama_pabrik)=mysql_fetch_array($res)){
		echo"<option value='$id_pabrik'>$nama_pabrik</option>";
	}?></select> <input type='button' name='button' value='Ubah' onclick='simpan_pabrik();'><input type='button' name='refresh' value='Cancel' onclick='document.location.reload();'");
	});
	
	
	
});

var hitungan=0;

function conterso(){
hitungan++;
	if(hitungan==rebes){
		window.print();
	}
}
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
							
					conterso();		
			  }
			});	
			   
			}catch(err){alert(err.message);}
}
</script>
<table border="0" >
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
        <td class="model"><?php echo $model?></td>
      </tr>
      <tr>
        <td><span class="style3">No CO</span></td>
        <td><span class="style3">:</span></td>
        <td id="no_co"><span class="style3"><?php echo $no_co; ?></span></td>
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
        <td class="c_bahan" ><?php echo $c_produk?>&nbsp;&nbsp;&nbsp;Kg/pcs</td>
      </tr>
      <tr>
        <td><span class="style3">Pabrik Sewing </span></td>
        <td><span class="style3">:</span></td>
        <td ><span class="style3"><?php echo $pabrik_sewing;?></span></td>
        <td width="150"><span class="style3"></span></td>
        <td><span class="style3">Comsumsion Variasi</span></td>
        <td><span class="style3">:</span></td>
        <td class="c_variasi"><?php echo $c_variasi?> &nbsp;&nbsp;&nbsp;Kg/pcs</td>
      </tr>
      <tr>
        <td colspan="10" height="25"><span class="style3"></span></td>
      </tr>
     
      <tr>
        <td><span class="style3">Catatan</span></td>
        <td><span class="style3">:</span></td>
        <td><?php echo $catatan?></td>
      </tr>
    </table>
	<table border="1" width="1000" class="border_tabel" bgcolor="#000000" cellspacing="1" cellpadding="2" bordercolor="#000000">
      <tr bordercolordark="#000000">
        <th width="20" align="center" rowspan="2" height="30"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">No</font></th>
        <th rowspan="2" width="200"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Nama Produk</font></b></th>
        <th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif"  size="2">Warna</font></b></th>
        <th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Ukuran</font></b></th>
        <th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">Ass</font></b></th>
        <th rowspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">QTY</font></b></th>
        <th colspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Bahan Baku</font></b></th>
        <th><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Supplier</font></b></th>
        <th colspan="2"><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Variasi</font></b></th>
        <th ><b><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Supplier</font></b></th>
      </tr>
      <tr>
        <th width="100" height="20"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif">Kg</font></th>
        <th width="100"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">ROll</font></th>
        <th ><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Kode Produksi</font></th>
        <th width="100"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Kg</font></th>
        <th width="100"><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Roll</font></th>
        <th ><font color="#FFFFFF" face="Verdana, Arial, Helvetica, sans-serif" size="2">Kode Produksi</font></th>
      </tr>
      <?php 
	$sql="select * from job_gelaran_detail_rian_detail where no_co='$no_co' and no_po='$no_po'";
	$query=mysql_query($sql)or die($sql);
	if(mysql_num_rows($query)>0){
		$benar=true;
	}else{
		$benar=false;
	}
	$sql="SELECT job_gelaran_detail.kd_produk,produk.nama,mst_size.size,substring(job_gelaran_detail.kd_produk,13,3),qty_produk FROM job_gelaran_detail,mst_size,produk WHERE 
no_po='$no_po' AND no_co='$no_co' AND mst_size.kode=SUBSTRING(job_gelaran_detail.kd_produk,8,2) AND 
job_gelaran_detail.kd_produk=produk.kode order  by substring(job_gelaran_detail.kd_produk,13,3)";
$query=mysql_query($sql)or die($sql);
if(!isset($_REQUEST[hal])){
	$hal="1";
}else{
	$hal=$_REQUEST[hal];
}
$banyakdata=mysql_num_rows($query);
$dataperhal=32;
$banyakhal=ceil($banyakdata/$dataperhal); $awal=($hal-1)*$dataperhal;
$sql.=" limit $awal,$dataperhal";

$query=mysql_query($sql)or die($sql);

$no=($hal-1)*$dataperhal;
while(list($kd_produk,$nama,$ukuran,$kode_war,$qty)=mysql_fetch_array($query)){
$no++;
$j++;
if($j==1){
$cont++;

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
	$sql="SELECT s_bahan,s_variasi,roll_bahan_real,roll_variasi_real,kode_produksi_bahan,kode_produksi_variasi FROM job_gelaran_detail_rian_detail where no_co='$no_co' and no_po='$no_po' and kode_warna='$kode_war' ";
		$res=mysql_query($sql)or die($sql);
		list($s_bahan,$s_variasi,$roll_real,$variasi_real,$kode_produksi_bahan,$kode_produksi_variasi)=mysql_fetch_array($res);	
		if($s_bahan==""){
			$s_bahan="xxxx";
		}
		if($s_variasi==""){
			$s_variasi="xxxx1";
		}
	}
}
?>
	  <tr bgcolor="#FFFFFF" bordercolordark="#000000">
        <td align="center"><?php echo $no?></td>
	    <td><?php echo $nama;$rekam_kode.=$kd_produk."-";?></td>
	    <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center"><?php 
	echo $arrwarna[$kode_war]	?></td>
	    <?php }?>
        <td align="center"><?php echo $ukuran?></td>
	    <td id="ass_<?php echo $kd_produk?>" align="center">0</td>
	    <td align="center"><?php echo $qty;$tqty+=$qty;?></td>
	    <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "bahan_baku_".$kode_war?>">&nbsp;</td>
	    <?php }?>
        <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "roll_baku_".$kode_war?>">&nbsp;</td>
	    <?php }?>
        <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="supp_<?php echo $kode_war?>" ><?php echo $s_bahan?>&nbsp;</td>
	    <?php }?>
        <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "variasi_".$kode_war?>">&nbsp;</td>
	    <?php }?>
        <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="<?php echo "roll_variasi_".$kode_war?>">&nbsp;</td>
	    <?php }?>
        <?php if($j==1){?>
        <td rowspan="<?php echo $arrcount[$kode_war]?>" align="center" id="svariasi_<?php echo $kode_war?>" ><?php echo  $s_variasi?></td>
	    <?php }?>
      </tr>
	  <?php
	if($j==$arrcount[$kode_war]){
		$j=0;
		$rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
		$eksekusi_rekam_ass.="ass_landing('ass.php','$rekam_kode','$no_co','$no_po');";
		$rekam_kode="";
		?>
      <tr bgcolor="#666666">
        <td colspan="5" height="25">Jumlah</td>
        <td align="center"><?php echo $tqty?></td>
        <td><strong>Realisasi Roll</strong></td>
        <td align="center" id="roll_bahan_real_<?php echo $kode_war?>" ><?php echo $roll_real ?></td>
        <td  align="center" id="kode_produksi_bahan_<?php echo $kode_war?>" ><?php echo $kode_produksi_bahan?></td>
        <td><strong>Realisasi Roll</strong></td>
        <td  align="center" id="roll_variasi_real_<?php echo $kode_war?>" ><?php echo $variasi_real ?></td>
        <td  align="center" id="kode_produksi_variasi_<?php echo $kode_war?>" ><?php echo $kode_produksi_variasi?></td>
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
    </table>
	<table border="0">
	<tr>
		<td>Mengetahui,</td>
		<td width="300">&nbsp;</td>
		<td width="300">Bandung,</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td width="300">&nbsp;</td>
		<td width="300">Staff PPIC</td>
	</tr>
	<tr>
		<td height="100"></td>
		<td width="300">&nbsp;</td>
		<td width="300">&nbsp;</td>
	</tr>
	<tr>
		<td><u>Nani Suryani</u></td>
		<td width="300">&nbsp;</td>
		<td width="300"><u>Triwulan</u></td>
	</tr>
	<tr>
		<td width="200">Manajer PPIC </td>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
	</tr>
	</table>
	<script type="text/javascript">
<?php echo $eksekusi_rekam_ass;?>
var rebes=<?php echo $cont?>;
</script>	
</td></tr></table>

<?php if($banyakhal!=$hal){
?>
<script>
window.open("job_gelaran_print_2.php?no_po=<?php echo $no_po ?>&no_co=<?php echo $no_co ?>&hal=<?php echo $hal+1 ?>","mywindow","width=100,height=100");
</script>
<?php }?>
	</fieldset>
	
