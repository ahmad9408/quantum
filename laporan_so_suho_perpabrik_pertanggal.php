<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = "Report SO Suho Per Pabrik "; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
    include('header.php');
}

include "config.php";
include("css_group.php");

$isShowHpp = 0;

# if($username=='budi-it'||$username=='faipusat_yati'){//tambahan 23122015 request p yudi
# $isShowHpp=1;
# }

$sql = "SELECT COUNT(*) ada  FROM user_account_showhpp WHERE username='$username' AND aktif=1;";
$res_show_hpp = mysql_query($sql);
list($check_hpp) = mysql_fetch_array($res_show_hpp);
if ($check_hpp >= 1) {
    $isShowHpp = 1;
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


?>
<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script src="jquery.iframe-post-form.js"></script>
<script language="javascript" src="app_libs/laporan_so_suho_perpabrik_pertanggal.js?d=<?php echo date('YmdHis'); ?>"></script>
<link rel="stylesheet" href="calendar.css">
<style>
    .hpp {
        display: none;
    }
</style>



<?php
$tgl_now = date('Y-m-d');
$bg = " background='images/footer.gif' ";
$footer_bg = "background='images/notupload.jpg'";
$array_bulan = array(
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei',
    '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
);
$tgl_skg = date('Y-m-d');

//paging
$dataPerPage = 50;

if (isset($_GET['page'])) {
    $noPage = $_GET['page'];
} else $noPage = 1;
$offset = ($noPage - 1) * $dataPerPage;

$table_koreksi_stok = '';

if (isset($_REQUEST['action'])) {
    $tgl1 = $_POST['tgl1'];
    //$tahun1=$_POST['tahunl'];
    $txt_pabrik = $_POST['txt_pabrik'];
    $barcode = trim($_POST['barcode']);
    //$txtlocation=$_POST['txtlocation'];

    $_SESSION['txt_pabrik'] = $txt_pabrik;
    $_SESSION['tgl1'] = $tgl1;
    $_SESSION['barcode'] = $barcode;

    $txt_nama = trim($_POST['txt_nama']);
    $_SESSION['txt_nama'] = $txt_nama;

    $filter = '';


    if (!empty($barcode)) {
        $filter = " AND kso.kode_15 like '$barcode%'";
    }

    if (!empty($txt_nama)) {
        $filter .= " AND p.nama like '%$txt_nama%' ";
    }
    $tgl_now = $_SESSION['tgl1'];


    $d = explode("-", $tgl1);
    $table_koreksi_stok = '';
    if ($d[0] . '-' . $d[1] != date('Y-m')) {

        // $table_koreksi_stok = 'koreksi_finishing_stok_' . $d[1] . $d[0];
        $table_koreksi_stok = 'koreksi_finishing_stok';
    } else {
        $table_koreksi_stok = 'koreksi_finishing_stok';
    }

    if ($isShowHpp == 1) {
        $field_hpp = ' p.hargadasar ';
    } else {
        $field_hpp = '0';
    }


    $main_sql = "SELECT SQL_CALC_FOUND_ROWS p.kode_grade_a,kso.kode_15,p.nama,kso.stok_awal,(kso.stok_awal* $field_hpp ) AS hpp_awal
    ,(kso.stok_awal*p.hargajual) AS hpj_awal,kso.stok_akhir,(kso.stok_akhir* $field_hpp ) AS hpp_SO
    ,(kso.stok_akhir*p.hargajual) AS hpj_SO 
    ,kso.qty AS qty_koreksi
    ,(kso.qty* $field_hpp ) as hpp_koreksi
    ,(kso.qty*p.hargajual) as hpj_koreksi       
    ,kso.pabrik,kso.update_date
   FROM $table_koreksi_stok AS kso LEFT JOIN produk AS p  
   ON (p.kode = kso.kode_15) WHERE kso.pabrik='$txt_pabrik' AND 
   kso.tanggal='$tgl1' $filter order by kso.kode_15 ";


    session_start();
    $_SESSION['export_pabrik'] = $main_sql;
    $_SESSION['tanggal1'] = $tgl1;

    $main_sql .= " limit $offset,$dataPerPage";
    // untuk pagging hitung jumlah data di tabel t_postingan
    $query    = "SELECT COUNT(kso.kode_15) as jumData 
                    FROM $table_koreksi_stok AS kso LEFT JOIN produk AS p  
                    ON (p.kode = kso.kode_15) 
				    WHERE pabrik='$txt_pabrik' AND tanggal like'%$tgl1%' $filter ";
    $query = "SELECT FOUND_ROWS() as jumData";
} else {
    //$tgl1=$tgl_skrg;
    if (isset($_REQUEST['page'])) {
        $txt_pabrik = $_SESSION['txt_pabrik'];
        $tgl1 = $_SESSION['tgl1'];
        $barcode = $_SESSION['barcode'];
        $txt_nama = $_SESSION['txt_nama'];
    } else {
        $txt_pabrik = $_SESSION['txt_pabrik'];
        $barcode = $_SESSION['barcode'];
        $tgl1 = date('Y-m-d');
        $txt_nama = $_SESSION['txt_nama'];
    }

    $d = explode("-", $tgl1);
    $table_koreksi_stok = '';
    if ($d[0] . '-' . $d[1] != date('Y-m')) {

        $table_koreksi_stok = 'koreksi_finishing_stok_' . $d[1] . $d[0];
    } else {
        $table_koreksi_stok = 'koreksi_finishing_stok';
    }

    if ($isShowHpp == 1) {
        $field_hpp = ' p.hargadasar ';
    } else {
        $field_hpp = '0';
    }
    //$tahun1=$tahun_skrg;
    $main_sql = "SELECT SQL_CALC_FOUND_ROWS p.kode_grade_a,kso.kode_15,p.nama,kso.stok_awal,(kso.stok_awal* $field_hpp ) AS hpp_awal
    ,(kso.stok_awal*p.hargajual) AS hpj_awal,kso.stok_akhir,(kso.stok_akhir* $field_hpp ) AS hpp_SO
    ,(kso.stok_akhir*p.hargajual) AS hpj_SO 
    ,kso.qty AS qty_koreksi
    ,(kso.qty* $field_hpp ) as hpp_koreksi
    ,(kso.qty*p.hargajual) as hpj_koreksi       
    ,kso.pabrik,kso.update_date
   FROM $table_koreksi_stok AS kso LEFT JOIN produk AS p  
   ON (p.kode = kso.kode_15) WHERE kso.pabrik='$txt_pabrik' AND 
   kso.tanggal='$tgl1' $filter order by kso.kode_15 ";

    session_start();
    $_SESSION['export_pabrik'] = $main_sql;

    $_SESSION['tanggal1'] = $tgl1;

    $main_sql .= " limit $offset,$dataPerPage";
    // untuk pagging hitung jumlah data di tabel t_postingan

    $query    = "SELECT COUNT(kso.kode_15) as jumData 
    FROM $table_koreksi_stok AS kso LEFT JOIN produk AS p  
    ON (p.kode = kso.kode_15) 
    WHERE pabrik='$txt_pabrik' AND tanggal like'%$tgl1%' $filter ";

    $query = "SELECT FOUND_ROWS() as jumData";
}

if ($username == 'iwan-it' || $username == 'B120938_ahmad') {
    echo "$main_sql";
}

$q_utama = mysql_query($main_sql) or die("wrong query !!");
$hasil    = mysql_query($query) or die('wrong syntax query');
$data     = mysql_fetch_array($hasil);
$jumData  = $data['jumData'];
$jumPage = ceil($jumData / $dataPerPage);
?>

<script>
    $(document).ready(function() {

        activateAutoCompleteAll();

    });

    function activateAutoCompleteAll() {

        activateAutoComplete($('#txt_pabrik'));
        activateAutoComplete($('#pabrik'));

    }

    function activateAutoComplete(component) {
        component.chosen({});
    }
</script>


<p>&nbsp;</p>
<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>?action=searchxjgsx" name="f_pabrik" id="f_pabrik">
    <table class="table table-bordered">
        <tr>
            <td style="width:100px;">Pabrik</td>
           
            <td>
                <select style="width:300px" name="txt_pabrik" id="pabrik">
                    <option value="">-- Pilih Wilayah Pabrik --</option>
                    <?php
                    $sql = "SELECT $sql_cache id, nama from pabrik where status='1' AND id $_pabrik";
                    $hsltemp = mysql_query($sql, $db);
                    while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
                    ?>
                        <option value="<?php echo $id; ?>" <?php
                                                            if ($txt_pabrik == $id) {
                                                                echo "selected";
                                                            } ?>>
                            <?php
                            echo "$id [$nama]";
                            ?>
                        </option>
                    <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Tanggal</td>
           
            <td><input type="text" style="width:120px" name="tgl1" id="tgl1" value="<?php echo $tgl1 ?>">
                <script language="JavaScript">
                    new tcal({
                        'formname': 'f_pabrik',
                        'controlname': 'tgl1'
                    });
                </script>&nbsp;
                &nbsp;
            </td>
        </tr>
        <tr id="barcodedata">
            <td>Barcode 15</td>
         
            <td><input type="text" style="width:250px" name="barcode" id="barcode"  value="<?php echo $barcode ?>" /></td>
        </tr>
        <tr>
            <td>Nama</td>
          
            <td><input type="text" style="width:250px" name="txt_nama" id="txt_nama"  value="<?php echo $txt_nama; ?>" /></td>
        </tr>
        <tr>
            <td><button type="submit" class="btn btn-warning btn-block" value="Cari">Cari</button></td>
          
            <td>&nbsp;</td>
        </tr>

    </table>
</form>

<fieldset id="upload_file" style="display:none;">
    <legend>Upload File Pernyataan</legend>
    <form action="upload_bukti_so.php" method="post" enctype="multipart/form-data" name="form-upload" id="form-upload">
        <input type="text" name="tgl" id="v_tgl" value="" />
        <input type="text" name="gudang" id="v_gudang" value="" />
        <input type="text" name="jenis" id="v_jenis_gudang" value="" />
        <input type="file" name="fupload" id="fupload" /> <input type="button" id="tutup" value="Tutup" />
        <p id="up-result"></p>
    </form>
</fieldset>


<?php
$sql = "SELECT $sql_cache id, nama from pabrik where status='1' AND id='$txt_pabrik'";
$hsltemp = mysql_query($sql, $db);
while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
?>
    Laporan SO Pabrik : <?php echo "$outlet&nbsp;$id&nbsp;$nama&nbsp;$tgl1"; ?>
<?php
}
?>

