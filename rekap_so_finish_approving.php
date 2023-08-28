<?php $content_title = "Approving DO QC";
include_once "header.php";
include "pdo_produksi/Db.class.php" ?>

<?php
$kode_so = sanitasi($_GET["kode_so"]);
$sql = "SELECT `complete` FROM so_finishing WHERE kode_so='$kode_so'";
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

                    $db->query("UPDATE so_finishing SET `complete`='1' WHERE kode_so='$kode_so'");

                    $executeTransaction = $db->executeTransaction();
                } catch (PDOException $e) {
                    //atau (Exception $e) 
                    $rollBack = $db->rollBack();
                    echo "error msg: " . $e->getMessage();
                    throw $e;
                }
                ?>

    <script language="javascript">
        //alert("Stok Opname telah di Approve.");
        window.location = "rekap_so_finishing_detail.php?kode_so=<?php echo $kode_so; ?>";
    </script>
<?php
            } else {
?>
    <script language="javascript">
        alert("Stok Opname gagal di Approve.");
        window.location = "rekap_so_finishing_detail.php?kode_so=<?php echo $kode_so; ?>";
    </script>
<?php
            }
?>
<?php include_once "footer.php" ?>