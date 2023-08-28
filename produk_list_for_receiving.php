
<?php include_once "connect.php" ?>
<?php $content_title = "DAFTAR LIST PRODUK";
include_once "header_window_content.php" ?>
<script language="javascript">
	function showparent(textid, kode) {
		window.opener.document.getElementById(textid).value = kode;
		window.close();
	}
</script>

<?php
echo $cari = $_REQUEST[cari];
 if ($cari==''){
$cari2='';
 }else{
	$cari2.="%$cari%";
 }
$sql_cache = ' SQL_CACHE ';
?>
<form method="post">
	<table border="0" width="150%">
		<tr>
			<td>Pencarian : <input type="text" name="cari" value="<?php echo $cari ?>" size="40" />&nbsp;<input type="submit" name="submit" value="Cari" /></td>
		</tr>
		
		<tr>
			<td><sub>Pencarian bisa berdasarkan Nama Pabrik / No. SJ / Deskripsi / Tgl. SJ </sub></td>
		</tr>
	</table>
</form>
<table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
	<tr class="header_table_q">
		<td align="center" width="48" bgcolor="#0F74A8"  height="24"><b style="color:white">No</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Barcode</b></td>
		<td align="center" width="250" bgcolor="#0F74A8" height="24"><b style="color:white">Nama Produk</b></td>
		<td align="center" width="10" bgcolor="#0F74A8" height="24"><b style="color:white">Kode Size</b></td>
		<td align="center" width="10" bgcolor="#0F74A8" height="24"><b style="color:white">Size</b></td>
		<td align="center" width="10" bgcolor="#0F74A8" height="24"><b style="color:white">Kode Warna</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Warna</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Harga Satuan</b></td>
	</tr>
	<?php

	$sql = "SELECT p.kode as kode, p.nama as nama, p.kode_size as kode_size, s.size as size, p.kode_warna as kode_warna, w.warna as warna, p.hargajual as hargajual
	FROM produk AS p INNER JOIN mst_size AS s  ON (p.kode_size = s.kode)
					 INNER JOIN mst_warna AS w ON (p.kode_warna = w.kode)
	where p.nama LIKE '$cari2'";

	// if ($_SESSION['username'] == 'B120938_ahmad') {
	// 	echo $sql;
	// }

	$query = mysql_query($sql) or die($sql);
	$jmlData = mysql_num_rows($query);
	$hal = $_REQUEST[hal];
	if ($hal == "") {
		$hal = "0";
	}
	$jmlHal = 500;
	$awal = $hal * $jmlHal;
	$tothal = ceil($jmlData / $jmlHal);
	$sql = $sql . " limit $awal,$jmlHal";
	$hsl = mysql_query($sql) or die($sql);
	$no = $hal * $jmlHal;
	while ($rs = mysql_fetch_array($hsl)) {
		$no++;
		$kode = $rs["kode"];
		$nama = $rs["nama"];
		$kode_size = $rs["kode_size"];
		$size = $rs["size"];
		$kode_warna = $rs["kode_warna"];
		$warna = $rs["warna"];
		$hargajual = $rs["hargajual"];


		$bgclr1 = "#F9F9F9";
		$bgclr2 = "#D9EDF7";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr onMouseOver="this.bgColor = '#87CEFA'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td align="center" width="48" height="20"><?php echo $no; ?></td>
			<td height="20">&nbsp;<a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $kode; ?>');"><?php echo $kode; ?></a></td>
			<td height="20">&nbsp;<?php echo $nama; ?></td>
			<td align="center" height="5">&nbsp;<?php echo $kode_size; ?></td>
			<td align="center"height="5">&nbsp;<?php echo $size; ?></td>
			<td align="center"height="5">&nbsp;<?php echo $kode_warna; ?></td>
			<td height="20">&nbsp;<?php echo $warna; ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($hargajual, 2, ",", "."); ?></td>
		</tr>
	<?php
	
	}
	?>
</table>

<table style="margin-left:10px; margin-top:10px;">
	<tr>
		<td class="text_standard">
			<?php $terusan = "&cari1=$cari1"; ?>
			Page :
			<span class="hal" onclick="location.href='produk_list_for_receiving.php?hal=0&textid=kode&cari=<?php echo $cari ?>';">First</span>
			<?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='produk_list_for_receiving.php?hal=<?php echo $i ?>&textid=kode&cari=<?php echo $cari ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData / $jmlHal)) {
					if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='produk_list_for_receiving.php?hal=<?php echo $i ?>&textid=kode&cari=<?php echo $cari ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='produk_list_for_receiving.php?hal=<?php echo $i ?>&textid=kode&cari=<?php echo $cari ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="produk_list_for_receiving.php.php?hal=<?php echo $tothal ?>&textid=kode&cari=<?php echo $cari ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($jmlData); ?>
		</td>
	</tr>
</table>
<?php include_once "footer_window_content.php" ?>