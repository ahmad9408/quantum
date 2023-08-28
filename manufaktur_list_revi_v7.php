<?php $content_title="DAFTAR PERMINTAAN MANUFAKTUR"; 
/*
v4 edit rian without session
v5 edit budi (13 nov 2012 variable =terusan di set null untuk alasan keamanan dan session diberlakukan kembali
  tambah fasilitas pemilihan planning atau real po)

*/
if(isset($_POST['j'])){
	session_start();
    @$username=$_SESSION["username"];
   if(empty($username)){
       die('You can\'t see this page');
    }
    
	
	$nama=$_POST['nm'];
	$isi=$_POST['is'];
	$lanjut=$_POST['lj'];
	$tujuan=$_POST['tj'];
	$model=trim($_POST['m']);
	
	
  
 
    require_once("config.php");
	
	if(!empty($model)){
		$sql="SELECT SUM(po_manufaktur_detail.qty),SUM(po_manufaktur_detail.jumlah) FROM po_manufaktur p,po_manufaktur_detail left join produk  on po_manufaktur_detail.kd_produk=produk.kode LEFT JOIN mst_model AS m   ON (m.kode = produk.kode_model) AND (m.kode_style = produk.kode_style) 
	AND (m.kode_kelas = produk.kode_kelas) AND (m.kode_kategori = produk.kode_kategori) AND (m.kode_basic_item = produk.kode_basic_item) WHERE m.model like '%$model%' AND 
  p.no_manufaktur=po_manufaktur_detail.no_manufaktur AND p.closeco IS NULL AND (produk.nama LIKE  '%$nama%') $isi $lanjut and request_ke like '$tujuan%'";
	}else{
		$sql="SELECT SUM(po_manufaktur_detail.qty),SUM(po_manufaktur_detail.jumlah) FROM po_manufaktur p,po_manufaktur_detail left join produk  on po_manufaktur_detail.kd_produk=produk.kode WHERE 
  p.no_manufaktur=po_manufaktur_detail.no_manufaktur AND p.closeco IS NULL AND (produk.nama LIKE  '%$nama%') $isi $lanjut and request_ke like '$tujuan%'";
	}
	
	
 
  //die($sql);
    $res=mysql_query($sql) or die(mysql_error().'#'.$sql);
    list($totalQty,$totalRp)=mysql_fetch_array($res);
    die($totalQty.'#'.$totalRp);
 
 
}

/*
v4 : ditambah supplier


*/
include_once "header.php" ;
$thisPage=$_SERVER['PHP_SELF'];


?>
<style>
.header1{
	display:block;
	font-weight:bold;
	text-align:center;
	width:300;
	
}
.header2{
	display:block;
	font-weight:bold;
	text-align:center;
	width:100;
	
}

.header4{
	display:block;
	font-weight:bold;
	text-align:center;
	width:100;
}
.headerNominal{
	display:block;
	font-weight:bold;
	text-align:center;
	width:75;
}


</style>
<script language="JavaScript" src="format.20110630-1100.min.js"></script>
<script type="application/javascript">
    function closingPo(id,no_po,jenis){
		var alasan='';
		if(jenis=='plan'){
		   var tambah=' Planning ';
		}else{
		   var tambah='';
		}
	    if(confirm('Anda yakin ingin menutup '+ tambah +' PO '+ no_po )){
			if(alasan=prompt('Keterangan?')){
				$.ajax({
					url:'po_manufaktur_close_v2.php',
					type:"GET",
					dataType:'text',
					data:{no_manufaktur:no_po ,desc:alasan,j:jenis},
					success: function(message) {
					    //alert(message);
						response=message.split('#');
						if(response[1]=='ok'){// hide row dengan id ini
						   $('#'+ id ).hide();
						   //alert(id + ' Will be hidden');
							
						}else{
						   alert(response[2]);// tampilkan pesan errornya	
						}
    					
						
    					//alert( "Data Saved: " + response  + ' # ' + myid);
 					}				
				});
				
			  //window.location='po_manufaktur_close.php?no_manufaktur='+no_po+'&desc='+alasan;
			 }
		}
	
	}
	
	function getTotal(cari,isi,lanjut,tujuan,model){
		
	   $.ajax({
			url:"<?php echo $thisPage; ?>",
			type:"POST",
			cache: false,
			dataType:'text',
			data:{nm:cari,j:'t',is:isi,lj:lanjut,tj:tujuan,m:model},// jenis internal
			success: function(data) {//alert(data);
				//alert(data);return;
				try{
					var d=data.split('#');
				    $('#totalQty').text(format('#,##0.#0',d[0]));
				    $('#totalRp').text(format('#,##0.#0',d[1]));
				}catch(e){
				   alert(e.message);	
				}
				
						
			}				
		});	
		
	}
