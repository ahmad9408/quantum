<?php $content_title="DAFTAR QC LIST "; include_once "header.php" ?>
<?php
	$no_co=sanitasi($_GET["no_co"]);
	$no_sew=sanitasi($_GET["no_sew"]);
	$cari=$_POST['cari'];
	$status=$_POST['status'];
	if($status=='1'){
		$terusan=" and approve2='1' ";
	}else if ($status=='2'){
		$terusan=" and approve2 IS NULL ";
	}else{
		$terusan=" ";
	}
	$dari=$_POST['dari'];
	$sampai=$_POST['sampai'];
	if($dari==""){
		$dari=date("Y-")."01-01";
		$sampai=date("Y-m-d");
		// $hari_ini = date("Y-m-d");
		// $tgl1 = date('Y-01-01', strtotime($hari_ini));
		// $tgl2 = date('Y-m-t', strtotime($hari_ini));
	}
?>
	<form method="post" action="job_qc_list_v2.php" id="f1">
    <table border="0">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>
		<input type="button" value="Kembali" onclick="window.location='job_qc_rekap.php';">
		</td>
	</tr>
	</table>
	</form>
	<script language="javascript">
	function pindah_halaman(i){
		$("#hal").val(i);
		$("#submit").click();
	}
	</script>
	<table border="1" width="100%" style="font-size: 10pt" height="68" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8">
		<tr>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No CO</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No CO Mapping</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No QC</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Model</b></td>
			<td align="center" width="120" bgcolor="#99CC00" height="20"><b>Pabrik</b></td>
            <td align="center" width="50" bgcolor="#99CC00" height="20"><b>QTY</b></td>
            <td align="center" width="50" bgcolor="#99CC00" height="20"><b>Grade A</b></td>
            <td align="center" width="50" bgcolor="#99CC00" height="20"><b>Grade B</b></td>
            <td align="center" width="50" bgcolor="#99CC00" height="20"><b>Service / Pending</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Tanggal</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20" nowrap><b>Approve I</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20" nowrap><b>Approve II</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20" ><b>Action</b></td>
		</tr>
		<?php
			$_pabrik=" like '%'";
			if(strtoupper($_SESSION['outlettype'])=="P"){
				$_pabrik=$_SESSION['outlet'];
				if($_pabrik=='P0006'){
					$sql="select id from pabrik where mk='1' ";
					$resri=mysql_query($sql)or die($sql);
					$banyak_pabrik=mysql_num_rows($resri);
					while(list($kd_pabrik)=mysql_fetch_array($resri)){
						$j++;
						if($j==$banyak_pabrik){
							$pabrik.="'$kd_pabrik'";
						}else{
							$pabrik.="'$kd_pabrik',";
						}
					}
					$_pabrik=" in (".$pabrik.")";
						
				}else{
					$_pabrik=" like '$_pabrik%'";
				}
			} 
			
			
	if(isset($_POST['hal'])) $hal=$_POST['hal']; else $hal=0;
	$jmlHal=50;
	$page=$hal;  
			$no_co=sanitasi($_GET["no_co"]);
			$sql="SELECT job_qc.* FROM job_qc 
			inner join job_qc_detail as jqd on 
			(jqd.no_qc=job_qc.no_qc)
			left join mst_model_fix as f on 
			(substring(jqd.kd_produk,1,7)=f.kode_model)
 			WHERE no_sew LIKE '%$no_sew' AND job_qc.no_co like '%$no_co%' AND tanggal between '$dari 00:00:00' and '$sampai 23:59:59' and   ";
			$sql.="no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load IN (SELECT no_load FROM job_loading WHERE pabrik_tujuan $_pabrik and pabrik_tujuan not like '%P1000%' )) and
			(f.nama_model like '%$cari%' or job_qc.no_co like '%$cari%' or job_qc.no_qc like '%$cari%') $terusan and job_qc.totalqty>0
			group by job_qc.no_qc";

			$query=mysql_query($sql,$link);
			
			$jmlData[0]=mysql_num_rows($query); 
			
			 
			$sql.=" ORDER BY approve ,tanggal DESC,no_qc limit ".($page*$jmlHal).",".$jmlHal; 
			 
			$hsl=mysql_query($sql,$db);
			$no=($hal*$jmlHal);
			while($rs=mysql_fetch_array($hsl)){
				
				$no_qc=$rs["no_qc"];
				$no_sew=$rs["no_sew"];
				$sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
				$hsltemp=mysql_query($sql,$db);
				list($no_load)=mysql_fetch_array($hsltemp);
				
				$sql="SELECT no_co,pabrik_dari,pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
				$hsltemp=mysql_query($sql,$db);
				list($no_co,$pabrik_dari,$pabrik_tujuan)=mysql_fetch_array($hsltemp);
				if(!$pabrik_tujuan){$pabrik_tujuan=$pabrik_dari;}

				$sql="SELECT nama FROM pabrik WHERE id='$pabrik_tujuan'";
				$hsltemp=mysql_query($sql,$db);
				list($pabrikname)=mysql_fetch_array($hsltemp);

				$sql="SELECT no_co_mapping FROM job_gelaran WHERE no_co='$no_co'";
				$hsltemp=mysql_query($sql,$db);
				list($no_co_mapping)=mysql_fetch_array($hsltemp);

				$sql="SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
				$hsltemp=mysql_query($sql,$db);
				list($no_jo,$no_po)=mysql_fetch_array($hsltemp);
				$tanggal=$rs["tanggal"];
				$totalqty=$rs["totalqty"];
				$jumlah=$rs["totalrp"];
				$approved=$rs["approve"];
				// Edited Bye Goberan
                                $sql="SELECT kd_produk FROM job_cutting_detail WHERE no_co='$no_co'";
                                $hsltemp=mysql_query($sql,$db);
                                list($kd_produk)=mysql_fetch_array($hsltemp);

                                $sql="SELECT * FROM produk WHERE kode = '$kd_produk'";
                                $hsltemp=mysql_query($sql,$db);
                                $rsa=mysql_fetch_array($hsltemp);
                                $kode=$rsa["kode"];
                                $kode_basic_item=$rsa["kode_basic_item"];
                                $kode_kategori=$rsa["kode_kategori"];
                                $kode_kelas=$rsa["kode_kelas"];
                                $kode_style=$rsa["kode_style"]; 
                                $kode_warna=$rsa["kode_warna"];
                                $kode_model=$rsa["kode_model"];
                                $sql="SELECT model FROM mst_model WHERE kode='$kode_model' AND kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
                                $hsltemp=mysql_query($sql,$db);
                                list($style)=mysql_fetch_array($hsltemp);
								
								$sql="SELECT SUM(grade_a),SUM(grade_b) FROM job_qc_detail  WHERE no_qc='$no_qc'";
								$hsltemp=mysql_query($sql,$db);
                                list($ga,$gb)=mysql_fetch_array($hsltemp);
								if($ga==""){
									$ga="-";$gb="-";
								}
								if($approved){
									$service=$totalqty-($ga+$gb);
								}else{
									$service=0;
								}

				if($approved){
					$status= "<b>Approved</b>";
				}else{
					$status="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
				$approved2=$rs["approve2"];
				if($approved2){
					$status2= "<b>Approved</b>";
				}else{
					$status2="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
                $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
                if($pabrik_tujuan!='P1000'){ $no++;
		?>
			<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
				<td align="center" width="20" height="20"><?php echo $no; ?></td>
				<td align="left" width="150" height="20">&nbsp;<?php echo $no_co; ?></td>
				<td align="left" width="50" height="20">&nbsp;<?php echo $no_co_mapping; ?></td>
				<td align="left" width="150" height="20">&nbsp;<?php echo $no_qc; ?></td>
				<td align="left" width="120" height="20">&nbsp;<?php echo $style; ?></td>
				<td align="left" width="170" height="20">&nbsp;<?php echo $pabrikname." [$pabrik_tujuan]"; ?></td>
				<td align="left" width="50" height="20">&nbsp;<?php echo $totalqty; ?></td>
                <td align="left" width="50" height="20">&nbsp;<?php echo $ga; ?></td>
                <td align="left" width="50" height="20">&nbsp;<?php echo $gb; ?></td>
                <td align="left" width="50" height="20">&nbsp;<?php echo $service; ?></td>
                <td align="left" width="140" height="20">&nbsp;<?php echo $tanggal; ?></td>
				<td align="left" width="120" height="20">&nbsp;<?php echo $status; ?></td>
				<td align="left" width="120" height="20">&nbsp;<?php echo $status2; ?></td>
				<td align="left" width="120" height="20" nowrap>&nbsp;
					<a href="job_qc_detail_tester.php?no_qc=<?php echo $no_qc; ?>">Detil</a>
				<?php
					if($approved2){
						$sql="SELECT no_fin FROM job_fin WHERE no_qc='$no_qc'";
						$hsltemp=mysql_query($sql,$db);
						if(mysql_affected_rows($db)>0){
					?>
						|
						<a href="job_fin_list.php?no_qc=<?php echo $no_qc; ?>">Finishing</a>
					<?php
						}
					}
				?>				
				</td>
			</tr>
		<?php }
			}
		?>
	</table>
    
    <table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
            	Page : 
                <span class="hal" onclick="pindah_halaman('0')">First</span>
                <?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="pindah_halaman('<?php echo $i?>')"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
						if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="pindah_halaman('<?php echo $i?>')"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="pindah_halaman('<?php echo $i?>')"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="pindah_halaman('<?php echo intval(($jmlData[0]/$jmlHal)) ?>')" >Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> total halaman <?php echo ceil($jmlData[0]/$jmlHal);?>
            </td>
        </tr>
    </table>
    <br /><br />
<?php include_once "footer.php" ?>
