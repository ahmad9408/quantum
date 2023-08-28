<?php $content_title = "PERMINTAAN MANUFAKTUR";
include_once "header.php" ?>
<?php include_once "clsaddrow.php"; ?>
<style>
	.mylink {
		cursor: pointer;
		color: #0099FF;
	}
</style>
<?php
/*
	Last Edit 13 nov 2012 multi with plann
	
	*/
$no_manufaktur = sanitasi($_GET["no_manufaktur"]);
if (isset($_REQUEST['j'])) {
	$plan = '_planning';
} else {
	$plan = '';
}
$sql = "SELECT * FROM po_manufaktur" . $plan . " WHERE no_manufaktur='$no_manufaktur'";
$hsl = mysql_query($sql, $db);
$rs = mysql_fetch_array($hsl);
$no_po = $rs["no_po"];
$tanggal = $rs["tanggal"];
$totalqty = $rs["totalqty"];
$totalrp = $rs["totalrp"];
$closeco = $rs["closeco"];
$approved = $rs["approve"];
$approved2 = $rs["approve2"];

?>
<script language="JavaScript">
	var detailsWindow;

	function showCalendar(textid) {
		detailsWindow = window.open("calendar.php?textid=" + textid + "", "calendar", "width=260,height=250,top=300,scrollbars=yes");
		detailsWindow.focus();
	}

	function showVendor(textid, txtname, txtaddr, mode) {
		detailsWindow = window.open("window_vendor.php?textid=" + textid + "&txtname=" + txtname + "&txtaddr=" + txtaddr + "&mode=" + mode + "", "vendor", "width=400,height=600,top=0,scrollbars=yes");
		detailsWindow.focus();
	}

	function showProduk(textid, textnama, kodeproduk, idukuran, idsatuan) {
		detailsWindow = window.open("window_produk.php?textid=" + textid + "&textnama=" + textnama + "&kodeproduk=" + kodeproduk + "&idukuran=" + idukuran + "&idsatuan=" + idsatuan, "window_produk", "width=800,height=600,scrollbars=yes");
		detailsWindow.focus();
	}
