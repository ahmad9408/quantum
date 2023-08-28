<?php $content_title = "DETIL SEWING";
include_once "header.php" ?>
<?php
include_once "job_loading_init.php";
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
		width: 50px;
		border: thin solid #06F;
		position: static;
		position: inherit !important;
		text-align: center;
		cursor: pointer;

	}

	.kelas_departemen:hover {
		background-color: #E2FBFC;

	}

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
<link rel="stylesheet" href="themes/base/jquery.ui.all.css">

<?php

$_pabrik = " like '%'";
if (strtoupper($_SESSION['outlettype']) == "P") {
	$_pabrik = $_SESSION['outlet'];
	if ($_pabrik == 'P0006') {
		$sql = "select id from pabrik where mk='1' ";
		$resri = mysql_query($sql) or die($sql);
		$banyak_pabrik = mysql_num_rows($resri);
		while (list($kd_pabrik) = mysql_fetch_array($resri)) {
			$j++;
			if ($j == $banyak_pabrik) {
				$pabrik .= "'$kd_pabrik'";
			} else {
				$pabrik .= "'$kd_pabrik',";
			}
		}
		$_pabrik = " in (" . $pabrik . ")";
	} else {
		$_pabrik = " like '$_pabrik%'";
	}
}

$no_load = sanitasi($_GET["no_load"]);
$sql = "SELECT no_load FROM job_loading WHERE no_load='$no_load'";
$hsl = mysql_query($sql, $db);
if (mysql_affected_rows($db) < 1) {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No Load Tidak Ada");
		window.location = "job_sewing_rekap.php";
	</script>
	<?php
}
$no_sew = no_sew();

$sql = "SELECT no_co FROM job_loading WHERE no_load='$no_load'";
$hsltemp = mysql_query($sql, $db);
list($no_co) = mysql_fetch_array($hsltemp);

$sql = "SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($no_jo, $no_po) = mysql_fetch_array($hsltemp);

