<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="WIP PO Manufaktur-DO Gunas"; ?>
<?php  include('header.php'); 
$dari=$_REQUEST['dari'];
$sampai=$_REQUEST['sampai'];
$cari=$_POST['cari'];
$berdasar=$_REQUEST['berdasar'];
if($berdasar==""){
	$berdasar="0";
}



if($dari==""){
    $jumhari = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
	$dari=date("Y-m-")."01";
	$sampai=date("Y-m-").$jumhari;

}
$bulan=substr($dari,0,7)."-01";
?> 

<script src="jquery.js"></script>
<script>
$(document).ready(function(){
$(".hilang").hide();
});
</script>

 <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
<!-- .replace(/,/g, ''), 10 -->

</script>
<form method="post" action="wip_po_do_2.php" name="f1"  id="f1">
<input type="hidden" id="temp_po" name="temp_po" />
<input type="hidden" id="temp_co" name="temp_co" />
<input type="hidden" id="temp_model" name="temp_model"/>
<input type="hidden" id="kode_model" name="kode_model"/>	
 <fieldset>
 	<table border="0" width="1000">
	<tr>
		<td>Pencarian</td>
		<td width="2">:</td>
		<td><input type="text" name="cari"  size="30" value="<?php echo $cari?>" /></td>
		
		<td>&nbsp;&nbsp;&nbsp;Berdasar <select id="berdasar" name="berdasar">
		<option value="0" <?php if($berdasar==0){echo"selected";}?>>-Semua-</option>
		<option value="1" <?php if($berdasar==1){echo"selected";}?>>Kode</option>
		<option value="2" <?php if($berdasar==2){echo"selected";}?>>Nama</option>
		</select></td>
		<td> <script language="JavaScript" src="calendar_us.js"></script>
              <link rel="stylesheet" href="calendar.css" />
              <!-- calendar attaches to existing form element -->
              
            Dari :  <input type="text" name="dari" readonly id="dari" value="<?php echo $dari; ?>" size="16"/>
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
            &nbsp;&nbsp;Sampai
                   <input type="text" name="sampai" readonly id="sampai" value="<?php echo $sampai; ?>" size="16"/>
            &nbsp;
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'sampai'
              });
            </script>
</td>
		<td width="20"></td>
		
		<td><input type="submit" name="submit" id="submit" value="Cari" /></td>
	</tr>
	</table>
 </fieldset>
<table border="0" width="1500" style="font-size: 8pt">
          <tr>
            <td background="images/footer.gif" align="center" width="20" height="30" rowspan="2"><strong>NO</strong></td>
            <td background="images/footer.gif" align="center" width="80" rowspan="2"><strong>Kode</strong></td>
			<td background="images/footer.gif" align="center" width="200" rowspan="2"><strong>Nama Model</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>Upload</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>Rencana Kirim</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>Po</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>CO</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>Real Cutting</strong></td>
			<td background="images/footer.gif" align="center" width="70" colspan="2"><strong>Sewing</strong></td>
			<td background="images/footer.gif" align="center" width="70" colspan="2"><strong>QC</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>DO</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>WIP Sewing</strong></td>
			<td background="images/footer.gif" align="center" width="70" rowspan="2"><strong>WIP SUHO</strong></td>
			<td background="images/footer.gif" align="center" width="200" rowspan="2"><strong>Pabrik</strong></td>
			<td background="images/footer.gif" align="center" width="100" rowspan="2"><strong>Total HPP</strong></td>
			<td background="images/footer.gif" align="center" width="100" rowspan="2"><strong>Total HPJ</strong></td>
			<td background="images/footer.gif" align="center" width="100" rowspan="2"><strong>Keterangan</strong></td>
          </tr>
		  <tr>
		  	<td background="images/footer.gif" align="center"><strong>Qty</strong></td>
			<td background="images/footer.gif" align="center"><strong>Reject</strong></td>
			<td background="images/footer.gif" align="center"><strong>Grade A</strong></td>
			<td background="images/footer.gif" align="center"><strong>Grade B</strong></td>
		  </tr>
		  
<?php
 
