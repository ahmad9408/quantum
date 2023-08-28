<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = "Form Koreksi Izin";
/*



*/


?>
<style>
	.flat {
		border-top-style: none;
		border-right-style: none;
		border-bottom-style: none;
		border-left-style: none;
		background-color: #FFFFFF;

	}
</style>
<style type="text/css">
	.myButton {
		-webkit-box-shadow: rgba(0, 0, 0, 0.2) 0 1px 0 0;
		-moz-box-shadow: rgba(0, 0, 0, 0.2) 0 1px 0 0;
		box-shadow: rgba(0, 0, 0, 0.2) 0 1px 0 0;
		border-bottom-color: #333;
		border: 1px solid #61c4ea;
		background-color: #7cceee;
		border-radius: 4px;
		-moz-border-radius: 4px;
		-webkit-border-radius: 4px;
		color: #333;
		font-family: 'Verdana', Arial, sans-serif;
		font-size: 10px;
		text-shadow: #b2e2f5 0 1px 0;
		padding: 2px
	}
</style>
<?php
$lihat = 1;
if ($lihat == 1) {
	include('header.php');
	include("css_group.php");
}
$thispage = $_SERVER['PHP_SELF'];

$tgl1 = $_POST['tgl1'];
$tgl2 = $_POST['tgl2'];
$today = date('Y-m-d');


function jumlahHari($month, $year)
{
	return date("j", strtotime('-1 second', strtotime('+1 month', strtotime($month . '/01/' . $year . ' 00:00:00'))));
}

function dateMysql($number)
{
	if ($number < 10) {
		return '0' . $number;
	} else {
		return $number;
	}
}


function createMonthRangeArray($strDateFrom, $strDateTo)
{


	$aryRange = array();

	$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2),     substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
	$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2),     substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

	if ($iDateTo >= $iDateFrom) {
		array_push($aryRange, date('Y-m-01', $iDateFrom)); //  entry
		$month_before = trim(date('Y-m-01', $iDateFrom));
		while ($iDateFrom < $iDateTo) {
			$iDateFrom += 86400; // add 24 hours
			if ($month_before != trim(date('Y-m-01', $iDateFrom))) {
				array_push($aryRange, date('Y-m-01', $iDateFrom));
				$month_before = trim(date('Y-m-01', $iDateFrom));
			}
		}
	}
	return $aryRange;
}

function intervalHari($strDateFrom, $strDateTo)
{
	$hasil = 1;
	//echo "($strDateFrom,$strDateTo)";


	$iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2),     substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
	$iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2),     substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

	if ($iDateTo >= $iDateFrom) {
		while ($iDateFrom < $iDateTo) {
			$hasil++;
			$iDateFrom += 86400; // add 24 hours

		}
	}
	return  $hasil;
}

$array_bulan = array(
	'01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei',
	'06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
);
$tahun_skrg = date('Y');
$bulan_skrg = date('m');

if (isset($_GET['hal'])) {
	$tgl1 = $_SESSION['tgl1'];
	$tgl2 = $_SESSION['tgl2'];
	$pabrik = $_SESSION['pabrik'];
} elseif (isset($_GET['action'])) {
	session_start();
	$tambah = "&action=search";
	$_SESSION['tgl1'] = $_POST['tgl1'];
	$_SESSION['tgl2'] = $_POST['tgl2'];
	$tgl1 = $_SESSION['tgl1'];
	$tgl2 = $_SESSION['tgl2'];
	$pabrik = $_POST['pabrik'];
	$_SESSION['pabrik'] = $pabrik;
} else {
	unset($_SESSION['tgl1']);
	unset($_SESSION['tgl2']);
	unset($_SESSION['pabrik']);
}


if (empty($tgl1)) {
	$tgl1 = date("Y-01-01");
	$h = jumlahHari(date('m'), date('Y'));
	$tgl2 = date("Y-m-") . $h;
}

$data_periode = split('-', $tgl1);
$filter_periode = $data_periode[0] . '-' . $data_periode[1];

?>

<!--Date Time Picker -->
<link type="text/css" href="css3/smoothness/ui.all.css" rel="stylesheet" />
<link type='text/css' href='css/demo.css' rel='stylesheet' media='screen' />
<!-- Contact Form CSS files -->
<link type='text/css' href='css/basic.css' rel='stylesheet' media='screen' />
<script type="text/javascript" src="jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="ui/ui.core.js"></script>
<script type="text/javascript" src="ui/ui.datepicker.js"></script>
<script type="text/javascript" src="ui/i18n/ui.datepicker-id.js"></script>

<script type="text/javascript">
	var jquery4 = $.noConflict(true);
	jquery4(document).ready(function() {
		jquery4(".tanggal").datepicker({
			dateFormat: "yy-mm-dd"
		});
	});
</script>

<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="sortable.js"></script>
<script language="JavaScript" src="jquery.timer.js"></script>


