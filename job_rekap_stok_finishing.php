<?php session_start(); ?>
<?php $content_title = "REKAP STOK FINISHING";
set_time_limit(86400); // 24 jam
error_reporting(-1);
include("css_group.php");



$se = ','; //separator export

//ini_set('memory_limit','256M');
/*
v3 tgl 11 april 2012
ditambah  fasilitas muncul kolom data total stok in,retur out,stok out,retur in, koreksi
v4 tgl 12 april 2012
Ditambah fasilitas stok pertanggal
v5 Ditambah fasilitas pencarian produk per style 23 April 2012

v5 1 Mei Add Security

v9 ada tambahan pilihan export untuk beberapa reshare permintaan FAI
 ketika pilihan ALL reshare untuk export maka muncul pilihan reshare sesuai dengan usernya

v10 Download menggunakan engine download berformat zip

v14 tambahah export by detailsel1
*/



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

$isDirectDownload = 1;

$kdgd = $_SESSION['outlet'];
$area_lain = $_SESSION['area_lain'];
$group = substr($kdgd, 5, 5);
if ($group == 'O0000' || $group == 'o0000') {
	$area = substr($kdgd, 0, 4);
} else {
	$area = $kdgd;
}

$data_tambahan = " r.kode_manufaktur like '$area%' ";
if (!empty($area_lain)) {
	$d = explode(';', $area_lain);
	$d_lain = '';
	foreach ($d as $kd_lain) {
		$kd_lain = trim($kd_lain);
		if (!empty($kd_lain)) {
			$d_lain .= " OR r.kode_manufaktur LIKE '$kd_lain%' ";
		}
	}
	$data_tambahan .= $d_lain;
}

# $sql_tambahan=" AND ( $data_tambahan ) ";	


#$sql_area= " AND r.kode_manufaktur like '$area%' ";
$sql_area = "  AND ( $data_tambahan ) ";

$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

$sql_tambahan = " AND r.kode_manufaktur like '%0%' and length(trim(r.kode_manufaktur))=10 "; // add tgl 22 jan 2014
@$txt_organization = $_POST['txt_organization'];
$sql_cache = ' SQL_CACHE ';

@$txtTable = $_POST['txtTable'];
if (empty($txtTable)) {
	$table = 'rekap_stok_manufaktur';
} else {
	$table = $txtTable;
}

@$txtprodukpilihan = $_POST['txtprodukpilihan'];
@$txtjenis = $_POST['txtjenis'];
@$txtmarkas = $_POST['txtmarkas'];
@$barcode = $_POST['barcode'];
@$txt_nama = $_POST['txt_nama'];
@$tanggal = $_POST['tanggal'];
@$jenis_pabrik = $_POST['jenis_pabrik'];


if ($jenis_pabrik <> '') {
	$filter_pabrik = " AND p.id_group='$jenis_pabrik' ";
}

if ($tanggal == '') {
	$tanggal = date('j');
}


@$outlet_export = $_POST['outlet_export'];
//echo 'Markas :' .$txtmarkas;
if (isset($txtmarkas)) {
	if ($txtmarkas == 'ALL') {
		$area == '';
	} else {
		//$area=substr($txtmarkas,0,4);;
	}
}
//function for XLS
// ----- begin of function library -----
// Excel begin of file header
function xlsBOF()
{
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	return;
}

function xlsEOF()
{
	echo pack("ss", 0x0A, 0x00);
	return;
}

function xlsWriteNumber($Row, $Col, $Value)
{
	echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	echo pack("d", $Value);
	return;
}

function xlsWriteLabel($Row, $Col, $Value)
{
	$L = strlen($Value);
	echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	echo $Value;
	return;
}
// ----- end of function library ----- 

#error_reporting(1);
#ini_set('error_reporting',E_ALL);


$export_to_excel = '';

@$detail = $_POST['cb_detail'];
@$pabrik1 = $_POST['pabrik1'];
@$jenis_pabrik = $_POST['jenis_pabrik'];


