<?php
$content_title = "TAMBAH REAL CUTTING";
$data_global['is_overridesecurity_mode'] = '1';
$data_global['overridesecurity_mode'] = '0';

include_once "header.php";
include_once "clsaddrow.php";
include_once "job_cutting_init.php";
include "pdo_produksi/Db.class.php";


$isDebug = 0;
if ($username == 'budi-it') {
	$isDebug = 1;
}
$no_co = sanitasi($_GET['no_co']);
$sql = "SELECT no_po,pabrik FROM job_gelaran WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql);
list($no_po, $id_pabrik) = mysql_fetch_array($hsltemp);
$sql = "SELECT closeco FROM po_manufaktur WHERE no_manufaktur='$no_po'";
$hsl = mysql_query($sql);
if (mysql_affected_rows($db) < 1) {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No Po Tidak Ada");
		window.location = "job_cutting_list.php";
	</script>
<?php
	exit;
}
list($closeco) = mysql_fetch_array($hsl);
if ($closeco == "1") {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No Po Sudah Di Close");
		window.location = "job_gelaran_list.php";
	</script>
<?php
	exit;
}
$sql = "SELECT closeco FROM po_markas_pusat WHERE no_po='$no_po'";
$hsl = mysql_query($sql);
list($closeco) = mysql_fetch_array($hsl);
if ($closeco == "1") {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No Po Sudah Di Tutup");
		window.location = "job_cutting_list.php";
	</script>
<?php
}
$sql = "SELECT no_co FROM job_gelaran WHERE no_co='$no_co' AND approve='1'";
$hsl = mysql_query($sql);
if (mysql_affected_rows($db) < 1) {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No CO ini belum dilakukan approval PPIC!");
		window.location = "job_cutting_list.php";
	</script>
<?php
}
$sql = "SELECT no_co FROM job_cutting WHERE no_co='$no_co' AND realcuting='1'";
$hsl = mysql_query($sql);
if (mysql_affected_rows($db) > 0) {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No CO ini Sudah dilakukan Real Cutting!");
		window.location = "job_cutting_list.php";
	</script>
	<?php
}
//$no_co=no_co();
$sql = "SELECT no_jo FROM job_cutting WHERE no_po='$no_po' AND no_jo!=''"; //echo $sql;
$hsl = mysql_query($sql);
if (mysql_affected_rows($db) > 0) {
	list($no_jo) = mysql_fetch_array($hsl);
} else {
	$no_jo = no_jo();
}

$no_load = no_load();

$sql = "SELECT nama FROM pabrik WHERE id='$id_pabrik'";
$hsltemp = mysql_query($sql);
list($nama_pabrik) = mysql_fetch_array($hsltemp);
$pabrik = "$nama_pabrik [$id_pabrik]";