if($berdasar=='1'){
	$terusan=" and  SUBSTRING(pmd.kd_produk,1,7) like '$cari%'";
}else if($berdasar=='2'){
	$terusan=" and m.nama_model like '%$cari%'  ";
}else{
	$terusan="";
}
 $sql="SELECT SUBSTRING(pmd.kd_produk,1,7),m.nama_model,SUM(qty) FROM po_manufaktur_detail AS pmd
INNER JOIN po_manufaktur AS pm ON 
(pm.no_manufaktur=pmd.no_manufaktur)
INNER JOIN mst_model_fix AS m ON 
(m.kode_model=SUBSTRING(pmd.kd_produk,1,7))
WHERE  LENGTH(TRIM(SUBSTRING(pmd.kd_produk,1,7)))='7'  AND pm.tanggal	 BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' 
and (pmd.kd_produk not like 'K%' and pmd.kd_produk not like 'A%') 
$terusan and closeco IS NULL  GROUP BY SUBSTRING(pmd.kd_produk,1,7)  limit 1000";


$query=mysql_query($sql)or die($sql);
while(list($kode_model,$nama_model,$qty)=mysql_fetch_array($query)){
	$no++;
    $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
    $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;


$sql="SELECT keterangan FROM wip_keterangan WHERE bulan='$bulan' AND model='$kode_model'";

$res=mysql_query($sql)or die($sql);
list($keterangan)=mysql_fetch_array($res);

$sql="SELECT SUBSTRING(pm.approvedate,1,10) FROM po_manufaktur AS pm 
INNER JOIN po_manufaktur_detail AS pmd 
ON (pmd.no_manufaktur=pm.no_manufaktur) 
WHERE SUBSTRING(pmd.kd_produk,1,7)='$kode_model' and tanggal  BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' 
ORDER BY SUBSTRING(pm.approvedate,1,7) LIMIT 1  ";
$res=mysql_query($sql)or die($sql);
list($tgl_upload)=mysql_fetch_array($res);


$sql="SELECT substring(tanggal,1,10) FROM po_manufaktur AS pm 
INNER JOIN po_manufaktur_detail AS pmd 
ON (pmd.no_manufaktur=pm.no_manufaktur) 
WHERE SUBSTRING(pmd.kd_produk,1,7)='$kode_model' and tanggal  BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59' 
ORDER BY tanggal desc LIMIT 1  ";
$res=mysql_query($sql)or die($sql);
list($tgl_kirim)=mysql_fetch_array($res);

	?>
		 <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td><?php echo $no?></td>
			<td id="code<?php echo  $no?>"><?php echo $kode_model?></td>
			<td ><?php echo $nama_model?></td>
			<td ><?php echo $tgl_upload ?></td>
			<td ><?php echo $tgl_kirim ?></td>
			<td align="center"><a href="po_do_detail_v2.php?t1=<?php echo $dari?>&t2=<?php echo $sampai?>&m=<?php echo $kode_model?>&j=p" target="_blank")><?php echo number_format($qty,"0",".",",");?></a></td>
			<td align="center" id="co<?php echo $kode_model?>">0</td>
			<td align="center" id="rc<?php echo $kode_model?>">0</td>  <span id="rcuang<?php echo $kode_model?>" class="hilang">0</span>  
			<span id="rchpp<?php echo $kode_model?>" class="hilang">0</span>
			<td align="center" id="sew<?php echo $kode_model?>">0</td>
			<td align="center" id="reject<?php echo $kode_model?>">0</td>  <span id="rejectuang<?php echo $kode_model?>" class="hilang" >0</span> 
			 <span id="rejecthpp<?php echo $kode_model?>" class="hilang">0</span>  
			<td align="center" id="grade_a<?php echo $kode_model?>">0</td>
			<td align="center" id="grade_b<?php echo $kode_model?>">0</td>
			<td align="center" id="do<?php echo $kode_model?>">0</td> <span id="douang<?php echo $kode_model?>" class="hilang">0</span> 
			<span id="dohpp<?php echo $kode_model?>" class="hilang">0</span> 
			<td align="center" id="wip<?php echo $kode_model?>" bgcolor="#00CCFF">0</td>
			<td align="center" id="wipbgrade<?php echo $kode_model?>" bgcolor="#00FFFF">0</td>
			<td  id="pabrik<?php echo $kode_model?>"></td>
			<td id="hpp<?php echo $kode_model?>" align="right"></td>
			<td  id="hpj<?php echo $kode_model?>" align="right" ></td>
			
			<td align="center" id="keterangan<?php echo $kode_model?>" ondblclick="m_ket('<?php echo $kode_model?>')"><?php echo $keterangan?></td>
		</tr>
	<?php
		$rekam_kode.=$kode_model.",";
}	
$rekam_kode=substr($rekam_kode,0,strlen($rekam_kode)-1);
	
