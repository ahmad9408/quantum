<?php $content_title = "DAFTAR SEWING";
include_once "header.php";
include("css_group.php");
?>

<style>
	.mylink {
		cursor: pointer;
		color: #398439;
	}
</style>
<?php
$no_load = sanitasi($_GET["no_load"]);
// $mapping = $_POST['mapping'];
// $pabrik1 = $_POST['pabrik'];
// $line1 = $_POST['linepabrik'];

$mapping = $_REQUEST['mapping'];
$pabrik1 = $_REQUEST['pabrik'];
$line1 = $_REQUEST['linepabrik'];

// session_start();
// if (isset($_POST['tombolcari'])) {
// 	$mapping = $_POST['mapping'];
// 	$pabrik1 = $_POST['pabrik'];
// 	$line1 = $_POST['linepabrik'];
// 	$
// }

if (isset($_GET['hal'])) {
	session_start();
	$tgl1 = $_REQUEST['tgl1'];
	$tgl2 = $_REQUEST['tgl2'];
	$tgl1_default = date("Y-") . "01-01";
	$tgl2_default = date("Y-m-d");
	if ($tgl1 != "") {
		$filter_tanggal = " js.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
	} else {
		$filter_tanggal = " js.tanggal BETWEEN '$tgl1_default 00:00:00' AND '$tgl2_default 23:59:59'";
	}


	$no_load = $_SESSION["no_load"];
	$mapping = $_REQUEST['mapping'];
	$pabrik1 = $_REQUEST['pabrik'];
	$line1 = $_REQUEST['linepabrik'];
} elseif (isset($_GET['action'])) {
	session_start();
	$tgl1 = $_POST['tgl1'];
	$tgl2 = $_POST['tgl2'];
	$_SESSION['tgl1'] = $tgl1;
	$_SESSION['tgl2'] = $tgl2;
	$tgl1_default = date("Y-") . "01-01";
	$tgl2_default = date("Y-m-d");
	if ($tgl1 != "") {
		$filter_tanggal = " js.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
	} else {
		$filter_tanggal = " js.tanggal BETWEEN '$tgl1_default 00:00:00' AND '$tgl2_default 23:59:59'";
	}


	$tgl1 = sanitasi($_POST["tgl1"]);
	$tgl2 = sanitasi($_POST['tgl2']);
	$no_load = sanitasi($_POST["no_load"]);
	$mapping = sanitasi($_POST['mapping']);
	$pabrik1 = sanitasi($_POST['pabrik']);
	$line1 = sanitasi($_POST['linepabrik']);
	$_SESSION["no_load"] = $no_load;
	$_SESSION['mapping'] = $mapping;
	$_SESSION['pabrik'] = $pabrik1;
	$_SESSION['linepabrik'] = $line1;
} else {
	$tgl1 = $_POST['tgl1'];
	$tgl2 = $_POST['tgl2'];
	$tgl1_default = date("Y-") . "01-01";
	$tgl2_default = date("Y-m-d");
	if ($tgl1 != "") {
		$filter_tanggal = " js.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
	} else {
		$filter_tanggal = " js.tanggal BETWEEN '$tgl1_default 00:00:00' AND '$tgl2_default 23:59:59'";
	}


	// $tgl1=date('2017-01-01');//mulai stabil 2017
	// $tgl2=date('Y-m-d');
}

$sql_cache = ' SQL_CACHE ';

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

if ($app == 1) {
	$appcode = " and js.`approve2`='1'";
} else if ($app == 0) {
	$appcode = " ";
} else if ($app == 2) {
	$appcode = " and js.`approve2` IS NULL";
}



if ($mapping != "") {
	$mapping2 = "AND jg.no_co_mapping Like '%$mapping%'";
} else {
	$mapping2 = "";
}

if ($pabrik1 != "") {
	$pabrik2 = "AND jl.pabrik_tujuan = '$pabrik1'";
} else {
	$pabrik2 = $pabrik2;
}

if ($line1 != "") {
	$line2 =  "AND jsl.keterangan Like'%$line1%'";
} else {
	$line2 = $line2;
}

