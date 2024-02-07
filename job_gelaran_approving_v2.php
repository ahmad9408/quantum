<?php $content_title = "APPROVING PPIC";
include_once "header.php" ?>
<?php include "pdo_produksi/Db.class.php"; ?>

<?php
$no_po = sanitasi($_GET["no_po"]);
$no_co = sanitasi($_GET["no_co"]);
$sql = "SELECT approve FROM job_gelaran WHERE no_po='$no_po' AND no_co='$no_co'";
$hsl = mysql_query($sql, $db);
if (mysql_affected_rows($db) < 1) { ?> <script language="javascript">
		history.back();
	</script> <?php exit();
			}
			list($approve) = mysql_fetch_array($hsl);
			if (!$approve) {
				//cek stok
				$arrkebutuhanrm = array();
				$sql = "SELECT kd_barang,kainmasuk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co'";
				$hslrm = mysql_query($sql, $db);
				while (list($kdrm, $qty) = mysql_fetch_array($hslrm)) {
					$arrkebutuhanrm[$kdrm] += $qty;
				}
				$kebutuhanterpenuhi = true;
				foreach ($arrkebutuhanrm as $kdrm => $qtykebutuhan) {
					//cari stok rm
					$tomorrow = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + 1, date("Y")));
					//cari qty masuk
					$sql = "SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$kdrm' AND tanggal < '$tomorrow%' AND (mode='1' OR mode='2' OR mode='4')";
					//echo "<br>$sql";
					$hsltemp = mysql_query($sql, $db);
					list($stokin) = mysql_fetch_array($hsltemp);
					$stokin += 0;

					//cari qty keluar
					$sql = "SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$kdrm' AND tanggal < '$tomorrow%' AND mode='3'";
					//echo "<br>$sql";
					$hsltemp = mysql_query($sql, $db);
					list($stokout) = mysql_fetch_array($hsltemp);
					$stokout += 0;
					$stokrm = $stokin - $stokout;
					if ($stokrm < $qtykebutuhan) {
						$kebutuhanterpenuhi = false;
						break;
					}
				}
				/* Tutup Goberan
		if($kebutuhanterpenuhi){ */
				$approveby = $_SESSION["username"];

				// $sql_trans="SET autocommit = 0;"; //tambhan 19072022 13:37
				// $query_trans=mysql_query($sql_trans);

				// $sql_trans="START TRANSACTION;";

				// $query_trans=mysql_query($sql_trans);

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("UPDATE job_gelaran SET approve='1', approveby='$approveby',approvedate=NOW() WHERE no_po='$no_po' AND no_co='$no_co';-- " . basename(__FILE__));
					mysql_query($sql, $db);

					$executeTransaction = $sql->executeTransaction();
				} catch (PDOException $e) {
					//atau (Exception $e) 
					$rollBack = $sql->rollBack();
					echo "error msg: " . $e->getMessage();
					throw $e;
				}

				// $sql_trans="COMMIT;";	
				// $query_trans=mysql_query($sql_trans);

				if (mysql_affected_rows($db) > 0) {
					//insert barang_stok & barang_stok_control
					$sql = "SELECT qty_produk,kd_barang,supplier,satuan,qty,kainmasuk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co'";
					$hslgelaran = mysql_query($sql, $db);
					while (list($qty_produk, $kd_barang, $kd_supplier, $satuan, $qty_barang, $kainkeluar) = mysql_fetch_array($hslgelaran)) {
						if ($qty_barang > 0) {
							$sql = "SELECT seqno FROM barang_stok WHERE kode_supplier='$kd_supplier' AND kode_barang='$kd_barang'";
							mysql_query($sql, $db);

							if (mysql_affected_rows($db) > 0) {
								$sql = "UPDATE barang_stok SET stok=stok-$kainkeluar,updateby='$approveby',updatedate=NOW() WHERE kode_supplier='$kd_supplier' AND kode_barang='$kd_barang'";
							} else {
								$sql = "INSERT INTO barang_stok (kode_supplier,kode_barang,stok,updateby,updatedate) VALUES ('$kd_supplier','$kd_barang','$kainkeluar','$approveby',NOW())";
							}
							mysql_query($sql, $db);


							$sql = new Db();

							try {
								$beginTransaction = $sql->beginTransaction();

								$sql->query("INSERT INTO barang_stok_control (mode,tanggal,no_po,dari,tujuan,kd_barang,qty,satuan,updateby,updatedate) VALUES
							('3',NOW(),'$no_po','RBN','PPIC','$kd_barang','$kainkeluar','$satuan','$approveby',NOW())");
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
				?>
		<script language="javascript">
			alert("PPIC telah di approve.");
			window.location = "job_gelaran_list_v3.php?no_po=<?php echo $no_po; ?>";
		</script>
	<?php
				} else {
	?>
		<script language="javascript">
			alert("PPIC gagal di approve, Silakan hubungi Technical Support Anda!");
			window.location = "job_gelaran_approving_v2.php?no_po=<?php echo $no_po; ?>";
		</script>
	<?php
				}
				// Di Tutup Goberan 
				/*
		}else{
			?>
				<script language="javascript">
					alert("Stok RM tidak mencukupi. Kemungkinan ada user lain yang menggunakan RM yang sama!");
					window.location="job_gelaran_list.php?no_po=<?php echo $no_po; ?>";
				</script>
			<?php
		}*/
			} else {
	?>
	<script language="javascript">
		alert("PPIC sudah di approve.");
		window.location = "job_gelaran_list_v3.php?no_po=<?php echo $no_po; ?>";
	</script>
<?php
			}
?>
<?php include_once "footer.php" ?>