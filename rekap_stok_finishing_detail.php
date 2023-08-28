<?php //ob_start("ob_gzhandler"); ?>
<?php $content_title="Rekap Stok Gudang All"; 
//error_reporting(1);

/*
 1 Mei 2012 [add security ]
 26 sept 2012 ditambah fasilitas  wip

*/

ob_start("ob_gzhandler");
include_once "header.php";
$background=" background='images/footer.gif' ";

 $thisPage=$_SERVER['PHP_SELF'];

//error_reporting(E_ALL);
//ini_set('display_errors','On');
setlocale(LC_MONETARY, 'en_US');


//+++++++++++++++++++Tambahan Untuk fungsi Hitung Bulan

	function firstOfMonth($month,$year) {
		return date("Y-m-d", strtotime($month.'/01/'.$year.' 00:00:00'));
	}
	
	function lastOfMonth($month,$year) {
	   return date("Y-m-d", strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00'))));
	}




set_time_limit(36000);

 $array_bulan = array('01'=>'Januari','02'=>'Februari','03'=>'Maret', '04'=>'April', '05'=>'Mei',
 '06'=> 'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober', '11'=>'November','12'=>'Desember');
$tahun_skrg=date('Y');
$bulan_skrg=date('m');
 if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
    $jmlHal=1000;
    $page=$hal;
    $gudang=$_GET['o'];
	$periode=$_GET['p'];
	$field=$_GET['j'];
	$type=$_GET['t'];
	$JenisGudang=$_GET['jg'];
	$barcode=$_GET['b'];
	$kode_lokasi=$_GET['l'];
	
	$tambah="action=search&o=$gudang&p=$periode&j=$field&t=$type&l=$kode_lokasi";
	
	if($username=='budi-it'){
	  print_r($_POST);	
	}
	
	$field_muncul=$field;
	$sql_order_by='barcode_15';
	
	if(!empty($JenisGudang)){
		
		$tambah='action=search&jg='.$JenisGudang;
		
		$periode=trim($_POST['p']);
		$field=trim($_POST['j']);
		$type=trim($_POST['t']);
		$barcode=trim($_POST['b']);
		$gudang=trim($_POST['gd']);
		
		if($_GET['hal']==0){
			$_SESSION['p']=$periode;
			$_SESSION['j']=$field;
			$_SESSION['t']=$type;
			$_SESSION['b']=$barcode; 
			$_SESSION['gd']=$gudang;
			$_SESSION['l']=$kode_lokasi;
		}elseif(isset($_GET['hal'])){
			$periode=$_SESSION['p'];
			$field=$_SESSION['j'];
			$type=$_SESSION['t'];
			$barcode=$_SESSION['b'];
			$gudang=$_SESSION['gd'];
			$kode_lokasi=$_SESSION['l'];
		}
		
		$field_muncul=" TOTAL ".$field;
		
		if($field=='sk'){
			$field='stok_akhir';
		}elseif($field=='saw'){
			$field='stok_awal';
		}else{
			
			
			if($periode==date('Y-m')){
			   $lastDate=date('j')-1;
			}else{
			   $lastDate=31;	
			}
			
			$fl='('; 
			for($i=1;$i<=$lastDate;$i++){
				$fl.=$field.$i .'+';	
			}
			$field=substr($fl,0,strlen($fl)-1).')';
		}
		
		
	}else{
		if($field=='sk'){
			$field='stok_akhir';
		}elseif($field=='saw'){
			$field='stok_awal';
		}
	}
	
	
	
	//$q="SELECT outlet FROM user_account WHERE username='$username'";
  // $s=mysql_query($q);
  // list($kodeoutlet)=mysql_fetch_array($s);
   
   $kodeoutlet=$_SESSION['outlet'];
   $group=substr($kodeoutlet,5,5);
   if($group=='O0000' ||$group=='o0000'){
	  $area=substr($kodeoutlet,0,4);
	}else{
	  $area=$kodeoutlet;  
	}
	 
	 if($periode==date('Y-m')){
		$tbl='';
	}else{
		$d=explode('-',$periode);			
		$tbl='_'.$d[1].$d[0];		
	}
	 
	 if($type=='o'){
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_manufaktur,nama_manufaktur,barcode_13,barcode_15,nama,$field,($field *hpj),hpj
	      FROM rekap_stok_manufaktur WHERE kode_manufaktur='$gudang'  and kode_manufaktur like '$area%' and periode like '$periode%' and $field<>0  ";
		 $kd_gdg='KODE PABRIK';
		 $nama_gdg='NAMA PABRIK';
		 
	}elseif($type=='m'){
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_markas,nama_markas,barcode_13,barcode_15,nama,$field,($field *hpj),hpj
	      FROM rekap_stok_markas$tbl WHERE kode_markas='$gudang' and kode_markas like '$area%' and periode like '$periode%' and $field<>0 ";
		 $kd_gdg='KODE MARKAS'; 
		 $nama_gdg='NAMA MARKAS';
	}elseif($type=='d'){
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_distribusi,nama_distribusi,barcode_13,barcode_15,nama,$field,($field * hpj),hpj
	      FROM rekap_stok_distribusi$tbl WHERE kode_distribusi='$gudang' and kode_distribusi like '$area%' and periode like '$periode%' and $field<>0 ";
		 $kd_gdg='KODE DISTRIBUSI';
		 $nama_gdg='NAMA DISTRIBUSI';
		
	}elseif($type=='w'){
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_pabrik,nama_pabrik,barcode_13,barcode_15,nama,$field,($field * hpj),hpj
	      FROM rekap_stok_wip$tbl WHERE kode_pabrik='$gudang' and kode_pabrik like '$area%' and periode like '$periode%' and $field<>0 ";
		 $kd_gdg='KODE PABRIK';
		 $nama_gdg='NAMA PABRIK';
		//echo $sql;
	}
	$JenisGudang=$type;
	#$filter="and kode_lokasi='$kode_lokasi' ";
	
	if($JenisGudang=='o'){		
		if(!empty($gudang)){
			$filter.=" AND kode_manufaktur ='$gudang' ";
		}
		
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS  DATE_FORMAT(periode,'%M-%Y'),kode_manufaktur,nama_manufaktur,barcode_13,barcode_15,nama,$field,($field *hpj),hpj
	      FROM rekap_stok_manufaktur WHERE jenis=1 and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter  ";
		  
		 $sql_total="SELECT SQL_CACHE   SUM($field)qty,SUM($field *hpj)hpj_qty,hpj
	      FROM rekap_stok_manufaktur WHERE jenis=1 and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter"; 
		 $kd_gdg='KODE PABRIK';
		 $nama_gdg='NAMA PABRIK';
		 $sql_order_by='kode_manufaktur,barcode_15';
		 
	}elseif($JenisGudang=='m'){
		if(!empty($gudang)){
			$filter.=" AND kode_markas ='$gudang' ";
		}
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_markas,nama_markas,barcode_13,barcode_15,nama,$field,($field *hpj),hpj
	      FROM rekap_stok_markas$tbl WHERE jenis=1 and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter ";
		  
		   $sql_total="SELECT SQL_CACHE   SUM($field)qty,SUM($field *hpj)hpj_qty,hpj
	       FROM rekap_stok_markas$tbl WHERE jenis=1 and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter ";
		 $kd_gdg='KODE MARKAS'; 
		 $nama_gdg='NAMA MARKAS';
		 $sql_order_by='kode_markas,barcode_15';
	}elseif($JenisGudang=='d'){
		if(!empty($gudang)){
			$filter.=" AND kode_distribusi ='$gudang' ";
		}
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_distribusi,nama_distribusi,barcode_13,barcode_15,nama,$field,($field * hpj),hpj
	      FROM rekap_stok_distribusi$tbl WHERE kode_distribusi NOT LIKE '%S%' and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter ";
		 $sql_total="SELECT SQL_CACHE   SUM($field)qty,SUM($field *hpj)hpj_qty,hpj
	       FROM rekap_stok_distribusi$tbl WHERE kode_distribusi NOT LIKE '%S%' and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter "; 
		 $kd_gdg='KODE DISTRIBUSI';
		 $nama_gdg='NAMA DISTRIBUSI';
		 $sql_order_by='kode_distribusi,barcode_15';
		
	}elseif($JenisGudang=='w'){
		if(!empty($gudang)){
			$filter.=" AND kode_pabrik ='$gudang' ";
		}
		$sql="SELECT SQL_CACHE SQL_CALC_FOUND_ROWS DATE_FORMAT(periode,'%M-%Y'),kode_pabrik,nama_pabrik,barcode_13,barcode_15,nama,$field,($field * hpj),hpj
	      FROM rekap_stok_wip$tbl WHERE kode_pabrik NOT LIKE '%S%' and periode like '$periode%' and $field<>0 and barcode_15 like '$barcode%' $filter ";
		 $kd_gdg='KODE PABRIK';
		 $nama_gdg='NAMA PABRIK';
		 $sql_order_by='kode_pabrik,barcode_15';
		//echo $sql;
	}
	
	
	
	$sql.=" ORDER BY $sql_order_by  DESC LIMIT ".($page*$jmlHal).",".$jmlHal;
	if($username=='budi-it'){
	   echo "<h3> SQL :$sql</h3>";
	   echo "<hr>";
	   echo "[ $sql_total ]";
	   echo "<hr>";	 
	   #die();
	}
	
	//$hsltmp1=mysql_query($sql1,$db);
    //$jmlData=mysql_fetch_row($hsltmp1);
	

   //echo($sql);
    $hsltemp=mysql_query($sql);// or die($sql.' # '.mysql_error());
	if(!$hsltemp){
	   if($username=='budi-it'){
	       echo mysql_error()."<h3> SQL :$sql</h3>";	 
	   #die();
	   }else{
		   echo "<h3>SOMETHING WRONG!!<h3>";	   
		   include('footer.php');
		   die();
	   }
	   	
	}
	$sql='SELECT FOUND_ROWS();';
	$hsltemp1=mysql_query($sql) or die($sql.' # '.mysql_error());
	$jmlData=mysql_fetch_row($hsltemp1);
	
	
	$res_total=mysql_query($sql_total);
	//print_r($jmlData);
	if($username=='budi-it'){
	   //die('jumlah data ' .$jmlData[0].' '.$sql);
	}
	
	list($totalall_qty,$totalall_qtyhpj)=mysql_fetch_array($res_total);
	//echo $sql;

  // die();
 #echo "<h3> SQL $username </h3>";	
  


?>






 

<table border="0" width="100%" style="font-size: 8pt" height="126">
  <tr>
    <td width="41" height="22" align="center" <?php echo $background?>><strong>NO</strong></td>
    <td <?php echo $background;?> align="center" width="49"><strong>Periode</strong></td>
    <td <?php echo $background;?> align="center" width="50"><b><?php echo $kd_gdg;?></b></td>
    <td <?php echo $background;?> align="center" width="93"><b><?php echo $nama_gdg;?></b></td>
    <td <?php echo $background;?> align="center" width="93" class="detail1"><strong>Barcode 13</strong></td>
    <td <?php echo $background;?> align="center" width="93" class="detail1"><strong>Barcode 15</strong></td>
    <td <?php echo $background;?> align="center" width="93" class="detail1"><strong>Produk</strong></td>
    <td <?php echo $background;?> align="center" width="93" class="detail1"><strong><?php echo $field_muncul; ?></strong></td>
    <td <?php echo $background;?> align="center" width="93" class="detail1">HPJ</td>
    <td <?php echo $background;?> align="center" width="93" class="detail1">SUBTOTAL</td>
    
	   
  </tr>
  <?php
   
   
	
		
    
    
    //echo $sql;
	
    
	
	
    //$no=0;
    $no=($hal*$jmlHal); 	 
	$total_qty=0;
	$total_hpj=0;
    while ( list($periode,$kode_manufaktur,$nama_manufaktur,$barcode_13,$barcode_15,$nama,$qty,$subtotal,$hpj)=mysql_fetch_array($hsltemp)){
      $total_qty+=$qty;
	  $total_subtotal+=$subtotal;
	 
		 
	   
	    
        //$hpp='-'; //dirubah tanggal 2 desember 2011 untuk menghindari non super user
        $no++;
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
	

		
        /* Cari Nama */
        
        
?>


    <tr onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">

    
 
   
    <td width="41" bgcolor="<?php echo $bgcolor; ?>" >&nbsp;<?php echo $no; ?></td>
    <td width="49">&nbsp;<?php echo $periode; ?></td>
    <td width="50">&nbsp;<?php echo $kode_manufaktur; ?></td>
    <td width="93">&nbsp;<?php echo $nama_manufaktur; ?></td>
    <td width="93" ><?php echo $barcode_13;?></td>
    <td width="93" ><?php echo $barcode_15;?></td>
    <td width="93" ><?php echo $nama;?></td>
    <td width="93" align="right" ><?php echo number_format($qty); ?></td>
    <td width="93" align="right" ><?php echo number_format($hpj); ?></td>
    <td width="93" align="right" ><?php echo number_format($subtotal); ?></td> 
    
	
  </tr>
  <?php }?>
 
 <tr > 
    <td height="22" colspan="7" <?php echo $background?>>Subtotal</td>
    <td <?php echo $background?>  width="93" align="right"><?php echo number_format($total_qty); ?></td>
    <td <?php echo $background?>  width="93" align="right">&nbsp;	</td>
    <td <?php echo $background?>  width="93" align="right"><?php echo number_format($total_subtotal) ?></td>
    
 
  </tr>
 <tr >
   <td height="22" colspan="7" <?php echo $background?>>TOTAL</td>
   <td <?php echo $background?> align="right"><?php echo number_format($totalall_qty);?></td>
   <td <?php echo $background?> align="right">&nbsp;</td>
   <td <?php echo $background?> align="right"><?php echo number_format($totalall_qtyhpj);?></td>
 </tr>
</table>

<table style="margin-left:10px; margin-top:10px;">
<tr>
                        <td class="text_standard">
                            Page : 
                            <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?&hal=0<?php echo $tambah?>';">First</span>
                            <?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
                                if($hal<=0){ ?>
                                    <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
                                    <?php if($i>=4) break;
                                }else if(($hal+1)>=($jmlData[0]/$jmlHal)){
                                    if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
                                        <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
                                    <?php } 
                                }else{
                                    if($i<=($hal+2)and $i>=($hal-2)){ ?>
                                        <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
                                    <?php }
                                }
                            } ?>
                            <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?><?php echo $tambah?>';">Last</span>
                            &nbsp;&nbsp;
                            Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>
                        </td>
  </tr>
</table>

<?php include_once "footer.php"; ?>