$id_line = sanitasi($_POST["linepabrik"]);
$tanggal = $rs["tanggal"];
$tanggal = date("Y-m-d H:i:s");
$totalqty = $rs["totalqty"];
$jumlah = $rs["totalrp"];
$approved = $rs["approve"];
$approved2 = $rs["approve2"];
$approveby = $rs["approveby"];
$approveby2 = $rs["approveby2"];
if ($_POST["simpan"]) {
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	// Array
	// (
	// [qty] => Array
	// (
	// [BBA120005001002] =>
	// [BBA110005001002] =>
	// [BAA010002001002] =>
	// )

	// [qtyturunan] => Array
	// (
	// [AAA010100001003] =>
	// [ABA010000999003] =>
	// )

	// [simpan] => Simpan
	// )

	//cek qty tidak boleh nol dan tidak boleh > dari qty yang belum
	$sql = "SELECT kd_produk,qty_produk,satuan_produk FROM job_loading_detail WHERE no_load='$no_load' GROUP BY kd_produk ORDER BY seqno";
	$hsl = mysql_query($sql, $db);
	$qtyvalid = true;
	$semuanol = true;
	while (list($kd_produk, $qty_produk, $satuan_produk) = mysql_fetch_array($hsl)) {
		$sql = "SELECT sum(qty) FROM job_sewing_detail WHERE kd_produk='$kd_produk' AND no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load='$no_load')";
		$hsltemp = mysql_query($sql, $db);
		list($qtysudah) = mysql_fetch_array($hsltemp);
		$qtybelum = $qty_produk - $qtysudah;
		if (sanitasi($_POST["qty"][$kd_produk]) > 0) {
			$semuanol = false;
			if (sanitasi($_POST["qty"][$kd_produk]) > $qtybelum) {
				$qtyvalid = false;
				break;
			}
		}
	}
	if ($qtyvalid) {
		//TURUNAN
		$sql = "SELECT kd_produk,qty_produk,satuan_produk FROM job_loading_turunan WHERE no_load='$no_load' GROUP BY kd_produk ORDER BY seqno";
		$hsl = mysql_query($sql, $db);
		while (list($kd_produk, $qty_produk, $satuan_produk) = mysql_fetch_array($hsl)) {
			$sql = "SELECT sum(qty) FROM job_sewing_turunan WHERE kd_produk='$kd_produk' AND no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load='$no_load')";
			$hsltemp = mysql_query($sql, $db);
			list($qtysudah) = mysql_fetch_array($hsltemp);
			$qtybelum = $qty_produk - $qtysudah;
			if (sanitasi($_POST["qtyturunan"][$kd_produk]) > $qtybelum) {
				$qtyvalid = false;
				break;
			}
		}
	}

	if (!$semuanol) {
		//if($qtyvalid){
		$totalqty = 0;
		$totalrp = 0;
		$seqno = -1;
		$no_co = sanitasi($_POST["no_co"]);
		foreach ($_POST["qty"] as $kd_produk => $qtyproduk) {
			$seqno++;
			$sql = "SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
			$hsltemp = mysql_query($sql, $db);
			list($harga) = mysql_fetch_array($hsltemp);
			$jumlah = $harga * $qtyproduk;
			$totalqty += $qtyproduk;
			$totalrp += $jumlah;
			$id_line = sanitasi($_POST["linepabrik"]);
			$no_co = sanitasi($_POST["no_co"]);
			if ($qtyproduk > 0) {

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("INSERT INTO job_sewing_detail (no_sew,seqno,kd_produk,harga,qty,reject,pending,no_co,no_line) VALUES ('$no_sew','$seqno','$kd_produk','$harga','$qtyproduk','0','0','$no_co','$id_line')");
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

		$no_co = sanitasi($_POST["no_co"]);
		$id_line = sanitasi($_POST["linepabrik"]);

		$sql = new Db();

		try {
			$beginTransaction = $sql->beginTransaction();

			$sql->query("INSERT INTO job_sewing (no_sew,no_load,tanggal,totalqty,totalrp,approve,approveby,approvedate,no_co,no_line) VALUES ('$no_sew','$no_load',NOW(),'$totalqty','$totalrp','1','$username',NOW(),'$no_co','$id_line')");
			mysql_query($sql, $db);


			$executeTransaction = $sql->executeTransaction();
		} catch (PDOException $e) {
			//atau (Exception $e) 
			$rollBack = $sql->rollBack();
			echo "error msg: " . $e->getMessage();
			throw $e;
		}


		//TURUNAN
		$totalqty = 0;
		$totalrp = 0;
		$seqno = -1;
		foreach ($_POST["qtyturunan"] as $kd_produk => $qtyproduk) {
			$seqno++;
			$sql = "SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
			$hsltemp = mysql_query($sql, $db);
			list($harga) = mysql_fetch_array($hsltemp);
			$jumlah = $harga * $qtyproduk;
			$totalqty += $qtyproduk;
			$totalrp += $jumlah;
			if ($qtyproduk > 0) {

				$sql = new Db();

				try {
					$beginTransaction = $sql->beginTransaction();

					$sql->query("INSERT INTO job_sewing_turunan (no_sew,seqno,kd_produk,harga,qty,reject,pending) VALUES ('$no_sew','$seqno','$kd_produk','$harga','$qtyproduk','0','0')");
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
		#die();
	?>
		<script language="javascript">
			alert("Sewing Tersimpan");
			window.location = "job_sewing_rekap.php";
		</script>
		<?php
		//	}else{
		?>
		<!--
					<script language="javascript">
						alert("Qty Sewing harus lebih kecil atau sama dengan Qty Yang Belum");
						//window.location="job_sewing_list.php";
					</script> -->
	<?php
		//}
	} else {
	?>
		<script language="javascript">
			alert("Salah satu Qty Sewing harus lebih besar dari 0!");
			//window.location="job_sewing_list.php";
		</script>
<?php
	}
}


?>
<form method="POST" id="f1" name="f1" action="job_sewing_add.php?no_load=<?php echo $no_load; ?>" class="datagrid" cellspacing="0" cellpadding="0">
	<table class="table " style="font-size: 10pt" height="28">
		<tr>
			<td valign="top" width="100">
				<table class="table " style="font-size: 10pt" height="28">
					<tr class="alt" height="20">
						<td height="20" width="100"><b>No PO</b></td>
						<td width="2"><b>:</b></td>
						<td><?php echo $no_po; ?><input type="hidden" id="simpan" name="simpan" value="1" /></td>
					</tr>
					<tr height="20">
						<td><b>No JO</b></td>
						<td><b>:</b></td>
						<td><?php echo $no_jo; ?></td>
					</tr>
					<tr class="alt" height="20">
						<td><b>No CO</b></td>
						<td><b>:</b></td>
						<td><?php echo $no_co; ?></td>
					</tr>
					<tr height="20">
						<td><b>No LOAD</b></td>
						<td><b>:</b></td>
						<td><?php echo $no_load; ?></td>
					</tr>
					<tr class="alt" height="20">
						<td><b>No Sewing</b></td>
						<td><b>:</b></td>
						<td><?php echo $no_sew; ?></td>
					</tr>
					<tr height="20">
						<td><b>Tanggal</b></td>
						<td><b>:</b></td>
						<td><?php echo $tanggal; ?></td>
					</tr>
				</table>
			</td>
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
				<th><b>Qty Loading</b></th>
				<th><b>Qty Yg Belum</b></th>
				<th><b>Qty Sewing</b></th>
			</tr>
		</thead>
		<?php
		// $sql="SELECT kd_produk,qty,reject,pending FROM job_sewing_detail WHERE no_sew='$no_sew' ORDER BY seqno";
		$sql = "SELECT kd_produk,qty_produk FROM job_loading_detail WHERE no_load='$no_load' GROUP BY kd_produk ORDER BY seqno";
		$hsl = mysql_query($sql, $db);
		$banyak = mysql_num_rows($hsl);
		$no = 0;
		$_adasewing = false;
		?><input type="hidden" id="by" name="by" value="<?php echo $banyak ?>" /><?php
																					while (list($kd_produk, $qty) = mysql_fetch_array($hsl)) {
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

																						//	$sql="SELECT sum(qty-reject-pending) FROM job_sewing_detail WHERE kd_produk='$kd_produk' AND no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load='$no_load')";
																						$sql = "SELECT sum(qty-pending) FROM job_sewing_detail WHERE kd_produk='$kd_produk' AND no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load='$no_load')";

																						$hsltemp = mysql_query($sql, $db);
																						list($qtysudah) = mysql_fetch_array($hsltemp);
																						$qtybelum = $qty - $qtysudah;
																						if ($qtybelum > 0) {
																							$_adasewing = true;
																						}

																						if ($no % 2 == 1) {
																							$kelas1 = "alt";
																						} else {
																							$kelas1 = "";
																						}
																					?>
			<tr class="<?php echo $kelas1 ?>">
				<td><?php echo $no; ?></td>
				<td><?php echo $kd_produk; ?></td>
				<td><?php echo $nama; ?></td>
				<td><?php echo $warna; ?></td>
				<td><?php echo $size; ?></td>
				<td align="right" id="tdqtyload<?php echo $no ?>"><?php echo number_format($qty); ?></td>
				<td align="right" id="tdqty<?php echo $no ?>"><?php echo number_format($qtybelum); ?></td>
				<td align="left"><input style="width:100px" class="form-control" type="text" id="fqty<?php echo $no ?>" size="5" name="qty[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["qty"][$kd_produk]); ?>"></td>
			</tr>
		<?php
																					}
		?>
		<thead>
			<tr class="alt" height="20">
				<th colspan="4"><b> </b></th>
				<th colspan="3" align="right"><b>Line Pabrik</b></th>
				<th>
					<select style="width:150px" class="form-control" name="linepabrik" id="linepabrik">
						<option value="">Pilih Line Pabrik</option>
						<?php
						$sql = "SELECT id,keterangan FROM job_sewing_line WHERE status='1' AND id $_pabrik";
						$hsltemp = mysql_query($sql, $db);
						while (list($id_line, $keterangan) = mysql_fetch_array($hsltemp)) {
						?>
							<option value="<?php echo $id_line; ?>"><?php echo "$id_line [$keterangan]"; ?></option>
						<?php
						}
						?>
				</th>
			</tr>

		</thead>
	</table>
	<table class="table " style="font-size: 10pt" height="28">
		<thead>
			<tr>
				<th colspan="8" align="center"><b>PRODUK TAMBAHAN</b>
				</th>
			</tr>
			<tr>
				<th><b>No</b>
				</th>
				<th><b>Kode Produk</b>
				</th>
				<th><b>Nama Produk</b>
				</th>
				<th><b>Warna</b>
				</th>
				<th><b>Size</b>
				</th>
				<th><b>Qty Loading</b>
				</th>
				<th><b>Qty Yg Belum</b>
				</th>
				<th><b>Qty Sewing</b>
				</th>
			</tr>
		</thead>
		<?php
		//$sql="SELECT kd_produk,qty,reject,pending FROM job_sewing_turunan WHERE no_sew='$no_sew' ORDER BY seqno";
		$sql = "SELECT kd_produk,qty_produk FROM job_loading_turunan WHERE no_load='$no_load' GROUP BY kd_produk ORDER BY seqno";
		$hsl = mysql_query($sql, $db);
		$no = 0;
		while (list($kd_produk, $qty) = mysql_fetch_array($hsl)) {
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

			$sql = "SELECT sum(qty-reject-pending) FROM job_sewing_turunan WHERE kd_produk='$kd_produk' AND no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load='$no_load')";
			$hsltemp = mysql_query($sql, $db);
			list($qtysudah) = mysql_fetch_array($hsltemp);
			$qtybelum = $qty - $qtysudah;
		?>
			<tr>
				<td><?php echo $no; ?></td>
				<td><?php echo $kd_produk; ?></td>
				<td><?php echo $nama; ?></td>
				<td><?php echo $warna; ?></td>
				<td><?php echo $size; ?></td>
				<td align="right"><?php echo number_format($qty); ?></td>
				<td align="right"><?php echo number_format($qtybelum); ?></td>
				<td align="right"><input type="text" size="5" name="qtyturunan[<?php echo $kd_produk; ?>]" value="<?php echo sanitasi($_POST["qtyturunan"][$kd_produk]); ?>"></td>
			</tr>
		<?php
		}
		?>
	</table>
	<table width="100%">
		<tr>
			<td align="center">
				<input type="button" value="Simpan" onclick="job_sewing_proses()">
				<input type="button" value="Kembali" onclick="window.location='job_sewing_rekap.php';">
			</td>
		</tr>
	</table>
</form>
<?php include_once "footer.php" ?>
<script src="jquery.js"></script>
<script>
	function job_sewing_proses() {
		var banyak = $("#by").val();
		for (var i = 1; i <= banyak; i++) {
			var fqty = parseFloat($("#fqty1").val());
			var tdqty = parseFloat($("#tdqty1").text().replace(/,/g, ''), 10);
			var tdqtyload = parseFloat($("#tdqtyload1").text().replace(/,/g, ''), 10);

			if (tdqty < fqty) {
				alert('Maaf qty tidak valid');
				exit();
			}
		}

		$("#f1").submit();
	}
</script>
<?php
if (!$_adasewing) {
?>
	<script language="javascript">
		//alert ('Job Loading ini sudah di sewing semua!');
		//window.location='job_sewing_list.php';
	</script>
<?php
}
?>