if (@$_POST['submit'] == 'export' || $detail == 1 || @isset($_POST['btnExportDataStok'])) {
	@session_start();
	require_once("config.php");

	@$username = $_SESSION["username"];
	if (empty($username)) {
		die('You can\'t see this page');
	}

	$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

	if ($jenis_pabrik <> '') {
		$filter_pabrik = " AND p.id_group='$jenis_pabrik' ";
	}


	$tahun_skrg = date('Y');
	$bulan_skrg = date('m');

	if (isset($_REQUEST['action'])) {
		@$bulan1 = $_POST['bulan1'];
		@$tahun1 = $_POST['tahunl'];
		//echo "Kapilih";
	} else {
		$bulan1 = $bulan_skrg;
		$tahun1 = $tahun_skrg;
	}

	$isShowHpp = 0;
	$sql = "SELECT COUNT(*) ada FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
	$res = mysql_query($sql);
	list($ada) = mysql_fetch_array($res);

	if ($ada > 0) {
		$isShowHpp = 1;
	}


	$filter_periode = $tahun1 . '-' . $bulan1;
	if ($filter_periode != date('Y-m')) {
		$table_rekap_stok = 'rekap_stok_manufaktur';
		$hari_download = '';
	} else {
		$table_rekap_stok = $table;
		$hari_download = date('Y-m-d');
	}

	/*contoh Export ke excel*/
	if (@$_POST['cb_xls'] != '1') {
		$csv = 1;
	} else {
		$csv = 0;
	}


	$time = date('YmdHis');

	$isExportStokOnly = 0;
	if (@isset($_POST['btnExportDataStok'])) {
		$isExportStokOnly = 1;
		$csv = 1;
	}

	if ($csv == 1) {
		$ext = 'csv';
	} else {
		$ext = 'xls';
	}

	#ob_start("ob_gzhandler");//tambahan 30 jan 2014


	if ($csv == 0) {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");;
		if ($isExportStokOnly == 1) {
			header("Content-Disposition: attachment;filename=" . $pabrik1 . $jenis_pabrik . $txtprodukpilihan . "StokOnly" . $time . "." . $ext);
		} else {
			header("Content-Disposition: attachment;filename=" . $pabrik1 . $jenis_pabrik . $txtprodukpilihan . "_" . $time . "." . $ext);
		}

		header("Content-Transfer-Encoding: binary ");
	} else { //export to csv format
		if ($isExportStokOnly == 1) {
			#$filename=$pabrik1."StokOnly".$time.".".$ext;	

			$myFile = 'StokOnly' . $txtprodukpilihan . str_ireplace('.', '', $txtmarkas . $pabrik1) . '_' . $jenis_pabrik . '_' . $txtjenis . '_' . $filter_periode .
				'_' . $hari_download . '_' . $tanggal . '_' . date('His') . '.' . $ext;
		} else {
			#$filename=$pabrik1."_".$time.".".$ext;
			$myFile = $txtprodukpilihan . str_ireplace('.', '', $txtmarkas . $pabrik1) . '_' . $jenis_pabrik . '_' . $txtjenis . '_' . $filter_periode . '_' . $hari_download . '.' . $ext;
		}

		if ($isShowHpp == 1) {
			$myFile = 'C_' . $myFile;
		}

		if ($isShowHpp == 1) {
			$location_save = 'export_rekap_cmplt/';
			$field_hpp_export = 'r.hpp';
		} else {
			$location_save = 'export_rekap/';
			$field_hpp_export = '0';
		}



		$destination = '/var/www/html/quantum/' . $location_save;
		#$myFile = str_ireplace('.','',$txtmarkas.$pabrik1).'_'.$txtjenis.'_'.$filter_periode.'_'.$hari_download.'.'.$ext;

		$zip_file = str_ireplace('csv', 'zip', $destination . $myFile);
		$zip_file_name = str_ireplace('csv', 'zip', $myFile);




		if ($isDirectDownload) {
			//die('direct acces');
			if (file_exists($zip_file)) {
				//echo "<meta http-equiv='refresh' content='0'; URL='http://103.14.21.57/quantum/export_rekap/$zip_file_name'>";	
				header('location:' . $location_save . $zip_file_name);
				die();
			}
		} else {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment;filename=$zip_file_name");
			header("Content-Transfer-Encoding: binary ");

			if (file_exists($zip_file)) {
				#echo file_get_contents($zip_file);
				readfile($zip_file);
				#header('location:export_rekap/'.$zip_file_name);
				die();
			}
		}


		//Hapus File yang ada dan 1 periode berdasarkan gudang

		@$result_delete = exec("/bin/rm -f $destination" . $txtmarkas . $pabrik1 . "_" . $filter_periode . "_*");

		#echo "Result Delete $result_delete $myFile "."/bin/rm -f $destination".$txtmarkas.$pabrik1."_".$filter_periode."_*";
		ini_set('memory_limit', '-1'); //menghindari exhausted memori
		@$fh = fopen($destination . $myFile, 'w') or die("can't open file " . $destination . $myFile);
		#die();
	}

	$background = '';
	$export_to_excel = '1';

	// if (!empty($outlet_export)) {
	// 	$dt_outlet = explode(';', $outlet_export);
	// 	$data_in = '';
	// 	foreach ($dt_outlet as $d_otl) {
	// 		if (!empty($d_otl)) {
	// 			$data_in .= "'$d_otl',";
	// 		}
	// 	}
	// 	$data_in = substr($data_in, 0, strlen($data_in) - 1);
	// 	$sql_pilih_reshare = " AND r.kode_manufaktur in ($data_in) ";
	// } else {
	// 	$sql_pilih_reshare = '';
	// }

	// $sql_inner_outlet = ' INNER JOIN outlet o on o.id=r.kode_manufaktur ';

	$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

	if ($jenis_pabrik <> '') {
		$filter_pabrik = " AND p.id_group='$jenis_pabrik' ";
	}

	// $sql_aktif = ' AND o.is_show_omset=1 ';
	$filter_barcode = '';
	if (!empty($barcode)) {
		# $filter_barcode=" And (barcode_15 like '$barcode%' or barcode_13 like '$barcode%' or nama like '$barcode%') ";
		$filter_barcode .= " And (r.barcode_15 like '$barcode%') ";
	}
	if (!empty($txt_nama)) {
		$filter_barcode .= " And (r.nama like '%$txt_nama%') ";
	}

	if ($txtmarkas == 'ALL' || $txtmarkas == '--') { //edit tgl 25072017 by budi
		$txtmarkas = '';
	}

	if (!empty($txtmarkas)) {
		$txtmarkas = substr($txtmarkas, 0, 4);
		$filter_barcode .= " AND o.id_markas like '$txtmarkas%'";
	}

	if (!empty($txtprodukpilihan)) {

		if (trim($pabrik1) == 'ALL') {
			// $filter = " r.jenis like '$txtjenis%' ";
		} else {
			$filter = " r.kode_manufaktur='$pabrik1' ";
		}

		$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),r.kode_manufaktur,r.nama_manufaktur,
				r.barcode_13,r.barcode_15,r.nama,( $field_hpp_export ) as hpp,r.hpj as hpj, (  r.stok_awal * $field_hpp_export ) as hpp_stok,(r.stok_awal * r.hpj) as hpj_stok,r.stok_awal,
				r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20,
				r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31,
				r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20,
				r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31,
				r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20,
				r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31,
				r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20,
				r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31,
				r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20,
				r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,(0),(r.hpj),p.id_group 
				FROM $table_rekap_stok as r $sql_inner_group WHERE  $filter and r.periode like '$filter_periode%' $filter_barcode $filter_pabrik $sql_tambahan $sql_area ORDER by  r.kode_manufaktur,r.barcode_15; -- sql 78 ";
		#die($sql); 
	} elseif (trim($pabrik1) == 'ALL') {


		if ($isExportStokOnly == 1) { //dipindah ke rekap_stok_outlet_complete_stock_only_export.php

			$field_stok_akhir = '(r.stok_awal';
			for ($i = 1; $i <= $tanggal; $i++) {
				$field_stok_akhir .= '+ r.si' . $i . ' - r.ro' . $i . ' - r.so' . $i . ' + r.ri' . $i . ' + r.k' . $i;
			}
			$field_stok_akhir .= ')';
			if ($username != 'budi-it') {
				//$field_stok_akhir='r.stok_akhir';	  
			}

			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),r.kode_manufaktur,r.nama_manufaktur,";
			$sql .= "r.barcode_13,r.barcode_15,r.nama,r.hpj,";
			$sql .= "sum(r.stok_akhir),sum(  $field_hpp_export   * r.stok_akhir),sum(r.hpj* r.stok_akhir),p.id_group ";
			$sql .= "FROM $table_rekap_stok  r $sql_inner_group WHERE  r.periode like '$filter_periode%' ";
			$sql .= " $filter_barcode $filter_pabrik $sql_area group by r.kode_manufaktur,r.barcode_15;  -- sql 9";
		} else {

			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),r.kode_manufaktur,r.nama_manufaktur,";
			$sql .= "r.barcode_13,r.barcode_15,r.nama,$field_hpp_export hpp,r.hpj , sum( $field_hpp_export * r.stok_awal),sum(r.hpj* r.stok_awal),sum(r.stok_awal),";
			$sql .= "sum(r.si1),sum(r.si2),sum(r.si3),sum(r.si4),sum(r.si5),sum(r.si6),sum(r.si7),sum(r.si8),sum(r.si9),sum(r.si10),";
			$sql .= "sum(r.si11),sum(r.si12),sum(r.si13),sum(r.si14),sum(r.si15),sum(r.si16),sum(r.si17),sum(r.si18),sum(r.si19),sum(r.si20),";
			$sql .= "sum(r.si21),sum(r.si22),sum(r.si23),sum(r.si24),sum(r.si25),sum(r.si26),sum(r.si27),sum(r.si28),sum(r.si29),sum(r.si30),sum(r.si31),";
			$sql .= "sum(r.ro1),sum(r.ro2),sum(r.ro3),sum(r.ro4),sum(r.ro5),sum(r.ro6),sum(r.ro7),sum(r.ro8),sum(r.ro9),sum(r.ro10),";
			$sql .= "sum(r.ro11),sum(r.ro12),sum(r.ro13),sum(r.ro14),sum(r.ro15),sum(r.ro16),sum(r.ro17),sum(r.ro18),sum(r.ro19),sum(r.ro20),";
			$sql .= "sum(r.ro21),sum(r.ro22),sum(r.ro23),sum(r.ro24),sum(r.ro25),sum(r.ro26),sum(r.ro27),sum(r.ro28),sum(r.ro29),sum(r.ro30),sum(r.ro31),";
			$sql .= "sum(r.so1),sum(r.so2),sum(r.so3),sum(r.so4),sum(r.so5),sum(r.so6),sum(r.so7),sum(r.so8),sum(r.so9),sum(r.so10),";
			$sql .= "sum(r.so11),sum(r.so12),sum(r.so13),sum(r.so14),sum(r.so15),sum(r.so16),sum(r.so17),sum(r.so18),sum(r.so19),sum(r.so20),";
			$sql .= "sum(r.so21),sum(r.so22),sum(r.so23),sum(r.so24),sum(r.so25),sum(r.so26),sum(r.so27),sum(r.so28),sum(r.so29),sum(r.so30),sum(r.so31),";
			$sql .= "sum(r.ri1),sum(r.ri2),sum(r.ri3),sum(r.ri4),sum(r.ri5),sum(r.ri6),sum(r.ri7),sum(r.ri8),sum(r.ri9),sum(r.ri10),";
			$sql .= "sum(r.ri11),sum(r.ri12),sum(r.ri13),sum(r.ri14),sum(r.ri15),sum(r.ri16),sum(r.ri17),sum(r.ri18),sum(r.ri19),sum(r.ri20),";
			$sql .= "sum(r.ri21),sum(r.ri22),sum(r.ri23),sum(r.ri24),sum(r.ri25),sum(r.ri26),sum(r.ri27),sum(r.ri28),sum(r.ri29),sum(r.ri30),sum(r.ri31),";
			$sql .= "sum(r.k1),sum(r.k2),sum(r.k3),sum(r.k4),sum(r.k5),sum(r.k6),sum(r.k7),sum(r.k8),sum(r.k9),sum(r.k10),";
			$sql .= "sum(r.k11),sum(r.k12),sum(r.k13),sum(r.k14),sum(r.k15),sum(r.k16),sum(r.k17),sum(r.k18),sum(r.k19),sum(r.k20),";
			$sql .= "sum(r.k21),sum(r.k22),sum(r.k23),sum(r.k24),sum(r.k25),sum(r.k26),sum(r.k27),sum(r.k28),sum(r.k29),sum(r.k30),sum(r.k31),sum(r.stok_akhir),sum( $field_hpp_export *r.stok_akhir),sum(r.hpj*r.stok_akhir),p.id_group ";
			$sql .= "FROM $table_rekap_stok r $sql_inner_group WHERE  r.periode like '$filter_periode%' ";
			$sql .= " $filter_barcode $filter_pabrik $sql_area  group by r.kode_manufaktur,r.barcode_15; -- sql 87";
		}
	} else {
		if ($isExportStokOnly == 1) {

			$field_stok_akhir = '(r.stok_awal';
			for ($i = 1; $i <= $tanggal; $i++) {
				$field_stok_akhir .= '+ r.si' . $i . ' - r.ro' . $i . ' - r.so' . $i . ' + r.ri' . $i . ' + r.k' . $i;
			}
			$field_stok_akhir .= ')';
			if ($username != 'budi-it') {
				#$field_stok_akhir='r.stok_akhir';	  
			}
			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),r.kode_manufaktur,r.nama_manufaktur,
				r.barcode_13,r.barcode_15,r.nama,r.hpj,$field_stok_akhir as stok_akhir,(  $field_stok_akhir * $field_hpp_export ) as hpp,( r.hpj * $field_stok_akhir) as hpj,p.id_group 
				FROM $table_rekap_stok r $sql_inner_group WHERE r.kode_manufaktur='$pabrik1'  and r.periode like '$filter_periode%' $filter_barcode $filter_pabrik $sql_area ; -- sql10";
		} else {

			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(tgl_stok_awal,'%d %M %Y'),r.kode_manufaktur,r.nama_manufaktur,
				r.barcode_13,r.barcode_15,r.nama,$field_hpp_export as hpp,r.hpj hpj,( r.stok_awal * $field_hpp_export  ) as hpp_stok,(  r.stok_awal * r.hpj ) as hpj_stok,r.stok_awal,
				r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20,
				r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31,
				r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20,
				r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31,
				r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20,
				r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31,
				r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20,
				r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31,
				r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20,
				r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,( r.stok_akhir * $field_hpp_export ) as hpp_stok_akhir,( r.stok_akhir * r.hpj) hpj_stok_akhir,p.id_group 
				FROM $table_rekap_stok r $sql_inner_group WHERE r.kode_manufaktur='$pabrik1' and r.periode like '$filter_periode%' $filter_barcode $filter_pabrik $sql_area ; ";
		}
	}

	if ($username == 'A011161_iqbal') { //||$username=='sm_padalarang_handiani'){ 
		#die($sql);//Debug Export Stok
	}
	$hsltemp = mysql_query($sql); // or die($sql);
	if ($username == 'budi-it') { //||$username=='inventory_darman'){//||$username=='sm_padalarang_handiani'){
		# die($sql);
	}
	if (!$hsltemp) {
		if ($username == 'faipusat_yati') { //||$username=='sm_padalarang_handiani'){ 
			echo $sql . "<br>"; //Debug Export Stok
			die(mysql_error());
		}
	}
	#die($sql);
	$row_found = mysql_num_rows($hsltemp);
	if (empty($row_found)) {
		die($sql);
		// 

	}

	$i = 0;
	$count = 0;
	$row = 0;



	if ($csv == 0) {
		xlsBOF();
		//===============Judul=========
		xlsWriteLabel($row, 1, "NO");
		xlsWriteLabel($row, 2, "Periode");
		xlsWriteLabel($row, 3, "Tgl Upload");
		xlsWriteLabel($row, 4, "Kode Pabrik");
		xlsWriteLabel($row, 5, "Nama Pabrik");
		xlsWriteLabel($row, 6, "Barcode 13");
		xlsWriteLabel($row, 7, "Barcode 15");
		xlsWriteLabel($row, 8, "Produk");
		xlsWriteLabel($row, 9, "O / B ");
		xlsWriteLabel($row, 10, "HPP");
		xlsWriteLabel($row, 11, "HPJ");
		xlsWriteLabel($row, 12, "HPP_TOTAL");
		xlsWriteLabel($row, 13, "HPJ_TOTAL");
		$column = 14;
		for ($i = 1; $i <= 31; $i++) {
			$column++;
			eval("xlsWriteLabel($row,$column,$i);");
		}
		xlsWriteLabel($row, ++$column, 'Total IN RETUR ');
		for ($i = 1; $i <= 31; $i++) {
			$column++;
			eval("xlsWriteLabel($row,$column,$i);");
		}
		xlsWriteLabel($row, ++$column, 'Total STOCK IN ');
		for ($i = 1; $i <= 31; $i++) {
			$column++;
			eval("xlsWriteLabel($row,$column,$i);");
		}
		xlsWriteLabel($row, ++$column, 'Total STOCK OUT ');
		for ($i = 1; $i <= 31; $i++) {
			$column++;
			eval("xlsWriteLabel($row,$column,$i);");
		}
		xlsWriteLabel($row, ++$column, 'Total OUT RETUR ');
		for ($i = 1; $i <= 31; $i++) {
			$column++;
			eval("xlsWriteLabel($row,$column,$i);");
		}
		xlsWriteLabel($row, ++$column, 'Total Adjustment ');
		xlsWriteLabel($row, ++$column, 'Stok Akhir ');
		xlsWriteLabel($row, ++$column, 'Stok Akhir HPP ');
		xlsWriteLabel($row, ++$column, 'Stok Akhir HPJ');
	} else {
		//$isExportStokOnly dipindah ke rekap_stok_outlet_complete_stock_only_export.php
		if ($isExportStokOnly == 1) {
			$judul = "\t $se \t $se \t $se \t $se \t $se \t $se \t $se price $se";
			$judul .= "(E/B)Stok Akhir $se \t $se \t\n";

			if ($csv == 0) {
				echo $judul;
			} else {
				fwrite($fh, $judul);
			}


			$judul = "No $se Periode $se Kode Pabrik $se Nama Pabrik $se Barcode 13 $se Barcode 15 $se Produk $se Hpj $se";
			$judul .= "(E/B) Stok Akhir $se ";
			if ($isShowHpp == 1) {
				$judul .= "Hpp_total $se ";
			}
			$judul .= "Amount(Hpj)\r\n";
			if ($csv == 0) {
				echo $judul;
			} else {
				fwrite($fh, $judul);
			}
		} else {
			$judul = "\t $se \t $se \t $se \t $se \t $se \t $se\t $se \t $se O / B  $se ";
			if ($isShowHpp == 1) {
				$judul .= "\t $se ";
			}
			$judul .= "\t $se ";
			if ($isShowHpp == 1) {
				$judul .= "\t $se ";
			}
			$judul .= "\t $se ";

			$judul .= "STOCK IN => $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se Total STOCK IN $se ";
			$judul .= "OUT RETUR => $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se Total OUT RETUR $se ";
			$judul .= "STOCK OUT => $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se Total STOCK OUT $se ";
			$judul .= "IN RETUR => $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se Total RETUR IN $se ";
			$judul .= "Adjustment => $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se \t $se Total KOREKSI $se ";
			$judul .= "(E/B)Stok Akhir $se";

			if ($isShowHpp == 1) {
				$judul .= "\t $se ";
			}
			$judul .= "\t \r\n";

			if ($csv == 0) {
				echo $judul;
			} else {
				fwrite($fh, $judul);
			}

			$judul = "No $se Periode $se Tanggal Stok Awal $se Kode Pabrik $se Nama Pabrik $se Barcode 13 $se Barcode 15 $se Produk $se   ";
			if ($isShowHpp == 1) {
				$judul .= "Hpp $se ";
			}
			$judul .= "Hpj $se Qty $se ";
			if ($isShowHpp == 1) {
				$judul .= "Hpp_total $se ";
			}
			$judul .= "Hpj_total $se ";

			$judul .= "1 $se 2 $se 3 $se 4 $se 5 $se 6 $se 7 $se 8 $se 9 $se 10 $se 11 $se 12 $se 13 $se 14 $se 15 $se 16 $se 17 $se 18 $se 19 $se 20 $se 21 $se 22 $se 23 $se 24 $se 25 $se 26 $se 27 $se 28 $se 29 $se 30 $se 31 $se Total Retur In $se ";
			$judul .= "1 $se 2 $se 3 $se 4 $se 5 $se 6 $se 7 $se 8 $se 9 $se 10 $se 11 $se 12 $se 13 $se 14 $se 15 $se 16 $se 17 $se 18 $se 19 $se 20 $se 21 $se 22 $se 23 $se 24 $se 25 $se 26 $se 27 $se 28 $se 29 $se 30 $se 31 $se Total Stok In $se ";
			$judul .= "1 $se 2 $se 3 $se 4 $se 5 $se 6 $se 7 $se 8 $se 9 $se 10 $se 11 $se 12 $se 13 $se 14 $se 15 $se 16 $se 17 $se 18 $se 19 $se 20 $se 21 $se 22 $se 23 $se 24 $se 25 $se 26 $se 27 $se 28 $se 29 $se 30 $se 31 $se Total Stok Out $se ";
			$judul .= "1 $se 2 $se 3 $se 4 $se 5 $se 6 $se 7 $se 8 $se 9 $se 10 $se 11 $se 12 $se 13 $se 14 $se 15 $se 16 $se 17 $se 18 $se 19 $se 20 $se 21 $se 22 $se 23 $se 24 $se 25 $se 26 $se 27 $se 28 $se 29 $se 30 $se 31 $se Total Retur Out $se ";
			$judul .= "1 $se 2 $se 3 $se 4 $se 5 $se 6 $se 7 $se 8 $se 9 $se 10 $se 11 $se 12 $se 13 $se 14 $se 15 $se 16 $se 17 $se 18 $se 19 $se 20 $se 21 $se 22 $se 23 $se 24 $se 25 $se 26 $se 27 $se 28 $se 29 $se 30 $se 31 $se Total Koreksi $se ";
			$judul .= "(E/B)Stok Akhir $se ";
			if ($isShowHpp == 1) {
				$judul .= "Hpp_total $se ";
			}
			$judul .= "Hpj_total\r\n ";

			if ($csv == 0) {
				echo $judul;
			} else {
				fwrite($fh, $judul);
			}
		}
	}

	if ($pabrik1 == 'ALL') {
		$sql_outlet = "SELECT $sql_cache UPPER(id),nama from pabrik where id like '%'";
	} else {
		$sql_outlet = "SELECT $sql_cache UPPER(id),nama from pabrik where id like '$pabrik1%'";
	}


	$hsl_outlet = mysql_query($sql_outlet) or die(mysql_error() . ' ' . $sql_outlet);


	$arrayOutlet = array();
	while (list($id_outlet, $nm_outlet) = mysql_fetch_array($hsl_outlet)) {
		$arrayOutlet[$id_outlet] = $nm_outlet;
	}

	if ($username == 'budi-it') {

		#print_r($arrayOutlet);
		#die($sql_outlet.' '.$sql);
	}

	if ($isExportStokOnly == 1) {
		while (list(
			$periode, $kode_manufaktur, $nama_manufaktur, $barcode_13, $barcode_15, $nama, $hpj,
			$stok_akhir, $stok_akhir_hpp, $stok_akhir_hpj
		) = mysql_fetch_array($hsltemp)) {
			//ambil data 
			$row++;
			$i++;
			$nama_manufaktur = $arrayOutlet[strtoupper($kode_manufaktur)];

			$char = array('\r\n', ',', '\t');
			#$nama=str_replace(',',' ',$nama);
			$nama = str_replace(";", " ", $nama);
			$nama = str_replace(",", " ", $nama);
			$nama = str_replace("'", " ", $nama);
			$nama = str_replace('"', ' ', $nama);
			$nama = preg_replace('/\s+/S', " ", $nama);


			$data = "$i $se ' $periode $se $kode_manufaktur $se $nama_manufaktur $se $barcode_13 $se $barcode_15 $se $nama $se $hpj $se ";
			$data .= "$stok_akhir $se $stok_akhir_hpp $se $stok_akhir_hpj\r\n";
			if ($csv == 0) {
				echo $data;
			} else {
				fwrite($fh, $data);
			}
		} //end while

		fclose($fh);
		echo 'Write TO File ' . $destination . $myFile . ' Success! ready for zip';


		$result = exec("gzip -9c " . $destination . $myFile . " > " . $zip_file);

		echo 'Result Zip ' . $result;


		//hapus file csvnya
		echo "/bin/rm -f $destination$myFile \n";
		$result = exec("/bin/rm -f $destination$myFile");
		echo 'DELETE CSV ' . $result;

		#echo file_get_contents($zip_file);	
		if ($isDirectDownload) {
			header('location:' . $location_save . $zip_file_name);
		} else {
			#echo file_get_contents($zip_file);	
			readfile($zip_file);
		}


		die(); //export untuk data stok only
	}


	while (list(
		$periode, $tgl_stok_awal, $kode_manufaktur, $nama_manufaktur, $barcode_13, $barcode_15, $nama, $hpp, $hpj, $hpp_stok, $hpj_stok, $stok_awal,
		$si1, $si2, $si3, $si4, $si5, $si6, $si7, $si8, $si9, $si10, $si11, $si12, $si13, $si14, $si15, $si16, $si17, $si18, $si19, $si20,
		$si21, $si22, $si23, $si24, $si25, $si26, $si27, $si28, $si29, $si30, $si31,
		$ro1, $ro2, $ro3, $ro4, $ro5, $ro6, $ro7, $ro8, $ro9, $ro10, $ro11, $ro12, $ro13, $ro14, $ro15, $ro16, $ro17, $ro18, $ro19, $ro20,
		$ro21, $ro22, $ro23, $ro24, $ro25, $ro26, $ro27, $ro28, $ro29, $ro30, $ro31,
		$so1, $so2, $so3, $so4, $so5, $so6, $so7, $so8, $so9, $so10, $so11, $so12, $so13, $so14, $so15, $so16, $so17, $so18, $so19, $so20,
		$so21, $so22, $so23, $so24, $so25, $so26, $so27, $so28, $so29, $so30, $so31,
		$ri1, $ri2, $ri3, $ri4, $ri5, $ri6, $ri7, $ri8, $ri9, $ri10, $ri11, $ri12, $ri13, $ri14, $ri15, $ri16, $ri17, $ri18, $ri19, $ri20,
		$ri21, $ri22, $ri23, $ri24, $ri25, $ri26, $ri27, $ri28, $ri29, $ri30, $ri31,
		$k1, $k2, $k3, $k4, $k5, $k6, $k7, $k8, $k9, $k10, $k11, $k12, $k13, $k14, $k15, $k16, $k17, $k18, $k19, $k20,
		$k21, $k22, $k23, $k24, $k25, $k26, $k27, $k28, $k29, $k30, $k31, $stok_akhir, $stok_akhir_hpp, $stok_akhir_hpj
	) = mysql_fetch_array($hsltemp)) {
		$row++;
		$i++;

		$nama = str_replace(";", " ", $nama);
		$nama = str_replace(",", " ", $nama);
		$nama = str_replace("'", " ", $nama);
		$nama = str_replace('"', ' ', $nama);
		$nama = preg_replace('/\s+/S', " ", $nama);
		/**/
		//Sementara
		//if($username!='superuser'){$hpp=0;$stok_akhir_hpp=0;}
		/*
		if($username=='budi-it'){
			
		}elseif($username=='uche_r'||$username=='faipusat_yati'||$username=='merchandise_dinda'||$username=='faisystem'){
			
		}else{
		   $hpp=0;$stok_akhir_hpp=0;	
		}
		*/
		if ($isShowHpp == 1) {
		} else {
			$hpp = 0;
			$hpp_stok = 0;
			$stok_akhir_hpp = 0;
		}



		$tsi = $si1 + $si2 + $si3 + $si4 + $si5 + $si6 + $si7 + $si8 + $si9 + $si10 + $si11 + $si12 + $si13 + $si14 + $si15 + $si16 + $si17 + $si18 + $si19 + $si20 +
			$si21 + $si22 + $si23 + $si24 + $si25 + $si26 + $si27 + $si28 + $si29 + $si30 + $si31;
		$tro = $ro1 + $ro2 + $ro3 + $ro4 + $ro5 + $ro6 + $ro7 + $ro8 + $ro9 + $ro10 + $ro11 + $ro12 + $ro13 + $ro14 + $ro15 + $ro16 + $ro17 + $ro18 + $ro19 + $ro20 +
			$ro21 + $ro22 + $ro23 + $ro24 + $ro25 + $ro26 + $ro27 + $ro28 + $ro29 + $ro30 + $ro31;
		$tso = $so1 + $so2 + $so3 + $so4 + $so5 + $so6 + $so7 + $so8 + $so9 + $so10 + $so11 + $so12 + $so13 + $so14 + $so15 + $so16 + $so17 + $so18 + $so19 + $so20 +
			$so21 + $so22 + $so23 + $so24 + $so25 + $so26 + $so27 + $so28 + $so29 + $so30 + $so31;
		$tri = $ri1 + $ri2 + $ri3 + $ri4 + $ri5 + $ri6 + $ri7 + $ri8 + $ri9 + $ri10 + $ri11 + $ri12 + $ri13 + $ri14 + $ri15 + $ri16 + $ri17 + $ri18 + $ri19 + $ri20 +
			$ri21 + $ri22 + $ri23 + $ri24 + $ri25 + $ri26 + $ri27 + $ri28 + $ri29 + $ri30 + $ri31;
		$tk = $k1 + $k2 + $k3 + $k4 + $k5 + $k6 + $k7 + $k8 + $k9 + $k10 + $k11 + $k12 + $k13 + $k14 + $k15 + $k16 + $k17 + $k18 + $k19 + $k20 +
			$k21 + $k22 + $k23 + $k24 + $k25 + $k26 + $k27 + $k28 + $k29 + $k30 + $k31;
		if ($csv == 0) {
			$count++;
			xlsWriteNumber($row, 1, $count);
			xlsWriteLabel($row, 2, $periode);
			xlsWriteLabel($row, 3, $tgl_stok_awal);
			xlsWriteLabel($row, 4, $kode_manufaktur);
			xlsWriteLabel($row, 5, $nama_manufaktur);
			xlsWriteLabel($row, 6, $barcode_13);
			xlsWriteLabel($row, 7, $barcode_15);
			xlsWriteLabel($row, 8, $nama);
			xlsWriteNumber($row, 9, $stok_awal);
			xlsWriteNumber($row, 10, $hpp);
			xlsWriteNumber($row, 11, $hpj);
			$column = 11;
			for ($i = 1; $i <= 31; $i++) {
				$column++;
				$var_baru = "\$ri$i";
				eval("xlsWriteNumber($row,$column,$var_baru);");
			}
			$column++;
			eval("xlsWriteNumber($row,$column,$tri);");
			for ($i = 1; $i <= 31; $i++) {
				$column++;
				$var_baru = "\$si$i";
				eval("xlsWriteNumber($row,$column,$var_baru);");
			}
			$column++;
			eval("xlsWriteNumber($row,$column,$tsi);");
			for ($i = 1; $i <= 31; $i++) {
				$column++;
				$var_baru = "\$so$i";
				eval("xlsWriteNumber($row,$column,$var_baru);");
			}
			$column++;
			eval("xlsWriteNumber($row,$column,$tso);");
			for ($i = 1; $i <= 31; $i++) {
				$column++;
				$var_baru = "\$ro$i";
				eval("xlsWriteNumber($row,$column,$var_baru);");
			}
			$column++;
			eval("xlsWriteNumber($row,$column,$tro);");
			for ($i = 1; $i <= 31; $i++) {
				$column++;
				$var_baru = "\$k$i";
				eval("xlsWriteNumber($row,$column,$var_baru);");
			}
			$column++;
			eval("xlsWriteNumber($row,$column,$tk);");
			$column++;
			eval("xlsWriteNumber($row,$column,$stok_akhir);");
			$column++;
			eval("xlsWriteNumber($row,$column,$stok_akhir_hpp);");
			$column++;
			eval("xlsWriteNumber($row,$column,$stok_akhir_hpj);");
		} else {
			$char = array('\r\n', ',', '\t');
			$nama = str_replace(';', ' ', str_replace(',', ' ', $nama));
			$data = "$i $se ' $periode $se ' $tgl_stok_awal $se $kode_manufaktur $se $nama_manufaktur $se $barcode_13 $se $barcode_15 $se $nama $se ";
			if ($isShowHpp == 1) {
				$data .= "$hpp $se ";
			}
			$data .= "$hpj $se $stok_awal $se ";
			if ($isShowHpp == 1) {
				$data .= "$hpp_stok $se ";
			}
			$data .= "$hpj_stok $se ";


			$data .= "$si1 $se $si2 $se $si3 $se $si4 $se $si5 $se $si6 $se $si7 $se $si8 $se $si9 $se $si10 $se $si11 $se $si12 $se $si13 $se $si14 $se $si15 $se ";
			$data .= "$si16 $se $si17 $se $si18 $se $si19 $se $si20 $se $si21 $se $si22 $se $si23 $se $si24 $se $si25 $se $si26 $se $si27 $se $si28 $se $si29 $se $si30 $se $si31 $se $tsi $se ";
			$data .= "$ro1 $se $ro2 $se $ro3 $se $ro4 $se $ro5 $se $ro6 $se $ro7 $se $ro8 $se $ro9 $se $ro10 $se $ro11 $se $ro12 $se $ro13 $se $ro14 $se $ro15 $se ";
			$data .= "$ro16 $se $ro17 $se $ro18 $se $ro19 $se $ro20 $se $ro21 $se $ro22 $se $ro23 $se $ro24 $se $ro25 $se $ro26 $se $ro27 $se $ro28 $se $ro29 $se $ro30 $se $ro31 $se $tro $se ";

			$data .= "$so1 $se $so2 $se $so3 $se $so4 $se $so5 $se $so6 $se $so7 $se $so8 $se $so9 $se $so10 $se $so11 $se $so12 $se $so13 $se $so14 $se $so15 $se ";
			$data .= "$so16 $se $so17 $se $so18 $se $so19 $se $so20 $se $so21 $se $so22 $se $so23 $se $so24 $se $so25 $se $so26 $se $so27 $se $so28 $se $so29 $se $so30 $se $so31 $se $tso $se ";
			$data .= "$ri1 $se $ri2 $se $ri3 $se $ri4 $se $ri5 $se $ri6 $se $ri7 $se $ri8 $se $ri9 $se $ri10 $se $ri11 $se $ri12 $se $ri13 $se $ri14 $se $ri15 $se ";
			$data .= "$ri16 $se $ri17 $se $ri18 $se $ri19 $se $ri20 $se $ri21 $se $ri22 $se $ri23 $se $ri24 $se $ri25 $se $ri26 $se $ri27 $se $ri28 $se $ri29 $se $ri30 $se $ri31 $se $tri $se ";
			$data .= "$k1 $se $k2 $se $k3 $se $k4 $se $k5 $se $k6 $se $k7 $se $k8 $se $k9 $se $k10 $se $k11 $se $k12 $se $k13 $se $k14 $se $k15 $se ";
			$data .= "$k16 $se $k17 $se $k18 $se $k19 $se $k20 $se $k21 $se $k22 $se $k23 $se $k24 $se $k25 $se $k26 $se $k27 $se $k28 $se $k29 $se $k30 $se $k31 $se $tk $se ";
			$data .= "$stok_akhir $se ";

			if ($isShowHpp == 1) {
				$data .= "$stok_akhir_hpp $se";
			}
			$data .= "$stok_akhir_hpj\r\n";
			if ($csv == 0) {
				echo $data;
			} else {
				fwrite($fh, $data);
			}
		}
	} // end while
	if ($csv == 0) {
		xlsEOF();
	} else {
		fclose($fh);
		echo 'Write TO File ' . $destination . $myFile . ' Success! ready for zip';


		$result = exec("gzip -9c " . $destination . $myFile . " > " . $zip_file);

		echo 'Result Zip ' . $result;


		//hapus file csvnya
		echo "/bin/rm -f $destination$myFile \n";
		$result = exec("/bin/rm -f $destination$myFile");
		echo 'DELETE CSV ' . $result;

		if ($isDirectDownload) {
			header('location:' . $location_save . $zip_file_name);
		} else {
			#echo file_get_contents($zip_file);	
			readfile($zip_file);
		}

		#header('location:export_rekap/'.$zip_file_name);


	}

	exit();
} else {
	#ob_start("ob_gzhandler");
	include_once "header.php";
	$isShowHpp = 0;
	$sql = "SELECT COUNT(*) ada FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
	$res = mysql_query($sql);
	list($ada) = mysql_fetch_array($res);

	if ($ada > 0) {
		$isShowHpp = 1;
	}
	$background = " background='images/footer.gif' ";
	$bgc_si = " bgcolor='#CCCCFF' ";
	$bgc_ro = " bgcolor='#FFCCFF' ";
	$bgc_so = " bgcolor='#CCFF66' ";
	$bgc_ri = " bgcolor='#33FFFF' ";
	$bgc_k = " bgcolor='#FFFF00' ";
}

