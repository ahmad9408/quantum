<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

session_start();
@$username = $_SESSION["username"];
if (empty($username)) {
  die('You can\'t see this page');
}

if ($lihat == 1) {
  $content_title = "Input Penerimaan Invoice FOB";
  include('header.php');
}
include "config.php";
include("css_group.php");
include_once('DateControl.php');
$dc = new DateControl();

$tahun_skrg = date('Y');
$bulan_skrg = date('m');
$tgl_bayar = date('Y-m-d');

// if($username=='B120938_ahmad'){
// echo $sql."<br/>";	
// }
include_once "clsaddrow.php";
?>
<script type="text/javascript">
  function kurang() {
    //alert('oke');
    var hitung = $("#counter").val();
    hitung = Number(hitung) - 1;
    $("#counter").val(hitung);
  }


  function hitung() {
    //alert('oke');
    var hitung = $("#counter").val();
    hitung++;
    $("#counter").val(hitung);
  }

  function tambah_baris() {
    hitung();
    addRow(' ', 0, 0, 'tbldetail', 'bodydetail');

  }

  function kurang_baris() {
    kurang();
    addRow(' ', 1, 0, 'tbldetail', 'bodydetail');
  }
</script>
<style type="text/css">
  .judul_tabel td {
    font-weight: bold;
    background-color: #f39c7d;
    height: 14px;
  }
</style>

<?php
function no_transaksi()
{

  $id_pembayaran = $_GET['kode_pembayaran'];

  return $no_so = $id_pembayaran;
}

$notrans = no_transaksi();
$_SESSION['kode_pembayaran'] = $notrans;


$pabrik_asal = $_GET['pabrik_asal'];
?>




<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="app_libs/fob_pembayaran.js?d=<?php echo date('YmdHis') ?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css" />


