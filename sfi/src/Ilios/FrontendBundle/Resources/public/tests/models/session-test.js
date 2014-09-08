moduleForModel('session', 'Unit - Session Model', {
    needs: ["model:course","model:offering"]
});

test("it exists", function(){
  expect(1);
  ok(this.subject() instanceof window.App.Session);
});
