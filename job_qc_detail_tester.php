<?php $content_title="DETIL QUALITY CONTROL"; include_once "header.php" ?>
<?php include_once "clsaddrow.php";?>

<style>
.datagrid table { border-collapse: collapse; text-align: left; width: 100%; } .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #36752D; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }.datagrid table td, 
.datagrid table th { padding: 3px 10px;  }
.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420) );background:-moz-linear-gradient( center top, #36752D 5%, #275420 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');background-color:#36752D; color:#FFFFFF; font-size: 12px; font-weight: bold; border-left: 1px solid #36752D; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #275420; border-left: 1px solid #C6FFC2;font-size: 10px;font-weight: normal; }.datagrid table tbody .alt td { background: #DFFFDE; color: #275420; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }.datagrid table tfoot td div { border-top: 1px solid #36752D;background: #DFFFDE;} .datagrid table tfoot td { padding: 0; font-size: 12px } .datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }.datagrid table tfoot  li { display: inline; }.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #36752D;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420) );background:-moz-linear-gradient( center top, #36752D 5%, #275420 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');background-color:#36752D; }.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #275420; color: #FFFFFF; background: none; background-color:#36752D;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }

.kelas_departemen {
width: 50px;
border: thin solid #06F;	
position:static; 
position:inherit !important;
text-align:center;
cursor:pointer;

}

.kelas_departemen:hover { 
background-color:#E2FBFC;

}

fieldset { border:1px solid green }

legend {
padding: 0.2em 0.5em;
border:1px solid green;
color:green;
font-size:90%; 
}
  
</style>
<link rel="stylesheet" href="themes/base/jquery.ui.all.css">

<?php
if($_SESSION['username']!="ppic_makloon_sri"){
//echo"maaf sedang di perbaiki selama 2 jam";die;
}
	$no_qc=sanitasi($_GET["no_qc"]);
	$sql="SELECT * FROM job_qc WHERE no_qc='$no_qc'";
	$hsl=mysql_query($sql,$db);
	$rs=mysql_fetch_array($hsl);
	$no_qc=$rs["no_qc"];
	$no_sew=$rs["no_sew"];
	$sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
	$hsltemp=mysql_query($sql,$db);
	list($no_load)=mysql_fetch_array($hsltemp);
	$sql="SELECT no_co FROM job_loading WHERE no_load='$no_load'";
	$hsltemp=mysql_query($sql,$db);
	list($no_co)=mysql_fetch_array($hsltemp);
	$sql="SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
	$hsltemp=mysql_query($sql,$db);
	list($no_jo,$no_po)=mysql_fetch_array($hsltemp);
	$tanggal=$rs["tanggal"];
	$totalqty=$rs["totalqty"];
	$jumlah=$rs["totalrp"];
	$approved=$rs["approve"];
	$approved2=$rs["approve2"];
	$approveby=$rs["approveby"];
	$approveby2=$rs["approveby2"];
	$sql="SELECT no_po FROM job_gelaran WHERE no_co='$no_co'";
	$hsltemp=mysql_query($sql,$db);
	list($no_po)=mysql_fetch_array($hsltemp);
	$sql="SELECT pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
	$hsltemp=mysql_query($sql,$db);
	list($id_pabrik)=mysql_fetch_array($hsltemp);
	$sql="SELECT nama FROM pabrik WHERE id='$id_pabrik'";
	$hsltemp=mysql_query($sql,$db);
	list($nama_pabrik)=mysql_fetch_array($hsltemp);
	$pabrik="$nama_pabrik [$id_pabrik]";
?>

 <script>
Number.prototype.formatMoney = function(c, d, t){
var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "," : d, t = t == undefined ? "." : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
<!-- .replace(/,/g, ''), 10 -->

</script>

	<script language="JavaScript">

function cek(no,field){
	window.onkeydown=function(e) { 

               if (e.keyCode === 13 ){ 
				  hitung(no,field); 
			   }
  	}
}

function change_process(colom,nomor,seqno){
	window.onkeydown=function(e) { 

               if (e.keyCode === 13 ){ 
				  ubah_proses(nomor,seqno);
			   }
  	}
}


function ubah_proses(nomor,seqno){
			 var grade_service=parseFloat($("#s_grade_service"+nomor).text().replace(/,/g, ''), 10); 
			 var grade_a=parseFloat($("#qtya"+nomor).val().replace(/,/g, ''), 10); 
			 var grade_b=parseFloat($("#qtyb"+nomor).val().replace(/,/g, ''), 10); 
			 var id_barang=$("#id_barang"+nomor).text().trim();
			 var total=parseFloat($("#total"+nomor).text().replace(/,/g, ''), 10); 
			 var no_qc=$("#no_qc").text();
			  
			  
			 if((grade_a+grade_b+grade_service)==total){
			 	var data="grade_a="+grade_a+"&grade_b="+grade_b+"&no_qc="+no_qc+"&seqno="+seqno+"&id_barang="+id_barang;
			 
				$.post("ubah_nilai_qc.php",data,function(response){
			  
					if(response.trim()=='berhasil'){
						alert('berhasil');
						document.location='job_qc_detail_tester.php?no_qc='+no_qc;
					}	
				});
			 }else{
			 	alert("Maaf Qty Tidak balance");
			 }
}


function hitung(no,field){
 	var ta		= $("#a"+no).val();
	var tb		= $("#b"+no).val();
	var ts		= $("#s"+no).val();
	var ttot	= $("#total"+no).text();
	 
	
	if(field=="a"){
		var sisa	= parseFloat((ttot).replace(/,/g, ''), 10)-parseFloat(tb)-parseFloat(ta);
		var n		= "s";
	}else if(field=="b"){
		var sisa	= parseFloat((ttot).replace(/,/g, ''), 10)-parseFloat(tb)-parseFloat(ta);
		var n		= "s";
	}else if(field=="s"){
		var sisa	= parseFloat((ttot).replace(/,/g, ''), 10)-parseFloat(tb)-parseFloat(ts);
		var n		= "a";
	}
	  
	if(sisa<0){
		 
		$("#a"+no).val(0);
		$("#b"+no).val(0);
		$("#s"+no).val(0);exit();
	}
	
	$("#"+n+""+no).val(sisa);
	
}

function simpan12(){
 $(".jmlh").attr("bgcolor","");
	var jumlah	= $("#jm").val();
	for(var i=1;i<=jumlah;i++){
		var ta		= $("#a"+i).val();
		var tb		= $("#b"+i).val();
		var ts		= $("#s"+i).val();
		var ttot	= $("#total"+i).text();
		 
		
		if(parseFloat(ttot.replace(/,/g, ''), 10)!=(parseFloat(ts.replace(/,/g, ''), 10)+parseFloat(tb.replace(/,/g, ''), 10)+parseFloat(ta.replace(/,/g, ''), 10))){
			alert('Maaf qty tidak valid');
			$("#total"+i).attr("bgcolor","#ff0000");
			exit();
		}
	}
	
	 
	 $("#f1").submit();
		 
}


		var detailsWindow;
		function showCalendar(textid)
		{
		   detailsWindow = window.open("calendar.php?textid="+textid+"","calendar","width=260,height=250,top=300,scrollbars=yes");
		   detailsWindow.focus();   
		}
		function showVendor(textid,txtname,txtaddr,mode)
		{
		   detailsWindow = window.open("window_vendor.php?textid="+textid+"&txtname="+txtname+"&txtaddr="+txtaddr+"&mode="+mode+"","vendor","width=400,height=600,top=0,scrollbars=yes");
		   detailsWindow.focus();   
		}
		function showProduk(textid,textnama,kodeproduk,idukuran,idsatuan) {
			detailsWindow = window.open("window_produk.php?textid="+textid+"&textnama="+textnama+"&kodeproduk="+kodeproduk+"&idukuran="+idukuran+"&idsatuan="+idsatuan,"window_produk","width=800,height=600,scrollbars=yes");
			detailsWindow.focus();   
		}
		
		function ubah(colom,nomor,seqno){
		 
			 var grade_a=$("#s_grade_a"+nomor).text();
			 var grade_b=$("#s_grade_b"+nomor).text();  
				$("#s_grade_a"+nomor).html("<input type='text' id='qtya"+nomor+"' name='qtya"+nomor+"' value='"+grade_a+"' onkeypress=change_process('"+colom+"','"+nomor+"','"+seqno+"')>"); 
				$("#s_grade_b"+nomor).html("<input type='text' id='qtyb"+nomor+"' name='qtyb"+nomor+"' value='"+grade_b+"' onkeypress=change_process('"+colom+"','"+nomor+"','"+seqno+"')>"); 
				 
				$("#ket"+nomor).html("<input type='button' id='button"+nomor+"' name='button"+nomor+"' value='Ubah' onclick=ubah_proses('"+nomor+"','"+seqno+"')>"); 
				$("#keterangan").html("<b>Tombol</b>"); 
		}
		
		
	</script>
	<form method="POST" id="f1" action="job_qc_approving3_tester.php" class="datagrid" cellspacing="0" cellpadding="0">
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<table class="datagrid" cellspacing="0" cellpadding="0">
						<tr class="alt" width="20">
							<td width="50"><b>Pabrik</b></td>
							<td width="2"><b>:</b></td>
							<td><?php echo $pabrik; ?></td>
						</tr>
						<tr width="20">
							<td><b>No PO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_po;?></td>
						</tr>
						<tr class="alt" width="20">
							<td><b>No JO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_jo;?></td>
						</tr>
						<tr width="20">
							<td><b>No CO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_co;?><input type="hidden" id="no_co12" name="no_co12" value="<?php echo $no_co?>" /></td>
						</tr>
						<tr class="alt" width="20">
							<td><b>No LOAD</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_load;?></td>
						</tr>
						<tr width="20">
							<td><b>No Sewing</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_sew;?><input type="hidden" id="no_sew12" name="no_sew12" value="<?php echo $no_sew?>" /></td>
						</tr>
						<tr class="alt" width="20">
							<td><b>No QC</b></td>
							<td><b>:</b></td>
							<td id="no_qc"><?php echo $no_qc;?><input type="hidden" id="no_qc12" name="no_qc12" value="<?php echo $no_qc?>" /></td>
						</tr>
						<tr width="20">
							<td><b>Tanggal</b></td>
							<td><b>:</b></td>
							<td><?php echo $tanggal;?></td>
						</tr>
						<tr class="alt" width="20">
							<td><b>Total Qty</b></td>
							<td><b>:</b></td>
							<td><?php echo $totalqty;?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><b>No </b></th>
				<th><b>Kode Produk</b></th>
				<th><b>Nama Produk</b></th>
				<th><b>Warna</b></th>
				<th><b>Size</b></th>
				<th><b>Jumlah</b></th>
				<th><b>Grade A</b></th>
				<th><b>Grade B</b></th>
				<th><b>Service</b></th>
				<th id="keterangan"><b>Keterangan</b></th>
			</tr>
			</thead>
			<?php 
				$sql="SELECT kd_produk,qty,grade_a,grade_b,keterangan,seqno,harga FROM job_qc_detail WHERE no_qc='$no_qc' ORDER BY seqno";
				$sql="SELECT j.kd_produk,j.qty,j.grade_a,j.grade_b,j.keterangan,j.seqno,j.harga FROM job_qc_detail j left join produk p on p.kode=j.kd_produk left join mst_warna m on p.kode_warna=m.kode WHERE j.no_qc='$no_qc' ORDER BY m.warna,p.kode	";
				 
				$hsl=mysql_query($sql,$db);
				$no=0;
				$totalqty=0;
				$totalgrade_a=0;
				$totalgrade_b=0;
				while(list($kd_produk,$qty,$grade_a,$grade_b,$keterangan,$seqno,$harga)=mysql_fetch_array($hsl)){
			 
					$no++;
					$sql="SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($nama,$kode_warna,$kode_size,$satuan)=mysql_fetch_array($hsltemp);
					$sql="SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
					$hsltemp=mysql_query($sql,$db);
					list($warna)=mysql_fetch_array($hsltemp);
					$sql="SELECT size FROM mst_size WHERE kode='$kode_size'";
					$hsltemp=mysql_query($sql,$db);
					list($size)=mysql_fetch_array($hsltemp);
					$totalqty+=$qty;
					$totalgrade_a+=$grade_a;
					$totalgrade_b+=$grade_b;
					if($no%2==1){
						$kelas1="alt";
					 }else{
						 $kelas1="";
						 }

			?>
				<tr class="<?php echo $kelas1?>">
					<td><?php echo $no; ?> </span></td>
					<td id="id_barang<?php echo $no?>"><?php echo $kd_produk; ?>
					<input type="hidden" id="id_b<?php echo $no?>" name="id_b<?php echo $no?>" value="<?php echo $kd_produk?>" />
					<input type="hidden" id="harga12<?php echo $no?>" name="harga12<?php echo $no?>" value="<?php echo $harga?>" /> </td>
					<td ><?php echo $nama; ?></td>
					<td><?php echo $warna; ?></td>
					<td><?php echo $size; ?></td>
					<td align="right" id="total<?php echo $no?>" class="jmlh" <?php if(!$approved2){?>
					ondblclick=getChangeQty('<?php echo $no?>','<?php echo $qty?>') <?php }?>
					><?php echo number_format($qty); ?></td>
					
					
					
					<?php
						if(!$approved || $approved2){
						
						$service=$qty-($grade_a+$grade_b);
						$tservice+=$service;
					?>
						 
					
					
						<td align="right" id="s_grade_a<?php echo $no?>" ondblclick="ubah('a','<?php echo $no?>','<?php echo $seqno?>')"><?php echo number_format($grade_a); ?></td>
						<td align="right" id="s_grade_b<?php echo $no?>" ondblclick="ubah('b','<?php echo $no?>','<?php echo $seqno?>')"><?php echo number_format($grade_b); ?></td>
						<td align="right" id="s_grade_service<?php echo $no?>" ><?php echo number_format($service); ?></td>
						<td id="ket<?php echo $no?>">&nbsp;<?php echo $keterangan; ?></td>
					<?php
						}else{
					?>
						<td><!-- <input type="text" size="5" id="a<?php echo $no?>" name="grade_a[<?php echo $kd_produk; ?>]" value="0" onkeypress="cek('<?php echo $no?>','a')"  onclick="hitung('<?php echo $no?>','a')"/> -->
						<input type="text" size="5" id="a<?php echo $no?>" name="a<?php echo $no?>" value="0" onkeypress="cek('<?php echo $no?>','a')"  onclick="hitung('<?php echo $no?>','a')"/>
						</td>
						<td><input type="text" size="5" id="b<?php echo $no?>" name="b<?php echo $no?>" value="0" onkeypress="cek('<?php echo $no?>','b')" onclick="hitung('<?php echo $no?>','a')"></td>
						<td><input type="text" size="5"  id="s<?php echo $no?>" name="s<?php echo $no?>" value="0" onkeypress="cek('<?php echo $no?>','s')" onclick="hitung('<?php echo $no?>','a')"></td>
						<td><input type="text" size="50"   id="keterangan<?php echo $no?>" name="keterangan<?php echo $no?>"></td>
					<?php
						}
					?>
				</tr>
			<?php
				}
			?>
			<thead>
			<tr>
				<th colspan="5"><b>Jumlah</b></th>
				<th align="right"><b><?php echo number_format($totalqty); ?></b></th>
				<th align="right"><b><?php echo number_format($totalgrade_a); ?></b></th>
				<th align="right"><b><?php echo number_format($totalgrade_b); ?></b></th>
				<th align="right"><b><?php echo number_format($tservice); ?></b></th>
				<th> </th>
			</tr>
			</thead>
		</table>
		<input type="hidden" id="jm" name="jm" value="<?php echo $no?>" />
		
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<tr>
				<td><br></td>
			</tr>
			<tr>
				<td colspan="9" align="center"><b>PRODUK TAMBAHAN</b></td>
			</tr>
		</table>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><b>No</b></th>
				<th><b>Kode Produk</b></th>
				<th><b>Nama Produk</b></th>
				<th><b>Warna</b></th>
				<th><b>Size</b></th>
				<th><b>Jumlah</b></th>
				<th><b>Grade A</b></th>
				<th><b>Grade B</b></th>
				<th><b>Service</b></th>
				<th><b>Keterangan</b></th>
			</tr>
			</thead>
			<?php 
				$sql="SELECT kd_produk,qty,grade_a,grade_b,keterangan FROM job_qc_turunan WHERE no_qc='$no_qc' ORDER BY seqno";
				$hsl=mysql_query($sql,$db);
				$no=0;
				$totalqty=0;
				$totalgrade_a=0;
				$totalgrade_b=0;
				while(list($kd_produk,$qty,$grade_a,$grade_b,$keterangan)=mysql_fetch_array($hsl)){
					$no++;
					$sql="SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($nama,$kode_warna,$kode_size,$satuan)=mysql_fetch_array($hsltemp);
					$sql="SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
					$hsltemp=mysql_query($sql,$db);
					list($warna)=mysql_fetch_array($hsltemp);
					$sql="SELECT size FROM mst_size WHERE kode='$kode_size'";
					$hsltemp=mysql_query($sql,$db);
					list($size)=mysql_fetch_array($hsltemp);
					$totalqty+=$qty;
					$totalgrade_a+=$grade_a;
					$totalgrade_b+=$grade_b;
			?>
				<tr>
					<td><?php echo $no; ?></td>
					<td><?php echo $kd_produk; ?></td>
					<td><?php echo $nama; ?></td>
					<td><?php echo $warna; ?></td>
					<td><?php echo $size; ?></td>
					<td align="right"><?php echo number_format($qty); ?></td>
					<?php
						if(!$approved || $approved2){
					?>
						<td align="right" id="s_grade_a<?php echo $no?>"  ><?php echo number_format($grade_a); ?></td>
						<td align="right" id="s_grade_b<?php echo $no?>" ><?php echo number_format($grade_b); ?></td>
						<td align="right" id="s_grade_service<?php echo $no?>" ><?php echo number_format($service); ?></td>
						<td>&nbsp;<?php echo $keterangan; ?></td>
					<?php
						}else{
					?>
						<td><input type="text" size="5" name="grade_aturunan[<?php echo $kd_produk; ?>]" value="0"></td>
						<td><input type="text" size="5" name="grade_bturunan[<?php echo $kd_produk; ?>]" value="0"></td>
						<td><input type="text" size="5" name="serviceturunan[<?php echo $kd_produk; ?>]" value="0"></td>
						<td><input type="text" size="50" name="keteranganturunan[<?php echo $kd_produk; ?>]"></td>
					<?php
						}
					?>
				</tr>
			<?php
				}
			?>
			<tr>
				<td colspan="5"><b>Jumlah</b></td>
				<td align="right"><b><?php echo number_format($totalqty); ?></b></td>
				<td align="right"><b><?php echo number_format($totalgrade_a); ?></b></td>
				<td align="right"><b><?php echo number_format($totalgrade_b); ?></b></td>
				<td>&nbsp;</td>
			</tr>
		</table>		
		<table width="100%">
			<tr>
				<td align="center">
					<?php 
						if($approved){
							echo "<b>(APPROVED I BY $approveby)</b>";
							if($approved2){
								echo "<b>(APPROVED II BY $approveby2)</b>";
							}else{
								?> <input type="button" name="approve" value="Approve II" onclick="if(confirm('Approving Quality Control <?php echo $no_qc; ?>?')){simpan12();}"> <?php
							}
						}else{
							?> <input type="button" value="Approve I" onclick="if(confirm('Approving Quality Control <?php echo $no_qc; ?>?')){window.location='job_qc_approving.php?no_qc=<?php echo $no_qc; ?>';}"> <?php
						}
					?>
					<?php
						$sql="SELECT no_fin FROM job_fin WHERE no_qc='$no_qc'";
						$hsltemp=mysql_query($sql,$db);
						if(mysql_affected_rows($db)>0){
					?>
					<input type="button" value="Finishing" onclick="window.location='job_fin_list.php?no_qc=<?php echo $no_qc;?>';">
					<?php
						}
						if($approved){
							?><input type="button" value="Print Mode" onclick="window.open('job_qc_print.php?no_qc=<?php echo $no_qc; ?>','job_qc_print','width=800,height=400,menubar=yes,scrollbars=yes');"><?php
						}
					?>
					<input type="button" value="Kembali" onclick="window.location='job_qc_rekap.php';">
				</td>
			</tr>
		</table>
	</form>
	 
<script>
function getChangeQty(no,qty){
	var vid_barang 	= $("#id_barang"+no).text().trim();
	$("#total"+no).html("<input type='text' size='10' id='itot"+no+"' name='itot"+no+"' "+
	"value='"+qty+"'><input type='button' value='S' onclick=getSave('"+no+"','"+vid_barang+"')>"); 
}

function getSave(no,id_barang){
	 
	var no_qc 	= $("#no_qc12").val().trim();
	var vqty 	= $("#itot"+no).val();
	var data 	= "id_barang="+id_barang+"&qty="+vqty+"&no_qc="+no_qc+"&proses=setQtyqc"; 
	$.post("maket_proses.php",data,function(response){
		 $("#total"+no).html(Number(vqty).formatMoney(0, '.', ','));
		
	});
}
</script>
<?php include_once "footer.php" ?>