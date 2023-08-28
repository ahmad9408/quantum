/*
by budi 17072021
base on https://jsfiddle.net/emkey08/tvx5e7q3
*/
(function($) {
  $.fn.inputFilter = function(inputFilter) {
    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
      if (inputFilter(this.value)) {
        this.oldValue = this.value;
        this.oldSelectionStart = this.selectionStart;
        this.oldSelectionEnd = this.selectionEnd;
      } else if (this.hasOwnProperty("oldValue")) {
        this.value = this.oldValue;
        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
      } else {
        this.value = "";
      }
    });
  };
  
  
  
  $.fn.alphaNumeric=function(alphaNumeric){
	  
  }
}(jQuery));

/*
contoh
// Install input filters.
integer
$("#intTextBox").inputFilter(function(value) {
  return /^-?\d*$/.test(value); });
  Integer >= 0
$("#uintTextBox").inputFilter(function(value) {
  return /^\d*$/.test(value); });
  Integer >= 0 and <= 500
$("#intLimitTextBox").inputFilter(function(value) {
  return /^\d*$/.test(value) && (value === "" || parseInt(value) <= 500); });
$("#floatTextBox").inputFilter(function(value) {
  return /^-?\d*[.,]?\d*$/.test(value); });
  Float (use . or , as decimal separator) Currency (at most two decimal places)	
$("#currencyTextBox").inputFilter(function(value) {
  return /^-?\d*[.,]?\d{0,2}$/.test(value); });
  A-Z only
$("#latinTextBox").inputFilter(function(value) {
  return /^[a-z]*$/i.test(value); });
  
$("#hexTextBox").inputFilter(function(value) {
  return /^[0-9a-f]*$/i.test(value); });
  
  $("#hexTextBox").inputFilter(function(value) {
  return /^[0-9a-f]*$/i.test(value); });
$('#txt_kartucustomer').inputFilter(function(value) {return /^[a-zA-Z0-9]*$/i.test(value); }); alphanumeric 
$("#txt_keteranganbiaya").inputFilter(function(value) {return /^[a-zA-Z0-9\-\#]*$/i.test(value); }); alphamuric -,#
*/