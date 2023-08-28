<?php $content_title="MASTER LINE PABRIK"; include_once "header.php" ?>
<?php include_once "clsaddrow.php";?>

<style>
.datagrid table { border-collapse: collapse; text-align: left; width: 100%; } .datagrid {font: normal 12px/150% Arial, Helvetica, sans-serif; background: #fff; overflow: hidden; border: 1px solid #36752D; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; }.datagrid table td, 
.datagrid table th { padding: 3px 10px;  }
.datagrid table thead th {background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420) );background:-moz-linear-gradient( center top, #36752D 5%, #275420 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');background-color:#36752D; color:#FFFFFF; font-size: 12px; font-weight: bold; border-left: 1px solid #36752D; } .datagrid table thead th:first-child { border: none; }.datagrid table tbody td { color: #275420; border-left: 1px solid #C6FFC2;font-size: 10px;font-weight: normal; }.datagrid table tbody .alt td { background: #DFFFDE; color: #275420; }.datagrid table tbody td:first-child { border-left: none; }.datagrid table tbody tr:last-child td { border-bottom: none; }.datagrid table tfoot td div { border-top: 1px solid #36752D;background: #DFFFDE;} .datagrid table tfoot td { padding: 0; font-size: 12px } .datagrid table tfoot td div{ padding: 2px; }.datagrid table tfoot td ul { margin: 0; padding:0; list-style: none; text-align: right; }.datagrid table tfoot  li { display: inline; }.datagrid table tfoot li a { text-decoration: none; display: inline-block;  padding: 2px 8px; margin: 1px;color: #FFFFFF;border: 1px solid #36752D;-webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px; background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #36752D), color-stop(1, #275420) );background:-moz-linear-gradient( center top, #36752D 5%, #275420 100% );filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#36752D', endColorstr='#275420');background-color:#36752D; }.datagrid table tfoot ul.active, .datagrid table tfoot ul a:hover { text-decoration: none;border-color: #275420; color: #FFFFFF; background: none; background-color:#36752D;}div.dhtmlx_window_active, div.dhx_modal_cover_dv { position: fixed !important; }

.kelas_departemen {
width: 50px;
border: thin solid #06F;	
position:static; 
position:inherit !important;
text-align:center;
cursor:pointer;

}

.kelas_departemen:hover { 
background-color:#E2FBFC;

}

fieldset { border:1px solid green }

legend {
padding: 0.2em 0.5em;
border:1px solid green;
color:green;
font-size:90%; 
}
  
</style>

<link rel="stylesheet" href="themes/base/jquery.ui.all.css">

<form method="post" class="datagrid" cellspacing="0" cellpadding="0">
    <table class="datagrid" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table valign="top" class="datagrid" cellspacing="0" cellpadding="0">
                                    <tr><br></tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>Master Line Pabrik Manufaktur</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" align="center"><b>SUHO GARMINDO</b></td>
                                    </tr>
                                </table>
                    </td>
                </tr>
    </table>
    <table class="datagrid" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>No</th>
                <th>No Line</th>
                <th>Pabrik</th>
                <th>Line Pabrik</th>
                <th>Status</th>
            </tr>
        </thead>

        <?php
        $sql="SELECT
            id AS no_line,
            id_pabrik AS pabrik,
            keterangan AS line_pabrik,
            status AS status
            FROM
            job_sewing_line AS jsl
            WHERE status='1'"
            ;
        $hsltemp=mysql_query($sql,$db);
        while(list($id_line,$pabrik,$keterangan,$status)=mysql_fetch_array($hsltemp)){
        $no++;
        if($no%2==1){
            $kelas1="alt";
        }else{
            $kelas1="";
            }

        if($status==1){
            $status1="Aktif";
        }else{
            $status1="Tidak Aktif";
            }

        if($pabrik=='P0172'){
            $pabrik1="Pabrik Cileunyi 1";
        }elseif($pabrik=='P0174'){
            $pabrik1="Pabrik Cileunyi 2";
        }elseif($pabrik=='P0185'){
            $pabrik1="Pabrik Cileunyi 3";
        }elseif($pabrik=='P0260'){
            $pabrik1="Pabrik Cileunyi 4";
        }else{
            
        }
            

            ?>
             <tr class="<?php echo $kelas1?>">
                <td><?php  echo $no;?></td>                
                <td><?php  echo $id_line;?></td>                
                <td><?php  echo $pabrik1;?></td>                
                <td><?php  echo $keterangan;?></td>
                <td><?php  echo $status1;?></td>                                   
            </tr>  
            <?php
        }
        ?>
          <thead>
            <tr>
                <th>No</th>
                <th>No Line</th>
                <th>Pabrik</th>
                <th>Line Pabrik</th>
                <th>Status</th>
            </tr>
        </thead>
    </table>
</form>

<?php include_once "footer.php" ?>