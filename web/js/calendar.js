(function() {
  var Calendarm, Calendarw, caldiv, currentDate, currentGroup, token, url;
  if (!currentDate) {
    currentDate = new Date();
  }
  if (!currentGroup) {
    currentGroup = 'G1';
  }
  caldiv = $('#calendar');
  token = $('#token').text().trim();
  url = window.location.pathname.slice(0, window.location.pathname.lastIndexOf('/') + 1);
  Calendarw = function() {
    var self;
    self = this;
    this.days = 7;
    this.draw = function() {
      var cal, d, days, first, i, timeslots, _ref;
      days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
      timeslots = "";
      for (i = 0; i <= 47; i++) {
        timeslots += "<li class=\"row\"></li>";
      }
      cal = "";
      cal += "<div id=\"first_calumn\" class=\"calumn\">        <h5>" + (currentDate.getFullYear()) + "</h5>        <ul>";
      for (i = 0; i <= 23; i++) {
        cal += "<li class=\"row\">" + i + ":00</li>";
        cal += "<li class=\"row\"></li>";
      }
      cal += '</ul></div>';
      for (i = 0, _ref = this.days - 1; (0 <= _ref ? i <= _ref : i >= _ref); (0 <= _ref ? i += 1 : i -= 1)) {
        d = new Date(currentDate);
        d.setDate(d.getDate() + i);
        cal += "<div class=\"calumn\" data-date=\"" + (d.nice()) + "\">              <h5>" + days[d.getDay()] + " " + (d.getDate()) + "</h5>              <ul>" + timeslots + "</ul>              </div>";
      }
      caldiv.html(cal);
      first = $('#first_calumn');
      return caldiv.find('.calumn').width(Math.floor((caldiv.outerWidth() - first.outerWidth() - this.days - 1) / this.days)).droppable({
        drop: function(event, obj) {
          return self.updateMovedEvent(obj, this);
        }
      });
    };
    this.distributeEvents = function(data) {
      var daydiv, divs, ebpadding, event, eventbox, eventboxSpan, hour, index, li, markup, newheight, newwidth, tohour, ul, _i, _len;
      divs = $('#calendar > div').not('#first_calumn');
      markup = '<div class="eventbox" data-id="${id}"><strong>${title}</strong><br />\
        <span class="info">\
                from ${from.split(" ")[1]} to ${to.split(" ")[1]}\
                in ${location}\
        </span></div>';
      $.template("event", markup);
      for (_i = 0, _len = data.length; _i < _len; _i++) {
        event = data[_i];
        daydiv = self.bi(event, divs);
        if (daydiv != null) {
          hour = event.from.split(" ")[1].split(":");
          index = hour[0] * 2;
          if (hour[1] !== '00') {
            index += 1;
          }
          tohour = event.to.split(" ")[1].split(":");
          eventbox = $($.tmpl("event", event));
          ul = daydiv.find('ul');
          li = $(ul.children()[index]);
          eventbox.insertBefore(ul);
          eventboxSpan = ((tohour[0] - hour[0]) * 60 + parseInt(tohour[1]) - hour[1]) / 30;
          ebpadding = parseInt(eventbox.css('padding-top')) + parseInt(eventbox.css('padding-bottom'));
          newheight = eventboxSpan * (li.outerHeight() + parseInt(li.css('margin-bottom'))) - ebpadding;
          eventbox.height(newheight);
          newwidth = li.outerWidth() - (eventbox.outerWidth() - eventbox.width());
          eventbox.width(newwidth - 10);
          eventbox.offset({
            top: li.offset().top
          });
        }
      }
      return $('.eventbox').draggable({
        grid: [divs.outerWidth(), caldiv.find('li.row').outerHeight()]
      }).resizable({
        handles: 'n,s',
        grid: [divs.outerWidth(), caldiv.find('li.row').outerHeight()],
        stop: function(event, ui) {
          return self.updateResizedEvent(ui);
        }
      });
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
      self.refreshTitle();
      self.draw();
      return self.refreshEvents();
    };
    this.refreshEvents = function(range) {
      if (range == null) {
        range = 7;
      }
      return $.getJSON(url + 'calendar', {
        "from": currentDate.nice(),
        "range": range,
        "group": currentGroup
      }, function(data) {
        return self.distributeEvents(data);
      });
    };
    this.refreshTitle = function() {
      var t, to;
      t = $('#caltitle > strong');
      to = new Date(currentDate);
      to.setDate(to.getDate() + this.days);
      t[0].innerHTML = currentDate.nice();
      return t[1].innerHTML = to.nice();
    };
    this.updateMovedEvent = function(obj) {
      var dates, event;
      event = obj.draggable.tmplItem();
      dates = self.getEventboxDate(obj);
      event.data.from = dates.newfrom;
      event.data.to = dates.newto;
      event.data.refgrp = currentGroup;
      event.data._token = token;
      return $.post(url + ("calendar/update/" + event.data.id), {
        event: event.data
      });
    };
    this.updateResizedEvent = function(obj) {
      var dates, event;
      event = obj.helper.tmplItem();
      dates = self.getEventboxDate(obj);
      event.data.from = dates.newfrom;
      event.data.to = dates.newto;
      event.data.refgrp = currentGroup;
      event.data._token = token;
      return $.post(url + ("calendar/update/" + event.data.id), {
        event: event.data
      });
    };
    this.getEventboxDate = function(obj) {
      var firstday, hoffset, hour, newdate, newfrom, newto, voffset;
      firstday = caldiv.find('.calumn').not('#first_calumn').first();
      hoffset = Math.floor((obj.position.left - firstday.offset().left) / firstday.width());
      newdate = firstday.nextAll().andSelf().eq(hoffset).data('date');
      voffset = Math.floor(obj.position.top - caldiv.find('ul').offset().top) / 25;
      hour = Math.floor(voffset / 2) % 24;
      hour = hour < 10 ? '0' + hour : hour;
      newfrom = "" + newdate + " " + hour + ":" + (Math.floor(voffset) % 2 ? '30' : '00');
      voffset += Math.floor(obj.helper.outerHeight() / 25);
      hour = Math.floor(voffset / 2) % 24;
      hour = hour < 10 ? '0' + hour : hour;
      newto = "" + newdate + " " + hour + ":" + (Math.floor(voffset) % 2 ? '30' : '00');
      return {
        newto: newto,
        newfrom: newfrom
      };
    };
    return false;
  };
  Calendarm = function() {
    var self;
    this.columns = 7;
    this.rows = 5;
    self = this;
    this.distributeEvents = function(data) {
      var daydiv, divs, event, _i, _len, _results;
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
      var boxheight, boxwidth, cal, d, days, i, _ref, _ref2;
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
      caldiv.html(cal);
      boxwidth = (caldiv.outerWidth() - this.columns) / this.columns;
      boxheight = (caldiv.outerHeight() - this.rows) / this.rows;
      caldiv.find('.dayofweek').width(boxwidth);
      caldiv.find('.daybox').width(boxwidth);
      return caldiv.find('.daybox').height(boxheight);
    };
    this.refreshEvents = function(range) {
      return Calendarm.prototype.refreshEvents(35);
    };
    this.prevRange = function() {
      currentDate.setDate(currentDate.getDate() - this.columns * this.rows);
      return self.refresh();
    };
    this.nextRange = function() {
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
