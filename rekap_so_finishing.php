<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  $content_title = "Rekap Stok Opname Finishing";
  include('header.php');
}
include "config.php";
include("css_group.php");
include_once('DateControl.php');
$dc = new DateControl();

$tahun_skrg = date('Y');
$bulan_skrg = date('m');


if (isset($_GET['hal'])) {
  session_start();
  $jpb = $dc->jumlahHari(date('m'), date('Y'));
  $tgl1 = $_REQUEST["tgl1"];
  $tgl2 = $_REQUEST["tgl2"];
  if (!$tgl1) {
    $tgl1 = date("Y-m-01");
  }
  if (!$tgl2) {
    $tgl2 = date("Y-m-$jpb");
  }

  $status_approve1 = $_REQUEST['status_approve'];
  $pabrik1 = $_REQUEST['pabrik'];
  $jenis_pabrik = $_REQUEST['jenis_pabrik'];
} elseif (isset($_REQUEST['search'])) {
  session_start();
  $jpb = $dc->jumlahHari(date('m'), date('Y'));
  $tgl1 = $_POST["tgl1"];
  $tgl2 = $_POST["tgl2"];
  if (!$tgl1) {
    $tgl1 = date("Y-m-01");
  }
  if (!$tgl2) {
    $tgl2 = date("Y-m-$jpb");
  }

  $pabrik1 = sanitasi($_POST['pabrik']);
  $status_approve1 = sanitasi($_POST['status_approve']);

  $_SESSION['pabrik'] = $pabrik1;
  $_SESSION['jenis_pabrik'] = $jenis_pabrik;
  $_SESSION['status_approve'] = $status_approve1;
} else {
  $jpb = $dc->jumlahHari(date('m'), date('Y'));
  $tgl1 = $_POST["tgl1"];
  $tgl2 = $_POST["tgl2"];
  if (!$tgl1) {
    $tgl1 = date("Y-m-01");
  }
  if (!$tgl2) {
    $tgl2 = date("Y-m-$jpb");
  }

  $pabrik1 = $_REQUEST['pabrik'];
  $jenis_pabrik = $_REQUEST['jenis_pabrik'];
  $status_approve1 = $_REQUEST['status_approve'];
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

$sql_inner_group = ' INNER JOIN pabrik AS p ON (sf.pabrik = p.id) ';

if ($jenis_pabrik <> '') {
	$filter_pabrik = "AND p.id_group='$jenis_pabrik'";
}

if ($pabrik1 != "") {
  $pabrik2 = "AND sf.pabrik = '$pabrik1'";
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



// if($username=='B120938_ahmad'){
// echo $sql."<br/>";	
// }
?>

<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="app_libs/rekap_so_finishing.js?d=<?php echo date('YmdHis') ?>"></script>
<form method="post" action="rekap_so_finishing.php?action=search" name="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <tr>
        <td>Dari</td>
        <td>:</td>
        <td>
          <script language="JavaScript" src="calendar_us.js"></script>
          <link rel="stylesheet" href="calendar.css" />
          <!-- calendar attaches to existing form element -->
          <input type="text" name="tgl1" readonly id="tgl1" value="<?php echo $tgl1; ?>" size="16" />
          &nbsp;
          <script language="JavaScript">
            new tcal({
              // form name
              'formname': 'f1',
              // input name
              'controlname': 'tgl1'
            });
          </script>
          Sampai :
          <input type="text" name="tgl2" readonly id="tgl2" value="<?php echo $tgl2; ?>" size="16" />
          &nbsp;
          <script language="JavaScript">
            new tcal({
              // form name
              'formname': 'f1',
              // input name
              'controlname': 'tgl2'
            });
          </script>
        </td>
      </tr>
      <tr>
        <td>Jenis Pabrik</td>
        <td>:</td>
        <td>
          <select size="1" name="jenis_pabrik" class="form-control" style="font-size: 8pt;width:150px;" id="jenis_pabrik">
            <option value="">--Select--</option>
            <?php
            if ($jenis_pabrik == '4') {
              $sel1 = ' selected ';
            } elseif ($jenis_pabrik == '2') {
              $sel2 = 'selected';
            } elseif ($jenis_pabrik == '3') {
              $sel3 = 'selected';
            }
            echo "<option value='4' $sel1>SUHO</option>";
            echo "<option value='2' $sel2>FOB</option>";
            echo "<option value='3' $sel3>CMT</option>";
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Pabrik</td>
        <td>:</td>
        <td>
          <select style="width:250px" name="pabrik" id="pabrik">
            <option value="">-- Pilih Wilayah Pabrik --</option>
            <?php
            $sql = "SELECT $sql_cache id, nama from pabrik where status='1' AND id $_pabrik";
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
      <tr>
        <td><button type="submit" name="submit" class="btn btn-warning btn-block" value="Cari">Cari</button></td>
        <td></td>
        <td></td>
      </tr>
    </table>
  </fieldset>
</form>

<?php
if (isset($_GET['hal'])) $hal = $_GET['hal'];
else $hal = 0;
$jmlHal = 20;
$page = $hal;

$sql = "SELECT
    sf.kode_so
    , sf.tgl_stok_pagi
    , sf.pabrik
    , p.nama
    , sf.total_qty
    , sf.total_hpj
    , sf.input_date
    , sf.update_by
    , sf.upload_date
    , sf.complete,IFNULL(SUM(sfd.stok),0) AS qty_produk
    , sf.keterangan
    , sf.is_batal
    , p.id_group
FROM so_finishing AS sf 
    $sql_inner_group
    INNER JOIN so_finishing_detail AS sfd ON (sf.kode_so = sfd.kode_so) 
 WHERE sf.tgl_stok_pagi BETWEEN '$tgl1' AND '$tgl2' AND sf.pabrik $_pabrik $filter_pabrik AND sf.kode_so NOT LIKE 'BTL%' $pabrik2  
 and sf.is_batal=0 GROUP BY sf.kode_so";

//echo $sql;
$query = mysql_query($sql) or die($sql);

echo "<span style='display:none;'>";
if ($username == 'B120938_ahmad' || $username == 'iwan-it') {
  echo $sql . "<br/>";
}
echo "</span>";

// $query = mysql_query($sql);
// $jmlData[0] = mysql_num_rows($query);

?>
<div class="pull-right"><a href="rekap_stok_finishing_import.php"><button type="submit" class="btn btn-success btn-block">Import SO</button></a><br></div>

<table class="table table-bordered table-responsive sortable">
  <thead>

    <tr style="background-color:#f39c7d; height: 14px">
      <td>No</td>
      <td>Kode SO</td>
      <td>Tanggal</td>
      <td>Lokasi Pabrik</td>
      <td>Total qty</td>
      <td>Total qty produk</td>
      <td>Total HPJ</td>
      <td>Tanggal input</td>
      <td>Upload by</td>
      <td>Tgl Upload</td>
      <td>Status approve</td>
      <td>Keterangan</td>
      <td>Action</td>
    </tr>
  </thead>
  <?php
  // $sql .= " limit " . ($page * $jmlHal) . "," . $jmlHal;
  // $hsl = mysql_query($sql);
  // $no = ($hal * $jmlHal);
  $no = 0;
  while (list($kode_so, $tgl_stok_pagi, $pabrik, $namapabrik, $subtotal_qty, $subtotal_hpj, $input_date, $update_by, $upload_date, $complete, $qty_produk, $ket, $is_batal) = mysql_fetch_array($query)) {
    $no++;
    $bgclr1 = "#FFFFCC";
    $bgclr2 = "#E0FF9F";
    $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
  ?>
    <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
      <td><?php echo $no; ?></td>
      <td><a href="rekap_so_finishing_detail.php?kode_so=<?php echo $kode_so; ?>"><?php echo $kode_so; ?></a></td>
      <td><?php echo $tgl_stok_pagi; ?></td>
      <td><?php echo $pabrik . "-" . $namapabrik; ?></td>
      <td align="right"><?php echo $subtotal_qty; ?></td>
      <td align="right"><?php echo $qty_produk; ?></td>
      <td align="right"><?php echo number_format($subtotal_hpj); ?></td>
      <td><?php echo $input_date; ?></td>
      <td><?php echo $update_by; ?></td>
      <td><?php echo $upload_date; ?></td>
      <td><?php if ($complete == 1) {
            echo "<b>Aproved</b>";
          } else {
            echo "Belum approve";
          }; ?></td>
      <td><?php echo $ket; ?></td>
      <td><?php if ($complete == 0) { ?> <a href="#" onclick="batal_so('<?php echo $kode_so ?>')">Batalkan</a><?php } else {
                                                                                                        echo "-";
                                                                                                      } ?></td>
    </tr>
  <?php
    $total_qty += $subtotal_qty;
    $total_qty_produk += $qty_produk;
    $total_subtotal_hpj += $subtotal_hpj;
  } ?>
  <tr style="background-color:#f39c7d; height: 14px">
    <td colspan="4"></td>
    <td align="right"><?php echo number_format($total_qty) ?></td>
    <td align="right"><?php echo number_format($total_qty_produk) ?></td>
    <td align="right"><?php echo number_format($total_subtotal_hpj) ?></td>
    <td colspan="6"></td>
  </tr>

</table>


<!-- <table style="margin-left:10px; margin-top:10px;">
  <tr>
    <td class="text_standard">
      <?php
      $terusan = "&pabrik=$pabrik1&tgl1=$tgl1&tgl2=$tgl2&status_approve=$status_approve1&jenis_pabrik=$jenis_pabrik";
      if ($username == 'B120938_ahmad') {
        echo $terusan;
      }
      ?>
      Halaman :
      <span class="hal" onclick="location.href='rekap_do_qc.php?x_idmenu=229&hal=0><?php echo $terusan ?>';">First</span>
      <?php for ($i = 0; $i < ($jmlData[0] / $jmlHal); $i++) {
        if ($hal <= 0) { ?>
          <span class="<?php if ($i == $hal) echo "hal_select";
                        else echo "hal"; ?>" onclick="location.href='rekap_do_qc.php?x_idmenu=149&hal=<?php echo $i; ?>><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
          <?php if ($i >= 4) break;
        } else if (($hal + 1) >= ($jmlData[0] / $jmlHal)) {
          if ($i >= (($jmlData[0] / $jmlHal) - 5)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='rekap_do_qc.php?x_idmenu=149&hal=<?php echo $i; ?>><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
          <?php }
        } else {
          if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='rekap_do_qc.php?x_idmenu=149&hal=<?php echo $i; ?>><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
      <?php }
        }
      } ?>
      <span class="hal" onclick="location.href='rekap_do_qc.php?x_idmenu=229&hal=<?php echo intval(($jmlData[0] / $jmlHal)); ?>><?php echo $terusan ?>';">Last</span>
      &nbsp;&nbsp;
      Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?> from <?php echo $jmlData[0]; ?> Data
    </td>
  </tr>
</table> -->

<?php  //mysql_close(); 
?>
<?php include_once "footer.php"; ?>