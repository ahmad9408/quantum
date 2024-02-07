<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  $content_title = "Rekap Penerimaan Invoice FOB";
  include('header.php');
}
include "config.php";
include("css_group.php");
// include_once('DateControl.php');
// $dc = new DateControl();

// $tahun_skrg = date('Y');
// $bulan_skrg = date('m');



if (isset($_REQUEST['hal'])) {
  $tgl1  = $_SESSION['tgl1'];
  $tgl2  = $_SESSION['tgl2'];
  $tgl_tempo  = $_SESSION['tgl_tempo'];
  $pabrik1 = $_SESSION['pabrik'];
  $data_tampil = $_SESSION['data_tampil'];
} elseif (isset($_REQUEST['action'])) {

  $pabrik1 = $_POST['pabrik'];
  $data_tampil = sanitasi($_POST['data_tampil']);

  $_SESSION['pabrik'] = $pabrik1;
  $_SESSION['tgl_tempo'] = $tgl_tempo;
  $_SESSION['data_tampil'] = $data_tampil;

  $tgl1  = $_POST['tgl1'];
  $tgl2  = $_POST['tgl2'];
  $tgl_tempo  = $_POST['tgl_tempo'];
  $_SESSION['tgl1'] = $tgl1;
  $_SESSION['tgl2'] = $tgl2;
  $_SESSION['tgl_tempo'] = $tgl_tempo;
} else {

  $data_tampil = 20;
}

if (empty($tgl1)) {
  $tgl1 = date('Y-01-01');
}
if (empty($tgl2)) {
  $tgl2 = date('Y-m-d');
}
if (isset($_GET['hal'])) $hal = $_GET['hal'];
else $hal = 0;

if (empty($data_tampil)) {
  $data_tampil = 20;
}
$jmlHal = $data_tampil;
$page = $hal;