<?php
$sql = "SELECT file_nama FROM koreksi_outlet_stok_bukti WHERE tanggal LIKE '$tgl1%' and kode_outlet='$txt_pabrik';";
$res = mysql_query($sql); //
list($pernyataan_so) = mysql_fetch_array($res);
?>
<br />
<!-- Pernyataan SO :
<span id="pr_<?php echo $txt_pabrik; ?>">
    <?php
    if ($pernyataan_so != '' && $txt_pabrik != '') {
    ?>
        <a href="bukti_koreksi/<?php echo $pernyataan_so; ?>" target="_blank"><img src="images/view-details.png" width="20" height="20" /></a>
    <?php
    }
    ?>
</span> -->

<!-- <span style="margin-left:20px;" id='up_<?php echo $txt_pabrik; ?>' onclick="uploadPernyataan('<?php echo $txt_pabrik; ?>','<?php echo $tgl1; ?>','pabrik');"> <img src="images/b_edit.png" width="20" height="20" /></span> -->
<br />
<?php
if ($isShowHpp) {
?>
    <input type="checkbox" name="cb_hpp" id="cb_hpp" /> HPP
<?php
}
?>
<span id="showSql" style="color:#00F;cursor:pointer;<?php if ($username != 'budi-it') {
                                                        echo 'display:none;';
                                                    } ?>">Show Debug</span>
