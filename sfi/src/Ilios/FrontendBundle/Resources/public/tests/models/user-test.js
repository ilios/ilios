moduleForModel('user', 'Unit - User Model', {
    needs: ["model:offering"]
});
test("it exists", function(){
  expect(1);
  ok(this.subject() instanceof window.App.User);
});
