describe("ilios", function() {

  it("should create a global ilios object", function() {
    expect(ilios).toBeDefined();
  });

  it("should have a namespace method", function () {
    expect(ilios.namespace).toBeDefined();
    expect(typeof ilios.namespace).toBe("function");
  });

  it("should have a lang namespace", function () {
    expect(ilios.lang).toBeDefined();
    expect(typeof ilios.lang).toBe("object");
  });

  it("should have an alert namespace", function () {
    expect(ilios.alert).toBeDefined();
    expect(typeof ilios.alert).toBe("object");
  });

  it("should have a global namespace", function () {
    expect(ilios.global).toBeDefined();
    expect(typeof ilios.global).toBe("object");
  });
});
