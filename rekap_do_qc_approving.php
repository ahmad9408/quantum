<?php $content_title = "Approving DO QC";
include_once "header.php";
include "pdo_produksi/Db.class.php" ?>

<?php
$no_do = sanitasi($_GET["no_do"]);
$sql = "SELECT approve2 FROM do_produk_qc WHERE no_do='$no_do'";
$hsl = mysql_query($sql, $db);
if (mysql_affected_rows($db) < 1) { ?> <script language="javascript">
        history.back();
    </script> <?php exit();
            }
            list($approve) = mysql_fetch_array($hsl);
            if (!$approve) {

                $db = new Db();
                $approveby = $_SESSION["username"];

                try {
                    $beginTransaction = $db->beginTransaction();

                    $db->query("UPDATE do_produk_qc SET approve2='1', approve2_by='$approveby',approve2_date=NOW() WHERE no_do='$no_do'");

                    $executeTransaction = $db->executeTransaction();
                } catch (PDOException $e) {
                    //atau (Exception $e) 
                    $rollBack = $db->rollBack();
                    echo "error msg: " . $e->getMessage();
                    throw $e;
                }
                ?>

    <script language="javascript">
        alert("Rekap DO QC telah di Approve.");
        window.location = "rekap_do_qc_detail.php?no_do=<?php echo $no_do; ?>";
    </script>
<?php
            } else {
?>
    <script language="javascript">
        alert("Rekap DO QC Gagal di Approve.");
        window.location = "rekap_do_qc_detail.php?no_do=<?php echo $no_do; ?>";
    </script>
<?php
            }
?>
<?php include_once "footer.php" ?>