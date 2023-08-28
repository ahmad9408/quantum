<?php include_once "connect.php" ?>
<?php $content_title="DAFTAR LOADING"; include_once "header_window_content.php" ?>
	<script language="javascript">
		function showparent(textid,no_load){
			window.opener.document.getElementById(textid).value=no_load;
			window.close();
		}
</script>
<?php echo $cari=$_REQUEST[cari];?>
<form method="post" action="window_job_loading.php?textid=no_load">
<table border="0" width="100%">
<tr>
	<td>Pencarian : <input type="text" name="cari" value="<?php echo $cari?>" size="40" />&nbsp;<input type="submit" name="submit" value="Cari" /><sub>ket*) Pencarian bisa berdasarkan no load dan model </sub></td>
</tr>
</table>
</form>
	<table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#C0C0C0">
		<tr>
			<td align="center" width="48" bgcolor="#99CC00" height="24"><b>No</b></td>
			<td align="center" width="48" bgcolor="#99CC00" height="24"><b>No Load</b></td>
			<td align="center" width="48" bgcolor="#99CC00" height="24"><b>No Mapping</b></td>
			<td align="center" width="48" bgcolor="#99CC00" height="24"><b>Tanggal</b></td>
			<td align="center" width="150" bgcolor="#99CC00" height="24"><b>Model</b></td>
			<td align="center" width="48" bgcolor="#99CC00" height="24"><b>Total Qty Produk</b></td>
			<td align="center" width="48" bgcolor="#99CC00" height="24"><b>Qty Sewing Sebelumnya</b></td>
		</tr>
		<?php
			$_pabrik="";
			$terusan_pabrik=" AND  (pabrik_dari LIKE '$_pabrik%' OR pabrik_tujuan LIKE '$_pabrik%' ) ";
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
					$pabrik_in="(".$pabrik.")";
						$terusan_pabrik=" and   pabrik_dari in $pabrik_in ";
					}else{
						$terusan_pabrik=" and pabrik_dari LIKE '$_pabrik%' ";
					}
				
			}
			$sql="SELECT * FROM job_loading WHERE pabrik_dari=pabrik_tujuan AND pabrik_dari LIKE '$_pabrik%' AND approve2='1' ORDER BY tanggal DESC,no_load";
			
			$sql="SELECT
			`jl`.`no_load` as 'no_load',
			`jl`.`nama` as 'nama',
			`jl`.`tanggal` as 'tanggal',
			`jl`.`totalqtyproduk` as 'totalqtyproduk',
			`jl`.`pabrik_dari` as 'pabrik_dari',
			`jl`.`pabrik_tujuan` as 'pabrik_tujuan',
			`jg`.`no_co_mapping` as 'no_co_mapping'
		  FROM
			`quantum`.`job_loading` AS `jl`
			LEFT JOIN `quantum`.`job_gelaran` AS `jg`
			  ON (`jl`.`no_co` = `jg`.`no_co`)
			   WHERE pabrik_dari=pabrik_tujuan  $terusan_pabrik AND  approve2='1' and (`no_load` like '%$cari%' or `nama` like '%$cari%') ORDER BY tanggal DESC,no_load ";
 
	if($_SESSION['username']=='B120938_ahmad'){
	echo $sql;
	}
 
$query=mysql_query($sql)or die($sql);
$jmlData=mysql_num_rows($query);
$hal=$_REQUEST[hal];
if($hal==""){
	$hal="0";
}
$jmlHal=100;
$awal=$hal*$jmlHal;
$tothal=ceil($jmlData/$jmlHal);
$sql=$sql." limit $awal,$jmlHal";
$hsl=mysql_query($sql)or die($sql);
	$no=$hal*$jmlHal;
			while($rs=mysql_fetch_array($hsl)){
				$no++;
				$no_load=$rs["no_load"];
				$tanggal=$rs["tanggal"];
				$totalqtyproduk=$rs["totalqtyproduk"];
				$style=$rs["nama"];
				$no_mapping=$rs["no_co_mapping"];
				//$totalqty=$rs["totalqty"];
			// Edited Bye Goberan
                               /* $sql="SELECT kd_produk FROM job_loading_detail WHERE no_load='$no_load'";
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
                                list($style)=mysql_fetch_array($hsltemp);	*/
				$sql="SELECT SUM(jsd.qty-jsd.pending-jsd.reject) FROM job_sewing_detail AS jsd 
INNER JOIN job_sewing AS js ON 
(js.no_sew=jsd.no_sew) 
WHERE js.no_load='$no_load'";
				$hsltemp=mysql_query($sql,$db);
				$cek=mysql_fetch_row($hsltemp);
				$bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
				$bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
                /* Tambah 08-12-2010 */
               /* $sql7="SELECT no_sew FROM job_sewing WHERE no_load='$no_load'";
                $hsltemp7=mysql_query($sql7);

                $nor=0;
                while ( list($no_sew)= mysql_fetch_array($hsltemp7)) {
    $nor++;
    // echo "$no_sew<br>";
    $sql3="SELECT sum(qty) from job_sewing_detail WHERE no_sew='$no_sew'";
    $hsltemp3=mysql_query($sql3);
    list($qtydetail)=mysql_fetch_array($hsltemp3);
    $qtydet +=$qtydetail;
   /*
    $sql4="SELECT sum(qty) from job_sewing_detail WHERE no_sew='$no_sew'";
    $hsltemp4=mysql_query($sql4);
    list($qtyturunan)=mysql_fetch_array($hsltemp4);
    $qtyturunan1+=$qtyturunan;
    */
    // echo "$qtydetail <br>";
    /*
}
$qtysewing=$qtydet+$qtyturunan1; 
/* End */

				//if ( $cek[0] < $totalqtyproduk ){
    //            if ( $qtysewing < $totalqtyproduk ){
			?>
			<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
				<td align="center" width="48" height="20"><?php echo $no; ?></td>
				<td height="20"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $no_load; ?>');"><?php echo $no_load; ?></a></td>
				<td height="20"><?php echo $no_mapping; ?></td>
				<td height="20"><?php echo $tanggal; ?></td>
				<td height="20">&nbsp;<?php echo $style; ?></td>
				<td align="right" width="100" height="20"><?php echo number_format($totalqtyproduk,2,",","."); ?></td>
				<td align="right" width="100" height="20"><?php echo number_format($cek[0],2,",","."); ?></td>
			</tr>
		<?php
	//	}
			}
		?>
	</table>

 <table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
			<?php $terusan="&cari1=$cari1";?>
            	Page : 
                <span class="hal" onclick="location.href='window_job_loading.php?hal=0&textid=no_load&cari=<?php echo $cari?>';">First</span>
                <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='window_job_loading.php?hal=<?php echo $i?>&textid=no_load&cari=<?php echo $cari?>';"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData/$jmlHal)){
						if($i>=(($jmlData/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='window_job_loading.php?hal=<?php echo $i?>&textid=no_load&cari=<?php echo $cari?>';"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='window_job_loading.php?hal=<?php echo $i?>&textid=no_load&cari=<?php echo $cari?>';"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="window_job_loading.php.php?hal=<?php echo $tothal?>&textid=no_load&cari=<?php echo $cari?>';">Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($jmlData); ?>
            </td>
        </tr>
</table>
<?php include_once "footer_window_content.php" ?>
