<?php $content_title="DAFTAR PERMINTAAN MANUFAKTUR"; 


include_once "header.php";
 include_once("DateControl.php");
 $cari=$_POST['cari'];
 
 $thisPage=$_SERVER['PHP_SELF'];
   //Dirubah terakhir by budi tgl 28 ags 2013
   //di ganti setting tanggal dan counter halamannya error
	$no_manufaktur=sanitasi($_GET["no_manufaktur"]);
    
    if($_GET[action]=="delete"){
        $no_manufaktur=$_GET[no_manufaktur];
        ?>
            <script language="javascript">
                alert("Apakah Yakin Akan Menghapus PO : <?php echo $no_manufaktur; ?> ");
                window.location="<?php echo $thisPage; ?>";
            </script>
        <?php
        
        $sql="update po_manufaktur set closeco=1,closecoby='$username',closedate=NOW(),closedesc='batal' WHERE no_manufaktur='$no_manufaktur'";
        $hsltemp=mysql_query($sql,$db);
        exit;
    }


	   if (isset($_GET['hal'])) { 		      
			  $tgl1=$_SESSION['tgl1'];
			  $tgl2=$_SESSION['tgl2'];
			 		 
			  $tambah="&act=search";
					   
	  }elseif (isset($_GET['act'])) { 
			session_start();
			$tambah="&act=search";
		   
			  $tgl1=sanitasi($_POST['tgl1']);
			  $tgl2=sanitasi($_POST['tgl2']);
			  $_SESSION['tgl1']=$tgl1;
			  $_SESSION['tgl2']=$tgl2;
				 
		   
		} else {
			unset($_SESSION['tgl1']);
			unset($_SESSION['tgl2']);
			
			$bulan1=$bulan_skrg;
			$tahun1=$tahun_skrg;
		}	
	 if(empty($tgl1)){
		$tgl1=date("Y-m-01");
		$dc=new DateControl();
		$tgl2=$dc->lastOfMonth(date('m'),date('Y'));
		
			
	  } 	


		if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
		$jmlHal=100;
		$page=$hal;
		
		?>    
   <?php 
  
     
    $lanjut=" and po_manufaktur.tanggal between '$tgl1 00:00:00' and '$tgl2 23:59:59' ";
 
   ?>         
   <form method="POST" action="<? echo $thisPage;?>?act=search" name="outlet">         
	<table>
		<tr>
			<td><input type="button" value="Tambah Manufaktur" onclick="window.location='po_manufaktur_add.php';"> 
           &nbsp;&nbsp;&nbsp;Pencarian <input type="text" id="cari" name="cari" size="30" value="<?php echo $cari?>"  /> &nbsp;&nbsp;&nbsp;&nbsp;Dari :    <script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
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
             &nbsp;
             
             <input type="text" name="tgl2" readonly id="tgl2" value="<?php echo $tgl2; ?>" size="16"/> &nbsp;
            
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'tgl2'
              }); 
            </script>&nbsp;&nbsp;&nbsp;&nbsp;<!--pencarian <input type="text" name="cari" value="" size="25 "> -->
            <input type="submit" name="submit" value="Cek"></td>
		</tr>
	</table>
    </form>
