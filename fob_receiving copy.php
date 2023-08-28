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
  $txt_limit = $_REQUEST['txt_limit'];

} elseif (isset($_REQUEST['search'])) {
  session_start();
  $jpb = $dc->jumlahHari(date('m'), date('Y'));
  $tgl1 = $_POST["tgl1"];
  $tgl2 = $_POST["tgl2"];
  if (!$tgl1) {
    $tgl1 = date("Y-01-01");
  }
  if (!$tgl2) {
    $tgl2 = date("Y-m-$jpb");
  }

  $pabrik1 = sanitasi($_POST['pabrik']);
  $status_approve1 = sanitasi($_POST['status_approve']);
  $_SESSION['txt_limit'] = trim($_POST['txt_limit']);

  $_SESSION['pabrik'] = $pabrik1;
  $_SESSION['status_approve'] = $status_approve1;
  $_SESSION['txt_limit']= $txt_limit;

} else {
  $jpb = $dc->jumlahHari(date('m'), date('Y'));
  $tgl1 = $_POST["tgl1"];
  $tgl2 = $_POST["tgl2"];
  if (!$tgl1) {
    $tgl1 = date("Y-01-01");
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

if(empty($txt_limit)){
  $txt_limit=100;	
}

if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
   
    
$jmlHal=$txt_limit;

$page=$hal;

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

<form method="post" action="<? echo $PHP_SELF;?>?action=search&rnd=<?php echo date('YmdHis');?>" name="f1">

  <fieldset id="fieldsearch">
    <table class="table table-bordered">
      <tr>
        <td style="width:100px;">Periode Dari</td>
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
        <td>
          <input class="form-control" type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1; ?>" style="font-size: 8pt;width:100px;" size="10" />
        </td>
      </tr>
      <tr>
        <td>Sampai</td>
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
        <td colspan="2">
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
        <td>Jumlah Data</td>
        <td colspan="2"><label for="textfield"></label>
          <input type="text" name="txt_limit" id="txt_limit" value="<?php echo $txt_limit; ?>" style="font-size: 8pt;width:50px;" class="form-control" />
        </td>
      </tr>
      <tr>
        <td></td>
        <td colspan='2'><button type="submit" name="submit" class="btn btn-info" value="Cari">Cari</button></td>
      </tr>
      <tr>
        <td></td>
        <td colspan="2"> <input type="checkbox" id="tampildetail">&nbsp;Tampil Detail</td>
      </tr>
    </table>
  </fieldset>
</form>

<?php

$sql = "SELECT SQL_CALC_FOUND_ROWS id_receiving,id_suratjalan,id_supplier,nama_supplier,keterangan,tgl_datang,qty,
        subtotal,ppn,total_harga,id_invoice,tgl_jatuhtempo,faktur_pajak,tgl_bayar,total_bayar,sisa_bayar,approve_date,approve_by,
        update_date,updateby,`status` FROM fob_receiving WHERE tgl_datang BETWEEN '$tgl1' AND '$tgl2' $pabrik2 " . " limit " . ($page*$jmlHal) . "," . $jmlHal . '; -- fob_receiving.php sql1';

if ($username == 'B120938_ahmad' || $username == 'iwan-it') { ?>
  <div><?php echo $sql . "<br/>"; ?></div>
<?php }

$query=mysql_query($sql)or die($sql);
$sql="SELECT FOUND_ROWS()";
$q2=mysql_query($sql);
 list($jmlData)=mysql_fetch_array($q2);
  $j=($page*$jmlHal);
?>
<div align="left"><a href="fob_receiving_input.php"><button class="btn btn-info">+ Receiving</button></a></div>
<br>

<table style="margin-left:10px; margin-top:10px;display:none;" >
<tr>
                <td class="text_standard">
                    Page : <?php $terusan='';//dirubah oleh budi(untuk keamanan menggunakan session)"&cari=$cari&tgl1=$tgl1&tgl2=$tgl2&cari2=$cari2&tujuan=$tujuan&tuj=$tujuan";?>
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=0<?php echo $terusan?>';">First</span>
                    <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
                        if($hal<=0){ ?>
                            <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php if($i>=4) break;
                        }else if(($hal+1)>=($jmlData/$jmlHal)){
                            if($i>=(($jmlData/$jmlHal)-5)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php } 
                        }else{
                            if($i<=($hal+2)and $i>=($hal-2)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php }
                        }
                    } ?>
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo intval(($jmlData/$jmlHal)); ?><?php echo $terusan?>';">Last</span>
                    &nbsp;&nbsp;
                    Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData; ?> Data
                </td>
            </tr>
 </table>
 <br />

<table border="1" class="table table-hover table-striped" style="width:1700px;">
  <thead>
    <tr class="bg-header">
      <td>No</td>
      <!--   <td>Id Receiving</td> -->
      <td style="width:220px;">Surat_jalan </td>
      <td>Tanggal</td>
      <td>supplier</td>
      <td>Qty</td>
      <td>Deskripsi</td>
      <td>Subtotal</td>
      <td>PPN</td>
      <td>Total</td>
      <td class="detail_baris">No.Invoice</td>
      <td class="detail_baris">Tgl Invoice</td>
      <td class="detail_baris">F_pajak</td>
      <td class="detail_baris">Tgl Bayar</td>
      <td class="detail_baris">Total Bayar</td>
      <td class="detail_baris">Sisa Bayar</td>
      <td>Act</td>
    </tr>
  </thead>
  <?php
  while (list(
    $id_receiving, $id_suratjalan, $id_supplier, $nama_supplier, $keterangan, $tgl_datang, $qty,
    $subtotal, $ppn, $total_harga, $id_invoice, $tgl_jatuhtempo, $faktur_pajak, $tgl_bayar, $total_bayar, $sisa_bayar, $approve_date, $approve_by,
    $update_date, $updateby, $status
  ) = mysql_fetch_array($query)) {

    $j++;
    $bgclr1 = "#FFFFCC";
    $bgclr2 = "#E0FF9F";
    $bgcolor = ($j % 2) ? $bgclr1 : $bgclr2;
    


  ?>
    <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
      <td><?php echo $j; ?></td>
      <!--  <td><?php echo $id_receiving; ?></td> -->
      <td><?php echo $id_suratjalan; ?></td>
      <td><?php echo $tgl_datang; ?></td>
      <td><?php echo $id_supplier . " " . $nama_supplier; ?></td>
      <td><?php echo $qty; ?></td>
      <td><?php echo $keterangan; ?></td>
      <td align="right"><?php echo number_format($subtotal, "2", ".", ",");
                        $totsubtotal = $totsubtotal + $subtotal; ?></td>
      <td align="right"><?php echo number_format($ppn, "2", ".", ",");
                        $totppn = $totppn + $ppn; ?></td>
      <td align="right"><?php echo number_format($total_harga, "2", ".", ",");
                        $totharga = $totharga + $total_harga; ?></td>
      <td class="detail_baris"><?php echo $id_invoice; ?></td>
      <td class="detail_baris"><?php echo $tgl_jatuhtempo; ?></td>
      <td class="detail_baris"><?php echo $faktur_pajak; ?></td>
      <td class="detail_baris"><?php echo $tgl_bayar; ?></td>
      <td align="right" class="detail_baris"><?php echo number_format($total_bayar, "2", ".", ","); ?></td>
      <td align="right" class="detail_baris"><?php echo number_format($sisa_bayar, "2", ".", ","); ?></td>
      <!--  <td><?php echo $approve_date; ?></td>
      <td><?php echo $approve_by; ?></td> -->
      <td><a href="fob_receiving_detail.php?id_suratjalan=<?php echo $id_suratjalan; ?>">Detail</a></td>

    </tr>
  <?php } ?>
  <tr class="bg-header">
    <td colspan="6">Jumlah</td>
    <td align="center"><strong><?php echo number_format($totsubtotal, "2", ".", ","); ?></strong></td>
    <td align="center"><strong><?php echo number_format($totppn, "2", ".", ","); ?></strong></td>
    <td align="center"><strong><?php echo number_format($totharga, "2", ".", ","); ?></strong></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
    <td class="detail_baris"></td>
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
      Page : <?php $terusan = ''; //"&cari=$cari&tgl1=$tgl1&tgl2=$tgl2&cari2=$cari2&tujuan=$tujuan&tuj=$tujuan";
              ?>
      <?php if ($jmlData > 20) { ?><span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=0<?php echo $terusan ?>';">First</span><?php } ?>
      <?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
        if ($hal <= 0) { ?>
          <span class="<?php if ($i == $hal) echo "hal_select";
                        else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
          <?php if ($i >= 4) break;
        } else if (($hal + 1) >= ($jmlData / $jmlHal)) {
          if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
          <?php }
        } else {
          if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
            <span class="<?php if ($i == $hal) echo "hal_select";
                          else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan ?>';"><?php echo ($i + 1); ?></span>
        <?php }
        }
      }
      if ($jmlData > 20) { ?>
        <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo intval(($jmlData / $jmlHal)); ?><?php echo $terusan ?>';">Last</span> <?php } ?>
      &nbsp;&nbsp;

      Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($no); ?> from <?php echo $jmlData; ?> Data
    </td>
  </tr>
</table>

<?php include_once "footer.php" ?>