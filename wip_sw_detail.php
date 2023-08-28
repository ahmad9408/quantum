<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="WIP Sewing Detail"; ?>
<?php  include('header.php'); 
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$kode_temp=$_POST['temp_co'];
$kode_model=$_POST['kode_model'];


if($kode_temp!=""){
	$kode_temp=explode(",",$kode_temp);
	foreach($kode_temp as $kode_id){
		$kode_in.="'".$kode_id."',";
	}
	$kode_in=substr($kode_in,0,strlen($kode_in)-1);
}else{
	$kode_in="''";
}


$sql="SELECT nama_model FROM mst_model_fix WHERE kode_model='$kode_model'";
$query=mysql_query($sql)or die($sql);
list($nama_model)=mysql_fetch_array($query);
?><table border="0">
<tr>
	<td>Kode Model</td>
	<td width="2"></td>
	<td><?php echo $kode_model?></td>
</tr>
<tr>
	<td>Nama Model</td>
	<td>:</td>
	<td><?php echo $nama_model?></td>
</tr>
</table>
<table border="0" width="1000" style="font-size: 8pt">
  <tr>
    <td background="images/footer.gif" align="center" width="20" height="30" ><strong>NO</strong></td>
    <td background="images/footer.gif" align="center" width="400" ><strong>No Co</strong></td>
    <td background="images/footer.gif" align="center" width="300" ><strong>Tanggal </strong></td>
    <td background="images/footer.gif" align="center" width="120" ><strong>Barcode</strong></td>
    <td background="images/footer.gif" align="center" width="70" ><strong>Ukuran</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Supplier</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Warna</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Qty</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Reject</strong></td>
  </tr>
  <?php $sql="SELECT j.no_sew,m.kode_model,j.tanggal,js.kd_produk,s.size,su.nama,w.warna,SUM(js.qty-js.pending) ,SUM(js.reject)
FROM job_sewing_detail AS js 
LEFT JOIN mst_model_fix AS m ON (m.kode_model=SUBSTRING(js.kd_produk,1,7)) 
LEFT JOIN mst_size AS s ON (s.kode=SUBSTRING(js.kd_produk,8,2)) 
LEFT JOIN supplier AS su ON (su.id=SUBSTRING(js.kd_produk,10,3)) 
LEFT JOIN mst_warna AS w ON (w.kode=SUBSTRING(js.kd_produk,13,3)) 
INNER JOIN job_sewing AS j ON (js.no_sew=j.no_sew) 
WHERE SUBSTRING(js.kd_produk,1,7)='$kode_model' AND j.no_co IN ($kode_in) and approve2='1' group by j.no_co";



$query=mysql_query($sql)or die($sql);
while(list($no_co,$model,$tanggal,$id_barang,$ukuran,$supplier,$warna,$qty,$reject)=mysql_fetch_array($query)){
$no++;
    $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
    $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
	?>
  <tr id="child-content" onmouseover="this.bgColor = '#CCCC00'" onmouseout ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
    <td><?php echo $no?></td>
    <td><?php echo $no_co?></td>
    <td><?php echo $tanggal?></td>
    <td ><?php echo $id_barang?></td>
	<td><?php echo $ukuran?></td>
	<td><?php echo $supplier?></td>
	<td ><?php echo $warna?></td>
	<td align="center"><?php echo number_format($qty,"0",".",",");?></td>
	<td align="center"><?php echo number_format($reject,"0",".",",");?></td>
  </tr>
  <?php
	
	$tqty+=$qty;
	$treject+=$reject;
}
?>
  <tr bgcolor="#006600">
    <td colspan="7" height="30"><em><b><font color="#FFFFFF">Total</font> </b></em></td>
    <td align="center"><em><b><font color="#FFFFFF"><?php echo number_format($tqty,"0",".",",");?></font> </b></em></td>
	 <td align="center"><em><b><font color="#FFFFFF"><?php echo number_format($treject,"0",".",",");?></font> </b></em></td>
  </tr>
</table>
<?php include_once "footer.php"; ?>
