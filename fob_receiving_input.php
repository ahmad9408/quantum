<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  $content_title = "Input Penerimaan Surat Jalan";
  include('header.php');
  
}
include "config.php";
include("css_group.php");
include_once('DateControl.php');
$dc = new DateControl();

$tahun_skrg = date('Y');
$bulan_skrg = date('m');
$tgldatang = date('Y-m-d');

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

  function hitung_total(kelas) {
    // alert(kelas);
    var nilai_total = 0;
    $('.' + kelas).each(function(key, element) {
      // alert(this.id);
      nilai_total += Number(removeFormat($('#' + this.id).val()));
    });
    // alert(nilai_total);
    // alert('#total_'+kelas);	
    $('#total_' + kelas).html("<b>" + format('#,##0.##', nilai_total) + "</b>");
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
<script type="text/javascript" src="app_libs/fob_receiving.js?d=<?php echo date('YmdHis') ?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css" />


<form method="post" action="fob_receiving.php?action=search" name="f1" id="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <tr>
        <td>Supplier</td>

        <td>
          <select style="width:300px" name="supplier" id="supplier" class="form-control" required>
            <option value="">-- Pilih --</option>
            <?php
            $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id_group=2 || id_group=3";
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
      </tr>
      <tr>
        <td style="width:100px;">Tanggal Datang</td>
        <td>

          <!-- calendar attaches to existing form element -->
          <input type="text" name="tgldatang" id="tgldatang" value="<?php echo $tgldatang; ?>" size="16" />
          &nbsp;
          <script language="JavaScript">
            new tcal({
              // form name
              'formname': 'f1',
              // input name
              'controlname': 'tgldatang'
            });
          </script>
        </td>
      </tr>
      <tr>
        <td style="width:100px;">No Surat Jalan</td>
        <td>
          <input type="text" name="sj" id="sj" value="<?php echo $invoice; ?>" size="30" placeholder="Harus di Isi" required />
        </td>
      </tr>
      <tr>
        <td style="width:100px;">Deskripsi</td>
        <td>
          <textarea name="ket" id="ket" cols="50" rows="2"></textarea>
        </td>
      </tr>
      <tr>
        <td style="width:100px;">NO CO Mapping</td>
        <td>
          <input type="text" name="hargasatuan" id="hargasatuan" value="<?php echo $invoice; ?>" size="30" required/>
        </td>
      </tr>
      <tr>
        <td></td>
        <td><a href="fob_receiving.php"><input type="button" class="btn btn-info" value="Kembali" style="height:30px;"></a></td>
      </tr>


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

        <td style="width: 200px;">Kode barang</td>
        <td style="width: 200px;">Nama</td>
        <td colspan="2" align="center">Size</td>
        <td colspan="2" align="center">Warna</td>
        <td style="width: 200px;">Unit Price</td>
        <td style="width: 50px;">Qty</td>
        <td style="width: 200px;">Subtotal</td>
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
          <input type="text" name="nama<?php echo $i; ?>" id="nama<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="size<?php echo $i; ?>" id="size<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="namasize<?php echo $i; ?>" id="namasize<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="warna<?php echo $i; ?>" id="warna<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="namawarna<?php echo $i; ?>" id="namawarna<?php echo $i; ?>" class="form-control" readonly />
        </td>
        <td>
          <input type="text" name="hargajual<?php echo $i; ?>" id="hargajual<?php echo $i; ?>" class="form-control" onkeyup="get_subtotal('<?php echo $i ?>')" readonly/>
        </td>
        <td>
          <input type="text" name="qty<?php echo $i; ?>" id="qty<?php echo $i; ?>" class="txt_qty form-control" onclick="hitung_total_qty('txt_qty')" onkeyup="get_subtotal('<?php echo $i ?>')" />
        </td>
        <td>
          <input type="text" name="subtotal<?php echo $i; ?>" id="subtotal<?php echo $i; ?>" class="txt_subtotal form-control" onclick="hitung_total('txt_subtotal')" readonly/>
        </td>
      </tr>
    </tbody>
    <thead>
      <tr class="bg-header">
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"></td>
        <td align="right"><span id="total_txt_qty"></span>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        <td align="right"><span id="total_txt_subtotal"></span>&nbsp;&nbsp;&nbsp;&nbsp;</td>
      </tr>
    </thead>


  </table>
</form>
<div class="text-center"><input type="button" class="btn btn-info" value="Simpan" onclick="simpan()" style="height:30px;"></div>
<p>&nbsp;</p>




<?php  //mysql_close(); 
?>
<?php include_once "footer.php"; ?>