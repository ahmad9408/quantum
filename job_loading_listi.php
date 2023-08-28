<?php $content_title = "DAFTAR LOADING";
include_once "header.php" ?>
<?php
$no_co = sanitasi($_GET["no_co"]);
$cari = $_REQUEST['cari'];
$app = $_REQUEST['approve'];
$awal = $_POST['awal'];
$akhir = $_POST['akhir'];
$awal_default = date('Y') . "-01-01";
$akhir_default = date('Y-m-d');

if ($app == 1) {
	$appcode = " and `job_loading`.`approve2`='1'";
} else if ($app == 0) {
	$appcode = " ";
} else if ($app == 2) {

	$appcode = " and `job_loading`.`approve2` IS NULL";
}

if ($awal != "") {
	$terusan_tanggal = " and 
		job_loading.tanggal between '$awal 00:00:00' and '$akhir 23:59:59'";
} else {
	$terusan_tanggal = "and 
		job_loading.tanggal between '$awal_default 00:00:00' and '$akhir_default 23:59:59'";
}


?>
<?php
if (isset($_GET['hal'])) $hal = $_GET['hal'];
else $hal = 0;
$jmlHal = 500;
$page = $hal;

?><form name="f1" method="post" action="<?php echo $PHP_SELF ?>">
	<table border="0">
		<tr>
			<td>
				<strong>Pencarian : <input type="text" name="cari" size="30" value="<?php echo $cari ?>" />
				</strong>
			</td>
			<td>
				Dari : <script language="JavaScript" src="calendar_us.js"></script>
				<link rel="stylesheet" href="calendar.css">
				<!-- calendar attaches to existing form element -->
				<input type="text" name="awal" readonly id="awal" value="<?php echo $_REQUEST['awal']; ?>" size="16" /> &nbsp;

				<script language="JavaScript">
					new tcal({
						// form name
						'formname': 'f1',
						// input name
						'controlname': 'awal'
					});
				</script>
				&nbsp;

				<script language="JavaScript">
					new tcal({
						// form name
						'formname': 'f1',
						// input name
						'controlname': 'akhir'
					});
				</script><input type="text" name="akhir" readonly id="akhir" value="<?php echo $_REQUEST['akhir']; ?>" size="16" />
			</td>
			<tD><select name="approve">
					<option value="0" <?php if ($app == "0") {
											echo "selected";
										} ?>>Semua Kondisi</option>
					<option value="1" <?php if ($app == "1") {
											echo "selected";
										} ?>>Sudah Approve2 </option>
					<option value="2" <?php if ($app == "2") {
											echo "selected";
										} ?>>Belum Approve2</option>
				</select><input type="submit" value="Cari" /></tD>
		</tr>
		<tr>
			<td colspan="3"><sub><strong>ket*)</strong>Pencarian bisa berdasarkan no co, no loading dan model</sub></td>
		</tr>
	</table>
</form>