set_time_limit(86400); // 24 jam
#error_reporting(E_ALL);
#ini_set('display_errors','On');
setlocale(LC_MONETARY, 'en_US');


//+++++++++++++++++++Tambahan Untuk fungsi Hitung Bulan

function firstOfMonth($month, $year)
{
	return date("Y-m-d", strtotime($month . '/01/' . $year . ' 00:00:00'));
}

function lastOfMonth($month, $year)
{
	return date("Y-m-d", strtotime('-1 second', strtotime('+1 month', strtotime($month . '/01/' . $year . ' 00:00:00'))));
}
//date_default_timezone_set('UTC');
#error_reporting(1);

function jumlahHari($month, $year)
{
	return date("j", strtotime('-1 second', strtotime('+1 month', strtotime($month . '/01/' . $year . ' 00:00:00'))));
}

// $sql_aktif = ' AND o.is_show_omset=1 ';

$array_bulan = array(
	'01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei',
	'06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
);
$tahun_skrg = date('Y');
$bulan_skrg = date('m');

@$jenis_pabrik = $_POST['jenis_pabrik'];

$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = " AND p.id_group='$jenis_pabrik' ";
}

if (isset($_REQUEST['action'])) {
	@$bulan1 = $_POST['bulan1'];
	@$tahun1 = $_POST['tahunl'];
	//echo "Kapilih";
} else {
	$bulan1 = $bulan_skrg;
	$tahun1 = $tahun_skrg;
}
$jpb = jumlahHari($bulan1, $tahun1);

$jpb = 31;

$filter_periode = $tahun1 . '-' . $bulan1;
$today = date('d');
if (empty($txtTable)) {
	$table = 'rekap_stok_manufaktur';
} else {
	$table = $txtTable;
}
if ($filter_periode != date('Y-m')) {
	$table_rekap_stok = 'rekap_stok_manufaktur';
	$today = 31; //diset itu bulan yang telah lalu edit tgl 3 mei 2013
} else {
	$table_rekap_stok = $table;
}

$now_tahun_bulan = date('Y-m');
if ($filter_periode == $now_tahun_bulan) {
	$ldps = 1; //lihat data periode sekarang
	$hari_ini = date('j') - 1;
} else {
	$ldps = 0;
	$hari_ini = $jpb;
}

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

if ($jenis_pabrik <> '') {
	$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
}



#$hari_ini=31;
#$jpb=31;
//echo "Filter Periode $filter_periode";
//echo 'Tanggal ='.$tanggal;
// @$pabrik1 = sanitasi($_POST['pabrik1']);
// echo 'Pabrik Finishing :' . $pabrik1;
// if (isset($txtmarkas)) {
// 	//$area=substr($txtmarkas,0,4);;
// }
if (@isset($_POST['pabrik1'])) {
	@$pabrik1 = sanitasi($_POST['pabrik1']);
} else {
	if (isset($_SESSION['pabrik1'])) {
		$pabrik1 = $_SESSION['pabrik1'];
	} else {
		if (!isset($_REQUEST['action'])) {
			$pabrik1 = 'ALL'; // awal buka page
			$txtmarkas = 'ALL';
		}
	}
}



@$cari_barcode = $_POST['CariBarcode'];
@$cari_minus = $_POST['CariMinus'];
@$barcode = $_POST['barcode'];
@$jumlah_data = $_POST['jumlah_data'];
@$txtjenis = $_POST['txtjenis'];
@$jenis_pabrik = $_POST['jenis_pabrik'];

@$txt_supp = $_POST['txt_supp'];
@$txt_kode_supp = trim($_POST['txt_kode_supp']);

// $pabrik1 = $_POST['pabrik'];

/* $q="SELECT $sql_cache outlet FROM user_account WHERE username='$username'";
   $s=mysql_query($q);
   list($kdgd)=mysql_fetch_array($s);
   */
$kdgd = $_SESSION['outlet'];
$group = substr($kdgd, 5, 5);
if ($group == 'O0000' || $group == 'o0000') {
	$area = substr($kdgd, 0, 4);
} else {
	$area = $kdgd;
}

