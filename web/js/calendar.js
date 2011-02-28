function distributeEvents (data) {
    var divs = $('#calendar > div');

    //creating microtemplate "event"
    var markup = '<strong>${title}</strong><br /><span class="info">at ${from.split(" ")[1]} in ${location}</span>';
    $.template("event", markup);

    // for each event, if event.from matches any div's date attribute, append it to this div
    for(var i = 0; i < data.length; i++) {

        // binary search --> O(logn)
        var value = data[i].from.split(" ")[0];
        var min = 1; //index 0 isn't what we're looking for
        var max = divs.length - 1;
        var elem, edate;

        if(typeof $(divs[min]).attr('date') === 'undefined') return;

        do {
            var mid = Math.floor(min + (max - min) / 2);

            elem = $(divs[mid]);
            edate = elem.attr('date');

            if (edate < value) min = mid + 1;
            else if (edate > value) max = mid - 1;

        } while (!(edate === value || min > max))

        //success, column found.. now the row..
        if(edate === value) {
            var hour = data[i].from.split(" ")[1].split(":"); //hour = '09:00'
            var index = hour[0] * 2; //09:00 --> index = 18
            if (hour[1] !== '00') index += 1; //09:30 --> index = 19

            $.tmpl("event", data[i]).appendTo(elem.find('ul').children()[index]);
        }
    }
}

function drawCalendar (from, n) {
    if (!(from instanceof Date)) {
        console.log(from, "isn't a Date !");
        return;
    }

    var calDiv = document.getElementById('calendar');
    //FIXME really ? i have to do that ?
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    var timeslots = "";
    for (var i = 0; i < 24; i++) {
        timeslots += '<li class="row"></li><li class="row"></li>';
    }

    var cal = "";
    for (var i = 0; i < n; i++) {
        var d = new Date(from);
        d.setDate(d.getDate() + i);
        cal += "<div class=\"calumn\" date=\""+ d.nice() +"\"><h5>"+
            days[d.getDay()] +' '+ d.getDate() +"</h5><ul>"+
                timeslots +"</ul></div>";
    }

    var first = $('#first_calumn');
    $(calDiv).html(first).append(cal);
}

var nextRange = function (e) {
    var range = 7;

    from = getCurrentDate();
    to = new Date(from);

    from.setDate(from.getDate() + range);
    to.setDate(to.getDate() + range * 2);

    changeCalendarTitle(from, to);

    drawCalendar(from, range);

    refreshEvents(from);

    return false;
}

var prevRange = function (e) {
    var range = 7;

    from = getCurrentDate();
    to = new Date(from);

    from.setDate(from.getDate() - range );
    to.setDate(to.getDate());

    changeCalendarTitle(from, to);

    drawCalendar(from, range);

    refreshEvents(from);

    return false;
}

var refreshEvents = function (from) {
    $.getJSON(from.nice(), function (data) {
        data.push({"title": "kikoo", "from": "2010-09-25 00:00", "location": "hahaha"});
        data.push({"title": "foobar", "from": "2010-09-25 1:30", "location": "hihihi"});
        distributeEvents(data);
    });
}

var changeCalendarTitle = function (from, to) {
    var pattern2 = /From \d{4}-\d{2}-\d{2}/;
    caltitle.innerHTML = caltitle.innerHTML.replace(pattern2, 'From ' + from.nice());
    var pattern2 = /to \d{4}-\d{2}-\d{2}/;
    caltitle.innerHTML = caltitle.innerHTML.replace(pattern2, 'to ' + to.nice());
}

var getCurrentDate = function () {
    caltitle = document.getElementById('caltitle');

    var pattern = /^From (\d{4})-(\d{2})-(\d{2}).* to (\d{4})-(\d{2})-(\d{2}).*$/;
    var matches = pattern.exec(caltitle.innerHTML)
      , from = new Date(matches[1], matches[2] - 1, matches[3]);

    delete matches;

    return from;
}

$(document).ready(function () {
    var range = 7;
    
    //TODO where can i put this ? ^-^
    Date.prototype.nice = function () {
        return this.getFullYear() +'-'+
            ((this.getMonth() + 1) < 10 ? '0' + (this.getMonth() + 1) : (this.getMonth() + 1))
        +'-'+ (this.getDate() < 10 ? '0'+ this.getDate() : this.getDate());
    }

    var m = window.location.toString().match(/(\d{4})-(\d{2})-(\d{2})$/)
    d = new Date(m[1], m[2] - 1, m[3]);

    drawCalendar(d, range);
    refreshEvents(d);

    $('#next').click(nextRange);
    $('#prev').click(prevRange);
});
