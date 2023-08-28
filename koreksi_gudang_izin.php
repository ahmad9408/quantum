<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="Form Koreksi Izin"; 
/*



*/


?>
<style>
.flat{
    border-top-style: none;
	border-right-style: none;
	border-bottom-style: none;
	border-left-style: none;
	background-color: #FFFFFF;
	
}
</style>
<style type="text/css">

.myButton{
-webkit-box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
	-moz-box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
	box-shadow:rgba(0,0,0,0.2) 0 1px 0 0;
	border-bottom-color:#333;
	border:1px solid #61c4ea;
	background-color:#7cceee;
	border-radius:4px;
	-moz-border-radius:4px;
	-webkit-border-radius:4px;
	color:#333;
	font-family:'Verdana',Arial,sans-serif;
	font-size:10px;
	text-shadow:#b2e2f5 0 1px 0;
	padding:2px
}

</style>
<?php 
 $lihat=1;
 if($lihat==1){ 
    include('header.php');
 }
  $thispage=$_SERVER['PHP_SELF'];

	$tgl1=$_POST['tgl1'];
	$tgl2=$_POST['tgl2'];
	$today=date('Y-m-d');
	
	
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
         array_push($aryRange,date('Y-m-01',$iDateFrom)); //  entry
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
   
   function intervalHari($strDateFrom,$strDateTo) {
        $hasil=1;
       //echo "($strDateFrom,$strDateTo)";
    

       $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
       $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

       if ($iDateTo>=$iDateFrom) {
         while ($iDateFrom<$iDateTo) {
		  $hasil++;
           $iDateFrom+=86400; // add 24 hours
          
         }
       }
       return  $hasil;
   }
   
   $array_bulan = array('01'=>'Januari','02'=>'Februari','03'=>'Maret', '04'=>'April', '05'=>'Mei',
 	'06'=> 'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober', '11'=>'November','12'=>'Desember');
	$tahun_skrg=date('Y');
	$bulan_skrg=date('m');
    
   if (isset($_GET['hal'])) { 		      
		  $tgl1=$_SESSION['tgl1'];
		  $tgl2=$_SESSION['tgl2'];
		  $barangCari=$_SESSION['barangCari'];
			       
	}elseif (isset($_GET['action'])) { 
		session_start();
		$tambah="&action=search";       
              $_SESSION['tgl1']=$_POST['tgl1'];
              $_SESSION['tgl2']=$_POST['tgl2'];
			  $tgl1=$_SESSION['tgl1'];
              $tgl2=$_SESSION['tgl2'];
			  $barangCari=$_POST['txtBarangCari'];
			  $_SESSION['barangCari']=$barangCari;
       
	} else {
		unset($_SESSION['tgl1']);
        unset($_SESSION['tgl2']);
		unset($_SESSION['barangCari']);
		
	
	}
	
	
  if(empty($tgl1)){
            $tgl1=date("Y-m-01");
			$h=jumlahHari(date('m'),date('Y'));		
            $tgl2=date("Y-m-").$h;
        
        } 

  $data_periode=split('-',$tgl1);
  $filter_periode=$data_periode[0].'-'.$data_periode[1];
	
    ?>
	
<!--Date Time Picker -->
<link type="text/css" href="css3/smoothness/ui.all.css" rel="stylesheet" /> 
<link type='text/css' href='css/demo.css' rel='stylesheet' media='screen' />
<!-- Contact Form CSS files -->
<link type='text/css' href='css/basic.css' rel='stylesheet' media='screen' />  
 <script type="text/javascript" src="jquery-1.3.2.min.js"></script>
    <script type="text/javascript" src="ui/ui.core.js"></script>
    <script type="text/javascript" src="ui/ui.datepicker.js"></script>    
    <script type="text/javascript" src="ui/i18n/ui.datepicker-id.js"></script>    

    <script type="text/javascript"> 
	  var jquery4=$.noConflict(true);
      jquery4(document).ready(function()
	  {
        jquery4(".tanggal").datepicker({
		dateFormat      : "yy-mm-dd"
		});
      });
</script>	

<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="text/javascript" src="sortable.js"></script> 
<script language="JavaScript" src="jquery.timer.js"></script>


