<?php $content_title="DAFTAR PPIC"; include_once "header.php" ?>
<?php
	$no_po=sanitasi($_GET["no_po"]);
	if(isset($_REQUEST['action'])){
		
		session_start();
		$tambah="&action=search";
		if(isset($_POST['model'])){
			  $kode_md=$_POST['model'];
              $_SESSION['model']=$kode_md;
        }else{
              $kode_md=$_SESSION['model'];
              
        }		
	}else{
		unset($_SESSION['model']);
		$tambah='';
	}
?>


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
		<table width="968">
			<tr>
				<td width="112"><b>No Manufaktur</b></td>
				<td width="6"><b>:</b></td>
				<td width="185"><input type="text" id="no_manufaktur" name="no_manufaktur" onclick="showManufaktur(this.id);" size="30"></td>
				<td width="89"><input type="button" value="Tambah CO" onclick="window.location='job_gelaran_add.php?no_manufaktur='+document.getElementById('no_manufaktur').value;" /></td>
				<td width="361">Model <input name="model" type="text" id="model" value="<?php echo $kode_md;?>"  size="30" /></td>
				<td width="187"><input type="submit" name="Cari" id="Cari" value="Cari Model" /></td>
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
        <td align="center" bgcolor="#99CC00" height="24" width="160"><b>No Manufaktur</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="160"><b>No CO</b></td>
        <td align="center" bgcolor="#99CC00" height="24"><b>Model</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="180"><b>Pabrik</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="80"><b>Qty</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="136"><b>Tanggal</b></td>
        <td align="center" bgcolor="#99CC00" height="24" width="194"><b>Approve I</b></td>
        <td align="center" width="87" bgcolor="#99CC00" height="24"><b>Action</b></td>
    </tr>
		<?php
			$_pabrik="";
			if(strtoupper($_SESSION['outlettype'])=="P"){
				$_pabrik=$_SESSION['outlet'];
			}
			if(isset($_REQUEST['action'])){
			   //cari model
			   
			  	
			    $sql="SELECT  SQL_CALC_FOUND_ROWS  j.seqno , j.no_po, j.no_co, j.pabrik , j.updateby , j.updatedate, j.approve , j.approvedate, j.approveby  FROM job_gelaran_detail AS d
					INNER JOIN job_gelaran AS j ON (d.no_co = j.no_co) INNER JOIN produk AS p ON (p.kode = d.kd_produk)
					INNER JOIN mst_model AS m  ON (m.kode_basic_item = p.kode_basic_item) AND (m.kode_kategori = p.kode_kategori) AND (m.kode_kelas = p.kode_kelas) AND (m.kode_style = p.kode_style) AND (p.kode_model = m.kode)
					WHERE m.model LIKE '%$kode_md%' 
					ORDER BY j.approve,j.updatedate DESC,no_po limit ".($page*$jmlHal).",".$jmlHal;
					
					
			}else{
			  $sql="SELECT  SQL_CALC_FOUND_ROWS seqno, no_po,no_co,pabrik, updateby, updatedate, approve, approveby, approvedate FROM job_gelaran WHERE no_po LIKE '%$no_po' AND pabrik LIKE '$_pabrik%' ORDER BY approve,updatedate DESC,no_po limit ".($page*$jmlHal).",".$jmlHal;
			}
			
			
			// $sql;
			
			
			$hsl=mysql_query($sql,$db);
			
			$sql3="SELECT FOUND_ROWS()";
			$hsltmp12=mysql_query($sql3,$db) or die ($sql3);
			$jmlData=mysql_fetch_row($hsltmp12);
			
			$no=($hal*$jmlHal);
			while($rs=mysql_fetch_array($hsl)){
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
                $sql1="SELECT sum(qty_produk) FROM job_gelaran_detail WHERE no_po='$no_po' AND no_co='$no_co'";
                $hsltemp=mysql_query($sql1,$db);
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
        <td align="left" height="20" width="160">&nbsp;<?php echo $no_co; ?></td>
        <td align="left" height="20">&nbsp;<?php echo $style; ?></td>
        <td align="left" height="20" width="180">&nbsp;<?php echo $pabrikname." [$pabrik]"; ?></td>
        <td align="right" height="20" width="80">&nbsp;<?php echo number_format($qty,2,",","."); ?></td>
        <td align="center" height="20" width="136"><?php echo $updatedate; ?></td>
        <td align="left" height="20" width="194">&nbsp;<?php echo $status; ?></td>
        <td align="center" width="87" height="20"><a href="job_gelaran_detail.php?no_po=<?php echo $no_po; ?>&no_co=<?php echo $no_co; ?>">Detil</a></td>
    </tr>
		<?php
			}
		?>
	</table>
    <table style="margin-left:10px; margin-top:10px;">
        <tr>
            <td class="text_standard">
            	Page : 
                <span class="hal" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?>&hal=0';">First</span>
                <?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
					if($hal<=0){ ?>
						<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?>&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php if($i>=4) break;
					}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
						if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?>&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php } 
					}else{
						if($i<=($hal+2)and $i>=($hal-2)){ ?>
							<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?>&hal=<?php echo $i; ?>';"><?php echo ($i+1); ?></span>
						<?php }
					}
				} ?>
                <span class="hal" onclick="location.href='job_gelaran_list.php?x_idmenu=229<?php echo $tambah;  ?>&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?>';">Last</span>
                &nbsp;&nbsp;
                Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData[0]; ?> Data
            </td>
        </tr>
    </table>
    <br /><br />
<?php include_once "footer.php" ?>