?>
<script src="app_libs/job_sewing_list_v2.js?d=<?php echo date('YmdHis'); ?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css">
<fieldset style="width=1%">

	<form name="text" method="post" action="job_sewing_rekap.php" id="job_sewing_rekap">
		<table class="table " width="50" style="font-size: 10pt" height="28">
			<tr>
				<td colspan="5" width="75">
					<h5><b>Pencarian Rekap Sewing: </b></h5>
				</td>
				<td colspan="3" width="75">
					<h5><b>Tambah Sewing Order :</b></h5>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td width="120">NO CO Mapping</td>
				<td width="6">:</td>
				<td width="295" colspan="2"><input style="width:250px" class="form-control" type="text" name="mapping" value="<?php echo $mapping ?>" size="30" /> </td>
				<td rowspan="6">&nbsp;</td>
				<td width="120"><b>No Loading</b></td>
				<td width="6"><b>:</b></td>
				<td width="295"><input style="width:250px" class="form-control" type="text" id="no_load" name="no_load" onclick="showJobLoading(this.id);" size="30" /></td>
			</tr>
			<tr>
				<td>Pabrik</td>
				<td>:</td>
				<td colspan="2">
					<select style="width:250px" class="form-control" name="pabrik" id="pabrik">
						<option value="">Pilih Wilayah Pabrik</option>
						<?php
						$sql = "SELECT $sql_cache id, nama from pabrik where mk<>'1' AND id $_pabrik AND status='1'";
						$hsltemp = mysql_query($sql, $db);
						while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
						?>
							<option value="<?php echo $id; ?>" <?php
																if ($pabrik1 == $id) {
																	echo "selected";
																} ?>>
								<?php
								echo "$id [$nama]";
								?>
							</option>
						<?php
						}
						?>
				</td>
				<td colspan="2"></td>
				<td width="20"><button style="width:250px" class="btn btn-success btn-block" type="button" value="Tambah SO" onclick="window.location='job_sewing_add.php?no_load='+document.getElementById('no_load').value;">Tambah SO</button></td>
			</tr>
			<tr>
				<td>Line</td>
				<td>:</td>
				<td colspan="2">
					<select style="width:250px" class="form-control" name="linepabrik" id="linepabrik">
						<option value="">Pilih Line Pabrik</option>
						<?php
						$sql = "SELECT $sql_cache id,keterangan FROM job_sewing_line WHERE status='1' AND id $_pabrik";
						$hsltemp = mysql_query($sql, $db);
						while (list($id_line, $keterangan) = mysql_fetch_array($hsltemp)) {
						?>
							<option value="<?php echo $keterangan; ?>" <?php
																		if ($line1 == $keterangan) {
																			echo "selected";
																		} ?>>
								<?php echo "$id_line [$keterangan]";
								?>
							</option>
						<?php
						}
						?>
				</td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>Tanggal Awal</td>
				<td>:</td>
				<td align="right" width="6">
					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'text',
							// input name
							'controlname': 'tgl1'
						});
					</script>
				</td>
				<td width="50"><input style="width:150px" class="form-control" type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1; ?>" size="10" />
				</td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>Tanggal Akhir</td>
				<td>:</td>
				<td align="right" width="6">
					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'text',
							// input name
							'controlname': 'tgl2'
						});
					</script>
				</td>
				<td width="50"><input style="width:150px" class="form-control" type="text" name="tgl2" id="tgl2" value="<?php echo $tgl2; ?>" size="10" />
				</td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
			<td colspan="2"><button class="btn btn-warning btn-block" type="submit" id="submit" value="Cari" name="tombolcari">Cari</button></td>
				<td colspan="5">&nbsp;</td>
			</tr>
		</table>
	</form>
</fieldset>
<?php

if (isset($_GET['hal'])) $hal = $_GET['hal'];
else $hal = 0;
$jmlHal = 100;
$page = $hal;

$id_pabrik = sanitasi($_GET["pabrik"]);