?>
<input type="hidden" id="row" name="row" value="<?php echo $no?>" />	
<tr bgcolor="#006600">
	<td colspan="13" height="25"><em><strong><font color="#FFFFFF">Total</font></strong></em></td>
	<td id="totalwip" align="center"><em><strong><font color="#FFFFFF">0</font></strong></em></td>
	<td id="totalwipbgrade" align="center"><em><strong><font color="#FFFFFF">0</font></strong></em></td>
	<td>&nbsp;</td>
	<td id="totalhpp" align="right"><em><strong><font color="#FFFFFF">0</font></strong></em></td>
	<td id="totalhpj" align="right"><em><strong><font color="#FFFFFF">0</font></strong></em></td>
	<td>&nbsp;</td>
</tr>
</table>
</form>
<script>
	$("#temp_model").val('<?php echo $rekam_kode?>');
	
	function cek_qty_produksi(jenis_cek){
	//alert(jenis_cek);
	var dari=$("#dari").val();
	var sampai=$("#sampai").val();
	if(jenis_cek=='co'){
	var rekam=$("#temp_po").val();
	}else{
		if(jenis_cek=='do'){
			var rekam=$("#temp_model").val();
		}else{
			var rekam=$("#temp_co").val();
		}
	}
	
		try{
			  $.ajax({
			  type: 'POST',
			  url: 'cek_qty_pabrik.php',
			  data: {rekam:rekam,dari:dari,sampai:sampai,jenis_cek:jenis_cek},
			  dataType: 'json',
			  success: function(data){ 
			/* if(jenis_cek=='sw'){
			 	alert(data);
			 }*/
			 
				$.each(data, function(key, val) 
             	{
						//alert(val.model+' '+val.qty+' '+jenis_cek );
					if(jenis_cek=='co'){
						$("#co"+val.model).html("<a href='#' onclick=lihat_co('"+val.model+"')>"+parseFloat(val.qty).formatMoney(0, '.', ',')+"</a>");
					}
					if(jenis_cek=='rc'){
						$("#rc"+val.model).html("<a href='#' onclick=lihat_rc('"+val.model+"')>"+val.qty+"</a>");
					 	 $("#rcuang"+val.model).html(val.hpj);
						 $("#rchpp"+val.model).html(val.hpp);
					}if(jenis_cek=='sw'){
						$("#sew"+val.model).html("<a href='#' onclick=lihat_sw('"+val.model+"')>"+parseFloat(val.qty).formatMoney(0, '.', ',')+"</a>");
						$("#reject"+val.model).text(parseFloat(val.qty_reject).formatMoney(0, '.', ','));
						 $("#rejectuang"+val.model).text(val.hpj);
						  $("#rejecthpp"+val.model).text(val.hpp);
					}if(jenis_cek=='qc'){ 
						$("#grade_a"+val.model).html("<a href='#' onclick=lihat_qc('"+val.model+"')>"+parseFloat(val.grade_a).formatMoney(0, '.', ',')+"</a>");
						$("#grade_b"+val.model).text(parseFloat(val.grade_b).formatMoney(0, '.', ','));
					}if(jenis_cek=='do'){
					//alert(val.qty);
						$("#do"+val.model).html("<a href='po_do_detail_v2.php?t1="+dari+"&t2="+sampai+"&m="+val.model+"&j=d' target='_BLANK'>"+val.qty+"</a>");
						 $("#douang"+val.model).html(val.hpj);
						 $("#dohpp"+val.model).html(val.hpp);				
						 	}
            	});	 	
				if(jenis_cek=='do'){
					hitung_do_retur(rekam,dari,sampai);
					//hitung_wip();
				}else{
					ambil_qty_berikut(jenis_cek);
				}
			  }
			});	
			   
		}catch(err){alert(err.message);}
	}
	
	
	function hitung_do_retur(rekam,dari,sampai){
 
		
		try{
			  $.ajax({
			  type: 'POST',
			  url: 'cek_retur_do_distribusi.php',
			  data: {rekam:rekam,dari:dari,sampai:sampai},
			  dataType: 'json',
			  success: function(data){ 
				$.each(data, function(key, val) 
             	{
					var qty_do=parseFloat($("#do"+val.model).text());
					var sisa=qty_do-parseFloat(val.qty);
					$("#do"+val.model).html("<a href='po_do_detail_v2.php?t1="+dari+"&t2="+sampai+"&m="+val.model+"&j=d' target='_BLANK'>"+sisa+"</a>");
					
					var uanghpj=parseFloat($("#douang"+val.model).text());
					var uanghpp=parseFloat($("#dohpp"+val.model).text());
					
					
					var sisahpj=uanghpj-parseFloat(val.hpj);
					$("#douang"+val.model).text(sisahpj);
					var sisahpp=uanghpp-parseFloat(val.hpp);
					$("#dohpp"+val.model).text(sisahpp);
					
            	});	 	
				hitung_wip();
			  }
			});	
			   
		}catch(err){alert(err.message);}
	}