<table border="1" width="100%" style="font-size: 10pt" height="68" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8">

	<tr>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No</b></td>
		<td align="center" width="150" bgcolor="#99CC00" height="20"><b>No CO</b></td>
		<td align="center" width="100" bgcolor="#99CC00" height="20"><b>No CO Mapping</b></td>
		<td align="center" width="180" bgcolor="#99CC00" height="20"><b>No LOADING</b></td>
		<!-- td align="center" width="180" bgcolor="#99CC00" height="20"><b>Pabrik Asal</b></td -->
		<td align="center" width="130" bgcolor="#99CC00" height="20"><b>Pabrik Tujuan</b></td>
		<td align="center" width="150" bgcolor="#99CC00" height="20"><b>Model</b></td>
		<td align="center" width="150" bgcolor="#99CC00" height="20"><b>Tanggal</b></td>
		<td align="center" width="80" bgcolor="#99CC00" height="20"><b>Qty Produk</b></td>
		<td align="center" width="80" bgcolor="#99CC00" height="20"><b>Qty Material</b></td>
		<td align="center" width="80" bgcolor="#99CC00" height="20" nowrap><b>Approve I</b></td>
		<td align="center" width="80" bgcolor="#99CC00" height="20" nowrap><b>Approve II</b></td>
		<td width="90" bgcolor="#99CC00" height="20" align="center"><b>Action</b></td>
	</tr>
	<?php
	if ($_REQUEST['awal'] == "") {
		$code_date = "";
	} else {
		$code_date = " and `job_loading`.`tanggal` between '$_REQUEST[awal] 00:00:00' and '$_REQUEST[akhir] 23:59:59' ";
	}
	$_pabrik = "";
	$terusan_pabrik = " AND  (pabrik_dari LIKE '$_pabrik%' OR pabrik_tujuan LIKE '$_pabrik%' ) ";
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
			$pabrik_in = "(" . $pabrik . ")";
			$terusan_pabrik = " and   (pabrik_dari in $pabrik_in OR pabrik_tujuan in $pabrik_in ) ";
		} else {
			$terusan_pabrik = " AND  (pabrik_dari LIKE '$_pabrik%' OR pabrik_tujuan LIKE '$_pabrik%' ) ";
		}
	}
	$no_co = sanitasi($_GET["no_co"]);
	$sql = "SELECT job_loading.* FROM  job_loading 
			WHERE no_co LIKE '%$no_co%'
			$terusan_pabrik  $terusan_tanggal $appcode 
			and no_load not like 'BTL%' ORDER BY approve,tanggal DESC,no_load ";

	if ($username == 'b120938_ahmad') {
		echo "<h3>$sql</h3>";
	}

	if ($username == 'budi-it') {
		echo "<h3>$sql</h3>";
	}
	$query = mysql_query($sql, $link);
	$jmlData = mysql_num_rows($query);
	$sql = $sql . " limit " . ($page * $jmlHal) . "," . $jmlHal;
	$hsl = mysql_query($sql, $db);
	$no = ($hal * $jmlHal);
	while ($rs = mysql_fetch_array($hsl)) {


		$no++;
		$no_load = $rs["no_load"];
		$no_co = $rs["no_co"];
		$pabrik_dari = $rs["pabrik_dari"];
		$sql = "SELECT nama FROM pabrik WHERE id='$pabrik_dari'";
		$hsltemp = mysql_query($sql, $db);
		list($pabrik_dari_name) = mysql_fetch_array($hsltemp);
		$pabrik_tujuan = $rs["pabrik_tujuan"];
		if (!$pabrik_tujuan) {
			$pabrik_tujuan = $pabrik_dari;
		}

		$sql = "SELECT nama FROM pabrik WHERE id='$pabrik_tujuan'";
		$hsltemp = mysql_query($sql, $db);
		list($pabrik_tujuan_name) = mysql_fetch_array($hsltemp);

		$sql = "SELECT no_co_mapping FROM job_gelaran WHERE no_co='$no_co' AND no_co_mapping like '%$cari%'";
		$hsltemp = mysql_query($sql, $db);
		list($no_co_mapping) = mysql_fetch_array($hsltemp);


		$sql = "SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
		$hsltemp = mysql_query($sql, $db);
		list($no_jo, $no_po) = mysql_fetch_array($hsltemp);
		$tanggal = $rs["tanggal"];
		$totalqtyproduk = $rs["totalqtyproduk"];
		$totalqty = $rs["totalqty"];
		$jumlah = $rs["totalrp"];
		$approved = $rs["approve"];
		$style = $rs['nama'];
		if ($style == "") {
			$sql = "SELECT kd_produk FROM job_cutting_detail WHERE no_co='$no_co'";
			$hsltemp = mysql_query($sql, $db);
			list($kd_produk) = mysql_fetch_array($hsltemp);
			$sql = "SELECT * FROM produk WHERE kode = '$kd_produk'";
			$hsltemp = mysql_query($sql, $db);
			$rsa = mysql_fetch_array($hsltemp);
			$kode = $rsa["kode"];
			$kode_basic_item = $rsa["kode_basic_item"];
			$kode_kategori = $rsa["kode_kategori"];
			$kode_kelas = $rsa["kode_kelas"];
			$kode_style = $rsa["kode_style"];
			$kode_warna = $rsa["kode_warna"];
			$kode_model = $rsa["kode_model"];
			$sql = "SELECT model FROM mst_model WHERE kode='$kode_model' AND kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
			$hsltemp = mysql_query($sql, $db);
			list($style) = mysql_fetch_array($hsltemp);
			$sql = "update job_loading set  nama='$style' where no_co='$no_co' and no_load='$no_load' and pabrik_dari='$pabrik_dari'";
			$hsltemp = mysql_query($sql, $db);
		}
		/* RM Terpakai */
		$sql = "SELECT sum(rm_terpakai) FROM job_cutting_rm_terpakai WHERE no_co='$no_co'";
		$hsltemp = mysql_query($sql, $db);
		list($qtyrm) = mysql_fetch_array($hsltemp);
		if ($approved) {

			$status = "<strong>App[<font color='#0099FF'>" . $rs[approveby] . "</font>]</strong>";
		} else {
			$sql = "select approve,approveby,approvedate from job_cutting where no_co='$no_co'";
			$resi = mysql_query($sql) or die($sql);
			list($apr, $aprb, $aprd) = mysql_fetch_array($resi);
			$sql = "update job_loading set approve='1',approveby='$aprb',approvedate='$aprd' where no_co='$no_co'";
			$resi = mysql_query($sql) or die($sql);
			$status = "<strong>App[<font color='#0099FF'>" . $aprb . "</font>]</strong>";
			//$status="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
		}

		$approved2 = $rs["approve2"];
		if ($approved2) {
			$status2 = "<strong>App[<font color='#0099FF'>" . $rs[approveby2] . "</font>]</strong>";
		} else {
			$status2 = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
		}
		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td align="center" width="20" height="20"><?php echo $no; ?></td>
			<td align="left" width="150" height="20">&nbsp;<?php echo $no_co; ?></td>
			<td align="center" width="100" height="20">&nbsp;<?php echo $no_co_mapping; ?></td>
			<td align="left" width="180" height="20">&nbsp;<?php echo $no_load; ?></td>
			<!-- td align="center" width="180" height="20"><?php // echo $pabrik_dari_name." [$pabrik_dari]"; 
															?></td -->
			<td align="left" width="130" height="20">&nbsp;<?php echo $pabrik_tujuan_name; //." [$pabrik_tujuan]"; 
															?></td>
			<td align="left" width="150" height="20">&nbsp;<?php echo $style; ?></td>
			<td align="center" width="150" height="20"><?php echo $tanggal; ?></td>
			<td align="center" width="43" height="20"><?php echo $totalqtyproduk; ?></td>
			<td align="center" width="50" height="20"><?php echo $qtyrm; ?></td>
			<td align="left" width="80" height="20">&nbsp;<?php echo $status; ?></td>
			<td align="left" width="80" height="20">&nbsp;<?php echo $status2; ?></td>
			<td align="left" width="90" height="20" nowrap>&nbsp;
				<a href="job_loading_detail.php?no_load=<?php echo $no_load; ?>&no_co=<?php echo $no_co; ?>" target="_blank">Detil</a>
				<?php
				if ($approved2) {
					$sql = "SELECT no_load FROM job_sewing WHERE no_load='$no_load'";
					if ($username == 'budi-it') {
						echo $sql . "<br/>";
					}
					$hsltemp = mysql_query($sql);
					if (mysql_affected_rows() > 0) {
				?>
						|
						<a href="job_sewing_list_v2.php?no_co=<?php echo $no_co; ?>">Sewing</a>
				<?php
					}
				}
				?>
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
			<span class="hal" onclick="location.href='job_loading_listi.php?x_idmenu=150&hal=0';">First</span>
			<?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='job_loading_listi.php?x_idmenu=150&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData / $jmlHal)) {
					if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='job_loading_listi.php?x_idmenu=150&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='job_loading_listi.php?x_idmenu=150&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="location.href='job_loading_listi.php?x_idmenu=150&hal=<?php echo intval(($jmlData / $jmlHal)); ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?> from <?php echo $jmlData; ?> Data
		</td>
	</tr>
</table>
<br /><br />
<?php include_once "footer.php" ?>