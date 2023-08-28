<?php 
$content_title="Produk Master [PARAMETER -> Produk View] ";
include_once "header.php"; 
$peri=500;
$thisPage=$_SERVER['PHP_SELF'];
?>
<style>
.hide{display:none}

</style>
<script type="text/javascript" src="sortable.js"></script> 
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
 <script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script language="javascript" src="app_libs/produk_list_fai_v2.js"></script>


<?php
	
	
	
	$startrow=0;	
	if(sanitasi($_REQUEST["prev"])){
		if(sanitasi($_REQUEST["startrow"])-$peri<=0){$startrow=0;}else{$startrow=sanitasi($_REQUEST["startrow"])-$peri;}
	}
	if(sanitasi($_REQUEST["next"])){
		$startrow=sanitasi($_REQUEST["startrow"])+$peri;
		
	}
	
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;	
	$page=$hal;
           		   
    if(isset($_GET['action'])){
		session_start();
		if(isset($_GET['hal'])){
			$kode=$_SESSION["srckode"];
			$barcode=$_SESSION["srckode"];
			$kode_basic_item=$_SESSION["srcbasic"];
			$kode_kategori=$_SESSION["srckategori"];
			$kode_kelas=$_SESSION["srckelas"];
			$kode_style=$_SESSION["srcstyle"];
			$kode_model=$_SESSION["srcmodel"];
			$kode_size=$_SESSION["srcsize"];
			$kode_supplier=$_SESSION["srcsupplier"];
			$kode_warna=$_SESSION["srcwarna"];
			$kode_grade=$_SESSION["srcgrade"];
			$srcnama=$_SESSION["srcnama"];
			$md=$_SESSION['md'];
			$pilihan=$_SESSION['pilihan'];
			$jmlHal=$_SESSION['jmlHal'];
		}else{
			$_SESSION["srckode"]=sanitasi($_POST["srckode"]);
			$_SESSION["srckode"]=sanitasi($_POST["srckode"]);
			$_SESSION["srcbasic"]=sanitasi($_POST["srcbasic"]);
			$_SESSION["srckategori"]=sanitasi($_POST["srckategori"]);
			$_SESSION["srckelas"]=sanitasi($_POST["srckelas"]);
			$_SESSION["srcstyle"]=sanitasi($_POST["srcstyle"]);
			$_SESSION["srcmodel"]=sanitasi($_POST["srcmodel"]);
			$_SESSION["srcsize"]=sanitasi($_POST["srcsize"]);
			$_SESSION["srcsupplier"]=sanitasi($_POST["srcsupplier"]);
			$_SESSION["srcwarna"]=sanitasi($_POST["srcwarna"]);
			$_SESSION["srcgrade"]=sanitasi($_POST["srcgrade"]);
			$_SESSION["srcnama"]=sanitasi($_POST["srcnama"]);
			$_SESSION['md']=sanitasi($_POST['md']); 	
			$_SESSION['pilihan']=sanitasi($_POST['pilihan']);
			$_SESSION['jmlHal']=sanitasi($_POST['jmlHal']);
			$kode=$_SESSION["srckode"];
			$barcode=$_SESSION["srckode"];
			$kode_basic_item=$_SESSION["srcbasic"];
			$kode_kategori=$_SESSION["srckategori"];
			$kode_kelas=$_SESSION["srckelas"];
			$kode_style=$_SESSION["srcstyle"];
			$kode_model=$_SESSION["srcmodel"];
			$kode_size=$_SESSION["srcsize"];
			$kode_supplier=$_SESSION["srcsupplier"];
			$kode_warna=$_SESSION["srcwarna"];
			$kode_grade=$_SESSION["srcgrade"];
			$srcnama=$_SESSION["srcnama"];
			$md=$_SESSION['md'];
			$pilihan=$_SESSION['pilihan'];
			$jmlHal=$_SESSION['jmlHal'];
		}
    }else{
		
	}
	
	if(empty($jmlHal)){		
	   $jmlHal=100;	
	}
	$arrayProdukPilihan=array();
	  if($jml_priv>0){
		  $sql="SELECT SQL_CACHE pp.pilihan  FROM produk_pilihan_mst pp inner join 
		  user_account_produk_pilihan up on up.produk_pilihan=pp.pilihan WHERE pp.aktif=1;"; 
	  }else{//kosong
		  $arrayProdukPilihan['']='---ALL---';
		  $sql="SELECT SQL_CACHE pilihan FROM produk_pilihan_mst WHERE aktif=1;";
	  }
	  
	 
	  $query=mysql_query($sql)or die($sql);
	  
	  while(list($list_pilihan)=mysql_fetch_array($query)){
			$arrayProdukPilihan[$list_pilihan]=$list_pilihan;
	  }
	
	
