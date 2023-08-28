<?php

// Upgrade by.Ahmad_Agustiawan - 14/03/2023

session_start();
$content_title = "Daftar DO Produksi";
$base = array('tanggal' => 'Pengiriman', 'approvedate2' => 'Approve2');

include_once "header.php";
include("css_group.php");
?>
<?php
$no_fin = sanitasi($_GET["no_fin"]);

if ($_SESSION["outlettype"] == "P" || $_SESSION['id_group'] == "1") {
	$_manufaktur = $_SESSION["outlet"];
}
$jmlh = $_POST['jmlh'];
if ($jmlh == "") {
	$jmlh = "100";
}

?>
<?php

$typeoutlet = $_SESSION["outlettype"];

if (!empty($_REQUEST['hal'])) {
	$gudang_filter = $_SESSION['gudang_filter'];
	$txt_kode = $_SESSION['txt_kode'];
	$search_model = $_SESSION['search_model'];
	$pabrik = $_SESSION['pabrik'];
	$jenis_pabrik = $_SESSION['jenis_pabrik'];
} elseif (isset($_REQUEST['search'])) {
	$gudang_filter = trim($_POST['gudang_filter']);
	$_SESSION['gudang_filter'] = $gudang_filter;
	$txt_kode = trim($_POST['txt_kode']);
	$_SESSION['txt_kode'] = $txt_kode;
	$search_model = trim($_POST['search_model']);
	$_SESSION['search_model'] = $search_model;
	$pabrik = trim($_POST['pabrik']);
	$_SESSION['pabrik'] = $pabrik;
	$jenis_pabrik = trim($_POST['jenis_pabrik']);
	$_SESSION['jenis_pabrik'] = $jenis_pabrik;
} else {
	//;
}

if ($username == 'budi-it') {
	echo "<h2> TEKS $gudang_filter</h2>";
	print_r($_POST);
}

$tgl1 = $_REQUEST['tgl1'];
$tgl2 = $_REQUEST['tgl2'];


if (empty($tgl1)) {
	$tgl1 = date('Y-m-d');
	$tgl2 = date('Y-m-d');
}

$berdasarkan = $_POST['berdasarkan'];

if (empty($berdasarkan)) {
	$berdasarkan = 'tanggal';
}
$addcode = "and do_produk.$berdasarkan between '$tgl1 00:00:00' and '$tgl2 23:59:59'";
$addpage = "&tgl1=$tgl1&tgl2=$tgl2";
#}
?>

