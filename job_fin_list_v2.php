<?php $content_title="DAFTAR FINISHING"; include_once "header.php";
include('DateControl.php');
 ?>
<?php
//  TAmbah filter
   //betulkan paging  09012015
	$no_qc=sanitasi($_GET["no_qc"]);
	$tahun_skrg=date('Y');
	$bulan_skrg=date('m');
    $array_bulan = array('01'=>'Januari','02'=>'Februari','03'=>'Maret', '04'=>'April', '05'=>'Mei',
 	'06'=> 'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober', '11'=>'November','12'=>'Desember');
	
   if (isset($_GET['hal'])) { 	
          $limit=$_SESSION['limit'];	      
		  $bulan1=$_SESSION['bulan1'];
		  $tahun1=$_SESSION['tahunl'];
		  $barcode_model=$_SESSION['barcode_model'];
		  $model=$_SESSION['model'];			 
		  $tgl1=$_SESSION['tgl1'];
		  $tgl2=$_SESSION['tgl2'];
		  $tujuan=$_SESSION['tujuan'];
		  $tambah="&action=search";
			       
	}elseif (isset($_GET['action'])) { 
		session_start();
		$tambah="&action=search";
       
              $_SESSION['tgl1']=$_POST['tgl1'];
              $_SESSION['tgl2']=$_POST['tgl2'];
			  $_SESSION['limit']=sanitasi(trim($_POST['limit']));
			  $_SESSION['model']=sanitasi(trim($_POST['txt_cari']));
			  $model=$_SESSION['model'];
			  $bulan1=$_POST['bulan1'];
              $tahun1=$_POST['tahunl'];			 
              $tgl1=$_SESSION['tgl1'];
              $tgl2=$_SESSION['tgl2'];
			  $limit=$_SESSION['limit'];	
			  
			  $_SESSION['bulan1']=$bulan1;
		      $_SESSION['tahunl']=$tahun1;
			  $tujuan=$_POST['tujuan'];
			  $_SESSION['tujuan']=$tujuan;
			  
			  $barcode_model=$_POST['barcode_model'];
			  $_SESSION['barcode_model']=$barcode_model;
			  
       
	} else {
		unset($_SESSION['tgl1']);
        unset($_SESSION['tgl2']);
		unset($_SESSION['bulan1']);
		unset($_SESSION['tahunl']);
		unset($_SESSION['tujuan']);
		$bulan1=$bulan_skrg;
	    $tahun1=$tahun_skrg;
		$tujuan='suho';//default
		$limit=200;	
	}
	
  if(empty($tgl1)){
	  $dc=new DateControl();
    $tgl1=date("Y-m-01");
	$h=$dc->jumlahHari(date('m'),date('Y'));		
    $tgl2=date("Y-m-").$h;
        
  } 
  $tujuan=$_POST['tujuan'];
  
 #echo "<h3>$tahun1 $bulan1</h3>";

?>
	
	

	
<script type="text/javascript" src="date.js"></script>
<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="sortable.js"></script> 
<script language="JavaScript" src="calendar_us.js"></script>
<script language="JavaScript" src="app_libs/job_fin_list_v2.js"></script>
<script>
var tgl1='<?php echo $tgl1 ?>';
var tgl2='<?php echo $tgl2 ?>';
var model='<?php echo $model; ?>';
var barcode_model='<?php echo $barcode_model; ?>';
var tujuan='<?php echo $tujuan; ?>'; 
var format_tgl_javascript='yyyy-MM-dd';

</script>

