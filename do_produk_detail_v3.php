<?php 
	//$content_title="DETAIL RECEIVING GUDANG DISTRIBUSI"; 
	include_once "header.php" ?>
<?php include_once "clsaddrow.php";?>
<?php

/*
Cek untuk retur 
batasin jangan sampai retur out membuat minus
22 12 2014 (budi)

v3 bse on do_produk tanpa release

*/

if($_POST[retur]){
	$sql="select sum(qty) from do_produk_detail where no_do='".$_GET[no_do]."' and kd_produk='".$_GET[kode]."'";

	$query=mysql_query($sql)or die($sql);
	list($jmlqty)=mysql_fetch_array($query);
    
    $sql="select trim(keterangan),trim(gudang) from do_produk where no_do='$_GET[no_do]'";
    $query=mysql_query($sql);
    list($gud,$gd)=mysql_fetch_array($query);
    
	
	if($jmlqty<$_POST[jml_ret]){
		echo"<script>alert('Maaf stok anda tidak valid');
		history.back();</script>";exit();
	}
	
	//CEk jumlah Stok enable tgl 22 12 2014
	$sql="select SQL_CACHE stok from produk_stok where kode_produk='".$_GET[kode]."' and kode_gudang= '$gd'";
	$query=mysql_query($sql)or die($sql);
	list($stokawal)=mysql_fetch_array($query);
	
	if(empty($stokawal)){$stokawal=0;}
	if($stokawal<$_POST[jml_ret]){
		echo"<script>alert('Stok di distribusi tidak mencukupi untuk di retur.Nilai Stok $stokawal ,  silahkan lakukan stok opname melalui pos');
		history.back();</script>";exit();
	}
	
	
	
	
	/*
	if(substr($_GET[no_do],0,5)=='P100S')
	{
		$gd="GD.0S";
	}else if(substr($_GET[no_do],0,5)=='P1001')
	{
		$gd="GD.002";
	}else{
		$gd="GD.001";
	}
	*/
	/*$sql="select stok from produk_stok where kode_produk='".$_GET[kode]."' and kode_gudang like '$gd%'";
	

	$query=mysql_query($sql)or die($sql);
	*/
	
	$sql="select hpp,harga from do_produk_detail where seqno='".$_GET[sq]."' and kd_produk='".$_GET[kode]."'";
	$query=mysql_query($sql)or die($sql);
	list($hpp,$hpj)=mysql_fetch_array($query);
	$amounthpp=$hpp*$_POST[jml_ret];
	$amounthpj=$hpj*$_POST[jml_ret];
	$sql="insert into retur_distribusi_rian(no_do,kd_produk,seqno,qty,hpp,hpj,amount_hpp,amount_hpj,tanggal,approve1,approve1by,keterangan)values
	('$_GET[no_do]','$_GET[kode]','$_GET[sq]','$_POST[jml_ret]','$hpp','$hpj','$amounthpp','$amounthpj',NOW(),'1','$_SESSION[username]','$_POST[alasan]') ";
	$query=mysql_query($sql)or die($sql);
	
	$sql="select SQL_CACHE stok from produk_stok where kode_produk='".$_GET[kode]."' and kode_gudang like '$gd%'";
	$query=mysql_query($sql)or die($sql);
	list($stokawal)=mysql_fetch_array($query);
	$sisa=$stokawal-$_POST[jml_ret];
    
    $sql="update produk_stok set stok=stok+$_POST[jml_ret] where kode_produk='".$_GET[kode]."'  and kode_gudang='$gd'";

    $query=mysql_query($sql);
	
	$sql="update produk_stok set stok='$sisa'  where kode_produk='".$_GET[kode]."' and kode_gudang like '$gd%'";
	$query=mysql_query($sql)or die($sql);
	
 	$sql="select stokakhir,hpj from produk_stok_card where barcode='".$_GET[kode]."' and gudang like '$gd%' order by ID desc limit 0,1";
	$query=mysql_query($sql)or die($sql);
	list($stokakhir,$hpj)=mysql_fetch_array($query);
	
	$stokakhir1=$stokakhir-$_POST[jml_ret];
	$sql="select max(ID)+1 from produk_stok_card ";
	$query=mysql_query($sql)or die($sql);
	list($ID)=mysql_fetch_array($query);
	
	/*$sql="insert into produk_stok_card (no_do,gudang,barcode,stokawal,returout,stokakhir,hpj,ID,updateby,updatedate)values
	('$_GET[no_do]-retur','$gd%','$_GET[kode]','$stokakhir','$_POST[jml_ret]','$stokakhir1','$hpj','$ID','$_SESSION[username]',NOW())";
	$query=mysql_query($sql)or die($sql);
	*/
	
	
	echo"<script>alert('barang telah di kembalikan');document.location=\"retur_distribusi.php?no_do=$_GET[no_do]&lagi\";</script>";
}
if(isset($_GET[retur])){
	$ret=true;
}else{
	$ret=false;
}
	$no_do=sanitasi($_GET["no_do"]);
	$sql="SELECT * FROM do_produk WHERE no_do='$no_do'";
	$hsl=mysql_query($sql,$db);
	$rs=mysql_fetch_array($hsl);
	$no_fin=$rs["no_fin"];
	$no_po=$rs["no_po"];
	/* Edit By Goberan */
	// $id_pabrik =$rs["pabrik"];
	$id_pabrik=substr($no_do,0,5);
	
	
	
	$id_gudang=$rs["gudang"];
	$sql="SELECT nama,cek_qtydo_app2 FROM gudang_distribusi WHERE id='$id_gudang'";
	$hsl=mysql_query($sql,$db);
	list($nama_gudang,$cek_qtydo_app2)=mysql_fetch_array($hsl);
	$sql="SELECT no_qc FROM job_fin WHERE no_fin='$no_fin'";
	$hsl=mysql_query($sql,$db);
	list($no_qc)=mysql_fetch_array($hsl);
	$sql="SELECT no_sew FROM job_qc WHERE no_qc='$no_qc'";
	$hsltemp=mysql_query($sql,$db);
	list($no_sew)=mysql_fetch_array($hsltemp);
	$sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
	$hsltemp=mysql_query($sql,$db);
	list($no_load)=mysql_fetch_array($hsltemp);
	$sql="SELECT no_co FROM job_loading WHERE no_load='$no_load'";
	$hsltemp=mysql_query($sql,$db);
	list($no_co)=mysql_fetch_array($hsltemp);
	$sql="SELECT no_jo FROM job_cutting WHERE no_co='$no_co'";
	$hsltemp=mysql_query($sql,$db);
	list($no_jo)=mysql_fetch_array($hsltemp);
	$tanggal=$rs["tanggal"];
	$keterangan=$rs["keterangan"];
	$sql="SELECT nama FROM pabrik WHERE id='$keterangan'";
	
	$hsl=mysql_query($sql,$db);
	list($nama_pabrik)=mysql_fetch_array($hsl);
	$totalqty=$rs["totalqty"];
	$totalrp=$rs["totalrp"];
	$approved=$rs["approve"];
	$approved2=$rs["approve2"];
	$approveby=$rs["approveby"];
	$approveby2=$rs["approveby2"];
	$sql="SELECT no_sj FROM sj_produk_manufaktur WHERE no_do='$no_do'";
	$hsltemp=mysql_query($sql,$db);
	list($no_sj)=mysql_fetch_array($hsltemp);
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
	</script>
		<table>
			<tr>
				<td valign="top">
					<table>
						<tr>
							<td><b>No DO</b></td>
							<td><b>:</b></td>
							<td><?php echo $no_do;?></td>
						</tr>
						<tr>
							<td><b>Tanggal</b></td>
							<td><b>:</b></td>
							<td><?php echo $tanggal;?></td>
						</tr>
						<tr>
							<td><b>Pabrik Asal</b></td>
							<td><b>:</b></td>
							<td><?php echo $nama_pabrik;?></td>
						</tr>
						<tr>
							<td><b>Gudang Distribusi</b></td>
							<td><b>:</b></td>
							<td><?php echo $nama_gudang;?></td>
						</tr>
						<tr>
							<td valign="top"><b>Kode Pabrik Pengirim</b></td>
							<td valign="top"><b>:</b></td>
							<?php
								if(!$approved || $approved2){
							?>
							<td valign="top"><?php echo $keterangan; ?></td>
							<?php
								}else{
							?>
							<td><?php echo $keterangan; ?></td>
							<?php
								}
							?>
						</tr>
						<tr>
							<td><b>Total Qty</b></td>
							<td><b>:</b></td>
							<td><?php echo number_format($totalqty,2,",","."); ?></td>
						</tr>
						<tr>
							<td><b>Jumlah (Rp.)</b></td>
							<td><b>:</b></td>
							<td><?php echo number_format($totalrp,2,",","."); ?></td>
						</tr>
					</table>
					<?php if(isset($_GET[kode])){?>
					<br /><form name="f1" method="post" action="<?php echo $PHP_SELF?>">
					
						<table border="0">
						<tr>
							<td><strong>Jumlah Retur untuk kode <blink><font color="#FF0000"><?php echo $_GET[kode]?></font></blink></strong></td>
							<td>:</td>
							<td> <input type="text" name="jml_ret" size="10" value="<?php echo $_GET[q]?>" /></td>
						</tr>
						<tr>
							<td><strong>Alasan Retur</strong></td>
							<td>:</td>
							<td><textarea name="alasan"></textarea></td>
						</tr>
						<tr>
							<td></td>
							<td></td>
							<td><input type="submit" value="Retur" name="retur" /></td>
						</tr>
						
					</table>
					</form>
					<?php }?>
				</td>
			</tr>
		</table>
		
   <script type="text/javascript" src="app_libs/do_produk_detail_v3.js"></script>    
    <style>
    .qtycek:focus {border:2px solid #fff; background-color:#0b7130; color:#fff;}
	.txtfollowup:focus{border:2px solid #fff; background-color:#0b7130; color:#fff;}
	.txtmasalah:focus{border:2px solid #fff; background-color:#0b7130; color:#fff;}
    </style>    
	<form method="POST" action="do_produk_approving3.php?no_do=<?php echo $no_do; ?>&rnd=<?php  echo date('YmdHis'); ?>" onsubmit="return validationEvent()" id="myForm">
		<table border="1" width="1100" cellspacing="0" cellpadding="0" bordercolor="#FFFFFF" style="font-size: 10pt">
			<tr>
				<td bgcolor="#99CC00" height="31" width="20"  align="center"><b>No</b></td>
				<td bgcolor="#99CC00" height="31" width="50" align="center"><b>Poly Bag</b></td>
                <td bgcolor="#99CC00" height="31" width="15" align="center"><b>Kode Produk</b></td>
				<td bgcolor="#99CC00" height="31" width="230"  align="center"><b>Nama</b></td>
				<td bgcolor="#99CC00" height="31" width="60" align="center"><b>Warna</b></td>
				<td bgcolor="#99CC00" height="31" width="30" align="center"><b>Size</b></td>
				<td bgcolor="#99CC00" height="31" width="50" align="center"><b>Qty DO</b></td>
				<td bgcolor="#99CC00" width="50" align="center" class="colqty">Qty Cek</td>
				<td bgcolor="#99CC00" height="31" width="10" align="center"><b>Satuan</b></td>
                <td bgcolor="#99CC00" height="31" width="70" align="center"><b>Harga Dasar</b></td>
				<td bgcolor="#99CC00" height="31" width="70" align="center"><b>Harga Jual</b></td>
				<td bgcolor="#99CC00" height="31" width="100" align="center"><b>Jumlah DO HPP</b></td> 
				<td bgcolor="#99CC00" height="31" width="100" align="center"><b>Jumlah DO HPJ</b></td>
				<?php if($ret){?>
				<td bgcolor="#99CC00" height="31" width="100" align="center"><b>Aksi</b></td>
				
				<?php }?>
                <td bgcolor="#99CC00" width="100" align="center" class="colmasalah">Permasalahan</td>
				<td bgcolor="#99CC00" width="100" align="center" class="colfu">Follow Up</td> 
			</tr>
			<?php
				// $sql="SELECT * FROM do_produk_detail  WHERE no_do='$no_do' ORDER BY seqno ";
				$sql="SELECT `no_do`,`no_po`,`seqno`,trim(`kd_produk`) as kd_produk,`kd_var`,`harga`,`qty`,`qty_a`,`qty_b`,`jumlah`,`polybag`,`barcode_lama`,hpp,seqno,hpp FROM do_produk_detail  WHERE  no_do='$no_do' ORDER BY polybag";
				
				$sql="SELECT dd.`no_do`,dd.`no_po`,dd.`seqno`,TRIM(dd.`kd_produk`) AS kd_produk,dd.`kd_var`,dd.`harga`,dd.`qty`,
dd.`qty_a`,dd.`qty_b`,dd.`jumlah`,dd.`polybag`,dd.`barcode_lama`,dd.hpp,dd.seqno,dd.hpp 
,ddc.qty AS qty_cek,ddm.masalah,ddm.followup as fu
FROM do_produk_detail dd LEFT JOIN `do_produk_detail_cek` AS `ddc`
ON (`ddc`.`no_do` = `dd`.`no_do`) AND (`ddc`.`kd_produk` = `dd`.`kd_produk`) AND (`ddc`.`polibag` = `dd`.`polybag`) 
LEFT JOIN `do_produk_detail_masalah` AS `ddm` 
ON (`ddm`.`no_do` = `dd`.`no_do`) AND (`ddm`.`kd_produk` = `dd`.`kd_produk`) AND (`ddm`.`polibag` = `dd`.`polybag`)
WHERE dd.no_do='$no_do' ORDER BY dd.polybag";
				
				if($username=='budi-it'){
				   echo " $sql </br>";	
				}
                $hsl=mysql_query($sql,$db);
				$no=0;
				$totalhargapo=0;
				$totalhargado=0;
				$totalqtypo=0;
				$totalqtydo=0;
				$totalpo=0;
				$totaldo=0;
				$total_qty_a=0;
				$total_qty_b=0;
				$isInvalid=0;
				while($rs=mysql_fetch_array($hsl)){
					$no++;
					$no_po=$rs["no_po"];
					$kd_produk=trim($rs["kd_produk"]);
					$sql="SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($namaproduk,$kode_warna,$kodesize,$satuan)=mysql_fetch_array($hsltemp);
					// tambahan Extreme
					if(empty($namaproduk)){
                                       $kd_lama=trim($rs["barcode_lama"]);
                                       $sql="SELECT nama,kode_warna,kode_size,satuan FROM produk WHERE kode_grade_a='$kd_lama'";
					    $hsltemp=mysql_query($sql,$db);
					    list($namaproduk,$kode_warna,$kodesize,$satuan)=mysql_fetch_array($hsltemp);
					    //echo $sql;
                                    
					}
					//Tambahan Extreme
					$sql="SELECT nama FROM satuan WHERE id='$satuan'";
					$hsltemp=mysql_query($sql,$db);
					list($satuan)=mysql_fetch_array($hsltemp);
					$sql="SELECT size FROM mst_size WHERE kode='$kodesize'";
					$hsltemp=mysql_query($sql,$db);
					list($size)=mysql_fetch_array($hsltemp);
					$sql="SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
					$hsltemp=mysql_query($sql,$db);
					list($warna)=mysql_fetch_array($hsltemp);
					$sql="SELECT qty,hargajual FROM po_markas_pusat_detail  WHERE no_po='$no_po' AND kd_produk='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($qtypo,$hargapo)=mysql_fetch_array($hsltemp);
					/* Cari Harga Dasar */
                //   $sql1="SELECT hargadasar,hargajual FROM produk WHERE kode='$kd_produk'";//
                    // handle barang yang tidak ada namanya karena kode 15 nya dirubah
		
		$sql1="SELECT harga FROM do_produk_detail  WHERE kd_produk='$kd_produk' and no_do='$no_do' limit 1";
		
		//$sql1="SELECT hargajual FROM produk WHERE kode='$kd_produk'"; // 4 agustus 2011
                 	 $hsltemp1=mysql_query($sql1,$db)or die($sql1); // 4 agustus 2011
                    list($hargajual)=mysql_fetch_array($hsltemp1); // 4 agustus 2011
					$harga=$rs["harga"];
				//	$hargajual=$harga;
					$hargadasar=$rs['hpp'];
				//echo $hargajual;
				
					if($hargadasar==0){
                        $hsltemp1=mysql_query("SELECT hargadasar FROM produk WHERE kode='$kd_produk'",$db);
  					    list($hargadasar)=mysql_fetch_array($hsltemp1);
					    if($hargadasar==0 || $hargadasar=""){$hargadasar=$hargajual * 0.57;}
					}
					
					//tgl 14092016 ditutup tgl 16092016 dibuka lagi permintaan pa Agus
					#if($username!='budi-it'){$hargadasar=0;}
					
					//$harga=$hargajual; by xtreme (teu kaitung lamun kieu mah) 
                    $qtydo=$rs["qty"];
					
					//Tambahan 17112016
					$qtycek=$rs["qty_cek"];
					$masalah=$rs["masalah"];
					$fu=$rs["fu"];
					
					// $jumlahdo=$rs["jumlah"];
					$jumlahpo=$hargapo*$qtypo;
					$jumlahhppdo=$hargadasar*$qtydo;
                    $jumlahhpjdo=$hargajual*$qtydo;
					//
					$totalhargado+=$harga;
					$totalhargapo+=$hargapo;
					$totalhargadasar+=$hargadasar;
                    $totalqtydo+=$qtydo;
					$totalqtypo+=$qtypo;
					$totaldo+=$jumlahhpjdo;
					$totaldohpp+=$jumlahhppdo;
                    $totalpo+=$jumlahpo;
					
					$sql="SELECT qty_a,qty_b FROM do_produk_detail WHERE no_do='$no_do' AND kd_produk='$kd_produk'";
					$hsltemp=mysql_query($sql,$db);
					list($qty_a,$qty_b)=mysql_fetch_array($hsltemp);
					$total_qty_a+=$qty_a;
					$total_qty_b+=$qty_b;
                    $bgclr1 = "#FF9900"; $bgclr2 = "#336699";
                    $polybag=$rs["polybag"];
                    $polybag=$polybag+1;
                    $bgcolor5 = ( $polybag % 2 ) ? $bgclr1 : $bgclr2;
                    $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
					
					
					if(empty($namaproduk)){
					   $isInvalid=1;	
					   $bgcolor='#FF0000';
					}
			?>
					<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
						<td align="center" height="20"><?php echo $no; ?></td>
						<td align="center" height="20" bgcolor="<?echo $bgcolor5;?>"><?php echo $polybag; ?></td> 
                        <td height="20" >&nbsp;<?php echo $kd_produk; ?></td>
						<td height="20">&nbsp;<?php echo $namaproduk; ?></td>
						<td height="20">&nbsp;<?php echo $warna; ?></td>
						<td height="20" align="center">&nbsp;<?php echo $size; ?></td>
						<td align="center" height="20" id="qty_<?php echo $kd_produk."_".$polybag; ?>"><?php echo number_format($qtydo); ?></td>
						<td align="center" class="colqty"><input class="qtycek" name="qtycek_<?php echo $kd_produk."_".$polybag; ?>" 
                        id="qtycek_<?php echo $kd_produk."_".$polybag; ?>" type="text" size="5" maxlength="10" value="<?php echo $qtycek; ?>" <?php if($approved2){ echo 'readonly="readonly"';} ?>  /></td>
						<td align="center" height="20">&nbsp;<?php echo $satuan; ?></td>
                        <td align="right" height="20"><?php echo number_format($hargadasar,2,",","."); ?></td>
						<td align="right" height="20"><?php echo number_format($harga,2,",","."); ?></td>
						<td align="right" height="20"><?php echo number_format($jumlahhppdo,2,",","."); ?></td>
					    <td align="right" height="20"><?php echo number_format($jumlahhpjdo,2,",","."); ?></td>
						<?php if($ret){
						?><td  height="20" align="center"><a href="do_produk_detail.php?no_do=<?php echo $_GET[no_do]?>&retur&kode=<?php echo $kd_produk?>&q=<?php echo $qtydo;?>&sq=<?php echo $rs['seqno']?>">Retur</a></td>
						
						<?php
						}?>
                        <td align="center" class="colmasalah"><textarea name="txtmasalah_<?php echo $kd_produk."_".$polybag; ?>" id="txtmasalah_<?php echo $kd_produk."_".$polybag; ?>" cols="45" rows="2" class="txtmasalah" <?php if($approved2){ echo 'readonly="readonly"';} ?>><?php echo $masalah ?></textarea></td>
						<td align="center" class="colfu"><textarea name="txtfollowup_<?php echo $kd_produk."_".$polybag; ?>" id="txtfollowup_<?php echo $kd_produk."_".$polybag; ?>" cols="45" rows="2" class="txtfollowup" <?php if($approved2){ echo 'readonly="readonly"';} ?>><?php echo $fu; ?></textarea></td>
                    </tr>
			<?php
				}
			?>
			<tr>
				<td height="20" align="center" colspan="6" bgcolor="#336699"><font color="#FFFFFF"><b>TOTAL</b></font></td>
				<td height="20" align="center" bgcolor="#336699"><font color="#FFFFFF"><b><?php echo number_format($totalqtydo); ?></b></font></td>
				<td align="center" bgcolor="#336699" class="colqty">&nbsp;</td>
				<td height="20" bgcolor="#336699">&nbsp;</td>
                <td height="20" bgcolor="#336699" align="right"><font color="#FFFFFF"><b><?php echo number_format($totalhargadasar,2,",","."); ?></b></font></td>
				<td height="20" bgcolor="#336699" align="right"><font color="#FFFFFF"><b><?php echo number_format($totalhargado,2,",","."); ?></b></font></td>
				<td height="20" bgcolor="#336699" align="right"><font color="#FFFFFF"><b><?php echo number_format($totaldohpp,2,",","."); ?></b></font></td>
                <td height="20" bgcolor="#336699" align="right"><font color="#FFFFFF"><b><?php echo number_format($totaldo,2,",","."); ?></b></font></td>
			</tr>
		</table>
      <table width="100%">
	      <tr>
	        <td align="center"><?php 
						if($approved){
							echo "<b>(APPROVED I BY $approveby)</b>";
							if($approved2){
								echo "<b>(APPROVED II BY $approveby2)</b>";
							}else{								
								?>
	          <input type="button" name="approve" value="Approve II <?php if($isInvalid==1){ echo '[ nama produk tidak lengkap!! ]';}?>" 
                                onclick="approve2('<?php echo $no_do; ?>');" 
								<?php if($isInvalid==1){ echo 'disabled="disabled"';}?> />
	          <?php
							}
						}else{
							?>
	          <input type="button" value="Approve I" onclick="if(confirm('Approving DO PRODUK <?php echo $no_do; ?>?')){window.location='do_produk_approving.php?no_do=<?php echo $no_do; ?>';}" />
	          <?php
						}
						if($approved){
							?>
	          <input type="button" value="Print Mode" onclick="window.open('do_produk_print.php?no_do=<?php echo $no_do; ?>','do_produk_print','width=800,height=400,menubar=yes,scrollbars=yes');" />
	          <?php
						}
						?>
	          <input type="button" value="Lihat Polybag" onclick="window.location='sj_produk_distribusi_detail.php?no_sj=<?php echo $no_sj; ?>';" />
	          <?php
					?>
	          <input type="button" value="Kembali" onclick="window.location='do_produk_listv4.php';" />
	          <input type="button" value="Export To Excel" onclick="window.open('do_produk_detail_barcode.php?pabrik=<?php echo $nama_pabrik?>&no_do=<?php echo $no_do; ?>','do_produk_detail_barcode','width=1,height=1,menubar=yes,scrollbars=yes');" />
              <input type="button" value="Export TO PDF" onclick="window.location='do_produk_detail_v3_pdf.php?no_do=<?php echo $no_do;?>';" /> </td>
        </tr>
      </table>
	</form>
    
    <script>
    var check_qty='<?php echo $cek_qtydo_app2;?>';
    </script>
    
   
<?php include_once "footer.php" ?>
