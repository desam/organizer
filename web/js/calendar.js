(function() {
  var Calendarm, Calendarw, currentDate, currentGroup;
  if (!currentDate) {
    currentDate = new Date();
  }
  if (!currentGroup) {
    currentGroup = 'G1';
  }
  Calendarw = function() {
    var self;
    self = this;
    this.days = 7;
    this.distributeEvents = function(data) {
      var daydiv, divs, event, hour, index, markup, _i, _len, _results;
      divs = $('#calendar > div').not('#first_calumn');
      markup = '<strong>${title}</strong><br />\
        <span class="info">\
                at ${from.split(" ")[1]}\
                in ${location}\
        </span>';
      $.template("event", markup);
      _results = [];
      for (_i = 0, _len = data.length; _i < _len; _i++) {
        event = data[_i];
        daydiv = self.bi(event, divs);
        _results.push(daydiv != null ? (hour = event.from.split(" ")[1].split(":"), index = hour[0] * 2, hour[1] !== '00' ? index += 1 : void 0, $.tmpl("event", event).appendTo(daydiv.find('ul').children()[index])) : void 0);
      }
      return _results;
    };
    this.bi = function(event, divs) {
      var edate, elem, max, mid, min, value;
      value = event.from.split(" ")[0];
      min = 0;
      max = divs.length - 1;
      elem = null;
      edate = null;
      if (!($(divs[min]).data('date') != null)) {
        return;
      }
      while (!(edate === value || min > max)) {
        mid = Math.floor(min + (max - min) / 2);
        elem = $(divs[mid]);
        edate = elem.data('date');
        if (edate < value) {
          min = mid + 1;
        } else if (edate > value) {
          max = mid - 1;
        }
      }
      if (edate === value) {
        return elem;
      }
      return null;
    };
    this.nextRange = function() {
      currentDate.setDate(currentDate.getDate() + this.days);
      return self.refresh();
    };
    this.prevRange = function() {
      currentDate.setDate(currentDate.getDate() - this.days);
      return self.refresh();
    };
    this.refresh = function() {
      self = this;
      self.refreshTitle();
      self.draw();
      return self.refreshEvents();
    };
    this.refreshEvents = function(range) {
      if (range == null) {
        range = 7;
      }
      return $.getJSON('calendar', {
        "from": currentDate.nice(),
        "range": range,
        "group": currentGroup
      }, function(data) {
        return self.distributeEvents(data);
      });
    };
    this.refreshTitle = function() {
      var pattern, t, to;
      t = document.getElementById('caltitle');
      to = new Date(currentDate);
      to.setDate(to.getDate() + this.days);
      pattern = /From \d{4}-\d{2}-\d{2}/;
      t.innerHTML = t.innerHTML.replace(pattern, "From " + (currentDate.nice()));
      pattern = /to \d{4}-\d{2}-\d{2}/;
      return t.innerHTML = t.innerHTML.replace(pattern, "to " + (to.nice()));
    };
    this.draw = function() {
      var cal, calDiv, d, days, first, i, timeslots, _ref;
      days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      timeslots = "";
      for (i = 1; i <= 24; i++) {
        timeslots += '<li class="row"></li><li class="row"></li>';
      }
      cal = "";
      cal += "<div id=\"first_calumn\" class=\"calumn\">        <h5>" + (currentDate.getFullYear()) + "</h5>        <ul>";
      for (i = 0; i <= 23; i++) {
        cal += "<li class=\"row\">" + i + ":00</li>";
        cal += "<li class=\"row\">" + i + ":30</li>";
      }
      cal += '</ul></div>';
      for (i = 0, _ref = this.days - 1; (0 <= _ref ? i <= _ref : i >= _ref); (0 <= _ref ? i += 1 : i -= 1)) {
        d = new Date(currentDate);
        d.setDate(d.getDate() + i);
        cal += "<div class=\"calumn\" data-date=\"" + (d.nice()) + "\">              <h5>" + days[d.getDay()] + " " + (d.getDate()) + "</h5>              <ul>" + timeslots + "</ul>              </div>";
      }
      calDiv = $('#calendar');
      calDiv.html(cal);
      first = $('#first_calumn');
      return calDiv.find('.calumn').width((calDiv.outerWidth() - first.outerWidth() - this.days) / this.days);
    };
    return false;
  };
  Calendarm = function() {
    this.columns = 7;
    this.rows = 5;
    this.distributeEvents = function(data) {
      var daydiv, divs, event, self, _i, _len, _results;
      self = this;
      divs = $('#calendar .daybox');
      _results = [];
      for (_i = 0, _len = data.length; _i < _len; _i++) {
        event = data[_i];
        daydiv = self.bi(event, divs);
        _results.push(daydiv != null ? daydiv.find('ul').append("<li>" + event.title + "</li>") : void 0);
      }
      return _results;
    };
    this.draw = function() {
      var boxheight, boxwidth, cal, calDiv, d, days, i, _ref, _ref2;
      days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      cal = "";
      for (i = 0, _ref = this.columns - 1; (0 <= _ref ? i <= _ref : i >= _ref); (0 <= _ref ? i += 1 : i -= 1)) {
        cal += "<div class=\"dayofweek\">              " + days[(currentDate.getDay() + i) % days.length] + "              </div>";
      }
      cal += '<div>';
      for (i = 0, _ref2 = this.columns * this.rows - 1; (0 <= _ref2 ? i <= _ref2 : i >= _ref2); (0 <= _ref2 ? i += 1 : i -= 1)) {
        d = new Date(currentDate);
        d.setDate(d.getDate() + i);
        cal += "          <div class=\"daybox\" data-date=\"" + (d.nice()) + "\">          <span class=\"dayofmonth\">" + (d.getDate()) + "</span>          <ul></ul>          </div>";
      }
      cal += '</div>';
      calDiv = $('#calendar');
      calDiv.html(cal);
      boxwidth = (calDiv.outerWidth() - this.columns) / this.columns;
      boxheight = (calDiv.outerHeight() - this.rows) / this.rows;
      calDiv.find('.dayofweek').width(boxwidth);
      calDiv.find('.daybox').width(boxwidth);
      return calDiv.find('.daybox').height(boxheight);
    };
    this.refreshEvents = function(range) {
      return Calendarm.prototype.refreshEvents(35);
    };
    this.prevRange = function() {
      var self;
      self = this;
      currentDate.setDate(currentDate.getDate() - this.columns * this.rows);
      return self.refresh();
    };
    this.nextRange = function() {
      var self;
      self = this;
      currentDate.setDate(currentDate.getDate() + this.columns * this.rows);
      return self.refresh();
    };
    return false;
  };
  Calendarm.prototype = new Calendarw();
  $(document).ready(function() {
    var cal;
    Date.prototype.nice = function() {
      var day, month, year;
      year = this.getFullYear();
      month = this.getMonth() + 1;
      if (month < 10) {
        month = "0" + month;
      }
      day = this.getDate();
      if (day < 10) {
        day = "0" + day;
      }
      return "" + year + "-" + month + "-" + day;
    };
    cal = new Calendarw();
    cal.refresh();
    $('#next').bind('click', function(event) {
      cal.nextRange();
      return false;
    });
    $('#prev').bind('click', function(event) {
      cal.prevRange();
      return false;
    });
    $('#week').bind('click', function(event) {
      cal = new Calendarw();
      cal.refresh();
      return false;
    });
    $('#month').bind('click', function(event) {
      cal = new Calendarm();
      cal.refresh();
      return false;
    });
    return $('#today').bind('click', function(event) {
      currentDate = new Date();
      cal.refresh();
      return false;
    });
  });
}).call(this);