$sql = "SELECT $sql_cache
		js.no_co AS no_co, 
		js.no_load AS no_load, 
		jsl.keterangan AS no_line, 
		jl.nama AS model, 
		jl.pabrik_tujuan AS pabrik_tujuan, 
		js.totalqty AS qty, 
		js.tanggal AS tanggal,
		jg.no_co_mapping AS no_co_mapping
		FROM job_sewing AS js 
		INNER JOIN job_loading AS jl ON (js.no_load = jl.no_load) 
		LEFT JOIN job_sewing_line jsl ON (js.no_line = jsl.id)
		LEFT JOIN job_gelaran AS jg ON (jl.no_co = jg.no_co)
		WHERE $filter_tanggal
		$pabrik2 AND jl.pabrik_tujuan $_pabrik $mapping2 $line2
		and js.no_co not like '%btl%'
		GROUP BY js.no_co 
		ORDER BY js.tanggal DESC ";

if ($username == 'B120938_ahmad') {
	echo $sql . "<br/>";
}

$query = mysql_query($sql);
$jmlData[0] = mysql_num_rows($query);


if ($username == 'budi-it') {
	echo $sql . "<br/>";
}
?>

<table border="1" width="100%" cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
	<thead>
		<tr class="header_table_q">
			<td align="center" width="20" rowspan="2"><b>No</b></td>
			<td align="center" width="170" rowspan="2"><b>No CO</b></td>
			<td align="center" width="100" rowspan="2"><b>No CO Mapping</b></td>
			<td align="center" width="180" rowspan="2"><b>No Loading</b></td>
			<td align="center" width="190" rowspan="2"><b>Model</b></td>
			<td align="center" width="180" rowspan="2"><b>Pabrik</b></td>
			<td align="center" width="80" rowspan="2"><b>No Line</b></td>
			<td align="center" width="50" rowspan="2"><b>QTY Real Cutting</b></td>
			<td align="center" width="50" colspan="3"><b>Rekap Hasil Sewing</b></td>
			<td align="center" width="150" rowspan="2"><b>Status Sewing</b></td>
			<td align="center" width="70" rowspan="2"><b>Action</b></td>
		</tr>
		<tr class="header_table_q">
			<td align="center"><strong>Bagus</strong></td>
			<td align="center"><strong>Reject</strong></td>
			<td align="center"><strong>Pending</strong></td>
		</tr>
	</thead>

	<?php
	$sql .= " limit " . ($page * $jmlHal) . "," . $jmlHal;
	$hsl = mysql_query($sql);
	$no = ($hal * $jmlHal);
	while ($rs = mysql_fetch_array($hsl)) {
		$no++;
		// $no_sew=$rs["no_sew"];
		$no_load = $rs["no_load"];
		$style = $rs["model"];
		$no_co = $rs["no_co"];
		$no_co_mapping = $rs["no_co_mapping"];
		$no_line = $rs["no_line"];
		$pabrik_tujuan = $rs["pabrik_tujuan"];
		$pabrik_dari = $rs["pabrik_dari"];

		if (!$pabrik_tujuan) {
			$pabrik_tujuan = $pabrik_dari;
		}
		$sql = "SELECT nama FROM pabrik WHERE id='$pabrik_tujuan'";
		$hsltemp = mysql_query($sql);
		list($pabrikname) = mysql_fetch_array($hsltemp);

		$tanggal = $rs["tanggal"];
		$totalqty = $rs["qty"];
		$approved = $rs["approve"];

		if ($approved) {
			$status = "<b>App[<font color='#0099FF'>$rs[approveby]</font>]</b>";
		} else {
			$status = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
		}
		$approved2 = $rs["approve2"];
		if ($approved2) {
			$status2 = "<b>App[<font color='#0099FF'>$rs[approveby2]</font>]</b>";
		} else {
			$status2 = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
		}
		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;

		$sql = "SELECT $sql_cache
		SUM(`jsd`.`qty`-`jsd`.`reject`-`jsd`.`pending`) AS bagus,
		SUM(`jsd`.`reject`) AS reject,
		`js`.`no_co`,
		`js`.`totalqty`,
		`js`.`no_line`
		FROM
		`quantum`.`job_sewing` AS `js`
		LEFT JOIN `quantum`.`job_sewing_detail` AS `jsd`
		ON (`js`.`no_sew` = `jsd`.`no_sew`)
		WHERE js.`no_co`='$no_co' AND js.`approve2`='1'
		GROUP BY `js`.`no_co`";

		if ($_SESSION['username'] == "rian-it") {
			//echo $sql;
		}
		$res = mysql_query($sql) or die($sql);
		list(
			$qtybagus,
			$reject,
			$coco,
			$totreal_cutting,
			$no_line2
		) = mysql_fetch_array($res);

		if ($qtybagus == "") {
			$qtybagus = "0";
		}

		if ($reject == "") {
			$reject = "0";
		}

		
		if ($no_line2 == "") {
			$no_line2 = "-";
		}

	?>
		<tr>
			<td align="center" width="20"><?php echo $no; ?></td>
			<td align="left" width="170">&nbsp;<?php echo $no_co; ?></td>
			<td align="center" width="100">&nbsp;<?php echo $no_co_mapping; ?></td>
			<td align="left" width="180">&nbsp;<?php echo $no_load; ?></td>
			<td align="left" width="190">&nbsp;<?php echo $style; ?></td>
			<td align="left" width="180">&nbsp;<?php echo $pabrikname . " [$pabrik_tujuan]"; ?></td>
			<td align="left" width="80">&nbsp;<?php echo $no_line2; ?></td>
			<td align="center" width="70">&nbsp;<?php echo number_format($totreal_cutting, "0", ".", ","); ?></td>
			<?php
			if ($qtybagus == "") {
				$qtybagus = "0";
			}
			?>
			<td align="center" width="50">&nbsp;<b><?php echo number_format($qtybagus, "0", ".", ",");
													$totbagus = $totbagus + $qtybagus; ?></b></td>
			<td align="center" width="50">&nbsp;<b><?php echo number_format($reject, "0", ".", ",");
													$totreject = $totreject + $reject; ?></b></td>
			<td align="center" width="50">&nbsp;<b><?php echo number_format($pending = $totreal_cutting - $qtybagus - $reject, "0", ".", ",");
													$totpending = $totpending + $pending; ?></b></td>
			<?php
			if ($pending !="0" || $qtybagus =="0") {
				$keterangan = "<blink><font color='red'><b>On Progress</b></font></blink>";
			} else {
				$keterangan = "<b><font color='#0099FF'>Selesai</b>";
			}
			?>
			<td align="center" width="150">&nbsp;<b><?php echo $keterangan; ?></b></td>
			<td align="center" width="70" nowrap>&nbsp;
				<a href="job_sewing_list_v2.php?no_co=<?php echo $no_co; ?>">
					<center>Detail</center>
				</a>
			</td>
		</tr>
	<?php
	}
	?>
	<thead>
		<tr class="footer_table_q">
			<td colspan="8" height="40" align="center"><strong>
					<blink>
						<font align="center" size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font>
						<blink>
				</strong></td>
			<td align="center"><strong>
					<blink>
						<font><?php echo number_format($totbagus, "0", ".", ","); ?>
					</blink>
					</font>
				</strong></td>
			<td align="center"><strong>
					<blink>
						<font><?php echo number_format($totreject, "0", ".", ","); ?></link>
						</font>
				</strong></td>
			<td align="center"><strong>
					<blink>
						<font><?php echo number_format($totpending, "0", ".", ","); ?></link>
						</font>
				</strong></td>
			<td colspan="2"></td>
		</tr>
	</thead>
</table>
<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			<?php
			$terusan = "&mapping=$mapping&pabrik=$pabrik1&line=$line1&tgl1=$tgl1&tgl2=$tgl2";
			if ($username == 'B120938_ahmad') {
				echo $terusan;
			}
			?>
			Halaman :
			<span class="hal" onclick="location.href='job_sewing_rekap.php?x_idmenu=229&hal=0><?php echo $terusan ?>';">First</span>
			<?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='job_sewing_rekap.php?x_idmenu=149&hal=<?php echo $i; ?>><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
					if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='job_sewing_rekap.php?x_idmenu=149&hal=<?php echo $i; ?>><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='job_sewing_rekap.php?x_idmenu=149&hal=<?php echo $i; ?>><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="location.href='job_sewing_rekap.php?x_idmenu=229&hal=<?php echo intval(($jmlData[0] / $jmlHal)); ?>><?php echo $terusan ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?> from <?php echo $jmlData[0]; ?> Data
		</td>
	</tr>
</table>
<br /><br />

<?php include_once "footer.php" ?>