<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = "DETIL REKAP DO QC";
include_once "header.php"; ?>

<?php
include_once "clsaddrow.php";
include "config.php";
include("css_group.php");
include_once('DateControl.php');
$dc = new DateControl();
?>

<?php
$no_do = sanitasi($_GET["no_do"]);
$sql = "SELECT
            no_do,
            id_pabrik,
            tanggal,
            total_qty,
            total_amount,
            update_user,
            update_date
          FROM do_produk_qc
          WHERE no_do = '$no_do'";
$query = mysql_query($sql) or die($sql);
$no = 0;
while (list(
    $nodo,
    $pabrik,
    $tgl,
    $totqty,
    $totamount,
    $uploadby,
    $uploaddate
) = mysql_fetch_array($query)) {
    $no++;
    $bgclr1 = "#FFFFCC";
    $bgclr2 = "#E0FF9F";
    $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
?>


    <div class="datagrid">
        <link rel="stylesheet" href="themes/base/jquery.ui.all.css">
        <script src="ui/jquery.ui.core.js"></script>
        <script src="ui/jquery.ui.widget.js"></script>
        <script src="ui/jquery.ui.datepicker.js"></script>
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
                        <td width="150"><b>NO DO</b></td>
                        <td width="2"><b>:</b></td>
                        <td><?php echo $nodo; ?></td>
                    </tr>
                    <tr>
                        <td><b>Pabrik Tujuan</b></td>
                        <td><b>:</b></td>

                        <td><?php
                            $sql = "SELECT nama FROM pabrik WHERE id = '$pabrik'";
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
                        <td><b>Tanggal</b></td>
                        <td><b>:</b></td>
                        <td><?php echo $uploaddate; ?></td>
                    </tr>
                    <tr>
                        <td><b>Upload By</b></td>
                        <td><b>:</b></td>
                        <td><?php echo $uploadby; ?></td>
                    </tr>
                <?php
            }
                ?>
                </table>


                <table class="table table-bordered table-responsive sortable">
                    <thead>
                        <tr style="background-color:#f39c7d; height: 14px">
                            <th>No</th>
                            <th>Barcode</th>
                            <th>Nama Produk</th>
                            <th>CO Mapping</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Disc</th>
                            <th>Subtotal</th>
                            <th>No Ikat</th>
                        </tr>
                    </thead>
                    <?php
                    $no_do = sanitasi($_GET["no_do"]);
                    $sql = "SELECT
                `doqc`.`no_do`,
                `doqc`.`barcode`,
                `prd`.`nama`,
                `doqc`.`harga_jual`,
                `doqc`.`qty`,
                `doqc`.`disc`,
                `doqc`.`subtotal`,
                `doqc`.`polybag`,
                `doqc`.`co_mapping`
                FROM
                `produk` AS `prd`
                LEFT JOIN `do_produk_qc_detail` AS `doqc`
                ON (`prd`.`kode` = `doqc`.`barcode`)
                WHERE  `doqc`.`no_do` = '$no_do'";
                    $query = mysql_query($sql) or die($sql);
                    $no = 0;
                    while (list(
                        $_nodo,
                        $barcode,
                        $nama_prod,
                        $harga_jual,
                        $_qty,
                        $_disk,
                        $subtotal,
                        $polybag,
                        $co_mapping
                    ) = mysql_fetch_array($query)) {
                        $no++;
                        $bgclr1 = "#FFFFCC";
                        $bgclr2 = "#E0FF9F";
                        $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
                    ?>
                        <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
                            <td><?php echo $no; ?></td>
                            <td><?php echo $barcode; ?></td>
                            <td><?php echo $nama_prod; ?></td>
                            <td align="center"><?php echo $co_mapping; ?></td>
                            <td align="right"><?php echo number_format($harga_jual, "0", ".", ",");
                                                $hrgjual = $hrgjual + $harga_jual ?></td>
                            <td align="right"><?php echo number_format($_qty, "0", ".", ",");
                                                $qty_ = $qty_ + $_qty ?></td>
                            <td align="right"><?php echo number_format($_disk, "0", ".", ",");
                                                $disk_ = $disk_ + $_disk ?></td>
                            <td align="right"><?php echo number_format($subtotal, "0", ".", ",");
                                                $_subtotal = $_subtotal + $subtotal ?></td>
                            <td align="right"><?php echo $polybag; ?></td>

                        </tr>
                    <?php
                    }
                    ?>
                    <tr style="background-color:#f39c7d; height: 14px">
                        <td colspan="4" height="20"><strong>
                                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font>
                            </strong></td>
                        <td align="center"><strong><?php echo number_format($harga_jual, "0", ".", ","); ?></strong></td>
                        <td align="center"><strong><?php echo number_format($qty_, "0", ".", ","); ?></strong></td>
                        <td align="center"><strong><?php echo number_format($disk_, "0", ".", ","); ?></strong></td>
                        <td align="center"><strong><?php echo number_format($_subtotal, "0", ".", ","); ?></strong></td>
                        <td></td>
                    </tr>
                </table>

                <?php
                $no_do = sanitasi($_GET["no_do"]);
                $sql = "SELECT
            no_do,
            approve1,
            approve1_by,
            approve1_date,
            approve2,
            approve2_by,
            approve2_date
          FROM do_produk_qc
          WHERE no_do = '$no_do'";
                $query = mysql_query($sql) or die($sql);
                while (list(
                    $nodo,
                    $approve1,
                    $approve1_by,
                    $approve1_date,
                    $approve2,
                    $approve2_by,
                    $approve2_date
                ) = mysql_fetch_array($query)) {
                ?>

                    <table width="100%">
                        <tr>
                            <td align="right">
                                <?php
                                if ($approve1 != '0') {
                                    echo "<b>Approved I By [<font color='#0099FF'>$approve1_by</font>]</b>";
                                } else {
                                    echo "<b>Belum Approved [<font color='#0099FF'>/font>]</b>";
                                }
                                ?>
                                &nbsp;&nbsp;
                            </td>
                            <td align="left">
                                <?php
                                if ($approve2 != '1') {
                                ?><input type="button" value="Approve II" onclick="if(confirm('Approving DO QC <?php echo $no_do; ?>?')){window.location='rekap_do_qc_approving.php?no_do=<?php echo $no_do; ?>';}">
                                <?php
                                } else {
                                    echo "<b>Approved II By [<font color='#0099FF'>$approve2_by</font>]</b>";
                                }
                                ?>
                                &nbsp;&nbsp;
                                <input type="button" value="Kembali" onclick="window.location='rekap_do_qc.php?>';">
                            </td>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" colspan="3">
                                <?php
                                if (($approve1 != '0') && ($approve2 != '1')) {
                                ?><span class='btn btn-danger' onclick='rekap_do_qc_batal()'>Batalkan DO</span>
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
                    </table>

                <?php
                }
                ?>
        </form>

        <form id="alasan_batal" action="rekap_do_qc_batal.php" style="display:none" method="POST">
            <table align="center" class="table table-bordered">
                <div id="alasan_batal" style="display:none">
                    <tr>
                        <td align="center" width="100">NO DO QC</td>
                        <td align="center" width="2">:</td>
                        <td align="center" width="150">
                            <input width="150" type="text" id="no_do" name="no_do" class="form-control" value="<?php echo $no_do; ?>" readonly>
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
    </div>

    <script src="..js/jquery.js"></script>
    <script>
        function rekap_do_qc_batal() {
            var x = document.getElementById("alasan_batal");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }
    </script>
    <?php include_once "footer.php"; ?>