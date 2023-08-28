<?php
	include_once "connect.php";
	$content_title="PRODUK";
	$rowperpage=50;
?>
<?php include_once "header_window_content.php"; ?>
<script language="javascript">
	var detailsWindow;
	function showVendor(textid,txtname,txtaddr,mode)
	{
	   detailsWindow = window.open("window_vendor.php?textid="+textid+"&txtname="+txtname+"&txtaddr="+txtaddr+"&mode="+mode+"","vendor","width=400,height=600,top=0,scrollbars=yes");
	   detailsWindow.focus();   
	}
	function showparent(textid,textnama,textharga,id,nama,idukuran,ukuran,idsatuan,satuan,harga){
		window.opener.document.getElementById(textid).value=id;
		window.opener.document.getElementById(textnama).value=nama;
	//	window.opener.document.getElementById(textharga).value=harga;
	//	window.opener.document.getElementById(idukuran).innerHTML=ukuran;
	//	window.opener.document.getElementById(idsatuan).innerHTML=satuan;
		window.close();
	}
	function barcode_load() {
		if(document.getElementById("srcbasic").value!=""){basic=document.getElementById("srcbasic").value;}else{basic="%";}
		if(document.getElementById("srckategori").value!=""){kategori=document.getElementById("srckategori").value;}else{kategori="%";}
		if(document.getElementById("srckelas").value!=""){kelas=document.getElementById("srckelas").value;}else{kelas="%";}
		if(document.getElementById("srcstyle").value!=""){style=document.getElementById("srcstyle").value;}else{style="%%";}
		if(document.getElementById("srcmodel").value!=""){model=document.getElementById("srcmodel").value;}else{model="%%";}
		if(document.getElementById("srcsize").value!=""){size=document.getElementById("srcsize").value;}else{size="%%";}
		if(document.getElementById("srcsupplier").value!=""){supplier=document.getElementById("srcsupplier").value;}else{supplier="%%%";}
		if(document.getElementById("srcwarna").value!=""){warna=document.getElementById("srcwarna").value;}else{warna="%%%";}
		if(document.getElementById("srcgrade").value!="a" && document.getElementById("srcgrade").value!=""){supplier=supplier.substring(0,2)+basic;}
		document.getElementById("srckode").value=basic+kategori+kelas+style+model+size+supplier+warna;
	}