<form method="post" action="fob_receiving.php?action=search" name="f1" id="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <?php
      $sql = "SELECT $sql_cache id_supplier,tgl_bayar,id_bank,rekening FROM fob_pembayaran WHERE id_pembayaran ='$notrans' ";
      $hsltemp = mysql_query($sql, $db);
      while (list($pabrik_asal, $tgl_bayar, $id_bank, $no_rek) = mysql_fetch_array($hsltemp)) {
      ?>
        <tr>
          <td>Id. Pembayaran</td>
          <td>
            <input type="text" name="id_pembayaran" id="id_pembayaran" value="<?php echo $notrans; ?>" style="width:300px" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td>Supplier</td>
          <td>
            <select style="width:300px" name="supplier" id="supplier" class="form-control">
              <?php
              $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id_group=2 AND id ='$pabrik_asal' ";
              $hsltemp = mysql_query($sql, $db);
              while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
              ?>
                <option value="<?php echo $id; ?>" <?php
                                                    if ($pabrik1 == $id) {
                                                      echo "selected";
                                                    } ?> readonly>
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
          <td style="width:100px;">Tanggal Bayar</td>
          <td>

            <!-- calendar attaches to existing form element -->
            <input type="text" name="tgl_bayar" id="tgl_bayar" value="<?php echo $tgl_bayar; ?>" style="width:150px" class="form-control" />
            &nbsp;
            <script language="JavaScript">
              new tcal({
                // form name
                'formname': 'f1',
                // input name
                'controlname': 'tgl_bayar'
              });
            </script>
          </td>
        </tr>

        <td style="width:100px;">Id. Bank</td>
        <td type="text" name="id_bank" id="id_bank" value="<?php echo $id_bank; ?>" style="width:300px"><?php echo $id_bank; ?>
        </td>
        </tr>
        <tr>
          <td style="width:100px;">No. Rekening</td>
          <td type="text" name="no_rek" id="no_rek" value="<?php echo $no_rek; ?>" style="width:300px"><?php echo $no_rek; ?>
          </td>
        </tr>
      <?php
      }
      ?>
      <!-- <tr>
        <td style="width:100px;">Total Bayar</td>
        <td type="text" name="nominal" id="nominal" value="<?php echo $nominal; ?>" style="width:300px" class="form-control" />
        </td>
      </tr> -->
      <tr>
        <td style="width:100px;">+ No invoice</td>
        <td>
          <input type="text" name="invoice_entry" id="invoice_entry" style="width:300px" onclick="showlistSJ(this.id);" class="form-control" />
        </td>
      </tr>
      <!-- <tr>
        <td><a href="fob_pembayaran_invoice.php"><input type="button" class="btn btn-success" value="+ Invoive" style="height:30px;"></a></td>
        <td><a href="fob_invoice.php"><input type="button" class="btn btn-info" value="Kembali" style="height:30px;"></a></td>
      </tr> -->
    </table>

  </fieldset>
  <!-- <div align="left">
    <img src='images/add.png' alt='add' align='middle' border='0' height='16' hspace='0' width='16' id="tambah_kode" onClick="tambah_baris()">
    <img src='images/remove.png' alt='inc' align='middle' border='0' height='16' hspace='0' width='16' id="hapus_kode" onClick="kurang_baris()">
  </div>
  <br> -->

  <?php
  $sql = "SELECT SQL_CACHE
  id_pembayaran
  ,id_invoice
  ,tgl_inv
  ,id_suratjalan
  ,tgl_sj
  ,nilai
  ,subtotal
  ,bayar_sebelumnya
  ,status
  ,updateby
  FROM
  fob_pembayaran_detail WHERE status ='0' AND updateby LIKE '$username' AND id_pembayaran = '$_SESSION[kode_pembayaran]'";

  $qshow = mysql_query($sql);
  //  echo $sql;		
  $no = 0;

  ?>
  <div id="transaksi">
    <table border="1" class="table table-hover table-striped">
      <thead>
        <tr class="bg-header">
          <td align="center" style="width: 200px;">No. Invoice</td>
          <td align="center" style="width: 100px;">Tgl. Invoice</td>
          <td align="center" style="width: 200px;">No. Surat Jalan</td>
          <td align="center" style="width: 100px;">Tgl. Surat Jalan</td>
          <td align="center" style="width: 250px;">Total Harus Bayar</td>
          <td align="center" style="width: 250px;">Total Sudah Di Bayar</td>
          <td align="center" style="width: 250px;">Total Sisa Bayar</td>
          <td align="center" style="width: 250px;">Total Yang Akan Di Bayar</td>
          <td align="center" style="width: 50px;">Action</td>
        </tr>
      </thead>
      <?php
      while (list(
        $id_pembayaran,
        $id_invoice,
        $tgl_inv,
        $id_suratjalan,
        $tgl_sj,
        $nilai,
        $subtotal,
        $bayar_sebelumnya,
        $status,
        $updateby
      ) = mysql_fetch_array($qshow)) {
        $no++;
      ?>
        <tbody>
          <tr>
            <td><?php echo $id_invoice ?></td>
            <td><?php echo $tgl_inv ?></td>
            <td><?php echo $id_suratjalan ?></td>
            <td><?php echo $tgl_sj ?></td>
            <td align="right"><?php echo number_format("$nilai", 2, ",", ","); ?></td>
            <td align="right"><?php echo number_format("$bayar_sebelumnya", 2, ",", ","); ?></td>
            <td align="right"><?php echo number_format("$nilai"-"$bayar_sebelumnya", 2, ",", ","); ?></td>
            <td id="tsubtotal_<?php echo $id_suratjalan; ?>" onDblClick="tampil_edit('<?php echo $id_suratjalan ?>','<?php echo $_SESSION['kode_pembayaran']; ?>','<?php echo $nilai ?>','<?php echo $subtotal ?>')"><?php echo number_format("$subtotal", 2, ",", ","); ?></td>
            <td align="center"><i class="fa fa-times" style="cursor:pointer" onclick="hapus('<?php echo $id_suratjalan; ?>','<?php echo $id_pembayaran; ?>')"></i></td>
            </td>
          </tr>
        <?php

        $tnilai += $nilai;
        $tsubtotal += $subtotal;
        $tbayar_sebelumnya += $bayar_sebelumnya;
        $sisa_bayar = $tnilai - $tbayar_sebelumnya;
      }
        ?>
        </tbody>
        <thead>
          <tr class="bg-header">
            <td colspan="4">
              <strong>TOTAL</strong>
            </td>
            <td align="right">
              <strong><?php echo number_format("$tnilai", 2, ",", ","); ?></strong>
            </td>
            <td align="right">
              <strong><?php echo number_format("$tbayar_sebelumnya", 2, ",", ","); ?></strong>
            </td>
            <td align="right">
              <strong><?php echo number_format("$sisa_bayar", 2, ",", ","); ?></strong>
            </td>
            <td align="right">
              <strong><?php echo number_format("$tsubtotal", 2, ",", ","); ?></strong>
            </td>
            <td>
              <strong>&nbsp;</strong>
            </td>
          </tr>
        </thead>
    </table>
  </div>
</form>
<div class="text-center"><input type="button" class="btn btn-success" value="Simpan" onclick="simpan()" style="height:30px;">&nbsp;&nbsp;&nbsp;&nbsp;<a href="fob_pembayaran_history.php"><input type="button" class="btn btn-info" value="Kembali" style="height:30px;"></a></div>
<p>&nbsp;</p>




<?php  //mysql_close(); 
?>
<?php include_once "footer.php"; ?>