fs = require('fs')
myCode = fs.readFileSync('kitten/TrialFunctions.js','utf-8');
eval(myCode)

//tests on the Trial object??

describe("Downloading of precrypted data", function() {
  it("takes the .responses from a data object and formats them to be csv readable", function() {
    exemplar_object = {
      "responses":
        [["header_1", "header_2"],
         ["value_1","value_2"]]
      },
    }
    expect(precrypted_data(exemplar_object)).toEqual({
      {
        "header_1":["value_1"],
        "header_2":["value_2"]
      }
    });
  });
});
