<?php $content_title = "DAFTAR Real Cutting";
include_once "header.php" ?>
<?php
$no_po = sanitasi($_GET["no_po"]);
$cari = $_REQUEST['cari'];
$app = $_REQUEST[approve];
$awal = $_POST['awal'];
$akhir = $_POST['akhir'];

$sql_cache = ' SQL_CACHE ';

$awal_default = date('Y') . "-01-01";
$akhir_default = date('Y-m-d');
if ($app == 1) {
	$appcode = " and `job_cutting`.`approve2`='1'";
} else if ($app == 0) {
	$appcode = " ";
} else if ($app == 2) {

	$appcode = " and (`job_cutting`.`approve2` IS NULL or job_cutting.approve2='0')";
}

if ($awal != "") {
	$terusan_tanggal = " and 
		job_cutting.tanggal between '$awal 00:00:00' and '$akhir 23:59:59'";
} else {
	$terusan_tanggal = "and 
		job_cutting.tanggal between '$awal_default 00:00:00' and '$akhir_default 23:59:59'";
}
?>
<script language="JavaScript">
	var detailsWindow;

	function showManufaktur(textid) {
		detailsWindow = window.open("window_co.php?textid=" + textid, "window_co", "width=800,height=600,scrollbars=yes");
		detailsWindow.focus();
	}
</script>
<fieldset style="width=1%">
	<legend><b>Tambah CO</b></legend>
	<form action="?search=yes" method="POST" id="gelaran">
		<table>
			<tr>
				<td><br /><b>No CO</b></td>
				<td><b>:</b></td>
				<td><input type="text" id="no_co" name="no_co" onclick="showManufaktur(this.id);" size="30"></td>
				<td><input type="button" value="Input Real Cutting" onclick="window.location='job_cutting_add_v3.php?no_co='+document.getElementById('no_co').value;"></td>
				<td><b>Pencarian </b></td>
				<td><b>:</b></td>

				<td><input type="text" id="cari" name="cari" size="30" value="<?php echo $cari ?>">
					&nbsp; Dari : <script language="JavaScript" src="calendar_us.js"></script>
					<link rel="stylesheet" href="calendar.css">
					<!-- calendar attaches to existing form element -->
					<input type="text" name="awal" id="awal" value="<?php echo $awal; ?>" size="16" />
					&nbsp;

					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'gelaran',
							// input name
							'controlname': 'awal'
						});
					</script>
					Sampai
					<input type="text" name="akhir" id="akhir" value="<?php echo $akhir; ?>" size="16" /> &nbsp;

					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'gelaran',
							// input name
							'controlname': 'akhir'
						});
					</script>
				</td>

				<td><select name="approve">
						<option value="0" <?php if ($app == "0") {
												echo "selected";
											} ?>>Semua Kondisi</option>
						<option value="1" <?php if ($app == "1") {
												echo "selected";
											} ?>>Sudah Approve2 </option>
						<option value="2" <?php if ($app == "2") {
												echo "selected";
											} ?>>Belum Approve2</option>
					</select> </td>
				<td><input type="submit" value="Search"></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td colspan="5"><sub>ket*)pencarian bisa berdasarkan no co dan model </sub></td>
			</tr>
		</table>
	</form>
</fieldset>

<?php

$_pabrik = "";
$gelaran_pabrik = " AND job_gelaran.pabrik LIKE '%' ";
if (strtoupper($_SESSION['outlettype']) == "P") {
	if ($_SESSION['outlet'] == 'P0006') {
		$gelaran_pabrik = " AND pabrik.mk='1' ";
	} else {
		$gelaran_pabrik = " AND job_gelaran.pabrik LIKE '$_SESSION[outlet]%' ";
	}
}
if (isset($_GET['hal'])) $hal = $_GET['hal'];
else $hal = 0;
$jmlHal = 100;
$page = $hal;
$sql = "SELECT $sql_cache
    `job_cutting`.`no_co` as no_co
    , `pabrik`.`nama` as pabrik
    , `job_gelaran`.`model` as model
    , `job_cutting`.`totalqty` as totalqty 
    , `job_cutting`.`tanggal` as tanggal
    , `job_cutting`.`approve` as approve
    , `job_cutting`.`approve2` as approve2
    , `job_cutting`.`approveby` as approveby
    , `job_cutting`.`approveby2` as approveby2
	, `job_gelaran`.`pabrik` as kode_pabrik
	,	job_gelaran.no_co_mapping as mapping 	
FROM
    `job_cutting`
    INNER JOIN `job_gelaran` 
        ON (`job_cutting`.`no_co` = `job_gelaran`.`no_co`)
    INNER JOIN `pabrik` 
        ON (`job_gelaran`.`pabrik` = `pabrik`.`id`) WHERE realcutting='1' $gelaran_pabrik
		and (`job_cutting`.`no_co` like '%$cari%' or `pabrik`.`nama` like '%$cari%' or job_gelaran.model like '%$cari%' or job_gelaran.no_co_mapping like '%$cari%') $appcode  $terusan_tanggal
        ORDER BY job_cutting.tanggal DESC ";



if ($username == 'budi-it' || $username == 'iwan-it') {
	echo 'SQL: ' . $sql;
}

