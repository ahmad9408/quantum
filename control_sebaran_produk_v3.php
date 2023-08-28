<?php
ob_start("ob_gzhandler");
$content_title="Control Sebaran produk"; 
if(1==1){include('header.php');} 

/*
last edit 03072017 untuk data default tgl sekarang
v3 di up by budi perbaikan kode supaya mudah dibayar 
 konsisten penggunanan js itu di file yang berbeda 
 perbaikan struktur kode php supaya readable 
*/
?>
<style>
.hide{display:none}
.mylink {
	cursor:pointer;
	color:#0033FF;
}
</style>
<link rel="stylesheet" href="js/chosen_v1.5.1/chosen.min.css">
 <script src="js/chosen_v1.5.1/chosen.jquery.min.js" type="text/javascript"></script>
<script language="javascript" src="format.20110630-1100.min.js"></script>
<script language="javascript" src="app_libs/control_sebaran_produk_v3.js?waktu=<?php echo date("YmdHis");?>"></script>
<script language="javascript">
	var organization='';//diisi dibawah
	var outlet='';
	var markas=''; 
</script>
<?php
	$thisPage=$_SERVER['PHP_SELF'];
	$isCache=$_POST['ic']; //is cache =0
	$isCache=1;
	if($isCache==1){
		 $sql_cache=' SQL_CACHE '; 
	 }else{
		 $sql_cache='';
	 }
	 
	
	 /* 
	
	 $chek		= $_POST['chek'];
	 $ot 		= $_SESSION['outlet'];
	 $pc	 	= explode("-",$ot);
	 if($pc[1]=="O0000"){
	 	$ter_markas 	= " id like '$pc[0]%' ";
		
						$sql 				= "SELECT area_lain FROM user_account WHERE  username='$_SESSION[username]'";
						$query 				= mysql_query($sql)or die($sql);
						list($area_lain)	= mysql_fetch_array($query);
						if(strlen($area_lain)>2){
							$pc					= explode(";",$area_lain);
							foreach($pc as $area){
								 
								if(strlen($area)>3){
									$terusan_area 	.= " or id like '$area%' ";
								}
							}
						}else{
								$terusan_area 	= "  ";	
						}
		
		$ter_markas =" and ( $ter_markas ".$terusan_area." )";
	 }else{
	 	$ter_markas 	= "  ";
	 }
	 */
	 					
	 $kodeoutlet=$_SESSION['outlet'];
	 /*
     $group=substr($kodeoutlet,5,5);
	  if($group=='O0000' ||$group=='o0000'){
		  $area=substr($kodeoutlet,0,4);
	  }else{
		  $area=$kodeoutlet;  
	  }
	  */
	  //untuk outlet bisa melihat stok semarkas lainnya rubah tanggal 24 juli 2017
	  if(!empty($kodeoutlet)){
		   $area=substr($kodeoutlet,0,4); 
	  }
	 
	  
	 $data_tambahan=" o.id LIKE '$area%' ";
	  if(!empty($area_lain)){
		  $d=explode(';',$area_lain); 
		  $d_lain='';
		  foreach($d as $kd_lain){
			  $kd_lain=trim($kd_lain);
				if(!empty($kd_lain)){
					$d_lain.=" OR o.id LIKE '$kd_lain%' ";
				}
		  }
		   $data_tambahan.=$d_lain;
	  } 	  
	  $sql_tambahan=" AND ( $data_tambahan ) ";
	  if(!empty($prefix)){
		  $sql_tambahan.=" AND  o.id LIKE '$prefix%'  ";
	  }
	 
	 
	 
	 
	 
	if(isset($_REQUEST['hal'])){
			$srcnama=$_SESSION['srcnama'];
		    $srckode=$_SESSION['srckode'];	
			$txt_organization=$_SESSION['txt_organization'];	
			$txt_outlet=$_SESSION['txt_outlet'];	
			$txt_markas=$_SESSION['txt_markas'];	
			$srcgrade=$_SESSION["srcgrade"];
			$data_tampil=$_SESSION['data_tampil'];
			$tgl1	= $_SESSION['tgl1'];
	        $tgl2	= $_SESSION['tgl2'];
			$chek	= $_SESSION['chek'];
		
	}elseif(isset($_REQUEST['action'])){
		$srcnama=sanitasi($_POST["srcnama"]);
		$srcbasic=sanitasi($_POST["srcbasic"]);
		$srckategori=sanitasi($_POST["srckategori"]);
		$srckelas=sanitasi($_POST["srckelas"]);
		$srcstyle=sanitasi($_POST["srcstyle"]);
		$srcmodel=sanitasi($_POST["srcmodel"]);
		$srcsize=sanitasi($_POST["srcsize"]);
		$srcsupplier=sanitasi($_POST["srcsupplier"]);
		$srcwarna=sanitasi($_POST["srcwarna"]);
		$srcgrade=sanitasi($_POST["srcgrade"]);
		$srckode=sanitasi($_POST["srckode"]);
		$txt_organization=$_POST['txt_organization'];
		$txt_outlet=$_POST['txt_outlet'];	
		$txt_markas=$_POST['txt_markas'];
		$data_tampil=sanitasi($_POST['data_tampil']);
		
		$_SESSION["srcnama"]=$srcnama;
		$_SESSION["srcgrade"]=$srcgrade;
		$_SESSION["srcbasic"]=$srcbasic;
		$_SESSION["srckategori"]=$srckategori;
		$_SESSION["srckelas"]=$srckelas;
		$_SESSION["srckelas"]=$srckelas;
		$_SESSION["srcmodel"]=$srcmodel;
		$_SESSION["srcsize"]=$srcsize;
		$_SESSION["srcsupplier"]=$srcsupplier;
		$_SESSION["srcwarna"]=$srcwarna;
		
		$_SESSION["srckode"]=$srckode;
		$_SESSION['txt_organization']=$txt_organization;
		$_SESSION['txt_outlet']=$txt_outlet;	
		$_SESSION['txt_markas']=$txt_markas;
		$_SESSION['data_tampil']=$data_tampil;
		
		$tgl1	= $_POST['tgl1'];
	    $tgl2	= $_POST['tgl2'];
		$_SESSION['tgl1']=$tgl1;
	    $_SESSION['tgl2']=$tgl2;
		
		$chek	= $_POST['chek'];
		$_SESSION['chek']=$chek;
		
	}else{//start
		$srcgrade='a';
		$txt_organization='M';
		$data_tampil=500;
	}
	 if(empty($tgl1)){
		 $tgl1=date('Y-m-01');
	}
	if(empty($tgl2)){
		 $tgl2=date('Y-m-d');
	}
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	
	if(empty($data_tampil)){
		$data_tampil=500;
	}
	$jmlHal=$data_tampil;
	$page=$hal;
	
