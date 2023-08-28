<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Evaluasi PO "; ?>
<?php $lihat=1;
 if($lihat==1){ 
    include('header.php');
 } ?>
<?php
    
    
	$tgl1=$_POST['tgl1'];
	$tgl2=$_POST['tgl2'];
    $model=$_POST['txt_cari'];
	
	if (isset($_GET['action'])) { 
		session_start();
		$tambah="&action=search";
        if($_POST['tgl1']!=''){
              $_SESSION['tgl1']=$_POST['tgl1'];
              $_SESSION['tgl2']=$_POST['tgl2'];
              $tgl1=$_SESSION['tgl1'];
              $tgl2=$_SESSION['tgl2'];
        }else{
              $tgl1=$_SESSION['tgl1'];
              $tgl2=$_SESSION['tgl2'];
        }
		
			
	} else {
		unset($_SESSION['tgl1']);
        unset($_SESSION['tgl2']);
		
		
	}
	
	if(empty($tgl1)){
            $tgl1=date("Y-m-d");
            $tgl2=date("Y-m-d");
        
    } 
   
	
	
	
    ?>
<script src="jquery-latest.js"></script>
<script language="JavaScript">
	$(document).ready(function(){
	    $('.hide').hide();
				
	});
</script>
<script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
   <form method="POST" action="move_from_manufaktur.php?action=search" name="outlet">
		<table>
			<tr>
				
              <td valign="top"> Periode Dari : 
                <script language="JavaScript" src="calendar_us.js"></script>
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
            </script>
                &nbsp;&nbsp;&nbsp;Cari Nama Model 
                <input name="txt_cari" type="text" value="<?=$model?>" >
			<input type="submit" value="Cari Model"/> 
            	<script language="javascript">
				function cari()
				{
					
					awal=outlet.startdate.value;
					akhir=outlet.enddate.value;
					cari1=outlet.cari1.value;
					sendRequest('isi_rekap_wip_produksi.php?awal='+awal+'&akhir='+akhir+'&cari1='+cari1,'isi');
				}
				
				function kembali()
				{
					awal=outlet.startdate.value;
					akhir=outlet.enddate.value;
					cari1=outlet.cari1.value;
					sendRequest('isi_rekap_pengiriram.php?awal='+awal+'&akhir='+akhir,'isi');
				}
				
				function baru(id_barang){
					awal=outlet.startdate.value;
					akhir=outlet.enddate.value;
					cari1=outlet.cari1.value;
					sendRequest('isi_detail_rekap.php?awal='+awal+'&akhir='+akhir+'&id_barang='+id_barang,'isi');
				}
			</script>
				</td>
			</tr>
		</table>
		</form>
