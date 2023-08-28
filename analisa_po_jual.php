<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Evaluasi PO "; ?>
<?php 
 /*
 Edit 02082017 tambah berdasarkan Approve2=1
 
 */

 $lihat=1;
 if($lihat==1){ 
    include('header.php');
 }
 include_once('RekapStok.php');

 
  ?>
<?php
    $tgl1=$_POST['tgl1'];
	$tgl2=$_POST['tgl2'];
    $model=$_POST['txt_cari'];
	
	//error_reporting(1);
	
    /*if(isset($_SESSION['tgl1'])){
        $tgl1=$_SESSION['tgl1'];
        $tgl2=$_SESSION['tgl2'];
    }else{
       if(empty($tgl1)){
            $tgl1=date("Y-m-d");
            $tgl2=date("Y-m-d");
        
        } 
        
    }*/
	
	function jumlahHari($month,$year) {
	   return date("j", strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00'))));
	}
	
	function dateMysql($number){
	   if($number<10){
		  return '0'.$number; 
	   }else{
		  return $number;   
	   }
	  
	}
	
	
	function createMonthRangeArray($strDateFrom,$strDateTo) {
  
   
      $aryRange=array();

       $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
       $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

       if ($iDateTo>=$iDateFrom) {
         array_push($aryRange,date('Y-m-01',$iDateFrom)); // first entry
         $month_before=trim(date('Y-m-01',$iDateFrom));
         while ($iDateFrom<$iDateTo) {
           $iDateFrom+=86400; // add 24 hours
		   if($month_before!=trim(date('Y-m-01',$iDateFrom))){
			   array_push($aryRange,date('Y-m-01',$iDateFrom));
			   $month_before=trim(date('Y-m-01',$iDateFrom));
		   }
           
         }
       }
       return $aryRange;
   }
   
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
	     include("DateControl.php");
		 $dc=new DateControl();
		 
		 
		$tgl1=date("Y-m-01");
		$tgl2=$dc->lastOfMonth(date('m'),date('Y'));
        
        } 
	
    ?>

<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script language="JavaScript">

    var totalQtyJualReshare=0;
	$(document).ready(function(){
	    $('.hide').hide();
				
	});
	
	function xhrRequest2(type) {
            type = type ||  "html";
            xhrSend =  !window.XMLHttpRequest ? new ActiveXObject('Microsoft.XMLHTTP') : new XMLHttpRequest();
            if (xhrSend.overrideMimeType) {   xhrSend.overrideMimeType("text/" + type); }
        return xhrSend;
    }
	function whatWhere2(url, boxid) {
		   var reqType = "text";
		   var xhrRec = xhrRequest2(reqType);
		   document.getElementById(boxid).innerHTML = "Loading .....";
		   xhrRec.open("GET", url, true);
		   xhrRec.onreadystatechange =    function () {
				if (xhrRec.readyState == 4 && xhrRec.status == 200) {
				var rt= xhrRec.responseText;
			    document.getElementById(boxid).innerHTML = format("#,##0.####",rt);
				hitungTotalQtyReshare(rt);//Masukan Kenilai totalPoin
				//var js = tr.replace(/\<script[\w\W]+?\>/i,"").replace(/\<\\script\s*\>/i,"");
				//try { eval(js); } catch(err){document.getElementById(boxid).innerHTML = String(err); }
					   xhrRec = null;        
			}
			   if (xhrRec.readyState == 4 && xhrRec.status == 404) {
					document.getElementById(boxid).innerHTML = xhrRec.statusText;
					xhrRec = null;                
			}
		   }
				xhrRec.send(null);
	 }
	
	function hitungQtyReshare(id,tgl1,tgl2,model){
       //alert(id);
	  var url="analisa_po_jual_data.php?t1="+ tgl1+"&t2="+tgl2+"&m=" +model + "&j=o";
     //alert(url + " # "+ id); 
     whatWhere2(url,id); 
	// alert(url + " # "+ id); 
        
   }
   
   function hitungTotalQtyReshare(jumlah){
	    totalQtyJualReshare+= Number(jumlah);
		document.getElementById('totalQtySellReshare').innerHTML =format("#,##0.####",totalQtyJualReshare);
  // totalPoinValue+=Number(jumlah);
  // document.getElementById('total_poin_id').innerHTML =totalPoinValue;
  }
