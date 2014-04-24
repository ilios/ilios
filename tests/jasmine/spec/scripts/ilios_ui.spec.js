describe("ilios_ui", function() {
    it("should create a ui namespace on the ilios global object", function () {
        expect(typeof ilios.utilities).toBe("object");
    });
});
