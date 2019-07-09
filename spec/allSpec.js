fs = require('fs')
myCode = fs.readFileSync('libraries/jquery-3.3.1.min.js','utf-8') // depends on the file encoding
eval(myCode)


// basic eg.
describe("A suite", function() {
  it("contains spec with an expectation", function() {
    expect(true).toBe(true);
  });
});


///////////////////////////
// kitten/jsFunctions.js //
///////////////////////////


fs = require('fs')
myCode = fs.readFileSync('kitten/jsFunctions.js','utf-8') // depends on the file encoding
eval(myCode)

describe("Cleaning object keys", function() {
  it("should remove .csv from an object key", function() {
    exemplar_object = {
      "key.csv":"value",
    }
    expect(clean_obj_keys(exemplar_object)).toEqual({
      "key":"value"
    });
  });
});




/////////////////////////////////////////////////////////
// kitten/IndexTabs/Simulator/HandsontableFunctions.js //
/////////////////////////////////////////////////////////
fs = require('fs')
myCode = fs.readFileSync('kitten/IndexTabs/Simulator/HandsontableFunctions.js','utf-8');
eval(myCode)


//isTrialTypeHeader
///////////////////
tt_header_true_array = ["trial type",
                       "post 1 trial type",
                       "Post 2 trial type"];
tt_header_false_array = ["trial_type",
                        "post_1 trial type",
                        "Post2 trial type"];
    
tt_header_true_array.forEach(function(exemplar_true_header){
  describe("evaluating isTrialTypeHeader", function() {
  it("should confirm whether '" + exemplar_true_header + "' is a valid header or not", function() {
    expect(isTrialTypeHeader(exemplar_true_header)).toEqual(true);  
    });
  });
});

tt_header_false_array.forEach(function(exemplar_false_header){
  describe("evaluating isTrialTypeHeader", function() {
  it("should confirm whether '" + exemplar_false_header + "' is NOT a valid header or not", function() {
    expect(isTrialTypeHeader(exemplar_false_header)).toEqual(false);  
    });
  });
});


//isNumericHeader
/////////////////
/*

to be continued

num_header_true_array = ["item",
                         "max time",
                         "min time"];
num_header_false_array = ["trial_type",
                          "post_1 trial type",
                          "Post2 trial type"];
    
num_header_true_array.forEach(function(exemplar_true_header){
  describe("evaluating isTrialTypeHeader", function() {
  it("should confirm whether '" + exemplar_true_header + "' is a valid header or not", function() {
    expect(isNumericHeader(exemplar_true_header)).toEqual(true);  
    });
  });
});

num_header_false_array.forEach(function(exemplar_false_header){
  describe("evaluating isTrialTypeHeader", function() {
  it("should confirm whether '" + exemplar_false_header + "' is NOT a valid header or not", function() {
    expect(isNumericHeader(exemplar_false_header)).toEqual(false);  
    });
  });
});
*/

//////////////////////////////
// kitten/TrialFunctions.js //
//////////////////////////////
/* on hiatus as there is jquery in here and reference to an object

fs = require('fs')
myCode = fs.readFileSync('kitten/TrialFunctions.js','utf-8');
eval(myCode)
 
describe("Downloading of precrypted data", function() {
  it("takes the .responses from a data object and formats them to be csv readable", function() {
    exemplar_object = {
      "responses":
        [["header_1", "header_2"],
         ["value_1","value_2"]]
    }
    expect(precrypted_data(exemplar_object)).toEqual({
      
      "header_1":["value_1"],
      "header_2":["value_2"]
      
    });
  });
});
*/