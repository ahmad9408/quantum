<?php $content_title="DAFTAR SEWING"; include_once "header.php" ?>
<style>
    .mylink {
        cursor: pointer;
        color: #0000FF;
    }
</style>
<?php
	$no_co=sanitasi($_GET["no_co"]);
	$no_load=sanitasi($_GET["no_load"]);
	$cari=$_REQUEST[cari];
	$app=$_REQUEST[approve];
	if($app==1){
		$appcode=" and js.`approve2`='1'";
	}else if($app==0){
		$appcode=" ";
	}else if($app==2){
		
		$appcode=" and js.`approve2` IS NULL";
	}
	if(isset($_GET['hal'])){
		 session_start();
		 $tgl1=$_SESSION['tgl1'];
		 $tgl2=$_SESSION['tgl2'];
		 $no_load=$_SESSION["no_load"];
	     $cari=$_SESSION['cari'];
		 $app=$_SESSION['approve'];
		
		 
	 }elseif(isset($_GET['action'])){
		 session_start();
		 $tgl1=$_POST['tgl1'];
		 $tgl2=$_POST['tgl2'];
		 $_SESSION['tgl1']=$tgl1;
		 $_SESSION['tgl2']=$tgl2;
		 
		 $no_load=sanitasi($_POST["no_load"]);
	     $cari=sanitasi($_POST['cari']);
		 $app=$_POST['approve'];
		 $_SESSION["no_load"]= $no_load;
	     $_SESSION['cari']=$cari;
		 $_SESSION['approve']=$app;
	 }else{
		$hari_ini = date("Y-m-d");
		$tgl1 = date('Y-01-01', strtotime($hari_ini));
		$tgl2 = date('Y-m-t', strtotime($hari_ini));

		// $tgl1=date('2017-01-01');//mulai stabil 2017
		// $tgl2=date('Y-m-d');
	 }
	
