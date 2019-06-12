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
function report_error(error){
	console.dir(error);
	bootbox.alert("<b>error:</b> " + error.error.error_summary + "<br> Perhaps wait a bit and save (again)?");
};