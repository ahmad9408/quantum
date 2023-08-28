<?php
@session_start();
if (@$_REQUEST['action'] == 'export') {
	//echo "<h2>TURN BACK</h2>";	
	//die();

}

#ini_set('memory_limit', '-1');

require_once("config.php");

@$username = $_SESSION["username"];
if (empty($username)) {
	die('You can\'t see this page');
}

$isDirectDownload = 0;
$isExportMode = 1;
/* setting hanya untuk file csv saja
$isWriteToZipFile=1;
$isWriteToZipFile=1;
*/
$isWriteToZipFile = 1;
$isForceIgnoreCompress = 1;
$isDebugMode = 0;
$csv = 1;
$se = ',';
$isShowHpp = 0;

if ($username == 'budi-it') {
	$isDebugMode = 1;
}

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



$sql_tambahan = " AND r.kode_manufaktur like '%0%' and length(trim(r.kode_manufaktur))=10 "; // add tgl 22 jan 2014
$txt_organization = $_POST['txt_organization'];
$sql_cache = ' SQL_CACHE ';
$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
}

@$txtTable = $_POST['txtTable'];
if (empty($txtTable)) {
	$table = 'rekap_stok_manufaktur';
} else {
	$table = $txtTable;
}

@$txtprodukpilihan = $_POST['txtprodukpilihan'];
@$cbProsesLengkap = $_POST['cbProsesLengkap'];
@$txtjenis = $_POST['txtjenis'];
@$jenis_pabrik = $_POST['jenis_pabrik'];
@$txtmarkas = $_POST['txtmarkas'];
@$barcode = $_POST['barcode'];
@$txt_nama = $_POST['txt_nama'];

@$tanggal = $_POST['tanggal'];

if ($tanggal == '') {
	$tanggal = date('j');
}

if ($isDebugMode == 1) {
	#print_r($_POST);	
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



$export_to_excel = '';
$filter = '';

@$detail = $_POST['cb_detail'];
@$pabrik1 = $_POST['pabrik1'];
@$jenis_pabrik = $_POST['jenis_pabrik'];
$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
}


$tahun_skrg = date('Y');
$bulan_skrg = date('m');
@$txtTable = $_POST['txtTable'];
if (empty($txtTable)) {
	$table = 'rekap_stok_manufaktur';
} else {
	$table = $txtTable;
}
if (isset($_REQUEST['action'])) {
	$bulan1 = $_POST['bulan1'];
	$tahun1 = $_POST['tahunl'];
	//echo "Kapilih";
} else {
	$bulan1 = $bulan_skrg;
	$tahun1 = $tahun_skrg;
}



$filter_periode = $tahun1 . '-' . $bulan1;
if ($filter_periode != date('Y-m')) {
	$table_rekap_stok = 'rekap_stok_manufaktur';
	$hari_download = '';
} else {
	$table_rekap_stok = $table;
	$hari_download = date('Y-m-d');
}

if ($isDebugMode == 1) {
	echo "A13<br>";
}


$time = date('YmdHis');

$isExportStokOnly = 0;
if (isset($_POST['btnExportDataStok'])) {
	$isExportStokOnly = 1;
	$csv = 1;
}

$ext = 'csv';

$myFile = 'StokOnly' . str_ireplace('.', '', $txtmarkas . $pabrik1) . '_' . $txtjenis . '_' . $filter_periode .
	'_' . $hari_download . '_' . $tanggal . '_' . date('His') . '.' . $ext;




$isShowHpp = 0;
$sql = "SELECT COUNT(*) ada FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
$res = mysql_query($sql);
list($ada) = mysql_fetch_array($res);

if ($ada > 0) {
	$isShowHpp = 1;
}

if ($isShowHpp == 1) {
	$myFile = 'C_' . $myFile;
}

if ($cbProsesLengkap == "1") {
	$myFile = 'lnk' . $myFile;
}
if ($isShowHpp == 1) {
	$location_save = 'export_rekap_cmplt/';
	$field_hpp_export = 'r.hpp';
} else {
	$location_save = 'export_rekap/';
	$field_hpp_export = '0';
}


if ($isDebugMode == 1) {
	echo "A14<br>";
}
$destination = '/var/www/html/quantum/' . $location_save;

if ($csv == 1) {
	$zip_file = $destination . $myFile;
	$zip_file_name = $myFile;
}

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename=$zip_file_name");
header("Content-Transfer-Encoding: binary ");




#echo "Result Delete $result_delete $myFile "."/bin/rm -f $destination".$txtmarkas.$pabrik1."_".$filter_periode."_*";
ini_set('memory_limit', '-1'); //menghindari exhausted memori
if ($isWriteToZipFile == 1) {
	@$fh = fopen($destination . $myFile, 'w') or die("can't open file " . $destination . $myFile);
}