<fieldset>
<form method="POST" action="<?php echo $PHP_SELF; ?>?action=search" name="outlet">
    <table class="otomatis">
			<tr>
			  <td width="117" valign="top">Bulan</td>
			  <td width="7" valign="top">:</td>
			  <td width="552" valign="top"><select name="bulan1" id="bulan1">
					  <?php 
						 foreach($array_bulan as $key => $value){
							 if($key==$bulan1){
								 echo  "<option value='$key' selected>$value</option>";
							 }else{
								 echo  "<option value='$key'>$value</option>";
							 }							
						 }
              
              ?>
            </select></td>
	      </tr>
			<tr>
			  <td valign="top">Tahun </td>
			  <td valign="top">:</td>
			  <td valign="top"><select name="tahunl" id="tahun1">
                      <?php 
						 $tahun=1996;
						 
						 for($i=1;$i<100;$i++){
							 $tahun++; 
							 if($tahun==$tahun1){
								 echo  "<option value='$tahun' selected='selected'>$tahun</option>";
							 }else{
								 echo  "<option value='$tahun'> $tahun </option>";
							 }
						 }
              		?>
                   
              </select></td>
	      </tr>
			<tr>
				
              <td valign="top"> Periode Dari </td>
              <td valign="top">: </td>
              <td valign="top">
            <link rel="stylesheet" href="calendar.css">
            <!-- calendar attaches to existing form element -->
            <input type="text" name="tgl1" readonly id="tgl1" value="<?php echo $tgl1; ?>" size="16"/> &nbsp;
			 
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl1'
              });
            </script>
			 &nbsp;
			 &nbsp;&nbsp;&nbsp;&nbsp;
			 
			 <input type="text" name="tgl2" readonly id="tgl2" value="<?php echo $tgl2; ?>" size="16"/> &nbsp;
			
            <script language="JavaScript">
              new tcal ({                                                         
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl2'
              }); 
            </script></td>
              
			</tr>
			<tr>
			  <td valign="top">Nama Model              </td>
			  <td valign="top">&nbsp;</td>
			  <td valign="top"><input name="txt_cari" type="text" value="<?php echo $model?>" /></td>
	      </tr>
			<tr>
			  <td valign="top">Kode Model</td>
			  <td valign="top">:</td>
			  <td valign="top"><input name="barcode_model" type="text" value="<?php echo $barcode_model?>" /></td>
		  </tr>
			<tr style="display:none">
			  <td valign="top">Pilihan</td>
			  <td valign="top">:</td>
			  <td valign="top"><select name="tujuan" size="1" id="tujuan">
			    <?php  
				   $arrayJenis=array(''=>'ALL','suho'=>'Suho','supplier'=>'Supplier' );
				   foreach($arrayJenis as $key=>$value){
					   if($key==$tujuan){
						    echo "<option value='$key' selected='selected'>$value</option>";
					   }else{
						    echo "<option value='$key'>$value</option>";
					   }
					   
				   }
				
				?>
		      </select></td>
		  </tr>
			<tr >
			  <td valign="top">Data Per Hal</td>
			  <td valign="top">:</td>
			  <td valign="top"><input type="text" name="limit" id="limit" value="<?php echo $limit; ?>" /></td>
	  </tr>
			<tr>
			  <td valign="top">&nbsp;</td>
			  <td valign="top">&nbsp;</td>
			  <td valign="top"><input type="submit" value="Cari"/></td>
		  </tr>
		</table>
