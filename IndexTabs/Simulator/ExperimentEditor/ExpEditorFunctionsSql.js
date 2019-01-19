/*  Collector (Garcia, Kornell, Kerr, Blake & Haffey)
    A program for running experiments on the web
    Copyright 2012-2016 Mikey Garcia & Nate Kornell


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 		
		Kitten release (2019) author: Dr. Anthony Haffey (a.haffey@reading.ac.uk)		
*/
function createExpEditorHoT(sheet,selected_handsonTable, sheet_name) {
	if (selected_handsonTable.toLowerCase() == "conditions") {
		var area = $("#conditionsArea");
		var table_name = 'handsOnTable_Conditions';
	} else if (selected_handsonTable.toLowerCase() == "stimuli") {
		var area = $("#stimsArea");
		var table_name = 'handsOnTable_Stimuli';
	} else if (selected_handsonTable.toLowerCase() == "procedure") { 
		var area = $("#procsArea");
		var table_name = 'handsOnTable_Procedure';
	} else {
		boostrap.alert("There is a bug in your code - not clear which experiment sheet you want to edit/update/create etc.");
	}
	area.html("<span class='sheet_name' style='display: none'>" + sheet_name + "</span>");
	var container = $("<div>").appendTo(area)[0];
	window[table_name] = createHoT(container, JSON.parse(JSON.stringify(sheet)),sheet_name);
}

function get_HoT_data(current_sheet) { // needs to be adjusted for 
    console.dir(current_sheet);
    var data = JSON.parse(JSON.stringify(current_sheet.getData()));
    
    // remove last column and last row
    data.pop();
    
    for (var i=0; i<data.length; ++i) {
        data[i].pop();
        
        for (var j=0; j<data[i].length; ++j) {
            if (data[i][j] === null) {
                data[i][j] = '';
            }
        }
    }
    
    // check for unique headers
    var unique_headers = [];
    
    for (var i=0; i<data[0].length; ++i) {
        while (unique_headers.indexOf(data[0][i]) > -1) {
            data[0][i] += '*';
        }
        
        unique_headers.push(data[0][i]);
    }
    
    return data;
}


function update_spreadsheet_selection() {
	var current_experiment = $("#experiment_name").val();
  
	var exp_data = experiment_files[current_experiment];
  
	var select_html = '<option class="condOption" value="Conditions.csv">Conditions</option>';
  
	select_html += '<optgroup label="Stimuli" class="stimOptions">';
  
	for (var i=0; i<exp_data['Stimuli'].length; ++i) {
		var file = exp_data['Stimuli'][i];
		select_html += '<option value="Stimuli/' + file + '">' + file + '</option>';
	}
  
	select_html += '</optgroup>';
  
	select_html += '<optgroup label="Procedures" class="procOptions">';
  
	for (var i=0; i<exp_data['Procedures'].length; ++i) {
		var file = exp_data['Procedures'][i];
		select_html += '<option value="Procedure/' + file + '">' + file + '</option>';
	}
  
	select_html += '</optgroup>';
  
	//$("#spreadsheet_selection").html(select_html);
}

function new_experiment(experiment){
	if($("#experiment_list").text().indexOf(experiment) !== -1){			
		bootbox.alert("Name already exists. Please try again.");		
	} else {		
	
		//create it first in dropbox, THEN update table with location - duh
		megaUberJson.exp_mgmt.experiment 			  			= experiment;
		megaUberJson.exp_mgmt.experiments[experiment] = experiment_template;
			
		update_handsontables();
		updateUberMegaFile();								
		var this_path = "/Experiments/"+experiment+".json";
		
		dbx_obj.new_upload({path:this_path,contents:JSON.stringify(experiment_template)},function(result){
				dbx.sharingCreateSharedLink({path:this_path})
					.then(function(returned_link){
						$.post(
							"IndexTabs/Simulator/AjaxMySQL.php",
							{
								action: "new",
								experiment: experiment,
								location:returned_link.url								
							},
							function(returned_data){
								console.dir(returned_data);				
								$('#experiment_list').append($('<option>', {         
									text : experiment 
								}));
								$("#experiment_list").val(experiment);
								
							}							
						);
					})
					.catch(function(error){
						report_error(error);
					});
			},function(error){
				report_error(error);				
			});
	}
}



function remove_from_list(experiment){	
	var x = document.getElementById("experiment_list");
	x.remove(experiment);
	megaUberJson.exp_mgmt.experiment =  $("#experiment_list").val();
	if(experiment !== "Select a dropbox experiment"){
		update_handsontables();
	}
}


function commit_save(){
	bootbox.dialog({
		title:"What commit messages do you want?",
		message:"<h5>Experiment Commit Message</h5><textarea id='experiment_commit_textarea' maxlength='500'></textarea>"+
				"<h5>Trialtype Commit Message</h5><textarea id='trialtype_commit_textarea' maxlength='500'></textarea>",
		buttons: {
			commit: {
				label: "Commit",
				className : "btn-success",
				callback: function(){
					experiment_commit = $("#experiment_commit_textarea").val();
					trialtype_commit  = $("#trialtype_commit_textarea").val();
					if(experiment_commit !== ""){
						experiment = megaUberJson.exp_mgmt.experiment;
						this_exp   = megaUberJson.exp_mgmt.experiments[experiment];
						this_exp.commit_message = experiment_commit;
						if(typeof(megaUberJson.exp_mgmt.versions[experiment]) == "undefined"){
							megaUberJson.exp_mgmt.versions[experiment] = [this_exp]; // note that this is incomplete until the user loads previous experiments.
						} else {
							megaUberJson.exp_mgmt.versions[experiment].push(this_exp);
						}
					}
					$("#save_btn").click();
					/*
						
						this is where I'll put code to deal with saving trialtypes
					
					*/
				}
			},
			cancel: {
				label: "Cancel",
				className: "btn-danger",
			}
		}
	});	
}

//solution on  https://stackoverflow.com/questions/46155/how-can-you-validate-an-email-address-in-javascript
function validateEmail(email) { 
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email.toLowerCase());
}


function stim_proc_selection(stim_proc,sheet_selected){
	var experiment = megaUberJson.exp_mgmt.experiment;
	var this_exp   = megaUberJson.exp_mgmt.experiments[experiment];		
	createExpEditorHoT(this_exp.all_stims[sheet_selected],stim_proc,sheet_selected);	//sheet_name
}

function show_run_stop_buttons(){
    if(simulator_on_off == "on"){
        $("#run_stop_buttons").show();
    }
}