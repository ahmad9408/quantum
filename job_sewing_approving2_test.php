<?php $content_title="APPROVING CO"; include_once "header.php" ?>
<?php include_once "job_sewing_init.php" ?>
<?php include_once "job_loading_init.php" ?>
<?php
	//include_once "po_manufaktur_approving_init.php";
	//echo nomor_manufaktur();exit;
    /* Select Gudang Dan Produk */
    $no_sew=sanitasi($_GET["no_sew"]);
    $sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
    $hsltemp=mysql_query($sql,$db);
    list($no_load)=mysql_fetch_array($hsltemp);
    $sql="SELECT pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
    $hsltemp=mysql_query($sql,$db);
    list($gudang)=mysql_fetch_array($hsltemp);
 
 	$sql="SELECT approve2 FROM job_sewing WHERE no_sew='$no_sew'";
	$hsl=mysql_query($sql,$db);
	if(mysql_affected_rows($db)<1){ ?> <script language="javascript">history.back();</script> <?php exit();}
	list($approve)=mysql_fetch_array($hsl);
	if(!$approve){
		$reject_pending_valid=true;
		foreach($_POST["reject"] as $kd_produk => $reject){
			$pending=sanitasi($_POST["pending"][$kd_produk]);
			$sql="SELECT qty FROM job_sewing_detail WHERE no_sew='$no_sew' AND kd_produk='$kd_produk'";
			$hsltemp=mysql_query($sql,$db);
			list($jumlah)=mysql_fetch_array($hsltemp);
			if($jumlah<$reject+$pending){$reject_pending_valid=false;break;}
		}
		foreach($_POST["rejectturunan"] as $kd_produk => $reject){
			$pending=sanitasi($_POST["pendingturunan"][$kd_produk]);
			$sql="SELECT qty FROM job_sewing_turunan WHERE no_sew='$no_sew' AND kd_produk='$kd_produk'";
			$hsltemp=mysql_query($sql,$db);
			list($jumlah)=mysql_fetch_array($hsltemp);
			if($jumlah<$reject+$pending){$reject_pending_valid=false;break;}
		}
		if($reject_pending_valid){
			/*Buar Jurnal by suro*/
			//include_once('acc_jurnal_function.php');
			/*Buar Jurnal by suro*/
			$approveby=$_SESSION["username"];

			$sql="UPDATE job_sewing SET approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_sew='$no_sew'";
			// echo "<br>".$sql;
			mysql_query($sql,$db);


			if(mysql_affected_rows($db)>0){
				
				$sql="SELECT kd_produk,harga,qty FROM job_sewing_detail WHERE no_sew='$no_sew'";
				$hsl=mysql_query($sql,$db);
				$arrproduk=array();
				$arrprodukharga=array();
				while(list($kd_produk,$harga,$qtyproduk)=mysql_fetch_array($hsl)){
					$arrproduk[$kd_produk]+=$qtyproduk;
					$arrprodukharga[$kd_produk]=$harga;
				}
				//TURUNAN
				$sql="SELECT kd_produk,harga,qty FROM job_sewing_turunan WHERE no_sew='$no_sew'";
				$hsl=mysql_query($sql,$db);
				$arrprodukturunan=array();
				$arrprodukhargaturunan=array();
				while(list($kd_produk,$harga,$qtyproduk)=mysql_fetch_array($hsl)){
					$arrprodukturunan[$kd_produk]+=$qtyproduk;
					$arrprodukhargaturunan[$kd_produk]=$harga;
				}
				$no_qc=no_qc();
				$totalqty=0;
				$totalrp=0;
				$seqno=-1;
				#Untuk Journal
				$ArrPrdNom='';
				$createjobsewing=false;
				foreach($arrproduk as $kd_produk => $qtyproduk){
					$seqno++;
					$sql="SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($harga)=mysql_fetch_array($hsltemp);
					$reject=sanitasi($_POST["reject"][$kd_produk]);
					$pending=sanitasi($_POST["pending"][$kd_produk]);
					$qtyprodukqc=$qtyproduk-($reject+$pending);
					$jumlah=$harga*$qtyprodukqc;
					$totalqty+=$qtyprodukqc;
					$totalrp+=$jumlah;
					$sql="UPDATE job_sewing_detail SET reject='$reject',pending='$pending' WHERE no_sew='$no_sew' AND kd_produk='$kd_produk'";
					// echo "<br>".$sql;
					mysql_query($sql,$db);
			
					if($qtyprodukqc>=0){
						$sql="INSERT INTO job_qc_detail (no_qc,seqno,kd_produk,harga,qty,grade_a,grade_b,keterangan) VALUES ('$no_qc','$seqno','$kd_produk','$harga','$qtyprodukqc','0','0','')";
						//  echo "<br>".$sql; 
						mysql_query($sql,$db);	 
					}
					 
					if($pending>0){//ada pending, persiapkan untuk create job sewing
						$createjobsewing=true;
						$arrsewing[$kd_produk]=$pending;
					}
					/*buat journal sewing disini*/
                    /* Edited By Goberan | 08 Oktober 2010 */
                    /* -- Cari Harga -- */
                    $sql="SELECT hargadasar, hargajual FROM produk WHERE kode='$kd_produk'";
                    $hsltemp=mysql_query($sql,$db);
                    list($hpp,$hpj)=mysql_fetch_array($hsltemp);
                
                    /* Cek Stok Awal Pada | Job_Stok */
                    $sql="SELECT stok FROM job_stok WHERE kode_produk='$kd_produk' AND mode_gudang='sewing' AND kode_gudang='$gudang'";
                    $hsltemp=mysql_query($sql,$db);
                    list($stokawal)=mysql_fetch_array($hsltemp);
                    if ($stokawal==""){$stokawal=0;}else{$stokawal=$stokawal;}
                    // $qtyproduk=;
                    $stokout=$qtyprodukqc+$reject+$pending;
                    $stokout_a=$qtyprodukqc+$reject;
                    $stokakhir=$stokawal-$stokout;


                    $sql1="INSERT INTO `job_stok_card`  VALUES ('$no_sew' , '$gudang','sewing', '$kd_produk', '$stokawal','0', '$stokout_a','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())";
                    $hsltemp1=mysql_query($sql1,$db);


                    if ($stokawal=="") {
                      $sql="INSERT INTO job_stok (kode_supplier,mode_gudang,kode_gudang,kode_produk,stok,updateby,updatedate) VALUES ";
                      $sql.="('RBN','sewing','$gudang','$kd_produk','$stokakhir','$approveby',NOW())";
                      $hsltemp=mysql_query($sql,$db);
                    } else{
                     $sql="UPDATE job_stok SET stok='$stokakhir', updatedate=NOW(),updateby='$approveby' WHERE mode_gudang='sewing' AND kode_produk='$kd_produk' AND kode_gudang='$gudang'"; 
                     $hsltemp=mysql_query($sql,$db);
                     }


                     /* Cek Barang Reject */
                     $sql8="INSERT INTO `job_stok_reject_card`  VALUES ('$no_sew' , '$gudang','sewing', '$kd_produk', '$stokawal','$reject', '0','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())";
                     $hsltemp8=mysql_query($sql8,$db);

                    
                     /* End Goberan */
                    $ArrPrdNom[$kd_produk]=$jumlah;	
				}

				$sql="INSERT INTO job_qc (no_qc,no_sew,tanggal,totalqty,totalrp,approve,approveby,approvedate) VALUES ('$no_qc','$no_sew',NOW(),'$totalqty','$totalrp','1','$username',NOW())";
				// echo "<br>".$sql;
				mysql_query($sql,$db);				
				/*buat journal sewing disini*/
				//jurnal_sewing($no_sew,$ArrPrdNom);

				
				//TURUNAN				
				$totalqty=0;
				$totalrp=0;
				$seqno=-1;
				$createjobsewingturunan=false;
				foreach($arrprodukturunan as $kd_produk => $qtyproduk){
					$seqno++;
					$sql="SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($harga)=mysql_fetch_array($hsltemp);
					$reject=sanitasi($_POST["rejectturunan"][$kd_produk]);
					$pending=sanitasi($_POST["pendingturunan"][$kd_produk]);
					$qtyprodukqc=$qtyproduk-($reject+$pending);
					$jumlah=$harga*$qtyprodukqc;
					$totalqty+=$qtyprodukqc;
					$totalrp+=$jumlah;
					$sql="UPDATE job_sewing_turunan SET reject='$reject',pending='$pending' WHERE no_sew='$no_sew' AND kd_produk='$kd_produk'";
					// echo "<br>".$sql;
					mysql_query($sql,$db);


					if($qtyprodukqc>0){
						$sql="INSERT INTO job_qc_turunan (no_qc,seqno,kd_produk,harga,qty,grade_a,grade_b,keterangan) VALUES ('$no_qc','$seqno','$kd_produk','$harga','$qtyprodukqc','0','0','')";
						// echo "<br>".$sql;
						mysql_query($sql,$db);


					}
					if($pending>0){//ada pending, persiapkan untuk create job sewing
						$createjobsewingturunan=true;
						$arrsewingturunan[$kd_produk]=$pending;
					}
				}
				//buat job sewing
				if($createjobsewing || $createjobsewingturunan){
					//$no_sew_pending=no_sew();
					$no_sew_pending=no_sew_pending($no_sew);
					if($createjobsewing){
						$totalqty=0;
						$totalrp=0;
						$seqno=-1;
						foreach($arrsewing as $kd_produk => $qty){
							$seqno++;
							$sql="SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
							$hsltemp=mysql_query($sql,$db);
							list($harga)=mysql_fetch_array($hsltemp);
							$jumlah=$harga*$qty;
							$totalqty+=$qty;
							$totalrp+=$jumlah;
							$sql="INSERT INTO job_sewing_detail (no_sew,seqno,kd_produk,harga,qty,reject,pending) VALUES ('$no_sew_pending','$seqno','$kd_produk','$harga','$qty','0','0')";
							// echo "<br>".$sql;
							mysql_query($sql,$db);

						}
					}					
					$sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
					$hsltemp=mysql_query($sql,$db);
					list($no_load)=mysql_fetch_array($hsltemp);


					$sql="INSERT INTO job_sewing (no_sew,no_load,tanggal,totalqty,totalrp) VALUES ('$no_sew_pending','$no_load',NOW(),'$totalqty','$totalrp')";
					mysql_query($sql,$db);
					// echo "<br>".$sql;

					
					//TURUNAN
					if($createjobsewingturunan){
						$totalqty=0;
						$totalrp=0;
						$seqno=-1;
						foreach($arrsewingturunan as $kd_produk => $qty){
							$seqno++;
							$sql="SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
							$hsltemp=mysql_query($sql,$db);
							list($harga)=mysql_fetch_array($hsltemp);
							$jumlah=$harga*$qty;
							$totalqty+=$qty;
							$totalrp+=$jumlah;
							$sql="INSERT INTO job_sewing_turunan (no_sew,seqno,kd_produk,harga,qty,reject,pending) VALUES ('$no_sew_pending','$seqno','$kd_produk','$harga','$qty','0','0')";
							// echo "<br>".$sql;
							mysql_query($sql,$db);

						}
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
					alert("Reject + Pending tidak boleh lebih dari jumlah produk!.");
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