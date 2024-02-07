<?php $content_title = "APPROVING CO";
include_once "header.php" ?>
<?php include_once "job_cutting_init.php" ?>
<?php
//include_once "po_manufaktur_approving_init.php";
//echo nomor_manufaktur();exit;
$no_co = sanitasi($_GET["no_co"]);
$sql = "SELECT pabrik FROM job_gelaran WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($gudang) = mysql_fetch_array($hsltemp);

$sql = "SELECT approve2 FROM job_cutting WHERE no_co='$no_co'";
$hsl = mysql_query($sql, $db);
if (mysql_affected_rows($db) < 1) { ?> <script language="javascript">
		history.back();
	</script> <?php exit();
			}
			list($approve) = mysql_fetch_array($hsl);
			if (!$approve) {
				/*Buar Jurnal by suro*/
				//include_once('acc_jurnal_function.php');
				/*Buar Jurnal by suro*/
				$approveby = $_SESSION["username"];
				$sql = "UPDATE job_cutting SET approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_co='$no_co'";
				mysql_query($sql, $db);
				if (mysql_affected_rows($db) > 0) {
					$sql = "SELECT kd_produk,qty FROM job_cutting_detail WHERE no_co='$no_co'";
					$hsl = mysql_query($sql, $db);
					$arrproduk = array();
					while (list($kd_produk, $qtyproduk) = mysql_fetch_array($hsl)) {
						$arrproduk[$kd_produk] += $qtyproduk;
					}
					$sql = "SELECT kd_produk,qty FROM job_cutting_turunan WHERE no_co='$no_co'";
					$hsl = mysql_query($sql, $db);
					$arrprodukturunan = array();
					while (list($kd_produk, $qtyproduk) = mysql_fetch_array($hsl)) {
						$arrprodukturunan[$kd_produk] += $qtyproduk;
					}
					$no_load = no_load();
					$totalqtyproduk = 0;
					$totalqty = 0;
					$totalrp = 0;
					$arrbarang = array();
					$arrbarangdetail = array();
					$seqno = -1;
					#Untuk Journal
					$ArrPrdNom = '';
					foreach ($arrproduk as $kd_produk => $qtyproduk) {
						$sql = "SELECT satuan FROM produk WHERE kode='$kd_produk'";
						$hsltemp = mysql_query($sql, $db);
						list($satuan_produk) = mysql_fetch_array($hsltemp);
						$totalqtyproduk += $qtyproduk;
						//cari produk detail
						$sql = "SELECT kode_barang,qty,satuan FROM produk_detail WHERE kode_produk='$kd_produk'";
						$hslbarang = mysql_query($sql, $db);
						if (mysql_affected_rows($db) > 0) {
							while (list($kode_barang, $qtybarang, $satuanbarang) = mysql_fetch_array($hslbarang)) {
								$seqno++;
								$sql = "SELECT harga FROM barangdetail WHERE id='$kode_barang'";
								$hsltemp = mysql_query($sql, $db);
								list($harga) = mysql_fetch_array($hsltemp);
								$qty = $qtybarang * $qtyproduk;
								$jumlah = $harga * $qty;
								$totalqty += $qty;
								$totalrp += $jumlah;
								$sql = "INSERT INTO job_loading_detail (no_load,seqno,kd_produk,qty_produk,satuan_produk,kd_barang,harga,qty_barang,qty,satuan) VALUES ";
								$sql .= "('$no_load','$seqno','$kd_produk','$qtyproduk','$satuan_produk','$kode_barang','$harga','$qtybarang','$qty','$satuanbarang')";
								mysql_query($sql, $db);
								/*buat journal cutting disini*/
								/* Edited By Goberan */
								// -- Cari Harga
								$sql = "SELECT hargadasar, hargajual FROM produk WHERE kode='$kd_produk'";
								$hsltemp = mysql_query($sql, $db);
								list($hpp, $hpj) = mysql_fetch_array($hsltemp);

								// -- Cek Stok Awal
								$sql = "SELECT stok FROM job_stok WHERE kode_produk='$kd_produk' AND mode_gudang='cutting' AND kode_gudang='$gudang'";
								$hsltemp = mysql_query($sql, $db);
								list($stokawal) = mysql_fetch_array($hsltemp);
								if ($stokawal == "") {
									$stokawal = 0;
								} else {
									$stokawal = $stokawal;
								}
								$stokakhir = $stokawal - $qtyproduk;
								$stokout = $qtyproduk;
								$sql1 = "INSERT INTO `job_stok_card`  VALUES ('$no_co' , '$gudang','cutting', '$kd_produk', '$stokawal','0','$stokout','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())";
								$hsltemp1 = mysql_query($sql1, $db);
								if ($stokawal == "") {
									$sql = "INSERT INTO job_stok (kode_supplier,mode_gudang,kode_gudang,kode_produk,stok,updateby,updatedate) VALUES ";
									$sql .= "('RBN','cutting','$gudang','$kd_produk','$stokakhir','$approveby',NOW())";
									$hsltemp = mysql_query($sql, $db);
								} else {
									$sql = "UPDATE job_stok SET stok='$stokakhir', updatedate=NOW(),updateby='$approveby' WHERE mode_gudang='cutting' AND kode_produk='$kd_produk' AND kode_gudang='$gudang'";
									$hsltemp = mysql_query($sql, $db);
								}

								/* End Goberan */
								$ArrPrdNom[$kd_barang] = $jumlah;
							}
						} else {
							$seqno++;
							$harga = 0;
							$qtybarang = 0;
							$qty = $qtybarang * $qtyproduk;
							$jumlah = $harga * $qty;
							$totalqty += $qty;
							$totalrp += $jumlah;
							$sql = "INSERT INTO job_loading_detail (no_load,seqno,kd_produk,qty_produk,satuan_produk,kd_barang,harga,qty_barang,qty,satuan) VALUES ";
							$sql .= "('$no_load','$seqno','$kd_produk','$qtyproduk','$satuan_produk','$kode_barang','$harga','$qtybarang','$qty','$satuanbarang')";
							mysql_query($sql, $db);
							/* Edited By Goberan */
							// -- Cari Harga
							$sql = "SELECT hargadasar, hargajual FROM produk WHERE kode='$kd_produk'";
							$hsltemp = mysql_query($sql, $db);
							list($hpp, $hpj) = mysql_fetch_array($hsltemp);

							// -- Cek Stok Awal
							$sql = "SELECT stok FROM job_stok WHERE kode_produk='$kd_produk' AND mode_gudang='cutting' AND kode_gudang='$gudang'";
							$hsltemp = mysql_query($sql, $db);
							list($stokawal) = mysql_fetch_array($hsltemp);
							if ($stokawal == "") {
								$stokawal = 0;
							} else {
								$stokawal = $stokawal;
							}
							$stokakhir = $stokawal - $qtyproduk;
							$stokout = $qtyproduk;
							$sql1 = "INSERT INTO `job_stok_card`  VALUES ('$no_co' , '$gudang','cutting', '$kd_produk', '$stokawal','0','$stokout','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())";
							$hsltemp1 = mysql_query($sql1, $db);
							if ($stokawal == "") {
								$sql = "INSERT INTO job_stok (kode_supplier,mode_gudang,kode_gudang,kode_produk,stok,updateby,updatedate) VALUES ";
								$sql .= "('RBN','cutting','$gudang','$kd_produk','$stokakhir','$approveby',NOW())";
								$hsltemp = mysql_query($sql, $db);
							} else {
								$sql = "UPDATE job_stok SET stok='$stokakhir', updatedate=NOW(),updateby='$approveby' WHERE mode_gudang='cutting' AND kode_produk='$kd_produk' AND kode_gudang='$gudang'";
								$hsltemp = mysql_query($sql, $db);
							}

							/* End Goberan */
						}
					}
					$sql = "SELECT no_po,pabrik FROM job_gelaran WHERE no_co='$no_co'";
					$hsltemp = mysql_query($sql, $db);
					list($no_po, $id_pabrik) = mysql_fetch_array($hsltemp);
					$sql = "INSERT INTO job_loading (no_load,no_co,pabrik_dari,pindah_pabrik,tanggal,totalqtyproduk,totalqty,totalrp) VALUES ('$no_load','$no_co','$id_pabrik','0',NOW(),'$totalqtyproduk','$totalqty','$totalrp')";
					mysql_query($sql, $db);
					#Untuk Journal
					//jurnal_cutting($no_co,$ArrPrdNom);
					#Untuk Journal

					//TURUNAN
					$totalqtyproduk = 0;
					$totalqty = 0;
					$totalrp = 0;
					$arrbarang = array();
					$arrbarangdetail = array();
					$seqno = -1;
					// echo "<pre>";
					// print_r($arrprodukturunan);
					// echo "</pre>";
					foreach ($arrprodukturunan as $kd_produk => $qtyproduk) {
						$sql = "SELECT satuan FROM produk WHERE kode='$kd_produk'";
						$hsltemp = mysql_query($sql, $db);
						list($satuan_produk) = mysql_fetch_array($hsltemp);
						$totalqtyproduk += $qtyproduk;
						//cari produk detail
						$sql = "SELECT kode_barang,qty,satuan FROM produk_detail WHERE kode_produk='$kd_produk'";
						$hslbarang = mysql_query($sql, $db);
						if (mysql_affected_rows($db) > 0) {
							while (list($kode_barang, $qtybarang, $satuanbarang) = mysql_fetch_array($hslbarang)) {
								$seqno++;
								$sql = "SELECT harga FROM barangdetail WHERE id='$kode_barang'";
								$hsltemp = mysql_query($sql, $db);
								list($harga) = mysql_fetch_array($hsltemp);
								$qty = $qtybarang * $qtyproduk;
								$jumlah = $harga * $qty;
								$totalqty += $qty;
								$totalrp += $jumlah;
								$sql = "INSERT INTO job_loading_turunan (no_load,seqno,kd_produk,qty_produk,satuan_produk,kd_barang,harga,qty_barang,qty,satuan) VALUES ";
								$sql .= "('$no_load','$seqno','$kd_produk','$qtyproduk','$satuan_produk','$kode_barang','$harga','$qtybarang','$qty','$satuanbarang')";
								mysql_query($sql, $db);
								//echo "<br>$sql=>".mysql_affected_rows($db);
							}
						} else {
							$sql = "INSERT INTO job_loading_turunan (no_load,seqno,kd_produk,qty_produk,satuan_produk,kd_barang,harga,qty_barang,qty,satuan) VALUES ";
							$sql .= "('$no_load','0','$kd_produk','$qtyproduk','$satuan_produk','$kode_barang','$harga','$qtybarang','$qty','$satuanbarang')";
							mysql_query($sql, $db);
							//echo "<br>$sql=>".mysql_affected_rows($db);
						}
					}
				?>
		<script language="javascript">
			alert("CO telah di approve.");
			window.location = "job_cutting_detail.php?no_co=<?php echo $no_co; ?>";
		</script>
	<?php
				} else {
	?>
		<script language="javascript">
			alert("CO gagal di approve, Silakan hubungi Technical Support Anda!");
			window.location = "job_cutting_detail.php?no_co=<?php echo $no_co; ?>";
		</script>
	<?php
				}
			} else {
	?>
	<script language="javascript">
		alert("CO sudah di approve.");
		window.location = "job_cutting_detail.php?no_co=<?php echo $no_co; ?>";
	</script>
<?php
			}
?>
<?php include_once "footer.php" ?>