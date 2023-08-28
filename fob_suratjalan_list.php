<?php include_once "connect.php" ?>
<?php $content_title = "DAFTAR LIST SURAT JALAN";
include_once "header_window_content.php" ?>
<script language="javascript">
	function showparent(textid, id_suratjalan) {
		window.opener.document.getElementById(textid).value = id_suratjalan;
		window.close();
	}
</script>
<?php
echo $cari = $_REQUEST[cari];
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
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">No Surat Jalan</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Tgl. Surat Jalan</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Supplier</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Deskripsi</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Qty</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Harga Satuan</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Subtotal</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">PPN</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Harga</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Harus Bayar</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Sudah Bayar</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Sisa Bayar</b></td>
	</tr>
	<?php

	$sql = "SELECT $sql_cache
					id_suratjalan
					, tgl_datang
					, nama_supplier
					, keterangan
					, qty
					, ppn
					, subtotal
					, total_harga
					, total_harus_bayar
					, total_bayar
					, sisa_bayar
					, status
					, approve2
					FROM
					fob_receiving
			   		WHERE id_suratjalan like '%$cari%' AND status='1' AND approve2='1'ORDER BY tgl_datang DESC,id_suratjalan ";

	if ($_SESSION['username'] == 'B120938_ahmad') {
		echo $sql;
	}

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
		$id_suratjalan = $rs["id_suratjalan"];
		$tgl_datang = $rs["tgl_datang"];
		$nama_supplier = $rs["nama_supplier"];
		$keterangan = $rs["keterangan"];
		$ppn = $rs["ppn"];
		$qty = $rs["qty"];
		$subtotal = $rs["subtotal"];
		$total_harga = $rs["total_harga"];
		$harus_bayar = $rs["total_harus_bayar"];
		$total_bayar = $rs["total_bayar"];
		$sisa_bayar = $rs["sisa_bayar"];


		$bgclr1 = "#F9F9F9";
		$bgclr2 = "#D9EDF7";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr onMouseOver="this.bgColor = '#87CEFA'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td align="center" width="48" height="20"><?php echo $no; ?></td>
			<td height="20"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $id_suratjalan; ?>');"><?php echo $id_suratjalan; ?></a></td>
			<td height="20"><?php echo $tgl_datang; ?></td>
			<td height="20"><?php echo $nama_supplier; ?></td>
			<td height="20">&nbsp;<?php echo $keterangan; ?></td>
			<td height="20">&nbsp;<?php echo $qty; ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($hargasatuan = $subtotal/$qty, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($subtotal, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($ppn, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($total_harga, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($harus_bayar, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($total_bayar, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($sisa_bayar, 2, ",", "."); ?></td>
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
			<span class="hal" onclick="location.href='fob_suratjalan_list.php?hal=0&textid=id_suratjalan&cari=<?php echo $cari ?>';">First</span>
			<?php for ($i = 0; $i < ($jmlData / $jmlHal); $i++) {
				if ($hal <= 0) { ?>
					<span class="<?php if ($i == $hal) echo "hal_select";
									else echo "hal"; ?>" onclick="location.href='fob_suratjalan_list.php?hal=<?php echo $i ?>&textid=id_suratjalan&cari=<?php echo $cari ?>';"><?php echo ($i + 1); ?></span>
					<?php if ($i >= 4) break;
				} else if (($hal + 1) >= ($jmlData / $jmlHal)) {
					if ($i >= (($jmlData / $jmlHal) - 5)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='fob_suratjalan_list.php?hal=<?php echo $i ?>&textid=id_suratjalan&cari=<?php echo $cari ?>';"><?php echo ($i + 1); ?></span>
					<?php }
				} else {
					if ($i <= ($hal + 2) and $i >= ($hal - 2)) { ?>
						<span class="<?php if ($i == $hal) echo "hal_select";
										else echo "hal"; ?>" onclick="location.href='fob_suratjalan_list.php?hal=<?php echo $i ?>&textid=id_suratjalan&cari=<?php echo $cari ?>';"><?php echo ($i + 1); ?></span>
			<?php }
				}
			} ?>
			<span class="hal" onclick="fob_suratjalan_list.php.php?hal=<?php echo $tothal ?>&textid=id_suratjalan&cari=<?php echo $cari ?>';">Last</span>
			&nbsp;&nbsp;
			Data <?php echo ($hal * $jmlHal) + 1; ?> of <?php echo ($jmlData); ?>
		</td>
	</tr>
</table>
<?php include_once "footer_window_content.php" ?>