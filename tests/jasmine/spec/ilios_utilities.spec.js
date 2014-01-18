describe("ilios_utilities", function() {
  it("should create a utilities namespace on the ilios global object", function () {
    expect(typeof ilios.utilities).toBe("object");
  });
});
