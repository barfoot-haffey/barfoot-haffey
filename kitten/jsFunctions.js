function clean_obj_keys(this_obj){
	Object.keys(this_obj).forEach(function(this_key){
		clean_key = this_key.toLowerCase().replace(".csv","");
		this_obj[clean_key] = this_obj[this_key];
    if(this_key !== clean_key){
      delete(this_obj[this_key]);
    }
	});
	return this_obj;
}

function download_collector_file(filename,content,type){
	var blob = new Blob([content], {type: 'text/' + type});
	if(window.navigator.msSaveOrOpenBlob) {
		window.navigator.msSaveBlob(blob, filename);
	}	else{
		var elem = window.document.createElement('a');
		elem.href = window.URL.createObjectURL(blob);
		elem.download = filename;
		document.body.appendChild(elem);
		elem.click();
		document.body.removeChild(elem);
	}
}

function report_error(error,collector_error_message){
	console.dir(error);
  if(typeof(collector_error_message) !== "undefined"){
    bootbox.alert(collector_error_message);
  }
	bootbox.alert("<b>error:</b> " + error.error.error_summary + "<br> Perhaps wait a bit and save (again)?");
};