?>
	<fieldset style="width:800px;">
		<legend>Cari:</legend>
		<form method="POST" action="<?php echo $thisPage; ?>?action=search">
			<table>
				<tr>
					<td valign="top">
						<table>
							<tr>
								<td nowrap><b>Barcode</b></td>
								<td><b>:</b></td>
								<td><input type="text" name="srckode" id="srckode" value="<?php echo $barcode ?>" size="25"></td>
							</tr>
							<tr>
								<td nowrap><b>Basic Item</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcbasic" id="srcbasic" onchange="barcode_load();submit();" style="width:300px;">
										<option value="">-Basic Item-</option>
										<?php
											$sql="SELECT kode,item FROM mst_basic_item";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srcbasic){echo "selected";} ?>><?php echo $item; ?></option>
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
									<select name="srckategori" id="srckategori" style="width:300px;" onchange="barcode_load();submit();">
										<option value="">-Kategori-</option>
										<?php
											$sql="SELECT SQL_CACHE kode,kategori FROM mst_kategori WHERE kode_basic_item='$kode_basic_item'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srckategori){echo "selected";} ?>><?php echo $item; ?></option>
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
									<select name="srckelas" id="srckelas"  style="width:300px;"  onchange="barcode_load();submit();">
										<option value="">-Kelas-</option>
										<?php
											$sql="SELECT SQL_CACHE kode,kelas FROM mst_kelas WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srckelas){echo "selected";} ?>><?php echo $item; ?></option>
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
									<select name="srcstyle" id="srcstyle"  style="width:300px;"  onchange="barcode_load();submit();">
										<option value="">-Style-</option>
										<?php
											$sql="SELECT SQL_CACHE  kode,style FROM mst_style WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srcstyle){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>							
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Model</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcmodel" id="srcmodel" style="width:300px;"  onchange="barcode_load();submit();">
										<option value="">-Model-</option>
										<?php
											$sql="SELECT  SQL_CACHE kode,model FROM mst_model WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srcmodel){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>
									</select>
								</td>
							</tr>
							<tr>
							  <td nowrap>MD</td>
							  <td>&nbsp;</td>
							  <td><select id="md" name="md" style="width:300px;" >
		<option value="">All</option>
		 <?php $sql ="SELECT SQL_CACHE id,nama FROM md_produk ORDER BY seq";
			$res 	= mysql_query($sql)or die($sql);
			$arrayMD=array();
			while(list($kode_md,$nama)	= mysql_fetch_array($res)){
				$arrayMD[$kode_md]=$nama;
				?><option value="<?php echo $kode_md?>" <?php if($md==$kode_md){echo"selected";}?>><?php echo $nama?></option><?php
				
			}
		 ?>
		</select></td>
						  </tr>
							<tr>
							  <td nowrap>Pilihan</td>
							  <td>&nbsp;</td>
							  <td><select name="pilihan" id="pilihan" style=";width:300px;">
								<?php
                                         foreach($arrayProdukPilihan as $key => $value){
                                           ?>
                                <option value="<?php echo $key; ?>" <?php if($pilihan==$key){echo"selected";}?>><?php echo $value; ?></option>
                                <?php  	 
                                         }
                           ?>
                    </select></td>
						  </tr>
							<tr>
							  <td nowrap>Jumlah Data</td>
							  <td>&nbsp;</td>
							  <td><input type="text" name="jmlHal" id="jmlHal"  value="<?php echo $jmlHal; ?>"/></td>
						  </tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td nowrap><b>Size</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcsize" id="srcsize" style="width:300px;"  onchange="barcode_load();submit();">
										<option value="">-Size-</option>
										<?php
											$sql="SELECT kode,size FROM mst_size";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srcsize){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>
									</select>								</td>
							</tr>
							<tr>
								<td nowrap><b>Supplier</b></td>
								<td><b>:</b></td>
								<td><input id="srcsupplier" type="text" readonly name="srcsupplier" value="<?php echo $srcsupplier; ?>" onclick="return showVendor(this.id,'','','1')" size="12" onblur="barcode_load();submit();"></td>
							</tr>
							<tr>
								<td nowrap><b>Warna</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcwarna" id="srcwarna" style="width:300px;"  onchange="barcode_load();submit();">
										<option value="">-Warna-</option>
										<?php
											$sql="SELECT kode,warna FROM mst_warna WHERE LENGTH(kode)=3 ORDER BY warna";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srcwarna){echo "selected";} ?>><?php echo $item." [$kode]"; ?></option>
										<?php
											}
										?>
									</select></td>
							</tr>
							<tr>
								<td nowrap><b>Grade</b></td>
								<td><b>:</b></td>
								<td><select name="srcgrade" id="srcgrade"  style="width:100px;" onchange="barcode_load();submit();">
								  <option value="">Pilih Grade</option>
                                  <option value="a" <?php if("a"==$srcgrade){echo "selected";} ?>>Grade A</option>
                                  <option value="b" <?php if("b"==$srcgrade){echo "selected";} ?>>Grade B</option>
                                </select></td>
							</tr>
							<tr>
								<td nowrap><b>Nama Produk</b></td>
								<td><b>:</b></td>
								<td><input type="text" name="srcnama" id="srcnama" value="<?php echo $srcnama?>" size="70"></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="left"><input type="submit" name="search" value="   Cari   " />    </td>
				</tr>
				<?php
					//if(sanitasi($_REQUEST["search"]) || sanitasi($_REQUEST["prev"]) || sanitasi($_REQUEST["next"])){
				?>
				<tr>
					<td colspan="2" align="center">&nbsp;</td>
				</tr>
				<input type="hidden" name="startrow" value="<?php echo $startrow; ?>">
				<?php
					//}
				?>
			</table>
		</form>
	</fieldset>
    <?php
	  if($_GET['action']=='search'){
		  
	  }else{
		  include('footer.php');
		  die()  ;
	  }
	
	?>
	<form action="produk_list_fai_v2_export.php?action=export" method="post" name="formExport" target="_blank">
     <input type="submit" name="btnExport" value="   Export   " />    </form>
    <?php
	 $inner_sql='';
	 $filter='';
	  if(!empty($pilihan)){
		  $inner_sql.=" inner join produk_pilihan pp on pp.id_barang=p.kode ";
		  $filter.=" AND pp.pilihan='$pilihan' and pp.status=1 ";
	 }
	 
	 if(!empty($md)){
		  $filter.=" AND p.wil_md='$md' ";
	 }
	
	         if(!empty($barcode)){$filter.=" AND (p.kode LIKE '$barcode%')";}
			 if(!empty($srcnama)){$filter.=" AND p.nama like '$srcnama%'";}
			 if(!empty($kode_basic_item)){$filter.=" AND p.kode_basic_item = '$kode_basic_item'";}
			if(!empty($kode_kategori)){$filter.=" AND p.kode_kategori = '$kode_kategori'";}
			
			if(!empty($kode_kelas)){$filter.=" AND p.kode_kelas = '$kode_kelas'";}
			if(!empty($kode_style)){$filter.=" AND p.kode_style = '$kode_style'";}
			if(!empty($kode_model)){$filter.=" AND p.kode_model = '$kode_model'";}
			if(!empty($kode_size)){$filter.=" AND p.kode_size = '$kode_size'";}
			if(!empty($kode_model)){$filter.=" AND p.kode_model = '$kode_model'";}
			if(!empty($kode_supplier)){$filter.=" AND p.kode_supplier = '$kode_supplier'";}
			if(!empty($kode_warna)){$filter.=" AND p.kode_warna = '$kode_warna'";}			
			if(!empty($kode_grade)){$filter.=" AND p.grade like '$kode_grade%'";}
	   
	         $sql="SELECT SQL_CALC_FOUND_ROWS `p`.`kode` , `p`.`kode_grade_a` , `p`.`grade` , `p`.`kode_basic_item` 
			  ,`p`.`kode_kategori` , `p`.`kode_kelas` , `p`.`kode_style` , `p`.`kode_model` , `p`.`kode_size`  , 
			  `p`.`kode_supplier` , `p`.`kode_warna`, `p`.`nama` , `p`.`hargajual` , `p`.`satuan`, p.wil_md
			    FROM produk p $inner_sql WHERE  p.kode_basic_item LIKE '%' $filter ";
			$sql.="  order by p.kode,p.updatedate DESC ";
			
			$_SESSION['sql_exp']=$sql;
			
			$sql.= " LIMIT ".($page*$jmlHal).",".$jmlHal;
			
			
			$sql_count="SELECT FOUND_ROWS()";
			$sql_count="SELECT count(`p`.`kode`) jumlah FROM produk p $inner_sql WHERE  p.kode_basic_item LIKE '%' $filter ";
			
			
			if($username=='budi-it'){
			   echo "<h3> $sql </h3>";	
			}
			// echo $sql;
			$xhsl=mysql_query($sql);
			
			$res_all=mysql_query($sql_count);
			list($jmlAll)=mysql_fetch_array($res_all);
	
	?>
	<table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" height="119">
		<tr>
			<td align="center" height="24" bgcolor="#99CC00"><b>No</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Kode(BarCode)</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Barcode 13</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Grade</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Nama</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Basic Item</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Kategori</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Kelas</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Style</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Model</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Size</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Supplier</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Warna</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Satuan</b></td>
			<td align="center" height="24" bgcolor="#99CC00"><b>Harga Jual</b></td>
			<td align="center" bgcolor="#99CC00">MD</td>
		</tr>
		<?php
			
			
			$no=($hal*$jmlHal);
			while($rs=mysql_fetch_array($xhsl)){
				
				$kode=$rs["kode"];
				$kode13=$rs["kode_grade_a"];
				$grade=strtoupper($rs["grade"]);
				$kode_basic_item=$rs["kode_basic_item"];
				$kode_kategori=$rs["kode_kategori"];
				$kode_kelas=$rs["kode_kelas"];
				$kode_style=substr($kode,3,2);
				$kode_model=$rs["kode_model"];
				$kode_size=$rs["kode_size"];
				$kode_supplier=$rs["kode_supplier"];
				$kode_warna=$rs["kode_warna"];
				$nama=$rs["nama"];
				$startqty=$rs["startqty"];
				$satuan=$rs["satuan"];
				#$hargadasar=$rs["hargadasar"];
				$hargajual=$rs["hargajual"];
				$wil_md=$rs['wil_md'];
				
				$md=$arrayMD[$wil_md];
				#$harganaik=$rs["harganaik"];
				
				
				if($username!='budi-it'){
				   $hargadasar=0;	
				}
				
				if($harganaik=="1"){$harganaik="Ya";}else{$harganaik="Tidak";}
				$sql="SELECT item FROM mst_basic_item WHERE kode='$kode_basic_item'";
				$hsl=mysql_query($sql);
				list($basic)=mysql_fetch_array($hsl);
				$sql="SELECT kategori FROM mst_kategori WHERE kode_basic_item='$kode_basic_item' AND kode='$kode_kategori'";
				$hsl=mysql_query($sql);
				list($kategori)=mysql_fetch_array($hsl);
				$sql="SELECT kelas FROM mst_kelas WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode='$kode_kelas'";
				$hsl=mysql_query($sql);
				list($kelas)=mysql_fetch_array($hsl);
				$sql="SELECT style FROM mst_style WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode='$kode_style'";
				$hsl=mysql_query($sql);
				list($style)=mysql_fetch_array($hsl);
				$sql="SELECT model FROM mst_model WHERE kode='$kode_model' AND kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
				//echo "<!-- $sql -->";
				$hsl=mysql_query($sql);
				list($model)=mysql_fetch_array($hsl);
				$sql="SELECT size FROM mst_size WHERE kode='$kode_size'";
				$hsl=mysql_query($sql);
				list($size)=mysql_fetch_array($hsl);
				$sql="SELECT nama FROM supplier WHERE id='$kode_supplier'";
				$hsl=mysql_query($sql);
				list($supplier)=mysql_fetch_array($hsl);
				$sql="SELECT warna FROM mst_warna WHERE kode='$kode_warna'";
				$hsl=mysql_query($sql);
				list($warna)=mysql_fetch_array($hsl);
				$bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
				$bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
				if($model==''){//dirubah tgl 28 feb 2013 karena untuk mengecek ketersediaan kode model by xtreme
					//$sql="select nama from produk where kode='$kode'";
					//$res=mysql_query($sql)or die($sql);
					//list($model)=mysql_fetch_array($res);				
				}
				$mod=false;$sty=false; $war=false;
				if($model!=''){
				$mod=true;
				}else{
					$mod=false;
				}
				
				if($style!=''){
					$sty=true;
				}else{
					$sty=false;
				}
				
				if($warna!=''){
					$war=true;
				}else{
					$war=false;
				}
				
				if((!$war)&&($sty)&&($mod)){
					$sql="insert into mst_warna (kode,warna)values('$kode_warna','unidefined')";
					$res=mysql_query($sql);
					}
				
		// if ( $kode != $kode13 ) {
		if((!$mod)||(!$war)||(!$sty)){
			$bgcolor='red';
		}
		$no++;
		?>
				<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
					<td valign="top"><?php echo $no; ?></td>
					<td valign="top"><?php echo $kode; ?></td>
					<td valign="top"><?php echo $kode13; ?></td>
					<td valign="top" align="center"><?php echo $grade; ?></td>
					<td width="180" valign="top">&nbsp;<?php echo $nama; ?></td>
					<td valign="top">&nbsp;<?php echo $basic; ?></td>
					<td valign="top">&nbsp;<?php echo $kategori; ?></td>
					<td valign="top">&nbsp;<?php echo $kelas; ?></td>
					<td valign="top">&nbsp;<?php echo $style; ?></td>
					<td valign="top">&nbsp;<?php echo $model; ?></td>
					<td valign="top">&nbsp;<?php echo $size; ?></td>
					<td valign="top">&nbsp;<?php echo $supplier; ?></td>
					<td valign="top">&nbsp;<?php echo $warna; ?></td>
					<td valign="top" align="right">&nbsp;<?php echo $satuan; ?></td>
					<td valign="top" align="right"><?php echo number_format($hargajual,2,",","."); ?></td>
					<td valign="top" align="right"><?php echo $md;?></td>
				</tr>
		<?php 
		// }
			}
		?>
	</table>
	
    
<table style="margin-left:10px; margin-top:10px;">
<tr>
						<td class="text_standard">
							Page : 
						  <span class="hal" onClick="location.href='<?php echo $thisPage; ?>?action=search&hal=0';">First</span>
							<?php for($i=0;$i<($jmlAll/$jmlHal);$i++){ 
								if($hal<=0){ ?>
									<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlAll/$jmlHal)){
									if($i>=(($jmlAll/$jmlHal)-5)){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
									<?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
						  <span class="hal" onClick="location.href='<?php echo $thisPage; ?>?action=search&hal=<?php echo intval(($jmlAll/$jmlHal)); ?>';">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlAll; ?>						</td>
  </tr>
</table>
<?php include_once "footer.php" ?>
