describe("ilios.cim.model", function () {

    it("should create a cim.model namespace on the ilios global object", function () {
        expect(typeof ilios.cim.model).toBe("object");
    });

    describe("ilios.cim.model.BaseModel", function () {

        it("should be an constructor function", function () {
            var obj = new ilios.cim.model.BaseModel();

            expect(typeof ilios.cim.model.BaseModel).toBe("function");
            expect(typeof obj).toBe("object");
            expect(obj instanceof ilios.cim.model.BaseModel).toEqual(true);
        });

        describe("getName()", function () {

           it("should be a method", function () {
               expect(typeof ilios.cim.model.BaseModel.prototype.getName).toBe("function");
           });

           it('should return "baseModel"', function () {
               var obj = new ilios.cim.model.BaseModel();
               expect(obj.getName()).toEqual("baseModel");
           });
        });

        describe("getClientId()", function () {
            it("should be a method", function () {
                expect(typeof ilios.cim.model.BaseModel.prototype.getClientId).toBe("function");
            });

            it("should return a value starting with the model name, followed by an underscore", function () {
                var obj = new ilios.cim.model.BaseModel();
                var name = obj.getName();
                expect(obj.getClientId().indexOf(name + "_")).toEqual(0);
            });

            it("should return an unique value per instance, containing an incremental suffix", function () {
                var obj1 = new ilios.cim.model.BaseModel();
                var obj2 = new ilios.cim.model.BaseModel();
                var obj3 = new ilios.cim.model.BaseModel();
                expect(obj1.getClientId().localeCompare(obj2.getClientId())).toBeLessThan(0);
                expect(obj2.getClientId().localeCompare(obj3.getClientId())).toBeLessThan(0);
            });
        });

        describe("getId()", function () {

            it("should be a method", function () {
                expect(typeof ilios.cim.model.BaseModel.prototype.getId).toBe("function");
            });

            it("should return the model's 'id' attribute, if set", function () {
                var obj = new ilios.cim.model.BaseModel({'dbId': 10});
                expect(obj.getId()).toEqual(10);
            });
        });
    });
});
