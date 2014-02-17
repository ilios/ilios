describe("ilios.preferences", function () {
        describe("preferencesModel", function () {
        it("should initialize preferencesModel to null if no prefs data in DOM", function () {
          expect(ilios.preferences.preferencesModel).toBe(null);
        });
      });

      describe("installPreferencesModel()", function () {
        var container;

        beforeEach(function () {
          container = document.createElement('script');
          container.setAttribute("id", "iliosPreferencesModel");
          container.setAttribute("type", "application/json");
        });

        afterEach(function () {
          if (container && container.parentNode) {
            container.parentNode.removeChild(container);
          }
          ilios.preferences.preferencesModel = null;
        });

        it("should return object with expected properties", function () {
          ilios.preferences.installPreferencesModel();
          expect(typeof ilios.preferences.preferencesModel.courseArchiving).toBe("boolean");
          expect(typeof ilios.preferences.preferencesModel.courseRollover).toBe("boolean");
          expect(typeof ilios.preferences.preferencesModel.programYearArchiving).toBe("boolean");
          expect(typeof ilios.preferences.preferencesModel.updateWithServerDispatchedObject).toBe("function");
        });

        it("should default all properties to false", function () {
          ilios.preferences.installPreferencesModel();
          expect(ilios.preferences.preferencesModel.courseArchiving).toBe(false);
          expect(ilios.preferences.preferencesModel.programYearArchiving).toBe(false);
          expect(ilios.preferences.preferencesModel.courseRollover).toBe(false);
        });

        describe("updateWithServerDispatchedObject()", function () {
          it("should update from provided object", function () {
            ilios.preferences.installPreferencesModel();
            var updateObject = {
              py_archiving: true,
              course_archiving: true,
              course_rollover: true
            };
            ilios.preferences.preferencesModel.updateWithServerDispatchedObject(updateObject);
            expect(ilios.preferences.preferencesModel.courseArchiving).toBe(true);
            expect(ilios.preferences.preferencesModel.programYearArchiving).toBe(true);
            expect(ilios.preferences.preferencesModel.courseRollover).toBe(true);
          });
        });

        it("should intialize preferencesModel with prefs data in DOM", function () {
          container.innerHTML = "{\"py_archiving\":true,\"course_archiving\":true,\"course_rollover\":true}";
          document.body.appendChild(container);
          ilios.preferences.installPreferencesModel();
          expect(ilios.preferences.preferencesModel.programYearArchiving).toBe(true);
          expect(ilios.preferences.preferencesModel.courseArchiving).toBe(true);
          expect(ilios.preferences.preferencesModel.courseRollover).toBe(true);
        });

        it("should call failure handler and act as if no object passed if preferencesModel in DOM is invalid JSON", function () {
          spyOn(ilios.global, 'defaultAJAXFailureHandler');
          container.innerHTML = "fjkldjlsf{over\":true}";
          document.body.appendChild(container);
          ilios.preferences.installPreferencesModel();
          expect(ilios.global.defaultAJAXFailureHandler).toHaveBeenCalled();
          expect(ilios.preferences.preferencesModel.programYearArchiving).toBe(false);
          expect(ilios.preferences.preferencesModel.courseArchiving).toBe(false);
          expect(ilios.preferences.preferencesModel.courseRollover).toBe(false);
        });
      });



});