?>
	<fieldset style="width:2%;">
		<legend>Cari:</legend>
		<form method="POST" name="f1" action="<?php echo $thisPage; ?>?action=search">
		
			<table>
				<tr>
					<td >
						<table>
							<tr>
							  <td><?php echo $data_global['f_organization']; ?></td>
							  <td>&nbsp;</td>
							  <td><b><font color="#0000FF">
							    <select name="txt_organization" style="font-size: 8pt;width:200px;" id="txt_organization" onchange="changeOrganization(this);">
							      <option value="">--Select--</option>
							      <?php
		  
		   $sql_otl="select SQL_CACHE trim(prefix), nama from organization  where prefix like '$prefix%' order by urutan";
		   $result=mysql_query($sql_otl) or die($sql_otl);
		   while(list($id,$nama)=mysql_fetch_array($result)){
		   	
		   ?>
							      <option value="<?php echo $id; ?>" <?php if($id==$txt_organization){echo"selected";}?>><?php echo $nama; ?></option>
							      <?php
		   }
		   
		
		
		?>
						      </select>
							  </font></b></td>
						  </tr>
							<tr>
								<td nowrap><b>Barcode</b></td>
								<td><b>:</b></td>
								<td><input type="text" name="srckode" id="srckode" value="<?php echo $srckode ?>" size="25"></td>
							</tr>
							<tr>
							  <td nowrap><b>Nama Produk</b></td>
							  <td>:</td>
							  <td><input type="text" name="srcnama" id="srcnama"  value="<?php echo $srcnama ?>" /></td>
						  </tr>
							<tr>
								<td nowrap><b>Basic Item</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcbasic" id="srcbasic"  style="width:300px;">
										<option value="">-Basic Item-</option>
										<?php
											$sql="SELECT SQL_CACHE kode,item FROM mst_basic_item 
											     WHERE  item NOT LIKE '%-%' AND item NOT LIKE 'no name'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_REQUEST["srcbasic"])){echo "selected";} ?>><?php echo $item; ?></option>
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
									<select name="srckategori" id="srckategori" style="width:300px;" >
										<option value="">-Kategori-</option>
										<?php
											$sql="SELECT SQL_CACHE kode,kategori FROM mst_kategori WHERE 
											   kode_basic_item='$srcbasic'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_REQUEST["srckategori"])){echo "selected";} ?>><?php echo $item; ?></option>
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
									<select name="srckelas" id="srckelas" style="width:300px;" >
										<option value="">-Kelas-</option>
										<?php
											$sql="SELECT SQL_CACHE kode,kelas FROM mst_kelas WHERE kode_basic_item='$srcbasic' AND kode_kategori='$srckategori'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_REQUEST["srckelas"])){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>							
									</select>
								</td>
							</tr>
							<tr>
								<td nowrap><b>Model</b></td>
								<td><b>:</b></td>
								<td><select name="srcmodel" id="srcmodel" style="width:300px;" >
								  <option value="">-Model-</option>
								  <?php
											$sql="SELECT SQL_CACHE kode,model FROM mst_model WHERE 
											  kode_basic_item='$srcbasic' AND kode_kategori='$srckategori' 
											    AND kode_kelas='$srckelas AND kode_style='$srcstyle'";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
										?>
								  <option value="<?php echo $kode; ?>" <?php if($kode==sanitasi($_REQUEST["srcmodel"])){echo "selected";} ?>><?php echo $item; ?></option>
								  <?php
											}
										?>
							    </select></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table>
							<tr>
								<td nowrap><b>Size</b></td>
								<td><b>:</b></td>
								<td>
									<select name="srcsize" id="srcsize" style="width:300px;">
										<option value="">-Size-</option>
										<?php
											$sql="SELECT SQL_CACHE kode,size FROM mst_size";
											$hsl=mysql_query($sql);
											$arraySize=array();
											while(list($kode,$item)=mysql_fetch_array($hsl)){
												$arraySize[$kode]=$item;
										?>
											<option value="<?php echo $kode; ?>" <?php if($kode==$srcsize){echo "selected";} ?>><?php echo $item; ?></option>
										<?php
											}
										?>
									</select>								</td>
							</tr>
							<tr>
								<td nowrap><b>Warna</b></td>
								<td><b>:</b></td>
								<td>
                                      <input type="text" name="srcwarna" id="srcwarna"  value="<?php echo $srcwarna ?>" />
									
										<?php
										    $arrayWarna=array();
											$sql="SELECT SQL_CACHE kode,warna FROM mst_warna WHERE LENGTH(kode)=3 ORDER BY warna";
											$hsl=mysql_query($sql);
											while(list($kode,$item)=mysql_fetch_array($hsl)){
												 $arrayWarna[$kode]=$item;										
											}
										?>
									</select></td>
							</tr>
							<tr>
								<td nowrap><b>Grade</b></td>
								<td><b>:</b></td>
								<td><select name="srcgrade" id="srcgrade"  style="width:300px;">
								  <option value="">Pilih Grade</option>
                                  <option value="a" <?php if("a"==$srcgrade){echo "selected";} ?>>Grade A</option>
                                  <option value="b" <?php if("b"==$srcgrade){echo "selected";} ?>>Grade B</option>
                                </select></td>
							</tr>
							<tr>
								<td nowrap colspan="3">
								
								<input type="checkbox" name="chek" id="chek" value="1" onclick="date_view()" <?php if($chek==1){echo"checked";}?>  /> <em><strong>Search range date 	launching</strong></em></td>
								 
								<?php
									if(!sanitasi($_REQUEST["srcnama"])){
										$sql="SELECT nama FROM produk WHERE kode LIKE '$barcode' AND kode_basic_item LIKE '$kode_basic_item' AND kode_kategori LIKE '$kode_kategori'";
										$sql.=" AND kode_kelas LIKE '$kode_kelas' AND kode_style LIKE '$kode_style' AND kode_model LIKE '$kode_model' AND kode_size LIKE '$kode_size' AND kode_supplier LIKE '$kode_supplier'";
										$sql.=" AND kode_warna LIKE '$kode_warna' AND grade LIKE '%$kode_grade'";
										$hsl=mysql_query($sql);
										list($_REQUEST["srcnama"])=mysql_fetch_array($hsl);
									}
								?>
								 
							</tr>
							<script language="JavaScript" src="calendar_us.js"></script>
            				<link rel="stylesheet" href="calendar.css" />
							<tr class="trhidetgl">
								<td nowrap><strong>Dari</strong></td>
								<td>:</td>
								 
								<td><input type="text" name="tgl1"   id="tgl1" value="<?php echo $tgl1; ?>" size="16"/>
									&nbsp;
									<script language="JavaScript">
									  new tcal ({
										// form name
										'formname': 'f1',
										// input name
										'controlname': 'tgl1'
									  });
										</script>
								</td>
							</tr>
							
							<tr class="trhidetgl">
								<td nowrap><strong>Sampai</strong></td>
								<td>:</td>
								 
								<td><input type="text" name="tgl2"   id="tgl2" value="<?php echo $tgl2; ?>" size="16"/>
									&nbsp;
									<script language="JavaScript">
									  new tcal ({
										// form name
										'formname': 'f1',
										// input name
										'controlname': 'tgl2'
									  });
										</script>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
                
				  <td colspan="2" align="left">
                  <table>
                  <tr>
                      <td>Outlet</td>
                      <td>:</td>
                      <td> <?php  $sql="SELECT SQL_CACHE  o.id,o.nama FROM outlet as o WHERE o.jenis in (1,2) and o.`type`=4 and o.is_show_omset=1 $sql_tambahan   order by o.id ASC";
					  if($username=='budi-it'){ 
						   echo "<h3>$sql</h3>";	
						}
					 # echo "<!-- $sql -->";
					  ?>
					  <select  name="txt_outlet" id="txt_outlet"  style="width:400px;" onchange="changeOutlet()">
                      <option value="">-All Location-</option>
                          <?php
										
											
											          
											$hsltemp=mysql_query($sql);
											while(list($id,$nama)=mysql_fetch_array($hsltemp)){
											?>
                      <option value="<?php echo $id; ?>" <?php if($id==$txt_outlet){echo "selected";} ?>><?php echo "[$id]".$nama; ?></option>
                      <?php
											}
											?>
                                            </select></td>
                  </tr>
                  <tr>
                    <td>Markas</td>
                    <td>&nbsp;</td>
                    <td><?php  $sql="SELECT SQL_CACHE  upper(o.id) id,o.nama FROM outlet o WHERE o.jenis in (1,2) 
									and o.`type`=1 and o.is_show_omset=1 $sql_tambahan  order by o.id ASC";		
								if($username=='budi-it'){
								   echo "<h3>$sql</h3>";	
								}				               
											$hsltemp=mysql_query($sql);
					?>
                      <select  name="txt_markas" id="txt_markas"  style="width:400px;">
                        <option value="">-All Location-</option>
                        <?php
											while(list($id,$nama)=mysql_fetch_array($hsltemp)){
											?>
                        <option value="<?php echo $id; ?>" <?php if($id==$txt_markas){echo "selected";} ?>><?php echo "[$id]".$nama; ?></option>
                        <?php
											}
											?>
                    </select></td>
                  </tr>
                  </table>
                  
                  
                  </td>
			  </tr>
				<tr>
					<td colspan="2" align="left">Data Tampil 
				    <input type="text" name="data_tampil" id="data_tampil"  value="<?php echo $data_tampil; ?>" /></td>
				</tr>
				<tr>
				  <td colspan="2" align="left"><input type="submit" name="search" value="Cari" /></td>
			  </tr>				
			</table>
		</form>
	</fieldset>
	<?php
	if($_GET['action']!='search'){
		include_once('footer.php');
		die();
	}	
	$listBarcode='';
	
	?>
    <span id="Process" style="font-size:14px;font-weight:bold;"></span><br />
    <span id="ProcessDetail" style="font-size:12px;font-weight:bold;"></span>
    
	<table border="1" width="1000" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" height="63">
		<tr>
			<td width="24" height="24" align="center" bgcolor="#99CC00"><strong>No</strong></td>
			<td width="82" height="24" align="center" bgcolor="#99CC00"><strong>Barcode </strong></td>
			<td width="33" height="24" align="center" bgcolor="#99CC00" ><strong>Grade</strong></td>
			<td align="center" height="24" bgcolor="#99CC00"><strong>Nama</strong></td>
			<td align="center" bgcolor="#99CC00"><strong>Warna</strong></td>
			<td align="center" bgcolor="#99CC00"><strong>Ukuran</strong></td>
			<td width="58" height="24" align="center" bgcolor="#99CC00" style="width:50px;"><strong>Reshare</strong></td>
			<td width="72" height="24" align="center" bgcolor="#99CC00" style="width:50px;"><strong>Markas</strong></td>
			<td width="69" height="24" align="center" bgcolor="#99CC00" style="width:50px;"><strong>Distribusi</strong></td>
			<td width="94" height="24" align="center" bgcolor="#99CC00"  ><strong>Tgl Launch</strong></td>
		</tr>
		<?php
			$sql_filter=' AND p.aktif=1';
			$inner_join='';
			if(!empty($srcnama)){
				$sql_filter.=" AND p.nama like '%$srcnama%'";
			}
			if(!empty($srcbasic)){
				$sql_filter.=" AND p.kode_basic_item = '$srcbasic' ";
			}
			if(!empty($srcgrade)){
				$sql_filter.=" AND p.grade ='$srcgrade'";
			}
			
			if(!empty($srcsize)){
				$sql_filter.=" AND p.kode_size = '$srcsize'";
			}
			
			if(!empty($srcwarna)){
				$inner_join.=" INNER JOIN `mst_warna` AS `w` ON (`p`.`kode_warna` = `w`.`kode`) ";
				$sql_filter.=" AND w.warna like  '%$srcwarna%' ";
			}
			
			if(!empty($chek)){
				$sql_filter.=" and l.tanggal_launching between '$tgl1' and '$tgl2' ";
			 }
		 
			
			$sql="SELECT SQL_CALC_FOUND_ROWS $sql_cache   p.kode,p.kode_grade_a,p.nama,p.grade,p.kode_warna,p.kode_size ,l.tanggal_launching     FROM produk p   LEFT JOIN mst_model_launching AS l ON 
				(l.kode_model=SUBSTRING(p.kode,1,7))
				 $inner_join WHERE p.kode LIKE '$srckode%' $sql_filter   ";
				 			
			$sql.="  order by p.kode ASC LIMIT ".($page*$jmlHal).",".$jmlHal.';';
			 
			if(($username=='budi-it')||($username=='rian-it')){
				echo $sql;
			}
			// 
			$xhsl=mysql_query($sql);
			
			$sql="SELECT FOUND_ROWS();";
			$query=mysql_query($sql)or die($sql);
			list($jmlData)=mysql_fetch_array($query);
			
			
			$no=($hal*$jmlHal);
			while(list($kode,$kode13,$nama,$grade,$kode_warna,$kode_size,$tgl_launching)=mysql_fetch_array($xhsl)){
				$listBarcode.="'$kode',";
				$warna=$arrayWarna[$kode_warna];
				$size=$arraySize[$kode_size];
			
			
				if($username!='budi-it'){
				   $hargadasar=0;	
				}
				
				
				
				$bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
				$bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
				
				
				
			
		
		$no++;
		?>
				<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
					<td height="37" ><?php echo $no; ?></td>
					<td ><?php echo $kode; ?></td>
					<td  align="center"><?php echo $grade; ?></td>
					<td width="326" >&nbsp;<?php echo $nama; ?></td>
					<td width="156" >&nbsp;<?php echo $warna; ?></td>
					<td width="64" >&nbsp;<?php echo $size; ?></td>
					<td id="o_<?php echo $kode; ?>" align="right">0</td>
					<td id="m_<?php echo $kode; ?>" align="right">0</td>
					<td id="d_<?php echo $kode; ?>" align="right">0</td>
					<td align="center"  ><?php echo $tgl_launching?></td>
				</tr>
		<?php 
		// }
			}
		?>
	</table>
    <?php
	   if(!empty($listBarcode)){
		   $listBarcode=substr($listBarcode,0,strlen($listBarcode)-1);
	  }
	   
	   if($_REQUEST['action']=='search'){
		     $_SESSION['listBarcode']=$listBarcode;
		}
	   
	   if($username=='budi-it'){
		    echo "<h3>$jmlData </h3>"; 
			#print_r($arrayWarna);
		 }
	?>
<table style="margin-left:10px; margin-top:10px;">
					<tr>
						<td class="text_standard">
							Page : 
						  <span class="hal" onclick="location.href='<?php  echo $thisPage; ?>?action=search&hal=0'">First</span>
							<?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
								if($hal<=0){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php  echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData/$jmlHal)){
									if($i>=(($jmlData/$jmlHal)-5)){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php  echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i+1); ?></span>
									<?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php  echo $thisPage; ?>?action=search&hal=<?php echo $i; ?>'"><?php echo ($i+1); ?></span>
									<?php }
								}
							} ?>
						  <span class="hal" onclick="location.href='<?php  echo $thisPage; ?>?action=search&hal=<?php echo intval(($jmlData/$jmlHal)); ?>'">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData; ?>
						</td>
					</tr>
</table>
</fieldset>
<script language="javascript">
$(document).ready(function(){
	outlet='<?php echo $txt_outlet; ?>';
	markas='<?php echo $txt_markas; ?>';
	organization='<?php echo $txt_organization; ?>';
	getStokOutlet();	
	getStokMarkas();
	getStokDistribusi();
})


</script>
<span id="load_space"></span>
<?php include_once "footer.php" ?>