<!-- Auto complete -->
<!-- Jquery Autocomplete -->
<script type='text/javascript' src='jquery.autocomplete.js'></script>
<script type='text/javascript' src='koreksi_gudang_izin.js'></script>
<script>
$(document).ready(function(){
   $('#bulan1').change(function(){
	   setTgl();
	   
   })
   
   $('#tahun1').change(function(){
	   setTgl();
	   
   })


})
function daysInMonth(month,year) {
   var dd = new Date(year, month, 0);
   return dd.getDate();
} 

function setTgl(){
   var tahun=$('#tahun1').val();
   var bulan=$('#bulan1').val();
   
   var jmlhHari=daysInMonth(Number(bulan),Number(tahun));
   $('#tgl1').val(tahun +'-' + bulan + '-01');
   $('#tgl2').val(tahun +'-' + bulan + '-' + jmlhHari);
   
  // alert(jmlhHari + ' untuk Bulan dan tahun ' + tahun + ' ' + bulan);
}
</script>
<link rel="stylesheet" type="text/css" href="jquery.autocomplete.css" />

<fieldset>

<form method="POST" action="<?php echo $thispage; ?>?action=search" name="outlet">
  <!-- tengah -->
   
    
    
		<table class="otomatis">
			
			
			
			<tr>
			  <td width="111" valign="top">Gudang</td>
			  <td width="3" valign="top">:</td>
			  <td valign="top"><input type="text" name="txtBarangCari" id="txtBarangCari" value="<?php echo "$barangCari" ?>"/></td>
          </tr>
			<tr>
			   <td valign="top">Tgl koreksi Turun</td>
			   <td valign="top">:</td>
			  <td width="313" valign="top"><input type="text" name="tgl1" readonly id="tgl1" value="<?php echo $tgl1; ?>" size="10" class="tanggal"/> 
			  Sampai  
			  <input type="text" name="tgl2" readonly id="tgl2" value="<?php echo $tgl2; ?>" size="10" class="tanggal"/>
              </td>
		    </tr>
			<tr>
			  <td valign="top">&nbsp;</td>
			  <td valign="top">&nbsp;</td>
			  <td valign="top"><input name="submit" type="submit" value="         Cari         " class="myButton"/></td>
	      </tr>
		</table>
