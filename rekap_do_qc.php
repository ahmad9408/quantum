<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = ""; ?>
<?php $lihat = 1;
error_reporting(1);

if ($lihat == 1) {
  $content_title = "Rekap DO QC ke Finish";
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

if ($pabrik1 != "") {
  $pabrik2 = "AND id_pabrik = '$pabrik1'";
} else {
  $pabrik2 = "";
}

if ($status_approve1 ==0) {
  $status_approve2 = "";
} elseif($status_approve1 ==1) {
  $status_approve2 = " AND approve2 = 0 ";
} elseif($status_approve1 ==2){
  $status_approve2 = " AND approve2 = 1 ";
}



// if($username=='B120938_ahmad'){
// echo $sql."<br/>";	
// }
?>

<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
<script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="app_libs/rekap_do_qc.js?d=<?php echo date('YmdHis') ?>"></script>

<form method="post" action="rekap_do_qc.php?action=search" name="f1">

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
        <td>Pabrik</td>
        <td>:</td>
        <td>
          <select style="width:300px" name="pabrik" id="pabrik" class="form-control">
            <option value="">-- Pilih Wilayah Pabrik --</option>
            <?php
            $sql = "SELECT $sql_cache id,nama,mk FROM pabrik WHERE `status`=1 AND id $_pabrik";
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
      </tr>
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
    a.no_do as no_do,
    a.id_pabrik as id_pabrik,
    b.nama as nama,
    a.tanggal as tanggal,
    a.total_qty as total_qty,
    a.total_amount as total_amount,
    a.approve1 as approve1,
    a.approve1_by as approve1_by,
    a.approve1_date as approve1_date,
    a.approve2 as approve2,
    a.approve2_by as approve2_by,
    a.approve2_date as approve2_date,
    a.update_date as update_date,
    a.update_user as update_user,
    a.status as status
    FROM do_produk_qc as a
    LEFT JOIN pabrik AS b ON (a.id_pabrik = b.id)
    WHERE a.status ='1' AND a.id_pabrik $_pabrik 
    AND a.tanggal BETWEEN '$tgl1 00:00:00' and '$tgl2 23:59:59'
    AND a.no_do NOT LIKE '%btl%' $pabrik2 $status_approve2 ORDER BY a.tanggal DESC ";

if ($username == 'B120938_ahmad') {
  echo $sql . "<br/>";
}

$query = mysql_query($sql);
$jmlData[0] = mysql_num_rows($query);

?>

<table class="table table-bordered table-responsive sortable">
  <thead>
    <tr>
      <td colspan="9"></td>
      <td>
        <div align="right"><a href="rekap_do_qc_import.php"><button type="submit" class="btn btn-success btn-block">Import DO</button></a></div>
      </td>
    </tr>
    <tr style="background-color:#f39c7d; height: 14px">
      <td>No</td>
      <td>No DO </td>
      <td>ID pabrik</td>
      <td>Tanggal</td>
      <td>Total qty</td>
      <td>Total HPJ</td>
      <td>Approve 1</td>
      <td>Approve 2</td>
      <td>Tgl Approve 2</td>
      <td>Action</td>
    </tr>
  </thead>
  <?php
  $sql .= " limit " . ($page * $jmlHal) . "," . $jmlHal;
  $hsl = mysql_query($sql);
  $no = ($hal * $jmlHal);
  while ($rs = mysql_fetch_array($hsl)) {
    $no++;
    $no_do = $rs["no_do"];
    $id_pabrik = $rs["id_pabrik"];
    $pabrik_nama = $rs["nama"];
    $tanggal = $rs["tanggal"];
    $total_qty = $rs["total_qty"];
    $total_amount = $rs["total_amount"];
    $approve1 = $rs["approve1"];
    $approve1_by = $rs["approve1_by"];
    $approve1_date = $rs["approve1_date"];
    $approve2 = $rs["approve2"];
    $approve2_by = $rs["approve2_by"];
    $approve2_date = $rs["approve2_date"];
    $update_date = $rs["update_date"];
    $update_user = $rs["update_user"];
    $status = $rs["status"];

    $bgclr1 = "#FFFFCC";
    $bgclr2 = "#E0FF9F";
    $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;

    if ($approve1 != '1') {
      $apprv1 = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
    } else {
      $apprv1 = "<b>Approved [<font color='#0099FF'>$approve1_by</font>]</b>";
    }

    if ($approve2 != '1') {
      $apprv2 = "<blink><font color='red'><b>Belum di Approve</b></font></blink>";
    } else {
      $apprv2 = "<b>Approved [<font color='#0099FF'>$approve2_by</font>]</b>";
    }
  ?>
    <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
      <td><?php echo $no; ?></td>
      <td><?php echo $no_do; ?></td>
      <td><?php echo $pabrik_nama; ?></td>
      <td><?php echo $tanggal; ?></td>
      <td align="right"><?php echo number_format($total_qty, "0", ".", ",");
                        $totalqty = $totalqty + $total_qty; ?></td>
      <td align="right"><?php echo number_format($total_amount, "0", ".", ",");
                        $totalamount = $totalamount + $total_amount; ?></td>
      <td><?php echo $apprv1; ?></td>
      <td><?php echo $apprv2; ?></td>
      <td><?php echo $approve2_date; ?></td>
      <td><a href="rekap_do_qc_detail.php?no_do=<?php echo $no_do; ?>">Detail Approvement</a></td>
    </tr>
  <?php } ?>
  <tr style="background-color:#f39c7d; height: 14px">
    <td colspan="4" height="20"><strong>
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font>
      </strong></td>
    <td align="right"><strong><?php echo number_format($totalqty, "0", ".", ","); ?></strong></td>
    <td align="right"><strong>Rp <?php echo number_format($totalamount, "0", ".", ","); ?></strong></td>
    <td colspan="4"></td>

  </tr>
</table>


<table style="margin-left:10px; margin-top:10px;">
  <tr>
    <td class="text_standard">
      <?php
      $terusan = "&pabrik=$pabrik1&tgl1=$tgl1&tgl2=$tgl2&status_approve=$status_approve1";
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
</table>

<?php  //mysql_close(); 
?>
<?php include_once "footer.php"; ?>