</script>
<script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
<form method="POST" action="<?php echo $PHP_SELF; ?>?action=search" name="outlet">
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
                <input name="txt_cari" type="text" value="<?php echo $model?>" >
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
    
        

<?php
 if (isset($_GET['action'])) { 
 
 }else{
	  include("footer.php");
	  die(); 
 }
 
  //ambil data do-po
		 $sql_do="SELECT SUBSTRING(a.kd_produk,1,7) as id ,SUM(IFNULL(k.jumlah_kirim,0)) AS jumlah_kirim 
		FROM (SELECT pd.kd_produk, SUM(pd.qty) AS jumlah FROM po_manufaktur_detail AS pd
		INNER JOIN po_manufaktur AS p ON (pd.no_manufaktur = p.no_manufaktur) 
		INNER JOIN produk i ON i.kode=pd.kd_produk LEFT JOIN mst_model 
		AS m ON (m.kode = i.kode_model) AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`) 
		AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`)     
		WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND p.closeco IS NULL   AND p.approve2=1 
		AND m.model LIKE '%$model%' and p.request_ke like '$supplier%' 
		GROUP BY pd.kd_produk) AS a LEFT JOIN (SELECT dd.kd_produk, SUM(dd.qty) AS jumlah_kirim FROM
		do_produk_detail AS dd
		INNER JOIN do_produk AS d 
		ON (dd.no_do = d.no_do) WHERE  d.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' 
		AND  d.no_do NOT LIKE 'BTL%'  GROUP BY dd.kd_produk) AS k  ON k.kd_produk=a.kd_produk
		GROUP BY SUBSTRING(a.kd_produk,1,7);";
		//ambil data 
	  $res_do=mysql_query($sql_do);
	  $arrayDo=array();
	  while(list($kd_model,$do)=mysql_fetch_array($res_do)){
		   $arrayDo[$kd_model]=$do;
	  }
	  
	  //get Retur
	   $sql_retur_do="SELECT SUBSTRING(a.kd_produk,1,7) AS id ,SUM(IFNULL(k.jumlah_retur,0)) AS jumlah_retur 
		FROM (SELECT pd.kd_produk, SUM(pd.qty) AS jumlah FROM po_manufaktur_detail AS pd
		INNER JOIN po_manufaktur AS p ON (pd.no_manufaktur = p.no_manufaktur) 
		INNER JOIN produk i ON i.kode=pd.kd_produk LEFT JOIN mst_model 
		AS m ON (m.kode = i.kode_model) AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`) 
		AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`)     
		WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' AND p.closeco IS NULL 
		AND m.model LIKE '%$model%' AND p.request_ke LIKE '$supplier%' 
		GROUP BY pd.kd_produk) AS a LEFT JOIN (SELECT rd.kd_produk, SUM(rd.qty) AS jumlah_retur FROM retur_distribusi_rian AS rd
INNER JOIN do_produk AS d 
ON (rd.no_do = d.no_do) WHERE  d.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' 
AND  d.no_do NOT LIKE 'BTL%'  GROUP BY rd.kd_produk) AS k  ON k.kd_produk=a.kd_produk
		GROUP BY SUBSTRING(a.kd_produk,1,7);";
		//ambil data 
	  $res_retur_do=mysql_query($sql_retur_do);
	  $arrayReturDo=array();
	  while(list($kd_model,$returdo)=mysql_fetch_array($res_returdo)){
		   $arrayReturDo[$kd_model]=$returdo;
	  }
	  
	
	
	  
   if($username=='budi-it'){
	   #echo $sql_do;  
	   
	   #print_r($arrayDo);
	   #print_r($arrayReturDo);  
   } 
