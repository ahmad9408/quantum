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
    <td background="images/footer.gif" align="center" width="400" ><strong>No Qc</strong></td>
    <td background="images/footer.gif" align="center" width="300" ><strong>Tanggal </strong></td>
    <td background="images/footer.gif" align="center" width="120" ><strong>Barcode</strong></td>
    <td background="images/footer.gif" align="center" width="70" ><strong>Ukuran</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Supplier</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Warna</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Grade A</strong></td>
    <td background="images/footer.gif" align="center" width="100" ><strong>Grade B</strong></td>
  </tr>
  </tr>
  <?php $sql="
SELECT j.no_qc,m.kode_model,j.tanggal,jq.kd_produk,s.size,su.nama,w.warna,SUM(jq.grade_a),SUM(jq.grade_b) FROM job_qc_detail AS jq
LEFT JOIN mst_model_fix AS m ON 
(m.kode_model=SUBSTRING(jq.kd_produk,1,7))
LEFT JOIN mst_size AS s ON 
(s.kode=SUBSTRING(jq.kd_produk,8,2))
LEFT JOIN supplier AS su ON 
(su.id=SUBSTRING(jq.kd_produk,10,3))
LEFT JOIN mst_warna AS w ON 
(w.kode=SUBSTRING(jq.kd_produk,13,3))
INNER JOIN job_qc AS j ON 
(jq.no_qc=j.no_qc)
WHERE SUBSTRING(jq.kd_produk,1,7)='$kode_model' AND j.no_co IN ($kode_in) GROUP BY j.no_co
";

$query=mysql_query($sql)or die($sql);
while(list($no_co,$model,$tanggal,$id_barang,$ukuran,$supplier,$warna,$qty,$grade_b)=mysql_fetch_array($query)){
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
	<td align="center"><?php echo $grade_b?></td>
  </tr>
  <?php
	
	$tqty+=$qty;
	$tgrade_b+=$grade_b;
}
?>
  <tr bgcolor="#006600">
    <td colspan="7" height="30"><em><b><font color="#FFFFFF">Total</font> </b></em></td>
    <td align="center"><em><b><font color="#FFFFFF"><?php echo $tqty;?></font> </b></em></td>
	 <td align="center"><em><b><font color="#FFFFFF"><?php echo $tgrade_b;?></font> </b></em></td>
  </tr>
</table>
<?php include_once "footer.php"; ?>
