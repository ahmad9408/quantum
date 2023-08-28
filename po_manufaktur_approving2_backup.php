<?php $content_title = "APPROVING MANUFAKTUR II";
include_once "header.php" ?>
<?php include "pdo_produksi/Db.class.php"; ?>

<?php
//include_once "po_manufaktur_approving_init.php";
//echo nomor_manufaktur();exit;
$no_manufaktur = sanitasi($_GET["no_manufaktur"]);
$sql = "SELECT approve2 FROM po_manufaktur WHERE no_manufaktur='$no_manufaktur'";
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

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("UPDATE po_manufaktur SET approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_manufaktur='$no_manufaktur'");
					mysql_query($sql, $db);

					$executeTransaction = $sql->executeTransaction();
				} catch (PDOException $e) {
					//atau (Exception $e) 
					$rollBack = $sql->rollBack();
					echo "error msg: " . $e->getMessage();
					throw $e;
				}

				if (mysql_affected_rows($db) > 0) {
					$sql = "SELECT kd_produk,qty,hargajual FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'"; //echo "<br>$sql";
					$hsl = mysql_query($sql, $db);
					$adareq = false;
					//$adasj=false;
					$seqno = 0;
					//$seqnosj=0;
					$totalrp = 0;
					//$totalrpsj=0;
					$totalqty = 0;
					//$totalqtysj=0;
					#Untuk Journal
					$ArrPrdNom = '';

					while (list($kd_produk, $qtyproduk, $hargajual) = mysql_fetch_array($hsl)) {
						//cari produk_Detail
						$sql = "SELECT kode_barang,qty,satuan FROM produk_detail WHERE kode_produk='$kd_produk'";
						$hslbarang = mysql_query($sql, $db);
						while (list($kd_barang, $qtybarang, $satuanbarang) = mysql_fetch_array($hslbarang)) {
							$jml_kebutuhan = $qtybarang * $qtyproduk;
							$sql = "SELECT stok FROM barang_stok WHERE kode_barang='$kd_barang'";
							$hsltemp = mysql_query($sql, $db);
							list($stokbarang) = mysql_fetch_array($hsltemp);
							if ($stokbarang < $jml_kebutuhan) {
								$qty_kurang = $jml_kebutuhan - $stokbarang;
								$sql = "SELECT no_manufaktur FROM barang_kurang WHERE no_manufaktur='$no_manufaktur' AND kd_barang='$kd_barang'";
								$hsltemp = mysql_query($sql, $db);
								if (mysql_affected_rows($db) > 0) {

									$sql = new Db();

									try {
										$beginTransaction = $sql->beginTransaction();

										$sql->query("UPDATE barang_kurang SET qty=qty+$qty_kurang WHERE no_manufaktur='$no_manufaktur' AND kd_barang='$kd_barang'");
										mysql_query($sql, $db);

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

										$sql->query("INSERT INTO barang_kurang (no_manufaktur,kd_barang,qty,updateby,updatedate) VALUES ('$no_manufaktur','$kd_barang','$qty_kurang','$approveby',NOW())");
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
						}
						/*buat journal Manufaktur disini*/
						#Untuk Journal
						$jumlah = $qtyproduk * $hargajual;
						$ArrPrdNom[$kd_produk] = $jumlah;
						#Untuk Journal
					}
					//jurnal_po_markas_manufaktur($no_manufaktur,$ArrPrdNom);
				?>
		<script language="javascript">
			alert("Manufaktur telah di approve.");
			window.location = "permintaan_manufaktur_detail.php?no_manufaktur=<?php echo $no_manufaktur; ?>";
		</script>
	<?php
				} else {
	?>
		<script language="javascript">
			alert("Manufaktur gagal di approve, Silakan hubungi Technical Support Anda!");
			window.location = "permintaan_manufaktur_detail.php?no_manufaktur=<?php echo $no_manufaktur; ?>";
		</script>
	<?php
				}
			} else {
	?>
	<script language="javascript">
		alert("Manufaktur sudah di approve.");
		window.location = "permintaan_manufaktur_detail.php?no_manufaktur=<?php echo $no_manufaktur; ?>";
	</script>
<?php
			}
?>
<?php include_once "footer.php" ?>