</script>
<?php
	if(sanitasi($_GET['delaxscdv'])){
		$barkode=sanitasi($_GET['delaxscdv']);
		$sql="DELETE FROM produk WHERE kode='$barkode'";
		mysql_query($sql,$db);
		if(mysql_affected_rows($db)>0){
			?>
				<script language="javascript">
					alert('Produk Berhasil Dihapus.');
					window.location='produk_list.php';
				</script>
			<?php
		}else{
			?>
				<script language="javascript">
					alert('Produk Gagal Dihapus!');
					window.location='produk_list.php';
				</script>
			<?php
		}
	}
	if($_POST["srckode"]==""){$_POST["srckode"]=$_SESSION["search"]["kode"];}
	if($_POST["srcbasic"]==""){$_POST["srcbasic"]=$_SESSION["search"]["kode_basic_item"];}
	if($_POST["srckategori"]==""){$_POST["srckategori"]=$_SESSION["search"]["kode_kategori"];}
	if($_POST["srckelas"]==""){$_POST["srckelas"]=$_SESSION["search"]["kode_kelas"];}
	if($_POST["srcstyle"]==""){$_POST["srcstyle"]=$_SESSION["search"]["kode_style"];}
	if($_POST["srcmodel"]==""){$_POST["srcmodel"]=$_SESSION["search"]["kode_model"];}
	if($_POST["srcsize"]==""){$_POST["srcsize"]=$_SESSION["search"]["kode_size"];}
	if($_POST["srcsupplier"]==""){$_POST["srcsupplier"]=$_SESSION["search"]["kode_supplier"];}
	if($_POST["srcwarna"]==""){$_POST["srcwarna"]=$_SESSION["search"]["kode_warna"];}
	if($_POST["srcgrade"]==""){$_POST["srcgrade"]=$_SESSION["search"]["kode_grade"];}
	if($_POST["srcnama"]==""){$_POST["srcnama"]=$_SESSION["search"]["nama"];}
	if(sanitasi($_POST["search"])){
		$_SESSION["search"]["kode"]=sanitasi($_POST["srckode"]);
		$_SESSION["search"]["barcode"]=sanitasi($_POST["srckode"]);
		$_SESSION["search"]["kode_basic_item"]=sanitasi($_POST["srcbasic"]);
		$_SESSION["search"]["kode_kategori"]=sanitasi($_POST["srckategori"]);
		$_SESSION["search"]["kode_kelas"]=sanitasi($_POST["srckelas"]);
		$_SESSION["search"]["kode_style"]=sanitasi($_POST["srcstyle"]);
		$_SESSION["search"]["kode_model"]=sanitasi($_POST["srcmodel"]);
		$_SESSION["search"]["kode_size"]=sanitasi($_POST["srcsize"]);
		$_SESSION["search"]["kode_supplier"]=sanitasi($_POST["srcsupplier"]);
		$_SESSION["search"]["kode_warna"]=sanitasi($_POST["srcwarna"]);
		$_SESSION["search"]["kode_grade"]=sanitasi($_POST["srcgrade"]);
		$_SESSION["search"]["nama"]=sanitasi($_POST["srcnama"]);
		$_SESSION["search"]["issessioned"]=1;
	}
	if(sanitasi($_POST["reset"])){$_POST=array();$_SESSION["search"]=array();}
	$kode=sanitasi($_POST["srckode"]);
	$barcode=sanitasi($_POST["srckode"]);
	$kode_basic_item=sanitasi($_POST["srcbasic"]);
	$kode_kategori=sanitasi($_POST["srckategori"]);
	$kode_kelas=sanitasi($_POST["srckelas"]);
	$kode_style=sanitasi($_POST["srcstyle"]);
	$kode_model=sanitasi($_POST["srcmodel"]);
	$kode_size=sanitasi($_POST["srcsize"]);
	$kode_supplier=sanitasi($_POST["srcsupplier"]);
	$kode_warna=sanitasi($_POST["srcwarna"]);
	$kode_grade=sanitasi($_POST["srcgrade"]);
	$nama=sanitasi($_POST["srcnama"]);
