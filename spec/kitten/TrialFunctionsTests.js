fs = require('fs')
myCode = fs.readFileSync('kitten/TrialFunctions.js','utf-8');
eval(myCode)

//tests on the Trial object??

describe("Downloading of precrypted data", function() {
  it("takes the .responses from a data object and formats them to be csv readable", function() {
    exemplar_object = {
      "responses":{
        "row_1": ["header_1", "header_2"]
      },
    }
    expect(clean_obj_keys(exemplar_object)).toEqual({
      "key":"value"
    });
  });
});
