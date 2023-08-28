<?php include("koneksi_rian.php");
//var data="id_barang="+id_barang+"&no_co="+no_co+"&qty="+qty+"&no="+no+"&seqno="+seqno;
$proses	= $_POST['proses'];
 
if($proses=="ubah_rm_terpakai"){
	$nilai		= $_POST['nilai'];
	$no_co		= $_POST['no_co'];
	 $sql 	= "UPDATE job_cutting_rm_terpakai SET rm_terpakai='$nilai' WHERE no_co='$no_co'";
	 $res 	= mysql_query($sql)or die($sql);
	 echo "berhasil";die;
} else{
	$id_barang=$_POST['id_barang'];
	$no_co=$_POST['no_co'];
	$qty=$_POST['qty'];
	$seqno=$_POST['seqno'];
	
	// $sql="UPDATE job_cutting_ikatan SET qty='$qty' WHERE kd_produk='$id_barang' AND seqno='$seqno' AND no_co='$no_co' ";
	// $query=mysql_query($sql)or die($sql);
	// if($query){
		
		// $sql="update job_cutting_detail set qty=(select sum(qty) from job_cutting_ikatan where kd_produk='$id_barang' and no_co='$no_co') where no_co='$no_co' and kd_produk='$id_barang'";
		// $res=mysql_query($sql)or die($sql);

		$sql="update job_cutting_detail set qty='$qty' where no_co='$no_co' and kd_produk='$id_barang'";
		$res=mysql_query($sql)or die($sql);
		
		$sql="UPDATE job_cutting SET totalqty=(SELECT SUM(qty) FROM job_cutting_detail WHERE no_co='$no_co') WHERE no_co='$no_co'";
		$res=mysql_query($sql)or die($sql);
		echo "berhasil";
	// }
}  
?>