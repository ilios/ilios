moduleForModel('course', 'Unit - Course Model', {
    needs: ["model:session"]
});

test("it exists", function(){
  expect(1);
  ok(this.subject() instanceof window.App.Course);
});