?>
<script src="app_libs/job_sewing_list_v2.js?d=<?php echo date('YmdHis');?>"></script>
<script language="JavaScript" src="calendar_us.js"></script>
<link rel="stylesheet" href="calendar.css">	
	<fieldset style="width=1%">
		<legend><b>List Sewing Order</b></legend>
		<table>
			<tr>
			<input type="button" value="Kembali" onclick="window.location='job_sewing_rekap.php';">
			</tr>
		</table>
		
		<!-- <form method="post" action="?action=search">		
		<table>
			<tr>
				<td><b>No Loading</b></td>
				<td><b>:</b></td>
				<td><input type="text" id="no_load" name="no_load" onclick="showJobLoading(this.id);" size="30"></td>
				<td><input type="button" value="Tambah SO" onclick="window.location='job_sewing_add.php?no_load='+document.getElementById('no_load').value;"></td>
				<td width="50">&nbsp;</td>
				<td>Pencarian <input type="text" name="cari" value="<?php echo $cari?>" size="30" /> </td>
				<td >&nbsp;</td>
			</tr>
			<tr>
			  <td>Tanggal</td>
			  <td>:</td>
			  <td><input type="text" name="tgl1" id="tgl1" value="<?php echo $tgl1; ?>" size="10"/>
			<script language="JavaScript">
			  new tcal ({
				// form name
				'formname': 'outlet',
				// input name
				'controlname': 'tgl1'
			  });
			</script>
			Sampai <input type="text" name="tgl2" id="tgl2" value="<?php echo $tgl2; ?>" size="10"/>
			<script language="JavaScript">
			  new tcal ({
				// form name
				'formname': 'outlet',
				// input name
				'controlname': 'tgl2'
			  });
			</script></td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td >&nbsp;</td>
		  </tr>
			<tr>
			  <td>Status</td>
			  <td>&nbsp;</td>
			  <td><select name="approve">
			    <option value="0" <?php if($app=="0"){echo"selected";}?>>Semua Kondisi</option>
			    <option value="1" <?php if($app=="1"){echo"selected";}?>>Sudah Approve2 </option>
			    <option value="2" <?php if($app=="2"){echo"selected";}?>>Belum Approve2</option>
		      </select></td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td>&nbsp;</td>
			  <td >&nbsp;</td>
		  </tr>
			<tr>
				<td colspan="5"><input type="submit" id="submit" value="Cari" /></td>
				<td colspan="3"><sub>ket*) Pencarian bisa berdasarkan model, no sewing, no co</sub></td>
			</tr>
		</table>
		</form> -->
	</fieldset>
    <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=100;
	$page=$hal;
	$_pabrik=" like '%'";
	if(strtoupper($_SESSION['outlettype'])=="P"){
		$_pabrik=$_SESSION['outlet'];
		if($_pabrik=='P0006'){
			$sql="select id from pabrik where mk='1' ";
			$resri=mysql_query($sql)or die($sql);
			$banyak_pabrik=mysql_num_rows($resri);
			while(list($kd_pabrik)=mysql_fetch_array($resri)){
				$j++;
				if($j==$banyak_pabrik){
					$pabrik.="'$kd_pabrik'";
				}else{
					$pabrik.="'$kd_pabrik',";
				}
			}
			$_pabrik=" in (".$pabrik.")";
		}else{
			$_pabrik=" like '$_pabrik%'";
		}
	}
	$sql_tbhn='';
	
	if(!empty($cari)){
		$sql_tbhn.=" and 	(jl.`no_co` like '%$cari%' or  js.`no_sew` like '%$cari%' or  jl.no_load like '%$cari%' or jl.`nama` like '%$cari%'
		 or jl.pabrik_tujuan like '%$cari%' )" ;
	}
	
	$sql="SELECT SQL_CALC_FOUND_ROWS SQL_CACHE     
				jl.`no_co` as no_co,
				js.`no_sew` as no_sew,
				jsl.`keterangan` AS no_line,
				jl.`no_load` as no_load,
				jl.`nama` as model,
				js.`totalqty` as qty,
				js.`tanggal` as tanggal,
				jl.`pabrik_tujuan` as pabrik_tujuan,
				jl.`pabrik_dari` as pabrik_dari,
				js.`approve` as approve,
				js.`approve2` as approve2,
				js.`approveby` as approveby,
				js.`approveby2` as approveby2
				FROM    `job_sewing`  js  
				LEFT JOIN `job_loading`  jl ON (js.`no_load` = jl.`no_load`)
				LEFT JOIN `job_sewing_line` jsl ON (js.`no_line` = `jsl`.`id`)
				where jl.pabrik_tujuan  $_pabrik   
				and jl.no_co='$no_co'
				and js.no_sew not like '%btl%' $appcode $sql_tbhn
				ORDER BY js.approve,
				js.tanggal DESC,
				no_sew desc limit ".($page*$jmlHal).",".$jmlHal;
		

		// $sql="SELECT * FROM job_sewing ";
		$hsl=mysql_query($sql);
		$sql_count="SELECT FOUND_ROWS();";
			$query=mysql_query($sql_count);//or die($sql);
			list($jmlData)=mysql_fetch_array($query);
		// $no=0;
		$no=($hal*$jmlHal);
	
	
	if($username=='B120938_ahmad'){
	   echo $sql."<br/>";	
	}
	?>
    
	<table border="1" width="100%"  cellspacing="0" cellpadding="0" class="table_q table_q-striped table_q-hover sortable">
      <thead>
		<tr class="header_table_q">
			<td align="center" width="20"   rowspan="2"><b>No</b></td>
			<td align="center" width="150"   rowspan="2"><b>No CO</b></td>
			<td align="center" width="180"   rowspan="2"><b>No SEWING</b></td>
			<td align="center" width="100"   rowspan="2"><b>Model</b></td>
			<td align="center" width="170"   rowspan="2"><b>Pabrik</b></td>
			<!-- <td align="center" width="100"   rowspan="2"><b>No Line</b></td> -->
            <td align="center" width="50"   rowspan="2"><b>QTY</b></td>
			<td align="center" width="50"    colspan="3"><b>Hasil Sewing</b></td> 
			<td align="center" width="140"   rowspan="2"><b>Tanggal</b></td>
			<td align="center" width="80"   nowrap  rowspan="2"><b>Approve I</b></td>
			<td align="center" width="80"   nowrap rowspan="2"><b>Approve II</b></td>
			<td align="center" width="150"   rowspan="2" ><b>Action</b></td>
		</tr>
		<tr class="header_table_q" >
			<td align="center"><strong>Bagus</strong></td>
			<td align="center"><strong>Reject</strong></td>
			<td align="center"><strong>Pending</strong></td>
		</tr>
        </thead>

		<?php
			
			
			
			
			while($rs=mysql_fetch_array($hsl)){
				$no++;
				$no_sew=$rs["no_sew"];
				$no_load=$rs["no_load"];
				$no_line=$rs["no_line"];
				$style=$rs["model"];
				$no_co=$rs["no_co"];
				$pabrik_tujuan=$rs["pabrik_tujuan"];
				$pabrik_dari=$rs["pabrik_dari"];
				 
				if(!$pabrik_tujuan){$pabrik_tujuan=$pabrik_dari;}
				$sql="SELECT nama FROM pabrik WHERE id='$pabrik_tujuan'";
				$hsltemp=mysql_query($sql);
				list($pabrikname)=mysql_fetch_array($hsltemp);
				 
				$tanggal=$rs["tanggal"];
				$totalqty=$rs["qty"]; 
				$approved=$rs["approve"];
				 

				if($approved){
					$status= "<b>App[<font color='#0099FF'>$rs[approveby]</font>]</b>";
				}else{
					$status="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
				$approved2=$rs["approve2"];
				if($approved2){
					$status2= "<b>App[<font color='#0099FF'>$rs[approveby2]</font>]</b>";
				}else{
					$status2="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
                $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
                $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
				$sql="SELECT SUM(qty-reject-pending),sum(reject),sum(pending) FROM  job_sewing_detail WHERE no_sew='$no_sew'";
				if($_SESSION['username']=="rian-it"){
					//echo $sql;
				}
				$res=mysql_query($sql)or die($sql);
				list($bagus,$reject,$pending)=mysql_fetch_array($res);
				if(!$approved2){
					$bagus="-";
					$reject="-";
					$pending="-";
				}
				
		?>
			<tr >
				<td align="center" width="20" ><?php echo $no; ?></td>
				<td align="left" width="150" >&nbsp;<?php echo $no_co; ?></td>
				<td align="left" width="180" >&nbsp;<?php echo $no_sew; ?></td>
				<td align="left" width="100" >&nbsp;<?php echo $style; ?></td>
				<td align="left" width="170" >&nbsp;<?php echo $pabrikname." [$pabrik_tujuan]"; ?></td>
				<!-- <td align="left" width="100" >&nbsp;<?php echo $no_line; ?></td> -->
				<td align="center" width="50" >&nbsp;<?php echo $totalqty; ?></td>
				<td align="center" width="50" >&nbsp;<?php echo $bagus; ?></td>
				<td align="center" width="50" >&nbsp;<?php echo $reject; ?></td>
				<td align="center" width="50"  >&nbsp;<?php echo $pending; ?></td>
				<td align="left" width="140" >&nbsp;<?php echo $tanggal; ?></td>
                <td align="left" width="80" >&nbsp;<?php echo $status; ?></td>
				<td align="left" width="80" >&nbsp;<?php echo $status2; ?></td>
				<td align="left" width="150"  nowrap>&nbsp;
					<a href="job_sewing_detail.php?no_sew=<?php echo $no_sew; ?>">Detail Approvement</a>
				<?php
				
					if($approved2){
						$sql="SELECT j.no_qc FROM job_qc as j 
						inner join job_qc_detail as jq on 
						(j.no_qc=jq.no_qc)
						WHERE j.no_sew='$no_sew'";
						$hsltemp=mysql_query($sql);
						if(mysql_affected_rows($db)>0){
					?>
						|
						<a href="job_qc_list.php?no_sew=<?php echo $no_sew; ?>">Quality Control</a> 
                        
					<?php
						}else{

                                ?> |
                                    <span class="mylink" onclick="syncronization('<?php echo $no_sew?>','<?php echo $no_co?>')"> Perbaiki QC </span>
                                <?php


                        }
					}
				?>				
				</td>
			</tr>
		<?php
			}
		?>
	</table>
	<table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
			<?php echo $terusan="&approve=$_REQUEST[approve]&cari=$cari";?>
            	Page : 
                <span class="hal" onclick="location.href='?x_idmenu=150&hal=0<?php echo $terusan?>';">First</span>
                <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='?x_idmenu=150&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData/$jmlHal)){
						if($i>=(($jmlData/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='?x_idmenu=150&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='?x_idmenu=150&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="location.href='?x_idmenu=150&hal=<?php echo intval(($jmlData/$jmlHal)); ?><?php echo $terusan?>';">Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData; ?> Data
            </td>
        </tr>
    </table>
	<br /><br />

<?php include_once "footer.php" ?>
