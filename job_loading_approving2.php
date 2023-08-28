<?php $content_title = "APPROVING CO";
include_once "header.php";
include_once "job_loading_init.php";
include "pdo_produksi/Db.class.php";
?>
<?php
//include_once "po_manufaktur_approving_init.php";
//echo nomor_manufaktur();exit;
$no_load = sanitasi($_GET["no_load"]);
$no_co = sanitasi($_GET["no_co"]);


$pabrik_tujuan = sanitasi($_GET["pabrik"]);
$sql = "SELECT pabrik_dari,approve2 FROM job_loading WHERE no_load='$no_load'";
$hsl = mysql_query($sql, $db);
if (mysql_affected_rows($db) < 1) { ?> <script language="javascript">
		history.back();
	</script> <?php exit();
			}
			list($pabrik_dari, $approve) = mysql_fetch_array($hsl);
			if (!$approve) {
				/*Buar Jurnal by suro*/
				//include_once('acc_jurnal_function.php');
				/*Buar Jurnal by suro*/
				$pindah_pabrik = 0;
				if ($pabrik_tujuan != $pabrik_dari) {
					$pindah_pabrik = 1;
				}
				$approveby = $_SESSION["username"];
				$no_load_new = no_load_pindah($no_load);

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("UPDATE job_loading SET pabrik_tujuan='$pabrik_tujuan',pindah_pabrik='$pindah_pabrik',approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_load='$no_load'");
					mysql_query($sql, $db);

					$executeTransaction = $sql->executeTransaction();
				} catch (PDOException $e) {
					//atau (Exception $e) 
					$rollBack = $sql->rollBack();
					echo "error msg: " . $e->getMessage();
					throw $e;
				}


				if (mysql_affected_rows($db) > 0) {
					if ($pindah_pabrik) {
						$no_load_new = no_load_pindah($no_load);
						$sql = "SELECT * FROM job_loading_detail WHERE no_load='$no_load' ORDER BY seqno";
						$hsl = mysql_query($sql, $db);
						$seqno = -1;
						while ($rs_detail_load = mysql_fetch_array($hsl)) {
							$seqno++;
							$kd_produk = $rs_detail_load["kd_produk"];
							$qty_produk = $rs_detail_load["qty_produk"];
							$satuan_produk = $rs_detail_load["satuan_produk"];
							$kd_barang = $rs_detail_load["kd_barang"];
							$kd_var = $rs_detail_load["kd_var"];
							$harga = $rs_detail_load["harga"];
							$qty_barang = $rs_detail_load["qty_barang"];
							$qty = $rs_detail_load["qty"];
							$satuan = $rs_detail_load["satuan"];

							$sql = new Db();

							try {
								$beginTransaction = $sql->beginTransaction();

								$sql->query("INSERT INTO `job_loading_detail` (`no_load`, `seqno`, `kd_produk`, `qty_produk`, `satuan_produk`, `kd_barang`, `kd_var`, `harga`, `qty_barang`, `qty`, `satuan`)
							VALUES ('$no_load_new','$seqno','$kd_produk','$qty_produk','$satuan_produk','$kd_barang','$kd_var','$harga','$qty_barang','$qty','$satuan')");
								mysql_query($sql, $db);

								$executeTransaction = $sql->executeTransaction();
							} catch (PDOException $e) {
								//atau (Exception $e) 
								$rollBack = $sql->rollBack();
								echo "error msg: " . $e->getMessage();
								throw $e;
							}
						}

						$sql = "SELECT * FROM job_loading WHERE no_load='$no_load'";
						$hsl = mysql_query($sql, $db);
						$rs_load = mysql_fetch_array($hsl);
						$no_co = $rs_load["no_co"];
						$pabrik_dari = $pabrik_tujuan;
						$pindah_pabrik = "1";
						$totalqtyproduk = $rs_load["totalqtyproduk"];
						$totalqty = $rs_load["totalqty"];
						$totalrp = $rs_load["totalrp"];

						$sql = new Db();

						try {
							$beginTransaction = $sql->beginTransaction();

							$sql->query("INSERT INTO `job_loading` (`no_load`, `no_co`, `pabrik_dari`, `pabrik_tujuan`, `pindah_pabrik`, `tanggal`, `totalqtyproduk`, `totalqty`, `totalrp`) VALUES
						('$no_load_new','$no_co','$pabrik_dari','$pabrik_tujuan','$pindah_pabrik',NOW(),'$totalqtyproduk','$totalqty','$totalrp')");
							mysql_query($sql, $db);

							$executeTransaction = $sql->executeTransaction();
						} catch (PDOException $e) {
							//atau (Exception $e) 
							$rollBack = $sql->rollBack();
							echo "error msg: " . $e->getMessage();
							throw $e;
						}

						$sql = "SELECT * FROM job_loading_turunan WHERE no_load='$no_load' ORDER BY seqno";
						$hsl = mysql_query($sql, $db);
						$seqno = -1;
						while ($rs_turunan_load = mysql_fetch_array($hsl)) {
							$seqno++;
							$kd_produk = $rs_turunan_load["kd_produk"];
							$qty_produk = $rs_turunan_load["qty_produk"];
							$satuan_produk = $rs_turunan_load["satuan_produk"];
							$kd_barang = $rs_turunan_load["kd_barang"];
							$kd_var = $rs_turunan_load["kd_var"];
							$harga = $rs_turunan_load["harga"];
							$qty_barang = $rs_turunan_load["qty_barang"];
							$qty = $rs_turunan_load["qty"];
							$satuan = $rs_turunan_load["satuan"];

							$sql = new Db();

							try {
								$beginTransaction = $sql->beginTransaction();

								$sql->query("INSERT INTO `job_loading_turunan` (`no_load`, `seqno`, `kd_produk`, `qty_produk`, `satuan_produk`, `kd_barang`, `kd_var`, `harga`, `qty_barang`, `qty`, `satuan`) VALUES
							('$no_load_new','$seqno','$kd_produk','$qty_produk','$satuan_produk','$kd_barang','$kd_var','$harga','$qty_barang','$qty','$satuan')");
								mysql_query($sql, $db);

								$executeTransaction = $sql->executeTransaction();
							} catch (PDOException $e) {
								//atau (Exception $e) 
								$rollBack = $sql->rollBack();
								echo "error msg: " . $e->getMessage();
								throw $e;
							}
						}
					}

					$sql = "SELECT kd_produk,qty_produk,satuan_produk FROM job_loading_detail WHERE no_load='$no_load' GROUP BY kd_produk ORDER BY seqno";
					$hsl = mysql_query($sql, $db);
					$arrproduk = array();
					$arrproduksatuan = array();
					while (list($kd_produk, $qty_produk, $satuan_produk) = mysql_fetch_array($hsl)) {
						$arrproduk[$kd_produk] += $qty_produk;
						$arrproduksatuan[$kd_produk] = $satuan_produk;

						/* 08 Oktober 2010 Time 13:04 PM - Edited This Here By Goberan */
						// -- Cari Harga
						$sql = "SELECT pabrik_dari FROM job_loading WHERE no_load='$no_load' AND no_co='$no_co'";
						$hsltemp = mysql_query($sql, $db);
						list($gudang) = mysql_fetch_array($hsltemp);

						$sql = "SELECT hargadasar, hargajual FROM produk WHERE kode='$kd_produk'";
						$hsltemp = mysql_query($sql, $db);
						list($hpp, $hpj) = mysql_fetch_array($hsltemp);

						// -- Cek Stok Awal
						$sql = "SELECT stok FROM job_stok WHERE kode_produk='$kd_produk' AND mode_gudang='loading' AND kode_gudang='$gudang'";
						$hsltemp = mysql_query($sql, $db);
						list($stokawal) = mysql_fetch_array($hsltemp);
						if ($stokawal == "") {
							$stokawal = 0;
						} else {
							$stokawal = $stokawal;
						}
						$stokakhir = $stokawal - $qty_produk;
						$stokout = $qty_produk;


						$sql1 = new Db();

						try {
							$beginTransaction = $sql1->beginTransaction();



							$sql1->query("INSERT INTO `job_stok_card`  VALUES ('$no_load' , '$gudang','loading', '$kd_produk', '$stokawal','0','$stokout','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())");
							$hsltemp1 = mysql_query($sql1, $db);

							$executeTransaction = $sql1->executeTransaction();
						} catch (PDOException $e) {
							//atau (Exception $e) 
							$rollBack = $sql1->rollBack();
							echo "error msg: " . $e->getMessage();
							throw $e;
						}



						if ($stokawal == "") {

							$sql = new Db();

							try {
								$beginTransaction = $sql->beginTransaction();

								$sql->query("INSERT INTO job_stok (kode_supplier,mode_gudang,kode_gudang,kode_produk,stok,updateby,updatedate) VALUES
							('RBN','loading','$gudang','$kd_produk','$stokakhir','$approveby',NOW())");
								$hsltemp = mysql_query($sql, $db);

								$executeTransaction = $sql->executeTransaction();
							} catch (PDOException $e) {
								//atau (Exception $e) 
								$rollBack = $sql->rollBack();
								echo "error msg: " . $e->getMessage();
								throw $e;
							}
						} else {
							$sql = new Db();

							try {
								$beginTransaction = $sql->beginTransaction();

								$sql->query("UPDATE job_stok SET stok='$stokakhir', updatedate=NOW(),updateby='$approveby' WHERE mode_gudang='loading' AND kode_produk='$kd_produk' AND kode_gudang='$gudang'");
								$hsltemp = mysql_query($sql, $db);

								$executeTransaction = $sql->executeTransaction();
							} catch (PDOException $e) {
								//atau (Exception $e) 
								$rollBack = $sql->rollBack();
								echo "error msg: " . $e->getMessage();
								throw $e;
							}
						}

						/* End Script */
					}
					//TURUNAN
					$sql = "SELECT kd_produk,qty_produk,satuan_produk FROM job_loading_turunan WHERE no_load='$no_load' GROUP BY kd_produk ORDER BY seqno";
					$hsl = mysql_query($sql, $db);
					$arrprodukturunan = array();
					$arrproduksatuanturunan = array();
					while (list($kd_produk, $qty_produk, $satuan_produk) = mysql_fetch_array($hsl)) {
						$arrprodukturunan[$kd_produk] += $qty_produk;
						$arrproduksatuanturunan[$kd_produk] = $satuan_produk;
					}
					$no_sew = no_sew();
					$totalqty = 0;
					$totalrp = 0;
					$seqno = -1;
					#Untuk Journal
					$ArrPrdNom = '';
					foreach ($arrproduk as $kd_produk => $qtyproduk) {
						$seqno++;
						$sql = "SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
						$hsltemp = mysql_query($sql, $db);
						list($harga) = mysql_fetch_array($hsltemp);
						$jumlah = $harga * $qtyproduk;
						$totalqty += $qtyproduk;
						$totalrp += $jumlah;
						/*
				$sql="INSERT INTO job_sewing_detail (no_sew,seqno,kd_produk,harga,qty,reject,pending) VALUES ('$no_sew','$seqno','$kd_produk','$harga','$qtyproduk','0','0')";
				// echo "<br>".$sql;
				mysql_query($sql,$db);	
				*/
						/*buat journal loading disini*/
						$ArrPrdNom[$kd_produk] = $jumlah;
					}
					/*
			$sql="INSERT INTO job_sewing (no_sew,no_load,tanggal,totalqty,totalrp) VALUES ('$no_sew','$no_load',NOW(),'$totalqty','$totalrp')";
			mysql_query($sql,$db);
			// echo "<br>".$sql;
			*/
					#Untuk Journal
					//jurnal_loading($no_load,$ArrPrdNom);
					#Untuk Journal
					//TURUNAN
					$totalqty = 0;
					$totalrp = 0;
					$seqno = -1;
					foreach ($arrprodukturunan as $kd_produk => $qtyproduk) {
						$seqno++;
						$sql = "SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
						$hsltemp = mysql_query($sql, $db);
						list($harga) = mysql_fetch_array($hsltemp);
						$jumlah = $harga * $qtyproduk;
						$totalqty += $qtyproduk;
						$totalrp += $jumlah;
						/*
				$sql="INSERT INTO job_sewing_turunan (no_sew,seqno,kd_produk,harga,qty,reject,pending) VALUES ('$no_sew','$seqno','$kd_produk','$harga','$qtyproduk','0','0')";
				// echo "<br>".$sql;
				mysql_query($sql,$db);	
				*/
					}
				?>
		<script language="javascript">
			alert("LOADING telah di approve.");
			window.location = "job_loading_detail.php?no_load=<?php echo $no_load; ?>";
		</script>
	<?php
				} else {
	?>
		<script language="javascript">
			alert("LOADING gagal di approve, Silakan hubungi Technical Support Anda!");
			window.location = "job_loading_detail.php?no_load=<?php echo $no_load; ?>";
		</script>
	<?php
				}
			} else {
	?>
	<script language="javascript">
		alert("LOADING sudah di approve.");
		window.location = "job_loading_detail.php?no_load=<?php echo $no_load; ?>";
	</script>
<?php
			}
?>
<?php include_once "footer.php" ?>