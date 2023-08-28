<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

session_start();

@$username = $_SESSION["username"];
if (empty($username)) {
  die('You can\'t see this page');
}

include "config.php";


$sql = "SELECT
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