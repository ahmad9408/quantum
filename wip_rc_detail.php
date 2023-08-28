<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="WIP Real Cutting Detail"; ?>
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
  </tr>
  <?php $sql="SELECT j.no_co,m.kode_model,j.tanggal,jc.kd_produk,s.size,su.nama,w.warna,SUM(jc.qty) FROM job_cutting_detail AS jc 
LEFT JOIN mst_model_fix AS m ON 
(m.kode_model=SUBSTRING(jc.kd_produk,1,7))
LEFT JOIN mst_size AS s ON 
(s.kode=SUBSTRING(jc.kd_produk,8,2))
LEFT JOIN supplier AS su ON 
(su.id=SUBSTRING(jc.kd_produk,10,3))
LEFT JOIN mst_warna AS w ON 
(w.kode=SUBSTRING(jc.kd_produk,13,3))
INNER JOIN job_cutting AS j ON 
(jc.no_co=j.no_co)
WHERE SUBSTRING(jc.kd_produk,1,7)='$kode_model' AND jc.no_co IN ($kode_in) group by j.no_co";

$query=mysql_query($sql)or die($sql);
while(list($no_co,$model,$tanggal,$id_barang,$ukuran,$supplier,$warna,$qty)=mysql_fetch_array($query)){
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
	<td align="center"><?php echo $qty?></td>
  </tr>
  <?php
	
	$tqty+=$qty;
}
?>
  <tr bgcolor="#006600">
    <td colspan="7" height="30"><em><b><font color="#FFFFFF">Total</font> </b></em></td>
    <td align="center"><em><b><font color="#FFFFFF"><?php echo $tqty;?></font> </b></em></td>
  </tr>
</table>
<?php include_once "footer.php"; ?>