</script>
<?php
 /* 
 v2. 20 April 2012
 Tambah Closoing Po Dengan Bantuan Jquery with hide id after clossing
 
 */
    $no_manufaktur=sanitasi($_GET["no_manufaktur"]);
    
    if($_GET[action]=="delete"){
        $no_manufaktur=$_GET[no_manufaktur];
        ?>
            <script language="javascript">
                alert("Apakah Yakin Akan Menghapus PO : <?php echo $no_manufaktur; ?> ");
                window.location="<?php echo $thisPage; ?>";
            </script>
        <?php
        
        $sql="DELETE FROM po_manufaktur WHERE no_manufaktur='$no_manufaktur'";
        $hsltemp=mysql_query($sql,$db);
        exit;
    }
?>

 <?php 
 // Ganti By Extreme
 $jml_hari= cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));
 $tgl1=date("Y-m")."-01";
 $tgl2=date("Y-m")."-".$jml_hari;
 
  if (isset($_GET['hal'])) { 		      
		  $tgl1=$_SESSION['tgl1'];
		  $tgl2=$_SESSION['tgl2'];
		  $cari=$_SESSION['cari'];
		  $cari2=$_SESSION['cari2'];
		  $tujuan=$_SESSION['tujuan'];
		  $jenis=$_SESSION['jenis'];
		  $txt_model=$_SESSION['txt_model'];
		  
		   $txt_nopo=$_SESSION['txt_nopo'];
		   $txt_limit=$_SESSION['txt_limit'];
			       
	}elseif (isset($_GET['action'])) { 
		session_start();
		$tambah="&action=search";
       
			  $cari=$_POST['cari'];
			  $cari2=$_POST['cari2'];
              $tgl1=$_POST['tgl1'];
              $tgl2=$_POST['tgl2'];
			  $cari=$_POST['cari'];
		      $tujuan=$_POST['tujuan'];
			  $jenis=$_POST['jenis'];
			  $txt_model=trim($_POST['txt_model']);
			  $_SESSION['tgl1']=$tgl1;
		      $_SESSION['tgl2']= $tgl2;
		      $_SESSION['cari']=$cari;
		      $_SESSION['cari2'] =$cari2;
		     $_SESSION['tujuan']=$tujuan;
		     $_SESSION['jenis']=$jenis;
			 $_SESSION['txt_model']=$txt_model;
			 
			 $_SESSION['txt_nopo']=trim($_POST['txt_nopo']);
		     $_SESSION['txt_limit']=trim($_POST['txt_limit']);
			 $txt_nopo=$_SESSION['txt_nopo'];
		     $txt_limit=$_SESSION['txt_limit'];
       
	}else{
		unset($_SESSION['tgl1']);
        unset($_SESSION['tgl2']);
		unset($_SESSION['cari']);
		unset($_SESSION['cari2']);
		unset($_SESSION['tujuan']);
		unset($_SESSION['jenis']);
		
   } 
   
   
   if(empty($txt_limit)){
	   $txt_limit=100;	
	}
   function jumlahHari($month,$year) {
	   return date("j", strtotime('-1 second',strtotime('+1 month',strtotime($month.'/01/'.$year.' 00:00:00'))));
	}
   if(empty($tgl1)){
            $tgl1=date("Y-m-01");
			$h=jumlahHari(date('m'),date('Y'));		
            $tgl2=date("Y-m-").$h;        
   } else{
	   $lanjut=" and p.tanggal between '$tgl1 00:00:00' and '$tgl2 23:59:59' ";
   }
 
   if(isset($_GET['hal'])) $hal=$_GET['hal']; else $hal=0;
   
    
	$jmlHal=$txt_limit;
	
	$page=$hal;
        /*$sql="SELECT p.no_manufaktur,p.tanggal,produk.nama,p.totalqty,p.totalrp,
p.totalqty-SUM(job_gelaran_detail.qty_produk) AS sisa,p.approve,p.approveby,p.approve2,p.approveby2,p.closeco,date_format(approvedate,'%Y-%m-%d %T'),date_format(approvedate2,'%Y-%m-%d %T')
FROM po_manufaktur,job_gelaran_detail,produk WHERE job_gelaran_detail.no_po=p.no_manufaktur AND job_gelaran_detail.kd_produk=produk.kode AND 
p.closeco IS NULL  and (produk.nama like '%$_REQUEST[cari]%') $isi  $lanjut
GROUP BY p.no_manufaktur";
        $query=mysql_query($sql,$link);
        $jmlData=mysql_num_rows($query);*/
        ?>    
    
 <script src="jquery.jeditable.js" type="text/javascript"></script>   
 <script type="text/javascript">
	   $(function() {
	   $(".edit_text").editable("manufaktur_list_revi_data.php", {
		  indicator : "<img src='ajax-loader.gif'>",
		  submitdata: { _method: "post" },		 
		  onblur:'submit',
		  width : "200",
		  event     : "dblclick",
		  loadtext  : 'Updating…',
		  type:'textarea',
		  cols:"20",
		  rows:"1.5"
		  });
	});
	$.editable.addInputType('autogrow', {
    element : function(settings, original) {
        var textarea = $('<textarea>');
        if (settings.rows) {
            textarea.attr('rows', settings.rows);
        } else {
            textarea.height(settings.height);
        }
        if (settings.cols) {
            textarea.attr('cols', settings.cols);
        } else {
            textarea.width(settings.width);
        }
        $(this).append(textarea);
        return(textarea);
    }
});
	</script>
    
        
   <form method="POST" action="<? echo $PHP_SELF;?>?action=search&rnd=<?php echo date('YmdHis');?>" name="outlet">         
    <table>
        <tr>
            <td width="191"><input type="button" value="Tambah Manufaktur" onclick="window.location='po_manufaktur_add.php';" /></td>
            <td width="12">&nbsp;</td>
            <td width="514">&nbsp;</td>
        </tr>
        <tr>
          <td><strong>&nbsp;&nbsp;&nbsp;Dari &nbsp;&nbsp;&nbsp;</strong></td>
          <td><strong>:</strong></td>
          <td> <script language="JavaScript" src="calendar_us.js"></script>
            <link rel="stylesheet" href="calendar.css">
            <input type="text" name="tgl1"  id="startdate" value="<?php echo $tgl1; ?>" size="16"/> &nbsp;
             
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'startdate'
              });
            </script>
             &nbsp;
             &nbsp;
             
             <input type="text" name="tgl2" id="enddate" value="<?php echo $tgl2; ?>" size="16"/> &nbsp;
            
            <script language="JavaScript">
              new tcal ({
                // form name
                'formname': 'outlet',
                // input name
                'controlname': 'enddate'
              }); 
            </script></td>
        </tr>
        <tr>
          <td>No PO</td>
          <td>:</td>
          <td><input type="text" name="txt_nopo" id="txt_nopo" value="<?php echo $txt_nopo;?>"  style="width:400px;"/></td>
        </tr>
        <tr>
          <td><strong>Proces</strong></td>
          <td><strong>:</strong></td>
          <td><select name="cari2">
            <option value="" <?php if($cari2==""){ ?> selected=""<?}?>>--Pilih--</option>
            <option value="0" <?php if($cari2=="0"){ ?> selected=""<?}?>>Sisa</option>
            <option value="1" <?php if($cari2=="1"){ ?> selected=""<?}?>>Sudah Habis</option>
          </select></td>
        </tr>
        <tr>
          <td><strong>Nama Produk</strong></td>
          <td><strong>:</strong></td>
          <td><input type="text" name="cari" value="<?php echo $cari;?>" size="25 " /></td>
        </tr>
        <tr>
          <td>Nama Model</td>
          <td>:</td>
          <td><input type="text" name="txt_model" value="<?php echo $txt_model;?>" size="25 " /></td>
        </tr>
        <tr>
          <td><strong> Tujuan</strong></td>
          <td><strong>:</strong></td>
          <td><select name="tujuan" size="1" id="tujuan">
			    <?php  
				
				    $sql="SELECT SQL_CACHE LOWER(IFNULL(nama_po,nama)) as nama_po,nama FROM gudang_distribusi WHERE jenis=1";
				   $res_gdg=mysql_query($sql);
				
				    $arrayJenis=array(''=>'ALL');
				   while(list($id,$nama)=mysql_fetch_array($res_gdg)){
					   $arrayJenis[$id]=$nama;
				   }
				 
				  # $arrayJenis=array(''=>'ALL','suho'=>'Suho','supplier'=>'Supplier' );
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
        <tr>
          <td><span style="font-weight: bold">Jenis</span></td>
          <td>:</td>
          <td><select name="jenis" size="1" id="jenis">
            <?php  
				   $arrayJ=array('real'=>'Real','plan'=>'Plan' );
				   foreach($arrayJ as $key=>$value){
					   if($key==$jenis){
						    echo "<option value='$key' selected='selected'>$value</option>";
					   }else{
						    echo "<option value='$key'>$value</option>";
					   }
					   
				   }
				
				?>
          </select></td>
        </tr>
        <tr>
          <td>Jumlah Data</td>
          <td>:</td>
          <td><label for="textfield"></label>
          <input type="text" name="txt_limit" id="txt_limit" value="<?php echo $txt_limit;?>" style="width:100px;"/></td>
        </tr>
        <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td><input type="submit" name="submit" value="Lihat" /></td>
        </tr>
    </table>
