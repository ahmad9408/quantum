<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Laporan Stok Perperiode "; ?>
<?php if(1==1)include('header.php') ?>
<?php

    /*
	Last Change 2012-09-26
	add Fasilitas rekap wip produksi
	tambah variable $jenis_tabel_gudang untuk akomodasi wip karena tabel rekap tidak sma dengan kode_gudangnya
	
	
	*/
     
    $isShowHpp=0;
	  $sql_show_hpp="select count(*) from user_account_showhpp where username='$username' and aktif=1";
	  $res=mysql_query($sql_show_hpp);
	  if(!$res){
		 if($username=='budi-it'){
			echo "<h3>Error ".mysql_error()."</h3>";  
		 }	  
	  }
	  list($ada)=mysql_fetch_array($res);
	  if($ada>0){
		  $isShowHpp=1;
	  }
	
	
    $txtlocation=sanitasi($_REQUEST["r"]);
    $periode=sanitasi($_REQUEST["p"]);
	$jenis_stok=sanitasi($_REQUEST["a"]);// stok awal
	$kode_supplier=trim(sanitasi($_REQUEST['s']));
	$barcode=trim(sanitasi($_REQUEST['b']));//barcode
	$nama=trim(sanitasi($_REQUEST['n']));//barcode
	if(isset($_REQUEST["m"])){// lihat laporan markas
	    $txtlocation=sanitasi($_REQUEST["m"]);
	    $tambah="&p=$periode&r=$txtlocation&a=$jenis_stok&m=$txtlocation&s=$kode_supplier&b=$barcode&n=$nama";
		$jenis_tabel='markas';
		$jenis_tabel_gudang=$jenis_tabel;
		$sql="Select nama from outlet where id='$txtlocation';";
	}elseif($_REQUEST["g"]){
		$txtlocation=sanitasi($_REQUEST["g"]);
	     $tambah="&p=$periode&r=$txtlocation&a=$jenis_stok&g=$txtlocation&s=$kode_supplier&b=$barcode&n=$nama";
		$jenis_tabel='distribusi';
		$jenis_tabel_gudang=$jenis_tabel;
		$sql="SELECT nama FROM gudang_distribusi WHERE id='$txtlocation';";
	}elseif($_REQUEST["w"]){
		$txtlocation=sanitasi($_REQUEST["w"]);
	    $tambah="&p=$periode&r=$txtlocation&a=$jenis_stok&w=$txtlocation&s=$kode_supplier&b=$barcode&n=$nama";
		$jenis_tabel='wip';
		$jenis_tabel_gudang='pabrik';
		$sql="SELECT nama FROM pabrik WHERE id='$txtlocation';";
	}else{
		$tambah="&p=$periode&r=$txtlocation&a=$jenis_stok&s=$kode_supplier&b=$barcode&n=$nama";
		$jenis_tabel='manufaktur';
		$jenis_tabel_gudang=$jenis_tabel;
		$sql="Select nama from pabrik where id='$txtlocation';";
	}
	
	$date=explode('-',$periode);// ambil nilai bulan
	$tahun=$date[0];
	$bulan1=$date[1];
	
	$periode_aktif=date('Y-m');
	if($periode_aktif!=$tahun.'-'.$bulan1){
	   $jenis_tabel.='';	
	}
	#echo "<h2>$periode_aktif==$tahun.'-'.$bulan1 $jenis_tabel_gudang </h2>";
	
	if($jenis_stok==1){// stok awal
			$field='r.stok_awal';    
			$judul='Stok Awal Periode ' ;         
		}else{// stok_akhir
			$field='r.stok_akhir';
			$judul='Stok Akhir Periode ';
		}
		
	
	
	 $array_bulan = array('01'=>'Januari','02'=>'Februari','03'=>'Maret', '04'=>'April', '05'=>'Mei',
 		'06'=> 'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober', '11'=>'November','12'=>'Desember');

		
    list($gudang)=mysql_fetch_array(mysql_query($sql));	
	
	foreach($array_bulan as $key => $value){
			 if($key==$bulan1){
				 $judul=strtoupper("$judul $value $tahun [$txtlocation]$gudang");
				 break;
			 }
			
	 }
	?>
<!--script src="jquery-latest.js"></script-->
<script language="JavaScript">
	$(document).ready(function(){
	    $("#judul").html("<?php echo $judul?>");
		<?php
		  if($isShowHpp==1){
			  
		  }else{
		     ?>
			 $('.hpp').hide();
			 $('.footer').attr('colspan','4');
		  <?php	  
		  }
		
		?>
		
	});
</script>
    

