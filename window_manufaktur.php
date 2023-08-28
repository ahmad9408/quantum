<?php include_once "connect.php" ?>
<?php $content_title="DAFTAR MANUFAKTUR"; include_once "header_window_content.php";

$no_po 	= $_POST['no_po'];
$model 	= $_POST['model'];


 ?>
	<script language="javascript">
		function showparent(textid,no_manufaktur){
			window.opener.document.getElementById(textid).value=no_manufaktur;
			window.close();
		}
</script>
<form id="f1" method="post"  name="f1" action="<?php echo $PHP_SELF?>">
<table border="0">
<tr>
	<td>No PO</td>
	<td width="2">:</td>
	<td><input type="text" id="no_po" name="no_po" value="<?php echo $no_po?>" size="30" /></td>
</tr>

 
<tr>
	<td></td>
	<td>:</td>
	<td><input type="submit" id="submit" name="submit"/></td>
</tr>
</table>
</form>
	<table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" height="72">
    <tr>
			<td align="center" bgcolor="#99CC00" height="21"><b>No</b></td>
			<td align="center" bgcolor="#99CC00" height="21"><b>No Manufaktur</b></td>
			<td align="center" bgcolor="#99CC00" height="21"><b>Tanggal</b></td>
			<td align="center" bgcolor="#99CC00" height="21"><b>Model</b></td>
			<td align="center" bgcolor="#99CC00" height="21"><b>Total PO</b></td>
            <td align="center" bgcolor="#99CC00" height="21"><b>CO</b></td>
            <td align="center" bgcolor="#99CC00" height="21"><b>Sisa PO</b></td>
			<!-- td align="center" bgcolor="#99CC00" height="21"><b>Jumlah (Rp)</b></td -->
		</tr>
		<?php
			if (sanitasi($_GET["sebelumppic"])=="OKE"){
				$whereclauseygbelumppic="AND no_manufaktur NOT IN (SELECT no_po FROM job_gelaran) ";
			}
			$sql="SELECT * FROM po_manufaktur WHERE no_manufaktur LIKE '%$no_manufaktur' AND approve2='1' AND (closeco IS NULL OR closeco!='1') $whereclauseygbelumppic ORDER BY tanggal DESC";
			
			
			$sql 	="SELECT p.* FROM po_manufaktur AS p 
INNER JOIN po_manufaktur_detail AS pd ON 
(p.no_manufaktur=pd.no_manufaktur)
LEFT JOIN mst_model_fix AS f ON 
(f.kode_model=SUBSTRING(pd.kd_produk,1,7)) 
WHERE p.no_manufaktur LIKE '%$no_po%' AND approve2='1'  AND  
(closeco IS NULL OR closeco!='1') group by p.no_manufaktur ORDER BY tanggal DESC limit 100 ";
 
			 
			$hsl=mysql_query($sql,$db);
			$no=0;
			while($rs=mysql_fetch_array($hsl)){
				$no++;
				$no_manufaktur=$rs["no_manufaktur"];
				$tanggal=$rs["tanggal"];
				$kode_supplier=$rs["no_vendor"];
				$sql="SELECT nama FROM supplier WHERE id='$kode_supplier'";
				$hsltemp=mysql_query($sql,$db);
				list($supplier)=mysql_fetch_array($hsltemp);
				$totalqty=$rs["totalqty"];
				$jumlah=$rs["totalrp"];
// Edited Bye Goberan
                                $sql="SELECT kd_produk FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
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

		// cek sudah ada di gelaran
				$sql="SELECT sum(qty_produk) FROM job_gelaran_detail WHERE no_po='$no_manufaktur'";
                                $hsltemp=mysql_query($sql,$db);
                                $cek=mysql_fetch_row($hsltemp);
								//echo $cek[0]." ".$totalqty;
                               // if ( $cek[0] < $totalqty ){
                                // $sisaqty=$totalqty-$cek[0];
				//
				// $query5="SELECT sum(qty) FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
				// $sql5=mysql_query($query5);
				// list($totalqty)=mysql_fetch_array($sql5);
				$query4="SELECT sum(qty_produk) FROM `job_gelaran_detail` WHERE `no_po`='$no_manufaktur'";
				$sql4=mysql_query($query4);
				list($qtysudah)=mysql_fetch_array($sql4);
				$sisaqty=$totalqty-$qtysudah;
		$bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
        ?>
			<tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
				<td height="18"><?php echo $no; ?></td>
				<td height="18"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $no_manufaktur; ?>');"><?php echo $no_manufaktur; ?></a></td>
				<td height="18"><?php echo $tanggal; ?></td>
				<td height="18" align="left">&nbsp;<? echo $style; ?></td>
				<td height="18" align="right"><?php echo $totalqty; ?></td>
                <td height="18" align="right"><?php echo $qtysudah; ?></td>
                <td height="18" align="right"><?php echo $sisaqty; ?></td>
				<!-- td height="18" align="right"><?php // echo number_format($totalrp,2,",","."); ?></td -->
			</tr>
		<?php
		//}
			}
		?>
	</table>
<?php include_once "footer_window_content.php" ?>