<!-- Auto complete -->
<!-- Jquery Autocomplete -->
<script type='text/javascript' src='jquery.autocomplete.js'></script>
<script type='text/javascript' src='app_libs/koreksi_finishing_izin.js'></script>
<script>
	$(document).ready(function() {
		$('#bulan1').change(function() {
			setTgl();

		})

		$('#tahun1').change(function() {
			setTgl();

		})


	})

	function daysInMonth(month, year) {
		var dd = new Date(year, month, 0);
		return dd.getDate();
	}

	function setTgl() {
		var tahun = $('#tahun1').val();
		var bulan = $('#bulan1').val();

		var jmlhHari = daysInMonth(Number(bulan), Number(tahun));
		$('#tgl1').val(tahun + '-' + bulan + '-01');
		$('#tgl2').val(tahun + '-' + bulan + '-' + jmlhHari);

		// alert(jmlhHari + ' untuk Bulan dan tahun ' + tahun + ' ' + bulan);
	}
</script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css">
<fieldset>
	<form method="POST" action="<?php echo $thispage; ?>?action=search" name="text">
		<!-- tengah -->
		<table class="table " width="50" style="font-size: 10pt" height="28">
			<tr>
				<td width="111" valign="top">Pabrik</td>
				<td width="3" valign="top">:</td>
				<td colspan="3">
					<select style="font-size: 8pt;width:250px;" class="form-control" name="pabrik" id="pabrik">
						<option value="">Pilih Wilayah Pabrik</option>
						<?php
						$sql = "SELECT $sql_cache id, nama from pabrik where status='1'";
						$hsltemp = mysql_query($sql, $db);
						while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
						?>
							<option value="<?php echo $id; ?>" <?php
																if ($pabrik == $id) {
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
				<td valign="top">Tgl. Koreksi Turun</td>
				<td valign="top">:</td>
				<td width="3">
					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'text',
							// input name
							'controlname': 'tgl1'
						});
					</script>
				</td>
				<td colspan=2>
					<input class="form-control" type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1; ?>" style="font-size: 8pt;width:100px;" size="10" />
				</td>
			</tr>
			<tr>
				<td valign="top">Sampai</td>
				<td valign="top">:</td>
				<td width="3">
					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'text',
							// input name
							'controlname': 'tgl2'
						});
					</script>
				</td>
				<td colspan=2>
					<input class="form-control" type="text" name="tgl2" id="tgl2" value="<?php echo $tgl2; ?>" style="font-size: 8pt;width:100px;" size="10" />
				</td>
			</tr>
			<tr>
				<td valign="top">&nbsp;</td>
				<td valign="top">&nbsp;</td>
				<td colspan=7>
					<button style="font-size: 8pt;width:100px;" class="btn btn-success btn-block" id="submit" name="submit" type="submit" value="Search" width="100">Search</button>
				</td>
				<td valign="top">&nbsp;</td>
				<td valign="top">&nbsp;</td>
				<td>
					<butoon style="font-size: 8pt;width:100px;" class="btn btn-primary btn-block" type="button" id="add" value="Add" />Add</button>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<?php /*if (isset($_GET['action'])){include("progress_bar.php"); }*/ ?>

