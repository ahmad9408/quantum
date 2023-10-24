<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  $content_title = "Input Surat Jalan Ke Dalam Invoice";
  include('header.php');
}
include "config.php";
include("css_group.php");
include_once('DateControl.php');
$dc = new DateControl();

$tahun_skrg = date('Y');
$bulan_skrg = date('m');
$tglinv = date('Y-m-d');

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
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="app_libs/fob_invoice.js?d=<?php echo date('YmdHis') ?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css" />

<form method="post" action="fob_invoice_detail_input.php?action=search" name="f1" id="f1">
  <fieldset id="fieldsearch">

    <?php
    $no_inv = sanitasi($_GET["no_inv"]);
    $sql = "SELECT
    i.id_supplier
    ,p.nama
    ,i.id_invoice
    ,i.tanggal
    ,i.no_faktur_pajak
    ,i.tgl_harus_bayar
    FROM
    fob_invoice AS i
    LEFT JOIN pabrik AS p
    ON (i.id_supplier = p.id)
    WHERE i.id_invoice='$no_inv'
    GROUP BY i.id_invoice";
    $query = mysql_query($sql) or die($sql);
    while (list(
      $id_supplier,
      $supplier2,
      $invoice,
      $tglinv,
      $faktur,
      $duedate
    ) = mysql_fetch_array($query)) {
    ?>
      <table class="table table-bordered">
        <tr>
          <td>Id. Supplier</td>
          <td colspan="2">
            <input type="text" name="id_supplier" id="id_supplier" value="<?php echo $id_supplier; ?>" style="font-size: 8pt;width:300px;" size="10" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td>Nama Supplier</td>
          <td colspan="2">
            <input type="text" name="supplier2" id="supplier2" value="<?php echo $supplier2; ?>" style="font-size: 8pt;width:300px;" size="10" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td style="width:100px;">Tanggal Invoice</td>
          <td width="50">
            <input type="text" name="tglinv" id="tglinv" value="<?php echo $tglinv; ?>" style="font-size: 8pt;width:300px;" size="10" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td style="width:100px;">Due Date</td>
          <td width="50">
            <input type="text" name="duedate" id="duedate" value="<?php echo $duedate; ?>" style="font-size: 8pt;width:300px;" size="10" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td style="width:100px;">No Invoice</td>
          <td colspan="2">
            <input type="text" name="inv" id="inv" value="<?php echo $invoice; ?>" style="font-size: 8pt;width:300px;" size="10" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td style="width:100px;">No Faktur Pajak</td>
          <td colspan="2" type="text" name="fkp" id="fkp" value="<?php echo $faktur; ?>" style="font-size: 8pt;width:300px;" size="10" readonly onDblClick="tampil_edit('<?php echo $faktur ?>')">&nbsp;&nbsp;&nbsp;<?php echo $faktur; ?>
            <input type="hidden" name="fkp" id="fkp" value="<?php echo $faktur; ?>" style="font-size: 8pt;width:300px;" size="10" class="form-control" readonly />
          </td>
        </tr>
        <tr>
          <td></td>
          <td><a href="fob_invoice.php"><input type="button" class="btn btn-info" value="Kembali" style="height:30px;"></a></td>
        </tr>
      <?php
    }
      ?>



      </table>

  </fieldset>
  <div align="left">
    <img src='images/add.png' alt='add' align='middle' border='0' height='16' hspace='0' width='16' id="tambah_kode" onClick="tambah_baris()">
    <img src='images/remove.png' alt='inc' align='middle' border='0' height='16' hspace='0' width='16' id="hapus_kode" onClick="kurang_baris()">
  </div>
  <br>
  <table border="1" class="table table-hover table-striped">
    <thead>

      <tr class="bg-header">

        <td>Nomor Surat Jalan</td>
        <td style="width: 250px;">Deskripsi</td>
        <td>Harga Makloon</td>
        <td style="width: 70px;">Qty</td>
        <td>Jumlah</td>
        <td>Total PPN</td>
        <td>Total Harga</td>
        <td>Diskon %</td>
        <td>Total Diskon</td>
        <td>Total Harus Bayar</td>
        <td>Total Sudah Bayar</td>
        <!-- <td>Total Akan di Bayar</td> -->
        <td>Total Sisa Bayar</td>
      </tr>
    </thead>
    <tbody id="tbldetail0">
      <?php $i = -1; ?>
      <input type="hidden" id="counter" name="counter" value="<?php $i = $i + 1;
                                                              echo $i; ?>" />
      <tr id="bodydetail<?php echo $i; ?>">
        <td>
          <input type="text" name="kode<?php echo $i; ?>" id="kode<?php echo $i; ?>" class="form-control" onkeydown="get_data_barcode('<?php echo $i; ?>')" onclick="showlistSJ(this.id);" />
        </td>
        <td>
          <input type="text" name="deskripsi<?php echo $i; ?>" id="deskripsi<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="harga<?php echo $i; ?>" id="harga<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="qty<?php echo $i; ?>" id="qty<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="jumlah<?php echo $i; ?>" id="jumlah<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="ppn<?php echo $i; ?>" id="ppn<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="tot_harga<?php echo $i; ?>" id="tot_harga<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="diskon<?php echo $i; ?>" id="diskon<?php echo $i; ?>" class="form-control" onkeypress="get_diskon('<?php echo $i; ?>')" />
        </td>
        <td>
          <input type="text" name="diskon_nilai<?php echo $i; ?>" id="diskon_nilai<?php echo $i; ?>" class="form-control" onkeydown="get_diskon_nilai('<?php echo $i; ?>')" />
        </td>
        <td>
          <input type="text" name="tot_harusbayar<?php echo $i; ?>" id="tot_harusbayar<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="tot_sudahbayar<?php echo $i; ?>" id="tot_sudahbayar<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="tot_sisabayar<?php echo $i; ?>" id="tot_sisabayar<?php echo $i; ?>" class="form-control" readonly />
        </td>
      </tr>
    </tbody>


  </table>
</form>
<div class="text-center"><input type="button" class="btn btn-info" value="Simpan" onclick="simpan_detail()" style="height:30px;"></div>
<p>&nbsp;</p>




<?php  //mysql_close(); 
?>
<?php include_once "footer.php"; ?>