#$sql_area= " AND r.kode_manufaktur like '$area%' ";
$data_tambahan = " r.kode_manufaktur like '$area%' ";
if (!empty($area_lain)) {
	$d = explode(';', $area_lain);
	$d_lain = '';
	foreach ($d as $kd_lain) {
		$kd_lain = trim($kd_lain);
		if (!empty($kd_lain)) {
			$d_lain .= " OR r.kode_manufaktur LIKE '$kd_lain%' ";
		}
	}
	$data_tambahan .= $d_lain;
}


# $sql_tambahan=" AND ( $data_tambahan ) ";	


#$sql_area= " AND r.kode_manufaktur like '$area%' ";
$sql_area = "  AND ( $data_tambahan ) ";

if (empty($jumlah_data)) {
	$jumlah_data = 200;
}
?>

<link type='text/css' href='css/demo.css' rel='stylesheet' media='screen' />
<!-- Contact Form CSS files -->
<link type='text/css' href='css/basic.css' rel='stylesheet' media='screen' />
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />
<script type='text/javascript' src='jquery.autocomplete.js'></script>
<script type='text/javascript' src='js/jquery.simplemodal.js'></script>
<script type='text/javascript' src='js/basic.js'></script>
<script src="jquery.jeditable.js" type="text/javascript"></script>
<script src="format.20110630-1100.min.js"></script>
<script src="sortable.js"></script>
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script src="app_libs/job_rekap_stok_finishing.js?d=<?php echo date('YmdHis'); ?>"></script>
<script language="JavaScript">
	$(document).ready(function() {
		//$('#debug').text('Cek');
		$("#txt_supp").autocomplete(
			"proses_complete_supplier_rabbani.php", {
				width: 350
			});

		<?php
		if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
			//echo "$('.detail1').show();"; 

			// echo "$('.detail1').hide();"; 
			echo " $('.detail').hide();";
			echo " $('.header').attr('colspan',1);";
			echo " $('.total').show();";
		} else {
			echo "$('.detail1').hide();";
			echo " $('.detail').hide();";
			echo " $('.header').attr('colspan',1);";
			echo " $('.total').show();";
		}

		if ($pabrik1 != 'ALL') {
			echo "$('#txtjenis').hide();";
		}

		?>
		//$('.stok').hide();	
		//$('#txtjenis').hide();
		$('#pabrik1').change(function() {
			if ($('#pabrik1').val() == 'ALL') {
				$('#txtjenis').show();
			} else {
				$('#txtjenis').hide();
			}
			pilihDetail();
			//alert('text');	
		});
		$('.stok').hide();
		$('#show').click(function() {
			var htmlStr = $(this).html();
			//alert('Test ' + htmlStr);
			if (htmlStr == 'Show') {
				$(this).value = 'Hide';
				$('.detail').show();
				$('.stok').show();
			} else {
				$(this).value = 'Show';
				$('.detail').hide();
				$('.stok').hide();
			}
			$('.header').attr('colspan', <?php echo ($jpb + 1) ?>);
			$('.total').show();

		});
		$('#hide').click(function() {
			$('.detail').hide();
			$('.header').attr('colspan', 1);
			$('.total').show();
			$('.stok').hide();
			$('.datarow').hide();

		});
		<?php
		if ($isShowHpp == 1) {
		?>
			$('.hpp').show();
			$('.header_stok').attr('colspan', 3);
		<?php
		} else {
		?>
			$('.hpp').hide();
			$('.header_stok').attr('colspan', 2);
		<?php
		}
		?>
	});

	if ($('#pabrik1').val() == 'ALL') {
		$('#txtjenis').show();
	} else {
		$('#txtjenis').hide();
	}
	$('#show_stok').click(function() {
		$('.stok').show();
	});
	$('#hide_stok').click(function() {
		$('.stok').hide();
	});
	$(function() {
		$(".edit_text").editable("outlet_koreksi_insert.php", {
			indicator: "<img src='ajax-loader.gif'>",
			submitdata: {
				_method: "post"
			},
			select: true,
			submit: 'Update',
			cssclass: "editable",
			width: "10",
			loadtext: 'Updating…',
			type: 'textarea',
			cols: "20",
			rows: "1.5",
			event: "dblclick"
		});
	});
</script>



<?php if ($export_to_excel <> '1') { ?>

	<?php
	$sql1 = "SELECT $sql_cache nilai,pesan FROM config_system WHERE kode_config='brko';";
	$res = mysql_query($sql1);
	list($blokAkses, $pesan) = mysql_fetch_array($res);
	if ($blokAkses == 1) {
		echo $pesan;
		//echo "<!-- SQL : $sql-->";
		include_once('footer.php');
		die();
	}

	?>
	<fieldset>
		<form method="POST" id="frmFilter" name='outlet' action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=search" onsubmit="return validation();">

			<table width="1654" style="font-size: 10pt" height="50">
				<tr height="50">
					<td width="100"><b>Periode </b></td>
					<td width="10">:</td>
					<td width="200">
						<table>
							<tr>
								<td>
									<select name="bulan1" class="form-control" style="width:150px">
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
								</td>
								<td>&nbsp;</td>
								<td>
									<select name="tahunl" class="form-control" style="width:70px">
										<?php
										for ($tahun = 2018; $tahun <= date('Y'); $tahun++) {
											if ($tahun == $tahun1) {
												echo  "<option value='$tahun' selected>$tahun</option>";
											} else {
												echo  "<option value='$tahun'>$tahun</option>";
											}
										}
										?>
									</select>
								</td>
							</tr>
						</table>
					</td>

					<td width="7">&nbsp;</td>
					<td width="633">&nbsp;
						<table width="400" border="0">
							<tr>
								<td width="150">Barcode</td>
								<td width="8">:</td>
								<td width="295"><input class="form-control" name="barcode" type="text" id="barcode" value="<?php echo $barcode ?>" size="40" maxlength="500" /></td>
							</tr>
						</table>
					</td>
					<!-- <td width="272"> -->
					<!-- <strong>
							<font color="#FF0000">Jumlah Data max 9999</font>
						</strong> -->
					<!-- <input name="jumlah_data" type="text" id="jumlah_data" value="<?php echo $jumlah_data ?>" size="4" maxlength="4" /> -->
				</tr>
				<tr height="50">
					<td height="28"><b>Jenis Pabrik</b></td>
					<td>:</td>
					<td>
						<select size="1" name="jenis_pabrik" class="form-control" style="font-size: 8pt;width:150px;" id="jenis_pabrik">
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
					<td>&nbsp;</td>
					<td width="633">&nbsp;
						<table width="400" border="0">
							<tr>
								<td width="150">Nama Produk</td>
								<td width="8">:</td>
								<td width="295"><input class="form-control" name="txt_nama" type="text" id="txt_nama" value="<?php echo $txt_nama ?>" size="40" maxlength="50" /></td>
							</tr>
						</table>
					</td>
					<!-- <td>Style :
						<input name="txt_model" type="text" id="txt_model" value="<?php echo $nama_model ?>" size="30" onclick="clearBarcode();" /> -->
				</tr>
				<!-- <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td ondblclick="showTable();"> <input name="txt_supp" type="text" id="txt_supp" value="<?php echo $txt_supp ?>" size="40" maxlength="50" /><br />
						Kode Supplier :
						<input name="txt_kode_supp" type="text" id="txt_kode_supp" value="<?php echo $txt_kode_supp; ?>" size="3" maxlength="3" />
					</td>
					<td>&nbsp;</td>
				</tr> -->
				<tr height="50">
					<td style="font-size: 8pt;width:50px;" height="28"><b>Pabrik</b></td>
					<td width:6px;>:</td>
					<td>
						<select size="1" name="pabrik1" style="font-size: 8pt;width:300px;" id="pabrik1">
							<option value="--">--Silahkan Pilih Pabrik--</option>
							<?php
							$sql = "SELECT id, nama from pabrik where id $_pabrik AND status='1'";
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

							if (trim($pabrik1) == 'ALL') {
								echo '<option value="ALL" selected>--ALL--</option>';
							} else {
								echo '<option value="ALL">--ALL--</option>';
							}
							?>
						</select>
					</td>

					<td></td>
					<td>
						<table width="100" border="0">
							<tr>
								<td width="150">&nbsp;</td>
								<td width="8">&nbsp;</td>
								<td>
									<button type="submit" class="btn btn-success btn-block" name="CariBarcode" id="CariBarcode" value="Lihat Brdasarkan Barcode" style="font-size: 8pt">Lihat Berdasarkan Barcode</button>
								</td>
								<td>&nbsp;</td>
								<td>
									<button type="submit" class="btn btn-success btn-block" name="CariMinus" id="CariMinus" value="Lihat Data Minus" style="font-size: 8pt">Lihat Data Minus</button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr height="50">
					<td></td>
					<td></td>
					<td width='53'>
						<table width="100" border="0">
							<tr>
								<td>
									<button type="submit" class="btn btn-primary btn-block" value="lihat" name="btnLihat" style="font-size: 8pt" id="btnLihat" width="53"> Lihat </button>
								</td>
								<td>&nbsp;</td>
								<td>
									<button type="submit" class="btn btn-warning btn-block" value="export" name="submit" style="font-size: 8pt"> Export </button>
								</td>
								<td>&nbsp;</td>
								<td>
									<b>xls
										<input name="cb_xls" type="checkbox" id="cb_xls" value="1" style="display:none" />
									</b>
								</td>
							</tr>

						</table>
					</td>
				</tr>
				<tr height="50">
					<td>
						<input name="cb_detail" type="checkbox" id="cb_detail" value="1" onchange="pilihDetail();" style="display:none" />
						<label for="cb_detail">Detail</label>
					</td>
					<td>:</td>
					<td colspan="3"><b>
							<a href="#" id="show">Show</a>&nbsp;&nbsp;<a href="#" id="hide">Hide</a>&nbsp;&nbsp;<a href="#" id="show_stok">.</a>&nbsp;&nbsp;<a href="#" id="hide_stok">,</a>
						</b>&nbsp;<input name="txtTable" type="text" id="txtTable" value="<?php echo $table; ?>" style="display:none;" />
						<b>
							<input type="submit" value="Export Stok Akhir" name="btnExportDataStok" style="font-size: 8pt;display:none;" id="btnExportDataStok" />
							&nbsp;&nbsp;<input type="button" value="Pilih Outlet Export" name="btnpilih" style="font-size: 8pt;display:none;" id="btnpilih" onclick="showPilihan()" ; />

						</b>
					</td>
					<!-- <td><input type="button" name="cariStyle" id="cariStyle" value="Cari Model" onclick="cariProduk();" style="font-size: 8pt" /> -->
				</tr>
				<tr height="50">
					<!-- <td>&nbsp;</td> -->
					<td colspan="4">
						<table>
							<tr>
								<td>
									<button type="button" class="btn btn-warning btn-block" value="Export hanya Stok Akhir" name="btnExportDataStok3" style="font-size: 8pt" id="btnExportDataStok3" onclick="exportStokAkhir()">Export Hanya Stock Akhir</button>
								</td>
								<td>&nbsp;</td>
								<td>
									<span id="id_tanggal" <?php if ($username == 'budi-it') {
															} else { /*echo 'style="display:none;"';*/
															} ?>> Tanggal </span>
								</td>
								<td>&nbsp;</td>
								<td>:</td>
								<td>&nbsp;</td>
								<td>
									<select name="tanggal" id="tanggal" style="width:70px">
										<?php
										for ($i = 0; $i <= 31; $i++) {
											$selected = '';
											if ($tanggal == $i) {
												$selected = 'selected';
											}
										?>
											<option value='<?php echo $i; ?>' <?php echo $selected; ?>><?php echo $i; ?></option>
										<?php
										}
										?>
									</select>
								</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>
									Lengkap Dengan Proses
									<input type="checkbox" name="cbProsesLengkap" id="cbProsesLengkap" value="1" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<span style="display:none" id="exprt">
				<h4>Kode Pabrik Yang akan diexport</h4>
				<input name="outlet_export" type="text" id="otl_export" value="" readonly="readonly" size="200" maxlength="500" style="color:#03F;background-color:#0F6" />
			</span>
		</form>
	</fieldset>
<?php } // end if $export_to_excel<>'1''



?>
<div id="pil_reshare_export" style="padding:0px; background:#fff;display:none;">
	<h3>Data Pabrik yang akan dieksport</h3>
	<table width="370" border="1">
		<tr>
			<td width="70">Pilih &nbsp;&nbsp;<input name="cb_all" type="checkbox" value="" id="cb_all" onclick="updateAktifAll();" /></td>
			<td width="300">Nama</td>
		</tr>
		<?php
		foreach ($arrayReshare as  $key => $value) {
		?>
			<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
				<td><input name="<?php echo 'cb_' . $key ?>" type="checkbox" value="1" class="cb_outlet" id="<?php echo 'cb_' . $key ?>" onchange="addReshare('<?php echo $key ?>');" /></td>
				<td><?php echo "[ $key ] $value"; ?></td>
			</tr>
		<?php
		}

		?>
		<tr bgcolor="#0099CC">
			<!-- <td>&nbsp;</td>
			<td>&nbsp;</td> -->
		</tr>
	</table>
</div>
<style>
	.hpp {
		display: none;
	}

	<?php
	if (strtoupper($kdgd) == 'M008-O0000') {
		echo '#listProdukPilihan{display:none;}';
	}
	?>
</style>
<?php
//tambahan 27 sept 2014
if ($_GET['action'] == 'search') {
} else {
	include_once("footer.php");
	die();
}