<fieldset>
        <table border="0" width="100%" style="font-size: 8pt">
          <tr> 
            <td background="images/footer.gif" align="center" width="20" height="22"><b>BARCODE 
              13</b></td>
            <td background="images/footer.gif" align="center" width="20" height="22"><b>BARCODE 
              15</b></td>
            <td background="images/footer.gif" align="center" width="200" height="22"><b>NAMA 
              ITEM</b></td>
            <td background="images/footer.gif" align="center" width="50" class="hpp"><strong>HPP</strong></td>
            <td background="images/footer.gif" align="center" width="50" height="22"><b>HPJ</b></td>
            <td background="images/footer.gif" align="center" width="100" height="22"><b>QTY</b></td>
            <td background="images/footer.gif" align="center" width="100" class="hpp"><strong>SUBTOTAL[HPP]</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22"><b>SUBTOTAL[HPJ]</b></td>
          </tr>
          <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=50;
	$page=$hal;
    //Tambahan Budi 02 November 2011
    $notallow_minus =' '; 
    
    
    //$notallow_minus =' And stok>0 ';
	     
		   $sql_inner='';
		   $sql_tbhn='';
		   if(!empty($kode_supplier)){
			   $sql_inner="INNER JOIN produk p on p.kode=r.barcode_15";
		       $sql_tbhn=" AND p.kode_supplier like '$kode_supplier%'";
			}
		
		
		if(!empty($barcode)){
			$sql_tbhn.=" AND r.barcode_15 like '$barcode%' ";
		}
		
		
		if(!empty($nama)){
			$sql_tbhn.=" AND r.nama like '%$nama%' ";
		}
		
			$sql1="SELECT count(distinct(r.barcode_15)), sum($field), sum($field* r.hpp), sum($field* r.hpj) FROM rekap_stok_$jenis_tabel r $sql_inner
			  WHERE r.kode_$jenis_tabel_gudang='$txtlocation' AND r.periode like '$periode%' $sql_tbhn ";
			$sql2="SELECT r.barcode_13,r.barcode_15,r.nama,r.hpp,r.hpj,$field FROM rekap_stok_$jenis_tabel r $sql_inner
WHERE r.kode_$jenis_tabel_gudang='$txtlocation' AND r.periode like '$periode%' $sql_tbhn ORDER BY $field DESC LIMIT ".($page*$jmlHal).",".$jmlHal;
		
	if($username=='budi-it'){
		echo "<h3>$sql2</h3>";
	}
	
     //echo "<!-- SQL $sql1 <br> $sql2 --> ";
	$hsltmp1=mysql_query($sql1,$db);
	$jmlData=mysql_fetch_row($hsltmp1);
	$hsltemp2=mysql_query($sql2,$db);
	
	$no=($hal*$jmlHal);
	$totalsubtotal_hpp=0;
	$totalsubtotal_hpj=0;
	$totalqty=0;
	if($isShowHpp==1){
		}else{
			
		    $jmlData[2]=0;
		}
    while ( list($barcode13,$barcode15,$nama,$hpp,$hpj,$stok)=mysql_fetch_array($hsltemp2)) {
        $no++;
		
		if($isShowHpp==1){
			
		}else{
			 $hpp=0;$stok_akhir_hpp=0;	
		   
		}
		$subtotal_hpp=$stok * $hpp;
		$subtotal_hpj=$stok * $hpj;
		$totalqty+=$stok;
		
		$totalsubtotal_hpp+=$subtotal_hpp;
		$totalsubtotal_hpj+=$subtotal_hpj;
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
		
         ?>
          <tr onMouseOver="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>"> 
            <td height="21">&nbsp;<?php echo $barcode13; ?></td>
            <td height="21">&nbsp;<?php echo $barcode15; ?></td>
            <td height="21">&nbsp;<?php echo $nama; ?></td>
            <td align="right" class="hpp"><?php echo number_format($hpp); ?></td>
            <td height="21" align="right">&nbsp;<?php echo number_format($hpj); ?></td>
            <td height="21" align="right">&nbsp;<font color="<?php echo $warfo;?>"> 
              <?php if($stok<=1){?>
              <blink><?php echo number_format($stok); ?></blink> 
              <?php }else{?>
              <?php echo number_format($stok); } ?></font></td>
            <td align="right" class="hpp"><?php echo number_format($subtotal_hpp); ?></td>
            <td height="21" align="right">&nbsp;<?php echo number_format($subtotal_hpj); ?></td>
          </tr>
          <?php
		}
    
    ?>
          <tr> 
            <td height="23" background="images/notupload.jpg" align="center" colspan="5" class="footer"><i><b>SUBTOTAL</b></i></td>
            <td height="23" background="images/notupload.jpg" width="6%" align="right">&nbsp;<b><?php echo number_format($totalqty); ?></b></td>
            <td background="images/notupload.jpg" width="10%" align="right" class="hpp"><b><?php echo number_format($totalsubtotal_hpp); ?></b></td>
            <td height="23" background="images/notupload.jpg" width="10%" align="right">&nbsp;<b><?php echo number_format($totalsubtotal_hpj); ?></b></td>
          </tr>
          <tr> 
            <td height="25" background="images/yesupload.jpg" align="center" colspan="5" class="footer"><b>TOTAL</b></td>
            <td height="25" background="images/yesupload.jpg" width="6%" align="right">&nbsp;<font color=red><b><?php echo number_format($jmlData[1]); ?></b></font></td>
            <td background="images/yesupload.jpg" width="10%" align="right" class="hpp"><font color="red"><b><?php echo number_format($jmlData[2]); ?></b></font></td>
            <td height="25" background="images/yesupload.jpg" width="10%" align="right">&nbsp;<font color=red><b><?php echo number_format($jmlData[3]); ?></b></font></td>
          </tr>
        </table>
<table style="margin-left:10px; margin-top:10px;">
				  <tr>
						<td class="text_standard">
							Page : 
							<span class="hal" onClick="location.href='stok_finishing_periode.php?&hal=0<?php echo $tambah?>';">First</span>
							<?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
								if($hal<=0){ ?>
									<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='stok_finishing_periode.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
									if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='stok_finishing_periode.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='stok_finishing_periode.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
							<span class="hal" onClick="location.href='stok_finishing_periode.php?&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?><?php echo $tambah?>';">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>						</td>
	</tr>
</table>
</fieldset>
<?php include_once "footer.php"; ?>