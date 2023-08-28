 <?php session_start();
 include_once "header.php";
  include_once "job_qc_init.php";
$no_qc	= $_POST["no_qc12"]; 
$no_co	= $_POST["no_co12"]; 
$no_sew	= $_POST["no_sew12"]; 
$jm		 = $_POST['jm'];
$no_fin=no_fin();
 
 
$pecah_qc	= explode("/",$no_qc);
$counter	= substr($pecah_qc[3],4,2);
 
if(strlen($counter)==0){
	$counter=1;
}
$counter++;
$temp	="";

for($i=1;$i<=2-strlen($counter);$i++){
	$temp.="0";
}
$hit	= $temp.$counter;
 
$nex_qc	= $pecah_qc[0]."/".$pecah_qc[1]."/".$pecah_qc[2]."/".substr($pecah_qc[3],0,4)."".$hit;
 
 	$ts			= 0;
	$ta			= 0;
	$tnilai		= 0;
for($j=1;$j<=$jm;$j++){
 
	$id_b		= $_POST['id_b'.$j];
 
	$a			= $_POST['a'.$j];
	$b			= $_POST['b'.$j];
	$c			= $_POST['c'.$j];
	$s			= $_POST['s'.$j];
	 
	$harga		= $_POST['harga12'.$j];
	$keterangan	= $_POST['keterangan'.$j]; 
	

	
	//eksekusi for a 
	if(($a>0)||($b>0)){
	$seqno++;
		$sql="UPDATE job_qc_detail SET grade_a='$a',grade_b='$b',keterangan='$keterangan' WHERE no_qc='$no_qc' AND kd_produk='$id_b'";
		 
	$query		 = mysql_query($sql)or die($sql);
		
		//finishing
		 	$qty_all	= $a+$b;
			$nilai		= $qty_all*$harga; 
		   $sql="INSERT ignore job_fin_detail (no_fin,seqno,kd_produk,harga,qty,qty_a,qty_b,keterangan) VALUES ('$no_fin','$seqno','$id_b','$harga','$qty_all','$a','$b','$keterangan')";
		  
		 $query		 = mysql_query($sql)or die($sql); 
		  //penjumlahan ta 
		  $ta		+= $qty_all;
		  $tnilai	+= $nilai;
		  
	} //end eksekusu for a
	
	// service pembuatan qc baru
		if($s>0){
			$sql="INSERT INTO job_qc_detail(no_qc,kd_produk,harga,qty,grade_a,grade_b,no_co)values('$nex_qc','$id_b','$harga','$s','0','0','$no_co')"; 
			$query		 = mysql_query($sql)or die($sql);
			$ts			+= $s;
			
			$nilai		= $s*$harga;
			$tsnilai	+= $nilai; 
		}
		
		
	 
		
	
}
// end for


$approveby=$_SESSION["username"];
	// pengkondisian jika qc a lebih dari 1   (a+b)>1  -->$ta
	if($ta>0){
			//update qc 
			
			$sql="UPDATE job_qc SET approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_qc='$no_qc'";		 
			$query		 = mysql_query($sql)or die($sql);
			
			
			$sql="INSERT ignore job_fin (no_fin,no_qc,tanggal,totalqty,totalrp) VALUES ('$no_fin','$no_qc',NOW(),'$ta','$tnilai')";
		 
			$query		 = mysql_query($sql)or die($sql);
	}
	 
	if($ts>0){
		$sql="INSERT INTO job_qc (no_qc,no_sew,tanggal,totalqty,totalrp,approve,approveby,approvedate)values('$nex_qc','$no_sew',NOW(),'$ts','$tsnilai','1','$approveby',NOW())"; 
		
		$query		 = mysql_query($sql)or die($sql);
	}

 
?>
<script>
document.location="job_qc_detail_tester.php?no_qc=<?php echo $no_qc?>";
</script>
