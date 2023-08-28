<?php $content_title="APPROVING CO"; include_once "header.php" ?>
<?php include_once "job_sewing_init.php" ?>
<?php
	//include_once "po_manufaktur_approving_init.php";
	//echo nomor_manufaktur();exit;
	$no_sew=sanitasi($_GET["no_sew"]);
	$sql="SELECT approve FROM job_sewing WHERE no_sew='$no_sew'";
	$hsl=mysql_query($sql,$db);
	if(mysql_affected_rows($db)<1){ ?> <script language="javascript">history.back();</script> <?php exit();}
	list($approve)=mysql_fetch_array($hsl);
	if(!$approve){
		/*Buar Jurnal by suro*/
		#include_once('acc_jurnal_function.php');
		/*Buar Jurnal by suro*/
		$approveby=$_SESSION["username"];
		$sql="UPDATE job_sewing SET approve='1', approveby='$approveby',approvedate=NOW() WHERE no_sew='$no_sew'";
		mysql_query($sql,$db);

		if(mysql_affected_rows($db)>0){
            /* Edited By Goberan | 08 Oktober 2010 */
            /* Select Gudang Dan Produk */
            $sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
            $hsltemp=mysql_query($sql,$db);
            list($no_load)=mysql_fetch_array($hsltemp);
            $sql="SELECT pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
            $hsltemp=mysql_query($sql,$db);
            list($gudang)=mysql_fetch_array($hsltemp);
            $sql="SELECT kd_produk,qty FROM job_sewing_detail WHERE no_sew='$no_sew'";
            $hsltemp=mysql_query($sql,$db);
            while ( list($kd_produk,$qty)=mysql_fetch_array($hsltemp)) {
                /* -- Cari Harga -- */
                $sql="SELECT hargadasar, hargajual FROM produk WHERE kode='$kd_produk'";
                $hsltemp=mysql_query($sql,$db);
                list($hpp,$hpj)=mysql_fetch_array($hsltemp);
                
                /* Cek Stok Awal Pada | Job_Stok */
                $sql="SELECT stok FROM job_stok WHERE kode_produk='$kd_produk' AND mode_gudang='sewing' AND kode_gudang='$gudang'";
                $hsltemp=mysql_query($sql,$db);
                list($stokawal)=mysql_fetch_array($hsltemp);
                if ($stokawal==""){$stokawal=0;}else{$stokawal=$stokawal;}
                $stokakhir=$stokawal+$qty;
                $stokin=$qty;
                $sql1="INSERT INTO `job_stok_card`  VALUES ('$no_sew' , '$gudang','sewing', '$kd_produk', '$stokawal','$stokin', '0','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())";
                $hsltemp1=mysql_query($sql1,$db);
                if ($stokawal=="") {
                      $sql="INSERT INTO job_stok (kode_supplier,mode_gudang,kode_gudang,kode_produk,stok,updateby,updatedate) VALUES ";
                      $sql.="('RBN','sewing','$gudang','$kd_produk','$stokakhir','$approveby',NOW())";
                      $hsltemp=mysql_query($sql,$db);
                } else{
                     $sql="UPDATE job_stok SET stok='$stokakhir', updatedate=NOW(),updateby='$approveby' WHERE mode_gudang='sewing' AND kode_produk='$kd_produk' AND kode_gudang='$gudang'"; 
                     $hsltemp=mysql_query($sql,$db);
                }
            }
			?>
				<script language="javascript">
					alert("SEWING telah di approve.");
					window.location="job_sewing_detail.php?no_sew=<?php echo $no_sew; ?>";
				</script>
			<?php
		}else{
			?>
				<script language="javascript">
					alert("SEWING gagal di approve, Silakan hubungi Technical Support Anda!");
					window.location="job_sewing_detail.php?no_sew=<?php echo $no_sew; ?>";
				</script>
			<?php
		}
	}else{
		?>
			<script language="javascript">
				alert("SEWING sudah di approve.");
				window.location="job_sewing_detail.php?no_sew=<?php echo $no_sew; ?>";
			</script>
		<?php
	}
?>
<?php include_once "footer.php" ?>