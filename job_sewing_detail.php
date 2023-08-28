<?php $content_title = "DETIL SEWING";
include_once "header.php";

?>
<?php include_once "clsaddrow.php";
include "pdo_produksi/Db.class.php";
include("css_group.php");
?>

<style>
	.datagrid table {
		border-collapse: collapse;
		text-align: left;
		width: 100%;
	}

	.datagrid {
		font: normal 12px/150% Arial, Helvetica, sans-serif;
		background: #fff;
		overflow: hidden;
		border: 1px solid #36752D;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}

	.datagrid table td,
	.datagrid table th {
		padding: 3px 10px;
	}

	.datagrid table thead th {
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420));
		background: -moz-linear-gradient(center top, #36752D 5%, #275420 100%);
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');
		background-color: #36752D;
		color: #FFFFFF;
		font-size: 12px;
		font-weight: bold;
		border-left: 1px solid #36752D;
	}

	.datagrid table thead th:first-child {
		border: none;
	}

	.datagrid table tbody td {
		color: #275420;
		border-left: 1px solid #C6FFC2;
		font-size: 10px;
		font-weight: normal;
	}

	.datagrid table tbody .alt td {
		background: #DFFFDE;
		color: #275420;
	}

	.datagrid table tbody td:first-child {
		border-left: none;
	}

	.datagrid table tbody tr:last-child td {
		border-bottom: none;
	}

	.datagrid table tfoot td div {
		border-top: 1px solid #36752D;
		background: #DFFFDE;
	}

	.datagrid table tfoot td {
		padding: 0;
		font-size: 12px
	}

	.datagrid table tfoot td div {
		padding: 2px;
	}

	.datagrid table tfoot td ul {
		margin: 0;
		padding: 0;
		list-style: none;
		text-align: right;
	}

	.datagrid table tfoot li {
		display: inline;
	}

	.datagrid table tfoot li a {
		text-decoration: none;
		display: inline-block;
		padding: 2px 8px;
		margin: 1px;
		color: #FFFFFF;
		border: 1px solid #36752D;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420));
		background: -moz-linear-gradient(center top, #36752D 5%, #275420 100%);
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');
		background-color: #36752D;
	}

	.datagrid table tfoot ul.active,
	.datagrid table tfoot ul a:hover {
		text-decoration: none;
		border-color: #275420;
		color: #FFFFFF;
		background: none;
		background-color: #36752D;
	}

	div.dhtmlx_window_active,
	div.dhx_modal_cover_dv {
		position: fixed !important;
	}

	.kelas_departemen {
		width: 80px;
		border: thin solid #06F;
		position: static;
		margin-left: 950px;
		margin-top: -15px;
		position: inherit !important;
		text-align: center;
		cursor: pointer;

	}

	.kelas_departemen:hover {
		background-color: #E2FBFC;

	}
</style>

<?php
$no_sew = sanitasi($_GET["no_sew"]);
$sql = "SELECT * FROM job_sewing WHERE no_sew='$no_sew'";
$hsl = mysql_query($sql, $db);
$rs = mysql_fetch_array($hsl);
$no_sew = $rs["no_sew"];
$no_load = $rs["no_load"];
$sql = "SELECT no_co FROM job_loading WHERE no_load='$no_load'";
$hsltemp = mysql_query($sql, $db);
list($no_co) = mysql_fetch_array($hsltemp);
$sql = "SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($no_jo, $no_po) = mysql_fetch_array($hsltemp);
$tanggal = $rs["tanggal"];
$totalqty = $rs["totalqty"];
$jumlah = $rs["totalrp"];
$approved = $rs["approve"];
$approved2 = $rs["approve2"];
$approveby = $rs["approveby"];
$approveby2 = $rs["approveby2"];
$sql = "SELECT no_po FROM job_gelaran WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($no_po) = mysql_fetch_array($hsltemp);
$sql = "SELECT pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
$hsltemp = mysql_query($sql, $db);
list($id_pabrik) = mysql_fetch_array($hsltemp);
$sql = "SELECT nama FROM pabrik WHERE id='$id_pabrik'";
$hsltemp = mysql_query($sql, $db);
list($nama_pabrik) = mysql_fetch_array($hsltemp);
$pabrik = "$nama_pabrik [$id_pabrik]";
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



<div class="datagrid">
	<link rel="stylesheet" href="themes/base/jquery.ui.all.css">
	<script src="ui/jquery.ui.core.js"></script>
	<script src="ui/jquery.ui.widget.js"></script>
	<script src="ui/jquery.ui.datepicker.js"></script>
	<script>
		$(function() {
			$("#bulan").datepicker({
				dateFormat: 'yy-mm-dd',
				changeMonth: true,
				changeYear: true
			});
		});
	</script>
	<style>
		fieldset {
			border: 1px solid green
		}

		legend {
			padding: 0.2em 0.5em;
			border: 1px solid green;
			color: green;
			font-size: 90%;
		}
	</style>
	<form method="POST" id="f1" action="job_sewing_approving2.php?no_sew=<?php echo $no_sew; ?>">
		<table class="table " style="font-size: 10pt" height="28">
			<tr class="alt">
				<td width="100"><b>Pabrik</b></td>
				<td width="2"><b>:</b></td>
				<td><?php echo $pabrik; ?></td>
			</tr>
			<tr>
				<td><b>No PO</b></td>
				<td><b>:</b></td>
				<td><?php echo $no_po; ?></td>
			</tr>

			<tr class="alt">
				<td><b>No CO</b></td>
				<td><b>:</b></td>
				<td><?php echo $no_co; ?></td>
			</tr>
			<tr>
				<td><b>No LOAD</b></td>
				<td><b>:</b></td>
				<td><?php echo $no_load; ?></td>
			</tr>
			<tr class="alt">
				<td><b>No Sewing</b></td>
				<td><b>:</b></td>
				<td id="no_sew"><?php echo $no_sew; ?></td>
			</tr>
			<tr>
				<td><b>Tanggal</b></td>
				<td><b>:</b></td>
				<td><?php echo $tanggal; ?></td>
			</tr>
			<tr class="alt">
				<td><b>Total Qty</b></td>
				<td><b>:</b></td>
				<td><?php echo $totalqty; ?></td>
			</tr>
		</table>

		<table class="table " style="font-size: 10pt" height="28">
			<thead>
				<tr>
					<th><b>No</b></th>
					<th><b>Kode Produk</b></th>
					<th><b>Nama Produk</b></th>
					<th><b>Warna</b></th>
					<th><b>Size</b></th>
					<th><b>Jumlah</b></th>

					<th><b>Qty Bagus</b></th>
					<th><b>Reject</b></th>
					<th><b>Pending</b></th>
				</tr>
			</thead>
			<tbody><?php
					$sql = "SELECT kd_produk,qty,reject,pending FROM job_sewing_detail WHERE no_sew='$no_sew' ORDER BY seqno";
					$hsl = mysql_query($sql, $db);
					$no = 0;
					$totalqty = 0;
					$totalreject = 0;
					$totalpending = 0;
					$totalqty_bagus = 0;
					while (list($kd_produk, $qty, $reject, $pending) = mysql_fetch_array($hsl)) {
						$no++;
						$sql = "SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
						$hsltemp = mysql_query($sql, $db);
						list($nama, $kode_warna, $kode_size, $satuan) = mysql_fetch_array($hsltemp);
						$sql = "SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
						$hsltemp = mysql_query($sql, $db);
						list($warna) = mysql_fetch_array($hsltemp);
						$sql = "SELECT size FROM mst_size WHERE kode='$kode_size'";
						$hsltemp = mysql_query($sql, $db);
						list($size) = mysql_fetch_array($hsltemp);
						$totalqty += $qty;
						$totalreject += $reject;
						$totalpending += $pending;
						$qty_bagus = $qty - $reject - $pending;
						$totalqty_bagus += $qty_bagus;
					?>
					<tr>
						<td><?php echo $no; ?></td>
						<td><?php echo $kd_produk; ?></td>
						<td><?php echo $nama; ?></td>
						<td><?php echo $warna; ?></td>
						<td><?php echo $size; ?></td>
						<td align="right" id="jml<?php echo $kd_produk ?>" class="jml<?php echo $no ?>"><?php echo $qty; ?></td>
						<?php
						if (!$approved || $approved2) {
						?>
							<td align="right" ondblclick="ubah_qty('<?php echo $kd_produk ?>')" id="bagus<?php echo $kd_produk ?>"><?php echo number_format($qty_bagus); ?></td>
							<td align="right" id="reject<?php echo $kd_produk ?>"><?php echo number_format($reject); ?></td>
							<td align="right"><?php echo number_format($pending); ?></td>

						<?php
						} else {
						?><td><input type="text" size="5" id="bgs<?php echo $no ?>" name="bagus[<?php echo $kd_produk; ?>]" value="<?php echo number_format($qty_bagus); ?>" onkeypress="pilih1('bgs','<?php echo $id_produk ?>','<?php echo $no ?>')"></td>
							<td><input type="text" size="5" id="rjc<?php echo $no ?>" name="reject[<?php echo $kd_produk; ?>]" value="0" onkeypress="pilih1('rjc','<?php echo $id_produk ?>','<?php echo $no ?>')"></td>
							<td><input type="text" size="5" id="pen<?php echo $no ?>" name="pending[<?php echo $kd_produk; ?>]" value="0" readonly="" onkeypress="pilih1('pen','<?php echo $id_produk ?>','<?php echo $no ?>')"></td>

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

					<td align="right"><b><?php echo number_format($totalqty_bagus); ?></b></td>
					<td align="right"><b><?php echo number_format($totalreject); ?></b></td>
					<td align="right"><b><?php echo number_format($totalpending); ?></b></td>
				</tr>
		</table>
		<input type="hidden" id="tt" name="tt" value="<?php echo $no ?>" /><br />&nbsp;&nbsp;&nbsp;
		<table class="table " style="font-size: 10pt" height="28">
			<thead>
				<tr>

					<th colspan="9" align="center"><b>PRODUK TAMBAHAN</b></th>
				</tr>
				<tr>
					<th><b>No</b></th>
					<th><b>Kode Produk</b></th>
					<th><b>Nama Produk</b></th>
					<th><b>Warna</b></th>
					<th><b>Size</b></th>
					<th><b>Jumlah</b></th>
					<th><b>Reject</b></th>
					<th><b>Pending</b></th>
					<th><b>Qty Bagus</b></th>
				</tr>
			</thead>
			<?php
			$sql = "SELECT kd_produk,qty,reject,pending FROM job_sewing_turunan WHERE no_sew='$no_sew' ORDER BY seqno";
			$hsl = mysql_query($sql, $db);
			$no = 0;
			$totalqty = 0;
			$totalreject = 0;
			$totalpending = 0;
			$totalqty_bagus = 0;
			while (list($kd_produk, $qty, $reject, $pending) = mysql_fetch_array($hsl)) {
				$no++;
				$sql = "SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
				$hsltemp = mysql_query($sql, $db);
				list($nama, $kode_warna, $kode_size, $satuan) = mysql_fetch_array($hsltemp);
				$sql = "SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
				$hsltemp = mysql_query($sql, $db);
				list($warna) = mysql_fetch_array($hsltemp);
				$sql = "SELECT size FROM mst_size WHERE kode='$kode_size'";
				$hsltemp = mysql_query($sql, $db);
				list($size) = mysql_fetch_array($hsltemp);
				$totalqty += $qty;
				$totalreject += $reject;
				$totalpending += $pending;
				$qty_bagus = $qty - $reject - $pending;
				$totalqty_bagus += $qty_bagus;
			?>
				<tr>
					<td><?php echo $no; ?></td>
					<td><?php echo $kd_produk; ?></td>
					<td><?php echo $nama; ?></td>
					<td><?php echo $warna; ?></td>
					<td><?php echo $size; ?></td>
					<td align="right" id="qty<?php echo $no ?>"><?php echo number_format($qty); ?></td>
					<?php
					if (!$approved || $approved2) {
					?>
						<td align="right"><?php echo number_format($reject); ?></td>
						<td align="right"><?php echo number_format($pending); ?></td>
					<?php
					} else {
					?>
						<td><input type="text" size="5" name="rejectturunan[<?php echo $kd_produk; ?>]" value="0"></td>
						<td><input type="text" size="5" name="pendingturunan[<?php echo $kd_produk; ?>]" value="0"></td>
					<?php
					}
					?>
					<td align="right"><?php echo number_format($qty_bagus); ?></td>
				</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="5"><b>Jumlah</b></td>
				<td align="right"><b><?php echo number_format($totalqty); ?></b></td>
				<td align="right"><b><?php echo number_format($totalreject); ?></b></td>
				<td align="right"><b><?php echo number_format($totalpending); ?></b></td>
				<td align="right"><b><?php echo number_format($totalqty_bagus); ?></b></td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="center">
					<?php
					if ($approved) {
						echo "<b>(APPROVED I BY $approveby)</b>";
						if ($approved2) {
							echo "<b>(APPROVED II BY $approveby2)</b>";
						} else {
					?> <input type="button" name="approve" value="Approve II" onclick="if(confirm('Approving SEWING <?php echo $no_sew; ?>?')){aksi();}">
						<?php
						}
					} else {
						?> <input type="button" value="Approve I" onclick="if(confirm('Approving SEWING <?php echo $no_sew; ?>?')){window.location='job_sewing_approving.php?no_sew=<?php echo $no_sew; ?>';}">
					<?php
					}
					?>
				</td>
				<td>
					<?php
					if ($approved2) {
						$sql = "SELECT no_qc FROM job_qc WHERE no_sew='$no_sew'";
						$hsltemp = mysql_query($sql, $db);
						if (mysql_affected_rows($db) > 0) {
					?> <input type="button" value="Quality Control" onclick="window.location='job_qc_list.php?no_sew=<?php echo $no_sew; ?>';">
						<?php
						}
					}
					if ($approved) {
						?><input type="button" value="Print Mode" onclick="window.open('job_sewing_print.php?no_sew=<?php echo $no_sew; ?>','job_sewing_print','width=800,height=400,menubar=yes,scrollbars=yes');">
					<?php
					}
					?>
					<input type="button" value="Kembali" onclick="window.location='job_sewing_list_v2.php?no_co=<?php echo $no_co; ?>';">
				</td>
			</tr>
		</table>
	</form>
	<script>
		function ubah_qty(id_barang) {
			$("#bagus" + id_barang).html("<input type='hidden' id='jml_bagus" + id_barang + "' name='jml_bagus" + id_barang + "' value='" + $("#bagus" + id_barang).text().trim() + "'>");
			$("#reject" + id_barang).html("<input type='text' id='jml_reject" + id_barang + "' name='jml_reject" + id_barang + "' value='" + $("#reject" + id_barang).text().trim() + "'><input type='button' class='simpan' value='S' onclick=simpan('" + id_barang + "')>");
		}

		function aksi() {

			var jml = $("#tt").val();

			for (var j = 1; j <= jml; j++) {
				var rej = $("#rjc" + j).val();
				var bg = $("#bgs" + j).val();
				var qty = $(".jml" + j).text();
				var t = parseFloat(qty) - rej - bg;
				if (t < 0) {
					alert('qty tidak valid');
					exit();
				}
				$("#pen" + j).val(t);

			}
			$("#f1").submit();
		}

		function simpan(id_barang) {
			var no_sew = $("#no_sew").text().trim();
			var bagus = $("#jml_bagus" + id_barang).val();
			var reject = $("#jml_reject" + id_barang).val();
			var jml = $("#jml" + id_barang).text();
			var total = parseFloat(bagus) + parseFloat(reject);
			var data = "no_sew=" + no_sew + "&bagus=" + bagus + "&reject=" + reject + "&proses=ubah_qty&id_barang=" + id_barang;
			$.post("job_sewing_detail_proses.php", data, function(response) {
				if (response.trim() == "berhasil") {
					$("#bagus" + id_barang).text(jml - reject);
					$("#reject" + id_barang).text(reject);
				} else {
					alert(response);
				}
			});

		}


		function pilih1(nf, kd, no) {

			window.onkeydown = function(e) {

				if (e.keyCode === 13) {
					var qty = $(".jml" + no).text();
					var rej = $("#rjc" + no).val();
					var bg = $("#bgs" + no).val();
					var t = parseFloat(qty) - rej - bg;
					if (t < 0) {
						alert('qty tidak valid');
						exit();
					}
					$("#pen" + no).val(t);



					return false;
				}
			}
		}
	</script>
	<?php include_once "footer.php" ?>