?>
<input type="button" value="Export Stok Akhir" name="btnExportDataStok2" style="font-size: 8pt" id="btnExportDataStok2" onclick="exportStok()" />
<table class="table table-bordered" width="100%" style="font-size: 8pt" height="126">
	<thead>
		<tr>
			<td <?php echo $background ?> align="center" width="41">&nbsp;</td>
			<td <?php echo $background ?> align="center" width="49">&nbsp;</td>
			<td <?php echo $background ?> align="center" width="50">&nbsp;</td>
			<td <?php echo $background ?> align="center" width="93">&nbsp;</td>
			<td <?php echo $background ?> align="center" width="93" class="detail1">&nbsp;</td>
			<td <?php echo $background ?> align="center" width="93" class="detail1">&nbsp;</td>
			<td <?php echo $background ?> align="center" width="93" class="detail1">&nbsp;</td>
			<td height="24" colspan="3" align="center" <?php echo $background ?> id="header_stok" class="header_stok"><b>(O / B) Stok Awal </b></td>

			<td colspan="<?php echo ($jpb + 1) ?>" align="center" <?php echo $bgc_si ?> class="header">STOCK IN</td>
			<td colspan="<?php echo ($jpb + 1) ?>" align="center" <?php echo $bgc_ro ?> class="header">OUT RETUR</td>
			<td colspan="<?php echo ($jpb + 1) ?>" align="center" <?php echo $bgc_so ?> class="header">STOCK OUT</td>
			<td colspan="<?php echo ($jpb + 1) ?>" align="center" <?php echo $bgc_ri ?> class="header">IN RETUR</td>
			<td colspan="<?php echo ($jpb + 1) ?>" align="center" <?php echo $bgc_k ?> class="header">ADJUSTMENT</td>
			<td colspan="3" align="center" <?php echo $background ?> class="header_stok"><b>(E / B ) Stok Akhir</b></td>
			<td colspan="<?php echo $hari_ini ?>" align="center" <?php echo $background ?> class="stok"><b>Stok Akhir Pertanggal</b></td>

		</tr>
	</thead>
	<thead>
		<tr>
			<td <?php echo $background ?> align="center" width="41"><strong>NO</strong></td>
			<td <?php echo $background ?> align="center" width="49"><strong>Periode</strong></td>
			<td <?php echo $background ?> align="center" width="50"><b>Kode
					Pabrik</b></td>
			<td <?php echo $background ?> align="center" width="93"><b>Nama
					Pabrik</b></td>
			<td <?php echo $background ?> align="center" width="93" class="detail1"><strong>Barcode 13</strong></td>
			<td <?php echo $background ?> align="center" width="93" class="detail1"><strong>Barcode 15</strong></td>
			<td <?php echo $background ?> align="center" width="93" class="detail1"><strong>Produk</strong></td>
			<td width="62" align="center" <?php echo $background ?>><strong>Qty</strong></td>
			<td width="62" align="center" <?php echo $background ?> class="hpp"><strong>Hpp Total</strong></td>
			<td width="62" height="22" align="center" <?php echo $background ?>><strong>Hpj Total</strong></td>
			<?php
			//$i=0;
			/*
	   bgcolor='#CCCCFF'
	   bgcolor='#FFCCFF'
	   bgcolor='#CCFF66'
	   bgcolor='#33FFFF'
	   bgcolor='#FFFF00'
	   */


			for ($i = 1; $i <= $jpb; $i++) {
				if (($ldps == 1) && ($i == $today)) {
					echo "<td width=\"47\" align=\"center\"  bgcolor=\"#FF0000\" class=\"detail\">$i</td>";
				} else {
					echo "<td width=\"47\" align=\"center\" $bgc_si  class=\"detail\">$i</td>";
				}
			}
			?>

			<td width="47" align="center" <?php echo $background ?> class="total">Total</td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				if (($ldps == 1) && ($i == $today)) {
					echo "<td width=\"47\" align=\"center\"  bgcolor=\"#FF0000\" class=\"detail\">$i</td>";
				} else {
					echo "<td width=\"47\" align=\"center\" $bgc_ro   class=\"detail\">$i</td>";
				}
			}
			?>
			<td width="47" align="center" <?php echo $background ?> class="total">Total</td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				if (($ldps == 1) && ($i == $today)) {
					echo "<td width=\"47\" align=\"center\"  bgcolor=\"#FF0000\" class=\"detail\">$i</td>";
				} else {
					echo "<td width=\"47\" align=\"center\" $bgc_so  class=\"detail\">$i</td>";
				}
			}
			?>
			<td width="47" align="center" <?php echo $background ?> class="total">Total</td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				if (($ldps == 1) && ($i == $today)) {
					echo "<td width=\"47\" align=\"center\"  bgcolor=\"#FF0000\" class=\"detail\">$i</td>";
				} else {
					echo "<td width=\"47\" align=\"center\" $bgc_ri  class=\"detail\">$i</td>";
				}
			}
			?>
			<td width="47" align="center" <?php echo $background ?> class="total">Total</td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				if (($ldps == 1) && ($i == $today)) {
					echo "<td width=\"47\" align=\"center\"  bgcolor=\"#FF0000\" class=\"detail\">$i</td>";
				} else {
					echo "<td width=\"47\" align=\"center\" $bgc_k   class=\"detail\">$i</td>";
				}
			}
			?>
			<td width="47" align="center" <?php echo $background ?> class="total">Total</td>
			<td align="center" <?php echo $background ?>><strong>Qty</strong></td>
			<td align="center" <?php echo $background ?> class="hpp"><strong>Hpp Total</strong></td>
			<td align="center" <?php echo $background ?>><strong>Hpj Total</strong></td>
			<?php
			for ($i = 1; $i <= $hari_ini; $i++) {
				echo "<td align='center' $background class='stok'><strong> $i </strong></td>";
			}

			?>
		</tr>
	</thead>
	<?php
	if (isset($_GET['hal'])) $hal = $_GET['hal'];
	else $hal = 0;
	$jmlHal = 150000;
	$page = $hal;
	@$search = $_GET[action];



	echo "<h5>Cari Barcode Value $cari_barcode</h5>";
	if ($search == "search") {
		session_start();
		$tambah = "&action=search";
		if ($_POST['pabrik1'] != "") {
			$pabrik1 = $_POST['pabrik1'];
			$_SESSION['pabrik1'] = $pabrik1;
			$_SESSION['bulan1'] = $tgl1;
			$_SESSION['tahun1'] = $tgl2;
		} else {
			$pabrik1 = $_SESSION['pabrik1'];
		}
		if (isset($txtmarkas)) {
			// $area=substr($txtmarkas,0,4);	
		}

		if ($area == 'ALL') {
			$area = '';
		}
		if (trim($pabrik1) == 'ALL') {
			// $tambahan = " And r.jenis like '$txtjenis%' and r.kode_manufaktur not like '%O0000' and r.kode_manufaktur like '$area%'";
			$pabrik1 = '';
		}
		if ($jenis_pabrik <> '') {
			$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
		}

		//$sql1="SELECT count(tanggal) FROM $table  where kode_manufaktur like '$pabrik1%' and tanggal between '$tgl1' and '$tgl2' $tambahan";
		// $sql_inner_produk = '';
		$sql_tbhn2 = '';

		if (!empty($txt_kode_supp)) {
			// $sql_inner_produk = " INNER JOIN produk p on p.kode=r.barcode_15 ";
			$sql_tbhn2 .= " AND p.kode_supplier like '$txt_kode_supp%' ";
		}

		if (!empty($txt_organization)) {
			$sql_tbhn2 .= " AND r.kode_manufaktur like '$txt_organization%' ";
		}

		// if (!empty($txtmarkas)) {
		// 	if ($txtmarkas != 'ALL') {
		// 		// $sql_tbhn2 .= " AND o.id_markas = '$txtmarkas' ";
		// 	}
		// }

		if ($pabrik1 == 'MSB1-O0001') {
			$sql_aktif = '';
		}

		if (!empty($txt_nama)) {
			$sql_tbhn2 .= " AND r.nama like '%$txt_nama%' ";
		}



		if (!empty($txtprodukpilihan)) {
			if (!empty($barcode)) {
				#$tambahanPencarianBerdasarkanBarcode=" and ( barcode_15 like '$barcode%') ";
				$dt = explode(',', $barcode);
				$lstbr = '';
				foreach ($dt as $singleBarcode) {
					$singleBarcode = trim($singleBarcode);
					if (!empty($singleBarcode)) {
						$singleBarcode = trim($singleBarcode);
						$lstbr .= "barcode_15 like '$singleBarcode%' OR ";
					}
				}
				if (!empty($lstbr)) {
					$lstbr = substr($lstbr, 0, strlen($lstbr) - 3);
				}
				$tambahanPencarianBerdasarkanBarcode = " and ( $lstbr) ";
			} else {
				$tambahanPencarianBerdasarkanBarcode = " ";
			}
			if (isset($cari_barcode)) {
				$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
				$sql .= "	IF(LENGTH(r.barcode_13)=0,r.barcode_15,r.barcode_13),r.barcode_15,r.nama,(hpp*stok_awal),(r.hpj*r.stok_awal),r.stok_awal, ";
				$sql .= "	r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20, ";
				$sql .= "	r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31, ";
				$sql .= "	r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20, ";
				$sql .= "	r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31, ";
				$sql .= "	r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20, ";
				$sql .= "	r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31, ";
				$sql .= "	r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20, ";
				$sql .= "	r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31, ";
				$sql .= "	r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20, ";
				$sql .= "	r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,(r.hpp* r.stok_akhir),(r.hpj* r.stok_akhir),p.id_group ";
				$sql .= "	FROM $table_rekap_stok as r $sql_inner_group 
							WHERE r.kode_manufaktur like '$pabrik1%'  $tambahan and r.periode like '$filter_periode%'  AND pp.status=1 and pp.pilihan='$txtprodukpilihan'";
				$sql .= "	 $filter_minus $tambahanPencarianBerdasarkanBarcode	  $sql_area   $filter_pabrik $sql_aktif  $sql_tbhn2 order by kode_manufaktur limit $jumlah_data" . '; -- akses 41';
			} else {
				$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
				$sql .= " IF(LENGTH(r.barcode_13)=0,r.barcode_15,r.barcode_13),r.barcode_15,r.nama,";
				$sql .= "sum(hpp*stok_awal),sum(hpj*stok_awal),sum(stok_awal), ";
				$sql .= " SUM(r.si1),SUM(r.si2),SUM(r.si3),SUM(r.si4),SUM(r.si5),SUM(r.si6),SUM(r.si7),SUM(r.si8),SUM(r.si9),SUM(r.si10), ";
				$sql .= " SUM(r.si11),SUM(r.si12),SUM(r.si13),SUM(r.si14),SUM(r.si15),SUM(r.si16),SUM(r.si17),SUM(r.si18),SUM(r.si19),SUM(r.si20), ";
				$sql .= " SUM(r.si21),SUM(r.si22),SUM(r.si23),SUM(r.si24),SUM(r.si25),SUM(r.si26),SUM(r.si27),SUM(r.si28),SUM(r.si29),SUM(r.si30),SUM(r.si31), ";
				$sql .= " SUM(r.ro1),SUM(r.ro2),SUM(r.ro3),SUM(r.ro4),SUM(r.ro5),SUM(r.ro6),SUM(r.ro7),SUM(r.ro8),SUM(r.ro9),SUM(r.ro10), ";
				$sql .= " SUM(r.ro11),SUM(r.ro12),SUM(r.ro13),SUM(r.ro14),SUM(r.ro15),SUM(r.ro16),SUM(r.ro17),SUM(r.ro18),SUM(r.ro19),SUM(r.ro20), ";
				$sql .= " SUM(r.ro21),SUM(r.ro22),SUM(r.ro23),SUM(r.ro24),SUM(r.ro25),SUM(r.ro26),SUM(r.ro27),SUM(r.ro28),SUM(r.ro29),SUM(r.ro30),SUM(r.ro31), ";
				$sql .= " SUM(r.so1),SUM(r.so2),SUM(r.so3),SUM(r.so4),SUM(r.so5),SUM(r.so6),SUM(r.so7),SUM(r.so8),SUM(r.so9),SUM(r.so10), ";
				$sql .= " SUM(r.so11),SUM(r.so12),SUM(r.so13),SUM(r.so14),SUM(r.so15),SUM(r.so16),SUM(r.so17),SUM(r.so18),SUM(r.so19),SUM(r.so20), ";
				$sql .= " SUM(r.so21),SUM(r.so22),SUM(r.so23),SUM(r.so24),SUM(r.so25),SUM(r.so26),SUM(r.so27),SUM(r.so28),SUM(r.so29),SUM(r.so30),SUM(r.so31), ";
				$sql .= " SUM(r.ri1),SUM(r.ri2),SUM(r.ri3),SUM(r.ri4),SUM(r.ri5),SUM(r.ri6),SUM(r.ri7),SUM(r.ri8),SUM(r.ri9),SUM(r.ri10), ";
				$sql .= " SUM(r.ri11),SUM(r.ri12),SUM(r.ri13),SUM(r.ri14),SUM(r.ri15),SUM(r.ri16),SUM(r.ri17),SUM(r.ri18),SUM(r.ri19),SUM(r.ri20), ";
				$sql .= " SUM(r.ri21),SUM(r.ri22),SUM(r.ri23),SUM(r.ri24),SUM(r.ri25),SUM(r.ri26),SUM(r.ri27),SUM(r.ri28),SUM(r.ri29),SUM(r.ri30),SUM(r.ri31), ";
				$sql .= " SUM(r.k1),SUM(r.k2),SUM(r.k3),SUM(r.k4),SUM(r.k5),SUM(r.k6),SUM(r.k7),SUM(r.k8),SUM(r.k9),SUM(r.k10), ";
				$sql .= " SUM(r.k11),SUM(r.k12),SUM(r.k13),SUM(r.k14),SUM(r.k15),SUM(r.k16),SUM(r.k17),SUM(r.k18),SUM(r.k19),SUM(r.k20), ";
				$sql .= " SUM(r.k21),SUM(r.k22),SUM(r.k23),SUM(r.k24),SUM(r.k25),SUM(r.k26),SUM(r.k27),SUM(r.k28),SUM(r.k29),SUM(r.k30),SUM(r.k31), ";
				$sql .= " sum(r.stok_akhir),sum(r.hpp*r.stok_akhir),sum(r.hpj*r.stok_akhir),p.id_group ";
				$sql .= "	FROM $table_rekap_stok as r $sql_inner_group 
							WHERE r.kode_manufaktur like '$pabrik1%'  $tambahan and r.periode like '$filter_periode%'  AND pp.status=1 and pp.pilihan='$txtprodukpilihan'";
				$sql .= "	 $filter_minus $tambahanPencarianBerdasarkanBarcode	  $sql_area  $filter_pabrik  $sql_aktif  $sql_tbhn2 
				group by DATE_FORMAT(r.periode,'%M-%Y'),r.kode_manufaktur  order by kode_manufaktur limit $jumlah_data" . '; -- akses 41group';
			}
		} elseif ($detail == 1) {
			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
			$sql .= " r.barcode_13,r.barcode_15,r.nama,(r.hpp*r.stok_awal),(r.hpj*r.stok_awal),r.stok_awal, ";
			$sql .= "r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20, ";
			$sql .= "r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31, ";
			$sql .= "r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20, ";
			$sql .= "r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31, ";
			$sql .= "r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20, ";
			$sql .= "r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31, ";
			$sql .= "r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20, ";
			$sql .= "r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31, ";
			$sql .= "r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20, ";
			$sql .= "r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,(r.hpp*r.stok_akhir),(r.hpj*r.stok_akhir),p.id_group ";
			$sql .= "FROM $table_rekap_stok r $sql_inner_group WHERE kode_manufaktur='$pabrik1'  and periode like '$filter_periode%' $sql_area  $filter_pabrik  $sql_tbhn2 ";
			$sql .= "order by kode_manufaktur LIMIT " . ($page * $jmlHal) . "," . $jmlHal;
		} elseif ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
			if (isset($cari_minus)) {
				$filter_minus = ' AND stok_akhir<0';
			} else {
				$filter_minus = '';
			}
			if (!empty($barcode)) {
				#$tambahanPencarianBerdasarkanBarcode=" and ( barcode_15 like '$barcode%') ";
				$dt = explode(',', $barcode);
				$lstbr = '';
				foreach ($dt as $singleBarcode) {
					$singleBarcode = trim($singleBarcode);
					if (!empty($singleBarcode)) {

						$lstbr .= "barcode_15 like '$singleBarcode%' OR ";
					}
				}
				if (!empty($lstbr)) {
					$lstbr = substr($lstbr, 0, strlen($lstbr) - 3);
				}
				$tambahanPencarianBerdasarkanBarcode = " and ( $lstbr) ";
			} else {
				$tambahanPencarianBerdasarkanBarcode = " ";
			}


			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
			$sql .= "	IF(LENGTH(r.barcode_13)=0,r.barcode_15,r.barcode_13),r.barcode_15,r.nama,(r.hpp*r.stok_awal),(r.hpj*r.stok_awal),r.stok_awal, ";
			$sql .= "r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20, ";
			$sql .= "r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31, ";
			$sql .= "r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20, ";
			$sql .= "r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31, ";
			$sql .= "r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20, ";
			$sql .= "r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31, ";
			$sql .= "r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20, ";
			$sql .= "r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31, ";
			$sql .= "r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20, ";
			$sql .= "r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,(r.hpp*r.stok_akhir),(r.hpj*r.stok_akhir),p.id_group ";
			$sql .= "	FROM $table_rekap_stok r $sql_inner_group WHERE r.kode_manufaktur like '$pabrik1%'  $sql_aktif $tambahan $filter_pabrik and r.periode like '$filter_periode%'  ";
			$sql .= "	 $filter_minus $tambahanPencarianBerdasarkanBarcode	 $sql_area $sql_tbhn2 order by r.kode_manufaktur limit $jumlah_data" . '; -- akses 3';
		} else {

			if (!empty($barcode)) {
				#$sql_tbhn2.=" AND r.barcode_15 like '$barcode%'";

				$dt = explode(',', $barcode);
				$lstbr = '';
				foreach ($dt as $singleBarcode) {
					$singleBarcode = trim($singleBarcode);
					if (!empty($singleBarcode)) {
						$lstbr .= "r.barcode_15 like '$singleBarcode%' OR ";
					}
				}
				if (!empty($lstbr)) {
					$lstbr = substr($lstbr, 0, strlen($lstbr) - 3);
				}

				$sql_tbhn2 .= " AND ( $lstbr )";
			}


			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
			$sql .= "barcode_13,barcode_15,r.nama,sum(hpp*stok_awal),sum(hpj*stok_awal),sum(stok_awal), ";
			$sql .= " SUM(r.si1),SUM(r.si2),SUM(r.si3),SUM(r.si4),SUM(r.si5),SUM(r.si6),SUM(r.si7),SUM(r.si8),SUM(r.si9),SUM(r.si10), ";
			$sql .= " SUM(r.si11),SUM(r.si12),SUM(r.si13),SUM(r.si14),SUM(r.si15),SUM(r.si16),SUM(r.si17),SUM(r.si18),SUM(r.si19),SUM(r.si20), ";
			$sql .= " SUM(r.si21),SUM(r.si22),SUM(r.si23),SUM(r.si24),SUM(r.si25),SUM(r.si26),SUM(r.si27),SUM(r.si28),SUM(r.si29),SUM(r.si30),SUM(r.si31), ";
			$sql .= " SUM(r.ro1),SUM(r.ro2),SUM(r.ro3),SUM(r.ro4),SUM(r.ro5),SUM(r.ro6),SUM(r.ro7),SUM(r.ro8),SUM(r.ro9),SUM(r.ro10), ";
			$sql .= " SUM(r.ro11),SUM(r.ro12),SUM(r.ro13),SUM(r.ro14),SUM(r.ro15),SUM(r.ro16),SUM(r.ro17),SUM(r.ro18),SUM(r.ro19),SUM(r.ro20), ";
			$sql .= " SUM(r.ro21),SUM(r.ro22),SUM(r.ro23),SUM(r.ro24),SUM(r.ro25),SUM(r.ro26),SUM(r.ro27),SUM(r.ro28),SUM(r.ro29),SUM(r.ro30),SUM(r.ro31), ";
			$sql .= " SUM(r.so1),SUM(r.so2),SUM(r.so3),SUM(r.so4),SUM(r.so5),SUM(r.so6),SUM(r.so7),SUM(r.so8),SUM(r.so9),SUM(r.so10), ";
			$sql .= " SUM(r.so11),SUM(r.so12),SUM(r.so13),SUM(r.so14),SUM(r.so15),SUM(r.so16),SUM(r.so17),SUM(r.so18),SUM(r.so19),SUM(r.so20), ";
			$sql .= " SUM(r.so21),SUM(r.so22),SUM(r.so23),SUM(r.so24),SUM(r.so25),SUM(r.so26),SUM(r.so27),SUM(r.so28),SUM(r.so29),SUM(r.so30),SUM(r.so31), ";
			$sql .= " SUM(r.ri1),SUM(r.ri2),SUM(r.ri3),SUM(r.ri4),SUM(r.ri5),SUM(r.ri6),SUM(r.ri7),SUM(r.ri8),SUM(r.ri9),SUM(r.ri10), ";
			$sql .= " SUM(r.ri11),SUM(r.ri12),SUM(r.ri13),SUM(r.ri14),SUM(r.ri15),SUM(r.ri16),SUM(r.ri17),SUM(r.ri18),SUM(r.ri19),SUM(r.ri20), ";
			$sql .= " SUM(r.ri21),SUM(r.ri22),SUM(r.ri23),SUM(r.ri24),SUM(r.ri25),SUM(r.ri26),SUM(r.ri27),SUM(r.ri28),SUM(r.ri29),SUM(r.ri30),SUM(r.ri31), ";
			$sql .= " SUM(r.k1),SUM(r.k2),SUM(r.k3),SUM(r.k4),SUM(r.k5),SUM(r.k6),SUM(r.k7),SUM(r.k8),SUM(r.k9),SUM(r.k10), ";
			$sql .= " SUM(r.k11),SUM(r.k12),SUM(r.k13),SUM(r.k14),SUM(r.k15),SUM(r.k16),SUM(r.k17),SUM(r.k18),SUM(r.k19),SUM(r.k20), ";
			$sql .= " SUM(r.k21),SUM(r.k22),SUM(r.k23),SUM(r.k24),SUM(r.k25),SUM(r.k26),SUM(r.k27),SUM(r.k28),SUM(r.k29),SUM(r.k30),SUM(r.k31), ";
			$sql .= " sum(r.stok_akhir),sum(r.hpp*r.stok_akhir),sum(r.hpj*r.stok_akhir),p.id_group ";
			$sql .= "FROM $table_rekap_stok as r $sql_inner_group WHERE r.kode_manufaktur like '$pabrik1%' $tambahan AND r.periode like '$filter_periode%' $filter_barcode $filter_pabrik $sql_area $sql_aktif $sql_tbhn2";
			$sql .= "GROUP BY r.kode_manufaktur,DATE_FORMAT(r.periode,'%Y-%m') order by r.kode_manufaktur LIMIT " . ($page * $jmlHal) . "," . $jmlHal . '; -- akses 42';
		}
	} else {

		//$sql1="SELECT count(tanggal) FROM $table  where kode_manufaktur like '$pabrik1%' and tanggal between '$tgl1' and '$tgl2' And jenis=1";
		if ($detail == 1) {
			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
			$sql .= "r.barcode_13,r.barcode_15,r.nama,(r.hpp*r.stok_awal),(r.hpj*r.stok_awal),r.stok_awal, ";
			$sql .= "r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20, ";
			$sql .= "r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31, ";
			$sql .= "r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20, ";
			$sql .= "r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31, ";
			$sql .= "r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20, ";
			$sql .= "r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31, ";
			$sql .= "r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20, ";
			$sql .= "r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31, ";
			$sql .= "r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20, ";
			$sql .= "r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,(r.hpp*r.stok_akhir),(r.hpj*r.stok_akhir),p.id_group ";
			$sql .= "FROM $table_rekap_stok r $sql_inner_group WHERE r.kode_manufaktur='$pabrik1' $filter_pabrik and o.is_show_omset=1 and r.periode like '$filter_periode%' $sql_tbhn2 order by r.kode_manufaktur LIMIT " . ($page * $jmlHal) . "," . $jmlHal . '; -- akses 3';
		} elseif ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
			if (isset($cari_minus)) {
				$filter_minus = ' AND stok_akhir<0';
			} else {
				$filter_minus = '';
			}
			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
			$sql .= "	IF(LENGTH(r.barcode_13)=0,r.barcode_15,r.barcode_13),r.barcode_15,r.nama,(r.hpp*r.stok_awal),(r.hpj*r.stok_awal),r.stok_awal, ";
			$sql .= "r.si1,r.si2,r.si3,r.si4,r.si5,r.si6,r.si7,r.si8,r.si9,r.si10,r.si11,r.si12,r.si13,r.si14,r.si15,r.si16,r.si17,r.si18,r.si19,r.si20, ";
			$sql .= "r.si21,r.si22,r.si23,r.si24,r.si25,r.si26,r.si27,r.si28,r.si29,r.si30,r.si31, ";
			$sql .= "r.ro1,r.ro2,r.ro3,r.ro4,r.ro5,r.ro6,r.ro7,r.ro8,r.ro9,r.ro10,r.ro11,r.ro12,r.ro13,r.ro14,r.ro15,r.ro16,r.ro17,r.ro18,r.ro19,r.ro20, ";
			$sql .= "r.ro21,r.ro22,r.ro23,r.ro24,r.ro25,r.ro26,r.ro27,r.ro28,r.ro29,r.ro30,r.ro31, ";
			$sql .= "r.so1,r.so2,r.so3,r.so4,r.so5,r.so6,r.so7,r.so8,r.so9,r.so10,r.so11,r.so12,r.so13,r.so14,r.so15,r.so16,r.so17,r.so18,r.so19,r.so20, ";
			$sql .= "r.so21,r.so22,r.so23,r.so24,r.so25,r.so26,r.so27,r.so28,r.so29,r.so30,r.so31, ";
			$sql .= "r.ri1,r.ri2,r.ri3,r.ri4,r.ri5,r.ri6,r.ri7,r.ri8,r.ri9,r.ri10,r.ri11,r.ri12,r.ri13,r.ri14,r.ri15,r.ri16,r.ri17,r.ri18,r.ri19,r.ri20, ";
			$sql .= "r.ri21,r.ri22,r.ri23,r.ri24,r.ri25,r.ri26,r.ri27,r.ri28,r.ri29,r.ri30,r.ri31, ";
			$sql .= "r.k1,r.k2,r.k3,r.k4,r.k5,r.k6,r.k7,r.k8,r.k9,r.k10,r.k11,r.k12,r.k13,r.k14,r.k15,r.k16,r.k17,r.k18,r.k19,r.k20, ";
			$sql .= "r.k21,r.k22,r.k23,r.k24,r.k25,r.k26,r.k27,r.k28,r.k29,r.k30,r.k31,r.stok_akhir,(r.hpp*r.stok_akhir),(r.hpj*r.stok_akhir),p.id_group ";
			$sql .= "	FROM $table_rekap_stok r $sql_inner_group WHERE r.kode_manufaktur like '$pabrik1%' $tambahan $filter_pabrik and r.periode like '$filter_periode%'  ";
			$sql .= " AND r.kode_manufaktur like 'M0%' and length(trim(r.kode_manufaktur))=10  ";
			$sql .= "	and ( r.barcode_15 like '$barcode%') $filter_minus  $sql_area  $sql_tbhn2 order by r.kode_manufaktur limit $jumlah_data; -- akses 2";
		} else { //
			$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y'),DATE_FORMAT(r.tgl_stok_awal,'%d %M %Y'),upper(r.kode_manufaktur),r.nama_manufaktur, ";
			$sql .= "r.barcode_13,r.barcode_15,r.nama,sum(r.hpp*r.stok_awal),sum(r.hpj*r.stok_awal),sum(r.stok_awal), ";
			$sql .= " SUM(r.si1),SUM(r.si2),SUM(r.si3),SUM(r.si4),SUM(r.si5),SUM(r.si6),SUM(r.si7),SUM(r.si8),SUM(r.si9),SUM(r.si10), ";
			$sql .= " SUM(r.si11),SUM(r.si12),SUM(r.si13),SUM(r.si14),SUM(r.si15),SUM(r.si16),SUM(r.si17),SUM(r.si18),SUM(r.si19),SUM(r.si20), ";
			$sql .= "SUM(r.si21),SUM(r.si22),SUM(r.si23),SUM(r.si24),SUM(r.si25),SUM(r.si26),SUM(r.si27),SUM(r.si28),SUM(r.si29),SUM(r.si30),SUM(r.si31), ";
			$sql .= " SUM(r.ro1),SUM(r.ro2),SUM(r.ro3),SUM(r.ro4),SUM(r.ro5),SUM(r.ro6),SUM(r.ro7),SUM(r.ro8),SUM(r.ro9),SUM(r.ro10), ";
			$sql .= " SUM(r.ro11),SUM(r.ro12),SUM(r.ro13),SUM(r.ro14),SUM(r.ro15),SUM(r.ro16),SUM(r.ro17),SUM(r.ro18),SUM(r.ro19),SUM(r.ro20), ";
			$sql .= " SUM(r.ro21),SUM(r.ro22),SUM(r.ro23),SUM(r.ro24),SUM(r.ro25),SUM(r.ro26),SUM(r.ro27),SUM(r.ro28),SUM(r.ro29),SUM(r.ro30),SUM(r.ro31), ";
			$sql .= " SUM(r.so1),SUM(r.so2),SUM(r.so3),SUM(r.so4),SUM(r.so5),SUM(r.so6),SUM(r.so7),SUM(r.so8),SUM(r.so9),SUM(r.so10), ";
			$sql .= " SUM(r.so11),SUM(r.so12),SUM(r.so13),SUM(r.so14),SUM(r.so15),SUM(r.so16),SUM(r.so17),SUM(r.so18),SUM(r.so19),SUM(r.so20), ";
			$sql .= " SUM(r.so21),SUM(r.so22),SUM(r.so23),SUM(r.so24),SUM(r.so25),SUM(r.so26),SUM(r.so27),SUM(r.so28),SUM(r.so29),SUM(r.so30),SUM(r.so31), ";
			$sql .= " SUM(r.ri1),SUM(r.ri2),SUM(r.ri3),SUM(r.ri4),SUM(r.ri5),SUM(r.ri6),SUM(r.ri7),SUM(r.ri8),SUM(r.ri9),SUM(r.ri10), ";
			$sql .= " SUM(r.ri11),SUM(r.ri12),SUM(r.ri13),SUM(r.ri14),SUM(r.ri15),SUM(r.ri16),SUM(r.ri17),SUM(r.ri18),SUM(r.ri19),SUM(r.ri20), ";
			$sql .= " SUM(r.ri21),SUM(r.ri22),SUM(r.ri23),SUM(r.ri24),SUM(r.ri25),SUM(r.ri26),SUM(r.ri27),SUM(r.ri28),SUM(r.ri29),SUM(r.ri30),SUM(r.ri31), ";
			$sql .= " SUM(r.k1),SUM(r.k2),SUM(r.k3),SUM(r.k4),SUM(r.k5),SUM(r.k6),SUM(r.k7),SUM(r.k8),SUM(r.k9),SUM(r.k10), ";
			$sql .= " SUM(r.k11),SUM(r.k12),SUM(r.k13),SUM(r.k14),SUM(r.k15),SUM(r.k16),SUM(r.k17),SUM(r.k18),SUM(r.k19),SUM(r.k20), ";
			$sql .= " SUM(r.k21),SUM(r.k22),SUM(r.k23),SUM(r.k24),SUM(r.k25),SUM(r.k26),SUM(r.k27),SUM(r.k28),SUM(r.k29),SUM(r.k30),SUM(r.k31), ";
			$sql .= " sum(r.stok_akhir),sum(r.hpp*r.stok_akhir),sum(r.hpj*r.stok_akhir),p.id_group  ";
			$sql .= "FROM $table_rekap_stok r $sql_inner_group WHERE r.kode_manufaktur like '$pabrik1%' $tambahan $filter_pabrik and r.periode like '$filter_periode%' $sql_area ";
			$sql .= " AND r.kode_manufaktur like 'M0%' and length(trim(r.kode_manufaktur))=10  $sql_tbhn2 ";
			$sql .= "GROUP BY r.kode_manufaktur,DATE_FORMAT(r.periode,'%Y-%m') ORDER BY r.kode_manufaktur LIMIT " . ($page * $jmlHal) . "," . $jmlHal . " -- start";
		}
		unset($_SESSION['pabrik1']);
		unset($_SESSION['bulan1']);
		unset($_SESSION['tahun1']);
		unset($_SESSION['jenis_pabrik']);
	}


	//echo "<!-- SQL : $sql--> ";
	//die( "<!-- SQL : $sql-->");
	//$hsltmp1=mysql_query($sql1,$db);
	//$jmlData=mysql_fetch_row($hsltmp1);

	if ($username == 'budi-it') {
		echo "$sql";
	} else {
		#echo "<!-- SQL $sql -->";
	}

	// if ($username == 'B120938_ahmad') {
	// 	echo "$sql";
	// } else {
	// 	#echo "<!-- SQL $sql -->";
	// }

	#die($sql);

	$jmlData = 15000000;
	//===Tambahan tgl 1 feb 2013


	# echo "<!-- SQL $sql -->";

	$hsltemp = mysql_query($sql); //or die($sql.' # '.mysql_error());

	if (!$hsltemp) {
		if ($username == 'budi-it') {
			echo "<hr>";
			echo "$sql";
			echo "<hr>";
			echo "<h3>ERROR " . mysql_error() . "</h3>";
		}
		if ($username == 'B120938_ahmad') {
			echo "<hr>";
			echo "$sql";
			echo "<hr>";
			echo "<h3>ERROR " . mysql_error() . "</h3>";
		}
	}


	$no = 0;
	$tot_stok_in = 0;
	$tot_retur_out = 0;
	$tot_stok_out = 0;
	$tot_retur_in = 0;
	$tot_koreksi = 0;
	$tsi1 = 0;
	$tsi2 = 0;
	$tsi3 = 0;
	$tsi4 = 0;
	$tsi5 = 0;
	$tsi6 = 0;
	$tsi7 = 0;
	$tsi8 = 0;
	$tsi9 = 0;
	$tsi10 = 0;
	$tsi11 = 0;
	$tsi12 = 0;
	$tsi13 = 0;
	$tsi14 = 0;
	$tsi15 = 0;
	$tsi16 = 0;
	$tsi17 = 0;
	$tsi18 = 0;
	$tsi19 = 0;
	$tsi20 = 0;
	$tsi21 = 0;
	$tsi22 = 0;
	$tsi23 = 0;
	$tsi24 = 0;
	$tsi25 = 0;
	$tsi26 = 0;
	$tsi27 = 0;
	$tsi28 = 0;
	$tsi29 = 0;
	$tsi30 = 0;
	$tsi31 = 0;
	$tro1 = 0;
	$tro2 = 0;
	$tro3 = 0;
	$tro4 = 0;
	$tro5 = 0;
	$tro6 = 0;
	$tro7 = 0;
	$tro8 = 0;
	$tro9 = 0;
	$tro10 = 0;
	$tro11 = 0;
	$tro12 = 0;
	$tro13 = 0;
	$tro14 = 0;
	$tro15 = 0;
	$tro16 = 0;
	$tro17 = 0;
	$tro18 = 0;
	$tro19 = 0;
	$tro20 = 0;
	$tro21 = 0;
	$tro22 = 0;
	$tro23 = 0;
	$tro24 = 0;
	$tro25 = 0;
	$tro26 = 0;
	$tro27 = 0;
	$tro28 = 0;
	$tro29 = 0;
	$tro30 = 0;
	$tro31 = 0;
	$tso1 = 0;
	$tso2 = 0;
	$tso3 = 0;
	$tso4 = 0;
	$tso5 = 0;
	$tso6 = 0;
	$tso7 = 0;
	$tso8 = 0;
	$tso9 = 0;
	$tso10 = 0;
	$tso11 = 0;
	$tso12 = 0;
	$tso13 = 0;
	$tso14 = 0;
	$tso15 = 0;
	$tso16 = 0;
	$tso17 = 0;
	$tso18 = 0;
	$tso19 = 0;
	$tso20 = 0;
	$tso21 = 0;
	$tso22 = 0;
	$tso23 = 0;
	$tso24 = 0;
	$tso25 = 0;
	$tso26 = 0;
	$tso27 = 0;
	$tso28 = 0;
	$tso29 = 0;
	$tso30 = 0;
	$tso31 = 0;
	$tri1 = 0;
	$tri2 = 0;
	$tri3 = 0;
	$tri4 = 0;
	$tri5 = 0;
	$tri6 = 0;
	$tri7 = 0;
	$tri8 = 0;
	$tri9 = 0;
	$tri10 = 0;
	$tri11 = 0;
	$tri12 = 0;
	$tri13 = 0;
	$tri14 = 0;
	$tri15 = 0;
	$tri16 = 0;
	$tri17 = 0;
	$tri18 = 0;
	$tri19 = 0;
	$tri20 = 0;
	$tri21 = 0;
	$tri22 = 0;
	$tri23 = 0;
	$tri24 = 0;
	$tri25 = 0;
	$tri26 = 0;
	$tri27 = 0;
	$tri28 = 0;
	$tri29 = 0;
	$tri30 = 0;
	$tri31 = 0;
	$tk1 = 0;
	$tk2 = 0;
	$tk3 = 0;
	$tk4 = 0;
	$tk5 = 0;
	$tk6 = 0;
	$tk7 = 0;
	$tk8 = 0;
	$tk9 = 0;
	$tk10 = 0;
	$tk11 = 0;
	$tk12 = 0;
	$tk13 = 0;
	$tk14 = 0;
	$tk15 = 0;
	$tk16 = 0;
	$tk17 = 0;
	$tk18 = 0;
	$tk19 = 0;
	$tk20 = 0;
	$tk21 = 0;
	$tk22 = 0;
	$tk23 = 0;
	$tk24 = 0;
	$tk25 = 0;
	$tk26 = 0;
	$tk27 = 0;
	$tk28 = 0;
	$tk29 = 0;
	$tk30 = 0;
	$tk31 = 0;
	if (isset($txtmarkas)) {
		//$area=substr($txtmarkas,0,4);;
	}

	while (list(
		$periode, $tgl_stok_awal, $kode_manufaktur, $nama_manufaktur, $barcode_13, $barcode_15, $nama, $hpp, $hpj, $stok_awal,
		$si1, $si2, $si3, $si4, $si5, $si6, $si7, $si8, $si9, $si10, $si11, $si12, $si13, $si14, $si15, $si16, $si17, $si18, $si19, $si20,
		$si21, $si22, $si23, $si24, $si25, $si26, $si27, $si28, $si29, $si30, $si31,
		$ro1, $ro2, $ro3, $ro4, $ro5, $ro6, $ro7, $ro8, $ro9, $ro10, $ro11, $ro12, $ro13, $ro14, $ro15, $ro16, $ro17, $ro18, $ro19, $ro20,
		$ro21, $ro22, $ro23, $ro24, $ro25, $ro26, $ro27, $ro28, $ro29, $ro30, $ro31,
		$so1, $so2, $so3, $so4, $so5, $so6, $so7, $so8, $so9, $so10, $so11, $so12, $so13, $so14, $so15, $so16, $so17, $so18, $so19, $so20,
		$so21, $so22, $so23, $so24, $so25, $so26, $so27, $so28, $so29, $so30, $so31,
		$ri1, $ri2, $ri3, $ri4, $ri5, $ri6, $ri7, $ri8, $ri9, $ri10, $ri11, $ri12, $ri13, $ri14, $ri15, $ri16, $ri17, $ri18, $ri19, $ri20,
		$ri21, $ri22, $ri23, $ri24, $ri25, $ri26, $ri27, $ri28, $ri29, $ri30, $ri31,
		$k1, $k2, $k3, $k4, $k5, $k6, $k7, $k8, $k9, $k10, $k11, $k12, $k13, $k14, $k15, $k16, $k17, $k18, $k19, $k20,
		$k21, $k22, $k23, $k24, $k25, $k26, $k27, $k28, $k29, $k30, $k31, $stok_akhir, $stok_akhir_hpp, $stok_akhir_hpj
	) = mysql_fetch_array($hsltemp)) {



		//if($username!='superuser'){$hpp=0;$stok_akhir_hpp=0;}
		//dirubah tanggal 24 des 2012
		/*
	 if($username=='budi-it'){
			
		}elseif($username=='uche_r'||$username=='faipusat_yati'||$username=='merchandise_dinda'||$username=='faisystem'){
			
		}else{
		   $hpp=0;$stok_akhir_hpp=0;	
		}
		*/
		if ($isShowHpp == 1) {
		} else {
			$hpp = 0;
			$stok_akhir_hpp = 0;
		}

		// if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus) || !empty($txtprodukpilihan)) {
		// 	$sql_sa = "SELECT $sql_cache DATE_FORMAT(tanggal,'%d %M %Y') FROM stok_awal_reshare WHERE kode_manufaktur='$kode_manufaktur' limit 1";
		// 	list($tgl_stok_awal) = mysql_fetch_array(mysql_query($sql_sa));
		// } else {
		// 	if ($detail <> 1) {
		// 		$sql_sa = "SELECT $sql_cache DATE_FORMAT(tanggal,'%d %M %Y') FROM stok_awal_reshare WHERE kode_manufaktur='$kode_manufaktur' limit 1";
		// 		list($tgl_stok_awal) = mysql_fetch_array(mysql_query($sql_sa));
		// 		$barcode_13 = '';
		// 		$barcode_15 = '';
		// 		$nama = '';
		// 	}
		// }



		//$hpp='-'; //dirubah tanggal 2 desember 2011 untuk menghindari non super user
		$no++;
		$bgclr1 = "#FFFFCC";
		$bgclr2 = "#E0FF9F";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
		$tsi = $si1 + $si2 + $si3 + $si4 + $si5 + $si6 + $si7 + $si8 + $si9 + $si10 + $si11 + $si12 + $si13 + $si14 + $si15 + $si16 + $si17 + $si18 + $si19 + $si20 +
			$si21 + $si22 + $si23 + $si24 + $si25 + $si26 + $si27 + $si28 + $si29 + $si30 + $si31;
		$tro = $ro1 + $ro2 + $ro3 + $ro4 + $ro5 + $ro6 + $ro7 + $ro8 + $ro9 + $ro10 + $ro11 + $ro12 + $ro13 + $ro14 + $ro15 + $ro16 + $ro17 + $ro18 + $ro19 + $ro20 +
			$ro21 + $ro22 + $ro23 + $ro24 + $ro25 + $ro26 + $ro27 + $ro28 + $ro29 + $ro30 + $ro31;
		$tso = $so1 + $so2 + $so3 + $so4 + $so5 + $so6 + $so7 + $so8 + $so9 + $so10 + $so11 + $so12 + $so13 + $so14 + $so15 + $so16 + $so17 + $so18 + $so19 + $so20 +
			$so21 + $so22 + $so23 + $so24 + $so25 + $so26 + $so27 + $so28 + $so29 + $so30 + $so31;
		$tri = $ri1 + $ri2 + $ri3 + $ri4 + $ri5 + $ri6 + $ri7 + $ri8 + $ri9 + $ri10 + $ri11 + $ri12 + $ri13 + $ri14 + $ri15 + $ri16 + $ri17 + $ri18 + $ri19 + $ri20 +
			$ri21 + $ri22 + $ri23 + $ri24 + $ri25 + $ri26 + $ri27 + $ri28 + $ri29 + $ri30 + $ri31;
		$tk = $k1 + $k2 + $k3 + $k4 + $k5 + $k6 + $k7 + $k8 + $k9 + $k10 + $k11 + $k12 + $k13 + $k14 + $k15 + $k16 + $k17 + $k18 + $k19 + $k20 +
			$k21 + $k22 + $k23 + $k24 + $k25 + $k26 + $k27 + $k28 + $k29 + $k30 + $k31;

		if ($stok_akhir_hpp == 0) {
		};
		if ($stok_akhir_hpj == 0) {
		}

		/* Cari Nama */


	?>
		<?php
		if ($export_to_excel == '1') {
			echo "<tr>";
		} else { ?>
			<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>" class="datarow" id="<?php echo $kode_manufaktur . '_' . $barcode_15 . '_' . $barcode_13; ?>">
			<?php } ?>



			<td width="41" bgcolor="<?php echo $bgcolor; ?>">&nbsp;<?php echo $no; ?></td>
			<td width="49">&nbsp;<?php echo $periode; ?></td>
			<td width="50">&nbsp;<?php echo $kode_manufaktur; ?></td>
			<td width="93">&nbsp;<?php echo $nama_manufaktur; ?></td>
			<td width="93" class="detail1"><?php echo $barcode_13; ?></td>
			<td width="93" class="detail1"><?php echo $barcode_15; ?></td>
			<td width="93" class="detail1">&nbsp;<?php echo $nama; ?></td>
			<td width="51" align="right" id="stokawal_<?php echo $kode_manufaktur . '_' . $barcode_15; ?>">
				<?php
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					echo number_format($stok_awal);
				} else {
					echo "<a href=\"stok_finishing_periode.php?a=1&p=$filter_periode&r=$kode_manufaktur&n=$txt_nama\" target=\"_blank\">" . number_format($stok_awal) . "</a>";
				}
				?>
			</td>

			<td align="right" class="hpp"><?php echo number_format($hpp, 2, '.', ','); ?></td>
			<?php if ($export_to_excel <> '1') { ?>
			<?php } else { ?>
			<?php } ?>
			<td align="right"><?php echo number_format($hpj, 2, '.', ','); ?></td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				$var_baru = "\$si$i";
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					eval("echo \"<td align='right' $bgc_si class='detail' id='si_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'>$var_baru</td>\";");
				} else {
					eval("echo \"<td align='right' $bgc_si class='detail' id='si_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'><a href='rekap_stok_finishing_detail.php?o=$kode_manufaktur&p=$filter_periode&j=si$i&t=o' target='_blank'>$var_baru</a></td>\";");
				}
				//eval("echo \"<td align='right' class='detail'>$var_baru</td>\";");

				//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");

			}
			$tsi_total += $tsi;
			?>
			<td align="right" bgcolor="#DDA0DD" class="total"><?php echo $tsi ?></td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				$var_baru = "\$ro$i";
				//eval("echo \"<td align='right' class='detail'>$var_baru</td>\";");
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					eval("echo \"<td align='right' $bgc_ro class='detail' id='ro_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'>$var_baru</td>\";");
				} else {
					eval("echo \"<td align='right' $bgc_ro class='detail' id='ro_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'><a href='rekap_stok_finishing_detail.php?o=$kode_manufaktur&p=$filter_periode&j=ro$i&t=o' target='_blank'>$var_baru</a></td>\";");
				}
				//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
			}
			$tro_total += $tro;
			?>
			<td align="right" bgcolor="#FFC0CB" class="total"><?php echo $tro ?></td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				$var_baru = "\$so$i";
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					eval("echo \"<td align='right' $bgc_so class='detail' id='so_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'>$var_baru</td>\";");
				} else {
					eval("echo \"<td align='right' $bgc_so class='detail' id='so_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'><a href='rekap_stok_finishing_detail.php?o=$kode_manufaktur&p=$filter_periode&j=so$i&t=o' target='_blank'>$var_baru</a></td>\";");
				}
				//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
			}
			$tso_total += $tso;
			?>
			<td align="right" bgcolor="#98FB98" class="total"><?php echo $tso ?></td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				$var_baru = "\$ri$i";
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					eval("echo \"<td align='right' $bgc_ri class='detail' id='ri_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'>$var_baru</td>\";");
				} else {
					//eval("echo \"<td align='right' class='detail'>$var_baru</td>\";");
					eval("echo \"<td align='right' $bgc_ri  class='detail' id='ri_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'><a href='rekap_stok_finishing_detail.php?o=$kode_manufaktur&p=$filter_periode&j=ri$i&t=o' target='_blank'>$var_baru</a></td>\";");
				}
				//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
			}
			$tri_total += $tri;
			?>
			<td align="right" bgcolor="#00FFFF" class="total"><?php echo $tri ?></td>
			<?php
			for ($i = 1; $i <= $jpb; $i++) {
				$var_baru = "\$k$i";
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					eval("echo \"<td align='right' $bgc_k class='detail' id='k_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'>$var_baru</td>\";");
				} else {
					//eval("echo \"<td align='right' class='detail'>$var_baru</td>\";");
					eval("echo \"<td align='right' $bgc_k class='detail' id='k_" . $kode_manufaktur . "_" . $barcode_15 . "_" . $i . "'><a href='rekap_stok_finishing_detail.php?o=$kode_manufaktur&p=$filter_periode&j=k$i&t=o' target='_blank'>$var_baru</a></td>\";");
				}
				//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
			}
			$tk_total += $tk;
			?>
			<td align="right" bgcolor="#FFFF00" class="total"><?php echo $tk ?></td>
			<?php


			if ($stok_akhir == 0) {
				$stok_akhir = $stok_awal + $tsi - $tro - $tso + $tri + $tk;
			}

			?>
			<?php if ($stok_in > 0) {
				$bg1 = 'bgcolor="#3399FF"';
			} else {
				$bg1 = '';
			} ?>
			<?php if ($retur_out > 0) {
				$bg2 = 'bgcolor="#3399FF"';
			} else {
				$bg2 = '';
			} ?>
			<?php if ($stok_out > 0) {
				$bg3 = 'bgcolor="#3399FF"';
			} else {
				$bg3 = '';
			} ?>
			<?php if ($retur_in > 0) {
				$bg4 = 'bgcolor="#3399FF"';
			} else {
				$bg4 = '';
			} ?>
			<td width="51" align="right">
				<?php
				if ((isset($cari_barcode) && (!empty($barcode) || !empty($txt_nama))) || isset($cari_minus)) {
					$hr = date('j');
					echo "<div class='edit_text' id='$hr#$kode_manufaktur#$filter_periode#$barcode_13#$barcode_15'>" . number_format($stok_akhir) . "</div>";
				} else {
					echo "<a href=\"stok_finishing_periode.php?a=0&p=$filter_periode&r=$kode_manufaktur&n=$txt_nama\" target=\"_blank\" id='stok_" . $kode_manufaktur . "_" . $barcode_13 . "_" . $barcode_15 . "'>" . number_format($stok_akhir) . "</a>";
				}
				?>
			</td>
			<td width="63" align="right" class="hpp"><?php echo number_format($stok_akhir_hpp, 2, '.', ','); ?></td>
			<td width="83" align="right"><?php echo number_format($stok_akhir_hpj, 2, '.', ','); ?></td>
			<?php
			for ($i = 1; $i <= $hari_ini; $i++) {
				echo "<td align='center'  id='stok_" . $kode_manufaktur . "_" . $barcode_13 . "_" . $barcode_15 . "_" . $i . "' class='stok'>0</td>";
			}

			?>
			</tr>
		<?php
		// $total_si,$total_ro,$total_so,$total_ri,$total_koreksi
		$tsi1 +=   $si1;
		$tsi2 +=    $si2;
		$tsi3 +=    $si3;
		$tsi4 +=    $si4;
		$tsi5 +=    $si5;
		$tsi6 +=    $si6;
		$tsi7 +=    $si7;
		$tsi8 +=    $si8;
		$tsi9 +=    $si9;
		$tsi10 +=    $si10;
		$tsi11 +=    $si11;
		$tsi12 +=    $si12;
		$tsi13 +=    $si13;
		$tsi14 +=    $si14;
		$tsi15 +=    $si15;
		$tsi16 +=    $si16;
		$tsi17 +=    $si17;
		$tsi18 +=    $si18;
		$tsi19 +=    $si19;
		$tsi20 +=    $si20;
		$tsi21 +=    $si21;
		$tsi22 +=    $si22;
		$tsi23 +=    $si23;
		$tsi24 +=    $si24;
		$tsi25 +=    $si25;
		$tsi26 +=    $si26;
		$tsi27 +=    $si27;
		$tsi28 +=    $si28;
		$tsi29 +=    $si29;
		$tsi30 +=    $si30;
		$tsi31 +=    $si31;
		$tro1 +=    $ro1;
		$tro2 +=    $ro2;
		$tro3 +=    $ro3;
		$tro4 +=    $ro4;
		$tro5 +=    $ro5;
		$tro6 +=    $ro6;
		$tro7 +=    $ro7;
		$tro8 +=    $ro8;
		$tro9 +=    $ro9;
		$tro10 +=    $ro10;
		$tro11 +=    $ro11;
		$tro12 +=    $ro12;
		$tro13 +=    $ro13;
		$tro14 +=    $ro14;
		$tro15 +=    $ro15;
		$tro16 +=    $ro16;
		$tro17 +=    $ro17;
		$tro18 +=    $ro18;
		$tro19 +=    $ro19;
		$tro20 +=    $ro20;
		$tro21 +=    $ro21;
		$tro22 +=    $ro22;
		$tro23 +=    $ro23;
		$tro24 +=    $ro24;
		$tro25 +=    $ro25;
		$tro26 +=    $ro26;
		$tro27 +=    $ro27;
		$tro28 +=    $ro28;
		$tro29 +=    $ro29;
		$tro30 +=    $ro30;
		$tro31 +=    $ro31;
		$tso1 +=    $so1;
		$tso2 +=    $so2;
		$tso3 +=    $so3;
		$tso4 +=    $so4;
		$tso5 +=    $so5;
		$tso6 +=    $so6;
		$tso7 +=    $so7;
		$tso8 +=    $so8;
		$tso9 +=    $so9;
		$tso10 +=    $so10;
		$tso11 +=    $so11;
		$tso12 +=    $so12;
		$tso13 +=    $so13;
		$tso14 +=    $so14;
		$tso15 +=    $so15;
		$tso16 +=    $so16;
		$tso17 +=    $so17;
		$tso18 +=    $so18;
		$tso19 +=    $so19;
		$tso20 +=    $so20;
		$tso21 +=    $so21;
		$tso22 +=    $so22;
		$tso23 +=    $so23;
		$tso24 +=    $so24;
		$tso25 +=    $so25;
		$tso26 +=    $so26;
		$tso27 +=    $so27;
		$tso28 +=    $so28;
		$tso29 +=    $so29;
		$tso30 +=    $so30;
		$tso31 +=    $so31;
		$tri1 +=    $ri1;
		$tri2 +=    $ri2;
		$tri3 +=    $ri3;
		$tri4 +=    $ri4;
		$tri5 +=    $ri5;
		$tri6 +=    $ri6;
		$tri7 +=    $ri7;
		$tri8 +=    $ri8;
		$tri9 +=    $ri9;
		$tri10 +=    $ri10;
		$tri11 +=    $ri11;
		$tri12 +=    $ri12;
		$tri13 +=    $ri13;
		$tri14 +=    $ri14;
		$tri15 +=    $ri15;
		$tri16 +=    $ri16;
		$tri17 +=    $ri17;
		$tri18 +=    $ri18;
		$tri19 +=    $ri19;
		$tri20 +=    $ri20;
		$tri21 +=    $ri21;
		$tri22 +=    $ri22;
		$tri23 +=    $ri23;
		$tri24 +=    $ri24;
		$tri25 +=    $ri25;
		$tri26 +=    $ri26;
		$tri27 +=    $ri27;
		$tri28 +=    $ri28;
		$tri29 +=    $ri29;
		$tri30 +=    $ri30;
		$tri31 +=    $ri31;
		$tk1 +=    $k1;
		$tk2 +=    $k2;
		$tk3 +=    $k3;
		$tk4 +=    $k4;
		$tk5 +=    $k5;
		$tk6 +=    $k6;
		$tk7 +=    $k7;
		$tk8 +=    $k8;
		$tk9 +=    $k9;
		$tk10 +=    $k10;
		$tk11 +=    $k11;
		$tk12 +=    $k12;
		$tk13 +=    $k13;
		$tk14 +=    $k14;
		$tk15 +=    $k15;
		$tk16 +=    $k16;
		$tk17 +=    $k17;
		$tk18 +=    $k18;
		$tk19 +=    $k19;
		$tk20 +=    $k20;
		$tk21 +=    $k21;
		$tk22 +=    $k22;
		$tk23 +=    $k23;
		$tk24 +=    $k24;
		$tk25 +=    $k25;
		$tk26 +=    $k26;
		$tk27 +=    $k27;
		$tk28 +=    $k28;
		$tk29 +=    $k29;
		$tk30 +=    $k30;
		$tk31 +=    $k31;



		$total_saw += $stok_awal;
		$total_saw_hpp += $hpp;
		$total_saw_hpj += $hpj;

		$total_sak += $stok_akhir;
		$total_sak_hpp += $stok_akhir_hpp;
		$total_sak_hpj += $stok_akhir_hpj;
	}    //Barcode15 not Empty

		?>
		<tfoot>
			<tr>
				<td <?php echo $background ?> width="41" height="22">Data Hari ini</td>
				<td <?php echo $background ?> width="49">&nbsp;</td>
				<td <?php echo $background ?> width="50">&nbsp;</td>
				<td <?php echo $background ?> width="93">&nbsp;</td>
				<td <?php echo $background ?> width="93" class="detail1">&nbsp;</td>
				<td <?php echo $background ?> width="93" class="detail1">&nbsp;</td>
				<td <?php echo $background ?> width="93" class="detail1">&nbsp;</td>
				<td <?php echo $background ?> align="right"><?php echo number_format($total_saw); ?></td>
				<td <?php echo $background ?> align="right" class="hpp"><?php echo number_format($total_saw_hpp, 2, '.', ','); ?></td>
				<?php if ($export_to_excel <> '1') { ?>
				<?php } else { ?>
				<?php } ?>
				<td <?php echo $background ?> align="right"><?php echo number_format($total_saw_hpj, 2, '.', ','); ?></td>
				<?php
				for ($i = 1; $i <= $jpb; $i++) {
					$var_baru = "\$tsi$i";
					eval("echo \"<td align='right' class='detail' $bgc_si >$var_baru</td>\";");
					//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
				}
				?>
				<td <?php echo $background ?> align="right" class="total"><?php echo $tsi_total ?></td>
				<?php
				for ($i = 1; $i <= $jpb; $i++) {
					$var_baru = "\$tro$i";
					eval("echo \"<td align='right' class='detail' $bgc_ro >$var_baru</td>\";");
					//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
				}
				?>
				<td <?php echo $background ?> align="right" class="total"><?php echo $tro_total; ?></td>
				<?php
				for ($i = 1; $i <= $jpb; $i++) {
					$var_baru = "\$tso$i";
					eval("echo \"<td align='right' class='detail' $bgc_so >$var_baru</td>\";");
					//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
				}
				?>
				<td <?php echo $background ?> align="right" class="total"><?php echo $tso_total ?></td>
				<?php
				for ($i = 1; $i <= $jpb; $i++) {
					$var_baru = "\$tri$i";
					eval("echo \"<td align='right' class='detail' $bgc_ri >$var_baru</td>\";");
					//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
				}
				?>
				<td <?php echo $background ?> align="right" class="total"><?php echo $tri_total ?></td>
				<?php
				for ($i = 1; $i <= $jpb; $i++) {
					$var_baru = "\$tk$i";
					eval("echo \"<td align='right' class='detail' $bgc_k >$var_baru</td>\";");
					//echo("echo \"<td align='right' class='detail'>$var_baru</td>\";");		  
				}
				?>
				<td <?php echo $background ?> align="right" class="total"><?php echo $tk_total ?></td>
				<?php if ($stok_in > 0) {
					$bg1 = 'bgcolor="#3399FF"';
				} else {
					$bg1 = '';
				} ?>
				<?php if ($retur_out > 0) {
					$bg2 = 'bgcolor="#3399FF"';
				} else {
					$bg2 = '';
				} ?>
				<?php if ($stok_out > 0) {
					$bg3 = 'bgcolor="#3399FF"';
				} else {
					$bg3 = '';
				} ?>
				<?php if ($retur_in > 0) {
					$bg4 = 'bgcolor="#3399FF"';
				} else {
					$bg4 = '';
				} ?>
				<td <?php echo $background ?> width="51" align="right"><?php echo number_format($total_sak); ?></td>
				<td <?php echo $background ?> width="63" align="right" class="hpp"><?php echo number_format($total_sak_hpp, 2, '.', ','); ?></td>
				<td <?php echo $background ?> width="83" align="right"><?php echo number_format($total_sak_hpj, 2, '.', ','); ?></td>
				<td colspan="<?php echo $hari_ini ?>" align="center" <?php echo $background ?> class='stok'>&nbsp;</td>

			</tr>
		</tfoot>
