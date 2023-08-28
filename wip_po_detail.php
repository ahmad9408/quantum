<?php ob_start("ob_gzhandler"); ?>
<?php $content_title="WIP PO Detail"; ?>
<?php  include('header.php'); 
$dari=$_POST['dari'];
$sampai=$_POST['sampai'];
$kode_temp=$_POST['temp_po'];
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
<table border="0" width="500" style="font-size: 8pt">
 <tr>
      <td background="images/footer.gif" align="center" width="20" height="30" ><strong>NO</strong></td> 
	  <td background="images/footer.gif" align="center" width="200" ><strong>No PO</strong></td> 
	  <td background="images/footer.gif" align="center" width="100" ><strong>Tanggal Po</strong></td>
	  <td background="images/footer.gif" align="center" width="70" ><strong>Qty PO</strong></td> 
 </tr> 
 <?php $sql="SELECT pm.no_manufaktur,sum(pmd.qty) AS qty,pm.tanggal FROM po_manufaktur_detail AS pmd 
INNER JOIN po_manufaktur AS pm 
ON (pm.no_manufaktur=pmd.no_manufaktur) WHERE pm.no_manufaktur IN ($kode_in) AND SUBSTRING(kd_produk,1,7)='$kode_model'
GROUP BY pm.no_manufaktur ";
$query=mysql_query($sql)or die($sql);
while(list($no_po,$qty,$tanggal)=mysql_fetch_array($query)){
$no++;
    $bgclr1 = "#FFFFCC"; $bgclr2 = "#E0FF9F";
    $bgcolor = ( $no % 2 ) ? $bgclr1 : $bgclr2;
	?> <tr id="child-content" onMouseOver="this.bgColor = '#CCCC00'" onMouseOut ="this.bgColor = '<?php echo $bgcolor;?>'" bgcolor="<?php echo $bgcolor; ?>">
	<td><?php echo $no?></td>
	<td><?php echo $no_po?></td>
	<td><?php echo $tanggal?></td>
	<td align="center"><?php echo $qty?></td>
	</tr><?php
	
	$tqty+=$qty;
}
?>
<tr bgcolor="#006600">
	<td colspan="3"><em><b><font color="#FFFFFF">Total</font> </b></em></td>
	<td align="center"><em><b><font color="#FFFFFF"><?php echo $tqty;?></font> </b></em></td>
</tr>
</table>
<?php include_once "footer.php"; ?>