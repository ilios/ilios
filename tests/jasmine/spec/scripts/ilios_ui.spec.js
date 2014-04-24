describe("ilios_ui", function() {
    it("should create a ui namespace on the ilios global object", function () {
        expect(typeof ilios.ui).toBe("object");
    });
    describe("onIliosEvent", function () {
        it("should be a YUI custom event", function () {
            expect(ilios.ui.onIliosEvent instanceof YAHOO.util.CustomEvent).toBeTruthy();
        });
    });
});