</script>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
	<table>
		<tr>
			<td valign="top">
				<table>
					<tr>
						<td><b><span ondblclick="tambah()">Nomor Manufaktur</span></b></td>
						<td><b>:</b></td>
						<td id="no_manufaktur"><?php echo $no_manufaktur; ?></td>
					</tr>
					<tr>
						<td><b>Tanggal</b></td>
						<td><b>:</b></td>
						<td><?php echo $tanggal; ?></td>
					</tr>
					<tr class="hilang">
						<td colspan="3">
							<table border="0">
								<tr>
									<td width="100"><strong>Barcode</strong></td>
									<td width="2"><strong>:</strong></td>
									<td><input type="text" id="barcode" name="barcode" size="25" /></td>
								</tr>

								<tr>
									<td width="100"><strong>Qty</strong></td>
									<td width="2"><strong>:</strong></td>
									<td><input type="text" id="qty" name="qty" /></td>
								</tr>

								<tr>
									<td width="100"></td>
									<td width="2"></td>
									<td id="tdbutton"></td>
								</tr>
							</table>
						</td>

					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border="1">
		<tr>
			<td><b>No</b></td>
			<td><b>Kode Produk</b></td>
			<td><b>Nama Produk</b></td>
			<td><strong>Warna</strong></td>
			<td><b>Ukuran</b></td>
			<td><b>Qty</b></td>
			<td><b>Satuan</b></td>
			<td><b>Harga</b></td>
			<td><b>Jumlah</b></td>
		</tr>
		<?php
		//tambahan 2022-07-19
		$sql = "SET autocommit = 0;";
		$query = mysql_query($sql);

		$sql = "START TRANSACTION;";
		$query = mysql_query($sql);

		$sql = "SELECT * FROM po_manufaktur" . $plan . "_detail  WHERE no_manufaktur='$no_manufaktur' ORDER BY seqno";

		$hsl = mysql_query($sql, $db) or die($sql);

		//tambahan 2022-07-19
		$c = "COMMIT;";
		$qc = mysql_query($c);


		$no = 0;
		$totalharga = 0;
		$totalqty = 0;
		$total = 0;
		while ($rs = mysql_fetch_array($hsl)) {
			$no++;
			$barcode = $rs["kd_produk"];
			$sql = "SELECT nama,kode_size,satuan,hargajual,kode_warna FROM produk WHERE kode='$barcode'";
			$hsltemp = mysql_query($sql, $db);
			list($namaproduk, $kodesize, $satuan, $hargajual, $kode_warna) = mysql_fetch_array($hsltemp);
			/*
					$sql="SELECT nama FROM satuan WHERE id='$satuan'";
					$hsltemp=mysql_query($sql,$db);
					list($satuan)=mysql_fetch_array($hsltemp);
					*/
			$satuan = 'PCS';
			$sql = "SELECT size FROM mst_size WHERE kode='$kodesize'";
			$hsltemp = mysql_query($sql, $db);
			list($size) = mysql_fetch_array($hsltemp);
			$sql = "SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
			$hsltemp = mysql_query($sql, $db);
			list($warna) = mysql_fetch_array($hsltemp);

			$hargajual = $rs["hargajual"];
			$qty = $rs["qty"];
			$jumlah = $rs["jumlah"];
			$jumlah = $qty * $hargajual;
			$totalharga += $hargajual;
			$totalqty += $qty;
			$total += $jumlah;
			$sql = "SELECT kd_barang,qty FROM barang_kurang WHERE no_manufaktur='$no_manufaktur' AND qty>0";
			mysql_query($sql, $db);
			if (mysql_affected_rows($db) > 0) {
				$rmkurang = true;
			}
		?>
			<tr>
				<td align="right"><?php echo $no; ?></td>
				<td><span id="barcode<?php echo $no ?>" ondblclick="perintah('<?php echo $no ?>')"><?php echo $barcode; ?></span></td>
				<td><?php echo $namaproduk; ?></td>
				<td><?php echo "[$kode_warna] $warna"; ?></td>
				<td><?php echo $size; ?></td>
				<td align="right" id="qty<?php echo $no ?>" ondblclick="ubah_qty('<?php echo $no ?>')"><?php echo number_format($qty); ?></td>
				<td align="right"><?php echo $satuan; ?></td>
				<td align="right"><?php echo number_format($hargajual); ?></td>
				<td align="right"><?php echo number_format($jumlah); ?></td>
			</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="5"><b>TOTAL</b></td>
			<td align="right"><b><?php echo number_format($totalqty); ?></b></td>
			<td>&nbsp;</td>
			<td align="right"><b><?php echo number_format($totalharga); ?></b></td>
			<td align="right"><b><?php echo number_format($total); ?></b></td>
		</tr>
	</table>
	<script>
		$(".hilang").hide();

		function perintah(no) {
			var barcode = $("#barcode" + no).text();
			$("#barcode" + no).html(barcode + "&nbsp;<span id='bar" + no + "' onclick=hapus('" + no + "','" + barcode + "')  class='mylink'>Hapus</span>");
		}

		function hapus(no, barcode) {
			var no_manufaktur = $("#no_manufaktur").text().trim();
			var data = "barcode=" + barcode + "&no_manufaktur=" + no_manufaktur + "&proses=hapus";
			$.post("manufaktur_query.php", data, function(response) {
				document.location.reload();
			});
		}

		function ubah_qty(no) {
			var qty = $("#qty" + no).text();
			$("#qty" + no).html("<input type='text' id='qty1" + no + "' name='qty1" + no + "' size='5' value='" + qty + "'><span class='mylink' onclick=ubah_qty_proses('" + no + "')>Ubah</span>");
		}

		function ubah_qty_proses(no) {
			var no_manufaktur = $("#no_manufaktur").text().trim();
			var qty1 = $("#qty1" + no).val().trim();
			var barcode = $("#barcode" + no).text().trim();
			var data = "barcode=" + barcode + "&no_manufaktur=" + no_manufaktur + "&qty=" + qty1 + "&proses=ubah_qty";
			$.post("manufaktur_query.php", data, function(response) {
				document.location.reload();
			});
		}

		function hilang1() {
			$(".hilang").hide();
		}

		function tambah() {
			$(".hilang").show();
			$("#tdbutton").html("<input type='button' id='button' name='button' value='Simpan' onclick='simpan()'><input type='button' id='hilang' name='hilang' onclick='hilang1()' value='Hide'>");
		}

		function simpan() {
			var barcode = $("#barcode").val().trim();
			var no_manufaktur = $("#no_manufaktur").text().trim();
			var qty = $("#qty").val().trim();
			var data = "barcode=" + barcode + "&no_manufaktur=" + no_manufaktur + "&qty=" + qty + "&proses=cek_keberadaan_barcode";
			$.post("manufaktur_query.php", data, function(response) {

				if (response.trim() == 'ada') {
					alert('Barcode telah di pakai');
					exit();
				} else if (response.trim() == 'no') {
					alert('barcode tidak di temukan di master');
					exit();
				} else {
					var data = "barcode=" + barcode + "&no_manufaktur=" + no_manufaktur + "&qty=" + qty + "&proses=simpan";
					$.post("manufaktur_query.php", data, function(response) {

						if (response.trim() == 'berhasil') {
							alert('input berhasil');
							document.location.reload();
						} else {
							alert('Gagal input');
							exit();
						}
					});
				}
			});
		}
	</script>
	<table width="100%">
		<tr>
			<td align="center">
				<?php
				if ($approved) {
					echo "<b>(APPROVED I)</b>";
					if ($approved2) {
						echo "<b>(APPROVED II)</b>";
						if ($rmkurang) {
				?> <input type="button" value="Daftar Kekurangan RM" onclick="window.location='rm_kurang_list.php?no_manufaktur=<?php echo $no_manufaktur; ?>';">
						<?php
						}
					} else {
						?><input type="button" value="Approve II" onclick="if(confirm('Approving Manufaktur <?php echo $no_po; ?>?')){window.location='po_manufaktur_approving2.php?no_manufaktur=<?php echo $no_manufaktur; ?>';}">
					<?php
					}
				} else {
					?><input type="button" value="Approve I" onclick="if(confirm('Approving Manufaktur <?php echo $no_po; ?>?')){window.location='po_manufaktur_approving.php?no_manufaktur=<?php echo $no_manufaktur; ?>';}">
				<?php
				}
				?>
				<?php
				if ($closeco == "1") {
				?>
					<b>(PO Closed)</b>
				<?php
				} else {
				?>
					<input type="button" value="Close PO" onclick="if(confirm('Anda yakin ingin menutup PO <?php echo $no_manufaktur; ?>?')){if(alasan=prompt('Keterangan?')){window.location='po_manufaktur_close.php?no_manufaktur=<?php echo $no_manufaktur; ?>&desc='+alasan;}}">
				<?php
				}
				?>
				<?php
				if ($approved) {
				?><input type="button" value="Print Mode" onclick="window.open('permintaan_manufaktur_print.php?no_manufaktur=<?php echo $no_manufaktur; ?>','permintaan_manufaktur_print','width=800,height=400,menubar=yes,scrollbars=yes');">
				<?php
				}
				?>
				<input type="button" value="Kembali" onclick="window.location='<?php echo $_SERVER['HTTP_REFERER'] ?>';">
			</td>
		</tr>
	</table>
</form>
<?php include_once "footer.php" ?>