function hitung_wip(){
	var row=$("#row").val();
	var tsisa=0;
	var tsisawip=0;
	var tsisahpp=0;
	var tsisabgrade=0;
	for(var i=1;i<=row;i++){
		var model=$("#code"+i).text();
		//var sisa=parseFloat($("#rc"+model).text())-parseFloat($("#reject"+model).text())-parseFloat($("#do"+model).text());
		var sisa=parseFloat($("#rc"+model).text())-parseFloat($("#sew"+model).text().replace(/,/g, ''), 10)-parseFloat($("#reject"+model).text().replace(/,/g, ''), 10);
		var sisabgrade=parseFloat($("#rc"+model).text())-parseFloat($("#do"+model).text()); 
		$("#rc"+model).text(parseFloat($("#rc"+model).text()).formatMoney(0, '.', ','));
		$("#do"+model).text(parseFloat($("#do"+model).text()).formatMoney(0, '.', ','))
		var sisawip=parseFloat($("#rcuang"+model).text())-parseFloat($("#rejectuang"+model).text())-parseFloat($("#douang"+model).text());
		var sisahpp=parseFloat($("#rchpp"+model).text())-parseFloat($("#rejecthpp"+model).text())-parseFloat($("#dohpp"+model).text());
		tsisa+=sisa;
		tsisabgrade+=sisabgrade;
		tsisawip+=sisawip;
		tsisahpp+=sisahpp;
		if(sisa<0){sisa=0;}
		if(sisawip<0){sisawip=0;}
		if(sisahpp<0){sisahpp=0;}
		if(sisabgrade<0){sisabgrade=0;}
		
		
		$("#wip"+model).html(parseFloat(sisa).formatMoney(0, '.', ','));
		$("#wipbgrade"+model).html(parseFloat(sisabgrade).formatMoney(0, '.', ','));
		$("#hpj"+model).html(parseFloat(sisawip).formatMoney(0, '.', ','));
		$("#hpp"+model).html(parseFloat(sisahpp).formatMoney(0, '.', ','));
		
	}
	
	$("#totalwip").html("<em><b><font color='white'>"+parseFloat(tsisa).formatMoney(0, '.', ',')+"</font></b></em>");
	$("#totalhpp").html("<em><b><font color='white'>"+parseFloat(tsisahpp).formatMoney(0, '.', ',')+"</font></b></em>");
	$("#totalhpj").html("<em><b><font color='white'>"+parseFloat(tsisawip).formatMoney(0, '.', ',')+"</font></b></em>");
	$("#totalwipbgrade").html("<em><b><font color='white'>"+parseFloat(tsisabgrade).formatMoney(0, '.', ',')+"</font></b></em>");
}
	
function ambil_qty_berikut(jenis_cek){
	if(jenis_cek=='co'){
		cek_qty_produksi('rc');
	}else 
	if(jenis_cek=='rc'){
		cek_qty_produksi('sw');
	}else 
	if(jenis_cek=='sw'){
	
		cek_qty_produksi('qc');
	}else 
	if(jenis_cek=='qc'){
		cek_qty_produksi('do');
	}
}

