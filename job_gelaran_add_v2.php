<style>
	.datagrid table {
		border-collapse: collapse;
		text-align: left;
		width: 100%;
	}

	.datagrid {
		font: normal 12px/150% Arial, Helvetica, sans-serif;
		background: #fff;
		overflow: hidden;
		border: 1px solid #36752D;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
	}

	.datagrid table td,
	.datagrid table th {
		padding: 3px 10px;
	}

	.datagrid table thead th {
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420));
		background: -moz-linear-gradient(center top, #36752D 5%, #275420 100%);
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');
		background-color: #36752D;
		color: #FFFFFF;
		font-size: 12px;
		font-weight: bold;
		border-left: 1px solid #36752D;
	}

	.datagrid table thead th:first-child {
		border: none;
	}

	.datagrid table tbody td {
		color: #275420;
		border-left: 1px solid #C6FFC2;
		font-size: 10px;
		font-weight: normal;
	}

	.datagrid table tbody .alt td {
		background: #DFFFDE;
		color: #275420;
	}

	.datagrid table tbody td:first-child {
		border-left: none;
	}

	.datagrid table tbody tr:last-child td {
		border-bottom: none;
	}

	.datagrid table tfoot td div {
		border-top: 1px solid #36752D;
		background: #DFFFDE;
	}

	.datagrid table tfoot td {
		padding: 0;
		font-size: 12px
	}

	.datagrid table tfoot td div {
		padding: 2px;
	}

	.datagrid table tfoot td ul {
		margin: 0;
		padding: 0;
		list-style: none;
		text-align: right;
	}

	.datagrid table tfoot li {
		display: inline;
	}

	.datagrid table tfoot li a {
		text-decoration: none;
		display: inline-block;
		padding: 2px 8px;
		margin: 1px;
		color: #FFFFFF;
		border: 1px solid #36752D;
		-webkit-border-radius: 3px;
		-moz-border-radius: 3px;
		border-radius: 3px;
		background: -webkit-gradient(linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420));
		background: -moz-linear-gradient(center top, #36752D 5%, #275420 100%);
		filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');
		background-color: #36752D;
	}

	.datagrid table tfoot ul.active,
	.datagrid table tfoot ul a:hover {
		text-decoration: none;
		border-color: #275420;
		color: #FFFFFF;
		background: none;
		background-color: #36752D;
	}

	div.dhtmlx_window_active,
	div.dhx_modal_cover_dv {
		position: fixed !important;
	}

	.kelas_departemen {
		width: 50px;
		border: thin solid #06F;
		position: static;
		position: inherit !important;
		text-align: center;
		cursor: pointer;

	}

	.kelas_departemen:hover {
		background-color: #E2FBFC;

	}

	fieldset {
		border: 1px solid green
	}

	legend {
		padding: 0.2em 0.5em;
		border: 1px solid green;
		color: green;
		font-size: 90%;
	}
</style>
<link rel="stylesheet" href="themes/base/jquery.ui.all.css">


<?php
$content_title = "TAMBAH PPIC";
include_once "header.php";
include("css_group.php");
include_once "job_cutting_init.php";
$no_manufaktur = sanitasi($_GET['no_manufaktur']);
$sql = "SELECT closeco FROM po_manufaktur WHERE no_manufaktur='$no_manufaktur'";
$hsl = mysql_query($sql, $db);
if (mysql_affected_rows($db) < 1) {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No Po Tidak Ada");
		window.location = "job_gelaran_list_v3.php";
	</script>
<?php
	exit;
}
list($closeco) = mysql_fetch_array($hsl);
if ($closeco == "1") {
	include_once "footer.php";
?>
	<script language="javascript">
		alert("No Po Sudah Di Close");
		window.location = "job_gelaran_list_v3.php";
	</script>
	<?php
	exit;
}
/* $sql="SELECT no_po FROM job_gelaran WHERE no_po='$no_manufaktur'";
	$hsl=mysql_query($sql,$db);
	if(mysql_affected_rows($db)>0 && false){
		include_once "footer.php";
		?>
			<script language="javascript">
				alert("No Po ini Sudah dilakukan entry gelaran!");
				window.location="job_gelaran_list.php";
			</script>
		<?php
	} */




