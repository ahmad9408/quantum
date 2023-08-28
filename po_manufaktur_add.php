<?php $content_title="PERMINTAAN  MANUFAKTUR"; include_once "header.php" ?>
<?php include_once "clsaddrow.php";?>
<?php include_once "po_manufaktur_approving_init.php";?>

<?php

	$tglpo=sanitasi($_POST["tglpo"]);
	if(!$tglpo){$tglpo=date("Y-m-d");}
	
	$no_manufaktur=no_po_rm();
	if (sanitasi($_POST['reset'])){
		$_POST=array();
	}
	if (sanitasi($_POST['simpan'])){
		// echo "<pre>";
			// print_r($_POST);
		// echo "</pre>";
		
		// Array
		// (
		    // [tglpo] => 2009/04/26
		    // [tglanalisa1] => 2009/04/26
		    // [tglanalisa2] => 2009/04/26
		    // [deldate] => 2009/04/26
		    // [keterangan] => keterangan
		    // [novendor] => 001
		    // [vendorname] => PT. SUHO GARMINDO
		    // [vendoraddr] => BANDUNG
		    // [kdproduk] => Array
		        // (
		            // [0] => BAA010001001001
		            // [1] => BBA110102001002
		            // [2] => BBA120101001001
		        // )

		    // [qty] => Array
		        // (
		            // [0] => 200
		            // [1] => 500
		            // [2] => 700
		        // )

		    // [simpan] => Simpan
		// )
		$affected=0;
		$totalqty=0;
		$totalrp=0;
		$arrpo=array();
		$no=-1;
		foreach($_POST["kdproduk"] as $seqno => $barcode){
			if($barcode){
				$no++;
				$sql="SELECT kode_supplier,hargajual FROM produk WHERE kode='$barcode'";
				$hsl=mysql_query($sql,$db);
				list($kode_supplier,$hargajual)=mysql_fetch_array($hsl);
				$qty=sanitasi($_POST["qty"][$seqno]);
				$jumlah=$hargajual*$qty;
				$totalqty+=$qty;
				$totalrp+=$jumlah;
				$sql="INSERT INTO po_manufaktur_detail (no_manufaktur,seqno,kd_produk,qty,hargajual,jumlah) VALUES ('$no_manufaktur','$no','$barcode','$qty','$hargajual','$jumlah')";
				//echo $sql."<br>";
				mysql_query($sql,$db);
				if(mysql_affected_rows($db)>0){$affected++;}
				$arrpo[$kode_supplier]["kode_produk"][$seqno]=$barcode;
				$arrpo[$kode_supplier]["kd_var"][$seqno]=$kd_var;
				$arrpo[$kode_supplier]["hargajual"][$seqno]=$hargajual;
				$arrpo[$kode_supplier]["qty"][$seqno]=$qty;
				$arrpo[$kode_supplier]["jumlah"][$seqno]=$jumlah;
			}
		}
		// echo "<pre>";
			// print_r($arrpo);
		// echo "</pre>";
		$jumlahrow=count($_POST["kdproduk"]);
		if($affected==$jumlahrow){//semua detil berhasil di insert
			$tanggal=sanitasi($_POST["tglpo"])." ".date("H:i:s");
			$no_vendor=sanitasi($_POST["novendor"]);
			$keterangan=sanitasi($_POST["keterangan"]);
			$approveby=$_SESSION["username"];
			$sql="INSERT INTO po_manufaktur (no_manufaktur,tanggal,no_vendor,keterangan,totalqty,totalrp,approve,approveby,approvedate) ";
			$sql.="VALUES ('$no_manufaktur','$tanggal','$no_vendor','$keterangan','$totalqty','$totalrp','1','$approveby',NOW())";
			mysql_query($sql,$db);
			//echo $sql."<br><br>";
			if(mysql_affected_rows($db)>0){				
				?>
					<script language="javascript">
						alert("Permintaan Manufaktur Telah Disimpan.");
						window.location="manufaktur_list.php";
					</script>
				<?php
			}else{
				$sql="DELETE FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
				mysql_query($sql,$db);
				$errmessage="Permintaan Manufaktur Gagal Tersimpan!";				
			}
		}else{//tidak semua detil berhasil di insert
			$sql="DELETE FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
			mysql_query($sql,$db);
			$errmessage="Permintaan Manufaktur Gagal Tersimpan!Silakan ulangi lagi!";
		}
		echo "<font color='red'>$errmessage</font>";
	}
