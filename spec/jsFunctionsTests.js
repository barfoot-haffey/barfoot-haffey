
// <script src="open-collector\libraries"></script>
fs = require('fs')
myCode = fs.readFileSync('libraries/jsFunctions.js','utf-8') // depends on the file encoding
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

// hi there