function cek_no_po(rekam){
	var dari=$("#dari").val();
	var sampai=$("#sampai").val();
	try{
			  $.ajax({
			  type: 'POST',
			  url: 'wip_po_do_cek_po.php',
			  data: {rekam:rekam,dari:dari,sampai:sampai},
			  dataType: 'json',
			  success: function(data){ 
			  //alert(data);
			  var temp_po="";
				$.each(data, function(key, val) 
             	{
					//alert(val.no_po);
					
					temp_po+=val.no_po+",";
            	});	 
			
				
				var has=temp_po.substr(0,(temp_po.length-1));
				$("#temp_po").val(has);
				cek_no_co(has);	
					
			  }
			});	
			   
	}catch(err){alert(err.message);}
}


function cek_no_co(rekam){
	try{
			  $.ajax({
			  type: 'POST',
			  url: 'wip_po_do_cek_co.php',
			  data: {rekam:rekam},
			  dataType: 'json',
			  success: function(data){ 
			  //alert(data);
			  var temp_po="";
				$.each(data, function(key, val) 
             	{
					//alert(val.no_po);
					temp_po+=val.no_co+",";
            	});
				
				var has=temp_po.substr(0,(temp_po.length-1));
				$("#temp_co").val(has);
				 cek_qty_produksi('co')	;
				 cek_pabrik();
			  }
			});	
			   
	}catch(err){alert(err.message);}
}


function cek_pabrik(){
var rekam=$("#temp_co").val();
	try{
			  $.ajax({
			  type: 'POST',
			  url: 'wip_po_do_cek_pabrik.php',
			  data: {rekam:rekam},
			  dataType: 'json',
			  success: function(data){ 
			  
				$.each(data, function(key, val) 
             	{ 
					var semula=$("#pabrik"+val.model).text();
					if(semula==""){
						var koma="";
					}else{
						var koma=", ";
					}
					var isikan=semula+""+koma+""+val.pabrik;
					$("#pabrik"+val.model).text(isikan);
            	});	 
			
				
				
					
			  }
			});	
			   
	}catch(err){alert(err.message);}
}

function m_ket(kode_model){
var ket=$("#keterangan"+kode_model).text();
	$("#keterangan"+kode_model).html("<textarea name='ket"+kode_model+"' id='ket"+kode_model+"'>"+ket+"</textarea><input type='button' id='button"+kode_model+"' value='S' onclick=simpan('"+kode_model+"')>");
}

function simpan(kode_model){
	var bulan=$("#dari").val();
	bulan=bulan.substr(0,7);
	var ket=$("#ket"+kode_model).val();
	
	var data="model="+kode_model+"&bulan="+bulan+"&ket="+ket;
	$.post("simpan_ket_wip.php",data,function(response){
		if(response.trim()=='berhasil'){
			document.location.reload();
		}
	});
}


function lihat_detail_po(kode_model){
	$("#kode_model").val(kode_model);
	$("#f1").attr("target","_BLANK");
	$("#f1").attr("action","wip_po_detail.php");
	$("#submit").click();
	$("#f1").attr("target","");
	$("#f1").attr("action","wip_po_do.php");
	$("#kode_model").val(""); 	
	
}

function lihat_co(kode_model){
	awal(kode_model);
	$("#f1").attr("action","wip_co_detail.php");
	semula();
}

function lihat_wip(kode_model){
	awal(kode_model);
	$("#f1").attr("action","wip_detail.php");
	semula();
}

function lihat_rc(kode_model){
	awal(kode_model);
	$("#f1").attr("action","wip_rc_detail.php");
	semula();
}

function awal(kode_model){
	$("#kode_model").val(kode_model);
	$("#f1").attr("target","_blank");
}
function semula(){
	$("#submit").click();
	$("#f1").attr("target","");
	$("#f1").attr("action","wip_po_do.php");
	$("#kode_model").val("");
}

function lihat_sw(kode_model){
	awal(kode_model);
	$("#f1").attr("action","wip_sw_detail.php");
	semula();
}


function lihat_qc(kode_model){
	awal(kode_model);
	$("#f1").attr("action","wip_qc_detail.php");
	semula();
}


cek_no_po('<?php echo $rekam_kode?>');

</script>
<?php include_once "footer.php"; ?>