$query = mysql_query($sql);
$jmlData[0] = mysql_num_rows($query);
?>
<table border="1" width="100%" height="68" cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
	<thead>
		<tr class="header_table_q">
			<td align="center" width="43" height="30"><b>No</b></td>
			<td align="center" width="150" height="20"><b>No CO</b></td>
			<td align="center" width="150" height="20"><b>Mapping</b></td>
			<td align="center" width="180" height="20"><b>Pabrik</b></td>
			<td align="center" width="180" height="20"><b>Model</b></td>
			<td align="center" width="50" height="20"><b>CO</b></td>
			<td align="center" width="50" height="20"><b>RC</b></td>
			<td align="center" width="150" height="20"><b>Tanggal</b></td>
			<td align="center" width="170" height="20"><b>Approve I</b></td>
			<td align="center" width="170" height="20"><b>Approve II</b></td>
			<td align="center" height="20"><b>Action</b></td>
		</tr>
	</thead>
	<?php
	$_pabrik = "";
	if (strtoupper($_SESSION['outlettype']) == "P") {
		$_pabrik = $_SESSION['outlet'];
	}
	$kurus = false;

	$sql .= " limit " . ($page * $jmlHal) . "," . $jmlHal;

	$hsl = mysql_query($sql); //or die($sql);
	$no = ($hal * $jmlHal);
	while ($rs = mysql_fetch_array($hsl)) {
		$no++;
		$no_co = $rs["no_co"];
		$no_po = $rs["no_po"];
		$tanggal = $rs["tanggal"];
		$totalqty = $rs["totalqty"];
		$jumlah = $rs["totalrp"];
		$approved = $rs["approve"];

		$approved2	= $rs["approve2"];
		$approveby	= $rs["approveby"];
		$approveby2	= $rs["approveby2"];
		$pabrikname	= $rs["pabrik"];
		$style		= $rs["model"];
		$pabrik		= $rs["kode_pabrik"];
		$mapping	= $rs["mapping"];

		$sql = "SELECT $sql_cache SUM(qty_produk) FROM job_gelaran_detail WHERE no_co='$no_co'";
		$res = mysql_query($sql);
		list($co) = mysql_fetch_array($res);

		if ($approved) {
			$status = "<b>Approved [<font color='#0099FF'>$approveby</font>]</b>";
		} else {
			$status = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
		}
		if ($approved2) {
			$status2 = "<b>Approved [<font color='#0099FF'>$approveby2</font>]</b>";
		} else {
			$status2 = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
		}
		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr>
			<td align="center" width="43" height="20"><?php echo $no; ?></td>
			<td align="left" width="150" height="20">&nbsp;<?php echo $no_co; ?></td>
			<td align="center" width="150" height="20">&nbsp;<?php echo $mapping; ?></td>
			<td align="left" width="180" height="20">&nbsp;<?php echo $pabrikname . " [$pabrik]"; ?></td>
			<td align="left" width="180" height="20">&nbsp;<?php echo $style; ?></td>
			<td align="center" width="50" height="20">&nbsp;<?php echo $co;
															$ttotalco = $ttotalco + $co; ?></td>
			<td align="center" width="50" height="20">&nbsp;<?php echo $totalqty;
															$ttotalqty = $ttotalqty + $totalqty;  ?></td>
			<td align="center" width="153" height="20"><?php echo $tanggal; ?></td>
			<td align="left" width="170" height="20">&nbsp;<?php echo $status; ?></td>
			<td align="left" width="170" height="20">&nbsp;<?php echo $status2; ?></td>
			<td>&nbsp;<a href="job_cutting_detail.php?no_co=<?php echo $no_co; ?>">Detil<a>
						<?php
						if ($approved2) {
							$sql = "SELECT no_co FROM job_loading WHERE no_co='$no_co'";
							$hsltemp = mysql_query($sql, $db);
							if (mysql_affected_rows($db) > 0) {
						?>
								|
								<a href="job_loading_listi.php?no_co=<?php echo $no_co; ?>">Loading</a>
						<?php
							}
						}
						?> </td>
		</tr>
	<?php
	}
	?>
	<tr class="footer_table_q">
		<td colspan="5" height="20"><strong>
		<blink><font size="2" color='#0099FF' face="Verdana, Arial, Helvetica, sans-serif">Total</font><blink>
			</strong></td>
		<td align="center"><strong><blink><font color='#0099FF'><?php echo number_format($ttotalco, "0", ".", ","); ?></blink></font></strong></td>
		<td align="center"><strong><blink><font color='#0099FF'><?php echo number_format($ttotalqty, "0", ".", ","); ?></link></font></strong></td>
		<td colspan="4"></td>
	</tr>
</table>

<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			Page :
			<span class="hal" onclick="location.href='job_cutting_list.php?x_idmenu=229&hal=0';">First</span>
			<?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='job_cutting_list.php?x_idmenu=149&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
					if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='job_cutting_list.php?x_idmenu=149&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='job_cutting_list.php?x_idmenu=149&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="location.href='job_cutting_list.php?x_idmenu=229&hal=<?php echo intval(($jmlData[0] / $jmlHal)); ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?> from <?php echo $jmlData[0]; ?> Data
		</td>
	</tr>
</table>

<br /><br />

<br /><br />
<?php include_once "footer.php" ?>