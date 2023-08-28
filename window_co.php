<?php include_once "connect.php" ?>
<?php $content_title="DAFTAR CO"; include_once "header_window_content.php" ?>
<?php $cari=$_REQUEST[search_co];?>
	<script language="javascript">
		function showparent(textid,no_manufaktur){
			window.opener.document.getElementById(textid).value=no_manufaktur;
			window.close();
		}
</script>
	<table>
		<form action="?textid=no_co&search=yes" method="POST">
			<tr>
				<td><b>Pencarian * </b></td>
				<td><b>:</b></td>
				<td><input type="text" id="search_co" name="search_co" size="50" value="<?php echo $cari?>"></td>
				<td align='left'><input type="submit" value="Search"></form></td>
			</tr>
			<tr>
				<td colspan='4'><sub>* Jika tidak di temukan di list silahkan cari berdasarkan MODEL / NO CO / NO PO / NO CO MAPPING</sub></td>
			</tr>
		</table>
	<table border="1" style="font-size: 14px;">
		<tr style="background-color: #0f74a8; color: #fff;">
			<td><b>No</b></td>
			<td><b>Tanggal</b></td>
			<td><b>Pabrik</b></td>
			<td><b>Model</b></td>
			<td><b>No CO Mapping</b></td>
			<td><b>No Manufaktur</b></td>
			<td><b>No CO</b></td>
			<td><b>Catatan</b></td>
		</tr>
		<?php
			// $sql="SELECT * FROM po_manufaktur WHERE no_manufaktur LIKE '%$no_manufaktur' AND approve2='1' AND (closeco IS NULL OR closeco!='1') ORDER BY tanggal,no_manufaktur";
			// $sql="SELECT * FROM job_gelaran WHERE no_po IN (SELECT no_manufaktur FROM po_manufaktur WHERE approve2='1' AND (closeco IS NULL OR closeco!='1')) AND no_co IN (SELECT no_co FROM job_cutting WHERE realcutting='0')";
			$_pabrik="";
			if(strtoupper($_SESSION['outlettype'])=="P"){
				$_pabrik=$_SESSION['outlet'];
			}
			
			
			$_pabrik="";
				$gelaran_pabrik=" AND job_gelaran.pabrik LIKE '%' ";
			if(strtoupper($_SESSION['outlettype'])=="P"){
				if($_SESSION['outlet']=='P0006'){
					$gelaran_pabrik=" AND pabrik.mk='1' ";
				}else{
					$gelaran_pabrik=" AND job_gelaran.pabrik LIKE '$_SESSION[outlet]%' ";
				}
			}
			
		
			
			
				// $sql="SELECT * FROM job_cutting WHERE tanggal > DATE('2011-08-01') AND no_po IN (SELECT no_manufaktur FROM po_manufaktur WHERE approve2='1' AND (closeco IS NULL OR closeco!='1')) AND ";
				// $sql.="no_po IN (SELECT no_po FROM job_gelaran WHERE pabrik LIKE '$_pabrik%') AND no_co IN (SELECT no_co FROM job_gelaran WHERE pabrik LIKE '$_pabrik%') and (no_po like '%$cari%' or no_co like '%$cari%') ORDER BY realcutting,approve2,no_co DESC LIMIT 20";

				$sql="SELECT * FROM job_cutting WHERE tanggal > DATE('2021-08-01') AND no_po IN (SELECT no_manufaktur FROM po_manufaktur WHERE approve2='1' AND (closeco IS NULL OR closeco!='1')) AND ";
				$sql.="no_po IN (SELECT no_po FROM job_gelaran WHERE pabrik LIKE '$_pabrik%') AND no_co IN (SELECT no_co FROM job_gelaran WHERE pabrik LIKE '$_pabrik%') and (no_po like '%$cari%' or no_co like '%$cari%') ORDER BY realcutting,approve2,no_co DESC LIMIT 20";

	$_pabrik="";
			$terusan_pabrik="  ";
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
						$terusan_pabrik=" and   (`job_gelaran`.`pabrik` in $pabrik_in) ";
					}else{
						$terusan_pabrik=" AND  (job_gelaran.pabrik LIKE '$_pabrik%' ) ";
					}
				
			}
				
	/*			$sql="SELECT
    `job_gelaran`.`no_po` as no_po
    , `job_gelaran`.`no_co` as no_co
    , `job_gelaran`.`pabrik` as kode_pabrik
    , `pabrik`.`nama` as nama_pabrik
    , `job_gelaran`.`model` as model
, `job_cutting`.`tanggal` as tanggal
, `job_cutting`.`approve2` as approve2
FROM
    `job_gelaran`
    INNER JOIN `pabrik` 
        ON (`job_gelaran`.`pabrik` = `pabrik`.`id`)
    INNER JOIN `po_manufaktur` 
        ON (`job_gelaran`.`no_po` = `po_manufaktur`.`no_manufaktur`) 
    INNER JOIN `job_cutting` 
        ON (`job_cutting`.`no_co` = `job_gelaran`.`no_co`) WHERE 
(po_manufaktur.closeco IS NULL OR po_manufaktur.closeco!='1') AND 
job_cutting.tanggal > DATE('2011-08-01') 
AND (`job_gelaran`.`no_po` like '%$cari%' or `job_gelaran`.`no_co` like '%$cari%' or  
`job_gelaran`.`model` like '%$cari%' or pabrik.nama like '%$cari%') $terusan_pabrik
GROUP BY job_gelaran.no_co,job_gelaran.no_po ORDER BY 
realcutting,job_cutting.approve2,job_gelaran.no_co "; */