</form>
    <?php
	$filter='';
	if(!empty($txt_nopo)){
	   $filter.=" AND p.no_manufaktur like '%$txt_nopo%'";	
	}
	
   if($cari2=='0')
   {
       $isi=" and (p.totalqtyok='0' or p.totalqtyok is NULL)";
   }else if($cari2=='1')
   {
      $isi=" and p.totalqtyok='1'";
   }else
   {
       $isi="";
   }
   
   if($jenis=='plan'){
      $plan='_planning';
	  $cari_3='&j=p';
   }else{
      $plan='';
	  $cari_3='';
   }
  
    $field_tambahan=',TRIM(produk.kode_basic_item),TRIM(produk.kode_kategori),TRIM(produk.kode_kelas),TRIM(produk.kode_style),TRIM(produk.kode_model)';
    if(!empty($txt_model)){
		
		$sql="SELECT SQL_CALC_FOUND_ROWS p.no_manufaktur,DATE_FORMAT(p.tanggal,'%Y-%m-%d'),produk.nama,SUM(pd.qty),SUM(pd.jumlah),
p.approve,p.approveby,p.approve2,p.approveby2,p.closeco,closecoby,closedate,date_format(approvedate,'%Y-%m-%d %T'),date_format(approvedate2,'%Y-%m-%d %T')  $field_tambahan
FROM po_manufaktur".$plan." p,po_manufaktur".$plan."_detail pd left join produk  on pd.kd_produk=produk.kode
     LEFT JOIN mst_model AS m   ON (m.kode = produk.kode_model) AND (m.kode_style = produk.kode_style) 
	AND (m.kode_kelas = produk.kode_kelas) AND (m.kode_kategori = produk.kode_kategori) AND (m.kode_basic_item = produk.kode_basic_item)
 WHERE  p.no_manufaktur=pd.no_manufaktur AND  (produk.nama LIKE  '%$cari%') $isi $lanjut and request_ke like '$tujuan%'  AND m.model like '%$txt_model%' 
  $filter 
 GROUP BY p.no_manufaktur ORDER BY p.approve,p.approve2,p.tanggal  DESC limit ".($page*$jmlHal).",".$jmlHal;
	}else{
		 $sql="SELECT SQL_CALC_FOUND_ROWS p.no_manufaktur,DATE_FORMAT(p.tanggal,'%Y-%m-%d'),produk.nama,SUM(pd.qty),SUM(pd.jumlah),
p.approve,p.approveby,p.approve2,p.approveby2,p.closeco,closecoby,closedate,date_format(approvedate,'%Y-%m-%d %T'),date_format(approvedate2,'%Y-%m-%d %T')  $field_tambahan
FROM po_manufaktur".$plan." p,po_manufaktur".$plan."_detail pd left join produk  on pd.kd_produk=produk.kode WHERE 
  p.no_manufaktur=pd.no_manufaktur AND   (produk.nama LIKE  '%$cari%') $isi $lanjut and request_ke like '$tujuan%'  $filter GROUP BY p.no_manufaktur ORDER BY p.approve,p.approve2,p.tanggal  DESC limit ".($page*$jmlHal).",".$jmlHal;

	}
	   
	   if($username=='budi-it'){
		  echo "<h3>$sql</h3>";   
	   }
   
      //echo "$sql";
	  $query=mysql_query($sql)or die($sql);
	  $sql="SELECT FOUND_ROWS()";
	   $q2=mysql_query($sql);
      list($jmlData)=mysql_fetch_array($q2);
       $j=($page*$jmlHal);
	
	?>