$_pabrik = " like '%'";
if (strtoupper($_SESSION['outlettype']) == "P") {
  $_pabrik = $_SESSION['outlet'];
  if ($_pabrik == 'P0006') {
    $sql = "select id from pabrik where mk='1' ";
    $resri = mysql_query($sql) or die($sql);
    $banyak_pabrik = mysql_num_rows($resri);
    while (list($kd_pabrik) = mysql_fetch_array($resri)) {
      $j2++;
      if ($j2 == $banyak_pabrik) {
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

if ($pabrik1 != "") {
  $pabrik2 = "AND id_supplier = '$pabrik1'";
} else {
  $pabrik2 = "";
}

if ($tgl_tempo != "") {
  $tgl_tempo1 = "AND tgl_harus_bayar = '$tgl_tempo'";
} else {
  $tgl_tempo1 = "";
}

if ($status_approve1 == 0) {
  $status_approve2 = "";
} elseif ($status_approve1 == 1) {
  $status_approve2 = " AND approve2 = 0 ";
} elseif ($status_approve1 == 2) {
  $status_approve2 = " AND approve2 = 1 ";
}




// if($username=='B120938_ahmad'){
// echo $sql."<br/>";	
// }
?>

<style type="text/css">
  .judul_tabel td {
    font-weight: bold;
    background-color: #f39c7d;
    height: 14px;
  }
</style>
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="app_libs/fob_invoice.js?d=<?php echo date('YmdHis') ?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css" />

<form method="POST" action="<? echo $PHP_SELF; ?>?action=search&rnd=<?php echo date('YmdHis'); ?>" name="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <tr>
        <td style="width:100px;">Tgl. Invoice Dari</td>
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
        <td width="50">
          <input class="form-control" type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1; ?>" style="font-size: 8pt;width:100px;" size="10" />
        </td>
        <td style="width:100px;">Sampai</td>
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
      <td style="width:100px;">Tgl. Jatuh Tempo</td>
        <td width="5">
          <script language="JavaScript">
            new tcal({
              // form name
              'formname': 'f1',
              // input name
              'controlname': 'tgl_tempo'
            });
          </script>
        </td>
        <td width="50">
          <input class="form-control" type="text" name="tgl_tempo" id="tgl_tempo" value="<?php echo $tgl_tempo; ?>" style="font-size: 8pt;width:100px;" size="10" />
        </td>
      <tr>
        <td>Supplier</td>
        <td colspan="4">
          <select style="width:300px" name="pabrik" id="pabrik" class="form-control">
            <option value="">-- All Supplier --</option>
            <?php
            $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id_group=2";
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
          </select>
        </td>
        <td style="font-size: 8pt;width:100px;">Jumlah Data</td>
        <td colspan="5"><label for="textfield"></label>
          <input style="font-size: 8pt;width:50px;" class="form-control" type="text" name="data_tampil" id="data_tampil" value="<?php echo $data_tampil; ?>" />
        </td>

      </tr>
      <!-- <tr>
        <td>Status Approve</td>
        <td>:</td>
        <td>
          <select class="form-control" style="width:250px" name="status_approve" id="status_approve">
          <option value="0"<?php if ($status_approve1  == "0") {
                              echo "selected";
                            } ?>>-- Silahkan Pilih --</option>
            <option value="1"<?php if ($status_approve1  == "1") {
                                echo "selected";
                              } ?>>Belum Approve 2</option>
            <option value="2"<?php if ($status_approve1  == "2") {
                                echo "selected";
                              } ?>>Sudah Approve 2</option>
          </select>
        </td>
      </tr> -->
      <!-- <tr>
        <td>Jumlah Data</td>
        <td colspan="5"><label for="textfield"></label>
          <input style="font-size: 8pt;width:50px;" class="form-control" type="text" name="data_tampil" id="data_tampil" value="<?php echo $data_tampil; ?>" />
        </td>
      </tr>
      </tr> -->
      <tr>
        <td></td>
        <td colspan='5'><button type="submit" name="submit" class="btn btn-info" value="Cari">Cari</button></td>
      </tr>
      <tr>
        <td></td>
        <td colspan="5"> <input type="checkbox" id="tampildetail">&nbsp;Tampil Detail</td>
      </tr>
    </table>
  </fieldset>
</form>

<?php

$sql = "SELECT SQL_CALC_FOUND_ROWS 
                i.id_supplier
                ,p.nama
                ,i.id_invoice
                ,i.tanggal
                ,i.no_faktur_pajak
                ,i.tgl_harus_bayar
                ,i.total_qty
                ,i.total_jumlah
                ,i.total_ppn
                ,i.total_harga
                ,i.disc_nilai
                ,i.total_harus_bayar
                ,i.total_sudah_bayar
                ,i.total_sisa_bayar
                ,i.updateby
                ,i.updatedate
                ,i.status_pembayaran
                FROM
                fob_invoice AS i
                LEFT JOIN pabrik AS p
                ON (i.id_supplier = p.id)
                WHERE i.tanggal BETWEEN '$tgl1' AND '$tgl2' $tgl_tempo1 $pabrik2
                GROUP BY i.id_invoice
                ORDER BY i.updatedate DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- fob_invoice.php sql1';

if ($username == 'B120938_ahmad' || $username == 'iwan-it') { ?>
  <div style="display: none"><?php echo $sql . "<br/>"; ?></div>
<?php }



$query = mysql_query($sql) or die($sql);
$sql = "SELECT FOUND_ROWS()";
$q2 = mysql_query($sql);
list($jmlData) = mysql_fetch_array($q2);
$j = ($page * $jmlHal);
?>
<div align="left">
  <a href="fob_invoice_input.php"><button class="btn btn-primary">+ Invoice</button></a>&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="fob_pembayaran.php"><button class="btn btn-success">+ Payment</button></a>&nbsp;&nbsp;&nbsp;&nbsp;
  <a href="fob_pembayaran_history.php"><button class="btn btn-info">History Payment</button></a>
</div>
<br>

<table style="margin-left:10px; margin-top:10px;">
  <tr>
    <td class="text_standard">
      Page :
      <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=0'">First</span>
      <?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
        if ($hal <= 0) { ?>
          <span class="<?php if ($i == $hal) echo "hal_select";
                        else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i + 1); ?></span>
          <?php if ($i >= 4) break;
        } else if (($hal + 1) >= ($jmlData / $jmlHal)) {
          if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i + 1); ?></span>
          <?php }
        } else {
          if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i + 1); ?></span>
      <?php }
        }
      } ?>
      <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo intval(($jmlData / $jmlHal)); ?>'">Last</span>
      &nbsp;&nbsp;
      Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo $jmlData; ?>
    </td>
  </tr>
