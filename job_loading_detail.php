<?php $content_title = "DETIL LOADING";
include_once "header.php" ?>
<?php include_once "clsaddrow.php"; ?>

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
</style>

<?php
$no_load = sanitasi($_GET["no_load"]);
$sql = "SELECT * FROM job_loading WHERE no_load='$no_load'";
$hsl = mysql_query($sql, $db);
$rs = mysql_fetch_array($hsl);
$no_co = $rs["no_co"];
$pabrik_tujuan_id = $rs["pabrik_tujuan"];
$sql = "SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($no_jo, $no_po) = mysql_fetch_array($hsltemp);
$tanggal = $rs["tanggal"];
$totalqtyproduk = $rs["totalqtyproduk"];
$totalqty = $rs["totalqty"];
$jumlah = $rs["totalrp"];
$approved = $rs["approve"];
$approved2 = $rs["approve2"];
$approveby = $rs["approveby"];
$approveby2 = $rs["approveby2"];
$sql = "SELECT no_po,pabrik FROM job_gelaran WHERE no_co='$no_co'";
$hsltemp = mysql_query($sql, $db);
list($no_po, $id_pabrik) = mysql_fetch_array($hsltemp);
if ($pabrik_tujuan_id) {
	$id_pabrik = $pabrik_tujuan_id;
}
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

	<table cellspacing="0" cellpadding="0">

		<tr height="20" class="alt">
			<td height="20" width="100">No PO</td>
			<td width="2">:</td>
			<td id="no_po"><?php echo $no_po; ?></td>
		</tr>
		<tr height="20">
			<td>No CO</td>
			<td>:</td>
			<td id="no_co"><?php echo $no_jo; ?></td>
		</tr>

		<tr height="20" class="alt">
			<td>No LOADING</td>
			<td>:</td>
			<td id="no_load"><?php echo $no_load; ?></td>
		</tr>
		<tr height="20">
			<td>Tanggal</td>
			<td>:</td>
			<td><?php echo $tanggal; ?></td>
		</tr>
		<tr height="20" class="alt">
			<td>Total Qty Produk</td>
			<td>:</td>
			<td><?php echo $totalqtyproduk; ?></td>
		</tr>
		<tr height="20">
			<td>Total Qty RM</td>
			<td>:</td>
			<td><?php echo $totalqty; ?></td>
		</tr>
		<tr height="20" class="alt">
			<td>Pabrik</td>
			<td>:</td>
			<td><?php if (!$approved2 && $approved) { ?>
					<select name="pabrik" id="pabrik_id" class="chosen-select">
						<option value="">-Pabrik-</option>
						<?php
						$sql = "SELECT id,nama FROM pabrik where status='1' ORDER BY nama";
						$hsltemp = mysql_query($sql, $db);
						while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
						?>
							<option value="<?php echo $id; ?>" <?php if ($id_pabrik == $id) {
																	echo "selected";
																} ?>> <?php echo "$nama [$id]"; ?> </option>
						<?php
						}
						?>
					</select>
				<?php } else {
					echo $pabrik;
				} ?>
			</td>
		</tr>
	</table>


	<table>
		<thead>
			<tr>
				<th height="30">No</th>
				<th>Kode Produk</th>
				<th>Nama Produk</th>
				<th>Warna Produk</th>
				<th>Qty</th>
				<th>Satuan</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$sql = "SELECT  `jld`.`kd_produk`  , `p`.`nama`   , `w`.`warna`  , `jld`.`qty_produk`
FROM
    `job_loading_detail` AS `jld`
    INNER JOIN  `produk` AS `p` 
        ON (`jld`.`kd_produk` = `p`.`kode`)
    INNER JOIN  `mst_warna` AS `w`
        ON (`p`.`kode_warna` = `w`.`kode`) WHERE jld.no_load='$no_load' order by jld.seqno";
			$query = mysql_query($sql) or die($sql);
			while (list($kd_produk, $nama, $warna, $qty) = mysql_fetch_array($query)) {
				$i++;
				if ($i % 2 == 1) {
					$kelas1 = "alt";
				} else {
					$kelas1 = "";
				}

			?>
				<tr class="<?php echo $kelas1 ?>">
					<td><?php echo $i ?></td>
					<td><?php echo $kd_produk ?></td>
					<td><?php echo $nama ?></td>
					<td><?php echo $warna ?></td>
					<td align="center" id="qty<?php echo $i ?>" ondblclick="ubah_qty('<?php echo $kd_produk ?>','<?php echo $i ?>','<?php echo $qty ?>')"><?php echo $qty; ?></td>
					<?php $tqty += $qty; ?>
					<td><?php echo "pcs" ?></td>
				</tr>
			<?php
			} ?>
		</tbody>
		<thead>
			<tr>
				<th height="30" colspan="4"> Jumlah </th>
				<th height="30" align="center"><?php echo number_format($tqty, "0", ".", ","); ?></th>
				<th></th>
			</tr>
		</thead>
		<!-- 