<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css">
<fieldset style="width=1%">
	<form action="?search=yes&rnd=<?php echo date('YmdHis'); ?>" method="POST" name="f1">
		<table class="table " width="50" style="font-size: 10pt" height="28">
			<tr>
				<td colspan="5" width="75">
					<h5><b>Search Produk : </b></h5>
				</td>
			</tr>
			<tr>
				<td width="50">Barcode</td>
				<td width="10">:</td>
				<td width="295"><input style="font-size: 8pt;width:250px;" class="form-control" type="text" id="txt_kode" name="txt_kode" size="15" value="<?php echo $txt_kode; ?>" /></td>
				<td width="50">Pabrik</td>
				<td width="10">:</td>
				<td>
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
				<td>Pilih</td>
				<td width="10">:</td>
				<td colspan="2">
					<?php $pilst = $_POST['pilst'];
					if ($pilst == "1") {
						$terapp = " and approve2='1' ";
					} else if ($pilst == 2) {
						$terapp = " and approve2 is null ";
					} else {
						$terapp = " ";
					}
					?>
					<select style="font-size: 8pt;width:150px;" class="form-control" id="pilst" name="pilst">
						<option value="0" <?php if ($pilst == 0) {
												echo "selected";
											} ?>>All</option>
						<option value="1" <?php if ($pilst == 1) {
												echo "selected";
											} ?>>Sudah Approve 2</option>
						<option value="2" <?php if ($pilst == 2) {
												echo "selected";
											} ?>>Belum Approve 2</option>
					</select>
				</td>
				<td>Dari</td>
				<td>:</td>
				<td width="5">
					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'f1',
							// input name
							'controlname': 'tgl1'
						});
					</script>
				</td>
				<td>
					<input class="form-control" type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1; ?>" style="font-size: 8pt;width:100px;" size="10" />
				</td>
			</tr>
			<tr>
				<td>Nama Model</td>
				<td width="5">:</td>
				<td><input style="font-size: 8pt;width:250px;" class="form-control" type="text" id="search_model" name="search_model" size="15" value="<?php echo $search_model ?>" /></td>
				<td width="50">Jenis Pabrik</td>
				<td>:</td>
				<td>
					<select name="jenis_pabrik" class="form-control" style="font-size: 8pt;width:150px;" id="jenis_pabrik">
						<option value="">--Select--</option>
						<?php
						if ($jenis_pabrik == '4') {
							$sel1 = ' selected ';
						} elseif ($jenis_pabrik == '2') {
							$sel2 = 'selected';
						} elseif ($jenis_pabrik == '3') {
							$sel3 = 'selected';
						}
						echo "<option value='4' $sel1>SUHO</option>";
						echo "<option value='2' $sel2>FOB</option>";
						echo "<option value='3' $sel3>CMT</option>";
						?>
					</select>
				</td>
				<td>Tujuan</td>
				<td>:</td>
				<td colspan="2"><?php $sql = "select $sql_cache id,nama from gudang_distribusi where jenis=1";
								$res = mysql_query($sql) or die($sql);
								?>
					<select style="font-size: 8pt;width:150px;" class="form-control" name="gudang_filter" id="gudang_filter">
						<option value="0">Pilih All</option>
						<?php while (list($id_gudang, $nama_gudang) = mysql_fetch_array($res)) {
						?>
							<option value="<?php echo $id_gudang ?>" <?php if ($gudang_filter == $id_gudang) {
																			echo 'selected="selected"';
																		} ?>><?php echo $nama_gudang ?></option>
						<?php
						} ?>
					</select>
				</td>
				<td>Sampai</td>
				<td>:</td>
				<td width="5">
					<script language="JavaScript">
						new tcal({
							// form name
							'formname': 'f1',
							// input name
							'controlname': 'tgl2'
						});
					</script>
				</td>
				<td>
					<input class="form-control" type="text" name="tgl2" id="tgl2" value="<?php echo $tgl2; ?>" style="font-size: 8pt;width:100px;" size="10" />
				</td>
			</tr>
			<tr>
				<td>NO DO</td>
				<td>:</td>
				<td><input style="font-size: 8pt;width:250px;" class="form-control" type="text" id="master" name="master" size="15" value="<?php echo $_POST[master] ?>">&nbsp;&nbsp;&nbsp;
					<script language="JavaScript" src="calendar_us.js"></script>
					<link rel="stylesheet" href="calendar.css">
				</td>
				<td>Berdasarkan</td>
				<td>:</td>
				<td><select style="font-size: 8pt;width:150px;" class="form-control" id="berdasarkan" name="berdasarkan">
						<?php
						foreach ($base as $id => $value) {
						?>
							<option value="<?php echo $id; ?>" <?php if ($id == $berdasarkan) {
																	echo "selected";
																} ?>><?php echo $value; ?>&nbsp;</option>
						<?php
						}
						?>
					</select></td>
					<td>Jml Hal </td>
				<td width="10">:</td>
				<td colspan="2">
					<input style="font-size: 8pt;width:50px;" class="form-control" type="text" size="3" id="jmlh" name="jmlh" value="<?php echo $jmlh ?>" />
					<input type="hidden" id="tg" name="tg" value="<?php echo $_GET['tgl1']; ?>" />
					<input type="hidden" id="hal" name="hal" />
				</td>
				<td colspan="4">
					<button style="font-size: 8pt;width:100px;" class="btn btn-success btn-block" type="submit" id="but" value="Search" width="100">Search</button>
				</td>
			</tr>
		</table>
	</form>
</fieldset>

<?php
if (!empty($_POST['search_model'])) {
	$sql_model = "SELECT SQL_CACHE count(*) FROM mst_model WHERE model LIKE '%$_POST[search_model]%'";
	$res_model = mysql_query($sql_model);
	list($ada) = mysql_fetch_array($res_model);
	if ($ada > 0) {
	} else {
		echo "<h1>Nama Model tidak ditemukan</h1>";
		include_once('footer.php');
		die();
	}
}

