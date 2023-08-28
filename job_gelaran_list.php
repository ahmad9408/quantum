<?php $content_title="DAFTAR PPIC"; include_once "header.php"  ?>
<?php
	$no_po			= sanitasi($_GET["no_po"]);
	$iden 			= $_POST['iden'];
	$approv			= $_POST['approv'];
	$no_po			= $_POST['no_po'];
	$no_co			= $_POST['no_co'];
	$pabrik			= $_POST['pabrik'];
	$no_co_mapping	= $_POST['no_co_mapping'];
	$kode_md 		= $_POST['model'];
	if($kode_md!=""){
			$terusan_model 	= " and  job_gelaran.model like '%$kode_md%' ";
	}
	 
	 
	
	if($approv!=""){
		$terusan_approv		= " and job_gelaran.approveby like '%$approv%' ";
	} 
	
	if($iden==1){
		$terusan_iden 		= "  and  `job_gelaran`.`no_co_mapping` !='' ";	
	}else if($iden==2){
		$terusan_iden 		= " and  `job_gelaran`.`no_co_mapping` ='' ";	
	}else{
		$terusan_iden 		= "";	
	}
	 
	
	if($_REQUEST[awal]==""){
		
	}else{
		$awal=$_REQUEST[awal];
		$akhir=$_REQUEST[akhir];
		$qtanggal=" and `job_gelaran`.`updatedate` between '$awal 00:00:00' and '$akhir 23:59:59'";
	}
	
	
	
?>
<style>
.mylink {
	cursor:pointer;
	color:#00C;	
}
</style>


	<script language="JavaScript">
		var detailsWindow;
		function showManufaktur(textid) {
			detailsWindow = window.open("window_manufaktur.php?textid="+textid,"window_manufaktur","width=800,height=600,scrollbars=yes");
			detailsWindow.focus();   
		}
	</script>
	<fieldset style="width=1%">
		<legend><b>Tambah CO</b></legend>
       	
        <form id="gelaran" name="gelaran" method="post" action="job_gelaran_list.php?action=search">	
        <table border="0" width="1000" cellpadding="0" cellspacing="0">
        <tr>
        	<td width="150"><strong>No Manufaktur</strong></td>
        	<td width="2">:</td>
        	<td><input type="text" id="no_manufaktur" name="no_manufaktur" onclick="showManufaktur(this.id);" size="30">
            <input type="button" value="Tambah CO" onclick="window.location='job_gelaran_add.php?no_manufaktur='+document.getElementById('no_manufaktur').value;" />
            </td>
        </tr>
        <tr>
        	<td height="2">&nbsp;</td>
        </tr>
        <tr>
        	<td width="150" colspan="3"> 
            <table cellpadding="0" cellspacing="0">
            	<tr>
                	<td valign="top">
                    	<table>
                        	<tr>
                            	<td>No PO</td>
                            	<td width="2">:</td>
                            	<td>  <input name="no_po" type="text" id="no_po" value="<?php echo $no_po;?>"  size="30" /> </td>
                            </tr>
                            <tr>
                            	<td>No CO</td>
                            	<td width="2">:</td>
                            	<td>  <input name="no_co" type="text" id="no_co" value="<?php echo $no_co;?>"  size="30" /> </td>
                            </tr>
                            
                             <tr>
                            	<td>Pabrik</td>
                            	<td width="2">:</td>
                            	<td>  <input name="pabrik" type="text" id="pabrik" value="<?php echo $pabrik;?>"  size="30" /> </td>
                            </tr>
                            
                            
                             <tr>
                            	<td>Model</td>
                            	<td width="2">:</td>
                            	<td>  <input name="model" type="text" id="model" value="<?php echo $kode_md;?>"  size="30" /> </td>
                            </tr>
                            
                             
                            
                          
                            
                      </table>
                    </td>
                	<td width="50">&nbsp;</td>
                	<td valign="top">
                    	<table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            	<td width="200">NO CO Mapping</td>
                            	<td width="2">:</td>
                            	<td>  <input name="no_co_mapping" type="text" id="no_co_mapping" value="<?php echo $no_co_mapping;?>"  size="30" /> </td>
                            </tr>
                          <tr>
                            	<td>Identitas Kode C</td>
                                <td>:</td>
                                <td> <select id="iden" name="iden">
                                        <option value="">Pilih</option>
                                        <option value="1" <?php if($iden==1){echo"selected";}?>>Ada</option>
                                        <option value="2" <?php if($iden==2){echo"selected";}?>>Belum Ada</option>
                                        </select> 
                              </td>
                            </tr>
                            
                            
                             <tr>
                            	<td>Approve By</td>
                                <td>:</td>
                                <td><input type="text" id="approv" name="approv" value="<?php echo $approv?>" /></td>
                            </tr>
                        <tr>
                        	<td>Dari</td>
                        	<td width="2">:</td>
                       	  	<td width="300"><script language="JavaScript" src="calendar_us.js"></script>
                                    <link rel="stylesheet" href="calendar.css">
                                    <!-- calendar attaches to existing form element -->
                                    
                                    <input type="text" name="awal"  id="awal" value="<?php echo $awal; ?>" size="16"/> &nbsp;
                                     
                                    <script language="JavaScript">
                                      new tcal ({
                                        // form name
                                        'formname': 'gelaran',
                                        // input name
                                        'controlname': 'awal'
                                      });
                                    </script>
                                    
                                </td>
                        </tr>
                        
                        <tr>
                        	<td>Sampai</td>
                            <td>:</td>
                            <td><input type="text" name="akhir"  id="akhir" value="<?php echo $akhir; ?>" size="16"/> &nbsp;
			
										<script language="JavaScript">
                                          new tcal ({
                                            // form name
                                            'formname': 'gelaran',
                                            // input name
                                            'controlname': 'akhir'
                                          }); 
                                        </script></td>
                        </tr>
                        </table>
                    
                    </td>
                </tr>
                <tr>
                	<td><input type="submit" name="Cari" id="Cari" value="Cari Model" /></td>
                </tr>
            </table>
            
            </td>
        </tr>
        
        </table>
      </form>
    </fieldset>
    
    <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=50;
	$page=$hal;
	/*
	$sql="select count(*) from job_gelaran";
	$query=mysql_query($sql,$link);
	$jmlData=mysql_fetch_row($query);
	*/
	?>
 <table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8">
    <tr>
        <td align="center" width="48" bgcolor="#99CC00" height="24"><b>No</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="160"><b>No PO Manufaktur</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="160"><b>No CO</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="160"><b>Mapping NO CO</b></td>
        <td align="center" bgcolor="#99CC00" height="24"><b>Model</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="180"><b>Pabrik</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="80"><b>Qty</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="136"><b>Tanggal</b></td>
		<td align="center" bgcolor="#99CC00" height="24"><b>Umur<br />(Hari)</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="194"><b>Approve I</b></td>
		<td align="center" bgcolor="#99CC00" height="24" width="100"><b>Real Cutting</b></td>
        <td align="center" width="87" bgcolor="#99CC00" height="24"><b>Action</b></td>
    </tr>
		<?php
			$_pabrik="";
			if(strtoupper($_SESSION['outlettype'])=="P"){
				$_pabrik=$_SESSION['outlet'];
			}