$kode_mapping	= str_replace(" ", "", $_POST['kode_mapping']);
$harga_makloon	=  $_POST['harga_makloon'];

$username = $_SESSION["username"];
$isShowHargaMakloon = 0;
$sql = "SELECT COUNT(*) ada FROM user_account_privileges_parameter WHERE username='$username' AND is_show_harga_makloom=1;";
$res = mysql_query($sql);
list($ada) = mysql_fetch_array($res);

if ($ada > 0) {
	$isShowHargaMakloon = 1;
}




if ($kode_mapping != "") {

	$pabrik = sanitasi($_POST["pabrik"]);
	$sup = "SELECT id_group from pabrik where id='$pabrik'";
	$qsup = mysql_query($sup) or die('error query get nama supplier');
	list($id_group) = mysql_fetch_array($qsup);

	if ($id_group == '2' && $harga_makloon == '') {
		include_once "footer.php";
	?>
		<script language="javascript">
			alert("Silahkan Isi Harga Satuan Makloon FOB dan Input Ulang Qty");
			window.location = 'job_gelaran_add_v2.php?no_manufaktur=<?php echo $no_manufaktur;  ?>';
		</script>
	<?php
		die;
	}

	echo "bandung";
	$banyak			= $_POST['banyak'];
	$kode_mapping	= str_replace(" ", "", $_POST['kode_mapping']);
	$harga_makloon	=  $_POST['harga_makloon'];
	$no_co = no_co();

	/* Optimasi 2022-07-19 */
	$sql = "SET autocommit = 0;";
	$query = mysql_query($sql);

	$sql = "START TRANSACTION;";
	$query = mysql_query($sql);

	$sql = "INSERT INTO job_cutting (no_co,no_jo,no_po,tanggal,kd_supplier,kd_produk,totalqty,totalrp,realcutting) VALUES ('$no_co','','$no_manufaktur',NOW(),'','','0','0','0')";
	mysql_query($sql, $db);

	$sql = "COMMIT;";
	$query = mysql_query($sql);

	for ($i = 1; $i <= $banyak; $i++) {
		$kode = $_POST["kode" . $i];
		//echo $_POST["kode".$i]." ".$_POST['qty_produk'.$i]."<br>";
		if ($_POST['qty_produk' . $i] > 0) {
			$qty_produk = $_POST['qty_produk' . $i];
			$seqno++;

			$sql = "SELECT satuan,qty,kode_barang FROM produk_detail WHERE kode_produk='$kode' ";
			$query = mysql_query($sql) or die($sql);
			list($satuan, $qty, $kode_rm) = mysql_fetch_array($query);


			$sql = "SET autocommit = 0;";
			$query = mysql_query($sql);

			$sql = "START TRANSACTION;";
			$query = mysql_query($sql);

			$sql = "INSERT INTO job_gelaran_detail (no_po,no_co,seqno,kd_produk,qty_produk,kd_barang,supplier,satuan,qty,kainmasuk,jml_gelaran) VALUES ";
			$sql .= "('$no_manufaktur','$no_co','$seqno','$kode','$qty_produk','$kode_rm','$supplier','$satuan','$qty','$qty_kainmasuk','$jml_gelaran')";
			$res = mysql_query($sql);

			$sql = "COMMIT;";
			$query = mysql_query($sql);


			$sql = "SET autocommit = 0;";
			$query = mysql_query($sql);

			$sql = "START TRANSACTION;";
			$query = mysql_query($sql);

			$sql = "INSERT INTO `job_cutting_detail`
				            (`no_co`,
				             `seqno`,
				             `kd_produk`,
				             `qty`,
				             `coa`)
				VALUES ('$no_co',
				        '$seqno',
				        '$kode',
				        '$qty_produk',
				        '')";

			//	$res=mysql_query($sql);
			//mysql_query($sql,$db);	

			$sql = "COMMIT;";
			$query = mysql_query($sql);
		}
	}
	$pabrik = sanitasi($_POST["pabrik"]);
	$updateby = $_SESSION["username"];

	$_ppn = "SELECT ppn from pabrik where id='$pabrik'";
	$qppn = mysql_query($_ppn) or die('error query get nama supplier');
	list($ppn_) = mysql_fetch_array($qppn);

	// menentukan harga ppn dan non ppn
	if ($ppn_ == '1') {
	  $ppn = 1.11;
	} else {
	  $ppn = 1;
	}

	$harga_makloon2=$harga_makloon/$ppn;

	$sql = "SET autocommit = 0;";
	$query = mysql_query($sql);

	$sql = "START TRANSACTION;";
	$query = mysql_query($sql);

	$sql = "INSERT INTO job_gelaran (no_po,no_co,pabrik,updateby,updatedate,no_co_mapping,harga_makloon) VALUES ('$no_manufaktur','$no_co','$pabrik','$updateby',NOW(),'$kode_mapping','$harga_makloon2')";
	$query = mysql_query($sql);

	$sql = "COMMIT;";
	$query = mysql_query($sql);

	if ($query) {
	?>
		<script type="text/javascript">
			alert("CO Tersimpan.");
			window.location = "job_gelaran_list_v3.php";
		</script>
<?php
	}
}
// echo "Username : $username<br>";    
?>
<form method="POST" id="f1" action="<?php echo $_SERVER['PHP_SELF']; ?>?no_manufaktur=<?php echo $no_manufaktur; ?>" class="datagrid" cellspacing="0" cellpadding="0">
	<fieldset>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<tr class="alt" height="20">
				<td width='150'><b>No Manufaktur</b></td>
				<td width='2'><b>:</b></td>
				<td><b><?php echo $no_manufaktur; ?></b></td>
			</tr>
			<tr height="20">
				<td><b>Pabrik</b></td>
				<td><b>:</b></td>
				<td>
					<select name="pabrik">
						<!-- option value="">-Pabrik-</option -->
						<?php
						$_pabrik = "";
						if (strtoupper($_SESSION['outlettype']) == "P") {
							$_pabrik = $_SESSION['outlet'];
						}

						/* C e k  A u t h */
						/* if ( $username !='superuser') {
                                $sql="SELECT pabrik FROM user_account WHERE username='$username'";
							    $hsl=mysql_query($sql,$db);
                                list($pabrik)=mysql_fetch_array($hsl);
                                $sql="SELECT id,nama FROM pabrik WHERE id='$pabrik' ORDER BY nama";
                            } else { */
						$sql = "SELECT id,nama FROM pabrik WHERE id LIKE '$_pabrik%' and status='1' ORDER BY nama";
						// }
						$hsltemp = mysql_query($sql, $db);
						while (list($id, $nama) = mysql_fetch_array($hsltemp)) {
						?>
							<option value="<?php echo $id; ?>"><?php echo "$nama [$id]"; ?></option>
						<?php
						}
						?>
					</select>
				</td>
			</tr>


			<tr class="alt" height="20">
				<td><b>NO CO Kode D</b></td>
				<td><b>:</b></td>
				<td><input type="text" id="kode_mapping" name="kode_mapping" /></td>
			</tr>
			<?php
			if ($isShowHargaMakloon == 1) {
				echo "<tr class='alt' height='20'>";
				echo "<td colspan='3'><b>* WAJIB DI ISI UNTUK MAKLOON FOB & CMT</b></td>";
				echo "</tr>";
				echo "<tr class='alt' height='20'>";
				echo "<td><b>Harga Satuan Makloon</b></td>";
				echo "<td><b>:</b></td>";
				echo "<td><input type='text' id='harga_makloon' name='harga_makloon' /></td>";
				echo "</tr>";
			}
			?>
			<!-- <tr class="alt" height="20">
				<td colspan="3"><b>* Harga Makloon Wajib Di Isi Untuk Supplier Kategori FOB</b></td>
			</tr>
			<tr class="alt" height="20">
				<td><b>Harga Satuan Makloon</b></td>
				<td><b>:</b></td>
				<td><input type="text" id="harga_makloon" name="harga_makloon" /></td>
			</tr> -->
		</table>
		<table class="datagrid" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th><b>No</b></th>
					<th><b>Kode Produk</b></th>
					<th><b>Nama Produk</b></th>
					<th><b>Warna</b></th>
					<th><b>Qty Produk</b></th>

				</tr>
			</thead>
			<?php
			$sql = "SELECT kd_produk,qty,w.warna FROM po_manufaktur_detail  as pmd inner join 
				mst_warna as w on (w.kode=substring(kd_produk,13,3))WHERE no_manufaktur='$no_manufaktur' order by seqno";
			$hslprod = mysql_query($sql, $db);
			$banyak = mysql_num_rows($hslprod);
			?>
			<input type="hidden" name="banyak" value="<?php echo $banyak ?>" />
			<?php
			$no = 0;
			while (list($kd_produk, $qtyprod, $warna) = mysql_fetch_array($hslprod)) {

				// echo $kd_produk."=$qtyprod&nbsp;<br>";
				$sql = "SELECT nama FROM produk WHERE kode='$kd_produk'";
				$hsltemp = mysql_query($sql, $db);
				list($nama) = mysql_fetch_array($hsltemp);
				$no++;
				$sql1 = "SELECT sum(qty_produk) FROM job_gelaran_detail WHERE no_po='$no_manufaktur' AND kd_produk='$kd_produk'";
				$hsltemp1 = mysql_query($sql1, $db);
				list($qtyprodsudah) = mysql_fetch_array($hsltemp1);
				$sisaqtyprod = $qtyprod - $qtyprodsudah;
				// echo "<br>$kd_produk | $qtyprod - $qtyprodsudah  Sisa Qty=<b>".$sisaqtyprod."</b><br>";
				$jumlahqtyprod += $qtyprod;
				$jumlahqtyprodsudah += $qtyprodsudah;
				if ($sisaqtyprod > 0) {
					$masihada = true;
					if ($no % 2 == 1) {
						$kelas1 = "alt";
					} else {
						$kelas1 = "";
					}

			?>
					<tr class="<?php echo $kelas1 ?>">
						<td><?php echo $no; ?></td>
						<td><?php echo $kd_produk; ?></td>
						<td><?php echo $nama; ?></td>
						<td><?php echo $warna; ?></td>
						<!--td align="right"><?php /* echo number_format($qtyprod); */ echo $qtyprod; ?></td-->
						<td align="right">
							<input type="hidden" name="kode<?php echo $no ?>" value="<?php echo $kd_produk ?>" />
							<input type="text" size="3" name="qty_produk<?php echo $no ?>" value="<?php echo number_format($sisaqtyprod); /*echo $sisaqtyprod;*/ ?>">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="3">
							<!-- table width="100%" border="1">
								<tr>
									<td><b>Kode RM</b></td>
									<td><b>Nama RM</b></td>
									<td><b>Warna</b></td>
									<td><b>Supplier</b></td>
									<td><b>Cons</b></td>
									<td><b>Total Qty</b></td>
									<td><b>Satuan</b></td>
									<td><b>Kain Keluar</b></td>
									<td><b>Stok RM</b></td>
									<td><b>Jml Gelaran</b></td>
								</tr -->
							<?php
							/*
									$sql="SELECT produk_detail.kode_produk,produk_detail.kode_barang,barangdetail.nama,produk_detail.qty,satuan.nama,barangdetail.mode_gelar ";
									$sql.="FROM produk_detail INNER JOIN barangdetail ON (produk_detail.kode_barang = barangdetail.id) INNER JOIN satuan ON (produk_detail.satuan = satuan.id)";
									$sql.="WHERE produk_detail.kode_produk='$kd_produk'";
									$hslbrg=mysql_query($sql,$db);
									while(list($kd_produk_det,$koderm,$namarm,$qtyrm,$satuan,$modegelar)=mysql_fetch_array($hslbrg)){
										$totalqtyrm=$qtyrm*$qtyprod;
										$sql="SELECT warna FROM mst_warna WHERE kode IN (SELECT warna FROM barangdetail WHERE id='$koderm')";
										$hsltemp=mysql_query($sql,$db);
										list($warna)=mysql_fetch_array($hsltemp);
										
										$tomorrow=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
										//cari qty masuk
										$sql="SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$koderm' AND tanggal < '$tomorrow%' AND (mode='1' OR mode='2' OR mode='4')";
										//echo "<br>$sql";
										$hsltemp=mysql_query($sql,$db);
										list($stokin)=mysql_fetch_array($hsltemp);
										$stokin+=0;
										
										//cari qty keluar
										$sql="SELECT sum(qty) FROM barang_stok_control WHERE kd_barang='$koderm' AND tanggal < '$tomorrow%' AND mode='3'";
										//echo "<br>$sql";
										$hsltemp=mysql_query($sql,$db);
										list($stokout)=mysql_fetch_array($hsltemp);
										$stokout+=0;
										
										$stokrm=$stokin-$stokout;
								?>
									<tr>
										<td><?php echo $koderm; ?></td>
										<td><?php echo $namarm; ?></td>
										<td><?php echo $warna; ?></td>
										<td>
											<select name="supplier[<?php echo $kd_produk; ?>][<?php echo $koderm; ?>]">
												<option value="">-supplier-</option>
												<?php
													$sql="SELECT id,nama FROM supplier ORDER BY nama";
													$hsltemp=mysql_query($sql,$db);
													while(list($id,$nama)=mysql_fetch_array($hsltemp)){
												?>
														<option value="<?php echo $id; ?>"><?php echo "$nama [$id]"; ?></option>
												<?php
													}
												?>
											</select>
										</td>
										<td align="right"><?php echo number_format($qtyrm,3,",","."); ?></td>
										<td align="right"><?php echo number_format($totalqtyrm,2,",","."); ?></td>
										<td><?php echo $satuan; ?></td>
										<td align="right">
											<?php 
												if($modegelar){
											?>
												<input type="text" name="kainmasuk[<?php echo $kd_produk; ?>][<?php echo $koderm; ?>]" size="4" value="<?php echo $totalqtyrm; ?>">
											<?php
												}else{echo "&nbsp;";}
											?>
										</td>
										<!--td align="right">&nbsp;
											<?php 
												if($modegelar){
											?>
												<input type="text" name="jml_gelaran[<?php echo $kd_produk; ?>][<?php echo $koderm; ?>]" size="4" value="<?php echo $totalqtyrm; ?>">
											<?php
												}else{echo "&nbsp;";}
											?>
										</td-->
										<td align="right"><?php echo number_format($stokrm,3,",","."); ?></td>
									</tr>
								<?php
									}*/
							?>
							<!-- /table -->
						</td>
					</tr>
			<?php
				}
			}
			// echo "TOTAL QTY $jumlahqtyprod & $jumlahqtyprodsudah";
			?>
		</table>

		<table width="100%">
			<tr>
				<td align="center">
					<!-- <input type="button" name="simpan" value="Simpan" onclick="getSave()"> -->
					<button class="btn btn-success btn-block" style="width:150px" type="button" name="simpan" value="Simpan" onclick="getSave()">Simpan</button>
				</td>
			</tr>
		</table>
	</fieldset>
</form>
<?php include_once "footer.php"; ?>
<?php
if (!$masihada) {
?>
	<script language="javascript">
		alert("No Po ini Sudah dilakukan entry CO!");
		window.location = "job_gelaran_list_v3.php";
	</script>
<?php
}
?>
<script>
	function getSave() {
		var vkode_mapping = $("#kode_mapping").val();
		var vharga_makloon = $("#harga_makloon").val();
		if (vkode_mapping == "") {
			alert('Wajib isi kode co anda sok lupa');
			exit();
		}

		if (vkode_mapping.length != 7) {
			alert('DI COPY PASTE SANES DI MANUAL CEU....');
			exit();
		}
		$("#f1").submit();

	}
</script>