//TODO REFACTOOOOR,
//not DRY enough (wtf distinction week/month for every method?)

//0 -> week
//1 -> month
var calDisplay = 0;
var weekcolumnz = 7;
var monthcolumnz = 7;
var monthrowz = 5;
if (!currentDate) var currentDate = new Date(Date.now());
if (!currentGroup) var currentGroup = 1;

var distributeEvents = function (data) {
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

            $.tmpl("event", data[i]).appendTo(
		elem.find('ul').children()[index]);
        }
    }
}

var nextRange = function (e) {
    from = currentDate;
    to = new Date(from);

    from.setDate(from.getDate() + weekcolumnz);
    to.setDate(to.getDate() + weekcolumnz * 2);

    currentDate = from;

    changeCalendarTitle(from, to);

    if (calDisplay === 0) drawWeekCalendar(from, weekcolumnz);
    if (calDisplay === 1) drawMonthCalendar(from, monthcolumnz, monthrowz);

    refreshEvents(from);
}

var prevRange = function (e) {
    from = currentDate;
    to = new Date(from);

    currentDate = from;

    from.setDate(from.getDate() - weekcolumnz );
    to.setDate(to.getDate());

    changeCalendarTitle(from, to);

    if (calDisplay === 0) drawWeekCalendar(from, weekcolumnz);
    if (calDisplay === 1) drawMonthCalendar(from, monthcolumnz, monthrowz);

    refreshEvents(from);
}

var refreshEvents = function (from, range) {
    if (!range) var range = 7;

    $.getJSON('calendar', {"from": from.nice(), "range": range, "group": currentGroup}, function (data) {
        distributeEvents(data);
    });
}

var changeCalendarTitle = function (from, to) {
    var caltitle = document.getElementById('caltitle');

    var pattern2 = /From \d{4}-\d{2}-\d{2}/;
    caltitle.innerHTML = caltitle.innerHTML.replace(pattern2, 'From ' + from.nice());
    var pattern2 = /to \d{4}-\d{2}-\d{2}/;
    caltitle.innerHTML = caltitle.innerHTML.replace(pattern2, 'to ' + to.nice());
}

var drawWeekCalendar = function (from) {
    // TODO redraw the WHOLE calendar everytime? i dont think so
    if(!from) var from = currentDate;

    if (!(from instanceof Date)) {
        // console.log(from, "isn't a Date !");
        return false;
    }

    //FIXME really ? i have to do that ?
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var n = weekcolumnz;

    var timeslots = "";
    for (var i = 0; i < 24; i++) {
        timeslots += '<li class="row"></li><li class="row"></li>';
    }

    var cal = "";
    //drawing first column (hours)
    cal += '<div id="first_calumn" class="calumn"><h5>'+ from.getFullYear() +'</h5><ul>';
    for (var i = 0; i < 24; i++) {
        cal += '<li class="row">'+ i +':00</li>';
        cal += '<li class="row">'+ i +':30</li>';
    }
    cal += '</ul></div>'

    //drawing other columns
    for (var i = 0; i < n; i++) {
        var d = new Date(from);
        d.setDate(d.getDate() + i);
        cal += "<div class=\"calumn\" date=\""+ d.nice() +"\"><h5>"+
            days[d.getDay()] +' '+ d.getDate() +"</h5><ul>"+
                timeslots +"</ul></div>";
    }

    var calDiv = $('#calendar');

    calDiv.html(cal);

    var first = $('#first_calumn');
    //TODO use relative measures (% instead of px)
    //resizing columns
    $('#calendar .calumn').width((calDiv.outerWidth() - first.outerWidth() - n) / n);
}

var drawMonthCalendar = function (from) {
    // TODO redraw the WHOLE calendar everytime? i dont think so
    if(!from) var from = currentDate;

    if (!(from instanceof Date)) {
        // console.log(from, "isn't a Date !");
        return false;
    }

    //FIXME really ? i have to do that ?
    var days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    var n = monthcolumnz;

    var cal = "";
    for (var i = 0; i < days.length; i++) {
        cal += '<div class="mcalumn">';
        cal += '<div class="mdayofweek">'+ days[(from.getDay() + i) % days.length] +'</div>';

        var d = new Date(from);
        d.setDate(d.getDate() + i);
        for (var j = 0; j < monthrowz; j++) {
            cal += '<div class="mrow"><span class="mdayofmonth">'+ d.getDate() +'</span></div>';
            d.setDate(d.getDate() + 7);
        };
        cal += '</div>';
    };

    var calDiv = $('#calendar');

    calDiv.html(cal);

    //TODO use relative measures (% instead of px)
    //resizing columns
    var mcalumns = $('#calendar .mcalumn');
    var mrows = $('#calendar .mrow');
    var mdowHeight = mcalumns.first().find('.mdayofweek').height();

    mcalumns.width((calDiv.outerWidth() - n * 1) / n);
    mrows.height((calDiv.outerHeight() - mdowHeight - monthrowz * 1) / monthrowz);
}

$(document).ready(function () {
    //TODO where can i put this ? ^-^
    Date.prototype.nice = function () {
        return this.getFullYear()
        + '-' + ((this.getMonth() + 1) < 10 ? '0' + (this.getMonth() + 1) : (this.getMonth() + 1))
        + '-' + (this.getDate() < 10 ? '0'+ this.getDate() : this.getDate());
    }

    drawWeekCalendar(currentDate);
    refreshEvents(currentDate, 7);

    $('#next').click(function () {
        nextRange();
        return false;
    });
    $('#prev').click(function () {
        prevRange();
        return false;
    });
    $('#week').click(function () {
        drawWeekCalendar();
        return false;
    });
    $('#month').click(function () {
        drawMonthCalendar();
        return false;
    });
});