</table>

<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			Page :
			<span class="hal" onclick="location.href='monitoring_rekap_stok_detail_with_actual.php?&hal=0<?php echo $tambah ?>';">First</span>
			<?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='monitoring_rekap_stok_detail_with_actual.php?hal=<?php echo $i; ?><?php echo $tambah ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
					if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='monitoring_rekap_stok_detail_with_actual.php?hal=<?php echo $i; ?><?php echo $tambah ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='monitoring_rekap_stok_detail_with_actual.php?hal=<?php echo $i; ?><?php echo $tambah ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="location.href='monitoring_rekap_stok_detail_with_actual.php?hal=<?php echo intval(($jmlData[0] / $jmlHal)); ?><?php echo $tambah ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo $jmlData[0]; ?>
		</td>
	</tr>
</table>
<?php
if ($username == 'budi-it') {
	echo '<span id="debug" > C </span>';
} else {
	echo '<span id="debug" style="display:none"></span>';
}

// if ($username == 'B120938_ahmad') {
// 	echo '<span id="debug" > C </span>';
// } else {
// 	echo '<span id="debug" style="display:none"></span>';
// }
?>

<script language="JavaScript">
	var tanggalAkhir = <?php echo $hari_ini ?>;
	var today = <?php echo $today; ?>;
	var periode = '<?php echo $filter_periode; ?>';
	try {

		hitungStokPertanggal();
	} catch (e) {
		alert(e.message);
	}
</script>


<?php include_once "footer.php"; ?>