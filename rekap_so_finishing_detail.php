<?php ob_start("ob_gzhandler"); ?>
<?php $content_title = "DETIL REKAP SO FINISHING";
include_once "header.php"; ?>

<?php
include_once "clsaddrow.php";
include "config.php";
include("css_group.php");
?>

<?php
$kode_so = sanitasi($_GET["kode_so"]);
$sql = "SELECT
            kode_so,
            pabrik,
            input_date,
            total_qty,
            total_hpj,
            update_by,
            upload_date
          FROM so_finishing
          WHERE kode_so = '$kode_so'";
$query = mysql_query($sql) or die($sql);
$no = 0;
while (list(
    $noso,
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


        <form method="POST" id="f1" action="">
            <fieldset id="fieldsearch">
                <table class="table table-bordered">
                    <tr>
                        <td width="150"><b>Kode SO</b></td>
                        <td width="2"><b>:</b></td>
                        <td><?php echo $noso; ?></td>
                    </tr>
                    <tr>
                        <td><b>Pabrik</b></td>
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
                    <tr>
                        <td><b>Total Qty</b></td>
                        <td><b>:</b></td>
                        <td><?php echo $totqty; ?></td>
                    </tr>
                    <tr>
                        <td><b>Total Harga</b></td>
                        <td><b>:</b></td>
                        <td><?php echo number_format($totamount, "0", ".", ","); ?></td>
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
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <?php
                    $kode_so = sanitasi($_GET["kode_so"]);
                    $sql = "SELECT
                    sod.kode_so
                    ,sod.barcode
                    , p.nama
                    , sod.stok
                    , sod.price
                FROM
                    so_finishing_detail AS sod
                    LEFT JOIN produk AS p ON (sod.barcode = p.kode)
                        WHERE  sod.kode_so = '$kode_so'";
                    $query = mysql_query($sql) or die($sql);
                    $no = 0;
                    while (list(
                        $_noso,
                        $barcode,
                        $nama_prod,
                        $_qty,
                        $harga_jual,
                    ) = mysql_fetch_array($query)) {
                        $no++;
                        $subtotal = $_qty * $harga_jual;
                        $bgclr1 = "#FFFFCC";
                        $bgclr2 = "#E0FF9F";
                        $bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;

                    ?>
                        <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
                            <td><?php echo $no; ?></td>
                            <td><?php echo $barcode; ?></td>
                            <td><?php echo $nama_prod; ?></td>
                            <td align="right"><?php echo number_format($harga_jual, "0", ".", ",");
                                                $hrgjual = $hrgjual + $harga_jual ?></td>
                            <td align="right"><?php echo number_format($_qty, "0", ".", ",");
                                                $qty_ = $qty_ + $_qty ?></td>
                            <td align="right"><?php echo number_format($subtotal, "0", ".", ",");
                                                $_subtotal = $_subtotal + $subtotal ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr style="background-color:#f39c7d; height: 14px">
                        <td colspan="4" height="20"><strong>
                                <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Total</font>
                            </strong></td>
                        <td align="center"><strong><?php echo number_format($qty_, "0", ".", ","); ?></strong></td>
                        <td align="center"><strong><?php echo number_format($_subtotal, "0", ".", ","); ?></strong></td>
                    </tr>
                </table>

                <p>&nbsp;</p>
                <?php
                $sql = "SELECT `complete`,update_by  FROM so_finishing WHERE kode_so = '$kode_so'";
                $query = mysql_query($sql) or die($sql);
                list($complete,$approve_by)=mysql_fetch_array($query);
                
                ?>

                    <table width="100%">
                        <tr>
                            
                            <td align="center">
                                <?php
                                if ($complete!='1') {
                                ?><input type="button" value="Approve SO ini" onclick="if(confirm('Approving Stok opname <?php echo $kode_so; ?>?')){window.location='rekap_so_finish_approving.php?kode_so=<?php echo $kode_so; ?>';}">
                                <?php
                                } else {
                                    echo "<b>Approved By [<font color='#0099FF'>$approve_by</font>]</b>";
                                }
                                ?>
                                &nbsp;&nbsp;
                                <input type="button" value="Kembali" onclick="window.location='rekap_so_finishing.php?>';">
                            </td>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                       <!--  <tr>
                            <td align="center" colspan="3">
                                <?php
                                if (($complete != '0') && ($approve2 != '1')) {
                                ?><span class='btn btn-danger' onclick='rekap_do_qc_batal()'>Batalkan SO</span>
                                <?php
                                } else {
                                    echo "<b><font color='#0099FF'>NO DO : $no_do Sudah Approve I & II</font></b>";
                                }
                                ?>
                            </td>
                        </tr> -->
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                    </table>

                <?php
             
                ?>

                <?php include_once "footer.php"; ?>