moduleForModel('offering', 'Unit - Offering Model', {
    needs: ["model:session", "model:user", "model:course"]
});

test("it exists", function(){
  expect(1);
  ok(this.subject() instanceof window.App.Offering);
});