if($no_co_mapping!=""){
	$terusan_mapping 	= " and job_gelaran.no_co_mapping like '$no_co_mapping%'  ";	
}				
$sql="SELECT
    DISTINCT(`job_gelaran`.`no_co`)
    , `job_gelaran`.`seqno`
    , `job_gelaran`.`no_po`
    , `job_gelaran`.`pabrik`
    , `pabrik`.`nama`
    , `job_gelaran`.`approve`
    , `job_gelaran`.`updatedate`
    , `job_gelaran`.`approveby`
    , `job_gelaran`.`approvedate`
	, `job_gelaran`.`model`
	, `job_gelaran`.`no_co_mapping`
	,DATEDIFF('" .date("Y-m-d") ."',`job_gelaran`.`updatedate`) as usia 
FROM
    `job_gelaran`
    LEFT JOIN `pabrik` 
        ON (`job_gelaran`.`pabrik` = `pabrik`.`id`) 
         
        where   
		
		job_gelaran.no_po like '%$no_po%' and 
		job_gelaran.no_co like '%$no_co%' and 
		`pabrik`.`nama` like '%$pabrik%'  $terusan_mapping   
		 $terusan_iden   $qtanggal $terusan_approv  $terusan_model ORDER BY 
        `job_gelaran`.`approve`, `job_gelaran`.`updatedate` desc ";
		if($_SESSION['username']=="rian-it"){
		echo $sql;	
		}
		 	
$query=mysql_query($sql)or die($sql);
$jmlData[0]=mysql_num_rows($query);

