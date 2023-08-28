
 $(document).ready(function(){
 		
		activateAutoCompleteAll();



});	

function activateAutoCompleteAll(){
	
	activateAutoComplete($('#pabrik'));		
	activateAutoComplete($('#jenis_pabrik'));	
	
}

// function deactivateAutoComplete(component){
//    component.chosen("destroy");	
// }

function activateAutoComplete(component){
	component.chosen({});		
}	

function batal_so(kode_so){
	// $("#sendOTP").attr("disabled",true);	
    if (confirm("Anda yakin akan membatalkan SO ini ?")) {
    	$.ajax({
				type: 'POST',
				url: 'rekap_so_finishing_data.php',
				data: {j:'batal_so',k:kode_so},
				dataType: 'text',
				success: function(data){
					// alert(data);
					if(data.trim()=='success'){
						location.reload();
					} else {
						alert(data);
					}
				  
				}
			  });
    } 
}






                            

						

						