</form>
</fieldset>
<?php /*if (isset($_GET['action'])){include("progress_bar.php"); }*/?>
      <input type="button" id="add" value="Add" />
        <table border="0" width="1165" style="font-size: 8pt" class="sortable" id="myTable">
          <tr> 
            <td width="74" height="22" align="center" background="images/footer.gif"><strong>NO</strong></td>
            <td background="images/footer.gif" align="center" width="124"><span style="font-weight: bold">Tanggal</span></td>
            <td background="images/footer.gif" align="center" width="267">Gudang</td>
            <td background="images/footer.gif" align="center" width="111">Jenis Koreksi</td>
            <td background="images/footer.gif" align="center" width="411"><span style="font-weight: bold">Keterangan</span></td>
            <td background="images/footer.gif" align="center" width="97"><strong>Tgl Approve</strong></td>
            <td background="images/footer.gif" align="center" width="51"><span style="font-weight: bold">update by</span></td>
          </tr>
          <?php
	if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
	$jmlHal=100;
	$page=$hal;
	if (isset($_GET['action'])) { 
		
       		// cari berdasarkan tanggal 
			
		$sql2="SELECT   SQL_CALC_FOUND_ROWS  k.id,date_format(k.tanggal,'%Y-%m-%d'),k.id_gudang,a.nama,k.keterangan,k.update_by,k.update_date,tipe_so FROM koreksi_gudang_stok_izin k INNER JOIN 
(SELECT id,nama FROM outlet WHERE  jenis IN (1,2) 
UNION SELECT id,nama FROM gudang_distribusi ) AS a  ON a.id=k.id_gudang  where k.tanggal between '$tgl1' and '$tgl2' 
and (a.id like '%$barangcari%' or a.nama like '%$barangcari%' ) and k.id not like 'BTL%' ORDER BY k.tanggal DESC LIMIT ".($page*$jmlHal).",".$jmlHal;
		$sql3="SELECT FOUND_ROWS()";
		
		
		
		
	} else {
		
		 $sql2="SELECT   SQL_CALC_FOUND_ROWS  k.id,date_format(k.tanggal,'%Y-%m-%d'),k.id_gudang,a.nama,k.keterangan,k.update_by,k.update_date,tipe_so FROM koreksi_gudang_stok_izin k INNER JOIN 
(SELECT id,nama FROM outlet WHERE  jenis IN (1,2) 
UNION SELECT id,nama FROM gudang_distribusi ) AS a  ON a.id=k.id_gudang  WHERE k.id not like 'BTL%' ORDER BY k.tanggal DESC LIMIT ".($page*$jmlHal).",".$jmlHal;
        $sql3="SELECT FOUND_ROWS()";
		
			
		
	}
	

  if($username=='budi-it'){
	  echo $sql2;
	}	
	
	
	
	$hsltemp2=mysql_query($sql2,$db);// or die ('<h1>Error #'.mysql_error()."#$sql2".'</h1>');
    $hsltmp12=mysql_query($sql3,$db);// or die ($sql3);
	list($jmlData[0])=mysql_fetch_array($hsltmp12);
	$no=($hal*$jmlHal);
	$kd_before='';
	$counter=0;
	while ( list($kd_izin,$tgl,$id_gudang,$nama_gudang,$keterangan,$app_by,$app_date,$jenis_koreksi)=mysql_fetch_array($hsltemp2)) {
        $counter++;		
		  $no=$counter;
		  $btnBatal='<input type="button" name="batal" value="Batal" id="btnbatal_'.$kd_izin.'" onclick="batal(\''.$kd_izin.'\',\'' . $counter.'\',\''.$tgl.'\',\''.$id_gudang.'\');" class="btnKembali" />';
		
		
		
		if($jenis_koreksi=='-'){
			$jenis_koreksi_v='Turun';
		}elseif($jenis_koreksi=='+'){
			$jenis_koreksi_v='Naik';
		}else{
			$jenis_koreksi_v='All';
		}
		
        $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
		
		
         ?>
          <tr <?php echo $blink; ?> onMouseOver="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>" id="data_<?php echo $counter;?>" > 
          <td height="20" ><?php echo $no; ?></td>
		    <td><?php  echo $tgl; ?></td>
            <td><?php  echo $nama_gudang; ?></td>
            <td><?php  echo $jenis_koreksi_v; ?></td>
            <td><?php  echo $keterangan; ?></td>
            <td><?php  echo $app_date.' '.$app_by;?></td>
            <td id='kb_<?php echo $no; ?>'><?php  echo $btnBatal; ?></td>
            
          </tr>
          <?php
	}
   
    ?>
         <tfoot>
          <tr> 
            <td height="25" background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
            <td background="images/yesupload.jpg">&nbsp;</td>
           </tr>
          </tfoot>
</table>

<table style="margin-left:10px; margin-top:10px;" id="page">
<tr>
						<td class="text_standard">
							Page : 
							<span class="hal" onClick="location.href='<?php echo $thispage; ?>?&hal=0<?php echo $tambah?>';">First</span>
							<?php for($i=0;$i<($jmlData[0]/$jmlHal);$i++){ 
								if($hal<=0){ ?>
									<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
									<?php if($i>=4) break;
								}else if(($hal+1)>=($jmlData[0]/$jmlHal)){
									if($i>=(($jmlData[0]/$jmlHal)-5)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
  <?php } 
								}else{
									if($i<=($hal+2)and $i>=($hal-2)){ ?>
										<span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo $i; ?><?php echo $tambah?>';"><?php echo ($i+1); ?></span>
								  <?php }
								}
							} ?>
							<span class="hal" onClick="location.href='<?php echo $thispage; ?>?&hal=<?php echo intval(($jmlData[0]/$jmlHal)); ?><?php echo $tambah?>';">Last</span>
							&nbsp;&nbsp;
							Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo $jmlData[0]; ?>						</td>
				</tr>
</table>
<script language="JavaScript"> 
  var no=<?php echo $counter ?>;
  var tgl1='<?php echo $tgl1 ?>';
   var tgl2='<?php echo $tgl2 ?>';
   var today='<?php echo $today;   ?>';
   var username='<?php echo $username ?>';
<?php
  if (isset($_GET['action'])){
?>	  
   jmlProcess=1;   
   progressBar();
   
   
   

<?php  
  }   
  
  
  
  //var myOptions = [{ text: 'Suganthar', value: 1}, {text : 'Suganthar2', value: 2}];
  
?>
 
   
</script>
<span id="debug"></span>
<?php include_once "footer.php"; ?>