<table style="margin-left:10px; margin-top:10px;display:none;" >
<tr>
                <td class="text_standard">
                    Page : <?php $terusan='';//dirubah oleh budi(untuk keamanan menggunakan session)"&cari=$cari&tgl1=$tgl1&tgl2=$tgl2&cari2=$cari2&tujuan=$tujuan&tuj=$tujuan";?>
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=0<?php echo $terusan?>';">First</span>
                    <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
                        if($hal<=0){ ?>
                            <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php if($i>=4) break;
                        }else if(($hal+1)>=($jmlData/$jmlHal)){
                            if($i>=(($jmlData/$jmlHal)-5)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php } 
                        }else{
                            if($i<=($hal+2)and $i>=($hal-2)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php }
                        }
                    } ?>
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo intval(($jmlData/$jmlHal)); ?><?php echo $terusan?>';">Last</span>
                    &nbsp;&nbsp;
                    Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData; ?> Data
                </td>
            </tr>
 </table>
 <br />
<table border="1" width="800" style="font-size: 10pt" cellspacing="0" cellpadding="0" bordercolor="#ECE9D8" height="92">
    <tr>
        <td width="17" height="21" align="center" bgcolor="#99CC00"><strong>No</strong></td>
        <td align="center" bgcolor="#99CC00" height="21" ><span class="header1">  No Manufaktur </span></td>
        <td align="center" bgcolor="#99CC00" height="21" ><span class="header2">Tanggal</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="91"><span class="header1">Produk</span></td>
        <td align="center" bgcolor="#99CC00" width="91"><span class="header1">Model</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="67"><span class="headerNominal"> Total Qty</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="109"><span class="headerNominal">Jumlah (Rp)</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="66"><span class="headerNominal">Sisa</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="167"><span class="header4"> Approve I</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="167"><span class="header4"> Approve II</span></td>
        <td align="center" bgcolor="#99CC00" height="21" width="200"><span class="header4"> Action</span></td>
    </tr>
