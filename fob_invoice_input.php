<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

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
$tglinv = date('Y-m-d');
$duedate = date('Y-m-d');

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


<form method="post" action="fob_invoice_input.php?action=search" name="f1" id="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <tr>
        <td>Supplier</td>

        <td colspan="2">
          <select style="width:300px" name="supplier" id="supplier" class="form-control">
            <option value="">-- Pilih --</option>
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
      </tr>
      <tr>
        <td style="width:100px;">Tanggal Invoice</td>
        <td width="50">
          <input class="form-control" type="tglinv" name="tglinv" id="tglinv" value="<?php echo $tglinv; ?>" style="font-size: 8pt;width:100px;" size="10" required/>
        </td>
        <td>
          <script language="JavaScript">
            new tcal({
              // form name
              'formname': 'f1',
              // input name
              'controlname': 'tglinv'
            });
          </script>
        </td>
      </tr>
      <tr>
        <td style="width:100px;">Due Date</td>
        <td width="50">
          <input class="form-control" type="duedate" name="duedate" id="duedate" value="<?php echo $duedate; ?>" style="font-size: 8pt;width:100px;" size="10" required/>
        </td>
        <td>
          <script language="JavaScript">
            new tcal({
              // form name
              'formname': 'f1',
              // input name
              'controlname': 'duedate'
            });
          </script>
        </td>
      </tr>
      <tr>
        <td style="width:100px;">No Invoice</td>
        <td colspan="2">
          <input type="text" name="inv" id="inv" value="<?php echo $invoice; ?>" size="30" class="form-control" />
        </td>
      </tr>
      <tr>
        <td style="width:100px;">No Faktur Pajak</td>
        <td colspan="2">
          <input type="text" name="fkp" id="fkp" value="<?php echo $invoice; ?>" size="30" class="form-control" />
        </td>
      </tr>
      <tr>
        <td></td>
        <td colspan="2"><input type="button" class="btn btn-info" value="Simpan" onclick="simpan()" style="height:30px;"> <input type="button" class="btn btn-info" value="Kembali" style="height:30px;" onclick="window.location='fob_invoice.php'"></td>
      </tr>


    </table>
</form>
<!-- <div class="text-center"><a href="fob_invoice.php"><input type="button" class="btn btn-info" value="Kembali" style="height:30px;"></a></div> -->
<p>&nbsp;</p>




<?php  //mysql_close(); 
?>
<?php include_once "footer.php"; ?>