?>
	<script language="JavaScript">
		var detailsWindow;
		function showCalendar(textid)
		{
		   detailsWindow = window.open("calendar.php?textid="+textid+"","calendar","width=260,height=250,top=300,scrollbars=yes");
		   detailsWindow.focus();   
		}
		function showVendor(textid,txtname,txtaddr,mode)
		{
		   detailsWindow = window.open("window_vendor.php?textid="+textid+"&txtname="+txtname+"&txtaddr="+txtaddr+"&mode="+mode+"","vendor","width=400,height=600,top=0,scrollbars=yes");
		   detailsWindow.focus();   
		}
		function showProduk(textid,textnama,kodeproduk,idukuran,idsatuan) {
			detailsWindow = window.open("window_produk_manufaktur.php?textid="+textid+"&textnama="+textnama+"&kodeproduk="+kodeproduk+"&idukuran="+idukuran+"&idsatuan="+idsatuan,"window_produk","width=800,height=600,scrollbars=yes");
			detailsWindow.focus();   
		}
	</script>
	<form method="POST" name="outlet" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
		<table>
			<tr>
				<td valign="top">
					<table>
						<tr>
							<td><b>Tanggal Manufaktur</b></td>
							<td><b>:</b></td>
							<td>
						<div align="left">
            <script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
            <!-- calendar attaches to existing form element -->
            <input type="text" name="tglpo" readonly id="tglpo" value="<?php echo $tglpo; ?>" size="16"/>
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tglpo'
              });
            </script></td>
            			</tr>
						<tr>
							<td><b>Nomor Manufaktur</b></td>
							<td><b>:</b></td>
							<td><b><?php echo $no_manufaktur;?></b></td>
						</tr>
						<tr>
							<td valign="top"><b>Keterangan</b></td>
							<td valign="top"><b>:</b></td>
							<td valign="top"><textarea name="keterangan" rows="3" cols="30"></textarea></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table border="1">
			<tr>
				<td nowrap>
					<b>No</b>
					<a href="#" onClick="addRow(' ',0,0,'tbltrx','bodytrx');" style="text-decoration:none">
					<img src='images/add.png' alt='add' align='middle' border='0' height='16' hspace='0' width='16'></a>
					<a href="#" onClick="addRow(' ',1,0,'tbltrx','bodytrx');" style="text-decoration:none">
					<img src='images/remove.png' alt='inc' align='middle' border='0' height='16' hspace='0' width='16'></a>
				</td>
				<td><b>Kode Produk</b></td>
				<td><b>Nama Produk</b></td>
				<td><b>Ukuran</b></td>
				<td><b>Qty</b></td>
				<td><b>Satuan</b></td>
			</tr>
			<tbody id="tbltrx0">
				<tr id="bodytrx0">
					<td nowrap id="nomor0" align="right">1</td>
					<td nowrap>
						<input type="text" 
							name="kdproduk[0]" 
							id="idkdproduk[0]" 
							onclick="showProduk(this.id,'id_produk[0]',this.value,'id_ukuran[0]','id_satuan[0]');">
					</td>
					<td><input id="id_produk[0]" type="text" readonly></td>
					<td id="id_ukuran[0]">&nbsp;</td>
					<td><input type="text" size="3" name="qty[0]"></td>
					<td id="id_satuan[0]">&nbsp;</td>
				</tr>
			</tbody>
		</table>
		<table width="100%">
			<tr>
				<td align="center"><input type="submit" name="simpan" value="Simpan"><input type="submit" name="reset" value="Reset"></td>
			</tr>
		</table>
	</form>
<?php include_once "footer.php" ?>
