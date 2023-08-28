<?php $content_title = "DETIL Real Cutting";
include_once "header.php" ?>

<?php include_once "clsaddrow.php"; ?>

<script src="js/jquery.js"></script>
<script src="js/jquery.ui.datepicker.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css" />
<script src="js/jquery-1.4.js"></script>
<script src="js/jquery-ui.js"></script>
<script src="js/jquery-1.4.2.min.js"></script>
<script src="app_libs/job_cutting.js" type="text/javascript"></script>
<script type='text/javascript' src="js/autocomplete.js"></script>
<link rel="stylesheet" type="text/css" href="css/autocomplete.css" />

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

$no_co = sanitasi($_GET["no_co"]);
$sql = "SELECT no_po,pabrik FROM job_gelaran WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($no_po, $id_pabrik) = mysql_fetch_array($hsltemp);
$sql = "SELECT nama FROM pabrik WHERE id='$id_pabrik'";
$hsltemp = mysql_query($sql, $db);
list($nama_pabrik) = mysql_fetch_array($hsltemp);
$pabrik = "$nama_pabrik [$id_pabrik]";
$sql = "SELECT * FROM job_cutting WHERE no_co='$no_co'";
$hsl = mysql_query($sql, $db);
$rs = mysql_fetch_array($hsl);
$no_co = $rs["no_co"];
$no_jo = $rs["no_jo"];
$no_po = $rs["no_po"];
$tanggal = $rs["tanggal"];
$kode_supplier = $rs["kd_supplier"];
$kd_produk = $rs["kd_produk"];
$approved = $rs["approve"];
$approved2 = $rs["approve2"];
$approveby = $rs["approveby"];
$approveby2 = $rs["approveby2"];
$sql = "SELECT nama FROM supplier WHERE id='$kode_supplier'";
$hsltemp = mysql_query($sql, $db);
list($supplier) = mysql_fetch_array($hsltemp);
$sql = "SELECT * FROM produk WHERE kode = '$kd_produk'";
$hsl = mysql_query($sql, $db);
$rs = mysql_fetch_array($hsl);
$kode = $rs["kode"];
$kode_basic_item = $rs["kode_basic_item"];
$kode_kategori = $rs["kode_kategori"];
$kode_kelas = $rs["kode_kelas"];
$kode_style = $rs["kode_style"];
$kode_warna = $rs["kode_warna"];
$kode_size = $rs["kode_size"];
$sql = "SELECT style FROM mst_style WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode='$kode_style'";
$hsl = mysql_query($sql, $db);
list($style) = mysql_fetch_array($hsl);
$sql = "SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
$hsl = mysql_query($sql, $db);
list($warna) = mysql_fetch_array($hsl);
$sql = "SELECT size FROM mst_size WHERE kode='$kode_size'";
$hsl = mysql_query($sql, $db);
list($size) = mysql_fetch_array($hsl);
$sql = "SELECT no_co FROM job_cutting_majun WHERE no_co='$no_co'";
$hsl = mysql_query($sql, $db);
$majun_approved = false;
$majun_approved = true;
if (mysql_affected_rows($db) > 0) {
	$majun_approved = true;
}

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

	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
		<table>
			<tr>
				<td valign="top">
					<table>
						<tr class="alt">
							<td width="100"><b>No PO</b></td>
							<td width="2"><b>:</b></td>
							<td><?php echo $no_po; ?></td>
						</tr>
						<!--tr>
							<td><b>No JO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_jo; ?></td>
						</tr-->
						<tr>
							<td><b>No CO</b></td>
							<td><b>:</b></td>
							<td id="no_co"><?php echo $no_co; ?></td>
						</tr>
						<tr class="alt">
							<td><b>Pabrik</b></td>
							<td><b>:</b></td>
							<td><?php echo $pabrik; ?></td>
						</tr>
						<!--tr>
							<td><b>Style</b></td>
							<td><b>:</b></td>
							<td><?php echo $style; ?></td>
						</tr>
						<tr>
							<td><b>Warna</b></td>
							<td><b>:</b></td>
							<td><?php echo $warna; ?></td>
						</tr-->
						<tr>
							<td><b>Tanggal</b></td>
							<td><b>:</b></td>
							<td><?php echo $tanggal; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table border="">
			<thead>
				<tr>
					<th><b>No</b></th>
					<th><b>Kode Produk</b></th>
					<th><b>Nama Produk</b></th>
					<th><b>Warna</b></th>
					<th><b>Size</b></th>
					<th><b>Qty Cutting</b></th>
					<!-- <?php
					$sql = "SELECT seqno FROM job_cutting_ikatan WHERE no_co='$no_co' ORDER BY seqno DESC LIMIT 1 ";
					$hsl = mysql_query($sql, $db);
					list($jumlahikatan) = mysql_fetch_array($hsl);
					$jumlahikatan++;
					for ($i = 1; $i <= $jumlahikatan; $i++) {
					?>
						<th align="right"><b><?php echo $i; ?></b></th>
					<?php
					}
					?> -->
					<!-- <th><b>Jumlah</b></th> -->
				</tr>
			</thead>
			<?php
			$sql = "SELECT kd_produk,qty,seqno FROM job_cutting_detail WHERE no_co='$no_co'";
			$hsl = mysql_query($sql, $db);
			$no = 0;
			$JUMLAH = 0;
			$JUMLAHCUTTING = 0;
			while (list($kd_produk, $qtycutting, $seqno) = mysql_fetch_array($hsl)) {
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
				$JUMLAHCUTTING += $qtycutting;
			?>
				<tr>
					<td><?php echo $no; ?></td>
					<td id="id_barang<?php echo $no ?>"><?php echo $kd_produk; ?></td>
					<td><?php echo $nama; ?></td>
					<td><?php echo $warna; ?></td>
					<td><?php echo $size; ?></td>
					<!-- <td align="right"><?php echo number_format($qtycutting); ?></td> -->
					<!-- <?php
					$jumlah = 0;
					for ($i = 1; $i <= $jumlahikatan; $i++) {
						$seqno = $i - 1;
						$sql = "SELECT qty,no_co,seqno FROM job_cutting_detail WHERE no_co='$no_co' AND kd_produk='$kd_produk' AND seqno='$seqno'"; //echo $sql."<br>";
						$hsltemp = mysql_query($sql, $db);
						list($qty, $no_co, $seqno) = mysql_fetch_array($hsltemp);
						$jumlah += $qty;
						$JUMLAH += $qty;
					?> -->
						<td align="right" id="qty<?php echo $no ?><?php echo $seqno ?>" ondblclick="ubah('<?php echo $no ?>','<?php echo $seqno ?>')"><?php echo number_format(($qtycutting)); ?></td>
					<!-- <?php
					}
					?> -->
					<!-- <td align="right"><?php echo $jumlah; ?></td> -->
				</tr>
			<?php
			}
			?>
			<thead>
				<tr>
					<th colspan="5"><b>Jumlah</b></th>
					<th align="right"><b><?php echo number_format($JUMLAHCUTTING); ?></b></th>
					<!-- <th colspan="<?php echo $jumlahikatan; ?>">&nbsp;</th> -->
					<!-- <th align="right"><b><?php echo number_format($JUMLAH); ?></b></th> -->
				</tr>
			</thead>
		</table>
		<table>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table border="">
			<thead>
				<tr>
					<th colspan="6" align="center"><b>PRODUK TAMBAHAN</b></th>
				</tr>
				<tr>
					<th nowrap><b>No</b></th>
					<th><b>Kode Produk</b></th>
					<th><b>Nama Produk</b></th>
					<th><b>Ukuran</b></th>
					<th><b>Qty</b></th>
					<th><b>Satuan</b></th>
				</tr>
			</thead>
			<?php
			$sql = "SELECT * FROM job_cutting_turunan WHERE no_co='$no_co' ORDER BY seqno";
			$hsl = mysql_query($sql, $db);
			$no = 0;
			while ($rs = mysql_fetch_array($hsl)) {
				$no++;
				$barcode = $rs["kd_produk"];
				$sql = "SELECT nama,kode_size,satuan FROM produk WHERE kode='$barcode'";
				$hsltemp = mysql_query($sql, $db);
				list($namaproduk, $kodesize, $satuan) = mysql_fetch_array($hsltemp);
				$sql = "SELECT nama FROM satuan WHERE id='$satuan'";
				$hsltemp = mysql_query($sql, $db);
				list($satuan) = mysql_fetch_array($hsltemp);
				$sql = "SELECT size FROM mst_size WHERE kode='$kodesize'";
				$hsltemp = mysql_query($sql, $db);
				list($size) = mysql_fetch_array($hsltemp);
				$qty = $rs["qty"];
			?>
				<tr>
					<td align="right"><?php echo $no; ?></td>
					<td><?php echo $barcode; ?></td>
					<td><?php echo $namaproduk; ?></td>
					<td><?php echo $size; ?></td>
					<td align="right"><?php echo number_format($qty); ?></td>
					<td align="right"><?php echo $satuan; ?></td>
				</tr>
			<?php
			}
			?>
		</table>
		<table>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table border="">
			<thead>
				<tr>
					<th colspan="10" align="center"><b>RM Terpakai</b></th>
				</tr>
				<tr>
					<th nowrap><b>No</b></th>
					<th><b>Kode RM</b></th>
					<th><b>Nama RM</b></th>
					<th><b>Warna RM</b></th>
					<th><b>Total Qty</b></th>
					<th><b>Satuan</b></th>
					<th><b>Total RM Keluar</b></th>
					<th><b>Total RM Terpakai</b></th>
					<th><b>Consumtion Terpakai</b></th>
					<th><b>Consumtion Estimasi</b></th>
				</tr>
			</thead>
			<?php
			$sql = "SELECT kode_rm,qty,satuan,rm_keluar,rm_terpakai FROM job_cutting_rm_terpakai WHERE no_co='$no_co'";
			$hsltemp = mysql_query($sql, $db);
			$no = 0;
			while (list($kode_rm, $qty_cons, $satuan_rm, $rm_keluar, $rm_terpakai) = mysql_fetch_array($hsltemp)) {
				$no++;
				$sql = "SELECT nama,warna FROM barangdetail WHERE id='$kode_rm'";
				$hsltemp = mysql_query($sql, $db);
				list($nama_rm, $warna_id) = mysql_fetch_array($hsltemp);
				$sql = "SELECT warna FROM mst_warna WHERE kode='$warna_id'";
				$hsltemp = mysql_query($sql, $db);
				list($warna_rm) = mysql_fetch_array($hsltemp);
				$warna_rm = "$warna_rm [$warna_id]";
			?>
				<tr>
					<td align="right"><?php echo $no; ?></td>
					<td><?php echo $kode_rm; ?></td>
					<td><?php echo $nama_rm; ?></td>
					<td><?php echo $warna_rm; ?></td>
					<td align="right"><?php echo $qty_cons; ?></td>
					<td align="right"><?php echo $satuan; ?></td>
					<td align="right"><?php echo $rm_keluar; ?></td>
					<td align="right" ondblclick="ubah_rm_terpakai('<?php echo $rm_terpakai ?>')" id="rm_terpakai"><?php echo $rm_terpakai;  ?>
						<?php
						$consumsion_terpakai = $rm_terpakai / $JUMLAHCUTTING;
						$sql = "SELECT c_produk FROM job_gelaran_detail_rian WHERE no_po='$no_po' AND no_co='$no_co' ";
						$resultrian = mysql_query($sql); //or die($sql);
						list($consumsion_estimasi) = mysql_fetch_array($resultrian);
						?>
					</td>
					<td align="right"><?php echo number_format($consumsion_terpakai, "2", ".", ",") ?></td>
					<td align="right"><?php echo number_format($consumsion_estimasi, "2", ".", ",") ?></td>
				</tr>
			<?php
			}
			?>
		</table>
		<table>
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table width="100%">
			<tr>
				<td align="center">
					<?php
					if ($majun_approved) {
					?><input type="button" value="MAJUN" onclick="window.location='job_cutting_majun_detail.php?no_co=<?php echo $no_co; ?>';">
						<?php
						if ($approved) {
							echo "<b>(APPROVED I BY $approveby)</b>";
							if ($approved2) {
								echo "<b>(APPROVED II BY $approveby2)</b>";
							} else {
						?> <input type="button" value="Approve II" onclick="if(confirm('Approving CO <?php echo $no_co; ?>?')){window.location='job_cutting_approving2.php?no_co=<?php echo $no_co; ?>';}">
							<?php
							}
						} else {
							?>
							<input type="button" value="Approve I" onclick="if(confirm('Approving CO <?php echo $no_co; ?>?')){window.location='job_cutting_approving.php?no_co=<?php echo $no_co; ?>';}">
						<?php
						}
					} else {
						?><input type="button" value="MAJUN" onclick="window.location='job_cutting_majun_add.php?no_co=<?php echo $no_co; ?>';">
					<?php
					}
					?>
					<?php
					if ($approved) {
					?><input type="button" value="Print Mode" onclick="window.open('job_cutting_print.php?no_co=<?php echo $no_co; ?>','job_cutting_print','width=800,height=400,menubar=yes,scrollbars=yes');">
						<?php
					}
					if ($approved2) {
						$sql = "SELECT no_load FROM job_loading WHERE no_co='$no_co'";
						$hsltemp = mysql_query($sql, $db);
						if (mysql_affected_rows($db) > 0) {
							list($no_load) = mysql_fetch_array($hsltemp);
						?> <input type="button" value="LOADING" onclick="window.location='job_loading_list.php?no_co=<?php echo $no_co; ?>';">
					<?php
						}
					}
					?>
					<input type="button" value="Kembali" onclick="window.location='job_cutting_list.php';">
				</td>
			</tr>
		</table>
	</form>
	<script>
		$(document).ready(function() {

		});

		function ubah_rm_terpakai(rm_terpakai) {
			$("#rm_terpakai").html("<input type='text' id='txtrm' name='txtrm' value='" + rm_terpakai + "'><input type='button' id='button' name='button' value='Ubah' onclick=ubah_proses()>");
		}

		function ubah_proses() {
			var nilai = $("#txtrm").val();
			var no_co = $("#no_co").text().trim();
			var data = "nilai=" + nilai + "&proses=ubah_rm_terpakai&no_co=" + no_co;

			$.post("ubah_ikatan.php", data, function(response) {
				;
				if (response.trim() == "berhasil") {
					document.location.reload();
				}
			});
		}

		function ubah(no, seqno) {
			var id_barang = $("#id_barang" + no).text();
			var no_co = $("#no_co").text();
			var qty = parseFloat($("#qty" + no + "" + seqno).text().replace(/,/g, ''), 10);

			$("#qty" + no + "" + seqno).html("<input type='text' id='text" + seqno + "' name='text" + seqno + "' value='" + qty + "' size='5'><input type='button' id='simpan' name='simpan' value='Simpan' onclick=proses_simpan('" + id_barang + "','" + no_co + "','" + qty + "','" + no + "','" + seqno + "')>");

		}

		function proses_simpan(id_barang, no_co, qty, no, seqno) {

			//alert("id_barang="+id_barang+"\n no_co="+no_co+"\n qty="+qty+"\n no="+no+"\n seqno="+seqno);
			var qty = $("#text" + seqno).val();
			var data = "id_barang=" + id_barang + "&no_co=" + no_co + "&qty=" + qty + "&no=" + no + "&seqno=" + seqno;
			$.post("ubah_ikatan.php", data, function(response) {
				if (response.trim() == 'berhasil') {
					document.location.reload();
				}
			});
		}
	</script>
	<?php include_once "footer.php" ?>