</form>
</fieldset>
    <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=$limit;
	$page=$hal;
	
	
	$filter='';
	$inner_join='';
	if(!empty($model)){
		$inner_join.=" INNER JOIN job_fin_detail jfd on jf.no_fin=jfd.no_fin ";
		$inner_join.=" inner join mst_model_fix mmf on mmf.kode_model= SUBSTRING(jfd.kd_produk,1,7)";
		$filter.=" AND mmf.nama_model like '%$model%'";			}
	if(!empty($barcode_model)){
		$inner_join.=" INNER JOIN job_fin_detail jfd on jf.no_fin=jfd.no_fin ";
		$filter.=" AND jfd.kd_produk like '$barcode_model%'";
	}
	$sql="select count(jf.tanggal) from job_fin jf $inner_join WHERE jf.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' $filter;";
	if($username=='budi-it'){
	   echo "<3>$sql</h3>";	
	}
	
	$query=mysql_query($sql);
	$jmlData=mysql_fetch_row($query);
	?>
    
	<table border="1" width="100%" style="font-size: 10pt" height="68" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8">
		<tr>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No CO</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>No FINISHING</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Model</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Pabrik</b></td>
            <td align="center" width="20" bgcolor="#99CC00" height="20"><b>QTY</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Tanggal</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20" nowrap><b>Approve I</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20" nowrap><b>Approve II</b></td>
			<td align="center" width="20" bgcolor="#99CC00" height="20"><b>Action</b></td>
		</tr>
		<?php
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
			
			
			
			
			
			$sql="SELECT jf.* FROM job_fin jf $inner_join WHERE jf.no_qc LIKE '%$no_qc' AND ";
			$sql.="no_qc IN (SELECT no_qc FROM job_qc WHERE no_sew IN (SELECT no_sew FROM job_sewing WHERE no_load IN (SELECT no_load FROM job_loading WHERE pabrik_tujuan  $_pabrik ))) $filter AND jf.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' ORDER BY jf.approve,jf.tanggal DESC,jf.no_fin limit ".($page*$jmlHal).",".$jmlHal;
			
			
			/*$sql="SELECT    `jf`.`no_fin` , `jf`.`no_qc`, `jf`.`tanggal` , `jf`.`totalqty` , `jf`.`totalrp` , `jf`.`coa`
    , `jf`.`gudang` , `jf`.`approve` , `jf`.`approveby` , `jf`.`approvedate` , `jf`.`approve2` , `jf`.`approveby2`
    , `jf`.`approvedate2`
FROM  job_fin AS jf INNER JOIN job_qc jq ON (`jq`.`no_qc` = `jf`.`no_qc`)  
INNER JOIN `job_sewing` AS `js`   ON (`js`.`no_sew` = `jq`.`no_sew`)
INNER JOIN `job_loading` AS `jl`   ON  js.no_load=js.no_load
INNER JOIN `job_cutting` AS `jc` ON js.no_co=jl.no_co WHERE jl.pabrik_tujuan $_pabrik
ORDER BY approve,tanggal DESC,no_fin LIMIT ".($page*$jmlHal).",".$jmlHal;*/
			if($username=='budi-it'){
			   echo "<h4>$sql</h4>";	
			}
			#die();
			$hsl=mysql_query($sql);
			$no=($hal*$jmlHal);
			$totalAllQty=0;
			while($rs=mysql_fetch_array($hsl)){
				
				$no_fin=$rs["no_fin"];
				$no_qc=$rs["no_qc"];
				$sql="SELECT no_sew FROM job_qc WHERE no_qc='$no_qc'";
				$hsltemp=mysql_query($sql,$db);
				list($no_sew)=mysql_fetch_array($hsltemp);
				$sql="SELECT no_load FROM job_sewing WHERE no_sew='$no_sew'";
				$hsltemp=mysql_query($sql,$db);
				list($no_load)=mysql_fetch_array($hsltemp);
				$sql="SELECT no_co,pabrik_dari,pabrik_tujuan FROM job_loading WHERE no_load='$no_load'";
				$hsltemp=mysql_query($sql,$db);
				list($no_co,$pabrik_dari,$pabrik_tujuan)=mysql_fetch_array($hsltemp);
				
				if(!$pabrik_tujuan){$pabrik_tujuan=$pabrik_dari;}
				$sql="SELECT nama FROM pabrik WHERE id='$pabrik_tujuan'";
				$hsltemp=mysql_query($sql,$db);
				list($pabrikname)=mysql_fetch_array($hsltemp);
				
				$sql="SELECT no_jo,no_po FROM job_cutting WHERE no_co='$no_co'";
				$hsltemp=mysql_query($sql,$db);
				list($no_jo,$no_po)=mysql_fetch_array($hsltemp);
				$tanggal=$rs["tanggal"];
				$totalqty=$rs["totalqty"];
				
				$totalAllQty+=$totalqty;
				
				$jumlah=$rs["totalrp"];
				$approved=$rs["approve"];
// Edited Bye Goberan
                                $sql="SELECT kd_produk FROM job_cutting_detail WHERE no_co='$no_co'";
                                $hsltemp=mysql_query($sql,$db);
                                list($kd_produk)=mysql_fetch_array($hsltemp);

                                $sql="SELECT * FROM produk WHERE kode = '$kd_produk'";
                                $hsltemp=mysql_query($sql,$db);
                                $rsa=mysql_fetch_array($hsltemp);
                                $kode=$rsa["kode"];
                                $kode_basic_item=$rsa["kode_basic_item"];
                                $kode_kategori=$rsa["kode_kategori"];
                                $kode_kelas=$rsa["kode_kelas"];
                                $kode_style=$rsa["kode_style"];
                                $kode_warna=$rsa["kode_warna"];
                                $kode_model=$rsa["kode_model"];
                                $sql="SELECT model FROM mst_model WHERE kode='$kode_model' AND kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
                                $hsltemp=mysql_query($sql,$db);
                                list($style)=mysql_fetch_array($hsltemp);

				if($approved){
					$status= "<b>Approved</b>";
				}else{
					$status="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
				$approved2=$rs["approve2"];
				if($approved2){
					$status2= "<b>Approved</b>";
				}else{
					$status2="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
                $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
                if($pabrik_tujuan!='P1000'){ $no++;
		?>
			<tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
				<td align="center" width="20" height="20"><?php echo $no; ?></td>
				<td align="left" width="150" height="20">&nbsp;<?php echo $no_co; ?></td>
				<td align="left" width="170" height="20">&nbsp;<?php echo $no_fin; ?></td>
				<td align="left" width="120" height="20">&nbsp;<?php echo $style; ?></td>
				<td align="left" width="170" height="20">&nbsp;<?php echo $pabrikname." [$pabrik_tujuan]"; ?></td>
                <td align="center" width="50" height="20"><?php echo number_format($totalqty); ?></td>
				<td align="left" width="140" height="20">&nbsp;<?php echo $tanggal; ?></td>
				<td align="center" width="120" height="20"><?php echo $status; ?></td>
				<td align="center" width="120" height="20"><?php echo $status2; ?></td>
				<td align="left" width="120" height="20" nowrap>&nbsp;
					<a href="job_fin_detail.php?no_fin=<?php echo $no_fin; ?>">Detil</a>
				<?php
					if($approved2){
						$sql="SELECT no_do FROM do_produk WHERE no_fin='$no_fin'";
						$hsltemp=mysql_query($sql,$db);
						if(mysql_affected_rows($db)>0){
					?>
						|
						<a href="do_produk_list.php?no_fin=<?php echo $no_fin; ?>">DO PRODUK</a>
					<?php
						}
					}
				?>				
				</td>
			</tr>
			
		<?php }
			}
		?>
        <tr bgcolor="#99CC00">
			  <td align="center" height="20">&nbsp;</td>
			  <td align="left" height="20">&nbsp;</td>
			  <td align="left" height="20">&nbsp;</td>
			  <td align="left" height="20">&nbsp;</td>
			  <td align="left" height="20">&nbsp;</td>
			  <td align="center" height="20"><?php echo number_format($totalAllQty); ?></td>
			  <td align="left" height="20">&nbsp;</td>
			  <td align="center" height="20">&nbsp;</td>
			  <td align="center" height="20">&nbsp;</td>
			  <td align="left" height="20" nowrap>&nbsp;</td>
	  </tr>
	</table>
    
    <table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
            	Page : 
              <span class="hal" onclick="location.href='<?php echo $PHP_SELF; ?>?action=search&x_idmenu=153&hal=0';">First</span>
                <?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
					if($hal<=0){ ?>
			  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $PHP_SELF; ?>?action=search&x_idmenu=153&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
						if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
			  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $PHP_SELF; ?>?action=search&x_idmenu=153&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
	  <?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
	  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $PHP_SELF; ?>?action=search&x_idmenu=153&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
      <span class="hal" onclick="location.href='<?php echo $PHP_SELF; ?>?action=search&x_idmenu=153&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?>';">Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($jmlData[0]); ?>
            </td>
        </tr>
    </table>
    <br /><br />
<?php include_once "footer.php" ?>