$background = '';
$export_to_excel = '1';

if (!empty($outlet_export)) {
	$dt_outlet = explode(';', $outlet_export);
	$data_in = '';
	foreach ($dt_outlet as $d_otl) {
		if (!empty($d_otl)) {
			$data_in .= "'$d_otl',";
		}
	}
	$data_in = substr($data_in, 0, strlen($data_in) - 1);
	$sql_pilih_reshare = " AND r.kode_manufaktur in ($data_in) ";
}

$sql_inner_outlet = ' INNER JOIN outlet o on o.id=r.kode_manufaktur ';

$sql_aktif = ' AND o.is_show_omset=1 AND o.`type`=4 ';

@$jenis_pabrik = $_POST['jenis_pabrik'];
$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
}

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


$field_stok_awal = '(r.stok_awal)';
$field_stok_akhir = '(r.stok_awal';
$field_si = '(';
$field_ro = '(';
$field_so = '(';
$field_ri = '(';
$field_k = '(';

$filter_si = '';
$filter_ro = '';
$filter_so = '';
$filter_ri = '';
$filter_k = '';

for ($i = 1; $i <= $tanggal; $i++) {
	$field_stok_akhir .= '+ r.si' . $i . ' - r.ro' . $i . ' - r.so' . $i . ' + r.ri' . $i . ' + r.k' . $i;
	if ($i == 1) {
		$field_si .= ' r.si' . $i;
		$field_ro .= ' r.ro' . $i;
		$field_so .= ' r.so' . $i;
		$field_ri .= ' r.ri' . $i;
		$field_k .= ' r.k' . $i;

		$filter_si .= ' r.si' . $i;
		$filter_ro .= ' r.ro' . $i;
		$filter_so .= ' r.so' . $i;
		$filter_ri .= ' r.ri' . $i;
		$filter_k .= ' r.k' . $i;
	} else {
		$field_si .= '+ r.si' . $i;
		$field_ro .= '+ r.ro' . $i;
		$field_so .= '+ r.so' . $i;
		$field_ri .= '+ r.ri' . $i;
		$field_k .= '+ r.k' . $i;

		$filter_si .= '+ r.si' . $i;
		$filter_ro .= '+ r.ro' . $i;
		$filter_so .= '+ r.so' . $i;
		$filter_ri .= '+ r.ri' . $i;
		$filter_k .= '+ r.k' . $i;
	}
}
$field_stok_akhir .= ')';
$field_si .= ') as si';
$field_ro .= ') as ro';
$field_so .= ') as so';
$field_ri .= ') as ri';
$field_k .= ') as k';


if ($pabrik1 == 'ALL' || $pabrik1 == '--') {
	$filter .= " ";
} else {
	$filter .= " AND r.kode_manufaktur='$pabrik1'";
}

if (!empty($txtjenis)) {
	$filter .= " AND o.jenis='$txtjenis' ";
}

//stok yang 0 tidak tampil

if ($cbProsesLengkap == "1") {
	$filter .= " AND ( r.stok_awal <> 0 ";
	$filter .= " OR ( $filter_si ) <> 0 ";
	$filter .= " OR ( $filter_ro ) <> 0 ";
	$filter .= " OR ( $filter_so ) <> 0 ";
	$filter .= " OR ( $filter_ri ) <> 0 ";
	$filter .= " OR ( $filter_k ) <> 0 ";
	$filter .= " OR  $field_stok_akhir <> 0 )";
} else {
	$filter .= " AND  $field_stok_akhir <> 0 ";
}


$fieldtbhn = " 0 stok_awal,0 stok_awal_hpp,0 stok_awal_hpj , 0 si ,0 ro , 0 so ,0 ri , 0 k , ";
if ($cbProsesLengkap == "1") {
	$fieldtbhn = " $field_stok_awal , ($field_hpp_export * $field_stok_awal ) stok_awal_hpp, (r.hpj * $field_stok_awal ) stok_awal_hpj , $field_si ,$field_ro ,$field_so ,$field_ri , $field_k , ";
}

if ($username == 'budi-it') {
	$sql_area .= " limit 10 ";
}

@$jenis_pabrik = $_POST['jenis_pabrik'];

$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = " AND p.id_group='$jenis_pabrik' ";
}

$sql = "SELECT $sql_cache DATE_FORMAT(r.periode,'%M-%Y') periode,r.kode_manufaktur,r.nama_manufaktur,
		r.barcode_13,r.barcode_15,r.nama,$field_hpp_export as hpp, r.hpj, $fieldtbhn   $field_stok_akhir as stok_akhir,( $field_hpp_export * $field_stok_akhir) as hpp_total,(r.hpj * $field_stok_akhir) as hpj, p.id_group 
		FROM $table_rekap_stok r $sql_inner_group WHERE  r.periode like '$filter_periode%' $filter $filter_pabrik $filter_barcode $sql_area ; -- sql10";