//DATE('2011-08-01') 
$sql="SELECT SQL_CACHE 
    `job_gelaran`.`no_po` as no_po
    , `job_gelaran`.`no_co` as no_co
    , `job_gelaran`.`pabrik` as kode_pabrik
    , `pabrik`.`nama` as nama_pabrik
    , `job_gelaran`.`model` as model
	, `job_gelaran`.`no_co_mapping` as mapping
, `job_cutting`.`tanggal` as tanggal
, `job_cutting`.`approve2` as approve2
, jgdr.catatan
FROM
    `job_gelaran`
    INNER JOIN `pabrik` 
        ON (`job_gelaran`.`pabrik` = `pabrik`.`id`)
    INNER JOIN `po_manufaktur` 
        ON (`job_gelaran`.`no_po` = `po_manufaktur`.`no_manufaktur`) 
    INNER JOIN `job_cutting` 
        ON (`job_cutting`.`no_co` = `job_gelaran`.`no_co`) 
	inner join job_gelaran_detail_rian as jgdr on 
	(jgdr.no_po=job_gelaran.no_po) WHERE 
(po_manufaktur.closeco IS NULL OR po_manufaktur.closeco!='1') AND 
job_cutting.tanggal > DATE('2021-01-01') 
AND (`job_gelaran`.`no_po` like '%$cari%' or `job_gelaran`.`no_co` like '%$cari%' or  
`job_gelaran`.`model` like '%$cari%' or pabrik.nama like '%$cari%' or jgdr.catatan like '%$cari%' or `job_gelaran`.`no_co_mapping` like '%$cari%') $terusan_pabrik
GROUP BY job_gelaran.no_co,job_gelaran.no_po ORDER BY 
`job_cutting`.`tanggal` desc ,job_gelaran.no_co ";
//echo $sql;


	
$query=mysql_query($sql)or die($sql);
$jmlData=mysql_num_rows($query);
$hal=$_REQUEST[hal];
if($hal==""){
	$hal="0";
}
$jmlHal=50;
$awal=$hal*$jmlHal;
$tothal=ceil($jmlData/$jmlHal);
$sql=$sql." limit $awal,$jmlHal";
$hsl=mysql_query($sql)or die($sql);
			$no=$hal*$jmlHal;
			// echo $sql."<br>";
			while($rs=mysql_fetch_array($hsl)){
				$no++;
				$no_po=$rs["no_po"];
				$no_co=$rs["no_co"];
				$style=$rs["model"];
				$tanggal=$rs["tanggal"];
				$pabrik=$rs["nama_pabrik"]."[".$rs["kode_pabrik"]."]";
				$approve2=$rs["approve2"];
				$catatan=$rs["catatan"];
				$mapping=$rs["mapping"];
				if($approve2!="1"){$fontcolor="color='red'";}else{$fontcolor="";}
				/*$sql="SELECT pabrik FROM job_gelaran WHERE no_po='$no_po' AND no_co='$no_co'";
				$hsltemp=mysql_query($sql,$db);
				list($id_pabrik)=mysql_fetch_array($hsltemp); 
				$tanggal=$rs["tanggal"];
				$sql="SELECT nama FROM pabrik WHERE id='$id_pabrik'";
				$hsltemp=mysql_query($sql,$db);
				list($nama_pabrik)=mysql_fetch_array($hsltemp); 
				$pabrik="$nama_pabrik [$id_pabrik]";
				
				
				$sql="SELECT kd_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co'";
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
                list($style)=mysql_fetch_array($hsltemp); */
		?>
			<tr>
				<td><font <?php echo $fontcolor; ?>><?php echo $no; ?></font></td>
				<td><font <?php echo $fontcolor; ?>><?php echo $tanggal; ?></font></td>
				<td><font <?php echo $fontcolor; ?>><?php echo $pabrik; ?></font></td>
				<td><font color="#000000"><b><?php echo $style; ?></b></font></td>
				<td><font color="#000000"><b><?php echo $mapping; ?></b></font></td>
				<td><font <?php echo $fontcolor; ?>><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $no_co; ?>');"><?php echo $no_po; ?></a></font></td>
				<td><font <?php echo $fontcolor; ?>><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $no_co; ?>');"><?php echo $no_co; ?></a></font></td>
				<td><font <?php echo $fontcolor; ?>><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $no_co; ?>');"><?php echo $catatan; ?></a></font></td>
			</tr>
		<?php
			}
		?>
	</table>
	
 <table style="margin-left:10px; margin-top:10px;">

        <tr style="background-color: #0f74a8; color: #fff;">
            <td class="text_standard">
			<?php $terusan="&cari1=$cari1";?>
            	Page : 
                <span class="hal" onclick="location.href='window_co.php?hal=0&textid=no_co&search_co=<?php echo $cari?>';">First</span>
                <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='window_co.php?hal=<?php echo $i?>&textid=no_co&search_co=<?php echo $cari?>';"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData/$jmlHal)){
						if($i>=(($jmlData/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='window_co.php?hal=<?php echo $i?>&textid=no_co&search_co=<?php echo $cari?>';"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='window_co.php?hal=<?php echo $i?>&textid=no_co&search_co=<?php echo $cari?>';"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="window_co.php?hal=<?php echo $tothal?>&textid=no_co&search_co=<?php echo $cari?>';">Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($jmlData); ?>
            </td>
        </tr>
</table>
<?php include_once "footer_window_content.php" ?>
