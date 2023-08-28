<?php include("connect.php"); 
$cari1=$_POST['cari1']; 
$cari2=$_POST['cari2']; 
$cari3=$_POST['cari3'];
?>
<script src="jquery.js"></script>
<style>

 .tabel{
    border-collapse: collapse;
        border-spacing: 0;
	width:100%; 
	font-size:14;
}
.tabel tr:last-child td:last-child {
	-moz-border-radius-bottomright:0px;
	-webkit-border-bottom-right-radius:0px;
	border-bottom-right-radius:0px;
}
.tabel tr:first-child td:first-child {
	-moz-border-radius-topleft:0px;
	-webkit-border-top-left-radius:0px;
	border-top-left-radius:0px;
}
.tabel tr:first-child td:last-child {
	-moz-border-radius-topright:0px;
	-webkit-border-top-right-radius:0px;
	border-top-right-radius:0px;
}.tabel tr:last-child td:first-child{
	-moz-border-radius-bottomleft:0px;
	-webkit-border-bottom-left-radius:0px;
	border-bottom-left-radius:0px;
}
 
.tabel tr:nth-child(odd){ background-color:#d4ffaa; }
.tabel tr:nth-child(even)    { background-color:#ffffff; }
.tabel td{
	vertical-align:middle;
	
	
	border:1px solid #3f7f00;
	border-width:0px 1px 1px 0px; 
	padding:7px;
	font-size:14px;
	font-family:Arial;
	font-weight:normal;
	color:#000000;
}.tabel tr:last-child td{
	border-width:0px 1px 0px 0px;
}.tabel tr td:last-child{
	border-width:0px 0px 1px 0px;
}.tabel tr:last-child td:last-child{
	border-width:0px 0px 0px 0px;
}
.tabel tr:first-child td{
		background:-o-linear-gradient(bottom, #5fbf00 5%, #3f7f00 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #5fbf00), color-stop(1, #3f7f00) );
	background:-moz-linear-gradient( center top, #5fbf00 5%, #3f7f00 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#5fbf00", endColorstr="#3f7f00");	background: -o-linear-gradient(top,#5fbf00,3f7f00);

	background-color:#5fbf00;
	border:0px solid #3f7f00;
	text-align:center;
	border-width:0px 0px 1px 1px;
	font-size:14px;
	font-family:Arial;
	font-weight:bold;
	color:#ffffff;
}
.tabel tr:first-child:hover td{
	background:-o-linear-gradient(bottom, #5fbf00 5%, #3f7f00 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #5fbf00), color-stop(1, #3f7f00) );
	background:-moz-linear-gradient( center top, #5fbf00 5%, #3f7f00 100% );
	filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#5fbf00", endColorstr="#3f7f00");	background: -o-linear-gradient(top,#5fbf00,3f7f00);

	background-color:#5fbf00;
}
.tabel tr:first-child td:first-child{
	border-width:0px 0px 1px 0px;
}
.tabel tr:first-child td:last-child{
	border-width:0px 0px 1px 1px;
}

.tengah td{
	color:#FFFFFF;
	font-weight:bold;
	font-style:italic;
}

</style><form method="post" id="f1" name="f1" action="job_gelaran_detail_4_window.php">
<fieldset>

<legend><strong>Pencarian</strong> </legend>
<table cellspacing="0" cellpadding="0" width="100%">
  <td height="20" width="64">Cari1</td>
      <td width="178">:
      <input type="text" id="cari1" name="cari1" value="<?php echo $cari1?>" /></td>
    <td width="30">Cari 2</td>
    <td width="178">:
      <input type="text" id="cari2" name="cari2" value="<?php echo $cari2?>" /></td>
    <td width="30">Cari 3</td>
    <td width="172">:
      <input type="text" id="cari3" name="cari3" value="<?php echo $cari3?>" />
	  <input type="hidden" id="jenis" name="jenis" value="<?php echo $_REQUEST['jenis'];?>" />
	  <input type="hidden" id="kode_warna" name="kode_warna" value="<?php echo $_REQUEST['kode_warna'];?>" /></td>
    <td width="39"><input type="submit" id="submit" name="submit" value="Cari" /></td>
</table>

 
</fieldset></form>
<table cellspacing="0" cellpadding="0" width="100%" class="tabel">
  <tr>
  	<th>Cek</th>
    <th height="30" width="183">Id Barang</th>
    <th width="396">Jenis Barang</th>
    <th width="404">Supplier</th>
    <th width="332">Warna</th>
  </tr>
 <?php
 $sql="SELECT
    `b`.`id`
    , `j`.`nama`
    , `s`.`nama`
    , `w`.`warna`
FROM
   `barangdetail_rm` AS `b`
    INNER JOIN  `mst_warna_rm` AS `w` 
        ON (SUBSTRING(`b`.`id`,11,3) = `w`.`kode`)
    INNER JOIN  `jenisbarang_rm` AS `j`
        ON (SUBSTRING(`b`.`id`,3,3) = `j`.`kode`)
    INNER JOIN  `supplier_rm` AS `s`
        ON (SUBSTRING(`b`.`id`,6,2) = `s`.`id`) WHERE   
		(`b`.`id` like '%$cari1%' or  `j`.`nama` like '%$cari1%' or  `s`.`nama` like '%$cari1%' or  `w`.`warna` like '%$cari1%') and 
		(`b`.`id` like '%$cari2%' or  `j`.`nama` like '%$cari2%' or  `s`.`nama` like '%$cari2%' or  `w`.`warna` like '%$cari2%') and 
		(`b`.`id` like '%$cari3%' or  `j`.`nama` like '%$cari3%' or  `s`.`nama` like '%$cari3%' or  `w`.`warna` like '%$cari3%') and 
		SUBSTRING(`b`.`id`,8,3)='001' ORDER BY s.nama, j.nama,w.warna limit 1000";
 if($_SESSION['username']=="rian-it"){
	 echo $sql;
 }
		$query=mysql_query($sql)or die($sql);
		while(list($id,$jenis,$supplier,$warna)=mysql_fetch_array($query)){
		$no++;
		?>
			<tr>
				<td align="center"><input type="checkbox" id="cek<?php echo $no?>" name="cek<?php echo $no?>" onclick="pilih('<?php echo $no?>')" /></td>
				<td id="id_barang<?php echo $no?>"><?php echo $id?></td>
				<td id="jenis<?php echo $no?>"><?php echo $jenis?></td>
				<td id="supplier<?php echo $no?>"><?php echo $supplier?></td>
				<td><?php echo $warna?></td>
			</tr>
		<?php
		}
 
 ?>
</table>
<script>
function pilih(no){
 
	var jenis1=$("#jenis"+no).text().trim(); 
	var id_barang=$("#id_barang"+no).text().trim();
	var supplier=$("#supplier"+no).text().trim();
	var jen=$("#jenis").val();
	var warna=$("#kode_warna").val();
	 window.opener.kembalikan(id_barang,jenis1,supplier,jen,warna);
}
</script>
