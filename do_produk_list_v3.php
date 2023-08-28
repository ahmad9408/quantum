<?php
//$content_title="RECEIVING GUDANG DISTRIBUSI"; 
include_once "header.php"
?>
<?php
$no_fin = sanitasi($_GET["no_fin"]);

if ($_SESSION["outlettype"] == "P" || $_SESSION['id_group'] == "1") {
	$_manufaktur = $_SESSION["outlet"];
}

$array_bulan = array(
	'01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei',
	'06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
);

$tahun_skrg = date('Y');
$bulan_skrg = date('m');

if (isset($_GET['hal'])) {
	$bulan1 = $_SESSION['bulan1'];
	$tahun1 = $_SESSION['tahun1'];
	$kd_gudang = $_SESSION['kd_gudang'];
} elseif (isset($_REQUEST['search'])) {
	session_start();
	$_SESSION['bulan1'] = $_POST['bulan1'];
	$_SESSION['tahun1'] = $_POST['tahunl'];
	$_SESSION['kd_gudang'] = $_POST['kd_gudang'];
	$bulan1 = $_POST['bulan1'];
	$tahun1 = $_POST['tahunl'];
	$kd_gudang = $_POST['kd_gudang'];
	//echo "Kapilih";
} else {
	$bulan1 = $bulan_skrg;
	$tahun1 = $tahun_skrg;
	unset($_SESSION['bulan1']);
	unset($_SESSION['tahun1']);
	unset($_SESSION['kd_gudang']);
}


$filter_periode = $tahun1 . '-' . $bulan1;

//echo $filter_periode;
$today = date('d');
$now_tahun_bulan = date('Y-m');

?>
<?php

$typeoutlet = $_SESSION["outlettype"];
?>

<?php
if (isset($_GET['hal'])) $hal = $_GET['hal'];
else $hal = 0;
$jmlHal = 20;
$page = $hal;
//where no_do NOT LIKE 'P100S%'
$jenis_search = $_REQUEST['search'];
if ($jenis_search == 'm') {
	$sql = "select count(*) from do_produk";
} elseif ($jenis_search == 'p') {
	$sql = "select count(*) from do_produk where tanggal like '$filter_periode%' and gudang like '$kd_gudang%'";
} else {
	$sql = "select count(*) from do_produk";
}

$query = mysql_query($sql, $link);
$jmlData = mysql_fetch_row($query);
?>
<fieldset style="width:1%">
	<legend><b>Search Produk </b></legend>
	<table width="970" cellspacing="0" cellpadding=""="0">
		<tr>
			<td>
				<form action="?search=m" method="POST">
					<table width="871" border="0">
						<tr>
							<td width="115"><b>Search Model </b></td>
							<td width="3">:</td>
							<td width="680"><input type="text" id="search_model2" name="search_model" size="30" />
								Cari No Do/Tanggal
								<input type="text" id="search_model" name="master" size="30" />
								<input type="submit" value="Search" />
							</td>
							<td width="10">&nbsp;</td>
							<td width="9">&nbsp;</td>
							<td width="8">&nbsp;</td>
							<td width="20">&nbsp;</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</form>
			</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width="1430">
				<form action="?search=p" method="POST">

					<table width="701" border="0">
						<tr>
							<td width="82"><strong>Periode</strong></td>
							<td width="7"> :</td>
							<td width="536"><select name="bulan1">
									<?php
									foreach ($array_bulan as $key => $value) {
										if ($key == $bulan1) {
											echo  "<option value='$key' selected>$value</option>";
										} else {
											echo  "<option value='$key'>$value</option>";
										}
									}

									?>
								</select>
								<select name="tahunl">
									<?php
									$tahun = 1996;

									for ($i = 1; $i < 100; $i++) {
										$tahun++;
										if ($tahun == $tahun1) {
											echo  "<option value='$tahun' selected>$tahun</option>";
										} else {
											echo  "<option value='$tahun'>$tahun</option>";
										}
									}
									?>
								</select>
								<input type="submit" value="Search By Periode" />
							</td>
							<td width="10">&nbsp;</td>
							<td width="9">&nbsp;</td>
							<td width="40">&nbsp;</td>
						</tr>
						<tr>
							<td>Gudang</td>
							<td>:</td>
							<td><select name="kd_gudang">
									<?php
									$arrayGudang = array();
									$sql = "select trim(id),trim(nama) from gudang_distribusi where id not like '%S%'";
									$res = mysql_query($sql);
									while (list($key, $value) = mysql_fetch_array($res)) {
										$arrayGudang[$key] = $value;
									}

									$arrayGudang[' '] = '--ALL--';
									foreach ($arrayGudang as $key => $value) {
										if ($key == $kd_gudang) {
											echo  "<option value='$key' selected>$value</option>";
										} else {
											echo  "<option value='$key'>$value</option>";
										}
									}




									?>
								</select></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</form>
			</td>
			<td width="10">&nbsp;</td>

		</tr>
	</table>