<br />
<span id="dbgSQL" style="display:none;">
    <?php
    if ($username == 'budi-it') {
        echo " $main_sql <br/>";
    }
    if ($username == 'budi-it') {
        if (!$q_utama) {
            echo 'ERROR :' . mysql_error() . '</br>';
        }
        if (!$res_total) {
            echo 'ERROR :' . mysql_error() . '</br>';
        }
    }
    ?>
</span>
<br />

<form name="export" method="post" action="lapoarn_so_suho_perpabrik_pertanggal_print.php?action=export" target="_blank">
    <input type="hidden" name="barcode" id="f_barcode" value="<?php echo $barcode ?>" />
    <input type="hidden" name="pabrik" id="f_pabrik" value="<?php echo $txt_pabrik; ?>" />
    <input type="hidden" name="tgl1" id="f_tgl1" value="<?php echo $tgl1; ?>" />
    <button name="Submit" class="btn btn-success btn-block" style="width: 150px" type="submit" value="Export Data" >Export Data</button>
    <input type="hidden" id="datatodisplay" name="datatodisplay">
</form>

</form>
<table border="1" width="100%" class="table_q table_q-striped table_q-hover sortable" id="ReportTable">
    <thead>
        <tr class="header_table_q">
            <td widthalign="center" rowspan="2"><strong>&nbsp;No</strong></td>
            <td align="center" rowspan="2"><strong>Barcode 13</strong></td>
            <td align="center" rowspan="2"><strong>&nbsp;Barcode 15</strong></td>
            <td align="center" rowspan="2"><strong>&nbsp;Nama produk</strong></td>
            <td align="center" colspan="3" align="center" class="header head_dt">&nbsp;<strong>Data Stock Pagi</strong></td>
            <td align="center" colspan="3" align="center" class="header head_dt">&nbsp;<strong>Stock Opname</strong></td>
            <td align="center" colspan="3" align="center" class="header head_dt">&nbsp;<strong>Selisih (Plus/Minus)</strong></td>
            <td align="center" rowspan="2" align="center">&nbsp;<strong>Tanggal update</strong></td>
        </tr>


        <tr style="height:20px" class="header_table_q">
            <td align="center"><b>&nbsp;&nbsp;Qty</b></td>
            <td align="center" class="hpp body_hpp"><b>&nbsp;&nbsp;HPP</b></td>
            <td align="center"><b>&nbsp;&nbsp;HPJ</b></td>
            <td align="center"><b>&nbsp;&nbsp;Qty</b></td>
            <td align="center" class="hpp body_hpp"><b>&nbsp;&nbsp;HPP</b></td>
            <td align="center"><b>&nbsp;&nbsp;HPJ</b></td>
            <td align="center"><b>&nbsp;&nbsp;Qty</b></td>
            <td align="center" class="hpp body_hpp"><b>&nbsp;&nbsp;HPP</b></td>
            <td align="center"><b>&nbsp;&nbsp;HPJ</b></td>
        </tr>
    </thead>
    <?php

    $total_stok_awal = 0;
    $total_hpp_awal = 0;
    $total_hpj_awal = 0;
    $total_stok_so = 0;
    $total_hpp_so = 0;
    $total_hpj_so = 0;
    $total_qty_koreksi = 0;
    $total_hpp_koreksi = 0;
    $total_hpj_koreksi = 0;
    while (list($kode13, $kode, $nama_item, $stok_awal, $hpp_awal, $hpj_awal, $stok_so, $hpp_so, $hpj_so, $qty_koreksi, $hpp_koreksi, $hpj_koreksi, $kd_outlet, $update_date) = mysql_fetch_array($q_utama)) {
        $no++;
        $offset++;
        if ($username == 'budi-it' || $username == 'faipusat_yati' || $isShowHpp == 1) { //tambahan 23122015 request p yudi

        } else {
            $hpp_awal = 0;
            $hpp_so = 0;
            $hpp_koreksi = 0;
        }
        $total_stok_awal += $stok_awal;
        $total_hpp_awal += $hpp_awal;
        $total_hpj_awal += $hpj_awal;
        $total_stok_so += $stok_so;
        $total_hpp_so += $hpp_so;
        $total_hpj_so += $hpj_so;
        $total_qty_koreksi += $qty_koreksi;
        $total_hpp_koreksi += $hpp_koreksi;
        $total_hpj_koreksi += $hpj_koreksi;
        $bgclr1 = "#FFFFCC";
        $bgclr2 = "#E0FF9F";
        $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
        $cek_outlet = $_SESSION['outlet'];
        /*if ($cek_outlet!=''){
$hpp_awal=0;
$hpp_so=0;
$hpp_koreksi=0;
}*/


    ?>
        <tr style="height:25">
            <td>&nbsp;<?php echo $offset; ?></td>
            <td><?php echo $kode13; ?></td>
            <td><?php echo $kode; ?></td>
            <td>&nbsp;<?php echo $nama_item; ?></td>
            <td align="right" bgcolor="#9DB3D9">&nbsp;<?php echo number_format($stok_awal); ?></td>
            <td align="right" bgcolor="#9DB3D9" class="hpp body_hpp">&nbsp;<?php echo number_format($hpp_awal); ?></td>
            <td align="right" bgcolor="#9DB3D9">&nbsp;<?php echo number_format($hpj_awal); ?></td>
            <td align="right" bgcolor="#FFCC99">&nbsp;<?php echo number_format($stok_so); ?></td>
            <td align="right" bgcolor="#FFCC99" class="hpp body_hpp">&nbsp;<?php echo number_format($hpp_so); ?></td>
            <td align="right" bgcolor="#FFCC99">&nbsp;<?php echo number_format($hpj_so); ?></td>
            <td align="right" bgcolor="#E06A67">&nbsp;<?php echo number_format($qty_koreksi); ?></td>
            <td align="right" bgcolor="#E06A67" class="hpp body_hpp">&nbsp;<?php echo number_format($hpp_koreksi); ?></td>
            <td align="right" bgcolor="#E06A67">&nbsp;<?php echo number_format($hpj_koreksi) ?></td>
            <td colspan="3" align="right">&nbsp;<?php echo $update_date; ?></td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr class="footer_table_q">

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td align="right">&nbsp;<?php echo number_format($total_stok_awal); ?></td>
            <td align="right" class="hpp body_hpp">&nbsp;<?php echo number_format($total_hpp_awal); ?></td>
            <td align="right">&nbsp;<?php echo number_format($total_hpj_awal); ?></td>
            <td align="right">&nbsp;<?php echo number_format($total_stok_so); ?></td>
            <td align="right" class="hpp body_hpp">&nbsp;<?php echo number_format($total_hpp_so); ?></td>
            <td align="right">&nbsp;<?php echo number_format($total_hpj_so); ?></td>
            <td align="right">&nbsp;<?php echo number_format($total_qty_koreksi); ?></td>
            <td align="right" class="hpp body_hpp">&nbsp;<?php echo number_format($total_hpp_koreksi); ?></td>
            <td align="right">&nbsp;<?php echo number_format($total_hpj_koreksi); ?></td>
            <td>&nbsp;</td>

        </tr>
        <tr class="footer_table_q">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td id="totalSa" align="right">...</td>
            <td id="totalHppSa" align="right" class="hpp body_hpp">...</td>
            <td id="totalHpjSa" align="right">...</td>
            <td id="totalSo" align="right">...</td>
            <td id="totalHppSo" align="right" class="hpp body_hpp">...</td>
            <td id="totalHpjSo" align="right">...</td>
            <td id="totalSk" align="right">...</td>
            <td id="totalHppSk" align="right" class="hpp body_hpp"> ...</td>
            <td id="totalHpjSk" align="right">...</td>
            <td>&nbsp;</td>
        </tr>
    </tfoot>
</table>
<?php

?>
<p></p>
<div align="center">
    <font size=2> page:
        <?php
        if ($noPage > 1) echo "<a href='" . $_SERVER['PHP_SELF'] . "?page=" . ($noPage - 1) . "'><span class=page>
	         <font size=2>&lt;&lt; Previous </span></a>      ";
        // memunculkan nomor halaman dan linknya
        for ($page = 1; $page <= $jumPage; $page++) {
            if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage)) {
                if (($showPage == 1) && ($page != 2))
                    echo "...";
                if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  echo "...";
                if ($page == $noPage) echo " <b><font size=3>" . $page . "</b>&nbsp; ";
                else echo " <font size=2><a href='" . $_SERVER['PHP_SELF'] . "?page=" . $page . "'><span class=page>" . $page . "</span></a>
			     &nbsp;</a> ";
                $showPage = $page;
            }
        }
        // menampilkan link next
        if ($noPage < $jumPage) echo "<a href='" . $_SERVER['PHP_SELF'] . "?page=" . ($noPage + 1) . "'>
		  <font size=2><span class=page> Next    &gt;&gt;</span></a>";
        ?>