</fieldset>
<fieldset>
        <table border="0" width="100%" style="font-size: 8pt">
          <tr> 
            <td background="images/footer.gif" align="center" width="250"><strong>Model</strong></td>
            <td background="images/footer.gif" align="center" width="250" height="22"><b>Qty 
              Po </b></td>
            <td background="images/footer.gif" align="center" width="232"><strong>Qty CO</strong></td>
            <td background="images/footer.gif" align="center" width="232" height="22"><b>Qty 
              DO</b></td>
            <td background="images/footer.gif" align="center" width="218" height="22"><b>Stok 
              Distribusi</b></td>
            <td background="images/footer.gif" align="center" width="160" height="22"><b>Stok 
              Markas &amp; Reshare</b></td>
            <td background="images/footer.gif" align="center" width="105" height="22" class="hide"><b>Qty Penjualan</b></td>
          </tr>
          <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=50;
	$page=$hal;
	if (isset($_GET['action'])) { 
		
        $sql2=" SELECT    SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.model like '%$model%'
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`
                     order by i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item` ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
		$sql3="SELECT FOUND_ROWS()";
		
		
		
		
	} else {
		
		 $sql2=" SELECT    SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and model ='-'
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`   
                     order by i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item` ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
        $sql3="SELECT FOUND_ROWS()";
		
			
		
	}
	
	//echo $sql3.'#'.$sql2;
	//$hsltmp12=mysql_query($sql1,$db) or die('Error euy'.$sql1);
	//$jmlData=mysql_fetch_row($hsltmp12);
   // echo 'Print => jm'.$jmlData[0] .' test';
   
   
    print_r($jmlData);
	$hsltemp2=mysql_query($sql2,$db) or die ('<h1>Error #'.mysql_error()."#$sql2".'</h1>');
    $hsltmp12=mysql_query($sql3,$db) or die ($sql3);
	list($jmlData[0])=mysql_fetch_array($hsltmp12);
	$no=($hal*$jmlHal);
	$stok_persub=0;
	$co_persub=0;
	$pengiriman_persub=0;
	$distribusi_qty_persub=0;
	$markas_qty_persub=0;
	
	
    while ( list($model,$stok,$kode_model,$kode_style,$kode_kelas,$kode_kategori,$kode_basic_item)=mysql_fetch_array($hsltemp2)) {
        $no++;
		
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
		
		
         ?>
          <tr onMouseOver="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>"> 
            <td>&nbsp;<?php echo "[$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model]$model";?></td>
            <td height="21" align="right">&nbsp;<?php echo number_format($stok);$stok_persub+=$stok; ?></td>
            <td align="right"><?php
			   $sql="SELECT  SUM(cd.qty) FROM  job_cutting_detail AS cd INNER JOIN job_cutting AS c 
                     ON (cd.no_co = c.no_co) WHERE  cd.kd_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' 
					 AND  c.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
			   $sql="SELECT   SUM(jd.qty_produk) FROM job_gelaran_detail AS jd INNER JOIN job_gelaran AS j 
        			  ON (jd.no_co = j.no_co) WHERE jd.kd_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' 
					  AND   j.approvedate BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
			   $sql="SELECT   SUM(jd.qty_produk) FROM job_gelaran_detail AS jd INNER JOIN job_gelaran AS j 
        			  ON (jd.no_co = j.no_co) INNER JOIN po_manufaktur p on p.no_manufaktur=j.no_po WHERE jd.kd_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' 
					  AND   p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'";
			   
			  // echo $sql;
			  $res_co=mysql_query($sql) or die('Error '. $sql);      
              list($co)=mysql_fetch_array($res_co);     
			  if($co==''){
				  $co=0;
			  }
			  $co_persub+=$co;  
			  echo number_format($co);
			
			?></td>
            <?php
                      /* $sql="SELECT  ifnull(SUM(md.qty),0) FROM  `do_produk_detail` AS `md`  INNER JOIN `do_produk` AS `m` 
                                ON (`md`.`no_do` = `m`.`no_do`) WHERE `md`.`kd_produk` LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%'
                                AND `m`.tanggal BETWEEN  '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.no_do not like 'BTL%'";
						*/		
								$sql=" 
 SELECT ifnull(SUM(md.qty),0) FROM do_produk_detail AS md INNER JOIN do_produk AS m ON
 (md.no_do=m.no_do)
LEFT JOIN mst_model_fix AS f ON 
(f.kode_model=SUBSTRING(md.kd_produk,1,7))
WHERE md.kd_produk IN 
(SELECT kd_produk FROM  po_manufaktur_detail AS pmd 
INNER JOIN po_manufaktur AS pm ON 
(pm.no_manufaktur=pmd.no_manufaktur) WHERE pm.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59'
) AND m.tanggal BETWEEN  '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND `md`.`kd_produk` LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%'";
                       //echo $sql;
                        $res=mysql_query($sql) or die('Error '. $sql);      
                        list($pengiriman)=mysql_fetch_array($res);  
						$pengiriman_persub+=$pengiriman;
    
            
?>
            
            <td height="21" align="right"><?php echo number_format($pengiriman); ?></td>
            <?php
                     $sql_dist="SELECT IFNULL(SUM(stok),0) FROM produk_stok WHERE kode_gudang LIKE 'GD%' AND kode_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' and stok>0";
                     $res_dis=mysql_query($sql_dist) or die('Error '. $sql_dist);      
                     list($distribusi_qty)=mysql_fetch_array($res_dis);  
					 $distribusi_qty_persub+=$distribusi_qty;
            ?>	
            <td height="21" align="right"><?php echo number_format($distribusi_qty); ?></td>
            <?php
                    $sql_otl="SELECT ifnull(SUM(stok),0) FROM outlet_stok WHERE kode_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' AND jenis='1' and stok>0";
                     $res_otl=mysql_query($sql_otl) or die('Error '. $sql_otl);      
                     list($markas_qty)=mysql_fetch_array($res_otl);
					 $markas_qty_persub+=$markas_qty;
            ?>
            <td height="21" align="right"><?php echo number_format($markas_qty); ?></td>
            <?php
                     // Yang B Tampilkan
                     if(substr($kode_basic_item,0,1)=='B'){
                            $terjual= $pengiriman - $distribusi_qty  -$markas_qty; 
                     }else{
                          $terjual= '';
                     }
                     
                        
           
       
            ?>
            <td height="21" align="right" class="hide">&nbsp;<?php echo $terjual; ?></td>
          </tr>
          <?php
	}
   
    ?>
          <tr> 
            <td height="23" background="images/notupload.jpg" align="center"><i><b>SUBTOTAL</b></i></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($stok_persub); ?></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php  echo number_format($co_persub);?></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($pengiriman_persub); ?></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($distribusi_qty_persub); ?></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($markas_qty_persub); ?></td>
            
            <td height="23" background="images/notupload.jpg" width="105" align="right" class="hide">&nbsp;<b><?php echo number_format($totalbrssubtotal); ?></b></td>
          </tr>
          <tr> 
            <td height="25" background="images/yesupload.jpg" align="center">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg" width="105" align="right" class="hide">&nbsp;<font color=red><b><?php echo number_format($jmlData[3]); ?></b></font></td>
          </tr>
        </table>
<table style="margin-left:10px; margin-top:10px;">
				  <tr>
						<td class="text_standard">
							Page : 
							<span class="hal" onClick="location.href='move_from_manufaktur.php?&hal=0<?php echo $tambah?>';">First</span>
							<?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
								if($hal<=0){ ?>
									<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='move_from_manufaktur.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
									if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='move_from_manufaktur.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='move_from_manufaktur.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
							<span class="hal" onClick="location.href='move_from_manufaktur.php?&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?><?php echo $tambah?>';">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>						</td>
				</tr>
</table>
</fieldset>

<?php include_once "footer.php"; ?>