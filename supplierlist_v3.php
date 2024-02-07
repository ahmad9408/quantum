<?php $content_title="Supplier "; ?>
<?php 
   if(true){
	 include('header.php');
   }
  
$sql="SELECT change_supplier FROM user_account_privileges_parameter WHERE username='$username'";
$res=mysql_query($sql);
list($izin_edit)=mysql_fetch_array($res);


    if($_REQUEST['act']=='search'){
    	$id_supplier=$_POST['txt_id'];
    	$nama_supplier=$_POST['txt_nama'];
		$kd_mode=$_POST['txt_mode'];
		$konsinyasi=$_POST['konsinyasi'];
		$filter="?i=$id_supplier&n=$nama_supplier&m=$kd_mode&k=$konsinyasi";
	}else{
		$filter="?i=-";// inisialisasi nilai id supplier yang akan dicari
	}
    $arrayMode=array();
	
 
	try {
	  $dbh = new PDO('mysql:host='.$host.';dbname='.$dbname, $user, $pass,array(PDO::ATTR_PERSISTENT => true));
	  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);// berguna untuk develop
	   
	} catch (PDOException $e) {
	   print "Error!: " . $e->getMessage() . "<br/>";
	   //die();
	}
?> 
<link rel="stylesheet" type="text/css" href="easyUI/themes/default/easyui.css">
<link rel="stylesheet" type="text/css" href="easyUI/themes/icon.css">
<link rel="stylesheet" type="text/css" href="easyUI/demo.css">
<script src="jquery-latest.js"></script>
<script type="text/javascript" src="easyUI/jquery.easyui.min.js"></script>
<script type="text/javascript" src="easyUI/jquery.edatagrid.js"></script>
<script type="text/javascript">
    $(function(){
        $('#dg').edatagrid({
            url: 'supplier_get.php<?php echo $filter?>',
			<?php if($izin_edit==1){?> 
            saveUrl: 'supplier_save.php',
            updateUrl: 'supplier_update.php',
           /* destroyUrl: 'supplier_destroy.php',*/
			<?php } ?>
			
        });
    });
	
</script>
<form id="form1" name="form1" method="post" action="supplierlist_v3.php?act=search">
<table width="247" border="1">
  <tr>
    <td width="90">ID</td>
    <td width="55" style="color:#F00">contains</td>
    <td width="80"><label for="txt_id"></label>
      <input name="txt_id" type="text" id="txt_id" value="<?=$id_supplier?>" /></td>
  </tr>
  <tr>
    <td>Mode </td>
    <td>=</td>
    <td>
      <label for="txtmode"></label>
      <select name="txt_mode" id="txt_mode">
       <option value=''>Please Select</option>
       <?php
	     $sql='SELECT id,`mode` FROM  mode_supplier order by id';
		 $modes='[';
		 try{
			 $sth=$dbh->query($sql) or die($sql);// die wajib ada untuk ke error hadle
			 
			 while (list($id,$nama)= $sth->fetch(PDO::FETCH_NUM)) {
			   if($id==$kd_mode){
				    echo "<option value='$id' selected='true'>$nama</option>";
			   }else{
				    echo "<option value='$id'>$nama</option>";
			   }
			   $modes.="{id:'$id',value:'$nama'},";
			   $arrayMode[$id]=$nama;
			 }
			
			 $modes=substr($modes,0,strlen($modes)-1).']';// menghilangkan tanda koma dibagian terakhir 
			 
		     echo "<script>var modes =$modes;</script>";			
		 }catch(PDOException $e2){
			  echo  'Errr '.$e2->getMessage();
		 }
		 
	  
	  ?>
      
      </select>
   </td>
  </tr>
  <tr>
    <td>Nama</td>
    <td style="color:#F00">constains</td>
    <td><label for="txt_nama"></label>
      <input name="txt_nama" type="text" id="txt_nama" value="<?=$nama_supplier?>" /></td>
  </tr>
  <tr>
    <td>konsinyasi</td>
    <td style="color:#F00">=</td>
    <td><input name="konsinyasi" type="checkbox" id="konsinyasi"  value="1" <?php if($konsinyasi==1){echo "checked='checked'";} ?>/></td>
  </tr>
  <tr>
    <td colspan="3"><input type="submit" name="Reset" id="Reset" value="Reset" />
      <input type="submit" name="Cari" id="Cari" value="Cari" /></td>
    </tr>
</table>
</form>
<div class="demo-info" style="margin-bottom:10px">
		<div class="demo-tip icon-tip">&nbsp;</div>
		<div>Double click the row to begin editing.</div>
	</div>
	
	<table id="dg" title="My Users" style="width:700px;height:400px"
			toolbar="#toolbar" pagination="true"
			rownumbers="true" fitColumns="true" singleSelect="true"  idField="kode">
		<thead>
			<tr>
                <th field="kode" width="50" editor="{type:'validatebox',options:{required:true}}">Kode</th>
				<th field="nama" width="50" editor="{type:'validatebox',options:{required:true}}">nama</th>
				<th field="alamat" width="50" editor="text">alamat</th>
				<th field="mode" width="50" editor="{type:'combobox',options:{valueField:'id',textField:'value',data:modes,required:true}}">mode</th>
                <th field="konsinyasi" width="50" editor="{type:'checkbox',options:{on:'1',off:'0'}}">Konsinyasi</th>
				
			</tr>
		</thead>
	</table>
	<div id="toolbar" >
      <div <?php if($izin_edit!=1){echo 'style="display:none;"';} ?>>
		<a href="#" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="javascript:$('#dg').edatagrid('addRow')">New</a>
		<?php /*<a href="#" class="easyui-linkbutton" iconCls="icon-remove" plain="true" onclick="javascript:$('#dg').edatagrid('destroyRow')">Destroy</a> */ ?>
		<a href="#" class="easyui-linkbutton" iconCls="icon-save" plain="true" onclick="javascript:$('#dg').edatagrid('saveRow')">Save</a>
		<a href="#" class="easyui-linkbutton" iconCls="icon-undo" plain="true" onclick="javascript:$('#dg').edatagrid('cancelRow')">Cancel</a>
        </div>
	</div>

<?php $dbh = null;
include_once "footer.php"; 
?>