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
	$content_title="TAMBAH REAL CUTTING";
	$data_global['is_overridesecurity_mode']='1';
    $data_global['overridesecurity_mode']='0';

	include_once "header.php";
	include_once "clsaddrow.php";
	include_once "job_cutting_init.php";
	include "pdo_produksi/Db.class.php";
	
	
	$isDebug=0;
	if($username=='budi-it'){
		$isDebug=1;
	}
	$no_co=sanitasi($_GET['no_co']);
	$sql="SELECT no_po,pabrik FROM job_gelaran WHERE no_co='$no_co'";
	$hsltemp=mysql_query($sql);
	list($no_po,$id_pabrik)=mysql_fetch_array($hsltemp);
	$sql="SELECT closeco FROM po_manufaktur WHERE no_manufaktur='$no_po'";
	$hsl=mysql_query($sql);
	if(mysql_affected_rows($db)<1){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No Po Tidak Ada");
				window.location="job_cutting_list.php";
			</script>
		<?php
		exit;
	}
	list($closeco)=mysql_fetch_array($hsl);
	if($closeco=="1"){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No Po Sudah Di Close");
				window.location="job_gelaran_list.php";
			</script>
		<?php
		exit;
	}
	$sql="SELECT closeco FROM po_markas_pusat WHERE no_po='$no_po'";
	$hsl=mysql_query($sql);
	list($closeco)=mysql_fetch_array($hsl);
	if($closeco=="1"){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No Po Sudah Di Tutup");
				window.location="job_cutting_list.php";
			</script>
		<?php
	}
	$sql="SELECT no_co FROM job_gelaran WHERE no_co='$no_co' AND approve='1'";
	$hsl=mysql_query($sql);
	if(mysql_affected_rows($db)<1){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No CO ini belum dilakukan approval PPIC!");
				window.location="job_cutting_list.php";
			</script>
		<?php
	}
	$sql="SELECT no_co FROM job_cutting WHERE no_co='$no_co' AND realcuting='1'";
	$hsl=mysql_query($sql);
	if(mysql_affected_rows($db)>0){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No CO ini Sudah dilakukan Real Cutting!");
				window.location="job_cutting_list.php";
			</script>
		<?php
	}
	//$no_co=no_co();
	$sql="SELECT no_jo FROM job_cutting WHERE no_po='$no_po' AND no_jo!=''";//echo $sql;
	$hsl=mysql_query($sql);
	if(mysql_affected_rows($db)>0){
		list($no_jo)=mysql_fetch_array($hsl);
	}else{
		$no_jo=no_jo();
	}
	
	$no_load=no_load();
	
	$sql="SELECT nama FROM pabrik WHERE id='$id_pabrik'";
	$hsltemp=mysql_query($sql);
	list($nama_pabrik)=mysql_fetch_array($hsltemp);
	$pabrik="$nama_pabrik [$id_pabrik]";
	
	if (sanitasi($_POST['simpan'])){
		
		$tanggal=sanitasi($_POST['thncut'])."-".sanitasi($_POST['blncut'])."-".sanitasi($_POST['tglcut'])." ".sanitasi($_POST['timecut']);
		$totqtycutting=array();
		$totqtyikat=array();
		foreach($_POST["qtycutting"] as $barcode => $qtycutting){
			$totqtycutting[$barcode]+=$qtycutting;
			foreach ($_POST["qty"][$barcode] as $seqno2 => $qtyikat){
				$totqtyikat[$barcode]+=$qtyikat;
			}
		}
		$jml_cutting_valid=true;
		
		$jml_ikatan_valid=true;
		foreach($totqtyikat as $barcode => $qtyikat){
			if($_POST["qtycutting"][$barcode]!=$qtyikat){$jml_ikatan_valid=false;break;}
		}
		
		if(!$jml_cutting_valid){
			?>
				<script type="text/javascript">
					alert("'Qty Cutting' harus lebih kecil atau sama dengan dari 'Qty yg belum'! Silakan Perbaiki");
					// window.location="job_cutting_list.php";
				</script>
			<?php
		}
		//if(!$jml_ikatan_valid){
			?>
				<!-- <script type="text/javascript">
					alert("'Total' harus sama dengan dari 'Qty Cutting'! Silakan Perbaiki");
					// window.location="job_cutting_list.php";
				</script> -->
			<?php
		//}
		$adarmpakai=true;
		foreach ($_POST["rmpakai"] as $kode_rm => $qty_rm_terpakai){
			if($qty_rm_terpakai<=0){$adarmpakai=false;}
		}
		if($isDebug==1){
			echo "jml_cutting_valid:$jml_cutting_valid ;; jml_ikatan_valid:$jml_ikatan_valid ;; adarmpakai:$adarmpakai </br>";
		}
		
		if($jml_cutting_valid && $jml_ikatan_valid && $adarmpakai){
			$seqno=-1;
			$totalqty=0;
			$totalrp=0;
			
			if($isDebug==1){
				echo '<h3> _POST["qtycutting"] </h3>';
				print_r($_POST["qtycutting"]);
			}
			foreach($_POST["qtycutting"] as $barcode => $qtycutting){
				$seqno++;
				$totalqty+=$qtycutting;
				$totalrp+=$qtycutting*$harga;
				$sql="INSERT INTO job_cutting_detail (no_co,seqno,kd_produk,qty) VALUES ('$no_co','$seqno','$barcode','$qtycutting')";
				$query=mysql_query($sql);
				if(!$query){
					$sql="update job_cutting_detail set qty=qty+$qtycutting where no_co='$no_co' and seqno='$seqno' and kd_produk='$barcode'";
					$query=mysql_query($sql);
				}
				$sql1="INSERT INTO job_loading_detail (no_load,seqno,kd_produk,qty_produk) VALUES ('$no_load','$seqno2','$barcode','$qtycutting')";
				$hsltemp1 = mysql_query($sql1);
				
				if($isDebug==1){
				   echo $sql1.'</br>';
				   if(!$hsltemp1){
					   echo mysql_error().'</br>';   
				   }
				}
				 
				 
				 
				foreach ($_POST["qty"][$barcode] as $seqno2 => $qtyikat){
					$sql="INSERT INTO job_cutting_ikatan (no_co,seqno,kd_produk,qty) VALUES ('$no_co','$seqno2','$barcode','$qtyikat')";
					$query=mysql_query($sql);
					if(!$query){
						$sql="update job_cutting_ikatan set qty=qty+$qtyikat where no_co='$no_co' and seqno='$seqno2' and kd_produk='$barcode'";
						$query=mysql_query($sql);
					}
				 
					
				}
			}
			
			/* Tambah Loading */
			$sql2="INSERT INTO job_loading (no_load, no_co, pabrik_dari,
			tanggal,totalqtyproduk, totalrp, approve, approveby, approvedate) 
			VALUES ('$no_load','$no_co', '$id_pabrik', NOW(), '$totalqty', '$totalrp', '1','$username',NOW())";
			$hsltemp2 = mysql_query($sql2);
		
			//RM TERPAKAI
			$seqno=-1;
			foreach ($_POST["rmpakai"] as $kode_rm => $qty_rm_terpakai){
				$seqno++;
				$rm_cons=$_POST["rmpakai_cons"][$kode_rm];
				$rm_satuan=$_POST["rmpakai_satuan"][$kode_rm];
				$rm_keluar=$_POST["rmpakai_rm_keluar"][$kode_rm];
				$sql="INSERT INTO job_cutting_rm_terpakai (no_co,seqno,kode_rm,qty,satuan,rm_keluar,rm_terpakai) VALUES ";
				$sql.="('$no_co','$seqno','$kode_rm','$rm_cons','$rm_satuan','$rm_keluar','$qty_rm_terpakai')";
				mysql_query($sql);
			}
			
			$sql="UPDATE job_cutting SET ";
				$sql.="no_jo='$no_jo', ";
				$sql.="tanggal=NOW(), ";
				$sql.="kd_supplier='', ";
				$sql.="kd_produk='', ";
				$sql.="totalqty=totalqty+$totalqty, ";
				$sql.="totalrp='$totalrp', ";
				$sql.="realcutting='1', ";
				$sql.="approve='1', ";
				$sql.="approveby='$username', ";
				$sql.="approvedate=NOW(), ";
				$sql.="approve2='1', ";
				$sql.="approveby2='$username', ";
				$sql.="approvedate2=NOW() ";
			$sql.="WHERE no_co='$no_co'";
			mysql_query($sql);
			
			foreach($_POST["kdproduk"] as $seqno => $barcode){
				if($barcode){
					$qty=sanitasi($_POST["qty"][$seqno]);
					$sql="INSERT INTO job_cutting_turunan (no_co,seqno,kd_produk,qty) VALUES ('$no_co','$seqno','$barcode','$qty')";
					mysql_query($sql);
				}
			}
			?>
				<script type="text/javascript">
					
					<?php
					 if($isDebug==1){
						 
					 }else{
						 echo 'alert("Cutting Tersimpan [.]");';
						 echo 'window.location="job_cutting_list.php";';
					}
					
					?>
					
				</script>
			<?php
		}
		
	}
	foreach($_POST["addikatan"] as $add_kd_produk => $value){$_POST["ikatan"][$add_kd_produk]++;}
	foreach($_POST["incikatan"] as $add_kd_produk => $value){$_POST["ikatan"][$add_kd_produk]--;}
	
