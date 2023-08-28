<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  $content_title = "Rekap Penerimaan Surat Jalan";
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
  $pabrik1 = $_SESSION['pabrik'];
  $data_tampil = $_SESSION['data_tampil'];
} elseif (isset($_REQUEST['action'])) {

  $pabrik1 = $_POST['pabrik'];
  $data_tampil = sanitasi($_POST['data_tampil']);

  $_SESSION['pabrik'] = $pabrik1;
  $_SESSION['data_tampil'] = $data_tampil;

  $tgl1  = $_POST['tgl1'];
  $tgl2  = $_POST['tgl2'];
  $_SESSION['tgl1'] = $tgl1;
  $_SESSION['tgl2'] = $tgl2;
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

if ($status_approve1 == 0) {
  $status_approve2 = "";
} elseif ($status_approve1 == 1) {
  $status_approve2 = " AND approve2 = 0 ";
} elseif ($status_approve1 == 2) {
  $status_approve2 = " AND approve2 = 1 ";
}


$isShowHargaMakloon = 0;
$sql = "SELECT COUNT(*) ada FROM user_account_privileges_parameter WHERE username='$username' AND is_show_harga_makloom=1;";
$res = mysql_query($sql);
list($ada) = mysql_fetch_array($res);

if ($ada > 0) {
  $isShowHargaMakloon = 1;
}

if ($isShowHargaMakloon == 1) {
  $class = "";
} else {
  $class = "detail_baris";
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
<script type="text/javascript" src="app_libs/fob_receiving.js?d=<?php echo date('YmdHis') ?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css" />

<form method="POST" action="<? echo $PHP_SELF; ?>?action=search&rnd=<?php echo date('YmdHis'); ?>" name="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <tr>
        <td style="width:150px;">Tgl. Surat Jln. Dari</td>
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
      <tr>
        <td>Supplier</td>
        <td colspan="4">
          <select style="width:300px" name="pabrik" id="pabrik" class="form-control">
            <option value="">-- All Supplier --</option>
            <?php
            $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id_group=2 ||  id_group=3";
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
      </tr> -->
      </tr>
      <tr>
        <td></td>
        <td colspan='5'><button type="submit" name="submit" class="btn btn-info" value="Cari">Cari</button></td>
      </tr>
      <!-- <tr>
        <td></td>
        <td colspan="5"> <input type="checkbox" id="tampildetail">&nbsp;Tampil Detail</td>
      </tr> -->
    </table>
  </fieldset>
</form>

<?php

$sql = "SELECT SQL_CALC_FOUND_ROWS 
        id_receiving,
        id_suratjalan,
        id_supplier,
        nama_supplier,
        keterangan,
        tgl_datang,
        qty,
        subtotal,
        ppn,
        total_harga,
        id_invoice,
        tgl_jatuhtempo,
        faktur_pajak,
        tgl_bayar,
        total_bayar,
        sisa_bayar,
        approve_date,
        approve_by,
        update_date,
        updateby,
        `status`,
        subtotal_harga_jual, 
        approve2, 
        approve2_date, 
        approve2_by 
        FROM fob_receiving 
        WHERE tgl_datang BETWEEN '$tgl1' AND '$tgl2' 
        AND id_suratjalan NOT LIKE '%btl%' $pabrik2 
        order by tgl_datang DESC " . " limit " . ($page * $jmlHal) . "," . $jmlHal . '; -- fob_receiving.php sql1';

if ($username == 'B120938_ahmad' || $username == 'iwan-it') { ?>
  <div style="display: none"><?php echo $sql . "<br/>"; ?></div>
<?php }

$query = mysql_query($sql) or die($sql);
$sql = "SELECT FOUND_ROWS()";
$q2 = mysql_query($sql);
list($jmlData) = mysql_fetch_array($q2);
$j = ($page * $jmlHal);
?>
<div align="left"><a href="fob_receiving_input.php"><button class="btn btn-primary">+ Receiving</button></a></div>
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
      <td align="center" height='50' style="width:220px;">Surat Jalan </td>
      <td align="center">Tanggal</td>
      <td align="center">Supplier</td>
      <td align="center">Qty</td>
      <td align="center">Deskripsi</td>
      <?php
      if ($isShowHargaMakloon == 1) {
        echo "<td align='center'>Subtotal</td>";
        echo "<td align='center'>PPN</td>";
        echo "<td align='center'>Total Harga Makloon</td>";
      }
      ?>
      <td align="center">Subtotal Harga Jual</td>
      <td align="center">Approve 1 Date</td>
      <td align="center">Approve 1 By</td>
      <td align="center">Approve 2 Date</td>
      <td align="center">Approve 2 By</td>
      <td align="center" class="detail_baris">No. Invoice</td>
      <td align="center" class="detail_baris">Tgl. Invoice</td>
      <td align="center" class="detail_baris">F_Pajak</td>
      <td align="center" class="detail_baris">Tgl. Bayar</td>
      <td align="center" class="detail_baris">Total Bayar</td>
      <td align="center" class="detail_baris">Sisa Bayar</td>
      <td align="center">Act</td>
    </tr>
  </thead>
  <?php
  while (list(
    $id_receiving,
    $id_suratjalan,
    $id_supplier,
    $nama_supplier,
    $keterangan,
    $tgl_datang,
    $qty,
    $subtotal,
    $ppn,
    $total_harga,
    $id_invoice,
    $tgl_jatuhtempo,
    $faktur_pajak,
    $tgl_bayar,
    $total_bayar,
    $sisa_bayar,
    $approve_date,
    $approve_by,
    $update_date,
    $updateby,
    $status,
    $subtotal_harga_jual,
    $approve2,
    $approve2_date,
    $approve2_by
  ) = mysql_fetch_array($query)) {

    $j++;
    $bgclr1 = "#FFFFCC";
    $bgclr2 = "#E0FF9F";
    $bgcolor = ($j % 2) ? $bgclr1 : $bgclr2;

    if ($approve2_date == '') {
      $apprv2_date = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
    } else {
      $apprv2_date = $approve2_date;
    }



    if ($approve2 != '1') {
      $apprv2 = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
    } else {
      $apprv2 = "<b>Approved [<font color='#0099FF'>$approve2_by</font>]</b>";
    }



  ?>
    <tr>
      <td height="43" align="center" width="40"><?php echo $j; ?></td>
      <!--  <td><?php echo $id_receiving; ?></td> -->
      <td width="120" style="padding-left: 10px" align="left"><?php echo $id_suratjalan; ?></td>
      <td width="100" align="center"><?php echo $tgl_datang; ?></td>
      <td width="300" style="padding-left: 10px" align="left"><?php echo $id_supplier . " - " . $nama_supplier; ?></td>
      <td width="50" align="center"><?php echo $qty; ?></td>
      <td width="250" style="padding-left: 10px" align="left"><?php echo $keterangan; ?></td>
      <?php
      if ($isShowHargaMakloon == 1) {
        echo "<td width='150' style='padding-right: 10px' align='right'>" . number_format($subtotal, 2, '.', ',') . "</td>";
        $totsubtotal = $totsubtotal + $subtotal;
        echo "<td width='150' style='padding-right: 10px' align='right'>" . number_format($ppn, 2, '.', ',') . "</td>";
        $totppn = $totppn + $ppn;
        echo "<td width='150' style='padding-right: 10px' align='right'>" . number_format($total_harga, 2, '.', ',') . "</td>";
        $totharga = $totharga + $total_harga;
      }
      ?>
      <td width="150" style="padding-right: 10px" align="right"><?php echo number_format($subtotal_harga_jual, "2", ".", ",");
                                                                $tothpj = $tothpj + $subtotal_harga_jual; ?></td>
      <td width="250" align="center"><?php echo $approve_date; ?></td>
      <td width="300" style="padding-left: 10px" align="left"><b>Approved [<font color='#0099FF'><?php echo $approve_by; ?></font>]</b></td>
      <td width="250" align="center"><?php echo $apprv2_date; ?></td>
      <td width="300" style="padding-left: 10px" align="left"><?php echo $apprv2; ?></td>
      <td width="250" style="padding-left: 10px" align="left" class="detail_baris"><?php echo $id_invoice; ?></td>
      <td width="100" align="center" class="detail_baris"><?php echo $tgl_jatuhtempo; ?></td>
      <td width="250" style="padding-left: 10px" align="left" class="detail_baris"><?php echo $faktur_pajak; ?></td>
      <td width="100" align="center" class="detail_baris"><?php echo $tgl_bayar; ?></td>
      <td width="150" style="padding-right: 10px" align="right" class="detail_baris"><?php echo number_format($total_bayar, "2", ".", ",");
                                                                                      $totbayar = $totbayar + $total_bayar; ?></td>
      <td width="150" style="padding-right: 10px" align="right" class="detail_baris"><?php echo number_format($sisa_bayar, "2", ".", ",");
                                                                                      $totsisa_bayar = $totsisa_bayar + $sisa_bayar; ?></td>
      <!--  <td><?php echo $approve_date; ?></td>
      <td><?php echo $approve_by; ?></td> -->
      <td width="100" align="center"><a href="fob_receiving_detail.php?id_suratjalan=<?php echo $id_suratjalan; ?>">Detail</a></td>

    </tr>
  <?php } ?>
  <tr class="bg-header">
    <td height="50" colspan="6">Jumlah</td>
    <td align="center" class="<?php echo $class; ?>"><strong><?php echo number_format($totsubtotal, "2", ".", ","); ?></strong></td>
    <td align="center" class="<?php echo $class; ?>"><strong><?php echo number_format($totppn, "2", ".", ","); ?></strong></td>
    <td align="center" class="<?php echo $class; ?>"><strong><?php echo number_format($totharga, "2", ".", ","); ?></strong></td>
    <td align="center"><strong><?php echo number_format($tothpj, "2", ".", ","); ?></strong></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris" align="center"><strong><?php echo number_format($totbayar, "2", ".", ","); ?></strong></td>
    <td class="detail_baris" align="center"><strong><?php echo number_format($totsisa_bayar, "2", ".", ","); ?></strong></td>
    <td colspan="5"></td>
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



<!-- percobaan di edit 2-->

<!-- percobaan di edit github-->

<?php include_once "footer.php" ?>
