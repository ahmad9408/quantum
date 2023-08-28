<?php $content_title="APPROVING QUALITY CONTROL"; include_once "header.php" ?>
<?php include_once "job_qc_init.php" ?>
<?php
	//include_once "po_manufaktur_approving_init.php";
	//echo nomor_manufaktur();exit;
	$no_qc=sanitasi($_GET["no_qc"]);
	 $pecah_no_qc=explode("/",$no_qc);
	 if(strlen($pecah_no_qc[0])==6){
		 	$no_qc1=substr($no_qc,0,22);
		 }else{
			$no_qc1=substr($no_qc,0,20);
		 }
		 
		 
	
	
	 
	$sql="SELECT COUNT(no_qc) FROM job_qc WHERE no_qc like '%$no_qc1%' ";
	$resi=mysql_query($sql)or die($sql);
	list($counter)=mysql_fetch_array($resi); $counter++;
	if(strlen($counter)==1){
		$counter="0".$counter;
	}
	$no_qc_tr=$no_qc1.$counter;	
	 
	$oke=false;
	$sql="SELECT no_sew FROM job_qc WHERE no_qc='$no_qc'";
    $hsltemp=mysql_query($sql,$db);
    list($no_sew)=mysql_fetch_array($hsltemp);
    $sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
    $hsltemp=mysql_query($sql,$db);
    list($no_load)=mysql_fetch_array($hsltemp);
    $sql="SELECT pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
    $hsltemp=mysql_query($sql,$db);
    list($gudang)=mysql_fetch_array($hsltemp);
            
	$sql="SELECT approve2 FROM job_qc WHERE no_qc='$no_qc'";
	$hsl=mysql_query($sql,$db);	
	if(mysql_affected_rows($db)<1){ ?> <script language="javascript">history.back();</script> <?php exit();}
	list($approve)=mysql_fetch_array($hsl);
	if(!$approve){
		/* Buat Jurnal by suro*/
        //include_once('acc_jurnal_function.php');
		//cek hasil jumlah grade a dan b
		$sql="SELECT kd_produk,harga,qty FROM job_qc_detail WHERE no_qc='$no_qc'";
		$hsl=mysql_query($sql,$db);
		$arrproduk=array();
		$arrprodukharga=array();
		while(list($kd_produk,$harga,$qtyproduk)=mysql_fetch_array($hsl)){
			$arrproduk[$kd_produk]+=$qtyproduk;
			$arrprodukharga[$kd_produk]=$harga;
		}
		$jumlahvalid=true;
		foreach($arrproduk as $kd_produk => $qtyproduk){
			$grade_a=sanitasi($_POST["grade_a"][$kd_produk]);
			$grade_b=sanitasi($_POST["grade_b"][$kd_produk]);
			$service=sanitasi($_POST["service"][$kd_produk]);
	
			if($grade_a+$grade_b+$service!=$qtyproduk){
			 
				$jumlahvalid=false;
			}
		}
	
		// cek hasil jumlah grade a dan b TURUNAN
		$sql="SELECT kd_produk,harga,qty FROM job_qc_turunan WHERE no_qc='$no_qc'";
		$hsl=mysql_query($sql,$db);
		$arrprodukturunan=array();
		$arrprodukhargaturunan=array();
		while(list($kd_produk,$harga,$qtyproduk)=mysql_fetch_array($hsl)){
			$arrprodukturunan[$kd_produk]+=$qtyproduk;
			$arrprodukhargaturunan[$kd_produk]=$harga;
		}
		if($jumlahvalid){//kalo udah ga valid yang turunan ga usah di cek lagi
			foreach($arrprodukturunan as $kd_produk => $qtyproduk){
				$grade_a=sanitasi($_POST["grade_aturunan"][$kd_produk]);
				$grade_b=sanitasi($_POST["grade_bturunan"][$kd_produk]);
				$service=sanitasi($_POST["service_turunan"][$kd_produk]);
				if($grade_a+$grade_b+$service!=$qtyproduk){
					$jumlahvalid=false;
				}
			}
		}
		
		if($jumlahvalid){
		
			$approveby=$_SESSION["username"];
			$sql="UPDATE job_qc SET approve2='1', approveby2='$approveby',approvedate2=NOW() WHERE no_qc='$no_qc'";
			
			mysql_query($sql,$db);
			if(mysql_affected_rows($db)>0){
					
				$sql="SELECT kd_produk,harga,qty FROM job_qc_detail WHERE no_qc='$no_qc'";
				$hsl=mysql_query($sql,$db);
				$arrproduk=array();
				$arrprodukharga=array();
				while(list($kd_produk,$harga,$qtyproduk)=mysql_fetch_array($hsl)){
					$arrproduk[$kd_produk]+=$qtyproduk;
					$arrprodukharga[$kd_produk]=$harga;
				}	
				// TURUNAN
				$sql="SELECT kd_produk,harga,qty FROM job_qc_turunan WHERE no_qc='$no_qc'";
				$hsl=mysql_query($sql,$db);
				$arrprodukturunan=array();
				$arrprodukhargaturunan=array();
				while(list($kd_produk,$harga,$qtyproduk)=mysql_fetch_array($hsl)){
					$arrprodukturunan[$kd_produk]+=$qtyproduk;
					$arrprodukhargaturunan[$kd_produk]=$harga;
				}
				
				$no_fin=no_fin();
				$totalqty=0;
				$totalrp=0;
				$seqno=-1;
				# Untuk Journal
				$ArrPrdNom='';
				foreach($arrproduk as $kd_produk => $qtyproduk){
					$seqno++;
					$sql="SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($harga)=mysql_fetch_array($hsltemp);
					$jumlah=$harga*$qtyproduk;
					$totalqty+=$qtyproduk;
					$totalrp+=$jumlah;
					$grade_a=sanitasi($_POST["grade_a"][$kd_produk]);
					$grade_b=sanitasi($_POST["grade_b"][$kd_produk]);
					$service=sanitasi($_POST["service"][$kd_produk]);
					$keterangan=sanitasi($_POST["keterangan"][$kd_produk]);
					$sql="UPDATE job_qc_detail SET grade_a='$grade_a',grade_b='$grade_b',keterangan='$keterangan' WHERE no_qc='$no_qc' AND kd_produk='$kd_produk'";
					// echo "<br>".$sql;
				mysql_query($sql,$db);
		
				if($service>0){
					$sql="INSERT INTO job_qc_detail(no_qc,kd_produk,harga,qty,no_co)SELECT '$no_qc_tr',kd_produk,harga,'$service',no_co FROM  job_qc_detail WHERE no_qc='$no_qc' AND kd_produk='$kd_produk'";
					$resi=mysql_query($sql)or die($sql);
					$tqtyku+=$service;
					$tqtykuuang+=($service*$harga);
					$oke=true;
				}
				
				
                    
            /* Edited By Goberan | For Grade A | 08 Oktober 2010 */
            /* -- Cari Harga -- */
            $qty=$grade_a+$grade_b; // <--- Grade A + Grade B aja yg Di update
            $sql="SELECT hargadasar, hargajual FROM produk WHERE kode='$kd_produk'";
            $hsltemp=mysql_query($sql,$db);
            list($hpp,$hpj)=mysql_fetch_array($hsltemp);
            /* Cek Stok Awal Pada | Job_Stok */
            $sql="SELECT stok FROM job_stok WHERE kode_produk='$kd_produk' AND mode_gudang='qc' AND kode_gudang='$gudang'";
            $hsltemp=mysql_query($sql,$db);
            list($stokawal)=mysql_fetch_array($hsltemp);
            if ($stokawal==""){$stokawal=0;}else{$stokawal=$stokawal;}
            $stokakhir=$stokawal-($qty+$grade_b);
            $stokout=$qty;
            $sql1="INSERT INTO `job_stok_card`  VALUES ('$no_qc' , '$gudang','qc', '$kd_produk', '$stokawal','0', '$stokout','0','0', '$stokakhir','$disc', '$hpp','$hpj', '', '$approveby', NOW())";
            $hsltemp1=mysql_query($sql1,$db);
            if ($stokawal=="") {
            $sql="INSERT INTO job_stok (kode_supplier,mode_gudang,kode_gudang,kode_produk,stok,updateby,updatedate) VALUES ";
            $sql.="('RBN','qc','$gudang','$kd_produk','$stokakhir','$approveby',NOW())";
           echo "<br>".$sql;
		    $hsltemp=mysql_query($sql,$db);
            } else{
            $sql="UPDATE job_stok SET stok='$stokakhir', updatedate=NOW(),updateby='$approveby' WHERE mode_gudang='qc' AND kode_produk='$kd_produk' AND kode_gudang='$gudang'";
			echo "<br>".$sql; 
           $hsltemp=mysql_query($sql,$db);
            }
            /* End Goberan */
					
                    $sql="INSERT INTO job_fin_detail (no_fin,seqno,kd_produk,harga,qty,qty_a,qty_b,keterangan) VALUES ('$no_fin','$seqno','$kd_produk','$harga','$qtyproduk','$qty','$grade_b','')";
					 echo "<br>".$sql;
					mysql_query($sql,$db);
					
					//tambah produk grade b
					if($grade_b>0){
						//echo "OKE";exit;
						$kd_produk_b=substr($kd_produk,0,11).substr($kd_produk,0,1).substr($kd_produk,12,3);
						echo $sql="SELECT kode FROM produk WHERE kode='$kd_produk_b'";
						$hsltemp=mysql_query($sql,$db);
						if(mysql_affected_rows($db)<=0){
							$sql="SELECT * FROM produk WHERE kode='$kd_produk'";
							$hsltemp=mysql_query($sql,$db);
							$rsproduk=mysql_fetch_array($hsltemp);
							$kode=$kd_produk_b;
							$kode_grade_a=$kd_produk;
							$grade="b";
							$kode_basic_item=$rsproduk["kode_basic_item"];
							$kode_kategori=$rsproduk["kode_kategori"];
							$kode_kelas=$rsproduk["kode_kelas"];
							$kode_style=$rsproduk["kode_style"];
							$kode_model=$rsproduk["kode_model"];
							$kode_size=$rsproduk["kode_size"];
							$kode_supplier=$rsproduk["kode_supplier"];
							$kode_warna=$rsproduk["kode_warna"];
							$nama=$rsproduk["nama"];
							$startqty=$grade_b;
							$satuan=$rsproduk["satuan"];
							$hargadasar=$rsproduk["hargadasar"];
							$hargajual=$rsproduk["hargajual_b"];
							$hargadasar_b=$rsproduk["hargadasar_b"];
							$hargajual_b=$rsproduk["hargajual_b"];
							$harganaik=$rsproduk["harganaik"];
							$updateby=$approveby;
							$coahpp=$rsproduk["coahpp"];
							$coastok=$rsproduk["coastock"];
							// $sql="INSERT INTO produk (kode,kode_grade_a,grade,kode_basic_item,kode_kategori,kode_kelas,kode_style,kode_model,kode_size,kode_supplier,kode_warna,nama,startqty,satuan,hargadasar,hargajual,hpp_b,hargajual_b,harganaik,updatedate,updateby,coahpp,coastock) VALUES ";
							// $sql.="('$kode','$kode_grade_a','$grade','$kode_basic_item','$kode_kategori','$kode_kelas','$kode_style','$kode_model','$kode_size','$kode_supplier','$kode_warna','$nama','$startqty','$satuan','$hargadasar_b','$hargajual_b','0','0','$harganaik',NOW(),'$updateby','$coahpp','$coastok')";
							// $sql.="('$kode','$kode','$grade','$kode_basic_item','$kode_kategori','$kode_kelas','$kode_style','$kode_model','$kode_size','$kode_supplier','$kode_warna','$nama','$startqty','$satuan','$hargadasar_b','$hargajual_b','0','0','$harganaik',NOW(),'$updateby','$coahpp','$coastok')";
                            
                            // mysql_query($sql,$db);
					
              
						}
						//exit;
					}
					
					/*buat journal sewing disini*/
					$ArrPrdNom[$kd_produk]=$jumlah;	
				}
				$sql="INSERT INTO job_fin (no_fin,no_qc,tanggal,totalqty,totalrp) VALUES ('$no_fin','$no_qc',NOW(),'$totalqty','$totalrp')";
				 echo "<br>".$sql;
				mysql_query($sql,$db);
				
				if($oke){
					$sql="INSERT INTO job_qc (no_qc,no_sew,tanggal,totalqty,totalrp,approve,approveby,approvedate)values('$no_qc_tr','$no_sew',NOW(),'$tqtyku','$tqtykuuang','1','$_SESSION[username]',NOW())";
					mysql_query($sql,$db)or die($sql);
				}
				/*buat jurnal QC disini*/
				//jurnal_QC($no_qc,$ArrPrdNom);
				
				$totalqty=0;
				$totalrp=0;
				$seqno=-1;
				foreach($arrprodukturunan as $kd_produk => $qtyproduk){
					$seqno++;
					$sql="SELECT hargadasar FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($harga)=mysql_fetch_array($hsltemp);
					$jumlah=$harga*$qtyproduk;
					$totalqty+=$qtyproduk;
					$totalrp+=$jumlah;
					$grade_a=sanitasi($_POST["grade_aturunan"][$kd_produk]);
					$grade_b=sanitasi($_POST["grade_bturunan"][$kd_produk]);
					$keterangan=sanitasi($_POST["keteranganturunan"][$kd_produk]);
					$sql="UPDATE job_qc_turunan SET grade_a='$grade_a',grade_b='$grade_b',keterangan='$keterangan' WHERE no_qc='$no_qc' AND kd_produk='$kd_produk'";
					 echo "<br>".$sql;
					mysql_query($sql,$db);
					$sql="INSERT INTO job_fin_turunan (no_fin,seqno,kd_produk,harga,qty,keterangan) VALUES ('$no_fin','$seqno','$kd_produk','$harga','$qtyproduk','')";
					 echo "<br>".$sql;
					mysql_query($sql,$db);
					// tambah produk grade b turunan
					if($grade_b>0){
						$kd_produk_b=substr($kd_produk,0,11).substr($kd_produk,0,1).substr($kd_produk,12,3);
						$sql="SELECT kode FROM produk WHERE kode='$kd_produk'";
						$hsltemp=mysql_query($sql,$db);
						if(mysql_affected_rows($db)<=0){
							$sql="SELECT * FROM produk WHERE kode='$kd_produk'";
							$hsltemp=mysql_query($sql,$db);
							$rsproduk=mysql_fetch_array($hsltemp);
							$kode=$kd_produk_b;
							$kode_grade_a=$kd_produk;
							$grade="b";
							$kode_basic_item=$rsproduk["kode_basic_item"];
							$kode_kategori=$rsproduk["kode_kategori"];
							$kode_kelas=$rsproduk["kode_kelas"];
							$kode_style=$rsproduk["kode_style"];
							$kode_model=$rsproduk["kode_model"];
							$kode_size=$rsproduk["kode_size"];
							$kode_supplier=$rsproduk["kode_supplier"];
							$kode_warna=$rsproduk["kode_warna"];
							$nama=$rsproduk["nama"];
							$startqty=$grade_b;
							$satuan=$rsproduk["satuan"];
							$hargadasar=$rsproduk["hargadasar"];
							$hargajual=$rsproduk["hargajual_b"];
							$hargadasar_b=$rsproduk["hargadasar_b"];
							$hargajual_b=$rsproduk["hargajual_b"];
							$harganaik=$rsproduk["harganaik"];
							$updateby=$approveby;
							$coahpp=$rsproduk["coahpp"];
							$coastok=$rsproduk["coastok"];
		/* Tutup Goberan 2011-04-11 `*/				
		//	$sql="INSERT INTO produk (kode,kode_grade_a,grade,kode_basic_item,kode_kategori,kode_kelas,kode_style,kode_model,kode_size,kode_supplier,kode_warna,nama,startqty,satuan,hargadasar,hargajual,hpp_b,hargajual_b,harganaik,updatedate,updateby,coahpp,coastok) VALUES ";
						//	$sql.="('$kode','$kode_grade_a','$grade','$kode_basic_item','$kode_kategori','$kode_kelas','$kode_style','$kode_model','$kode_size','$kode_supplier','$kode_warna','$nama','$startqty','$satuan','$hargadasar_b','$hargajual_b','0','0','$harganaik',NOW(),'$updateby','$coahpp','$coastok')";
						//	mysql_query($sql,$db);
						}
					}
				}
				
				?>
					<script language="javascript">
						alert("QUALITY CONTROL telah di approve.");
						
						window.location="job_qc_detail.php?no_qc=<?php echo $no_qc; ?>";
					</script>
				<?php
			}else{
				?>
					<script language="javascript">
						alert("QUALITY CONTROL gagal di approve, Silakan hubungi Technical Support Anda!");
						window.location="job_qc_detail.php?no_qc=<?php echo $no_qc; ?>";
					</script>
				<?php
			}
		}else{
			?>
				<script language="javascript">
					alert("QUALITY CONTROL gagal di approve. Total Qty Grade A dan Grade B harus sama dengan Qty Produk!");
					window.location="job_qc_detail.php?no_qc=<?php echo $no_qc; ?>";
				</script>
			<?php
		}
	}else{
		?>
			<script language="javascript">
				alert("QUALITY CONTROL sudah di approve.");
				window.location="job_qc_detail.php?no_qc=<?php echo $no_qc; ?>";
			</script>
		<?php
	}
?>
<?php include_once "footer.php" ?>