<?php
      
        $sub_qty=0;
		$sub_rp=0;
		$arrayModel=array();
        while(list($no_po,$tanggal,$nama,$qty,$amount,$app,$appby,$app2,$appby2,$closeco,$closeby,$closedate,$appdate,$appdate2,$kode_basic_item,$kode_kategori,$kode_kelas,$kode_style,$kode_model)=mysql_fetch_array($query))
        { 
		  $j++;
		  $sub_qty+=$qty;
		  $sub_rp+=$amount;
        
		   $kode_model_complete=$kode_basic_item.$kode_kategori.$kode_kelas.$kode_style.$kode_model;
		   if(array_key_exists($kode_model_complete,$arrayModel)){
			  $model=$arrayModel[$kode_model_complete];
		   }else{
			  $sql_model="SELECT TRIM(model) from mst_model WHERE kode_basic_item='$kode_basic_item' AND kode_kategori='$kode_kategori' AND kode_kelas='$kode_kelas' AND kode_style='$kode_style' AND kode='$kode_model';"; 
			  $rest_model=mysql_query($sql_model);
			  list($model)=mysql_fetch_array($rest_model);
			  if($username=='budi-it'){
				  #echo $sql_model;
			  }
			  
		   }
		   
		   
         
            $sql="SELECT sum(qty_produk) FROM `job_gelaran_detail` WHERE `no_po`='$no_po'";
                $res=mysql_query($sql)or die($sql);
                list($sudah)=mysql_fetch_array($res);
                $sisa=$qty-$sudah;
            if($sisa<=0)
            {
				 
				$sql1="SET autocommit = 0;"; //tambhan 19072022 13:37
                $query1=mysql_query($sql1);
                
                $sql2="START TRANSACTION;";
                $query2=mysql_query($sql2); 
				 
                $sql="update po_manufaktur set totalqtyok='1' where no_manufaktur='$no_po'; -- ".basename(__FILE__);
                $res=mysql_query($sql)or die($sql);
				
				$sql3="COMMIT;";   
                $query3=mysql_query($sql3);
            }
            
            
            if($app==1)
            {
                $status="<strong>Approved - [$appby]<strong>";
            }else
            {
                $status="<blink><strong><font color='#FF0000'>Belum Approve 1</font></strong></blink>";
            }
            
            if($app2==1)
            {
                $status2="<strong>Approved - [$appby2]<strong>";
            }else
            {
                 $status2="<blink><strong><font color='#FF0000'>Belum Approve 2</font></strong></blink>";
            }
            $id=str_replace("/", "_", $no_po);
            
             $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F"; $bgcolor = ( $j % 2 ) ? $bgclr1 : $bgclr2;
            ?>
            <tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>" id="po_<?php echo $id?>">
            <td height="23" align="center"><?php echo $j; ?></td>
            <td height="23" align="left"  >&nbsp;<?php echo $no_po; ?></td>
            <td height="23" align="center"   class="edit_text" id="<?php echo trim($no_po); ?>"><?php echo $tanggal; ?></td>
            <td height="23" align="left" width="91">&nbsp;<?php echo $nama; ?></td>
            <td align="left" width="91">&nbsp;<?php echo $model; ?></td>
            <td height="23" align="right" width="67"><?php echo number_format($qty,2,".",","); ?></td>
            <td height="23" align="right" width="109"><?php echo number_format($amount,2,".",","); ?></td>
            <td height="23" align="right" width="66">
            <?php if($sisa>0){$warna="#FF0000";}else{
                
                $warna="#000000";
            }?>
            <font color="<?php echo $warna?>"><?php echo  number_format($sisa,2,".",",");?></font></td>
            <td height="23" align="left" width="167">&nbsp;<?php echo $status." [$appdate] "; ?></td>
            <td height="23" align="left" width="167">&nbsp;<?php echo $status2." [$appdate2] "; ?></td>
            <td height="23" align="center" width="200">
			
            &nbsp;
			<?php
			   if($closeco=='1'){
			   		echo "<em> Close PO ($closecoby)<br>
					($closedate) </em>";
			   }else{
			   
			   
			  
			
			?>
             <a href="permintaan_manufaktur_detail.php?no_manufaktur=<?php echo $no_po.$cari_3; ?>" target="_blank">Detil</a>
                    <?php
                        if ($app2=="1"){
                            $sql="SELECT no_po FROM po_rm WHERE no_manufaktur='$no_po'";
                            $hsltemp=mysql_query($sql,$db);
                            if(mysql_affected_rows($db)>0){
                                list($no_po_rm)=mysql_fetch_array($hsltemp);
                        ?>
                            |
                            <a href="po_rm_list.php?no_manufaktur=<?php echo $no_po; ?>">PO RM</a>
                        <?php
                            }
                            $sql="SELECT kd_barang,qty FROM barang_kurang WHERE no_manufaktur='$no_po' AND qty>0";
                            mysql_query($sql,$db);
                            if(mysql_affected_rows($db)>0){
                        ?>
                            |
                            <a href="rm_kurang_list.php?no_manufaktur=<?php echo $no_po; ?>">Daftar Kekurangan RM</a>
                        <?php    
                            }
                        }
                    ?>
                    <?php                        
                        if($closeco=="1"){
                    ?>
                        |
                        <b>PO Closed</b> | <b><font color="green"><a href="<?php echo $_SERVER['PHP_SELF']; ?>?action=delete&no_manufaktur=<?php echo $no_po; ?>">Delete</a></font></b>
                    <?php
                        }else{
                    ?>
                        |
                        <a href="#"  id="closing_po" onclick="closingPo('po_<?php echo $id?>','<?php echo $no_po?>','<?php echo $jenis; ?>');">Closing PO</a>
                     <?php
                        }
                  
				   }
				    ?>
        </td>
    </tr>
            
            <?php
        }
    ?>
    <tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
              <td height="23" colspan="5" align="center" bgcolor="#FF9966">SubTotal</td>
              <td height="23" align="right" bgcolor="#FF9966"><?php echo number_format($sub_qty,2,'.',','); ?></td>
              <td height="23" align="right" bgcolor="#FF9966"><?php echo number_format($sub_rp,2,'.',','); ?></td>
              <td height="23" colspan="4" align="right" bgcolor="#FF9966">&nbsp;</td>
            </tr>
            <tr onMouseOver="this.bgColor = '#C0C0C0'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
              <td height="23" colspan="5" align="center" bgcolor="#33FF99">Total</td>
              <td height="23" align="right" bgcolor="#33FF99" id="totalQty">Process....</td>
              <td height="23" align="right" bgcolor="#33FF99" id="totalRp">Process....</td>
              <td height="23" colspan="4" align="right" bgcolor="#33FF99">&nbsp;</td>
            </tr>
    </table>
    
    <table style="margin-left:10px; margin-top:10px;">
