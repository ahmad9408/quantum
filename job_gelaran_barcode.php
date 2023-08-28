<?php
	session_start();
	header("Content-type: application/x-msdownload");
	header("Content-Disposition: attachment; filename=barcode.xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	include_once "connect_config.php";
	if(!$db=mysql_connect($host,$user,$pass)){echo "DB Not Connect!";}
	mysql_select_db($dbname,$db);
	$no_po=sanitasi($_GET["no_po"]);
	$no_co=sanitasi($_GET["no_co"]);
	$sql="SELECT kd_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' GROUP BY seqno";
	$hslprod=mysql_query($sql,$db);
	$no=0;
?>
	<table>
		<tr>
			<td>Barcode 13</td>
			<td>Barcode 15</td>
            <td>Nama</td>
			<td>Size</td>
			<td>Harga</td>
			<td>Quantity</td>
		</tr>
<?php
	while(list($kd_produk)=mysql_fetch_array($hslprod)){
		/* Cari Kode 13 Nya */
        $sql="SELECT kode_grade_a FROM produk WHERE kode='$kd_produk'";
        $hsltemp=mysql_query($sql,$db);
        list($kd_produk_13)=mysql_fetch_array($hsltemp);
        $sql="SELECT nama,kode_size,hargajual FROM produk WHERE kode='$kd_produk'";
		$hsltemp=mysql_query($sql,$db);
		list($nama,$_size,$harga)=mysql_fetch_array($hsltemp);
		$sql="SELECT size FROM mst_size WHERE kode='$_size'";
		$hsltemp=mysql_query($sql,$db);
		list($size)=mysql_fetch_array($hsltemp);
		$sql="SELECT qty_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' AND kd_produk='$kd_produk' GROUP BY kd_produk";
		$hsltemp=mysql_query($sql,$db);
		list($qty)=mysql_fetch_array($hsltemp);
		?>
			<tr>
                <td><?php echo $kd_produk_13; ?></td>
				<td><?php echo $kd_produk; ?></td>
				<td><?php echo $nama; ?></td>
				<td><?php echo $size; ?></td>
				<td>Rp.<?php echo number_format($harga,0,",","."); ?></td>
				<td><?php echo $qty; ?></td>
			</tr>
		<?php
	}
?>
	</table>