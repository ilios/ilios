describe("ilios.timer", function () {
  describe("startIdleTimer()", function () {
    var container;

    beforeEach(function () {
      // test double
      YAHOO.util.IdleTimer = {subscribe: function () {}, start: function () {}};
      spyOn(YAHOO.util.IdleTimer, "subscribe");
      spyOn(YAHOO.util.IdleTimer, "start");

      container = document.createElement('script');
      container.setAttribute("id", "iliosIdleTimer");
      container.setAttribute("type", "application/json");
    });

    afterEach(function () {
      // clean up test double
      delete YAHOO.util.IdleTimer;

      if (container && container.parentNode) {
        container.parentNode.removeChild(container);
      }
    });

    it("should use the timeout specified in the idle timer data in the DOM", function () {
      container.innerHTML = "{\"timeout\":9999999}";
      document.body.appendChild(container);
      ilios.timer.startIdleTimer();
      expect(YAHOO.util.IdleTimer.start).toHaveBeenCalledWith(9999999, document);
    });

    it("should use default timeout if supplied timeout is not a number", function () {
      container.innerHTML = "{\"timeout\":\"totally not a number\"}";
      document.body.appendChild(container);
      ilios.timer.startIdleTimer();
      expect(YAHOO.util.IdleTimer.start).not.toHaveBeenCalledWith("totally not a number", document);
      expect(YAHOO.util.IdleTimer.start).toHaveBeenCalledWith(2700000, document);
    });

    it("should call subscribe with the hardcoded callback", function () {
      container.innerHTML = "{}";
      document.body.appendChild(container);
      ilios.timer.startIdleTimer();
      expect(YAHOO.util.IdleTimer.subscribe).toHaveBeenCalledWith("idle", jasmine.any(Function));
    });

    it("should be called through YAHOO.util.Event.onDOMReady()", function () {
      spyOn(YAHOO.util.Event, "onDOMReady");
      ilios.timer.startIdleTimer();
      expect(YAHOO.util.Event.onDOMReady).toHaveBeenCalled();
    });

    it("should not set an IdleTimer if no DOM data object is present", function () {
      ilios.timer.startIdleTimer();
      expect(YAHOO.util.IdleTimer.subscribe).not.toHaveBeenCalled();
    });
  });
});
