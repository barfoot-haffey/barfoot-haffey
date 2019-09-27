//detect if this is local or github or ocollector.org
developer_obj = {
  context : "",
}
function detect_context(){
  if(document.URL.indexOf("localhost") !== -1){
    return "localhost";
  } else if(document.URL.indexOf("ocollector.org") !== -1){
    return "ocollector";
  } else { //assume it's github
    return "github";
  }
}
function initiate_collector(){
  developer_obj.context = detect_context();
  switch(developer_obj.context){
    case "ocollector":
      console.dir("hi");
      $.post("code/initiateCollector.php",{
        //nothing to post, just want to run it.
      },function(local_key){
        if(local_key !== "no-key"){
          window.localStorage.setItem("local_key",local_key);
        }
      });      
      break;
    case "localhost":
    case "github":
      var user_email = window.localStorage.getItem("user_email");
      if(user_email == null){
        $("#login_div").show();
      } else {
        $("#logged_in").show();
      }
      break;
  }
}

initiate_collector();

//$.post("init