?>
        <table border="0" width="100%" style="font-size: 8pt">
          <tr> 
            <td background="images/footer.gif" align="center" width="350"><strong>Model</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22"><b>Qty 
              Po </b></td>
            <td background="images/footer.gif" align="center" width="100"><strong>Qty CO </strong></td>
            <td background="images/footer.gif" align="center" width="100"><strong>% CO</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22"><b>Qty 
              DO</b></td>
            <td background="images/footer.gif" align="center" width="100"><strong>% DO</strong></td>
            <td background="images/footer.gif" align="center" width="100" height="22"><b>Target Penjualan</b></td>
            <td background="images/footer.gif" align="center" width="100" height="22"><b>Penjualan Reshare</b></td>
            <td background="images/footer.gif" align="center" width="100" ><strong>Perbandingan Target Dan Penjualan [%]</strong></td>
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
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.model like '%$model%' AND p.closeco IS NULL 
                     GROUP BY i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`
                     order by i.kode_model ,`i`.`kode_style`,`i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item` ASC LIMIT ".($page*$jmlHal).",".$jmlHal;
					// cari berdasarkan tanggal 
		 $sql2=" SELECT    SQL_CALC_FOUND_ROWS  m.model  , sum(pd.qty) ,  i.kode_model,`i`.`kode_style`,
                  `i`.`kode_kelas`,`i`.`kode_kategori` ,`i`.`kode_basic_item`    FROM   po_manufaktur AS p
                    INNER JOIN po_manufaktur_detail AS pd ON (p.no_manufaktur = pd.no_manufaktur)  LEFT JOIN produk AS i 
                    ON (i.kode = pd.kd_produk) LEFT JOIN mst_model AS m 
                    ON (m.kode = i.kode_model)  AND (`i`.`kode_style` = `m`.`kode_style`) AND (`i`.`kode_kelas` = `m`.`kode_kelas`)
                     AND (`i`.`kode_kategori` = `m`.`kode_kategori`) AND (`i`.`kode_basic_item` = `m`.`kode_basic_item`) 
                     WHERE  p.tanggal BETWEEN '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.model like '%$model%'  AND p.closeco IS NULL 
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
	
	
	
	$hsltemp2=mysql_query($sql2,$db) or die ('<h1>Error #'.mysql_error()."#$sql2".'</h1>');
    $hsltmp12=mysql_query($sql3,$db) or die ($sql3);
	list($jmlData[0])=mysql_fetch_array($hsltmp12);
	$no=($hal*$jmlHal);
	$stok_persub=0;
	$co_persub=0;
	$pengiriman_persub=0;
	$distribusi_qty_persub=0;
	$markas_qty_persub=0;
	$script_otl='';
	
	
	
	
	
	
	
    while ( list($model,$po,$kode_model,$kode_style,$kode_kelas,$kode_kategori,$kode_basic_item)=mysql_fetch_array($hsltemp2)) {
        $no++;
		
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
		
		
         ?>
          <tr onMouseOver="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>"> 
            <td>&nbsp;<?php echo "[$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model]$model";?></td>
            <td height="21" align="right">&nbsp;<?php echo number_format($po);$po_persub+=$po; ?></td>
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
			   
			  //echo $sql;
			  $res_co=mysql_query($sql) or die('Error '. $sql);      
              list($co)=mysql_fetch_array($res_co);     
			  if($co==''){
				  $co=0;
			  }
			  $co_persub+=$co;  
			  echo number_format($co);
			  $co_persen=($co/$po) * 100;
			
			?></td>
            <td align="right"><?php echo number_format($co_persen).' %';?></td>
            <?php   
			    $kd_model=$kode_basic_item.$kode_kategori.$kode_kelas.$kode_style.$kode_model;
			    $pengiriman=$arrayDo[$kd_model]-$arrayReturDo[$kd_model];
			     /*
                       $sql="SELECT  ifnull(SUM(md.qty),0) FROM  `do_produk_detail` AS `md`  INNER JOIN `do_produk` AS `m` 
                                ON (`md`.`no_do` = `m`.`no_do`) WHERE `md`.`kd_produk` LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%'
                                AND `m`.tanggal BETWEEN  '$tgl1 00:00:00' AND '$tgl2 23:59:59' and m.no_do not like 'BTL%'";
                       //echo $sql;
                        $res=mysql_query($sql) or die('Error '. $sql);      
                        list($pengiriman)=mysql_fetch_array($res);  */
						$pengiriman_persub+=$pengiriman;
						$do_persen=($pengiriman/$po) * 100;
    
            
?>
            
            <td height="21" align="right"><?php echo number_format($pengiriman); ?></td>
            <td align="right"><?php echo number_format($do_persen).' %';?></td>
            <?php
                     /*$sql_dist="SELECT IFNULL(SUM(stok),0) FROM produk_stok WHERE kode_gudang LIKE 'GD%' AND kode_produk LIKE '$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%' and stok>0";
                     $res_dis=mysql_query($sql_dist) or die('Error '. $sql_dist);      
                     list($distribusi_qty)=mysql_fetch_array($res_dis);  
					 $distribusi_qty_persub+=$distribusi_qty;*/
            ?>
            <td height="21" align="right"><?php echo number_format($target_penjualan); ?></td>
            <?php
			        $km="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model%";//kode model
					$id_mod="$kode_basic_item$kode_kategori$kode_kelas$kode_style$kode_model";
					
					
					//$rekapStok=new RekapStok($host,$user,$pass,$dbname);// variabel didapat dari connect config
					
                    //$j_outlet=qtySellOutlet($tgl1,$tgl2,$kode_model);
					
					//$j_outlet=$rekapStok->qtyNettoOutletActual($tgl1,$tgl2,$km);// ambil dari penjualan reshare langsung
					//$j_outlet2=$rekapStok->qtySellOutlet($tgl1,$tgl2,$kode_model); ambil dari laporan
					
					//$j_markas=$rekapStok->qtySellMarkas($tgl1,$tgl2,$kode_model);
					//$j_distribusi=$rekapStok->qtySellDistribusi($tgl1,$tgl2,$kode_model);
					
            ?>
            
            <td height="21" align="right"><?php //echo number_format($j_outlet);// echo " [".number_format($j_outlet2)."]";?><div id="<?php  echo "otl".$id_mod; ?>"></div></td>
            <?php
                     $script_otl.="hitungQtyReshare('otl$id_mod','$tgl1','$tgl2','$id_mod');"; 
					 // Yang B Tampilkan
                     if(substr($kode_basic_item,0,1)=='B'){
                            $terjual= $pengiriman - $distribusi_qty  -$markas_qty; 
                     }else{
                          $terjual= '';
                     }
                     
                        
           
       
            ?>
            <td align="right" >&nbsp;</td>
          </tr>
          <?php
	}
   
    ?>
          <tr> 
            <td height="23" background="images/notupload.jpg" align="center"><i><b>SUBTOTAL</b></i></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($po_persub); ?></td>
            <td height="23" background="images/notupload.jpg" align="right"><?php  echo number_format($co_persub);?></td>
            <td background="images/notupload.jpg" align="right">&nbsp;</td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($pengiriman_persub); ?></td>
            <td background="images/notupload.jpg" align="right">&nbsp;</td>
            <td height="23" background="images/notupload.jpg" align="right"><?php echo number_format($distribusi_qty_persub); ?></td>
            <td height="23" background="images/notupload.jpg" align="right"><div id="totalQtySellReshare">0</div></td>
            <td background="images/notupload.jpg" width="105" align="right">&nbsp;</td>
          </tr>
          <tr> 
            <td height="25" background="images/yesupload.jpg" align="center">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg" width="105" align="right" >&nbsp;</td>
          </tr>
        </table>

<table style="margin-left:10px; margin-top:10px;">
<tr>
						<td class="text_standard">
							Page : 
						  <span class="hal" onClick="location.href='analisa_po_jual.php?&hal=0<?php echo $tambah?>';">First</span>
							<?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
								if($hal<=0){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='analisa_po_jual.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
									if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
						  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='analisa_po_jual.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
  <?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
  <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='analisa_po_jual.php?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
  <span class="hal" onClick="location.href='analisa_po_jual.php?&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?><?php echo $tambah?>';">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>						</td>
				</tr>
</table>
<script language="JavaScript"> 
<?php
   echo $script_otl;// tambahan tanggal 20 feb 2012 
   
?>
</script>

<?php include_once "footer.php"; ?>