</div>
<h3>Data yang diupload</h3>
<table width="675" border="1">
    <tr>
        <td width="20" bgcolor="#0066FF">No</td>
        <td width="302" bgcolor="#0066FF">Nama File</td>
        <td width="42" bgcolor="#0066FF">Qty</td>
        <td width="56" bgcolor="#0066FF">Upload Time</td>
        <td width="149" bgcolor="#0066FF">Approved Time</td>
        <td width="66" bgcolor="#0066FF">Approved By</td>
        <td width="66" bgcolor="#0066FF">action</td>
    </tr>
    <?php
    $sql = "SELECT  kode_temp,DATE_FORMAT(uploadtime,'%Y-%m-%d %T'),DATE_FORMAT(approveddate,'%Y-%m-%d %T'),approvedby,sum(qty) FROM temp_koreksi_outlet_v2 
WHERE tgl_koreksi LIKE '$tgl1%' AND kode_gudang LIKE '$pabrik%' group by kode_temp order by approveddate desc";
    // echo $sql;
    $res = mysql_query($sql) or die($sql);
    $no = 0;
    $today = date('Y-m-d');
    if ($tgl1 == $today) { //berarti boleh dibatalkan

        $action = 'batal';
    } else {
        $action = '';
    }


    #echo substr($username,0,4);
    if ($username == 'budi-it' || substr($username, 0, 4) == 'ulen' || substr($username, 0, 4) == 'meva') {
        $action = 'batal';
    }
    $total_qty = 0;
    $today = date('Y-m-d');
    while (list($nama_file, $uploadtime, $approveddate, $approveby, $qty) = mysql_fetch_array($res)) {
        $no++;
        $total_qty += $qty;
        if ($username == 'budi-it' || substr($username, 0, 4) == 'ulen' || substr($username, 0, 4) == 'meva') {
            $action = 'batal';
        } else {
            //asal ganti tanggal 11 nov 2013  if($approveby==$username&&substr($approveddate,0,10)){		   
            if (substr($approveddate, 0, 10) == $today) {
                $action = 'batal';
            } else {
            }
        }

    ?>
        <tr id="<?php echo $nama_file; ?>">
            <td><?php echo $no ?>&nbsp;</td>
            <td style="cursor:pointer" onclick="showDetail('<?php echo $nama_file; ?>')"><u><?php echo $nama_file; ?>&nbsp;</u></td>
            <td><?php echo $qty; ?>&nbsp;</td>
            <td><?php echo $uploadtime; ?>&nbsp;</td>
            <td><?php echo $approveddate; //. ' ('.substr($approveddate,0,10).')'; 
                ?>&nbsp;</td>
            <td><?php echo $approveby; ?>&nbsp;</td>
            <td><a href="#" onclick="deleteDataSo('<?php echo $nama_file; ?>')"><?php echo $action; ?></a></td>
        </tr>
    <?php
    }
    //`no_stok_opname`,`kode_outlet`,`nip_sm`
    $sql = "SELECT  `no_stok_opname`,DATE_FORMAT(tgl_koreksi,'%Y-%m-%d'),DATE_FORMAT(update_date,'%Y-%m-%d %T'),nip_sm,'-' as qty FROM koreksi_outlet_stok_validasi