<tfoot><tr><td colspan="12"><div id="paging"><ul><li><a href="#"><span>Previous</span></a></li><li><a href="#" class="active"><span>1</span></a></li><li><a href="#"><span>2</span></a></li><li><a href="#"><span>3</span></a></li><li><a href="#"><span>4</span></a></li><li><a href="#"><span>5</span></a></li><li><a href="#"><span>Next</span></a></li></ul></div></tr></tfoot>
  -->
	</table>
</div>
<table width="100%">
	<tr>
		<td align="center">
			<?php
			if ($approved) {
				echo "<b>(APPROVED I BY $approveby)</b>";
				if ($approved2) {
					echo "<b>(APPROVED II BY $approveby2)</b>";
				} else {
			?> <input type="button" value="Approve II" onclick="if(confirm('Approving LOADING <?php echo $no_load; ?>?')){window.location='job_loading_approving2.php?no_load=<?php echo $no_load; ?>&pabrik='+document.getElementById('pabrik_id').value;}">
				<?php
				}
			} else {
				?> <input type="button" value="Approve I" onclick="if(confirm('Approving LOADING <?php echo $no_load; ?>?')){window.location='job_loading_approving.php?no_load=<?php echo $no_load; ?>';}">
			<?php
			}
			?>
			<?php
			if ($approved2) {
				$sql = "SELECT no_sew FROM job_sewing WHERE no_load='$no_load'";
				$hsltemp = mysql_query($sql, $db);
				if (mysql_affected_rows($db) > 0) {
			?><input type="button" value="SEWING" onclick="window.location='job_sewing_list.php?no_load=<?php echo $no_load; ?>';">
				<?php
				}
			}
			if ($approved) {
				?><input type="button" value="Print Mode" onclick="window.open('job_loading_print.php?no_load=<?php echo $no_load; ?>','job_loading_print','width=800,height=400,menubar=yes,scrollbars=yes');">
			<?php
			}
			?>
			<input type="button" value="Kembali" onclick="window.location='job_loading_listi.php';">
		</td>
	</tr>
</table>
</form>
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="app_libs/rekap_target_outlet_complete_v13.js"></script>
<script src="jquery.jeditable.js" type="text/javascript"></script>
<script language="JavaScript">
	var config = {
		'.chosen-select': {},
		'.chosen-select-deselect': {
			allow_single_deselect: true
		},
		'.chosen-select-no-single': {
			disable_search_threshold: 10
		},
		'.chosen-select-no-results': {
			no_results_text: 'Oops, nothing found!'
		},
		'.chosen-select-width': {
			width: "95%"
		}
	}
	for (var selector in config) {
		$(selector).chosen(config[selector]);
	}
</script>

<script>
	function ubah_qty(id_barang, no, isi) {

		$("#qty" + no).html("<input type='text' size='4' id='txtqty" + no + "' name='txtqty" + no + "' value='" + isi + "'><div class='kelas_departemen' onclick=ubah_proses('" + id_barang + "','" + no + "')><strong>Simpan</strong> </div>");
	}

	function ubah_proses(id_barang, no) {
		var no_load = $("#no_load").text();
		var qty = $("#txtqty" + no).val();
		var data = "id_barang=" + id_barang + "&no_load=" + no_load + "&qty=" + qty + "&proses=simpan";
		$.post("job_loading_detail_proses.php", data, function(response) {
			alert(response);
			if (response.trim() == 'berhasil') {
				$("#qty" + no).text(qty);
			}
		});
	}
</script>
<?php include_once "footer.php" ?>