<?php include_once "connect.php" ?>
<?php $content_title = "DAFTAR LIST INVOICE";
include_once "header_window_content.php" ?>
<script language="javascript">
	function showparent(textid, id_inv) {
		window.opener.document.getElementById(textid).value = id_inv;
		window.close();
	}
</script>
<?php
echo $cari = $_REQUEST[cari];
$sql_cache = ' SQL_CACHE ';
?>
<form method="post">
	<table border="0" width="100%">
		<tr>
			<td>Pencarian : <input type="text" name="cari" value="<?php echo $cari ?>" size="40" />&nbsp;<input type="submit" name="submit" value="Cari" /></td>
		</tr>

		<tr>
			<td><sub>Pencarian bisa berdasarkan Nama Pabrik / No. Invoice / Tgl. Invoice </sub></td>
		</tr>
	</table>
</form>
<table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
	<tr class="header_table_q">
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">No</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">No. Invoice</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Tgl. Invoice</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">Nama Supplier</b></td>
		<td align="center" width="150" bgcolor="#0F74A8" height="24"><b style="color:white">No Faktur Pajak</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Qty</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total PPn</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Jumlah</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Harga</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Diskon Nilai</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Harus Bayar</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Due Date</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Sudah Bayar</b></td>
		<td align="center" width="48" bgcolor="#0F74A8" height="24"><b style="color:white">Total Sisa Bayar</b></td>
	</tr>
	<?php

	$sql = "SELECT $sql_cache
					fi.id_invoice as id_invoice,
					fi.tanggal as tanggal_inv,
					p.nama as nama_supplier,
					fi.no_faktur_pajak as no_fk,
					fi.total_qty as tot_qty,
					fi.total_ppn as tot_ppn,
					fi.total_jumlah as tot_jum,
					fi.total_harga as tot_har,
					fi.disc_nilai as disc_har,
					fi.total_harus_bayar as tot_harusbayar,
					fi.tgl_harus_bayar as due_date,
					fi.total_sudah_bayar as tot_sudahbayar,
					fi.total_sisa_bayar as tot_sisabayar
					FROM
					fob_invoice AS fi
					LEFT JOIN pabrik AS p
					ON (fi.id_supplier = p.id)
			   		WHERE fi.id_invoice like '%$cari%' or fi.tanggal like '%$cari%' or fi.tanggal like '%$cari%' or p.nama like '%$cari%' ORDER BY fi.tanggal DESC, fi.id_invoice ";

	// if ($_SESSION['username'] == 'B120938_ahmad') {
	// 	echo $sql;
	// }

	$query = mysql_query($sql) or die($sql);
	$jmlData = mysql_num_rows($query);
	$hal = $_REQUEST[hal];
	if ($hal == "") {
		$hal = "0";
	}
	$jmlHal = 100;
	$awal = $hal * $jmlHal;
	$tothal = ceil($jmlData / $jmlHal);
	$sql = $sql . " limit $awal,$jmlHal";
	$hsl = mysql_query($sql) or die($sql);
	$no = $hal * $jmlHal;
	while ($rs = mysql_fetch_array($hsl)) {
		$no++;
		$id_inv = $rs["id_invoice"];
		$tgl_inv = $rs["tanggal_inv"];
		$nama_supplier = $rs["nama_supplier"];
		$no_faktur = $rs["no_fk"];
		$tot_qty = $rs["tot_qty"];
		$tot_ppn = $rs["tot_ppn"];
		$tot_jumlah = $rs["tot_jum"];
		$tot_harga = $rs["tot_har"];
		$disc_harga = $rs["disc_har"];
		$harus_bayar = $rs["tot_harusbayar"];
		$due_date = $rs["due_date"];
		$sudah_bayar = $rs["tot_sudahbayar"];
		$sisa_bayar = $rs["tot_sisabayar"];


		$bgclr1 = "#F9F9F9";
		$bgclr2 = "#D9EDF7";
		$bgcolor = ($no % 2) ? $bgclr1 : $bgclr2;
	?>
		<tr onMouseOver="this.bgColor = '#87CEFA'" onMouseOut="this.bgColor = '<?php echo $bgcolor; ?>'" bgcolor="<?php echo $bgcolor; ?>">
			<td align="center" width="48" height="20"><?php echo $no; ?></td>
			<td height="20"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $id_inv; ?>');"><?php echo $id_inv; ?></a></td>
			<td height="20"><?php echo $tgl_inv; ?></td>
			<td height="20"><?php echo $nama_supplier; ?></td>
			<td height="20">&nbsp;<?php echo $no_faktur; ?></td>
			<td height="20">&nbsp;<?php echo $tot_qty; ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($tot_ppn, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($tot_jumlah, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($tot_harga, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($disc_harga, 2, ",", "."); ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($harus_bayar, 2, ",", "."); ?></td>
			<td height="20"><?php echo $due_date; ?></td>
			<td align="right" width="100" height="20"><?php echo number_format($sudah_bayar, 2, ",", "."); ?></td>
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