<table border="0" width="1165" style="font-size: 8pt" class="table_q table_q-striped table_q-hover sortable" id="myTable">
	<tr class="header_table_q">
		<td width="74" height="22" align="center" background="images/footer.gif"><strong>NO</strong></td>
		<td background="images/footer.gif" align="center" width="124" height="50"><span style="font-weight: bold">Tanggal</span></td>
		<td background="images/footer.gif" align="center" width="267" height="50">Pabrik</td>
		<td background="images/footer.gif" align="center" width="111" height="50">Jenis Koreksi</td>
		<td background="images/footer.gif" align="center" width="411" height="50"><span style="font-weight: bold">Keterangan</span></td>
		<td background="images/footer.gif" align="center" width="300" height="50"><strong>Tgl Approve</strong></td>
		<td background="images/footer.gif" align="center" width="150" height="50"><span style="font-weight: bold">Action</span></td>
	</tr>
	<?php
	if (isset($_GET['hal'])) $hal = $_GET['hal'];
	else $hal = 0;
	$jmlHal = 100;
	$page = $hal;
	if (isset($_GET['action'])) {

		// cari berdasarkan tanggal 

		$sql2 = "SELECT   SQL_CALC_FOUND_ROWS  k.id,date_format(k.tanggal,'%Y-%m-%d'),k.id_pabrik,a.nama,k.keterangan,k.update_by,k.update_date,tipe_so FROM koreksi_finishing_stok_izin k INNER JOIN 
					(SELECT id,nama FROM outlet WHERE  jenis IN (1,2) 
					UNION SELECT id,nama FROM pabrik ) AS a  ON a.id=k.id_pabrik  where k.tanggal between '$tgl1' and '$tgl2' 
					and (a.id like '%$pabrik%') and k.id not like 'BTL%' ORDER BY k.tanggal DESC LIMIT " . ($page * $jmlHal) . "," . $jmlHal;
		$sql3 = "SELECT FOUND_ROWS()";
	} else {
		$sql2 = "SELECT   SQL_CALC_FOUND_ROWS  k.id,date_format(k.tanggal,'%Y-%m-%d'),k.id_pabrik,a.nama,k.keterangan,k.update_by,k.update_date,tipe_so FROM koreksi_finishing_stok_izin k INNER JOIN 
					(SELECT id,nama FROM outlet WHERE  jenis IN (1,2) 
					UNION SELECT id,nama FROM pabrik ) AS a  ON a.id=k.id_pabrik  WHERE k.id not like 'BTL%' ORDER BY k.tanggal DESC LIMIT " . ($page * $jmlHal) . "," . $jmlHal;
		$sql3 = "SELECT FOUND_ROWS()";
	}


	if ($username == 'budi-it') {
		echo $sql2;
	}



	$hsltemp2 = mysql_query($sql2, $db); // or die ('<h1>Error #'.mysql_error()."#$sql2".'</h1>');
	$hsltmp12 = mysql_query($sql3, $db); // or die ($sql3);
	list($jmlData[0]) = mysql_fetch_array($hsltmp12);
	$no = ($hal * $jmlHal);
	$kd_before = '';
	$counter = 0;
	while (list($kd_izin, $tgl, $id_pabrik, $nama_gudang, $keterangan, $app_by, $app_date, $jenis_koreksi) = mysql_fetch_array($hsltemp2)) {
		$counter++;
		$no = $counter;
		$btnBatal = '<button style="font-size: 8pt;width:100px;" class="btn btn-danger btn-block" type="button" name="batal" value="Batal" id="btnbatal_' . $kd_izin . '" onclick="batal(\'' . $kd_izin . '\',\'' . $counter . '\',\'' . $tgl . '\',\'' . $id_pabrik . '\');" />Batal</button>';



		if ($jenis_koreksi == '-') {
			$jenis_koreksi_v = 'Turun';
		} elseif ($jenis_koreksi == '+') {
			$jenis_koreksi_v = 'Naik';
		} else {
			$jenis_koreksi_v = 'All';
		}

		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;


	?>
		<tr <?php echo $blink; ?> onMouseOver="this.bgColor = '#CCCC00'" onmouseout="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>" id="data_<?php echo $counter; ?>">
			<td height="45" align="center" width="10"><?php echo $no; ?></td>
			<td height="45" align="center"><?php echo $tgl; ?></td>
			<td height="45"><?php echo $nama_gudang; ?></td>
			<td height="45" align="center"><?php echo $jenis_koreksi_v; ?></td>
			<td height="45"><?php echo $keterangan; ?></td>
			<td height="45" align="center" width="100"><?php echo $app_date . ' ' . $app_by; ?></td>
			<td width="150" height="50" align="center" id='kb_<?php echo $no; ?>'><?php echo $btnBatal; ?></td>

		</tr>
	<?php
	}

	?>
	<tfoot>
		<tr>
			<td height="25" background="images/yesupload.jpg">&nbsp;</td>
			<td background="images/yesupload.jpg">&nbsp;</td>
			<td background="images/yesupload.jpg">&nbsp;</td>
			<td background="images/yesupload.jpg">&nbsp;</td>
			<td background="images/yesupload.jpg">&nbsp;</td>
			<td background="images/yesupload.jpg">&nbsp;</td>
			<td background="images/yesupload.jpg">&nbsp;</td>
		</tr>
	</tfoot>
</table>

<table style="margin-left:10px; margin-top:10px;" id="page">
	<tr>
		<td class="text_standard">
			Page :
			<span class="hal" onClick="location.href='<?php echo $thispage; ?>?&hal=0<?php echo $tambah ?>';">First</span>
			<?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo $i; ?><?php echo $tambah ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
					if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo $i; ?><?php echo $tambah ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo $i; ?><?php echo $tambah ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo intval(($jmlData[0] / $jmlHal)); ?><?php echo $tambah ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo $jmlData[0]; ?>
		</td>
	</tr>
</table>
<script language="JavaScript">
	var no = <?php echo $counter ?>;
	var tgl1 = '<?php echo $tgl1 ?>';
	var tgl2 = '<?php echo $tgl2 ?>';
	var today = '<?php echo $today;   ?>';
	var username = '<?php echo $username ?>';
	<?php
	if (isset($_GET['action'])) {
	?>
		jmlProcess = 1;
		progressBar();




	<?php
	}



	//var myOptions = [{ text: 'Suganthar', value: 1}, {text : 'Suganthar2', value: 2}];

	?>
</script>
<span id="debug"></span>
<?php include_once "footer.php"; ?>