<?php $content_title = "Pembatalan DO QC";
include_once "header.php" ?>
<?php

if (isset($_POST["batalkan"])) {

    $todayTime = date('Y-m-d H:i:s');
    $no_do = $_POST['no_do'];
    $sql = "UPDATE do_produk_qc SET no_do='btl.$no_do', STATUS='0' WHERE no_do='$no_do'";
    mysql_query($sql, $db);

    $no_do = $_POST['no_do'];
    $sql = "UPDATE do_produk_qc_detail SET no_do='btl.$no_do', STATUS='0' WHERE no_do='$no_do'";
    mysql_query($sql, $db);

    echo "
    <script>
        alert ('Berhasil !');
        window.location = 'rekap_do_qc.php';
    </script>";
} else {
    echo "
    <script>
        alert('Gagal!!!!!');
        window.location = 'rekap_do_qc.php';
    </script>";
}


?>
<?php include_once "footer.php" ?>