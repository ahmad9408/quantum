<?php 
	$content_title="TAMBAH PPIC";
	include_once "header.php";
	include_once "job_cutting_init.php";
	$no_manufaktur=sanitasi($_GET['no_manufaktur']);
	$sql="SELECT closeco FROM po_manufaktur WHERE no_manufaktur='$no_manufaktur'";
	$hsl=mysql_query($sql,$db);
	if(mysql_affected_rows($db)<1){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No Po Tidak Ada");
				window.location="job_gelaran_list.php";
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
	/* $sql="SELECT no_po FROM job_gelaran WHERE no_po='$no_manufaktur'";
	$hsl=mysql_query($sql,$db);
	if(mysql_affected_rows($db)>0 && false){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No Po ini Sudah dilakukan entry gelaran!");
				window.location="job_gelaran_list.php";
			</script>
		<?php
	} */
	if (sanitasi($_POST['simpan'])){

		$arrkebutuhanrm=array();
		foreach ($_POST["kainmasuk"] as $barcode => $arrrm){
			foreach ($arrrm as $kdrm=> $qty){
				$arrkebutuhanrm[$kdrm]+=$qty;				
			}
		}

		
		$kebutuhanterpenuhi=true;
		foreach ($arrkebutuhanrm as $kdrm => $qtykebutuhan){
			//cari stok rm
			$tomorrow=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
			//cari qty masuk
			$sql="SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$kdrm' AND tanggal < '$tomorrow%' AND (mode='1' OR mode='2' OR mode='4')";
			//echo "<br>$sql";
			$hsltemp=mysql_query($sql,$db);
			list($stokin)=mysql_fetch_array($hsltemp);
			$stokin+=0;
			
			//cari qty keluar
			$sql="SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$kdrm' AND tanggal < '$tomorrow%' AND mode='3'";
			//echo "<br>$sql";
			$hsltemp=mysql_query($sql,$db);
			list($stokout)=mysql_fetch_array($hsltemp);
			$stokout+=0;				
			$stokrm=$stokin-$stokout;
			if($stokrm<$qtykebutuhan){
				$kebutuhanterpenuhi=false;
				break;
			}
		}
		/*
		if(!$kebutuhanterpenuhi){
			//include_once "footer.php";
			?>
				<script language="javascript">
					alert("Stok RM tidak mencukupi.");
					//window.location="job_gelaran_list.php";
				</script>
			<?php
			//exit; */
		// Baru di tutup sama goberan }else{



			$pabrik=sanitasi($_POST["pabrik"]);
			if ( $pabrik != ""){
			$no_co=no_co();		
			$sql="INSERT INTO job_cutting (no_co,no_jo,no_po,tanggal,kd_supplier,kd_produk,totalqty,totalrp,realcutting) VALUES ('$no_co','','$no_manufaktur',NOW(),'','','0','0','0')";
			mysql_query($sql,$db);
			if(mysql_affected_rows($db)<=0){
				?>
					<script type="text/javascript">
						alert("No CO Gagal Tersimpan.");
						window.location="job_gelaran_list.php";
					</script>
				<?php
			}
			$seqno=-1;
			$sql="SELECT kd_produk,qty FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
			$hslprod=mysql_query($sql,$db);

			while(list($kd_produk,$qtyprod)=mysql_fetch_array($hslprod)){
				$barcode=$kd_produk;

				$qty_produk=sanitasi($_POST["qtyproduk"][$barcode]);
echo $_POST["qtyproduk"][$barcode]."<br>";
echo $koderm;
echo sanitasi($_POST["supplier"][$barcode][$koderm])."<br>";
				
					

						$seqno++;
						$jml_gelaran=0;
						$supplier=sanitasi($_POST["supplier"][$barcode][$koderm]);
						$sql="SELECT satuan,qty,kode_bara FROM produk_detail WHERE kode_produk='$barcode' ";
						$hsltemp=mysql_query($sql,$db);
						list($satuan,$qty)=mysql_fetch_array($hsltemp);
						$sql="INSERT INTO job_gelaran_detail (no_po,no_co,seqno,kd_produk,qty_produk,kd_barang,supplier,satuan,qty,kainmasuk,jml_gelaran) VALUES ";
						$sql.="('$no_manufaktur','$no_co','$seqno','$barcode','$qty_produk','$koderm','$supplier','$satuan','$qty','$qty_kainmasuk','$jml_gelaran')";
						// echo "<br>$sql";
						mysql_query($sql,$db);

$sql="INSERT INTO `job_cutting_detail`
            (`no_co`,
             `seqno`,
             `kd_produk`,
             `qty`,
             `coa`)
VALUES ('$no_co',
        '$seqno',
        '$barcode',
        '$qty_produk',
        '')";
mysql_query($sql,$db);					
				
			}
			$pabrik=sanitasi($_POST["pabrik"]);
			$updateby=$_SESSION["username"];
			$sql="INSERT INTO job_gelaran (no_po,no_co,pabrik,updateby,updatedate) VALUES ('$no_manufaktur','$no_co','$pabrik','$updateby',NOW())";
			// echo "<br>$sql";
			mysql_query($sql,$db);
			if(mysql_affected_rows($db)>0){
				?>
					<script type="text/javascript">
						alert("CO Tersimpan.");
						window.location="job_gelaran_list.php";
					</script>
				<?php
			}else{
				?>
					<script type="text/javascript">
						alert("CO Gagal Tersimpan.");
						window.location="job_gelaran_list.php";
					</script>
				<?php
			}
			} else {
				?>
					<script type="text/javascript">
						alert("CO Gagal Tersimpan Karena Pabrik Belum Dipilih.");
						window.location="job_gelaran_list.php";
					</script>
				<?php
			}
		// baru di tutup Sama goberan }
	}
	$masihada=false;
// echo "Username : $username<br>";    
?>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?no_manufaktur=<?php echo $no_manufaktur; ?>">
	<fieldset>
		<table>
			<tr>
				<td><b>No Manufaktur</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $no_manufaktur; ?></b></td>
			</tr>
			<tr>
				<td><b>Pabrik</b></td>
				<td><b>:</b></td>
				<td>
					<select name="pabrik">
						<!-- option value="">-Pabrik-</option -->
						<?php
							$_pabrik="";
							if(strtoupper($_SESSION['outlettype'])=="P"){
								$_pabrik=$_SESSION['outlet'];
							}
                            
                            /* C e k  A u t h */
                            /* if ( $username !='superuser') {
                                $sql="SELECT pabrik FROM user_account WHERE username='$username'";
							    $hsl=mysql_query($sql,$db);
                                list($pabrik)=mysql_fetch_array($hsl);
                                $sql="SELECT id,nama FROM pabrik WHERE id='$pabrik' ORDER BY nama";
                            } else { */
                                $sql="SELECT id,nama FROM pabrik WHERE id LIKE '$_pabrik%' ORDER BY nama";
                            // }
                            $hsltemp=mysql_query($sql,$db);
							while(list($id,$nama)=mysql_fetch_array($hsltemp)){
						?>
								<option value="<?php echo $id; ?>"><?php echo "$nama [$id]"; ?></option>
						<?php
							}
						?>
					</select>
				</td>
			</tr>
		</table>
		<table border="1">
			<tr>
				<td><b>No</b></td>
				<td><b>Kode Produk</b></td>
				<td><b>Nama Produk</b></td>
				<td><b>Qty Produk</b></td>
			</tr>			
			<?php
				$sql="SELECT kd_produk,qty FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
				$hslprod=mysql_query($sql,$db);
				$no=0;
				while(list($kd_produk,$qtyprod)=mysql_fetch_array($hslprod)){
					// echo $kd_produk."=$qtyprod&nbsp;<br>";
					$sql="SELECT nama FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($nama)=mysql_fetch_array($hsltemp);
					$no++;
					$sql1="SELECT sum(qty_produk) FROM job_gelaran_detail WHERE no_po='$no_manufaktur' AND kd_produk='$kd_produk'";
					$hsltemp1=mysql_query($sql1,$db);
					list($qtyprodsudah)=mysql_fetch_array($hsltemp1);
					$sisaqtyprod=$qtyprod-$qtyprodsudah;
					// echo "<br>$kd_produk | $qtyprod - $qtyprodsudah  Sisa Qty=<b>".$sisaqtyprod."</b><br>";
					$jumlahqtyprod+=$qtyprod;
					$jumlahqtyprodsudah+=$qtyprodsudah;
					if($sisaqtyprod>0){
						$masihada=true;
			?>
					<tr>
						<td><?php echo $no; ?></td>
						<td><?php echo $kd_produk; ?></td>
						<td><?php echo $nama; ?></td>
						<!--td align="right"><?php /* echo number_format($qtyprod); */ echo $qtyprod;?></td-->
						<td align="right"><input type="text" size="3" name="qtyproduk[<?php echo $kd_produk; ?>]" value="<?php echo number_format($sisaqtyprod); /*echo $sisaqtyprod;*/ ?>"><?php echo $kd_produk;?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="3">
							<!-- table width="100%" border="1">
								<tr>
									<td><b>Kode RM</b></td>
									<td><b>Nama RM</b></td>
									<td><b>Warna</b></td>
									<td><b>Supplier</b></td>
									<td><b>Cons</b></td>
									<td><b>Total Qty</b></td>
									<td><b>Satuan</b></td>
									<td><b>Kain Keluar</b></td>
									<td><b>Stok RM</b></td>
									<td><b>Jml Gelaran</b></td>
								</tr -->
								<?php
/*
									$sql="SELECT produk_detail.kode_produk,produk_detail.kode_barang,barangdetail.nama,produk_detail.qty,satuan.nama,barangdetail.mode_gelar ";
									$sql.="FROM produk_detail INNER JOIN barangdetail ON (produk_detail.kode_barang = barangdetail.id) INNER JOIN satuan ON (produk_detail.satuan = satuan.id)";
									$sql.="WHERE produk_detail.kode_produk='$kd_produk'";
									$hslbrg=mysql_query($sql,$db);
									while(list($kd_produk_det,$koderm,$namarm,$qtyrm,$satuan,$modegelar)=mysql_fetch_array($hslbrg)){
										$totalqtyrm=$qtyrm*$qtyprod;
										$sql="SELECT warna FROM mst_warna WHERE kode IN (SELECT warna FROM barangdetail WHERE id='$koderm')";
										$hsltemp=mysql_query($sql,$db);
										list($warna)=mysql_fetch_array($hsltemp);
										
										$tomorrow=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
										//cari qty masuk
										$sql="SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$koderm' AND tanggal < '$tomorrow%' AND (mode='1' OR mode='2' OR mode='4')";
										//echo "<br>$sql";
										$hsltemp=mysql_query($sql,$db);
										list($stokin)=mysql_fetch_array($hsltemp);
										$stokin+=0;
										
										//cari qty keluar
										$sql="SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$koderm' AND tanggal < '$tomorrow%' AND mode='3'";
										//echo "<br>$sql";
										$hsltemp=mysql_query($sql,$db);
										list($stokout)=mysql_fetch_array($hsltemp);
										$stokout+=0;
										
										$stokrm=$stokin-$stokout;
								?>
									<tr>
										<td><?php echo $koderm; ?></td>
										<td><?php echo $namarm; ?></td>
										<td><?php echo $warna; ?></td>
										<td>
											<select name="supplier[<?php echo $kd_produk; ?>][<?php echo $koderm; ?>]">
												<option value="">-supplier-</option>
												<?php
													$sql="SELECT id,nama FROM supplier ORDER BY nama";
													$hsltemp=mysql_query($sql,$db);
													while(list($id,$nama)=mysql_fetch_array($hsltemp)){
												?>
														<option value="<?php echo $id; ?>"><?php echo "$nama [$id]"; ?></option>
												<?php
													}
												?>
											</select>
										</td>
										<td align="right"><?php echo number_format($qtyrm,3,",","."); ?></td>
										<td align="right"><?php echo number_format($totalqtyrm,2,",","."); ?></td>
										<td><?php echo $satuan; ?></td>
										<td align="right">
											<?php 
												if($modegelar){
											?>
												<input type="text" name="kainmasuk[<?php echo $kd_produk; ?>][<?php echo $koderm; ?>]" size="4" value="<?php echo $totalqtyrm; ?>">
											<?php
												}else{echo "&nbsp;";}
											?>
										</td>
										<!--td align="right">&nbsp;
											<?php 
												if($modegelar){
											?>
												<input type="text" name="jml_gelaran[<?php echo $kd_produk; ?>][<?php echo $koderm; ?>]" size="4" value="<?php echo $totalqtyrm; ?>">
											<?php
												}else{echo "&nbsp;";}
											?>
										</td-->
										<td align="right"><?php echo number_format($stokrm,3,",","."); ?></td>
									</tr>
								<?php
									}*/
								?>
							<!-- /table -->
						</td>
					</tr>
			<?php
					}
				}
		// echo "TOTAL QTY $jumlahqtyprod & $jumlahqtyprodsudah";
			?>
		</table>
		
		<table width="100%">
			<tr>
				<td align="center"><input type="submit" name="simpan" value="Simpan"></td>
			</tr>
		</table>
	</fieldset>
<form>
<?php include_once "footer.php"; ?>
<?php
	if(!$masihada){
		?>
			<script language="javascript">
				alert("No Po ini Sudah dilakukan entry CO!");
				window.location="job_gelaran_list.php";
			</script>
		<?php
	}
?>
