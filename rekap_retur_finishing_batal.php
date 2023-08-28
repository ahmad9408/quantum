<?php $content_title = "Pembatalan RETUR FINISHING";
include_once "header.php" ?>
<?php

if (isset($_POST["batalkan"])) {

    $todayTime = date('Y-m-d H:i:s');
    $no_retur = $_POST['no_retur'];
    $sql = "UPDATE retur_finishing SET no_retur='btl.$no_retur', iscomplete='0' WHERE no_retur='$no_retur'";
    mysql_query($sql, $db);

    $no_retur = $_POST['no_retur'];
    $sql = "UPDATE retur_finishing_detail SET no_retur='btl.$no_retur' WHERE no_retur='$no_retur'";
    mysql_query($sql, $db);

    echo "
    <script>
        alert ('Berhasil !');
        window.location = 'rekap_retur_finishing.php';
    </script>";
} else {
    echo "
    <script>
        alert('Gagal!!!!!');
        window.location = 'rekap_retur_finishing.php';
    </script>";
}


?>
<?php include_once "footer.php" ?>