?>
<script language="JavaScript">
		var detailsWindow;
		function showProduk(textid,textnama,kodeproduk,idukuran,idsatuan) {
			detailsWindow = window.open("window_produk.php?textid="+textid+"&textnama="+textnama+"&kodeproduk="+kodeproduk+"&idukuran="+idukuran+"&idsatuan="+idsatuan,"window_produk","width=800,height=600,scrollbars=yes");
			detailsWindow.focus();   
		}
	</script>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?no_co=<?php echo $no_co; ?>" class="datagrid" cellspacing="0" cellpadding="0">
	<fieldset class="datagrid" cellspacing="0" cellpadding="0">
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<tr class="alt" height="20">
				<td width="50"><b>PO</b></td>
				<td width="2"><b>:</b></td>
				<td><b><?php echo $no_po; ?></b></td>
			</tr>
			<!--tr>
				<td><b>JO</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $no_jo; ?></b></td>
			</tr-->
			<tr>
				<td><b>CO</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $no_co; ?></b></td>
			</tr>
			<tr class="alt" height="20">
				<td><b>Pabrik</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $pabrik; ?></b></td>
			</tr>
			<tr>
				<td><b>Tanggal</b></td>
				<td><b>:</b></td>
				<td nowrap>
					<select name="tglcut">
						<?php
							for($i=1;$i<32;$i++){
								$val=substr("00",0,2-strlen($i)).$i;
								$selected="";
								if($val==date("d")){$selected="selected";}
						?>
							<option value="<?php echo $val;?>" <?php echo $selected;?>><?php echo $val;?></option>
						<?php
							}
						?>
					</select> /
					<select name="blncut">
						<?php
							for($i=1;$i<13;$i++){
								$val=substr("00",0,2-strlen($i)).$i;
								$selected="";
								if($val==date("m")){$selected="selected";}
						?>
							<option value="<?php echo $val;?>" <?php echo $selected;?>><?php echo $val;?></option>
						<?php
							}
						?>
					</select> /
					<select name="thncut">
						<?php
							for($i=date("Y")+1;$i>date("Y")-5;$i--){
								$val=$i;
								$selected="";
								if($val==date("Y")){$selected="selected";}
						?>
							<option value="<?php echo $val;?>" <?php echo $selected;?>><?php echo $val;?></option>
						<?php
							}
						?>
					</select> 
					<input type="text" name="timecut" value="<?php echo date("H:i:s");?>" size="8">
				</td>
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
				<!--td><b>Qty</b></td-->
				<!--td><b>Qty yg belum</b></td-->
				<th><b>Qty Cutting</b></th>
				<th><b>Satuan</b></th>
				<!--td><b>Jml Gelaran</b></td-->
				<!-- td><b>Polybag</b></td -->
			<!-- 	<td><b>Detil Polybag</b></td> -->
			</tr>
			</thead>
			<?php
				//$sql="SELECT kd_produk,qty FROM po_manufaktur_detail WHERE no_manufaktur='$no_po' ORDER BY seqno";//echo $sql;
				$sql="SELECT kd_produk,qty_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' GROUP BY kd_produk ORDER BY seqno";//echo $sql;
				$hsl=mysql_query($sql);
				$no=0;
				$_adacutting=false;
				$arrrm=array();
				while(list($kd_produk,$qty)=mysql_fetch_array($hsl)){
					if(sanitasi($_POST["qtycutting"][$kd_produk])>0){$qty=sanitasi($_POST["qtycutting"][$kd_produk]);}
					$sql="SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql);
					list($nama,$kode_warna,$kode_size,$satuan)=mysql_fetch_array($hsltemp);
					$sql="SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
					$hsltemp=mysql_query($sql);
					list($warna)=mysql_fetch_array($hsltemp);
					$sql="SELECT size FROM mst_size WHERE kode='$kode_size'";
					$hsltemp=mysql_query($sql);
					list($size)=mysql_fetch_array($hsltemp);
					$sql="SELECT nama FROM satuan WHERE id='$satuan'";
					$hsltemp=mysql_query($sql);
					list($satuan)=mysql_fetch_array($hsltemp);
					if(!sanitasi($_POST["ikatan"][$kd_produk])){$_POST["ikatan"][$kd_produk]=1;}
					//$sql="SELECT sum(qty) FROM job_cutting_detail WHERE kd_produk='$kd_produk' AND no_co IN (SELECT no_co FROM job_cutting WHERE no_jo='$no_jo')";//echo $sql."<br>";
					$sql="SELECT sum(qty) FROM job_cutting_detail WHERE kd_produk='$kd_produk' AND no_co IN (SELECT no_co FROM job_cutting WHERE no_po='$no_po')";//echo $sql."<br>";
					$hsltemp=mysql_query($sql);
					list($qtycuting)=mysql_fetch_array($hsltemp);
					$qtybelum=$qty-$qtycuting;
					$qtybelum=1;
					if($qtybelum>0){
						$_adacutting=true;
						$no++;
						$sql="SELECT sum(jml_gelaran) FROM job_gelaran_detail WHERE no_po='$no_po' AND kd_produk='$kd_produk'";
						$hsltemp=mysql_query($sql);
						list($jml_gelaran)=mysql_fetch_array($hsltemp);
						$sql="SELECT sum(qty) FROM job_cutting_ikatan WHERE no_co IN (SELECT no_co FROM job_cutting WHERE no_po='$no_po') AND kd_produk='$kd_produk'";
						$hsltemp=mysql_query($sql);
						list($jml_terikat)=mysql_fetch_array($hsltemp);
						$maxgelar=$jml_gelaran-$jml_terikat;
						$qtycutting=$qtybelum;
						if(sanitasi($_POST["qtycutting"][$kd_produk])){$qtycutting=sanitasi($_POST["qtycutting"][$kd_produk]);}
						$sql="SELECT qty_produk,kd_barang,satuan,qty,kainmasuk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' AND kd_produk='$kd_produk'";
						$hslrm=mysql_query($sql);
						while(list($qty_produk_rm,$kd_barang_rm,$satuan_rm,$qty_rm,$kainmasuk_rm)=mysql_fetch_array($hslrm)){
							$arrrm[$kd_barang_rm]["satuan_rm"]=$satuan_rm;
							$arrrm[$kd_barang_rm]["totalqty"]+=$qty_rm*$qty_produk_rm;
							$arrrm[$kd_barang_rm]["kainkeluar"]+=$kainmasuk_rm;
						}

						
					if($no%2==1){
						$kelas1="alt";
					 }else{
						 $kelas1="";
						 }

			?>
				<tr class="<?php echo $kelas1?>">
					<td valign="top"><?php echo $no; ?></td>
					<td valign="top"><?php echo $kd_produk; ?></td>
					<td valign="top"><?php echo $nama; ?></td>
					<td valign="top"><?php echo $warna; ?></td>
					<td valign="top"><?php echo $size; ?></td>
					<!--td valign="top" align="right"><?php echo number_format($qty); ?></td-->
					<!--td valign="top" align="right"><?php echo number_format($qtybelum); ?></td-->
					<td valign="top"><input type="text" name="qtycutting[<?php echo $kd_produk; ?>]" value="<?php echo $qty; ?>" size="4"></td>
					<td valign="top"><?php echo $satuan; ?></td>
					<!--td valign="top" align="right"><?php echo number_format($jml_gelaran); ?></td-->
					<!-- td valign="top" align="right"><?php echo number_format($jml_terikat); ?></td -->
					<input type="hidden" size="5" name="ikatan[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["ikatan"][$kd_produk]); ?>">
					<!-- <td valign="top">
						<table border="1">
							<tr>
								<td nowrap>
									<b>
										Polybag
										<input type="submit" name="addikatan[<?php echo $kd_produk; ?>]" value="+">
										<input type="submit" name="incikatan[<?php echo $kd_produk; ?>]" value="-">
									</b>
								</td>
								<td><b>Qty</b></td>
							</tr>
							<?php 
								$totalikat=0;
								for ($ikat=0;$ikat<sanitasi($_POST["ikatan"][$kd_produk]);$ikat++){
									$totalikat+=sanitasi($_POST["qty"][$kd_produk][$ikat]);
							?>
								<tr>
									<td align="right"><?php echo $ikat+1;?></td>
									<td><input type="text" size="5" name="qty[<?php echo $kd_produk; ?>][<?php echo $ikat; ?>]" value="<?php echo sanitasi($_POST["qty"][$kd_produk][$ikat]); ?>"></td>
								</tr>
							<?php
								}
							?>
							<tr>
								<td><b>Total</b></td>
								<td align="right"><b><?php echo number_format($totalikat); ?></b></td>
							</tr>
						</table>
					</td> -->
				</tr>
			<?php
					}
				}
			?>
		</table>
		<!-- <table class="datagrid" cellspacing="0" cellpadding="0">
			<tr>
				<td><br></td>
			</tr>
			<tr>
				<td colspan="6" align="center"><b>PRODUK TAMBAHAN</b></td>
			</tr>
		</table> -->
		<!-- <table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th nowrap>
					<b>No</b>
					<a href="#" onClick="addRow(' ',0,0,'tbltrx','bodytrx');" style="text-decoration:none">
					<img src='images/add.png' alt='add' align='middle' border='0' height='16' hspace='0' width='16'></a>
					<a href="#" onClick="addRow(' ',1,0,'tbltrx','bodytrx');" style="text-decoration:none">
					<img src='images/remove.png' alt='inc' align='middle' border='0' height='16' hspace='0' width='16'></a>
				</th>
				<th><b>Kode Produk</b></th>
				<th><b>Nama Produk</b></th>
				<th><b>Ukuran</b></th>
				<th><b>Qty</b></th>
				<th><b>Satuan</b></th>
			</tr>
			</thead>
			
			<tbody id="tbltrx0">
				<tr id="bodytrx0">
					<td nowrap id="nomor0" align="right">1</td>
					<td nowrap>
						<input type="text" 
							name="kdproduk[0]" 
							id="idkdproduk[0]" 
							onclick="showProduk(this.id,'id_produk[0]',this.value,'id_ukuran[0]','id_satuan[0]');">
					</td>
					<td><input id="id_produk[0]" type="text" readonly></td>
					<td id="id_ukuran[0]">&nbsp;</td>
					<td><input type="text" size="3" name="qty[0]"></td>
					<td id="id_satuan[0]">&nbsp;</td>
				</tr>
			</tbody>
		</table> -->
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<tr>
				<td><br></td>
			</tr>
			<tr>
				<td colspan="8" align="center"><b>RM Terpakai</b></td>
			</tr>
		</table>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th nowrap><b>No</b></th>
				<th><b>Kode RM</b></th>
				<th><b>Nama RM</b></th>
				<th><b>Warna RM</b></th>
				<th><b>Total Qty</b></th>
				<th><b>Satuan</b></th>
				<th><b>Total RM Keluar</b></th>
				<th><b>Total RM Terpakai</b></th>
			</tr>
			</thead>
			<?php
				$no=0;
				foreach ($arrrm as $kode_rm => $arrrmdetail){
					$no++;
					$sql="SELECT nama,warna FROM barangdetail WHERE id='$kode_rm'";
					$hsltemp=mysql_query($sql);
					list($nama_rm,$warna_id)=mysql_fetch_array($hsltemp);
					$sql="SELECT warna FROM mst_warna WHERE kode='$warna_id'";
					$hsltemp=mysql_query($sql);
					list($warna_rm)=mysql_fetch_array($hsltemp);
					$warna_rm="$warna_rm [$warna_id]";
					$satuan_id=$arrrmdetail["satuan_rm"];
					$sql="SELECT nama FROM satuan WHERE id='$satuan_id'";
					$hsltemp=mysql_query($sql);
					list($satuan)=mysql_fetch_array($hsltemp);
					$total_qty=$arrrmdetail["totalqty"];
					$total_kain_keluar=$arrrmdetail["kainkeluar"];
			?>
				<input type="hidden" name="rmpakai_cons[<?php echo $kode_rm; ?>]" value="<?php echo $total_qty; ?>">
				<input type="hidden" name="rmpakai_satuan[<?php echo $kode_rm; ?>]" value="<?php echo $satuan_id; ?>">
				<input type="hidden" name="rmpakai_rm_keluar[<?php echo $kode_rm; ?>]" value="<?php echo $total_kain_keluar; ?>">
				
				<tr class="<?php echo $kelas1?>">
					<td align="right">&nbsp;<?php echo $no; ?></td>
					<td align="right">&nbsp;<?php echo $kode_rm; ?></td>
					<td align="right">&nbsp;<?php echo $nama_rm; ?></td>
					<td align="right">&nbsp;<?php echo $warna_rm; ?></td>
					<td align="right">&nbsp;<?php echo $total_qty; ?></td>
					<td align="right">&nbsp;<?php echo $satuan; ?></td>
					<td align="right">&nbsp;<?php echo $total_kain_keluar; ?></td>
					<td align="right"><input type="text" name="rmpakai[<?php echo $kode_rm; ?>]" value="<?php echo $total_kain_keluar; ?>"></td>
				</tr>
			<?php
				}
			?>
		</table>
		<table width="100%">
			<tr>
				<td align="center"><input type="submit" name="reload" value="Reload"><input type="submit" name="simpan" value="Simpan"></td>
			</tr>
		</table>
	</fieldset>
<form>
<?php
	if(!$_adacutting){
?>
	<script language="javascript">
		alert ('Job Order ini sudah di cutting semua!');
		//window.location='job_cutting_list.php';
	</script>
<?php
	}
?>
<?php include_once "footer.php"; ?>