<tr>
                <td class="text_standard">
                    Page :  <?php $terusan='';//"&cari=$cari&tgl1=$tgl1&tgl2=$tgl2&cari2=$cari2&tujuan=$tujuan&tuj=$tujuan";?>
                   <?php if($jmlData>100){ ?><span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=0<?php echo $terusan?>';">First</span><?php }?>
                    <?php for($i=0;$i<($jmlData/$jmlHal);$i++){ 
                        if($hal<=0){ ?>
                            <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php if($i>=4) break;
                        }else if(($hal+1)>=($jmlData/$jmlHal)){
                            if($i>=(($jmlData/$jmlHal)-5)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php } 
                        }else{
                            if($i<=($hal+2)and $i>=($hal-2)){ ?>
                                <span class="<?php if($i==$hal) echo "hal_select"; else echo "hal"; ?>" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo $i; ?><?php echo $terusan?>';"><?php echo ($i+1); ?></span>
                            <?php }
                        }
                    } if($jmlData>100){ ?>
                    <span class="hal" onclick="location.href='<?php echo $thisPage; ?>?x_idmenu=243&hal=<?php echo intval(($jmlData/$jmlHal)); ?><?php echo $terusan?>';">Last</span> <?php }?>
                    &nbsp;&nbsp;
					
                    Data <?php echo ($hal*$jmlHal)+1; ?> of <?php echo ($no); ?> from <?php echo $jmlData; ?> Data
    </td>
</tr>
        </table>
        <br /><br />
      
    
<?php include_once "footer.php" ?>
<script>
try{
   <?php echo " getTotal(\"$cari\",\"$isi\",\"$lanjut\",\"$tujuan\",\"$txt_model\");";?>	
}catch(e){
	alert(e.message);
}
</script>