/*if($username=='faipusat_yati'){//||$username=='sm_padalarang_handiani'){ 
		#die($sql);//Debug Export Stok
	}*/

if ($username == 'inventory_darman' || $username == 'budi-it' || $username == 'pjmarkas_nandi' || $username == 'B120938_ahmad') {
	$sql_debug = mysql_escape_string($sql);
	$sql_insert = "INSERT INTO `system_debug`  (`page`,`trace`)VALUES ('" . basename(__FILE__) . " -jenis $jenis ', '$sql_debug');";
	mysql_query($sql_insert);
}

if ($isDebugMode == 1) { //||$username=='sm_padalarang_handiani'){
	echo $sql . "</br>"; //Debug Export Stok
}
$hsltemp = mysql_query($sql); // or die($sql);
if (!$hsltemp) {
	if ($isDebugMode == 1) { //||$username=='sm_padalarang_handiani'){
		echo mysql_error() . "</br>"; //Debug Export Stok
	}
}
if ($username == 'budi-it') { //||$username=='inventory_darman'){//||$username=='sm_padalarang_handiani'){
	# die($sql);
}
#die($sql);
$row_found = mysql_num_rows($hsltemp);
if (empty($row_found)) {
	die($sql);
}
$i = 0;
$count = 0;
$row = 0;

$judul = "\t $se \t $se \t $se \t $se \t $se \t $se \t $se price $se";
$judul .= "(E/B)Stok Akhir $se \t $se \t\n";

if ($csv == 1) {
	echo $judul;
}


$judul = "No $se Periode $se Kode Pabrik $se Nama Pabrik $se Barcode 13 $se Barcode 15 $se Produk $se";
if ($isShowHpp == 1) {
	$judul .= " Hpp/Pcs $se";
}
$judul .= " Hpj/Pcs $se ";
if ($cbProsesLengkap == "1") {
	$judul .= " Stok Awal  $se ";
	if ($isShowHpp == 1) {
		$judul .= " Hpp_Total $se";
	}
	$judul .= " Hpj_total  $se Stok in $se retur out $se Stok out $se Retur in $se Koreksi $se";
}
$judul .= " (E/B) Stok Akhir $se ";
if ($isShowHpp == 1) {
	$judul .= " Hpp_Total $se";
}

$judul .= " Hpj_Total \r\n";

if ($csv == 1) {
	echo $judul;
}



if ($pabrik1 == 'ALL' || $pabrik1 == '--') {
	$sql_outlet = "SELECT $sql_cache UPPER(id),nama from pabrik where id like '%';";
} else {
	$sql_outlet = "SELECT $sql_cache UPPER(id),nama from pabrik where id like '$pabrik1%';";
}

$hsl_outlet = mysql_query($sql_outlet) or die(mysql_error() . ' ' . $sql_outlet);


$arrayOutlet = array();
while (list($id_outlet, $nm_outlet) = mysql_fetch_array($hsl_outlet)) {
	$arrayOutlet[$id_outlet] = $nm_outlet;
}

@$jenis_pabrik = $_POST['jenis_pabrik'];

$sql_inner_group = ' LEFT JOIN pabrik AS p on r.kode_manufaktur = p.id ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
}



if ($isExportStokOnly == 1) {
	while (list(
		$periode, $kode_manufaktur, $nama_manufaktur, $barcode_13, $barcode_15, $nama, $hpp, $hpj,
		$stok_awal, $stok_awal_hpp, $stok_awal_hpj, $stokin, $returout, $stokout, $returin, $koreksi,
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
		$data = "$i $se ' $periode $se $kode_manufaktur $se $nama_manufaktur $se $barcode_13 $se $barcode_15 $se $nama $se";
		if ($isShowHpp == 1) {
			$data .= " $hpp $se ";
		}
		$data .= "  $hpj $se ";
		if ($cbProsesLengkap == "1") {
			$data .= " $stok_awal  $se ";
			if ($isShowHpp == 1) {
				$data .= " $stok_awal_hpp $se";
			}
			$data .= " $stok_awal_hpj $se $stokin  $se $returout  $se $stokout  $se $returin  $se $koreksi  $se";
		}
		$data .= " $stok_akhir $se ";
		if ($isShowHpp == 1) {
			$data .= " $stok_akhir_hpp $se ";
		}
		$data .= "$stok_akhir_hpj\r\n";

		if ($csv == 1) {
			echo $data;
		}
	} //end while

	if ($isDebugMode == 1) {
		echo 'Write TO File ' . $destination . $myFile . ' Success! ready for zip' . " -A21 </br>"; //Debug Export Stok
	}

	die(); //export untuk data stok only
}// end if export stok only
