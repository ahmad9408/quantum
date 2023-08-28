<?php
include("koneksi_rian.php");
include "pdo_produksi/Db.class.php";

$proses = $_POST['proses'];
if ($proses == "ubah_qty") {
	//var data="no_sew="+no_sew+"&bagus="+bagus+"&reject="+reject+"&proses=ubah_qty";
	$no_sew = $_POST['no_sew'];
	$bagus = $_POST['bagus'];
	$reject = $_POST['reject'];
	$id_barang = $_POST['id_barang'];

	$sql = new Db();

	try {
		$beginTransaction = $sql->beginTransaction();

		$sql->query("update job_sewing_detail set reject='$reject' where kd_produk='$id_barang' and no_sew='$no_sew'");
		$query = mysql_query($sql) or die($sql);
		echo "berhasil";

		$executeTransaction = $sql->executeTransaction();
	} catch (PDOException $e) {
		//atau (Exception $e) 
		$rollBack = $sql->rollBack();
		echo "error msg: " . $e->getMessage();
		throw $e;
	}
}