<table border="1" width="100%" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" height="72">
    <tr>
        <td align="center" bgcolor="#99CC00" height="21"><b>No</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="144"><b>No Manufaktur</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="140"><b>Tanggal</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="153"><b>Model</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="71"><b>Total Qty</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="116"><b>Jumlah (Rp)</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="71"><b>Sisa</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="180"><b>Approve I</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="180"><b>Approve II</b></td>
        <td align="center" bgcolor="#99CC00" height="21" width="210"><b>Action</b></td>
    </tr>
		</tr>
		<?php
			$sql="SELECT SQL_CALC_FOUND_ROWS no_manufaktur, no_po, tanggal, no_vendor, keterangan, totalqty, IFNULL(totalrp,0) AS totalrp, totalqtyok,
  				totalrpok, closeco, closecoby, closedate, closedesc, approve, approveby, approvedate,
  				approve2, approveby2, approvedate2 FROM po_manufaktur WHERE no_manufaktur LIKE '%$no_manufaktur'  $lanjut and no_manufaktur like '%$cari%'
             ORDER BY approve,approve2,tanggal DESC,no_manufaktur DESC limit ".($page*$jmlHal).",".$jmlHal;
			
			if($username=='rian-it'){
				echo "$sql</br>";
			}
			 
			$hsl=mysql_query($sql,$db);
			#$sql="select count(*) from po_manufaktur where closeco IS NULL";//edt by budi
			$sql="SELECT FOUND_ROWS()";
		    $query=mysql_query($sql);
		    $jmlData=mysql_fetch_row($query);
			
			$no=($hal*$jmlHal);
			while($rs=mysql_fetch_array($hsl)){
				$no++;
				$no_po=$rs["no_po"];
				$no_manufaktur=$rs["no_manufaktur"];
				$tanggal=$rs["tanggal"];
				$approvedate1=$rs["approvedate"];
				$approvedate2=$rs["approvedate2"];
				$kode_supplier=$rs["no_vendor"];
				$sql="SELECT nama FROM supplier WHERE id='$kode_supplier'";
				$hsltemp=mysql_query($sql,$db);
				list($supplier)=mysql_fetch_array($hsltemp);
				$totalqty=$rs["totalqty"];
				$jumlah=$rs["totalrp"];
				$closeco=$rs["closeco"];
				$closecoby=$rs["closecoby"];
				$closedate=$rs["closedate"];
			
// Edited Bye Goberan
                                $sql="SELECT kd_produk FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur'";
                                $hsltemp=mysql_query($sql,$db);
                                list($kd_produk)=mysql_fetch_array($hsltemp);

                                $sql="SELECT * FROM produk WHERE kode = '$kd_produk'";
                                $hsltemp=mysql_query($sql,$db);
                                $rsa=mysql_fetch_array($hsltemp);
                                $kode=$rsa["kode"];
								 $nama=$rsa["nama"];
                                $kode_basic_item=$rsa["kode_basic_item"];
                                $kode_kategori=$rsa["kode_kategori"];
                                $kode_kelas=$rsa["kode_kelas"];
                                $kode_style=$rsa["kode_style"];
                                $kode_warna=$rsa["kode_warna"];
                                $kode_model=$rsa["kode_model"];
                                $sql="SELECT model FROM mst_model WHERE kode='$kode_model' AND kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style'";
                                $hsltemp=mysql_query($sql,$db);
                                list($style)=mysql_fetch_array($hsltemp);
	
				$approve=$rs["approve"];
				$approveby1=$rs["approveby"];
				$approveby2=$rs["approveby2"];
				if($approve=="1"){
					$status="<b>Approved - [$approveby1]</b>";
				}else{
					$status="<blink><b><font color='red'>Belum Di Approve</font></b></blink>";
				}
				$approve2=$rs["approve2"];
				if($approve2=="1"){
					$status2="<b>Approved - [$approveby2]</b>";
				}else{
					$status2="<blink><b><font color='red'>Belum Di Approve</font></b></blink>";
				}
                
                //$sql="SELECT sum(qty_produk) FROM `job_gelaran_detail` WHERE `no_po`='$no_manufaktur'";
                $sql="SELECT sum(jc.totalqty) FROM job_gelaran AS jg 
				INNER JOIN job_cutting AS jc ON 
				(jc.no_co=jg.no_co) WHERE jg.no_po='$no_manufaktur'";
				$res=mysql_query($sql)or die($sql);
                list($sudah)=mysql_fetch_array($res);
				$sisa=$totalqty-$sudah;
		 $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
        ?>
        <tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
        <td height="23" align="center"><?php echo $no; ?></td>
        <td height="23" align="left" width="144">&nbsp;<?php echo $no_manufaktur; ?></td>
        <td height="23" align="center" width="140">&nbsp;<?php echo $tanggal; ?></td>
        <td height="23" align="left" width="153">&nbsp;<?php echo $nama; ?></td>
        <td height="23" align="right" width="71"><?php echo number_format($totalqty); ?></td>
        <td height="23" align="right" width="116"><?php 
		if($jumlah==0){// tambahan budi tanggal 02022012 jam 10:59
            // munculkan dulu nilai jumlahnya lalu update databasenya
			$sql="SELECT SUM(jumlah) FROM po_manufaktur_detail WHERE no_manufaktur='$no_manufaktur';";
			list($jumlah)=mysql_fetch_array(mysql_query($sql));
			
			$sql="UPDATE po_manufaktur SET totalrp='$jumlah' WHERE no_manufaktur='$jumlah'";
			$result=mysql_query($sql) or die($sql.' '.mysql_error());		
			
    			
		}
		echo number_format($jumlah); 
		
		?></td>
        <td height="23" align="right" width="71">
         <?php if($sisa>0){$warna="#FF0000";}else{
                
                $warna="#000000";
            }?>
            <font color="<?php echo $warna?>"><?php echo  number_format($sisa);?></font></td>
        </td>
        <td height="23" align="left" width="180">&nbsp;<?php echo "$status [$approvedate1]"; ?></td>
        <td height="23" align="left" width="180">&nbsp;<?php echo "$status2 [$approvedate2]"; ?></td>
        <td height="23" align="center" width="210">
		<?php if($closeco==1){
			echo "<em><font color='#ff000'><b>Close PO ($closecoby)<br>
			($closedate)</b></font></em>";
		}else{?>
        <p align="left">&nbsp;
        <a href="permintaan_manufaktur_detail.php?no_manufaktur=<?php echo $no_manufaktur; ?>" target="_blank">Detil</a>
                    <?php
                        if ($approve2=="1"){
                            $sql="SELECT no_po FROM po_rm WHERE no_manufaktur='$no_manufaktur'";
                            $hsltemp=mysql_query($sql,$db);
                            if(mysql_affected_rows($db)>0){
                                list($no_po_rm)=mysql_fetch_array($hsltemp);
                        ?>
                            |
                            <a href="po_rm_list.php?no_manufaktur=<?php echo $no_manufaktur; ?>">PO RM</a>
                        <?php
                            }
                            $sql="SELECT kd_barang,qty FROM barang_kurang WHERE no_manufaktur='$no_manufaktur' AND qty>0";
                            mysql_query($sql,$db);
                            if(mysql_affected_rows($db)>0){
                        ?>
                            |
                            <a href="rm_kurang_list.php?no_manufaktur=<?php echo $no_manufaktur; ?>">Daftar Kekurangan RM</a>
                        <?php    
                            }
                        }
                    ?>
                    <?php                        
                        if($closeco=="1"){
                    ?>
                        |
                        <b>PO Closed</b> | <b><font color="green"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&no_manufaktur=<?php echo $no_manufaktur; ?>">Delete</a></font></b>
                    <?php
                        }else{
                    ?>
                        |
                        <a href="#" onclick="if(confirm('Anda yakin ingin menutup PO <?php echo $no_manufaktur;?>?')){if(alasan=prompt('Keterangan?')){window.location='po_manufaktur_close.php?no_manufaktur=<?php echo $no_manufaktur;?>&desc='+alasan;}}">Closing PO</a>
                     <?php
                        }
                   
				   } ?>
        </td>
    </tr>
		<?php
			}
		?>
	</table>
    
    <table style="margin-left:10px; margin-top:10px;">
            <tr>
                <td class="text_standard">
                    Page : 
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=0';">First</span>
                    <?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
                        if($hal<=0){ ?>
                            <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
                            <?php if($i>=4) break;
                        }else if(($hal+1)>=($jmlData[0]/$jmlHal)){
                            if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
                            <?php } 
                        }else{
                            if($i<=($hal+2)and $i>=($hal-2)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
                            <?php }
                        }
                    } ?>
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?>';">Last</span>
                    &nbsp;&nbsp;
                    Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData[0]; ?> Data
                </td>
            </tr>
        </table>
        <br /><br />
    
<?php include_once "footer.php" ?>
