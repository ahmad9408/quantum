<?php $content_title="DETIL FINISHING"; include_once "header.php" ?>
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
	$no_fin=sanitasi($_GET["no_fin"]);
	$sql="SELECT * FROM job_fin WHERE no_fin='$no_fin'";
	$hsl=mysql_query($sql,$db);
	$rs=mysql_fetch_array($hsl);
	$no_qc=$rs["no_qc"];
	$id_gudang=$rs["gudang"];
	$sql="SELECT no_sew FROM job_qc WHERE no_qc='$no_qc'";
	$hsltemp=mysql_query($sql,$db);
	list($no_sew)=mysql_fetch_array($hsltemp);
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
	foreach($_POST["addrowbsr"] as $add_kd_produk => $value){$_POST["rowbsr"][$add_kd_produk]++;}
	foreach($_POST["incrowbsr"] as $add_kd_produk => $value){$_POST["rowbsr"][$add_kd_produk]--;}
	foreach($_POST["addrowkcl"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkcl"][$add_kd_produk][$i_besar]++;}}
	foreach($_POST["incrowkcl"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkcl"][$add_kd_produk][$i_besar]--;}}
	foreach($_POST["addrowbsrb"] as $add_kd_produk => $value){$_POST["rowbsrb"][$add_kd_produk]++;}
	foreach($_POST["incrowbsrb"] as $add_kd_produk => $value){$_POST["rowbsrb"][$add_kd_produk]--;}
	foreach($_POST["addrowkclb"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkclb"][$add_kd_produk][$i_besar]++;}}
	foreach($_POST["incrowkclb"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkclb"][$add_kd_produk][$i_besar]--;}}
	
	foreach($_POST["addrowbsrt"] as $add_kd_produk => $value){$_POST["rowbsrt"][$add_kd_produk]++;}
	foreach($_POST["incrowbsrt"] as $add_kd_produk => $value){$_POST["rowbsrt"][$add_kd_produk]--;}
	foreach($_POST["addrowkclt"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkclt"][$add_kd_produk][$i_besar]++;}}
	foreach($_POST["incrowkclt"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkclt"][$add_kd_produk][$i_besar]--;}}
	foreach($_POST["addrowbsrbt"] as $add_kd_produk => $value){$_POST["rowbsrbt"][$add_kd_produk]++;}
	foreach($_POST["incrowbsrbt"] as $add_kd_produk => $value){$_POST["rowbsrbt"][$add_kd_produk]--;}
	foreach($_POST["addrowkclbt"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkclbt"][$add_kd_produk][$i_besar]++;}}
	foreach($_POST["incrowkclbt"] as $add_kd_produk => $arrvalue){foreach($arrvalue as $i_besar => $value){$_POST["rowkclbt"][$add_kd_produk][$i_besar]--;}}
	if(sanitasi($_POST["approve2"])){
		$jumlahvalid=true;
		foreach($_POST["qtypoly"] as $barcode => $arrbesar){
			$sql="SELECT grade_a FROM job_qc_detail WHERE no_qc='$no_qc' AND kd_produk='$barcode'";
			$hsltemp=mysql_query($sql,$db);
			list($qty_a)=mysql_fetch_array($hsltemp);
			$subtotqty=0;
			foreach($arrbesar as $i => $arrkecil){
				foreach($arrkecil as $j => $qty){
					$subtotqty+=$qty;
				}
			}
			//echo "$qty_a!=$subtotqty";
			if($qty_a!=$subtotqty){$jumlahvalid=false;break;}
		}
		if($jumlahvalid){
			foreach($_POST["qtypolyb"] as $barcode => $arrbesar){
				$sql="SELECT grade_b FROM job_qc_detail WHERE no_qc='$no_qc' AND kd_produk='$barcode'";
				$hsltemp=mysql_query($sql,$db);
				list($qty_b)=mysql_fetch_array($hsltemp);
				$subtotqtyb=0;
				foreach($arrbesar as $i => $arrkecil){
					foreach($arrkecil as $j => $qty){
						$subtotqtyb+=$qty;
					}
				}
				if($qty_b!=$subtotqtyb){$jumlahvalid=false;break;}
			}
		}
		if($jumlahvalid){//cek yang turunan
			foreach($_POST["qtypolyt"] as $barcode => $arrbesart){
				$sql="SELECT grade_a FROM job_qc_turunan WHERE no_qc='$no_qc' AND kd_produk='$barcode'";
				$hsltemp=mysql_query($sql,$db);
				list($qty_at)=mysql_fetch_array($hsltemp);
				$subtotqtyt=0;
				foreach($arrbesart as $i => $arrkecilt){
					foreach($arrkecilt as $j => $qtyt){
						$subtotqtyt+=$qtyt;
					}
				}
				if($qty_at!=$subtotqtyt){$jumlahvalid=false;break;}
			}
		}
		if($jumlahvalid){//cek yang turunan
			foreach($_POST["qtypolybt"] as $barcode => $arrbesart){
				$sql="SELECT grade_b FROM job_qc_turunan WHERE no_qc='$no_qc' AND kd_produk='$barcode'";
				$hsltemp=mysql_query($sql,$db);
				list($qty_bt)=mysql_fetch_array($hsltemp);
				$subtotqtybt=0;
				foreach($arrbesart as $i => $arrkecilt){
					foreach($arrkecilt as $j => $qtyt){
						$subtotqtybt+=$qtyt;
					}
				}
				if($qty_bt!=$subtotqtybt){$jumlahvalid=false;break;}
			}
		}
		$gudang=sanitasi($_POST["gudang"]);
		if($gudang || true ){
			if($jumlahvalid){//jumlah semua valid
				//echo "OKE";exit;
				$arrpost=base64_encode(serialize($_POST));
				?>
				<form method="POST" action="job_fin_approving2.php?no_fin=<?php echo $no_fin; ?>">
					<input type="hidden" name="arrpost" value="<?php echo $arrpost; ?>">
					<input type="submit" id="approve_2" name="approve2" value="oke" style="visibility:hidden;">
				</form>
				<script language="javascript">
					document.getElementById("approve_2").click();
				</script>
				<?php
					// include_once "footer.php"
					// exit;
			}else{
				?>
					<script language="javascript">
						alert("Jumlah Di Polybag Tidak Valid!");
					</script>
				<?php
			}
		} else {
			?>
				<script language="javascript">
					alert("Silakan Pilih Gudang Distribusi !");
				</script>
			<?php				
		}

	}
	
?>
	<script language="JavaScript">
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
	</script>
	<!--form method="POST" action="job_fin_approving2.php?no_fin=<?php echo $no_fin; ?>"-->
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>?no_fin=<?php echo $no_fin; ?>" class="datagrid" cellspacing="0" cellpadding="0">
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<table valign="top" class="datagrid" cellspacing="0" cellpadding="0">
						<tr class="alt" width="52">
							<td width="100"><b>Pabrik</b></td>
							<td width="2"><b>:</b></td>
							<td><?php echo $pabrik; ?></td>
						</tr>
						<tr width="52">
							<td><b>No PO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_po;?></td>
						</tr>
						<tr class="alt" width="52">
							<td><b>No JO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_jo;?></td>
						</tr>
						<tr width="52">
							<td><b>No CO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_co;?></td>
						</tr>
						<tr class="alt" width="52">
							<td><b>No LOAD</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_load;?></td>
						</tr>
						<tr width="52">
							<td><b>No Sewing</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_sew;?></td>
						</tr>
						<tr class="alt" width="52">
							<td><b>No QC</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_qc;?></td>
						</tr>
						<tr width="52">
							<td><b>No FINISHING</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_fin;?></td>
						</tr>
						<tr class="alt" width="52">
							<td><b>Tanggal</b></td>
							<td><b>:</b></td>
							<td><?php echo $tanggal;?></td>
						</tr>
						<tr width="52">
							<td><b>Total Qty</b></td>
							<td><b>:</b></td>
							<td><?php echo $totalqty;?></td>
						</tr>
						<!--tr>
							<td><b>Gudang Distribusi</b></td>
							<td><b>:</b></td>
							<td>
								<?php
									if(!$approved || $approved2){
										$sql="SELECT nama FROM gudang_distribusi WHERE id='$id_gudang'";
										$hsltemp=mysql_query($sql,$db);
										list($namagudang)=mysql_fetch_array($hsltemp);
										echo $namagudang;
								?>
								<?php
									} else {
								?>
								<select name="gudang">
									<option value="">-Gudang Distribusi-</option>
									<?php
										$sql="SELECT id,nama FROM gudang_distribusi ORDER BY nama";
										$hsltemp=mysql_query($sql,$db);
										while(list($id,$nama)=mysql_fetch_array($hsltemp)){
									?>
										<option value="<?php echo $id; ?>" <?php if(sanitasi($_POST["gudang"])==$id){echo "selected";} ?>><?php echo "$nama [$id]"; ?></option>
									<?php
										}
									?>
								</select>
								<?php
									}
								?>
							</td>
						</tr-->
					</table>
				</td>
			</tr>
		</table>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><b>No</b></th>
				<th nowrap><b>Kode Produk</b></th>
				<th nowrap><b>Nama Produk</b></th>
				<th><b>Warna</b></th>
				<th><b>Size</b></th>
				<th nowrap><b>Qty Finishing</b></th>
				<th nowrap><b>Qty PO</b></th>
				<th nowrap><b>Grade A</b></th>
				<th nowrap><b>Polybag A</b></th>
				<th nowrap><b>Grade B</b></th>
				<th nowrap><b>Polybag B</b></th>
				<th><b>Keterangan</b></th>
			</tr>
			</thead>
			<?php 
				$sql="SELECT kd_produk,qty,keterangan FROM job_fin_detail WHERE no_fin='$no_fin' ORDER BY seqno";
				$hsl=mysql_query($sql,$db);
				$no=0;
				$totalqty=0;
				$totalqtypo=0;
				$totalqty_a=0;
				$totalqty_b=0;
				while(list($kd_produk,$qty,$keterangan)=mysql_fetch_array($hsl)){
					if(!sanitasi($_POST["rowbsr"][$kd_produk])){$_POST["rowbsr"][$kd_produk]=1;}
					if(!sanitasi($_POST["rowbsrb"][$kd_produk])){$_POST["rowbsrb"][$kd_produk]=1;}
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
					$sql="SELECT qty FROM po_markas_pusat_detail  WHERE no_po='$no_po' AND kd_produk='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($qtypo)=mysql_fetch_array($hsltemp);
					$totalqty+=$qty;
					$totalqtypo+=$qtypo;
					$sql="SELECT grade_a,grade_b FROM job_qc_detail WHERE no_qc='$no_qc' AND kd_produk='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($qty_a,$qty_b)=mysql_fetch_array($hsltemp);
					$totalqty_a+=$qty_a;
					$totalqty_b+=$qty_b;
					if($no%2==1){
						$kelas1="alt";
					 }else{
						 $kelas1="";
						 }

			?>
				<input type="hidden" name="rowbsr[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["rowbsr"][$kd_produk]); ?>">
				<input type="hidden" name="rowbsrb[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["rowbsrb"][$kd_produk]); ?>">
				<tr class="<?php echo $kelas1?>">
					<td valign="top"><?php echo $no; ?></td>
					<td valign="top"><?php echo $kd_produk; ?></td>
					<td valign="top"><?php echo $nama; ?></td>
					<td valign="top"><?php echo $warna; ?></td>
					<td valign="top"><?php echo $size; ?></td>
					<td valign="top" align="right"><?php echo number_format($qty); ?></td>
					<td valign="top" align="right"><?php echo number_format($qtypo); ?></td>
					<td valign="top" align="right"><?php echo number_format($qty_a); ?></td>
					<td valign="top" valign="top">
						<?php if($approved && !$approved2){ ?>
							<table class="datagrid" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" nowrap colspan="2">
										Bsr
										<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowbsr[<?php echo $kd_produk; ?>]">
										<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowbsr[<?php echo $kd_produk; ?>]">
									</td>
								</tr>
								<?php for ($i=0;$i<sanitasi($_POST["rowbsr"][$kd_produk]);$i++){ ?>
									<?php if(!sanitasi($_POST["rowkcl"][$kd_produk][$i])){$_POST["rowkcl"][$kd_produk][$i]=1;} ?>
									<input type="hidden" name="rowkcl[<?php echo $kd_produk; ?>][<?php echo $i; ?>]" value="<?php echo sanitasi($_POST["rowkcl"][$kd_produk][$i]); ?>">
									<tr>
										<td valign="top" nowrap><?php echo $i+1; ?></td>
										<td valign="top" nowrap>
											<table class="datagrid" cellspacing="0" cellpadding="0">
												<tr>
													<td colspan="2" nowrap>
														Kcl
														<?php if($approved && !$approved2){ ?>
														<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowkcl[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowkcl[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<?php } ?>
													</td>
												</tr>
												<?php for ($j=0;$j<sanitasi($_POST["rowkcl"][$kd_produk][$i]);$j++){ ?>
													<?php 
														if(!$_POST["qtypoly"][$kd_produk][$i][$j]){
															if($qty_a<5){
																$_POST["qtypoly"][$kd_produk][$i][$j]="0";
															}else{
																$_POST["qtypoly"][$kd_produk][$i][$j]="5";
															}
														}
													?>
													<tr>
														<td nowrap><?php echo $j+1; ?></td>
														<td nowrap><input type="text" name="qtypoly[<?php echo $kd_produk; ?>][<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo sanitasi($_POST["qtypoly"][$kd_produk][$i][$j]); ?>" size="3"></td>
													</tr>
												<?php } ?>
											</table>
										</td>
									</tr>
								<?php } ?>
							</table>
						<?php }else { ?>
							<table class="datagrid" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" nowrap>Bsr</td>
									<td valign="top" nowrap>Kcl</td>
									<td valign="top" nowrap>Qty</td>
								</tr>
								<?php
									$sql="SELECT besar,kecil,qty FROM job_fin_polybag WHERE no_fin='$no_fin' AND kd_produk='$kd_produk' AND grade='a' AND mode='' ORDER BY seqno";
									$hslpoly=mysql_query($sql,$db);
									$besartxt="";
									$keciltxt="";
									while(list($besar,$kecil,$qtypoly)=mysql_fetch_array($hslpoly)){
								?>
									<tr>
										<?php
											if($besartxt!=$besar){
												$besartxt=$besar;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $besartxt+1; ?></td>
										<?php }else{ ?>										
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>										
										<?php
											if($keciltxt!=$kecil){
												$keciltxt=$kecil;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $keciltxt+1; ?></td>
										<?php }else{ ?>
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>
										<td valign="top" nowrap>&nbsp;<?php echo $qtypoly; ?></td>
									</tr>
								<?php
									}
								?>
							</table>
						<?php } ?>
					</td>
					<td valign="top" align="right"><?php echo number_format($qty_b); ?></td>
					<td valign="top" valign="top">
						<?php if($approved && !$approved2){ ?>
							<table  class="datagrid" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" nowrap colspan="2">
										Bsr
										<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowbsrb[<?php echo $kd_produk; ?>]">
										<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowbsrb[<?php echo $kd_produk; ?>]">
										</td>
								</tr>
								<?php for ($i=0;$i<sanitasi($_POST["rowbsrb"][$kd_produk]);$i++){ ?>
									<?php if(!sanitasi($_POST["rowkclb"][$kd_produk][$i])){$_POST["rowkclb"][$kd_produk][$i]=1;} ?>
									<input type="hidden" name="rowkclb[<?php echo $kd_produk; ?>][<?php echo $i; ?>]" value="<?php echo sanitasi($_POST["rowkclb"][$kd_produk][$i]); ?>">
									<tr>
										<td valign="top" nowrap><?php echo $i+1; ?></td>
										<td valign="top" nowrap>
											<table  class="datagrid" cellspacing="0" cellpadding="0">
												<tr>
													<td colspan="2" nowrap>
														Kcl
														<?php if($approved && !$approved2){ ?>
														<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowkclb[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowkclb[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<?php } ?>
													</td>
												</tr>
												<?php for ($j=0;$j<sanitasi($_POST["rowkclb"][$kd_produk][$i]);$j++){ ?>
													
													<?php
														if(!$_POST["qtypolyb"][$kd_produk][$i][$j]){
															if($qty_b<5){
																$_POST["qtypolyb"][$kd_produk][$i][$j]="0";
															}else{
																$_POST["qtypolyb"][$kd_produk][$i][$j]="5";
															}
														}
													?>
													<tr>
														<td nowrap><?php echo $j+1; ?></td>
														<td nowrap><input type="text" name="qtypolyb[<?php echo $kd_produk; ?>][<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo sanitasi($_POST["qtypolyb"][$kd_produk][$i][$j]); ?>" size="3"></td>
													</tr>
												<?php } ?>
											</table>
										</td>
									</tr>
								<?php } ?>
							</table>
						<?php }else { ?>
							<table  class="datagrid" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top" nowrap>Bsr</td>
									<td valign="top" nowrap>Kcl</td>
									<td valign="top" nowrap>Qty</td>
								</tr>
								<?php
									$sql="SELECT besar,kecil,qty FROM job_fin_polybag WHERE no_fin='$no_fin' AND kd_produk='$kd_produk' AND grade='b' AND mode='' ORDER BY seqno";
									$hslpoly=mysql_query($sql,$db);
									$besartxt="";
									$keciltxt="";
									while(list($besar,$kecil,$qtypoly)=mysql_fetch_array($hslpoly)){
								?>
									<tr>
										<?php
											if($besartxt!=$besar){
												$besartxt=$besar;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $besartxt+1; ?></td>
										<?php }else{ ?>										
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>										
										<?php
											if($keciltxt!=$kecil){
												$keciltxt=$kecil;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $keciltxt+1; ?></td>
										<?php }else{ ?>
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>
										<td valign="top" nowrap>&nbsp;<?php echo $qtypoly; ?></td>
									</tr>
								<?php
									}
								?>
							</table>
						<?php } ?>
					</td>
					<?php
						if(!$approved || $approved2){
					?>
					<td valign="top">&nbsp;<?php echo $keterangan; ?></td>
					<?php
						}else{
					?>
						<td valign="top"><input type="text" size="50" name="keterangan[<?php echo $kd_produk;?>]" value="<?php echo sanitasi($_POST["keterangan"][$kd_produk]);?>"></td>
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
				<th align="right"><b><?php echo number_format($totalqtypo); ?></b></th>
				<th align="right"><b><?php echo number_format($totalqty_a); ?></b></th>
				<th>&nbsp;</th>
				<th align="right"><b><?php echo number_format($totalqty_b); ?></b></th>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			</thead>
		</table>
		<table  class="datagrid" cellspacing="0" cellpadding="0">
			<tr><br></tr>
			<tr>
				<td colspan="12" align="center"><b>PRODUK TAMBAHAN</b></td>
			</tr>
		</table>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
			<tr>
				<th><b>No</b></th>
				<th nowrap><b>Kode Produk</b></th>
				<th nowrap><b>Nama Produk</b></th>
				<th><b>Warna</b></th>
				<th><b>Size</b></th>
				<th nowrap><b>Qty Finishing</b></th>
				<th nowrap><b>Qty PO</b></th>
				<th nowrap><b>Grade A</b></th>
				<th nowrap><b>Polybag A</b></th>
				<th nowrap><b>Grade B</b></th>
				<th nowrap><b>Polybag B</b></th>
				<th><b>Keterangan</b></th>
			</tr>
			</thead>
			<?php 
				$sql="SELECT kd_produk,qty,keterangan FROM job_fin_turunan WHERE no_fin='$no_fin' ORDER BY seqno";
				$hsl=mysql_query($sql,$db);
				$no=0;
				$totalqty=0;
				$totalqtypo=0;
				while(list($kd_produk,$qty,$keterangan)=mysql_fetch_array($hsl)){
					if(!sanitasi($_POST["rowbsrt"][$kd_produk])){$_POST["rowbsrt"][$kd_produk]=1;}
					if(!sanitasi($_POST["rowbsrbt"][$kd_produk])){$_POST["rowbsrbt"][$kd_produk]=1;}
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
					$sql="SELECT qty FROM po_markas_pusat_detail  WHERE no_po='$no_po' AND kd_produk='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($qtypo)=mysql_fetch_array($hsltemp);
					$totalqty+=$qty;
					$totalqtypo+=$qtypo;
					$sql="SELECT grade_a,grade_b FROM job_qc_turunan WHERE no_qc='$no_qc' AND kd_produk='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($qty_at,$qty_bt)=mysql_fetch_array($hsltemp);
					$totalqty_at+=$qty_at;
					$totalqty_bt+=$qty_bt;
					if($no%2==1){
						$kelas1="alt";
					 }else{
						 $kelas1="";
						 }
			?>
				<input type="hidden" name="rowbsrt[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["rowbsrt"][$kd_produk]); ?>">
				<input type="hidden" name="rowbsrbt[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["rowbsrbt"][$kd_produk]); ?>">
				<tr class="<?php echo $kelas1?>">
					<td valign="top"><?php echo $no; ?></td>
					<td valign="top"><?php echo $kd_produk; ?></td>
					<td valign="top"><?php echo $nama; ?></td>
					<td valign="top"><?php echo $warna; ?></td>
					<td valign="top"><?php echo $size; ?></td>
					<td valign="top" align="right"><?php echo number_format($qty); ?></td>
					<td valign="top" align="right"><?php echo number_format($qtypo); ?></td>
					<td valign="top" align="right"><?php echo number_format($qty_at); ?></td>
					<td valign="top" valign="top">
						<?php if($approved && !$approved2){ ?>
							<table border="1" cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" nowrap colspan="2">
										Bsr
										<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowbsrt[<?php echo $kd_produk; ?>]">
										<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowbsrt[<?php echo $kd_produk; ?>]">
										</td>
								</tr>
								<?php for ($i=0;$i<sanitasi($_POST["rowbsrt"][$kd_produk]);$i++){ ?>
									<?php if(!sanitasi($_POST["rowkclt"][$kd_produk][$i])){$_POST["rowkclt"][$kd_produk][$i]=1;} ?>
									<input type="hidden" name="rowkclt[<?php echo $kd_produk; ?>][<?php echo $i; ?>]" value="<?php echo sanitasi($_POST["rowkclt"][$kd_produk][$i]); ?>">
									<tr>
										<td valign="top" nowrap><?php echo $i+1; ?></td>
										<td valign="top" nowrap>
											<table border="1" cellpadding="0" cellspacing="0">
												<tr>
													<td colspan="2" nowrap>
														Kcl
														<?php if($approved && !$approved2){ ?>
														<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowkclt[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowkclt[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<?php } ?>
													</td>
												</tr>
												<?php for ($j=0;$j<sanitasi($_POST["rowkclt"][$kd_produk][$i]);$j++){ ?>
													<?php
														if(!$_POST["qtypolyt"][$kd_produk][$i][$j]){
															if($qty_at<5){
																$_POST["qtypolyt"][$kd_produk][$i][$j]="0";
															}else{
																$_POST["qtypolyt"][$kd_produk][$i][$j]="5";
															}
														}
													?>
													<tr>
														<td nowrap><?php echo $j+1; ?></td>
														<td nowrap><input type="text" name="qtypolyt[<?php echo $kd_produk; ?>][<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo sanitasi($_POST["qtypolyt"][$kd_produk][$i][$j]); ?>" size="3"></td>
													</tr>
												<?php } ?>
											</table>
										</td>
									</tr>
								<?php } ?>
							</table>
						<?php }else { ?>
							<table border="1" cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" nowrap>Bsr</td>
									<td valign="top" nowrap>Kcl</td>
									<td valign="top" nowrap>Qty</td>
								</tr>
								<?php
									$sql="SELECT besar,kecil,qty FROM job_fin_polybag WHERE no_fin='$no_fin' AND kd_produk='$kd_produk' AND grade='a' AND mode='t' ORDER BY seqno";
									$hslpoly=mysql_query($sql,$db);
									$besartxt="";
									$keciltxt="";
									while(list($besar,$kecil,$qtypoly)=mysql_fetch_array($hslpoly)){
								?>
									<tr>
										<?php
											if($besartxt!=$besar){
												$besartxt=$besar;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $besartxt+1; ?></td>
										<?php }else{ ?>										
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>										
										<?php
											if($keciltxt!=$kecil){
												$keciltxt=$kecil;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $keciltxt+1; ?></td>
										<?php }else{ ?>
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>
										<td valign="top" nowrap>&nbsp;<?php echo $qtypoly; ?></td>
									</tr>
								<?php
									}
								?>
							</table>
						<?php } ?>
					</td>
					<td valign="top" align="right"><?php echo number_format($qty_bt); ?></td>
					<td valign="top" valign="top">
						<?php if($approved && !$approved2){ ?>
							<table border="1" cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" nowrap colspan="2">
										Bsr
										<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowbsrbt[<?php echo $kd_produk; ?>]">
										<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowbsrbt[<?php echo $kd_produk; ?>]">
										</td>
								</tr>
								<?php for ($i=0;$i<sanitasi($_POST["rowbsrbt"][$kd_produk]);$i++){ ?>
									<?php if(!sanitasi($_POST["rowkclbt"][$kd_produk][$i])){$_POST["rowkclbt"][$kd_produk][$i]=1;} ?>
									<input type="hidden" name="rowkclbt[<?php echo $kd_produk; ?>][<?php echo $i; ?>]" value="<?php echo sanitasi($_POST["rowkclbt"][$kd_produk][$i]); ?>">
									<tr>
										<td valign="top" nowrap><?php echo $i+1; ?></td>
										<td valign="top" nowrap>
											<table border="1" cellpadding="0" cellspacing="0">
												<tr>
													<td colspan="2" nowrap>
														Kcl
														<?php if($approved && !$approved2){ ?>
														<input type="submit" style="width:7px;font-size:8px;" value="+" name="addrowkclbt[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<input type="submit" style="width:7px;font-size:8px;" value="-" name="incrowkclbt[<?php echo $kd_produk; ?>][<?php echo $i; ?>]">
														<?php } ?>
													</td>
												</tr>
												<?php for ($j=0;$j<sanitasi($_POST["rowkclbt"][$kd_produk][$i]);$j++){ ?>
													<?php
														if(!$_POST["qtypolybt"][$kd_produk][$i][$j]){
															if($qty_bt<5){
																$_POST["qtypolybt"][$kd_produk][$i][$j]="0";
															}else{
																$_POST["qtypolybt"][$kd_produk][$i][$j]="5";
															}
														}
													?>
													<tr>
														<td nowrap><?php echo $j+1; ?></td>
														<td nowrap><input type="text" name="qtypolybt[<?php echo $kd_produk; ?>][<?php echo $i; ?>][<?php echo $j; ?>]" value="<?php echo sanitasi($_POST["qtypolybt"][$kd_produk][$i][$j]); ?>" size="3"></td>
													</tr>
												<?php } ?>
											</table>
										</td>
									</tr>
								<?php } ?>
							</table>
						<?php }else { ?>
							<table border="1" cellpadding="0" cellspacing="0">
								<tr>
									<td valign="top" nowrap>Bsr</td>
									<td valign="top" nowrap>Kcl</td>
									<td valign="top" nowrap>Qty</td>
								</tr>
								<?php
									$sql="SELECT besar,kecil,qty FROM job_fin_polybag WHERE no_fin='$no_fin' AND kd_produk='$kd_produk' AND grade='b' AND mode='t' ORDER BY seqno";
									$hslpoly=mysql_query($sql,$db);
									$besartxt="";
									$keciltxt="";
									while(list($besar,$kecil,$qtypoly)=mysql_fetch_array($hslpoly)){
								?>
									<tr>
										<?php
											if($besartxt!=$besar){
												$besartxt=$besar;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $besartxt+1; ?></td>
										<?php }else{ ?>										
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>										
										<?php
											if($keciltxt!=$kecil){
												$keciltxt=$kecil;
										?>
											<td valign="top" nowrap>&nbsp;<?php echo $keciltxt+1; ?></td>
										<?php }else{ ?>
											<td valign="top" nowrap>&nbsp;</td>
										<?php } ?>
										<td valign="top" nowrap>&nbsp;<?php echo $qtypoly; ?></td>
									</tr>
								<?php
									}
								?>
							</table>
						<?php } ?>
					</td>
					<?php
						if(!$approved || $approved2){
					?>
					<td valign="top">&nbsp;<?php echo $keterangan; ?></td>
					<?php
						}else{
					?>
						<td valign="top"><input type="text" size="50" name="keteranganturunan[<?php echo $kd_produk;?>]" value="<?php echo sanitasi($_POST["keteranganturunan"][$kd_produk]);?>"></td>
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
				<td align="right"><b><?php echo number_format($totalqtypo); ?></b></td>
				<td align="right"><b><?php echo number_format($totalqty_at); ?></b></td>
				<td>&nbsp;</td>
				<td align="right"><b><?php echo number_format($totalqty_bt); ?></b></td>
				<td>&nbsp;</td>
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
								?> <!--input type="button" name="approve" value="Approve II" onclick="if(confirm('Approving Finishing <?php echo $no_fin; ?>?')){submit();}"--> <?php
								?> <input type="submit" name="approve2" value="Approve II"> <?php
							}
						}else{
							?> <input type="button" value="Approve I" onclick="if(confirm('Approving Finishing <?php echo $no_fin; ?>?')){window.location='job_fin_approving.php?no_fin=<?php echo $no_fin; ?>';}"> <?php
						}
					?>
					<?php
						if($approved2){
							$sql="SELECT no_do FROM do_produk WHERE no_fin='$no_fin'";
							$hsltemp=mysql_query($sql,$db);
							if(mysql_affected_rows($db)>0){
								?> <input type="button" value="DO PRODUK" onclick="window.location='do_produk_list.php?no_fin=<?php echo $no_fin;?>';"> <?php
							}
						}
						if($approved){
							?><input type="button" value="Print Mode" onclick="window.open('job_fin_print.php?no_fin=<?php echo $no_fin; ?>','job_fin_print','width=800,height=400,menubar=yes,scrollbars=yes');"><?php
						}
					?>
					<input type="button" value="Kembali" onclick="window.location='job_fin_list.php';">
				</td>
			</tr>
		</table>
	</form>
<?php include_once "footer.php" ?>