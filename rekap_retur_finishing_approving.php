<?php $content_title = "Approving RETUR QC";
include_once "header.php";
include "pdo_produksi/Db.class.php" ?>

<?php
$no_retur = sanitasi($_GET["no_retur"]);
$sql = "SELECT approve2 FROM retur_finishing WHERE no_retur='$no_retur'";
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

                    $db->query("UPDATE retur_finishing SET approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_retur='$no_retur'");

                    $executeTransaction = $db->executeTransaction();
                } catch (PDOException $e) {
                    //atau (Exception $e) 
                    $rollBack = $db->rollBack();
                    echo "error msg: " . $e->getMessage();
                    throw $e;
                }
                ?>

    <script language="javascript">
        alert("Rekap Retur Finishing telah di Approve.");
        window.location = "rekap_retur_finishing.php?no_retur=<?php echo $no_retur; ?>";
    </script>
<?php
            } else {
?>
    <script language="javascript">
        alert("Rekap Retur Finishing Gagal di Approve.");
        window.location = "rekap_retur_finishing.php?no_retur=<?php echo $no_retur; ?>";
    </script>
<?php
            }
?>
<?php include_once "footer.php" ?>