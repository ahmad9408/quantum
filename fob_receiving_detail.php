<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = "DETIL REKAP RECEIVING SURAT JALAN";
include_once "header.php"; ?>

<?php
include_once "clsaddrow.php";
include "config.php";
include("css_group.php");
include_once('DateControl.php');
$dc = new DateControl();

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

?>

<?php
$id_suratjalan = sanitasi($_GET["id_suratjalan"]);
$sql = "SELECT
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
            subtotal_harga_jual,
            co_mapping
          FROM fob_receiving
          WHERE id_suratjalan = '$id_suratjalan'";
$query = mysql_query($sql) or die($sql);
$no = 0;
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
    $subtotal_harga_jual,
    $co_mapping
) = mysql_fetch_array($query)) {
    $no++;
?>


    <!-- <div class="datagrid"> -->
    <!-- <link rel="stylesheet" href="themes/base/jquery.ui.all.css"> -->
    <script src="ui/jquery.ui.core.js"></script>
    <script src="ui/jquery.ui.widget.js"></script>
    <script src="ui/jquery.ui.datepicker.js"></script>
    <script type="text/javascript" src="app_libs/fob_receiving.js?d=<?php echo date('YmdHis') ?>"></script>
    <link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
    <script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
    <script>
        $(function() {
            $("#bulan").datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });
        });
    </script>


    <form method="POST" id="f1" action="rekap_do_qc_approving.php?no_do=<?php echo $no_do; ?>">
        <fieldset id="fieldsearch">
            <table class="table table-bordered">
                <tr>
                    <td width="150"><b>No. Surat Jalan</b></td>
                    <td width="2"><b>:</b></td>
                    <td><?php echo $id_suratjalan; ?></td>
                </tr>
                <tr>
                    <td><b>Supplier</b></td>
                    <td><b>:</b></td>

                    <td><?php
                        $sql = "SELECT nama FROM pabrik WHERE id = '$id_supplier'";
                        $query = mysql_query($sql) or die($sql);
                        while (list($nama_pabrik) = mysql_fetch_array($query)) {
                        ?>
                            <?php echo $nama_pabrik; ?>
                    </td>
                <?php
                        }
                ?>
                </tr>

                <tr>
                    <td><b>Tanggal Datang</b></td>
                    <td><b>:</b></td>
                    <td><?php echo $tgl_datang; ?></td>
                </tr>
                <tr>
                    <td><b>CO Mapping</b></td>
                    <td><b>:</b></td>
                    <!-- <td><?php echo $co_mapping; ?></td> -->
                    <td type="text" name="co_mapping" id="co_mapping" value="<?php echo $co_mapping; ?>" style="font-size: 8pt;width:300px;" size="10" readonly onDblClick="tampil_edit_co('<?php echo $co_mapping ?>','<?php echo $id_suratjalan ?>')"><?php echo $co_mapping; ?>
                </tr>
                <tr>
                    <td><b>Qty</b></td>
                    <td><b>:</b></td>
                    <td><?php echo $qty; ?></td>
                </tr>
                <?php
                if ($isShowHargaMakloon == 1) {
                    echo "<tr>";
                    echo "<td><b>Total PPN</b></td>";
                    echo "<td><b>:</b></td>";
                    echo "<td>" . number_format($ppn, "2", ".", ",") . "</td>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td><b>Subtotal Harga Makloon</b></td>";
                    echo "<td><b>:</b></td>";
                    echo "<td>" . number_format($total_harga, "2", ".", ",") . "</td>";
                    echo "</tr>";
                }
                ?>
                <tr>
                    <td><b>Subtotal Harga Jual</b></td>
                    <td><b>:</b></td>
                    <td><?php echo number_format($subtotal_harga_jual, "2", ".", ","); ?></td>
                </tr>
            <?php
        }
            ?>
            </table>


            <table class="table table-bordered table-responsive sortable">

                <tr class="bg-header" style="height: 14px">
                    <td>No</td>
                    <td>Barcode</td>
                    <td>Nama Produk</td>
                    <td>Qty</td>
                    <?php
                    if ($isShowHargaMakloon == 1) {
                        echo "<td>Harga Makloon</td>";
                        echo "<td>Total Harga Makloon</td>";
                        echo "<td>Total PPN</td>";
                        echo "<td>Subtotal Harga Makloon</td>";
                    }
                    ?>
                    <td>Harga Jual</td>
                    <td>Total Harga Jual</td>
                    <td>CO Mapping</td>
                </tr>

                <?php
                $id_suratjalan = sanitasi($_GET["id_suratjalan"]);
                $sql = "SELECT
                            id_receiving,
                            kode_produk,
                            nama_produk,
                            qty,
                            harga,
                            ppn,
                            subtotal,
                            harga_jual,
                            total_harga_jual,
                            co_mapping
                            FROM
                            fob_receiving_detail
                            WHERE id_receiving = '$id_suratjalan'";
                $query = mysql_query($sql) or die($sql);
                $no = 0;
                while (list(
                    $_id_receiving,
                    $_kode_produk,
                    $_nama_produk,
                    $_qty,
                    $_harga,
                    $_ppn,
                    $_subtotal,
                    $_harga_jual,
                    $_total_harga_jual,
                    $_co_mapping
                ) = mysql_fetch_array($query)) {
                    $no++;
                    $bgclr1 = "#ccfbff";
                    $bgclr2 = "#9fe1ff";
                    $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
                ?>
                    <tr id="child-content" onMouseOver="this.bgColor = '#9fd7ff'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
                        <td><?php echo $no; ?></td>
                        <td><?php echo $_kode_produk; ?></td>
                        <td><?php echo $_nama_produk; ?></td>
                        <td align="right"><?php echo number_format($_qty, "0", ".", ",");
                                            $qty_ = $qty_ + $_qty ?></td>
                        <?php
                        if ($isShowHargaMakloon == 1) {
                            echo "<td align='right'>" . number_format($_harga, 2, '.', ',') . "</td>";
                            $hrgjual = $_harga;
                            echo "<td align='right'>" . number_format($_qty * $_harga, 2, '.', ',') . "</td>";
                            $totharga = $totharga + $_qty * $_harga;
                            echo "<td align='right'>" . number_format($_ppn, 2, '.', ',') . "</td>";
                            $ppn_ = $ppn_ + $_ppn;
                            echo "<td align='right'>" . number_format($_subtotal, 2, '.', ',') . "</td>";
                            $subtotal_ = $subtotal_ + $_subtotal;
                        }
                        ?>
                        <td align="right"><?php echo number_format($_harga_jual, "0", ".", ",");
                                            $harga_jual_ = $_harga_jual_ + $_harga_jual ?></td>
                        <td align="right"><?php echo number_format($_total_harga_jual, "0", ".", ",");
                                            $total_harga_jual_ = $total_harga_jual_ + $_total_harga_jual ?></td>
                        <td><?php echo $_co_mapping; ?></td>
                    </tr>
                <?php
                }
                ?>
                <tr class="bg-header" style="height: 14px">
                    <td colspan="3" height="20"><strong>
                            <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font>
                        </strong></td>
                    <td align="center"><strong><?php echo number_format($qty_, "0", ".", ","); ?></strong></td>
                    <?php
                    if ($isShowHargaMakloon == 1) {
                        echo "<td align='center'>" . number_format($hrgjual, 2, '.', ',') . "</td>";
                        echo "<td align='center'>" . number_format($totharga, 2, '.', ',') . "</td>";
                        echo "<td align='center'>" . number_format($ppn_, 2, '.', ',') . "</td>";
                        echo "<td align='center'>" . number_format($subtotal_, 2, '.', ',') . "</td>";
                    }
                    ?>
                    <td align="center"><strong><?php echo number_format($harga_jual_, "0", ".", ","); ?></strong></td>
                    <td align="center"><strong><?php echo number_format($total_harga_jual_, "0", ".", ","); ?></strong></td>
                    <td></td>
                </tr>
            </table>

            <?php
            $id_suratjalan = sanitasi($_GET["id_suratjalan"]);
            $co_mapping = sanitasi($_GET["co_mapping"]);
            $sql = "SELECT
            id_suratjalan,
            approve_by,
            approve_date,
            approve2,
            approve2_by,
            approve2_date
          FROM fob_receiving
          WHERE id_suratjalan = '$id_suratjalan'";
            $query = mysql_query($sql) or die($sql);
            while (list(
                $nodo,
                $approve_by,
                $approve_date,
                $approve2,
                $approve2_by,
                $approve2_date
            ) = mysql_fetch_array($query)) {
            ?>

                <table width="100%">
                    <tr>
                        <td align="right">
                            <?php
                            echo "<b>Approved I By [<font color='#0099FF'>$approve_by</font>]</b>";
                            ?>
                            &nbsp;&nbsp;
                        </td>
                        <td align="left">
                            <?php
                            if ($approve2 != '1') {
                            ?>
                                <span class='btn btn-success' onclick="approve2('<?php echo $id_suratjalan ?>')">Approve II</span>

                            <?php
                            } else {
                                echo "<b>Approved II By [<font color='#0099FF'>$approve2_by</font>]</b>";
                            }
                            ?>
                            &nbsp;&nbsp;
                            <span class='btn btn-primary' onclick="window.location='fob_receiving.php?>';">Kembali</span>
                        </td>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <?php
                            if (($approve1 != '0') && ($approve2 != '1')) {
                            ?><span class='btn btn-danger' onclick='rekap_do_qc_batal()'>Batalkan DO</span>&nbsp;&nbsp;<span class='btn btn-danger' onclick='edit_co_mapping()'>Edit CO Mapping</span>
                            <?php
                            } else {
                                echo "<b><font color='#0099FF'>NO DO : $no_do Sudah Approve I & II</font></b>";
                            }
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" colspan="2">
                            <?php
                            if ($approve2 != '1') {
                            ?>
                                <span class='btn btn-warning' onclick="approve2_rj('<?php echo $id_suratjalan ?>')">Approve II Rumah Jahit</span>

                            <?php
                            }
                            ?>

                        </td>

                    </tr>
                    <tr>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                </table>

            <?php
            }
            ?>
    </form>

    <form id="alasan_batal" action="fob_receiving_input_data.php" style="display:none" method="POST">
        <table align="center" class="table table-bordered">
            <div id="alasan_batal" style="display:none">
                <tr>
                    <td align="center" width="100">NO RECEIVING / DO QC</td>
                    <td align="center" width="2">:</td>
                    <td align="center" width="150">
                        <input width="150" type="text" id="no_do" name="no_do" class="form-control" value="<?php echo $id_suratjalan; ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="center">
                        <button type="submit" name="batalkan" id="batalkan" class="btn btn-danger btn-block">Batalkan</button>
                    </td>
                </tr>
            </div>
        </table>
    </form>

    <form id="alasan_edit" action="fob_receiving_input_data.php" style="display:none" method="POST">
        <table align="center" class="table table-bordered">
            <div id="alasan_batal" style="display:none">
                <tr>
                    <td align="center" width="100">NO RECEIVING / DO QC</td>
                    <td align="center" width="2">:</td>
                    <td align="center" width="150">
                        <input width="150" type="text" id="no_do" name="no_do" class="form-control" value="<?php echo $id_suratjalan; ?>" readonly>
                    </td>
                </tr>
                <tr>
                    <td align="center" width="100">NO CO Mapping</td>
                    <td align="center" width="2">:</td>
                    <td align="center" width="150">
                        <input width="150" type="text" id="no_co_m" name="no_co_m" class="form-control" value="<?php echo $co_mapping; ?>">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td align="center">
                        <button type="submit" name="edit_co" id="edit_co" class="btn btn-danger btn-block">Edit CO</button>
                    </td>
                </tr>
            </div>
        </table>
    </form>
    </div>

    <script src="..js/jquery.js"></script>
    <script>

    </script>
    <?php include_once "footer.php"; ?>