</table>
<br />

<table style="border-spacing: 30px" border="1" width="100%" cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
  <thead>
    <tr class="header_table_q">
      <td align="center">No</td>
      <!--   <td>Id Receiving</td> -->
      <td align="center" height='50' style="width:220px;">Supplier </td>
      <td align="center">No Invoice</td>
      <td align="center">Tgl Invoice</td>
      <td align="center">No Faktur Pajak</td>
      <td align="center">Tgl Jatuh Tempo</td>
      <td align="center" class="detail_baris">Tgl Surat Jalan</td>
      <td align="center" class="detail_baris">No. Surat Jalan</td>
      <td align="center" class="detail_baris">Qty</td>
      <td align="center" class="detail_baris">Deskripsi</td>
      <td align="center" class="detail_baris">Harga</td>
      <td align="center">Total Jumlah</td>
      <td align="center">Total PPN</td>
      <td align="center">Total Harga</td>
      <td align="center" class="detail_baris">Diskon %</td>
      <td align="center">Total Diskon</td>
      <td align="center">Total Harus Bayar</td>
      <td align="center">Total Sudah Bayar</td>
      <td align="center">Total Sisa Bayar</td>
      <td align="center">Update By</td>
      <td align="center">Update Date</td>
      <td align="center">Status Pembayaran</td>
      <td align="center">Action</td>
    </tr>
  </thead>
  <?php
  while (list(
    $id_supplier,
    $nama_supplier,
    $id_invoice,
    $tgl_jatuhtempo,
    $faktur_pajak,
    $tgl_hrs_byr,
    $tot_qty,
    $totsubtotal,
    $totppn,
    $totharga,
    $dis_nilai,
    $tottotal_bayar,
    $tottotal_sudah_bayar,
    $tottotal_sisa_bayar,
    $updateby,
    $updatedate,
    $status_pembayaran
  ) = mysql_fetch_array($query)) {

    $j++;
    // $bgclr1 = "#FFFFCC";
    // $bgclr2 = "#E0FF9F";
    // $bgcolor = ($j % 2) ? $bgclr1 : $bgclr2;



  ?>
    <tr>
      <td bgcolor="#FFFFCC" height="43" align="center" width="40"><strong><?php echo $j; ?></strong></td>
      <td bgcolor="#FFFFCC" width="250" style="padding-left: 10px" align="left"><strong><?php echo $id_supplier . " - " . $nama_supplier; ?></strong></td>
      <td bgcolor="#FFFFCC" width="250" style="padding-left: 10px" align="left"><strong><?php echo $id_invoice; ?></strong></td>
      <td bgcolor="#FFFFCC" width="100" align="center"><strong><?php echo $tgl_jatuhtempo; ?></strong></td>
      <?php
      if ($faktur_pajak == "") {
        $keterangan_pajak = "-";
      } else {
        $keterangan_pajak = $faktur_pajak;
      }
      ?>
      <td bgcolor="#FFFFCC" width="250" style="padding-left: 10px" align="left"><strong><?php echo $keterangan_pajak; ?></strong></td>
      <td bgcolor="#FFFFCC" width="100" align="center"><strong><?php echo $tgl_hrs_byr; ?></strong></td>
      <td bgcolor="#FFFFCC" width="250" class="detail_baris" style="padding-left: 10px" align="left" colspan="2"><strong><?php echo " - "; ?></strong></td>
      <td bgcolor="#FFFFCC" width="250" class="detail_baris" style="padding-left: 10px" align="center"><strong><?php echo $tot_qty;
                                                                                                                $tot_qty1 = $tot_qty1 + $tot_qty; ?></strong></td>
      <td bgcolor="#FFFFCC" width="250" class="detail_baris" style="padding-left: 10px" align="left" colspan="2"><strong><?php echo " - "; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($totsubtotal, "2", ".", ",");
                                                                                          $totsubtotal2 = $totsubtotal2 + $totsubtotal; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($totppn, "2", ".", ",");
                                                                                          $totppn2 = $totppn2 + $totppn; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($totharga, "2", ".", ",");
                                                                                          $totharga2 = $totharga2 + $totharga; ?></strong></td>
      <td bgcolor="#FFFFCC" width="250" class="detail_baris" style="padding-left: 10px" align="left"><strong><?php echo " - "; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($dis_nilai, "2", ".", ",");
                                                                                          $dis_nilai2 = $dis_nilai2 + $dis_nilai; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($tottotal_bayar, "2", ".", ",");
                                                                                          $tottotal_bayar2 = $tottotal_bayar2 + $tottotal_bayar; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($tottotal_sudah_bayar, "2", ".", ",");
                                                                                          $tottotal_sudah_bayar2 = $tottotal_sudah_bayar2 + $tottotal_sudah_bayar; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" style="padding-right: 10px" align="right"><strong><?php echo number_format($tottotal_sisa_bayar, "2", ".", ",");
                                                                                          $tottotal_sisa_bayar2 = $tottotal_sisa_bayar2 + $tottotal_sisa_bayar; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" align="center"><strong><?php echo $updateby; ?></strong></td>
      <td bgcolor="#FFFFCC" width="150" align="center"><strong><?php echo $updatedate; ?></strong></td>
      <?php
      if ($status_pembayaran != "1") {
        $keterangan = "<blink><font color='red'><b>Belum Lunas</b></font></blink>";
      } else {
        $keterangan = "<b><font color='#0099FF'>Lunas</b>";
      }
      ?>
      <td bgcolor="#FFFFCC" width="100" align="center"><strong><?php echo $keterangan; ?></strong></td>
      <td bgcolor="#FFFFCC" width="100" align="center"><strong><a href="fob_invoice_detail_input.php?no_inv=<?php echo $id_invoice; ?>">+ Add Surat Jalan</a></strong></td>

      <?php
      $sql2 = "SELECT
              id_invoice
              ,tgl_datang
              ,id_suratjalan
              ,qty
              ,keterangan
              ,subtotal
              ,ppn
              ,total_harga
              ,disc_persen
              ,disc_nilai
              ,total_harus_bayar
              ,total_bayar
              ,sisa_bayar
              ,approve_date
              ,approve_by
              ,tgl_jatuhtempo
              FROM
              fob_receiving
              WHERE id_invoice='$id_invoice'
              $pabrik2 ";
      $query2 = mysql_query($sql2) or die($sql2);
      while (list(
        $id_invoice2,
        $tgl_datang,
        $id_suratjalan,
        $qty,
        $keterangan,
        $subtotal,
        $ppn,
        $total_harga,
        $disc_persen,
        $disc_nilai,
        $total_bayar,
        $sudah_bayar,
        $sisa_bayar,
        $approve_date,
        $approve_by
      ) = mysql_fetch_array($query2)) {
      ?>
    <tr class="detail_baris">
      <td bgcolor="#E0FF9F" class="detail_baris" colspan="6"></td>
      <td bgcolor="#E0FF9F" height="43" width="250" class="detail_baris" style="padding-left: 10px" align="left"><?php echo $tgl_datang; ?></td>
      <td bgcolor="#E0FF9F" width="250" class="detail_baris" style="padding-left: 10px" align="left"><?php echo $id_suratjalan; ?></td>
      <td bgcolor="#E0FF9F" width="250" class="detail_baris" style="padding-left: 10px" align="center"><?php echo $qty; ?></td>
      <td bgcolor="#E0FF9F" width="250" class="detail_baris" style="padding-left: 10px" align="left"><?php echo $keterangan; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($harga = $subtotal / $qty, "2", ".", ","); ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($subtotal, "2", ".", ",");
                                                                                                        $totsubtotal = $totsubtotal + $subtotal; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($ppn, "2", ".", ",");
                                                                                                        $totppn = $totppn + $ppn; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($total_harga, "2", ".", ",");
                                                                                                        $total_harga2 = $total_harga2 + $total_harga; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($disc_persen, "2", ".", ",");
                                                                                                        $disc_persen2 = $disc_persen2 + $disc_persen; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($disc_nilai, "2", ".", ",");
                                                                                                        $disc_nilai2 = $disc_nilai2 + $disc_nilai; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($total_bayar, "2", ".", ",");
                                                                                                        $totbayar = $totbayar + $total_bayar; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($sudah_bayar, "2", ".", ",");
                                                                                                        $sudah_bayar2 = $sudah_bayar2 + $sudah_bayar; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo number_format($sisa_bayar, "2", ".", ",");
                                                                                                        $sisa_bayar2 = $sisa_bayar2 + $sisa_bayar; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo $approve_by; ?></td>
      <td bgcolor="#E0FF9F" width="150" class="detail_baris" style="padding-right: 10px" align="right"><?php echo $approve_date; ?></td>
      <td colspan="2" bgcolor="#E0FF9F" class="detail_baris"></td>
    </tr>
  <?php
      }
  ?>

  </tr>
<?php } ?>