$sql.=" LIMIT  ".($page*$jmlHal).",".$jmlHal;
			
			// $sql;
			echo "<!-- $sql -->";
			
			$hsl=mysql_query($sql,$db)or die($sql);
			
			
			$no=($hal*$jmlHal);
			while($rs=mysql_fetch_array($hsl)){
			if($rs['model']==""){
				
			}
				$no++;
				$no_po=$rs["no_po"];
				$no_co=$rs["no_co"];
				$pabrik=$rs["pabrik"];
				$sql="SELECT nama FROM pabrik WHERE id='$pabrik'";
				$hsltemp=mysql_query($sql,$db);
				list($pabrikname)=mysql_fetch_array($hsltemp);
				$updateby=$rs["updateby"];
				$updatedate=$rs["updatedate"];
				$approved=$rs["approve"];
				$approvedby=$rs["approveby"];
				$catatan=$rs["no_co_mapping"];
                $sql1="SELECT sum(qty_produk) FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co'";
                $hsltemp=mysql_query($sql1,$db);
				$usia=$rs["usia"];
                list($qty)=mysql_fetch_array($hsltemp);	
// Edited Bye Goberan
                                $sql="SELECT kd_produk FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co'";
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
								$sty=str_replace("'","",$style);
								if($rs['model']==""){
									$sql="update job_gelaran set model='$sty' where no_co='$no_co' and no_po='$no_po'";
									$resri=mysql_query($sql)or die($sql);
								}
								
								$sql="select approve2 from job_cutting where no_po='$no_po' and no_co='$no_co'";
								$resri=mysql_query($sql)or die($sql);
								list($approve2co)=mysql_fetch_array($resri);
								
			
	if($approved){
					$status= "<b>Approved - [$approvedby]</b>";
				}else{
					$status="<blink><font color='red'><b>Belum di Approve</b></font></blink>";
				}
          $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
          // $bgclr1 = ""; $bgclr2 = "";
          
          $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;      	
		?>
        <tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
        <td align="center" width="48" height="20"><?php echo $no; ?></td>
        <td align="left" height="20" width="160">&nbsp;<?php echo $no_po; ?></td>
        <td align="left" height="20" width="160" id="r<?php echo $no?>">&nbsp;<?php echo $no_co; ?></td>
        <td align="center" height="20" width="160" >&nbsp;<?php echo $catatan; ?></td>
        <td align="left" height="20">&nbsp;<?php echo $style; ?></td>
        <td align="left" height="20" width="180">&nbsp;<?php echo $pabrikname." [$pabrik]"; ?></td>
        <td align="right" height="20" width="80">&nbsp;<?php echo number_format($qty,2,",","."); ?></td>
        <td align="center" height="20" width="136"><?php echo $updatedate; ?></td>
		 <td height="20" align="center">&nbsp;<?php if($usia>2){ 
		 if($approve2co=='1'){
		 echo $usia;
		 }else{
		 echo"<font color='#FF0000'><blink>".$usia."</blink></font>"; }
		 }else{
		 	echo $usia;
		 } ?></td>
        <td align="left" height="20" width="194">&nbsp;<?php echo $status; ?></td>
		<td  height="20"  align="center"><?php if($approve2co=='1'){
			echo"<font color='#000000'>Yes</font>";
		}else{
			echo"<font color='#FF0000'><blink>No</blink></font>";
		}?></td>
        <td align="center" width="87" height="20"><a href="job_gelaran_detail_4.php?no_po=<?php echo $no_po; ?>&no_co=<?php echo $no_co; ?>&real=<?php echo $approve2co ?>">Detil</a> <?php if($_SESSION['username']=="rian-it"){
			?>
				 | <span class="mylink" onclick="hapus('<?php echo $no?>')">Hapus</span> 
			<?php
			}?></td>
    </tr>
		<?php
			}
			
			$terusan="&model=$kode_md&awal=$awal&akhir=$akhir";
		?>
	</table>
    <table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
            	Page : 
                <span class="hal" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?><?php echo $terusan?>&hal=0';">First</span>
                <?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?><?php echo $terusan?>&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
						if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?><?php echo $terusan?>&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?><?php echo $terusan?>&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?><?php echo $terusan?>&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?>';">Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData[0]; ?> Data
            </td>
        </tr>
    </table>
    <br /><br />
    <script>
    function hapus(no){
		var no_co	= $("#r"+no).text().trim();
		var data 	= "no_co="+no_co+"&proses=hapus_co";
	 
		$.post("job_gelaran_ubah_bahan.php",data,function(response){
			alert(response);
			if(response.trim()=="berhasil"){
				alert('no co sudah di hapus semua');	
			}
		});
	}
    </script>
<?php include_once "footer.php" ?>