?>
<table border="1" width="100%" cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
	<thead>
		<tr class="header_table_q">
			<td align="center" width="40" height="50"><b>No</b></td>
			<td align="center" width="250" height="50"><b>No DO</b></td>
			<!-- td align="center" width="110" height="20"><b>Model</b></td -->
			<td align="center" width="100" height="50"><b>Tanggal</b></td>
			<td align="center" width="20" height="50"><b>Pabrik</b></td>
			<td align="center" width="20" height="50"><b>Kode Pabrik</b></td>
			<td align="center" width="20"><strong>Kode Tujuan</strong></td>
			<td align="center" width="20">Tujuan</td>
			<td align="center" width="20" height="50"><b>Total Qty</b></td>
			<td align="center" width="20" height="50"><b>Jml Hpp (Rp)</b></td>
			<td align="center" width="20" height="50"><b>Jumlah (Rp)</b></td>
			<td align="center" width="20" height="50" nowrap><b>Approve I</b></td>
			<td align="center" width="20" height="50" nowrap><b>Approve II</b></td>
			<td width="20" align="center"><strong>Tgl Approve II</strong></td>
			<td align="center" width="20" height="50"><b>Jml Hari</b></td>
			<td width="20" height="50" align="center"><b>Action</b></td>
		</tr>
	</thead>
	<?php
	// Edit By Goberan <-> 26-6-2010
	if (isset($_REQUEST['hal'])) $hal = $_REQUEST['hal'];
	else $hal = 0;
	$jmlHal = $jmlh;
	$page = $hal;
	if ($_SESSION["id_group"] == "1" || $typeoutlet == "G") {
		if ($_GET[search] == "yes") {
			if (!empty($search_model) || !empty($txt_kode)) {

				if ($search_model) {
					//ganti by budi							
					$sql1 = mysql_query("SELECT SQL_CACHE kode,kode_basic_item,kode_kategori,kode_kelas,kode_style FROM mst_model WHERE model LIKE '%$_POST[search_model]%'");
					//asal
					//list($kd_model,$kbi,$kkat,$kkel,$ksty)=mysql_fetch_array($sql1);
					//$barcode="$kbi$kkat$kkel$ksty$kd_model";
					$sql_tbhn = '';
					$data = '';
					//pencarian model hanya dicari berdasarkan model saja
					while (list($kd_model, $kbi, $kkat, $kkel, $ksty) = mysql_fetch_array($sql1)) {
						$data .= " do_produk_detail.kd_produk LIKE '$kbi$kkat$kkel$ksty$kd_model%' OR";
					}
					if (!empty($data)) {
						$data = substr($data, 0, (strlen($data) - 2)); //menghilangkan or terakhir	
						$sql_tbhn .= " AND  ( $data )";
					}
				}



				if (!empty($_POST['master'])) {
					$sql_tbhn .= "and (do_produk.no_do like '$_POST[search_model]%' or do_produk.tanggal like '$_POST[search_model]%' or pabrik.nama like '%$_POST[master]%' )";
				}

				if ($txt_kode) {
					$sql_tbhn .= " and do_produk_detail.kd_produk LIKE '$txt_kode%'";
				}
				//$sql="SELECT * FROM do_produk as dp inner join do_produk_detail as dpd on dp.no_do = dpd.no_do WHERE dpd.kd_produk LIKE '$barcode%' AND dp.no_do NOT LIKE '%smt%' and no_fin LIKE '%$no_fin' ORDER BY approve,tanggal"; 
				//do_produk.no_do NOT LIKE 'P100S%' and


				if (!empty($gudang_filter)) {
					$sql_tbhn .= " AND do_produk.gudang='$gudang_filter'";
				}

				if ($pabrik == "") {
					$terusan_manufaktur = "";
				} else {
					$terusan_manufaktur = " AND do_produk.pabrik='$pabrik' ";
				}

				$sql = "SELECT SQL_CACHE do_produk.*,pabrik.nama,DATEDIFF('" . date("Y-m-d") . "',tanggal) as usia FROM do_produk,do_produk_detail,pabrik 
						WHERE do_produk.no_do NOT LIKE '%smt%' 
						and do_produk.no_do NOT LIKE '%btl%'  
						and do_produk.no_fin LIKE '%$no_fin' 
						and do_produk.no_do=do_produk_detail.no_do 
						and do_produk_detail.kd_produk LIKE '$barcode%' 
						and pabrik.id=substring(do_produk.no_do,1,5)
						$terusan_manufaktur $sql_tbhn $addcode $terapp 
						group by do_produk.no_do   
						ORDER BY approve,tanggal DESC,no_do DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- sql6';


				/* DESC limit 
					
					".($page*$jmlHal).",".$jmlHal; */
				// echo "Barcode : $barcode & Model : $_POST[search_model]<br>";
				// echo $sql;
			} else {
				//no_do NOT LIKE 'P100S%' and

				if ($pabrik == "") {
					$terusan_manufaktur = "";
				} else {
					$terusan_manufaktur = " AND do_produk.pabrik='$pabrik' ";
				}

				if ($jenis_pabrik == "") {
					$terusan_jenis_pabrik = "";
				} else {
					$terusan_jenis_pabrik = " AND pabrik.id_group='$jenis_pabrik' ";
				}

				if (!empty($gudang_filter)) {
					$sql_tbhn .= " AND do_produk.gudang='$gudang_filter'";
				}

				$sql = "SELECT SQL_CACHE do_produk.*,pabrik.id_group,DATEDIFF('" . date("Y-m-d") . "',tanggal) as usia FROM do_produk 
						LEFT JOIN pabrik ON( do_produk.pabrik = pabrik.id) 
						WHERE no_do NOT LIKE '%smt%' 
						and no_do NOT LIKE '%btl%' 
						and no_fin LIKE '%$no_fin' 
						and (no_do like '$_POST[master]%' or tanggal like '$_POST[master]%') 
						$terusan_manufaktur $terusan_jenis_pabrik $sql_tbhn $addcode $terapp  
						group by do_produk.no_do 
						ORDER BY approve,tanggal DESC,no_do DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- sql5';
			}
		} else {
			//no_do NOT LIKE 'P100S%' and 

			if ($pabrik == "") {
				$terusan_manufaktur = "";
			} else {
				$terusan_manufaktur = " AND do_produk.pabrik='$pabrik' ";
			}

			if ($jenis_pabrik == "") {
				$terusan_jenis_pabrik = "";
			} else {
				$terusan_jenis_pabrik = " AND pabrik.id_group='$jenis_pabrik' ";
			}

			$sql = "SELECT SQL_CACHE do_produk.*,pabrik.id_group,DATEDIFF('" . date("Y-m-d") . "',tanggal) as usia FROM do_produk 
					LEFT JOIN pabrik ON( do_produk.pabrik = pabrik.id) 
					WHERE no_do NOT LIKE '%smt%' 
					and no_do NOT LIKE '%btl%' 
					and no_do NOT LIKE '%btl%' 
					and no_fin LIKE '%$no_fin' 
					and (no_do like '$_POST[search_model]%' or tanggal like '$_POST[search_model]%') 
					$terusan_manufaktur $terusan_jenis_pabrik $addcode $terapp 
					group by do_produk.no_do 
					ORDER BY approve,tanggal DESC,no_do DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- sql4';

		}
	} else {
		if ($_GET[search] == "yes") {
			if (!empty($search_model) || !empty($txt_kode)) {

				if ($search_model) {
					//ganti by budi							
					$sql1 = mysql_query("SELECT SQL_CACHE kode,kode_basic_item,kode_kategori,kode_kelas,kode_style FROM mst_model WHERE model LIKE '%$_POST[search_model]%'");
					$sql_tbhn = '';
					$data = '';

					//pencarian model hanya dicari berdasarkan model saja
					
					while (list($kd_model, $kbi, $kkat, $kkel, $ksty) = mysql_fetch_array($sql1)) {
						$data .= " do_produk_detail.kd_produk LIKE '$kbi$kkat$kkel$ksty$kd_model%' OR";
					}
					if (!empty($data)) {
						$data = substr($data, 0, (strlen($data) - 2)); //menghilangkan or terakhir	
						$sql_tbhn .= " AND  ( $data )";
					}
				}

				if ($txt_kode) {
					$sql_tbhn .= " and do_produk_detail.kd_produk LIKE '$txt_kode%'";
				}

				if (!empty($_POST['master'])) {
					$sql_tbhn .= "and (do_produk.no_do like '$_POST[search_model]%' or do_produk.tanggal like '$_POST[search_model]%' or pabrik.nama like '%$_POST[master]%' )";
				}

				if (!empty($gudang_filter)) {
					$sql_tbhn .= " AND do_produk.gudang='$gudang_filter'";
				}

				if ($pabrik == "") {
					$terusan_manufaktur = "";
				} else {
					$terusan_manufaktur = " AND do_produk.pabrik='$pabrik' ";
				}

				$sql = "SELECT do_produk.*,pabrik.nama,DATEDIFF('" . date("Y-m-d") . "',tanggal) as usia FROM do_produk,do_produk_detail,pabrik 
						WHERE  do_produk.no_do NOT LIKE '%smt%' 
						and do_produk.no_do NOT LIKE '%btl%' 
						and do_produk.no_fin LIKE '%$no_fin' 
						and do_produk.no_do=do_produk_detail.no_do $sql_tbhn 
						and pabrik.id=substring(do_produk.no_do,1,5) 
						and (do_produk.no_do like '$_POST[master]%' or do_produk.tanggal like '$_POST[master]%' or pabrik.nama like '%$_POST[master]%' ) 
						and no_fin like '$no_fin%' 
						$terusan_manufaktur $addcode $terapp 
						group by do_produk.no_do 
						ORDER BY approve,tanggal DESC,no_do DESC" . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- sql3';

			} else {

				if ($pabrik == "") {
					$terusan_manufaktur = "";
				} else {
					$terusan_manufaktur = " AND do_produk.pabrik='$pabrik' ";
				}

				if ($jenis_pabrik == "") {
					$terusan_jenis_pabrik = "";
				} else {
					$terusan_jenis_pabrik = " AND pabrik.id_group='$jenis_pabrik' ";
				}

				$sql = "SELECT do_produk.*,pabrik.id_group,DATEDIFF('" . date("Y-m-d") . "',tanggal) as usia FROM do_produk 
						LEFT JOIN pabrik ON( do_produk.pabrik = pabrik.id) 
						WHERE no_do NOT LIKE '%smt%' 
						and no_do NOT LIKE '%btl%' 
						and no_do NOT LIKE '%btl%' 
						and  no_fin LIKE '%$no_fin'
						and (no_do like '$_POST[master]%' or tanggal like '$_POST[master]%' or pabrik.nama like '%$_POST[master]%' ) 
						$terusan_manufaktur $terusan_jenis_pabrik $sql_tbhn $addcode $terapp 
						group by do_produk.no_do 
						ORDER BY approve,tanggal DESC,no_do DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- sql2';

				/*echo "Input Kosong !!!";
						echo "<META HTTP-EQUIV=\"REFRESH\" CONTENT=\"0; url=do_produk_list.php\">";
						return 0;*/
			}
		} else {
			//SELECT * FROM do_produk WHERE no_do NOT LIKE 'P100S%'

			if ($pabrik == "") {
				$terusan_manufaktur = "";
			} else {
				$terusan_manufaktur = " AND do_produk.pabrik='$pabrik' ";
			}

			if ($jenis_pabrik == "") {
				$terusan_jenis_pabrik = "";
			} else {
				$terusan_jenis_pabrik = " AND pabrik.id_group='$jenis_pabrik' ";
			}

			if (!empty($gudang_filter)) {
				$sql_tbhn .= " AND do_produk.gudang='$gudang_filter'";
			}


			$sql = "SELECT do_produk.*,pabrik.id_group,DATEDIFF('" . date("Y-m-d") . "',tanggal) as usia FROM do_produk
					LEFT JOIN pabrik ON( do_produk.pabrik = pabrik.id) 
					WHERE no_do NOT LIKE '%smt%' 
					and no_do NOT LIKE '%btl%' 
					and no_do NOT LIKE '%btl%' 
					and no_fin LIKE '%$no_fin' 
					and (no_do like '$_POST[master]%' or tanggal like '$_POST[master]%' or pabrik.nama like '%$_POST[master]%' )  
					$terusan_manufaktur $terusan_jenis_pabrik $sql_tbhn  $addcode $terapp 
					group by do_produk.no_do 
					ORDER BY approve,tanggal DESC,no_do DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- sql1';
		}
	}

	//echo $sql;	
	// echo "<!-- $sql -->";

	if ($username == 'budi-it') {
		echo "$sql";
	}
	//where no_do NOT LIKE 'P100S%'
	$query = mysql_query($sql, $link);
	$jmlData = mysql_num_rows($query);
	$sql = $sql . " limit " . ($page * $jmlHal) . "," . $jmlHal;

	$hsl = mysql_query($sql, $db) or die($sql);
	$no = ($hal * $jmlHal);
	$arrayGudang = array();
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
		$kode_gudang = trim($rs['gudang']);
		$usia = trim($rs['usia']);
		if (array_key_exists($kode_gudang, $arrayGudang)) {
			$gudang = $arrayGudang[$kode_gudang];
		} else {
			$sql_gudang = "select nama from gudang_distribusi where id='$kode_gudang';";
			$res_gudang = mysql_query($sql_gudang);
			list($gudang) = mysql_fetch_array($res_gudang);
			$arrayGudang[$kode_gudang] = $gudang;
		}
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

		$sql = "select sum(hpp*qty) from do_produk_detail where no_do='$no_do'";
		$res = mysql_query($sql) or die($sql);
		list($hpp) = mysql_fetch_array($res);


	?>
		<tr>
			<td align="center" width="40" height="43"><?php echo $no; ?></td>
			<td align="left" width="250" height="43">&nbsp;<?php echo $no_do; ?></td>
			<!-- td align="center" width="110" height="20">&nbsp;<?php // echo $style; 
																	?></td -->
			<td align="left" width="150" height="43">&nbsp;<?php echo $tanggal; ?></td>
			<td align="left" width="200" height="43">&nbsp;<?php echo $pabrik; ?></td>
			<td align="center" width="100" height="43">&nbsp;<?php echo $keterangan; ?></td>
			<td align="center" width="100"><?php echo $kode_gudang; ?></td>
			<td align="center" width="100"><?php echo $gudang; ?></td>
			<td align="center" width="90" height="43"><?php echo number_format($totalqty, 0, ".", ",");
														$totqty = $totqty + $totalqty; ?></td>
			<td align="center" width="100" height="43"><?php echo number_format($hpp, "2", ".", ",");
														$tothpp = $tothpp + $hpp; ?></td>
			<td align="center" width="100" height="43"><?php echo number_format($totalrp, 2, ".", ",");
														$totrp = $totrp + $totalrp; ?></td>
			<td align="center" width="110" height="43"><?php echo $status; ?></td>
			<td align="center" width="110" height="43"><?php echo $status2; ?></td>

			<?php
			if ($tglapproved2 == "") {
				$tglapproved2 = "-";
			}
			?>
			<td nowrap align="center" width="120">&nbsp;<?php echo $tglapproved2; ?></td>
			<td nowrap align="center" width="120">&nbsp;<?php echo $usia; ?></td>
			<td nowrap align="center" width="120" height="43">&nbsp;
				<a href="do_produk_detail_v3.php?no_do=<?php echo $no_do; ?>&rnd=<?php echo date('YmdHis'); ?>" target="_blank">Detil</a> |

				<?php if (($approve2) && (($_SESSION['id_group'] == '125') || ($_SESSION['id_group'] == '1'))) { ?><a href="do_produk_detail.php?no_do=<?php echo $no_do; ?>&retur">Retur</a> <?php  } ?>

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
	<thead>
		<tr class="footer_table_q">
			<td colspan="7" height="30"><strong>&nbsp;Jumlah</strong> </td>
			<td align="center"><strong><?php echo number_format($totqty, "0", ".", ","); ?></strong></td>
			<td align="center"><strong><?php echo number_format($tothpp, "2", ".", ","); ?></strong></td>
			<td align="center"><strong><?php echo number_format($totrp, "2", ".", ","); ?></strong></td>
			<td colspan="5">&nbsp;</td>
		</tr>
	</thead>
</table>
<script>
	var tgl = $("#tg").val();
	if (tgl != "") {
		$("#but").click();
	}
</script>
<?php

?>
<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			Page :
			<span class="hal" onclick=pindah12('0')>First</span>
			<?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="pindah12('<?php echo $i ?>')"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData / $jmlHal)) {
					if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="pindah12('<?php echo $i ?>')"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="pindah12('<?php echo $i ?>')"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="pindah12('<?php echo intval(($jmlData / $jmlHal)); ?>')">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?>
		</td>
	</tr>
</table>

<script>
	function pindah12(halaman) {
		$("#hal").val(halaman);
		$("#but").click();
	}
</script>

<?php include_once "footer.php" ?>