?>
	<fieldset style="width:2%;">
		<legend>Cari:</legend>
		<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>?textid=<?php echo sanitasi($_GET["textid"]);?>&textnama=<?php echo sanitasi($_GET["textnama"]);?>&textharga=<?php echo sanitasi($_GET["textharga"]);?>&kodeproduk=<?php echo sanitasi($_GET["kodeproduk"]);?>&idukuran=<?php echo sanitasi($_GET["idukuran"]);?>&idsatuan=<?php echo sanitasi($_GET["idsatuan"]);?>&no_do=<?php echo sanitasi($_GET["no_do"]);?>">
			<table>
				<tr>
					<td valign="top">
						<table>
							<tr>
								<td nowrap><b>Barcode</b></td>
								<td><b>:</b></td>
								<td><input type="text" name="srckode" id="srckode" value="<?php echo sanitasi($_POST["srckode"]); ?>" size="25"></td>
							</tr>
							<tr>
								<td nowrap><b>Basic Item</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcbasic" id="srcbasic" onchange="barcode_load();submit();">
										<option value="">-Basic Item-</option>
										<?php
											$sql="SELECT kode,item FROM mst_basic_item";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srcbasic"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Kategori</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srckategori" id="srckategori" onchange="barcode_load();submit();">
										<option value="">-Kategori-</option>
										<?php
											$sql="SELECT kode,kategori FROM mst_kategori WHERE kode_basic_item='$kode_basic_item'";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srckategori"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>							
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Kelas</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srckelas" id="srckelas" onchange="barcode_load();submit();">
										<option value="">-Kelas-</option>
										<?php
											$sql="SELECT kode,kelas FROM mst_kelas WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori'";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srckelas"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>							
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Style</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcstyle" id="srcstyle" onchange="barcode_load();submit();">
										<option value="">-Style-</option>
										<?php
											$sql="SELECT kode,style FROM mst_style WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas'";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srcstyle"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>							
									</select>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td nowrap><b>Model</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcmodel" id="srcmodel" onchange="barcode_load();submit();">
										<option value="">-Model-</option>
										<?php
											//$sql="SELECT kode,model FROM mst_model";
											$sql="SELECT kode,model FROM mst_model WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srcmodel"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Size</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcsize" id="srcsize" onchange="barcode_load();submit();">
										<option value="">-Size-</option>
										<?php
											$sql="SELECT kode,size FROM mst_size";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srcsize"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>
									</select>
								</td>
							</tr>
							<!--tr>
								<td nowrap><b>Supplier</b></td>
								<td><b>:</b></td>
								<td><input id="srcsupplier" type="text" readonly name="srcsupplier" value="<?php echo sanitasi($_POST["srcsupplier"]); ?>" onclick="return showVendor(this.id,'','','1')" size="12" onblur="barcode_load();submit();"></td>
							</tr-->
							<tr>
								<td nowrap><b>Warna</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcwarna" id="srcwarna" onchange="barcode_load();submit();">
										<option value="">-Warna-</option>
										<?php
											$sql="SELECT kode,warna FROM mst_warna WHERE LENGTH(kode)=3 ORDER BY warna";
											$hsl=mysql_query($sql,$db);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_POST["srcwarna"])){echo "selected";} ?>><?php echo $item." [$kode]"; ?></option>
										<?php
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Grade</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcgrade" id="srcgrade" onchange="barcode_load();submit();">
												<option value="" <?php if(""==sanitasi($_POST["srcgrade"])){echo "selected";} ?>>-Grade-</option>
										<option value="a" <?php if("a"==sanitasi($_POST["srcgrade"])){echo "selected";} ?>>Grade A</option>
										<option value="b" <?php if("b"==sanitasi($_POST["srcgrade"])){echo "selected";} ?>>Grade B</option>
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Nama Produk</b></td>
								<td><b>:</b></td>
								<?php
									if(!sanitasi($_POST["srcnama"])){
										$sql="SELECT nama FROM produk WHERE kode LIKE '$barcode' AND kode_basic_item LIKE '$kode_basic_item' AND kode_kategori LIKE '$kode_kategori'";
										$sql.=" AND kode_kelas LIKE '$kode_kelas' AND kode_style LIKE '$kode_style' AND kode_model LIKE '$kode_model' AND kode_size LIKE '$kode_size' AND kode_supplier LIKE '$kode_supplier'";
										$sql.=" AND kode_warna LIKE '$kode_warna' AND grade LIKE '%$kode_grade'";
										$hsl=mysql_query($sql,$db);
										list($_POST["srcnama"])=mysql_fetch_array($hsl);
									}
								?>
								<td><input type="text" name="srcnama" id="srcnama" value="<?php echo sanitasi($_POST["srcnama"]); ?>"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="search" value="Cari">
						<input type="submit" name="reset" value="Reset">
					</td>
				</tr>
				<?php
					if(sanitasi($_POST["prev"])){
						if(sanitasi($_POST["startrow"])-$rowperpage<=0){$_POST["startrow"]=0;}else{$_POST["startrow"]=sanitasi($_POST["startrow"])-$rowperpage;}
					}
					if(sanitasi($_POST["next"])){
						$_POST["startrow"]=sanitasi($_POST["startrow"])+$rowperpage;
					}
					if(sanitasi($_POST["startrow"])){$startrow=sanitasi($_POST["startrow"]);}else{$startrow=0;}
				?>
				<tr>
					<td colspan="2" align="center">
						<input type="submit" name="prev" value="Prev">
						<input type="submit" name="next" value="Next">
					</td>
				</tr>
				<input type="hidden" name="startrow" value="<?php echo sanitasi($_POST["startrow"]); ?>">
			</table>
		</form>
	</fieldset>
	<table border="1">
		<tr>
			<td><b>No</b></td>
			<td><b>Kode(BarCode)</b></td>
			<td><b>Grade</b></td>
			<td><b>Nama</b></td>
			<td><b>Basic Item</b></td>
			<td><b>Kategori</b></td>
			<td><b>Kelas</b></td>
			<td><b>Style</b></td>
			<td><b>Model</b></td>
			<td><b>Size</b></td>
			<td><b>Supplier</b></td>
			<td><b>Warna</b></td>
			<td><b>Satuan</b></td>
			<td><b>Stok</b></td>
			<td><b>Harga Jual</b></td>
		</tr>
		<?php
			$no_do=sanitasi($_GET["no_do"]);
			if ($no_do){
				$sql="SELECT * FROM produk WHERE kode LIKE '%$barcode%' AND kode_basic_item LIKE '%$kode_basic_item' AND kode_kategori LIKE '%$kode_kategori'";
				$sql.=" AND kode_kelas LIKE '%$kode_kelas' AND kode_style LIKE '%$kode_style' AND kode_model LIKE '%$kode_model' AND kode_size LIKE '%$kode_size' AND kode_supplier LIKE '%$kode_supplier'";
				$sql.=" AND kode_warna LIKE '%$kode_warna' AND nama LIKE '%$nama%' AND grade LIKE '%$kode_grade' AND kode IN (SELECT kd_produk FROM do_produk_markas_detail  WHERE no_do='$no_do') order by updatedate DESC LIMIT $startrow,50";
			}else{
				$sql="SELECT * FROM produk WHERE kode LIKE '%$barcode%' AND kode_basic_item LIKE '%$kode_basic_item' AND kode_kategori LIKE '%$kode_kategori'";
				$sql.=" AND kode_kelas LIKE '%$kode_kelas' AND kode_style LIKE '%$kode_style' AND kode_model LIKE '%$kode_model' AND kode_size LIKE '%$kode_size' AND kode_supplier LIKE '%$kode_supplier'";
				$sql.=" AND kode_warna LIKE '%$kode_warna' AND nama LIKE '%$nama%' AND grade LIKE '%$kode_grade' order by updatedate DESC LIMIT $startrow,50";
			}
			//echo $sql;
			$xhsl=mysql_query($sql,$db);
			$no=$startrow;
			while($rs=mysql_fetch_array($xhsl)){
				$no++;
				$kode=$rs["kode"];
				$grade=strtoupper($rs["grade"]);
				$kode_basic_item=$rs["kode_basic_item"];
				$kode_kategori=$rs["kode_kategori"];
				$kode_kelas=$rs["kode_kelas"];
				$kode_style=$rs["kode_style"];
				$kode_model=$rs["kode_model"];
				$kode_size=$rs["kode_size"];
				$kode_supplier=$rs["kode_supplier"];
				$kode_warna=$rs["kode_warna"];
				$nama=$rs["nama"];
				$startqty=$rs["startqty"];
				$satuan=$rs["satuan"];
			//	$hargadasar=$rs["hargadasar"];

				$hargajual=$rs["hargajual"];
				$sql="SELECT item FROM mst_basic_item WHERE kode='$kode_basic_item'";
				$hsl=mysql_query($sql,$db);
				list($basic)=mysql_fetch_array($hsl);
				$sql="SELECT kategori FROM mst_kategori WHERE kode_basic_item='$kode_basic_item' AND kode='$kode_kategori'";
				$hsl=mysql_query($sql,$db);
				list($kategori)=mysql_fetch_array($hsl);
				$sql="SELECT kelas FROM mst_kelas WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode='$kode_kelas'";
				$hsl=mysql_query($sql,$db);
				list($kelas)=mysql_fetch_array($hsl);
				$sql="SELECT style FROM mst_style WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode='$kode_style'";
				$hsl=mysql_query($sql,$db);
				list($style)=mysql_fetch_array($hsl);
				$sql="SELECT model FROM mst_model WHERE kode='$kode_model' AND kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
				$hsl=mysql_query($sql,$db);
				list($model)=mysql_fetch_array($hsl);
				// echo "Model $kode_model - $kode_basic_item - $kode_kategori - $kode_kelas - $kode_style [$model]";
				$sql="SELECT size FROM mst_size WHERE kode='$kode_size'";
				$hsl=mysql_query($sql,$db);
				list($size)=mysql_fetch_array($hsl);
				$sql="SELECT nama FROM supplier WHERE id='$kode_supplier'";
				$hsl=mysql_query($sql,$db);
				list($supplier)=mysql_fetch_array($hsl);
		
				$sql="SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
				$hsl=mysql_query($sql,$db);
				list($warna)=mysql_fetch_array($hsl);
				$sql="SELECT nama FROM satuan WHERE id='$satuan'";
				$hsl=mysql_query($sql,$db);
				list($satuan)=mysql_fetch_array($hsl);
				if($_GET['idukuran']){$idukuran=$_GET['idukuran'];}else{$idukuran="";}
				if($_GET['idsatuan']){$idsatuan=$_GET['idsatuan'];}else{$idsatuan="";}
				
				$tomorrow=date("Y-m-d",mktime(0,0,0,date("m"),date("d")+1,date("Y")));
				//cari qty penambah
				$sql="SELECT sum(qty) FROM produk_stok_control WHERE kd_produk='$kode' AND tanggal<'$tomorrow' AND tujuan='DIST'";
				//echo "<br>$sql";
				$hsltemp=mysql_query($sql,$db);
				list($qtymasuk)=mysql_fetch_array($hsltemp);
				//cari qty pengurang
				$sql="SELECT sum(qty) FROM produk_stok_control WHERE kd_produk='$kode' AND tanggal<'$tomorrow' AND dari='DIST'";
				$hsltemp=mysql_query($sql,$db);
				list($qtykeluar)=mysql_fetch_array($hsltemp);
				$stok=$qtymasuk-$qtykeluar;
				// echo "Model : $kode_basic_item - $kode_kategori - $kode_kelas - $kode_style - $kode_model";
			?>
				<tr>
					<td valign="top">
						<a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>');">
							<?php echo $no; ?>
						</a>
					</td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $kode; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $grade; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $nama; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $basic; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $kategori; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $kelas; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $style; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $model; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $size; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $supplier; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $warna; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $satuan; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $stok; ?></a></td>
					<td valign="top"><a onclick="showparent('<?php echo $_GET['textid']; ?>','<?php echo $_GET['textnama']; ?>','<?php echo $_GET['textharga']; ?>','<?php echo $kode; ?>','<?php echo $nama; ?>','<?php echo $idukuran; ?>','<?php echo $size; ?>','<?php echo $idsatuan; ?>','<?php echo $satuan; ?>','<?php echo $hargajual; ?>');"><?php echo $hargajual; ?></a></td>
				</tr>
		<?php
			}
		?>
	</table>
	<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>?textid=<?php echo sanitasi($_GET["textid"]);?>&textnama=<?php echo sanitasi($_GET["textnama"]);?>&kodeproduk=<?php echo sanitasi($_GET["kodeproduk"]);?>&idukuran=<?php echo sanitasi($_GET["idukuran"]);?>&idsatuan=<?php echo sanitasi($_GET["idsatuan"]);?>&manufaktur=<?php echo sanitasi($_GET["manufaktur"]);?>">
		<table>
			<tr>
				<td colspan="2" align="center">
					<input type="submit" name="prev" value="Prev">
					<input type="submit" name="next" value="Next">
				</td>
			</tr>
		</table>
	</form>
<?php include_once "footer_window_content.php"; ?>
