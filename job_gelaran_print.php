<?php 
	include_once "connect_config.php";
	if(!$db=mysql_connect($host,$user,$pass)){echo "DB Not Connect!";}
	mysql_select_db($dbname,$db);
 ?>
 <style>
	a{
		text-decoration:none;
		font-family:verdana;
		color:blue;
		}
	a:hover{
		text-decoration:none;
		font-family:verdana;
		}
	td{
		font-size:10px;
		font-family:verdana;
	}
	body{
		font-size:10px;
		font-family:verdana;
	}
	input{
		height:18px;
		font-size:10px;
		padding-top:0px;
		font-family:verdana;
	}
	select{
		height:18px;
		font-size:10px;
		padding-top:0px;
		font-family:verdana;
	}
</style>
<?php
	$no_po=sanitasi($_GET["no_po"]);
	$no_co=sanitasi($_GET["no_co"]);
	$sql="SELECT * FROM job_gelaran WHERE no_po='$no_po' AND no_co='$no_co'";
	$hsl=mysql_query($sql,$db);
	$rs=mysql_fetch_array($hsl);
	$no_po=$rs["no_po"];
	$kode_pabrik=$rs["pabrik"];
	$sql="SELECT nama FROM pabrik WHERE id='$kode_pabrik'";
	$hsltemp=mysql_query($sql,$db);
	list($nama_pabrik)=mysql_fetch_array($hsltemp);
	$pabrik="$nama_pabrik [$kode_pabrik]";
	$tanggal=$rs["updatedate"];
	$approved=$rs["approve"];
	$approveby=$rs["approveby"];
	
?>
		<table width="100%">
			<tr>
				<td><b>No Manufaktur</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $no_po; ?></b></td>
			</tr>
			<tr>
				<td><b>No CO</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $no_co; ?></b></td>
			</tr>
			<tr>
				<td><b>Pabrik</b></td>
				<td><b>:</b></td>
				<td><b><?php echo $pabrik; ?></b></td>
			</tr>
		</table>
		<table border="1" width="100%">
			<tr>
				<td><b>No</b></td>
				<td><b>Kode Produk</b></td>
				<td><b>Nama Produk</b></td>
				<td><b>Qty</b></td>
			</tr>			
			<?php
				$sql="SELECT kd_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' GROUP BY kd_produk";
				$hslprod=mysql_query($sql,$db);
				$no=0;
				while(list($kd_produk)=mysql_fetch_array($hslprod)){
					$sql="SELECT nama FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($nama)=mysql_fetch_array($hsltemp);
					$sql="SELECT qty_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' AND kd_produk='$kd_produk' GROUP BY kd_produk";
					$hsltemp=mysql_query($sql,$db);
					list($qtyprod)=mysql_fetch_array($hsltemp);
					$no++;
			?>
					<tr>
						<td><?php echo $no; ?></td>
						<td><?php echo $kd_produk; ?></td>
						<td><?php echo $nama; ?></td>
						<td align="right"><?php echo number_format($qtyprod); ?></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td colspan="3">
							<table width="100%" border="1">
								<tr>
									<td><b>Kode RM</b></td>
									<td><b>Nama RM</b></td>
									<td><b>Warna</b></td>
									<td><b>Supplier</b></td>
									<td><b>Cons</b></td>
									<td><b>Total Qty</b></td>
									<td><b>Satuan</b></td>
									<td><b>Kain Keluar</b></td>
									<!--td><b>Jml Gelaran</b></td-->
								</tr>
								<?php
									// $sql="SELECT produk_detail.kode_produk,produk_detail.kode_barang,barangdetail.nama,produk_detail.qty,satuan.nama,barangdetail.mode_gelar ";
									// $sql.="FROM produk_detail INNER JOIN barangdetail ON (produk_detail.kode_barang = barangdetail.id) INNER JOIN satuan ON (produk_detail.satuan = satuan.id)";
									// $sql.="WHERE produk_detail.kode_produk='$kd_produk'";
									$sql="SELECT kd_barang,supplier,satuan,qty,kainmasuk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co' AND kd_produk='$kd_produk' ORDER BY seqno";
									$hslbrg=mysql_query($sql,$db);
									while(list($koderm,$id_supplier,$satuan,$qtyrm,$kainmasuk)=mysql_fetch_array($hslbrg)){
										$sql="SELECT nama FROM barangdetail WHERE id='$koderm'";
										$hsltemp=mysql_query($sql,$db);
										list($namarm)=mysql_fetch_array($hsltemp);
										$totalqtyrm=$qtyrm*$qtyprod;
										$sql="SELECT warna FROM mst_warna WHERE kode IN (SELECT warna FROM barangdetail WHERE id='$koderm')";
										$hsltemp=mysql_query($sql,$db);
										list($warna)=mysql_fetch_array($hsltemp);
										$sql="SELECT nama FROM supplier WHERE id='$id_suplier'";
										$hsltemp=mysql_query($sql,$db);
										list($nama_supplier)=mysql_fetch_array($hsltemp);
										$supplier="$nama_supplier [$id_supplier]";
										if($koderm){
								?>
									<tr>
										<td><?php echo $koderm; ?></td>
										<td><?php echo $namarm; ?></td>
										<td><?php echo $warna; ?></td>
										<td><?php echo $supplier; ?></td>
										<td align="right"><?php echo number_format($qtyrm,3,",","."); ?></td>
										<td align="right"><?php echo number_format($totalqtyrm,2,",","."); ?></td>
										<td><?php echo $satuan; ?></td>
										<td align="right"><?php echo number_format($kainmasuk,2,",","."); ?></td>
										<!--td align="right"><?php echo number_format($jml_gelaran); ?></td-->
									</tr>
								<?php
										}
									}
								?>
							</table>
						</td>
					</tr>
			<?php
				}
			?>
		</table>