if (sanitasi($_POST['simpan'])) {

	$tanggal = sanitasi($_POST['thncut']) . "-" . sanitasi($_POST['blncut']) . "-" . sanitasi($_POST['tglcut']) . " " . sanitasi($_POST['timecut']);
	$totqtycutting = array();
	$totqtyikat = array();
	foreach ($_POST["qtycutting"] as $barcode => $qtycutting) {
		$totqtycutting[$barcode] += $qtycutting;
		foreach ($_POST["qty"][$barcode] as $seqno2 => $qtyikat) {
			$totqtyikat[$barcode] += $qtyikat;
		}
	}
	$jml_cutting_valid = true;

	$jml_ikatan_valid = true;
	foreach ($totqtyikat as $barcode => $qtyikat) {
		if ($_POST["qtycutting"][$barcode] != $qtyikat) {
			$jml_ikatan_valid = false;
			break;
		}
	}

	if (!$jml_cutting_valid) {
	?>
		<script type="text/javascript">
			alert("'Qty Cutting' harus lebih kecil atau sama dengan dari 'Qty yg belum'! Silakan Perbaiki");
			// window.location="job_cutting_list.php";
		</script>
	<?php
	}
	if (!$jml_ikatan_valid) {
	?>
		<script type="text/javascript">
			alert("'Total' harus sama dengan dari 'Qty Cutting'! Silakan Perbaiki");
			// window.location="job_cutting_list.php";
		</script>
	<?php
	}
	$adarmpakai = true;
	foreach ($_POST["rmpakai"] as $kode_rm => $qty_rm_terpakai) {
		if ($qty_rm_terpakai <= 0) {
			$adarmpakai = false;
		}
	}
	if ($isDebug == 1) {
		echo "jml_cutting_valid:$jml_cutting_valid ;; jml_ikatan_valid:$jml_ikatan_valid ;; adarmpakai:$adarmpakai </br>";
	}

	if ($jml_cutting_valid && $jml_ikatan_valid && $adarmpakai) {
		$seqno = -1;
		$totalqty = 0;
		$totalrp = 0;

		if ($isDebug == 1) {
			echo '<h3> _POST["qtycutting"] </h3>';
			print_r($_POST["qtycutting"]);
		}
		foreach ($_POST["qtycutting"] as $barcode => $qtycutting) {
			$seqno++;
			$totalqty += $qtycutting;
			$totalrp += $qtycutting * $harga;

			$sql = new Db();

			try {
				$beginTransaction = $sql->beginTransaction();

				$sql->query("INSERT INTO job_cutting_detail (no_co,seqno,kd_produk,qty) VALUES ('$no_co','$seqno','$barcode','$qtycutting')");
				$query = mysql_query($sql);

				$executeTransaction = $sql->executeTransaction();
			} catch (PDOException $e) {
				//atau (Exception $e) 
				$rollBack = $sql->rollBack();
				echo "error msg: " . $e->getMessage();
				throw $e;
			}

			if (!$query) {

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("update job_cutting_detail set qty=qty+$qtycutting where no_co='$no_co' and seqno='$seqno' and kd_produk='$barcode'");
					$query = mysql_query($sql);

					$executeTransaction = $sql->executeTransaction();
				} catch (PDOException $e) {
					//atau (Exception $e) 
					$rollBack = $sql->rollBack();
					echo "error msg: " . $e->getMessage();
					throw $e;
				}
			}

			$sql1 = new Db();

			try {
				$beginTransaction = $sql1->beginTransaction();

				$sql1->query("INSERT INTO job_loading_detail (no_load,seqno,kd_produk,qty_produk) VALUES ('$no_load','$seqno2','$barcode','$qtycutting')");
				$hsltemp1 = mysql_query($sql1);

				$executeTransaction = $sql1->executeTransaction();
			} catch (PDOException $e) {
				//atau (Exception $e) 
				$rollBack = $sql1->rollBack();
				echo "error msg: " . $e->getMessage();
				throw $e;
			}

			if ($isDebug == 1) {
				echo $sql1 . '</br>';
				if (!$hsltemp1) {
					echo mysql_error() . '</br>';
				}
			}



			foreach ($_POST["qty"][$barcode] as $seqno2 => $qtyikat) {

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("INSERT INTO job_cutting_ikatan (no_co,seqno,kd_produk,qty) VALUES ('$no_co','$seqno2','$barcode','$qtyikat')");
					$query = mysql_query($sql);

					$executeTransaction = $sql->executeTransaction();
				} catch (PDOException $e) {
					//atau (Exception $e) 
					$rollBack = $sql->rollBack();
					echo "error msg: " . $e->getMessage();
					throw $e;
				}


				if (!$query) {

					$sql = new Db();

					try {
						$beginTransaction = $sql->beginTransaction();

						$sql->query("update job_cutting_ikatan set qty=qty+$qtyikat where no_co='$no_co' and seqno='$seqno2' and kd_produk='$barcode'");
						$query = mysql_query($sql);

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

		/* Tambah Loading */

		$sql2 = new Db();

		try {
			$beginTransaction = $sql2->beginTransaction();

			$sql2->query("INSERT INTO job_loading (no_load, no_co, pabrik_dari,
			tanggal,totalqtyproduk, totalrp, approve, approveby, approvedate) 
			VALUES ('$no_load','$no_co', '$id_pabrik', NOW(), '$totalqty', '$totalrp', '1','$username',NOW())");
			$hsltemp2 = mysql_query($sql2);

			$executeTransaction = $sql2->executeTransaction();
		} catch (PDOException $e) {
			//atau (Exception $e) 
			$rollBack = $sql2->rollBack();
			echo "error msg: " . $e->getMessage();
			throw $e;
		}

		//RM TERPAKAI
		$seqno = -1;
		foreach ($_POST["rmpakai"] as $kode_rm => $qty_rm_terpakai) {
			$seqno++;
			$rm_cons = $_POST["rmpakai_cons"][$kode_rm];
			$rm_satuan = $_POST["rmpakai_satuan"][$kode_rm];
			$rm_keluar = $_POST["rmpakai_rm_keluar"][$kode_rm];
			$sql = "INSERT INTO job_cutting_rm_terpakai (no_co,seqno,kode_rm,qty,satuan,rm_keluar,rm_terpakai) VALUES ";
			$sql .= "('$no_co','$seqno','$kode_rm','$rm_cons','$rm_satuan','$rm_keluar','$qty_rm_terpakai')";
			mysql_query($sql);
		}

		$sql = "UPDATE job_cutting SET ";
		$sql .= "no_jo='$no_jo', ";
		$sql .= "tanggal=NOW(), ";
		$sql .= "kd_supplier='', ";
		$sql .= "kd_produk='', ";
		$sql .= "totalqty=totalqty+$totalqty, ";
		$sql .= "totalrp='$totalrp', ";
		$sql .= "realcutting='1', ";
		$sql .= "approve='1', ";
		$sql .= "approveby='$username', ";
		$sql .= "approvedate=NOW(), ";
		$sql .= "approve2='1', ";
		$sql .= "approveby2='$username', ";
		$sql .= "approvedate2=NOW() ";
		$sql .= "WHERE no_co='$no_co'";
		mysql_query($sql);

		foreach ($_POST["kdproduk"] as $seqno => $barcode) {
			if ($barcode) {
				$qty = sanitasi($_POST["qty"][$seqno]);

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("INSERT INTO job_cutting_turunan (no_co,seqno,kd_produk,qty) VALUES ('$no_co','$seqno','$barcode','$qty')");
					mysql_query($sql);

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
		<script type="text/javascript">
			<?php
			if ($isDebug == 1) {
			} else {
				echo 'alert("Cutting Tersimpan [.]");';
				echo 'window.location="job_cutting_list.php";';
			}

			?>
		</script>
<?php
	}
}
foreach ($_POST["addikatan"] as $add_kd_produk => $value) {
	$_POST["ikatan"][$add_kd_produk]++;
}
foreach ($_POST["incikatan"] as $add_kd_produk => $value) {
	$_POST["ikatan"][$add_kd_produk]--;
}

?>
<script language="JavaScript">
	var detailsWindow;

	function showProduk(textid, textnama, kodeproduk, idukuran, idsatuan) {
		detailsWindow = window.open("window_produk.php?textid=" + textid + "&textnama=" + textnama + "&kodeproduk=" + kodeproduk + "&idukuran=" + idukuran + "&idsatuan=" + idsatuan, "window_produk", "width=800,height=600,scrollbars=yes");
		detailsWindow.focus();
	}
</script>
<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?no_co=<?php echo $no_co; ?>">
	<fieldset>
		<table>
			<tr>
				<td><b>PO</b></td>
				<td><b>:</b></td>
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
			<tr>
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
						for ($i = 1; $i < 32; $i++) {
							$val = substr("00", 0, 2 - strlen($i)) . $i;
							$selected = "";
							if ($val == date("d")) {
								$selected = "selected";
							}
						?>
							<option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
						<?php
						}
						?>
					</select> /
					<select name="blncut">
						<?php
						for ($i = 1; $i < 13; $i++) {
							$val = substr("00", 0, 2 - strlen($i)) . $i;
							$selected = "";
							if ($val == date("m")) {
								$selected = "selected";
							}
						?>
							<option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
						<?php
						}
						?>
					</select> /
					<select name="thncut">
						<?php
						for ($i = date("Y") + 1; $i > date("Y") - 5; $i--) {
							$val = $i;
							$selected = "";
							if ($val == date("Y")) {
								$selected = "selected";
							}
						?>
							<option value="<?php echo $val; ?>" <?php echo $selected; ?>><?php echo $val; ?></option>
						<?php
						}
						?>
					</select>
					<input type="text" name="timecut" value="<?php echo date("H:i:s"); ?>" size="8">
				</td>
			</tr>
		</table>
		<table border="1">
			<tr>
				<td><b>No</b></td>
				<td><b>Kode Produk</b></td>
				<td><b>Nama Produk</b></td>
				<td><b>Warna</b></td>
				<td><b>Size</b></td>
				<!--td><b>Qty</b></td-->
				<!--td><b>Qty yg belum</b></td-->
				<td><b>Qty Cutting</b></td>
				<td><b>Satuan</b></td>
				<!--td><b>Jml Gelaran</b></td-->
				<!-- td><b>Polybag</b></td -->
				<td><b>Detil Polybag</b></td>
			</tr>
			<?php
			//$sql="SELECT kd_produk,qty FROM po_manufaktur_detail WHERE no_manufaktur='$no_po' ORDER BY seqno";//echo $sql;
			$sql = "SELECT kd_produk,qty_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' GROUP BY kd_produk ORDER BY seqno"; //echo $sql;
			$hsl = mysql_query($sql);
			$no = 0;
			$_adacutting = false;
			$arrrm = array();
			while (list($kd_produk, $qty) = mysql_fetch_array($hsl)) {
				if (sanitasi($_POST["qtycutting"][$kd_produk]) > 0) {
					$qty = sanitasi($_POST["qtycutting"][$kd_produk]);
				}
				$sql = "SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
				$hsltemp = mysql_query($sql);
				list($nama, $kode_warna, $kode_size, $satuan) = mysql_fetch_array($hsltemp);
				$sql = "SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
				$hsltemp = mysql_query($sql);
				list($warna) = mysql_fetch_array($hsltemp);
				$sql = "SELECT size FROM mst_size WHERE kode='$kode_size'";
				$hsltemp = mysql_query($sql);
				list($size) = mysql_fetch_array($hsltemp);
				$sql = "SELECT nama FROM satuan WHERE id='$satuan'";
				$hsltemp = mysql_query($sql);
				list($satuan) = mysql_fetch_array($hsltemp);
				if (!sanitasi($_POST["ikatan"][$kd_produk])) {
					$_POST["ikatan"][$kd_produk] = 1;
				}
				//$sql="SELECT sum(qty) FROM job_cutting_detail WHERE kd_produk='$kd_produk' AND no_co IN (SELECT no_co FROM job_cutting WHERE no_jo='$no_jo')";//echo $sql."<br>";
				$sql = "SELECT sum(qty) FROM job_cutting_detail WHERE kd_produk='$kd_produk' AND no_co IN (SELECT no_co FROM job_cutting WHERE no_po='$no_po')"; //echo $sql."<br>";
				$hsltemp = mysql_query($sql);
				list($qtycuting) = mysql_fetch_array($hsltemp);
				$qtybelum = $qty - $qtycuting;
				$qtybelum = 1;
				if ($qtybelum > 0) {
					$_adacutting = true;
					$no++;
					$sql = "SELECT sum(jml_gelaran) FROM job_gelaran_detail WHERE no_po='$no_po' AND kd_produk='$kd_produk'";
					$hsltemp = mysql_query($sql);
					list($jml_gelaran) = mysql_fetch_array($hsltemp);
					$sql = "SELECT sum(qty) FROM job_cutting_ikatan WHERE no_co IN (SELECT no_co FROM job_cutting WHERE no_po='$no_po') AND kd_produk='$kd_produk'";
					$hsltemp = mysql_query($sql);
					list($jml_terikat) = mysql_fetch_array($hsltemp);
					$maxgelar = $jml_gelaran - $jml_terikat;
					$qtycutting = $qtybelum;
					if (sanitasi($_POST["qtycutting"][$kd_produk])) {
						$qtycutting = sanitasi($_POST["qtycutting"][$kd_produk]);
					}
					$sql = "SELECT qty_produk,kd_barang,satuan,qty,kainmasuk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' AND kd_produk='$kd_produk'";
					$hslrm = mysql_query($sql);
					while (list($qty_produk_rm, $kd_barang_rm, $satuan_rm, $qty_rm, $kainmasuk_rm) = mysql_fetch_array($hslrm)) {
						$arrrm[$kd_barang_rm]["satuan_rm"] = $satuan_rm;
						$arrrm[$kd_barang_rm]["totalqty"] += $qty_rm * $qty_produk_rm;
						$arrrm[$kd_barang_rm]["kainkeluar"] += $kainmasuk_rm;
					}
			?>
					<tr>
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
						<td valign="top">
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
								$totalikat = 0;
								for ($ikat = 0; $ikat < sanitasi($_POST["ikatan"][$kd_produk]); $ikat++) {
									$totalikat += sanitasi($_POST["qty"][$kd_produk][$ikat]);
								?>
									<tr>
										<td align="right"><?php echo $ikat + 1; ?></td>
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
						</td>
					</tr>
			<?php
				}
			}
			?>
		</table>
		<table border="1">
			<tr>
				<td colspan="6" align="center"><b>PRODUK TAMBAHAN</b></td>
			</tr>
			<tr>
				<td nowrap>
					<b>No</b>
					<a href="#" onClick="addRow(' ',0,0,'tbltrx','bodytrx');" style="text-decoration:none">
						<img src='images/add.png' alt='add' align='middle' border='0' height='16' hspace='0' width='16'></a>
					<a href="#" onClick="addRow(' ',1,0,'tbltrx','bodytrx');" style="text-decoration:none">
						<img src='images/remove.png' alt='inc' align='middle' border='0' height='16' hspace='0' width='16'></a>
				</td>
				<td><b>Kode Produk</b></td>
				<td><b>Nama Produk</b></td>
				<td><b>Ukuran</b></td>
				<td><b>Qty</b></td>
				<td><b>Satuan</b></td>
			</tr>
			<tbody id="tbltrx0">
				<tr id="bodytrx0">
					<td nowrap id="nomor0" align="right">1</td>
					<td nowrap>
						<input type="text" name="kdproduk[0]" id="idkdproduk[0]" onclick="showProduk(this.id,'id_produk[0]',this.value,'id_ukuran[0]','id_satuan[0]');">
					</td>
					<td><input id="id_produk[0]" type="text" readonly></td>
					<td id="id_ukuran[0]">&nbsp;</td>
					<td><input type="text" size="3" name="qty[0]"></td>
					<td id="id_satuan[0]">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<table border="1">
			<tr>
				<td colspan="8" align="center"><b>RM Terpakai</b></td>
			</tr>
			<tr>
				<td nowrap><b>No</b></td>
				<td><b>Kode RM</b></td>
				<td><b>Nama RM</b></td>
				<td><b>Warna RM</b></td>
				<td><b>Total Qty</b></td>
				<td><b>Satuan</b></td>
				<td><b>Total RM Keluar</b></td>
				<td><b>Total RM Terpakai</b></td>
			</tr>
			<?php
			$no = 0;
			foreach ($arrrm as $kode_rm => $arrrmdetail) {
				$no++;
				$sql = "SELECT nama,warna FROM barangdetail WHERE id='$kode_rm'";
				$hsltemp = mysql_query($sql);
				list($nama_rm, $warna_id) = mysql_fetch_array($hsltemp);
				$sql = "SELECT warna FROM mst_warna WHERE kode='$warna_id'";
				$hsltemp = mysql_query($sql);
				list($warna_rm) = mysql_fetch_array($hsltemp);
				$warna_rm = "$warna_rm [$warna_id]";
				$satuan_id = $arrrmdetail["satuan_rm"];
				$sql = "SELECT nama FROM satuan WHERE id='$satuan_id'";
				$hsltemp = mysql_query($sql);
				list($satuan) = mysql_fetch_array($hsltemp);
				$total_qty = $arrrmdetail["totalqty"];
				$total_kain_keluar = $arrrmdetail["kainkeluar"];
			?>
				<input type="hidden" name="rmpakai_cons[<?php echo $kode_rm; ?>]" value="<?php echo $total_qty; ?>">
				<input type="hidden" name="rmpakai_satuan[<?php echo $kode_rm; ?>]" value="<?php echo $satuan_id; ?>">
				<input type="hidden" name="rmpakai_rm_keluar[<?php echo $kode_rm; ?>]" value="<?php echo $total_kain_keluar; ?>">
				<tr>
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
		if (!$_adacutting) {
		?>
			<script language="javascript">
				alert('Job Order ini sudah di cutting semua!');
				//window.location='job_cutting_list.php';
			</script>
		<?php
		}
		?>
		<?php include_once "footer.php"; ?>