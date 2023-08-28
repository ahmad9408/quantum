var detailsWindow;
function showJobLoading(textid) {
	detailsWindow = window.open("window_job_loading.php?textid="+textid,"window_job_loading","width=800,height=600,scrollbars=yes");
	detailsWindow.focus();   
}
		
function hapus(no_sew){
	var data="no_sew="+no_sew+"&proses=hapus";
		$.post("job_sewing_proses.php",data,function(response){
		//alert(response);
			if(response.trim()=="berhasil"){
				//alert('berhasil');
				$("#submit").click();
			}
		});
	}

  function syncronization(no_sew,no_co){
       var data = "no_sew="+no_sew+"&no_co="+no_co+"&proses=input_qc";
        $.post("job_sewing_proses.php",data,function(response){
        	 
            document.location.reload();

        });
    }