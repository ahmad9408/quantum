<?php $content_title = "DAFTAR QC";
include_once "header.php" ?>
<?php
$no_sew = sanitasi($_GET["no_sew"]);
$mapping = $_POST['mapping'];
$pabrik1 = $_POST['pabrik'];
$status = $_POST['status'];

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

if ($status == '1') {
	$terusan = " and approve2='1' ";
} else if ($status == '2') {
	$terusan = " and approve2 IS NULL ";
} else {
	$terusan = " ";
}

if ($mapping != "") {
	$mapping2 = "AND `jg`.`no_co_mapping` Like '%$mapping%'";
} else {
	$mapping2 = "";
}

if ($pabrik1 != "") {
	$pabrik2 = "AND `jg`.`pabrik` = '$pabrik1'";
} else {
	$pabrik2 = "";
}



$dari = $_POST['dari'];
$sampai = $_POST['sampai'];
$dari_default = date("Y-") . "01-01";
$sampai_default = date("Y-m-d");
if ($dari != "") {
	$filter_tanggal = " tanggal BETWEEN '$dari 00:00:00' AND '$sampai 23:59:59'";
}else{
	$filter_tanggal = " tanggal BETWEEN '$dari_default 00:00:00' AND '$sampai_default 23:59:59'";

}

?>

<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css">
<form name="text" method="post" action="job_qc_rekap.php" id="search">
	<table>
		<tr>
			<td>NO CO Mapping</td>
			<td>:</td>
			<td><input type="text" name="mapping" value="<?php echo $mapping ?>" size="30" /> </td>
		</tr>
		<tr>
			<td>Pabrik</td>
			<td>:</td>
			<td>
				<select name="pabrik" id="pabrik">
					<option value="">Pilih Wilayah Pabrik</option>
					<?php
					$sql = "SELECT id, nama from pabrik where mk<>'1' AND id $_pabrik AND status='1'";
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
		</tr>
		<tr>
			<td>Tanggal</td>
			<td>:</td>
			<td><input type="text" name="dari" id="dari" value="<?php echo $dari; ?>" size="10" />
				<script language="JavaScript">
					new tcal({
						// form name
						'formname': 'search',
						// input name
						'controlname': 'dari'
					});
				</script>
				&nbsp;
				Sampai <input type="text" name="sampai" id="sampai" value="<?php echo $sampai; ?>" size="10" />
				<script language="JavaScript">
					new tcal({
						// form name
						'formname': 'search',
						// input name
						'controlname': 'sampai'
					});
				</script>
			</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td colspan="5"><input type="submit" id="submit" value="Cari" /></td>
		</tr>
	</table>
</form>
<script language="javascript">
	function pindah_halaman(i) {
		$("#hal").val(i);
		$("#submit").click();
	}
</script>
<?php


// if($username=='B120938_ahmad'){
// 	echo $sql."<br/>";	
//  }

// 
?>


<table border="1" width="100%" style="font-size: 10pt" height="68" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8">
	<tr>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No</b></td>
		<td align="center" width="150" bgcolor="#99CC00" height="20"><b>No CO</b></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No CO Mapping</b></td>
		<td align="center" width="120" bgcolor="#99CC00" height="20"><b>Model</b></td>
		<td align="center" width="170" bgcolor="#99CC00" height="20"><b>Pabrik</b></td>
		<!-- <td align="center" width="50" bgcolor="#99CC00" height="20"><b>QTY</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Tanggal</b></td> -->
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Action</b></td>
	</tr>
	<?php

	if (isset($_POST['hal'])) $hal = $_POST['hal'];
	else $hal = 0;
	$jmlHal = 500;
	$page = $hal;

	$sql = "SELECT
	`jq`.`no_co` AS no_co,
	`jg`.`no_co_mapping` AS no_co_mapping,
	`jg`.`model` AS model,
	`jg`.`pabrik` AS pabrik,
	`jq`.`totalqty` AS qty,
	`jq`.`tanggal` AS tanggal
	FROM `quantum`.`job_qc` AS `jq`
	LEFT JOIN `quantum`.`job_qc_detail` AS `jqd` ON (`jq`.`no_co` = `jqd`.`no_qc`)
	LEFT JOIN `quantum`.`job_gelaran` AS `jg` ON (`jq`.`no_co` = `jg`.`no_co`)
	WHERE $filter_tanggal $pabrik2 AND `jg`.`pabrik` $_pabrik $mapping2
	GROUP BY `jq`.`no_co`
	ORDER BY `jg`.`pabrik` ASC limit " . ($page * $jmlHal) . "," . $jmlHal;

	$query = mysql_query($sql, $link);

	$jmlData[0] = mysql_num_rows($query);

	$hsl = mysql_query($sql, $db);

	$no = ($hal * $jmlHal);

	while ($rs = mysql_fetch_array($hsl)) {
		$no++;
		$no_co = $rs["no_co"];
		$no_co_mapping = $rs["no_co_mapping"];
		$model = $rs["model"];
		$pabrik_tujuan = $rs["pabrik"];

		if (!$pabrik_tujuan) {
			$pabrik_tujuan = $pabrik_dari;
		}
		$sql = "SELECT nama FROM pabrik WHERE id='$pabrik_tujuan'";
		$hsltemp = mysql_query($sql);
		list($pabrikname) = mysql_fetch_array($hsltemp);

		$qty = $rs["qty"];
		$tanggal = $rs["tanggal"];

		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td align="center" width="20" height="20"><?php echo $no; ?></td>
			<td align="left" width="200" height="20">&nbsp;<?php echo $no_co; ?></td>
			<td align="left" width="50" height="20">&nbsp;<?php echo $no_co_mapping; ?></td>
			<td align="left" width="250" height="20">&nbsp;<?php echo $model; ?></td>
			<td align="left" width="170" height="20">&nbsp;<?php echo $pabrikname . " [$pabrik_tujuan]"; ?></td>
			<!-- <td align="left" width="50" height="20">&nbsp;<?php echo $qty; ?></td>
                <td align="left" width="140" height="20">&nbsp;<?php echo $tanggal; ?></td> -->
			<td align="left" width="20" height="20" nowrap>&nbsp;
				<a href="job_qc_list_v2.php?no_co=<?php echo $no_co; ?>">Detil</a>
			</td>
		</tr>
	<?php
	}
	?>
</table>

<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			Page :
			<span class="hal" onclick="pindah_halaman('0')">First</span>
			<?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="pindah_halaman('<?php echo $i ?>')"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
					if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="pindah_halaman('<?php echo $i ?>')"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="pindah_halaman('<?php echo $i ?>')"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="pindah_halaman('<?php echo intval(($jmlData[0] / $jmlHal)) ?>')">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?> total halaman <?php echo ceil($jmlData[0] / $jmlHal); ?>
		</td>
	</tr>
</table>
<br /><br />
<?php include_once "footer.php" ?>