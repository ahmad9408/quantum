<?php 
include("koneksi_rian.php");
$proses 	= $_POST['proses'];
 
if($proses=="hapus_co"){
	$no_co 	= $_POST['no_co'];
	
	$sql 	= "delete from job_cutting where no_co='$no_co' and no_co!=''";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql);
	$sql 	= "delete from job_cutting_detail where no_co='$no_co' and no_co!=''";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql);
	
	
	$sql 	= "delete from job_gelaran where no_co='$no_co' and no_co!='' ";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql); 
	$sql 	= "delete from job_gelaran_detail where no_co='$no_co' and no_co!='' ";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql);
	$sql 	= "delete from job_gelaran_detail_rian where no_co='$no_co' and no_co!='' "; 
	$query	= mysql_query($sql)or die($sql);
	
	
	$sql 	= "select no_load from job_loading where no_co='$no_co' ";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql);
	while(list($no_load) 	= mysql_fetch_array($query)){
			$sql 	= "delete from job_loading_detail where no_load='$no_load'";
			//echo $sql."<br>";
			$res	= mysql_query($sql)or die($sql);
	} 
	$sql 	= "delete from job_loading where no_co='$no_co' and no_co!='' ";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql); 
	
	
	$sql 	= "delete from job_sewing where no_co='$no_co' and no_co!=''";
	//echo $sql."<br>";
	$query	= mysql_query($sql)or die($sql); 
	$sql 	= "delete from job_sewing_detail where no_co='$no_co' and no_co!=''";
	$query	= mysql_query($sql)or die($sql);
	
	  
	$sql 	= "select no_qc from job_qc where no_co='$no_co' "; 
	$query	= mysql_query($sql)or die($sql);
	while(list($no_qc) 	= mysql_fetch_array($query)){
			$sql 	= "delete from job_qc_detail where no_qc='$no_qc'"; 
			$res	= mysql_query($sql)or die($sql);
	}
	$sql 	= "delete from job_qc where no_co='$no_co' and no_co!=''";
	 
	$query	= mysql_query($sql)or die($sql); 
	
	echo "berhasil";die; 
	
}else{
	/*$no_po=$_POST['no_po'];
	$no_co=$_POST['no_co'];
	$bahan=$_POST['bahan'];
	$sql="update job_gelaran_detail_rian set bahan='$bahan' where no_po='$no_po' and no_co='$no_co'";
	$query=mysql_query($sql)or die($sql);
	echo $bahan; */
}

$no_co 	= $_GET['no_co'];
	
	
	
?>