<tr class="bg-header">
  <td height="50" colspan="6">Jumlah</td>
  <td class="detail_baris"></td>
  <td class="detail_baris"></td>
  <td class="detail_baris" style="padding-right: 10px" align="center"><?php echo $tot_qty1; ?></td>
  <td class="detail_baris"></td>
  <td class="detail_baris"></td>
  <td align="center"><strong><?php echo number_format($totsubtotal2, "2", ".", ","); ?></strong></td>
  <td align="center"><strong><?php echo number_format($totppn2, "2", ".", ","); ?></strong></td>
  <td align="center"><strong><?php echo number_format($totharga2, "2", ".", ","); ?></strong></td>
  <td class="detail_baris"></td>
  <td align="center"><strong><?php echo number_format($dis_nilai2, "2", ".", ","); ?></strong></td>
  <td align="center"><strong><?php echo number_format($tottotal_bayar2, "2", ".", ","); ?></strong></td>
  <td align="center"><strong><?php echo number_format($sudah_bayar2, "2", ".", ","); ?></strong></td>
  <td align="center"><strong><?php echo number_format($sisa_bayar2, "2", ".", ","); ?></strong></td>
  <td bgcolor="#E0FF9F" colspan="3"></td>
  <td></td>
</tr>
</table>
<!-- <script>
  var tgl = $("#tg").val();
  if (tgl != "") {
    $("#but").click();
  }
</script> -->
<table style="margin-left:10px; margin-top:10px;">
  <tr>
    <td class="text_standard">
      Page :
      <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=0'">First</span>
      <?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
        if ($hal <= 0) { ?>
          <span class="<?php if ($i == $hal) echo "hal_select";
                        else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i + 1); ?></span>
          <?php if ($i >= 4) break;
        } else if (($hal + 1) >= ($jmlData / $jmlHal)) {
          if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i + 1); ?></span>
          <?php }
        } else {
          if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i + 1); ?></span>
      <?php }
        }
      } ?>
      <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo intval(($jmlData / $jmlHal)); ?>'">Last</span>
      &nbsp;&nbsp;
      Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo $jmlData; ?>
    </td>
  </tr>
</table>

<?php include_once "footer.php" ?>