WHERE tgl_koreksi LIKE '$tgl1%' AND kode_outlet LIKE '$pabrik%' order by update_date desc";
    // echo $sql;
    $res = mysql_query($sql) or die($sql);
    while (list($nama_file, $uploadtime, $approveddate, $approveby, $qty) = mysql_fetch_array($res)) {
        $no++;
        if (substr($approveddate, 0, 10) == $today) {
            $action = 'batal';
        } else {
        }
    ?>
        <tr id="<?php echo $nama_file; ?>">
            <td><?php echo $no ?>&nbsp;</td>
            <td style="cursor:pointer" onclick="showDetail('<?php echo $nama_file; ?>')"><u><?php echo $nama_file; ?>&nbsp;</u></td>
            <td><?php echo $qty; ?>&nbsp;</td>
            <td><?php echo $uploadtime; ?>&nbsp;</td>
            <td><?php echo $approveddate; ?>&nbsp;</td>
            <td><?php echo $approveby; ?>&nbsp;</td>
            <td><a href="#" onclick="deleteDataSo('<?php echo $nama_file; ?>')"><?php echo $action; ?></a></td>
        </tr>
    <?php
    }
    ?>
    <tr>
        <td bgcolor="#0066FF">&nbsp;</td>
        <td bgcolor="#0066FF">&nbsp;</td>
        <td bgcolor="#0066FF"><?php echo number_format($total_qty); ?></td>
        <td bgcolor="#0066FF">&nbsp;</td>
        <td bgcolor="#0066FF">&nbsp;</td>
        <td bgcolor="#0066FF" id="proc">&nbsp;</td>
        <td bgcolor="#0066FF">
            <?php
            if ($username == 'budi-it' || substr($username, 0, 4) == 'ulen' || substr($username, 0, 4) == 'meva') {
                //echo '<input name="loadall" type="button" value="load all to Koreksi" onclick="loadAllToKoreksi()"/>';
            }
            ?>
            &nbsp;</td>
    </tr>
</table>
<form name="formDetail" id="formDetail" method="POST" action="rekapupload_stok.php" target="_blank" style="display:none;">
    <input name="kode_temp" id="kode_temp" type="text" />
    <input name="j" id="kode_temp" type="text" value="o" />

</form>


<script>
    var isShowHpp = 0;
    <?php
    if ($isShowHpp == 1) {
        echo "isShowHpp=1;";
    }
    ?>
</script>
<?php //mysql_close(); 
include_once "footer.php"; ?>