<?php $content_title = "Import Stok Opname Gudang Barang Finish"; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  include('header.php');
}
// base on rekap_anggaran _realisasi_perdept.php 16112021
include_once('DateControl.php');
$dc = new DateControl();
include("css_group.php");
?>
<style>
  .sembunyi {
    display: none;
  }

  .button {
    display: none;
  }

  .inputan {
    background-color: #CAEDC9;
    height: 20px;
  }

  .link {
    cursor: pointer;
  }

  .sud {
    display: none;
  }

  .total {
    background-color: #f39c7d;
    text-align: right
  }

  .#total {
    background-color: #F93;
    text-align: right
  }

  .myLink {
    color: #00F;
    cursor: pointer;
  }

  .not_show {
    display: none;
  }
</style>
<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="sortable.js"></script>
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css">
<!-- <script type="text/javascript" src="app_libs/rekap_saldo_komisi_import.js?d=<?php echo date('YmdHis') ?>"></script> -->
<script type="text/javascript">
   $(document).ready(function(){
    activateAutoCompleteAll();
}); 

function activateAutoCompleteAll(){
  
  activateAutoComplete($('#pabrik'));   
  activateAutoComplete($('#id_group'));   
  activateAutoComplete($('#pabrik_tujuan'));   
  activateAutoComplete($('#id_group_tujuan'));   
  
}

function activateAutoComplete(component){
  component.chosen({});   
} 
</script>
<?php

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

$jpb = $dc->jumlahHari(date('m'), date('Y'));
$tgl1 = $_POST["t1"];
if (!$tgl1) {
  $tgl1 = date("Y-m-01");
}
$tgl2 = $_POST["t2"];
if (!$tgl2) {
  $tgl2 = date("Y-m-$jpb");
}
$array_bulan = array(
  '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei',
  '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
);

$tahun_skrg = date('Y');
$bulan_skrg = date('m');

if (isset($_REQUEST['action'])) {
  $tgl1 = $_POST['t1'];
  $tgl2 = $_POST['t2'];
  $affiliator = $_POST['affiliator'];
  $cari = $_POST['cari'];
  $tipe_select = $_POST['tipe_select'];
  $txtoutlet = $_POST['txtoutlet'];
  $wilayah_jajahan = $_REQUEST['txt_wilayah'];

  $_SESSION['tgl1'] = $tgl1;
  $_SESSION['tgl2'] = $tgl2;
  $_SESSION['affiliator'] = $affiliator;
  $_SESSION['cari'] = $cari;
  $_SESSION['txtoutlet'] = $txtoutlet;
  $_SESSION['wilayah_jajahan'] = $wilayah_jajahan;
}



?>

<p></p>
<form method="post" action="rekap_retur_finishing_import_proses.php" id="f1" name="f1" enctype="multipart/form-data">
  <input type="hidden" name="affiliator" id="affiliator">
  <table>
    <tr>
      <td>
        <table class="table table-bordered" style="border: solid 2px #ccc">
          <tr>
            <td>File Import</td>
            <td><input type="file" name="userfile" id="userfile" /></td>
          </tr>
          <tr class="periode_2">
            <td><b><i>Tanggal Retur </i></b></td>
            <!-- <td>:</td> -->
            <td><input type="text" name="tanggal_retur" id="tanggal_retur" value="<?php echo $tgl1 ?>" />
              <script language="JavaScript">
                new tcal({
                  // form name
                  'formname': 'f1',
                  // input name
                  'controlname': 'tanggal_retur'
                });
              </script>
            </td>
          </tr>
          <tr> 
            <td>Pabrik Asal</td>
            <td> <select name="pabrik" id="pabrik" class="form-control" required>
                <option value="">-- Pilih Pabrik Asal --</option>
                <?php
                $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id $_pabrik";
                $res = mysql_query($sql);
                while (list($id_pabrik, $nama_pabrik) = mysql_fetch_array($res)) {
                  if ($pabrik == $id_pabrik) {
                    $selected = 'selected';
                  } else {
                    $selected = '';
                  }
                  echo "<option value='$id_pabrik' $selected > [ $id_pabrik ] $nama_pabrik </option>";
                }
                ?>
              </select> </td>
          </tr>
          </tr>  
            <td>Group ID Asal</td>
            <td> <select name="id_group" id="id_group" class="form-control" required>
                <option value="">-- Pilih Group ID Asal --</option>
                <?php
                $sql = "SELECT $sql_cache id,nama FROM pabrik_group WHERE `status`=1";
                $res = mysql_query($sql);
                while (list($id_group, $nama_group) = mysql_fetch_array($res)) {
                  if ($id_group != "") {
                    $selected = '';
                  } else {
                    $selected = 'selected';
                  }
                  echo "<option value='$id_group' $selected > [ $id_group ] $nama_group </option>";
                }
                ?>
              </select> </td>
          </tr>
          <tr>
            <td>Keterangan</td>
            <td><textarea name="ket" id="ket" cols="40" rows="2"></textarea></td>
          </tr>
          <tr> 
            <td>Pabrik Tujuan</td>
            <td> <select name="pabrik_tujuan" id="pabrik_tujuan" class="form-control" required>
                <option value="">-- Pilih Pabrik Tujuan --</option>
                <?php
                $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id $_pabrik";
                $res = mysql_query($sql);
                while (list($id_pabrik, $nama_pabrik) = mysql_fetch_array($res)) {
                  if ($pabrik == $id_pabrik) {
                    $selected = 'selected';
                  } else {
                    $selected = '';
                  }
                  echo "<option value='$id_pabrik' $selected > [ $id_pabrik ] $nama_pabrik </option>";
                }
                ?>
              </select> </td>
          </tr>
          </tr>  
            <td>Group ID Tujuan</td>
            <td> <select name="id_group_tujuan" id="id_group_tujuan" class="form-control" required>
                <option value="">-- Pilih Group ID Tujuan --</option>
                <?php
                $sql = "SELECT $sql_cache id,nama FROM pabrik_group WHERE `status`=1";
                $res = mysql_query($sql);
                while (list($id_group, $nama_group) = mysql_fetch_array($res)) {
                  if ($id_group != "") {
                    $selected = '';
                  } else {
                    $selected = 'selected';
                  }
                  echo "<option value='$id_group' $selected > [ $id_group ] $nama_group </option>";
                }
                ?>
              </select> </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <!--  <td>&nbsp;</td> -->
            <td><button type="submit" value="Upload data" id="submit" class="btn btn-success btn-block">Upload Data</button></td>
          </tr>
        </table>
      </td>
      <td>&nbsp;</td>
      <td valign="top">

        <table class="table table-bordered">
          <tr>
            <td colspan="3"><b>Format file harus .xls, tanpa spasi & Format File import seperti berikut</b></td>
          </tr>
          <tr style="background-color: #74b85c;">
            <td>Itemcode</td>
            <td>Itemname</td>
            <td>Variantcode</td>
            <td>Unitprice</td>
            <td>Qty</td>
            <td>Disc</td>
            <td>Subtotal</td>
            <td>Polybag</td>
            <td>CO Mapping</td>
          </tr>
          <tr style="background-color: #fff;">
            <td>KAS6CB09241A</td>
            <td>KRD AN INNOVA Lx M</td>
            <td>100</td>
            <td>74,800</td>
            <td>100</td>
            <td>0</td>
            <td>5,984,000</td>
            <td>1</td>
            <td>A230123</td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</form>


<?php

include_once "footer.php";  ?>