</fieldset>
<table border="1" width="1184" style="font-size: 10pt" height="68" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8">
	<tr>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No</b></td>
		<td align="center" width="200" bgcolor="#99CC00" height="20"><b>No DO</b></td>
		<!-- td align="center" width="110" bgcolor="#99CC00" height="20"><b>Model</b></td -->
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Tanggal</b></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Pabrik</b></td>
		<td align="center" width="20" bgcolor="#99CC00"><strong>Tujuan</strong></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Keterangan</b></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Total Qty</b></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Jumlah (Rp)</b></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20" nowrap><b>Approve I</b></td>
		<td align="center" width="20" bgcolor="#99CC00" height="20" nowrap><b>Approve II</b></td>
		<td width="20" bgcolor="#99CC00" align="center"><strong>Tgl Approve II</strong></td>
		<td width="20" bgcolor="#99CC00" height="20" align="center"><b>Action</b></td>
	</tr>
	<?php
	// Edit By Goberan <-> 26-6-2010
	if ($_SESSION["id_group"] == "1" || $typeoutlet == "G") {
		echo "sesi Group or tipe outlet G";
		if ($jenis_search == 'm') {
			if (!empty($_POST[search_model])) {
				$sql1 = mysql_query("SELECT kode,kode_basic_item,kode_kategori,kode_kelas,kode_style FROM mst_model WHERE model LIKE '%$_POST[search_model]%'");
				list($kd_model, $kbi, $kkat, $kkel, $ksty) = mysql_fetch_array($sql1);
				$barcode = "$kbi$kkat$kkel$ksty$kd_model";
				//$sql="SELECT * FROM do_produk as dp inner join do_produk_detail as dpd on dp.no_do = dpd.no_do WHERE dpd.kd_produk LIKE '$barcode%' AND dp.no_do NOT LIKE '%smt%' and no_fin LIKE '%$no_fin' ORDER BY approve,tanggal"; 
				//do_produk.no_do NOT LIKE 'P100S%' and
				$sql = "SELECT do_produk.* FROM do_produk,do_produk_detail WHERE  do_produk.no_do NOT LIKE '%smt%' and  do_produk.no_fin LIKE '%$no_fin' and do_produk.no_do=do_produk_detail.no_do and do_produk_detail.kd_produk LIKE '$barcode%' group by do_produk.no_do   ORDER BY approve,tanggal DESC,no_do DESC limit " . ($page * $jmlHal) . "," . $jmlHal . "; --11";;
				/* DESC limit 
					
					".($page*$jmlHal).",".$jmlHal; */
				// echo "Barcode : $barcode & Model : $_POST[search_model]<br>";
				// echo $sql;
			} else {
				//no_do NOT LIKE 'P100S%' and
				if ($jenis_search == 'p') {
					$sql = "SELECT * FROM do_produk WHERE  no_do NOT LIKE '%smt%' and  tanggal like '$filter_periode%' and gudang like '$kd_gudang%'
							     and no_do not like 'BTL%' ORDER BY approve,tanggal DESC,no_do DESC 
										limit " . ($page * $jmlHal) . "," . $jmlHal . "; -- 1";;
				} else {
					$sql = "SELECT * FROM do_produk WHERE  no_do NOT LIKE '%smt%' and  no_fin LIKE '%$no_fin'   and (no_do like '$_POST[master]%' or tanggal like 								'$_POST[master]%') and gudang like '$kd_gudang%' and no_do not like 'BTL%' ORDER BY approve,tanggal DESC,no_do DESC limit " . ($page * $jmlHal) . "," . $jmlHal . "; -- 2";;
				}
			}
		} else {
			//no_do NOT LIKE 'P100S%' and 
			if ($jenis_search == 'p') {
				$sql = "SELECT * FROM do_produk WHERE  no_do NOT LIKE '%smt%' and  tanggal like '$filter_periode%' and gudang like '$kd_gudang%' 
							and no_do not like 'BTL%' ORDER BY approve,tanggal DESC,no_do DESC 
										limit " . ($page * $jmlHal) . "," . $jmlHal . "; -- 3";;
			} else {
				$sql = "SELECT * FROM do_produk WHERE  no_do NOT LIKE '%smt%' and  no_fin LIKE '%$no_fin'   and (no_do like '$_POST[master]%' or tanggal like 								'$_POST[master]%') and no_do not like 'BTL%' ORDER BY approve,tanggal DESC,no_do DESC limit " . ($page * $jmlHal) . "," . $jmlHal . "; -- 4";
			}
		}
	} else {
		if ($jenis_search == 'm') {
			if (!empty($_POST[search_model])) {
				$sql1 = mysql_query("SELECT kode,kode_basic_item,kode_kategori,kode_kelas,kode_style FROM mst_model WHERE model LIKE '%$_POST[search_model]%'"); //dp.no_do NOT LIKE 'P100S%' and
				list($kd_model, $kbi, $kkat, $kkel, $ksty) = mysql_fetch_array($sql1);
				$barcode = "$kbi$kkat$kkel$ksty$kd_model";
				$sql = "SELECT * FROM do_produk as dp inner join do_produk_detail as dpd on dp.no_do = dpd.no_do WHERE dpd.kd_produk LIKE '$barcode%' AND dp.no_do NOT LIKE '%smt%' and dp.no_do not like 'BTL%' and  no_fin LIKE '%$no_fin' AND pabrik='$_manufaktur' ORDER BY approve,tanggal "; /* DESC limit ".($page*$jmlHal).",".$jmlHal; */
			} else {
				echo "Input Kosong !!!";
				echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; url=do_produk_list_v3.php\">";
				return 0;
			}
		} elseif ($jenis_search == 'p') {
			$sql = "SELECT * FROM do_produk WHERE  no_do NOT LIKE '%smt%' and  tanggal like '$filter_periode%' and gudang like '$kd_gudang%'
							     and no_do not like 'BTL%' ORDER BY approve,tanggal DESC,no_do DESC 
										limit " . ($page * $jmlHal) . "," . $jmlHal . "; -- 1";;
		} else {

			// Edit tanggal 20 January 2012
			$sql = "SELECT no_do,tanggal, del_date,pabrik, gudang, outlet,  keterangan, totalqty, totalrp, approve, approveby,
  approvedate, approve2, approveby2, approvedate2  FROM do_produk WHERE  no_do NOT LIKE '%smt%' and no_do not like 'BTL%' and no_fin LIKE '%$no_fin' AND pabrik like '$_manufaktur%' ORDER BY approve,tanggal DESC,no_do DESC limit " . ($page * $jmlHal) . "," . $jmlHal;
		}
	}
	//echo "<!-- SQL $sql -->";
	$hsl = mysql_query($sql, $db);
	$no = ($hal * $jmlHal);
	while ($rs = mysql_fetch_array($hsl)) {
		$no++;

		$no_do = $rs["no_do"];

		$tanggal = $rs["tanggal"];
		$keterangan = $rs["keterangan"];
		$totalqty = $rs["totalqty"];
		$totalrp = $rs["totalrp"];
		$approve = $rs["approve"];
		$orang1 = $rs["approveby"];
		$orang2 = $rs["approveby2"];
		$tglapproved2 = $rs['approvedate2'];
		$kd_gudang = $rs['gudang'];
		/* Edit By Goberan 29-09-2010 13:13:13*/
		$sql = "SELECT nama FROM pabrik WHERE id='$keterangan'";
		$hsltemp = mysql_query($sql, $db);
		list($pabrik) = mysql_fetch_array($hsltemp);

		if ($approve == "1") {
			$status = "<b>$orang1</b>";
		} else {
			$status = "<blink><b><font color='red'>Belum Di Approve</font></b></blink>";
		}
		$approve2 = $rs["approve2"];
		if ($approve2 == "1") {
			$status2 = "<b>$orang2</b>";
		} else {
			$status2 = "<blink><b><font color='red'>Belum Di Approve</font></b></blink>";
		}
		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td align="center" width="20" height="20"><?php echo $no; ?></td>
			<td align="left" width="220" height="20">&nbsp;<?php echo $no_do; ?></td>
			<!-- td align="center" width="110" height="20">&nbsp;<?php // echo $style; 
																	?></td -->
			<td align="left" width="130" height="20">&nbsp;<?php echo $tanggal; ?></td>
			<td align="left" width="120" height="20">&nbsp;<?php echo $pabrik; ?></td>
			<td align="left" width="120"><?php echo $kd_gudang ?></td>
			<td align="center" width="20" height="20">&nbsp;<?php echo $keterangan; ?></td>
			<td align="right" width="90" height="20"><?php echo number_format($totalqty, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($totalrp, 2, ",", "."); ?></td>
			<td align="center" width="110" height="20"><?php echo $status; ?></td>
			<td align="center" width="110" height="20"><?php echo $status2; ?></td>
			<td nowrap align="center" width="120">&nbsp;<?php echo $tglapproved2; ?></td>
			<td nowrap align="center" width="120" height="20">&nbsp;
				<a href="do_produk_detail.php?no_do=<?php echo $no_do; ?>">Detil</a> |
				<a href="do_produk_add.php?firstload=1&no_doedit=<?php echo $no_do; ?>">Edit</a> |
				<?php if (($approve2) && ($_SESSION['id_group1'] == '23')) { ?><a href="do_produk_detail.php?no_do=<?php echo $no_do; ?>&retur">Retur</a> <?php  } ?>

				<?php $sql = "select no_do from retur_distribusi_rian where no_do='" . $no_do . "'";
				$resrian = mysql_query($sql) or die($sql);
				if (mysql_num_rows($resrian) > 0) {
				?><a href="">Lihat Retur</a><?php
												}
													?>
			</td>
		</tr>
	<?php
	}
	?>
</table>
<?php

?>
<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			Page :
			<span class="hal" onclick="location.href='do_produk_list_v3.php?x_idmenu=352&hal=0&search=<?php echo $jenis_search ?>';">First</span>
			<?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='do_produk_list_v3.php?x_idmenu=352&search=<?php echo $jenis_search ?>&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
					if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='do_produk_list_v3.php?x_idmenu=352&search=<?php echo $jenis_search ?>&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='do_produk_list_v3.php?x_idmenu=352&search=<?php echo $jenis_search ?>&hal=<?php echo $i; ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="location.href='do_produk_list_v3.php?x_idmenu=352&search=<?php echo $jenis_search ?>&hal=<?php echo intval(($jmlData[0] / $jmlHal)); ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?>
		</td>
	</tr>
</table>

<?php include_once "footer.php" ?>