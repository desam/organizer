if !currentDate then currentDate = new Date()
if !currentGroup then currentGroup = 'G1'

Calendarw = ->
  self = this
  @days = 7

  @distributeEvents = (data) ->
    divs = $('#calendar > div').not('#first_calumn')

    # creating microtemplate "event"
    markup = '<strong>${title}</strong><br />
        <span class="info">
                at ${from.split(" ")[1]}
                in ${location}
        </span>'
    $.template("event", markup)

    # for each event,
    # find daydiv[@date = event.from],
    # append it to this daydiv
    for event in data
      daydiv = self.bi(event, divs)

      # success, column found.. now the row..
      if daydiv?
        hour = event.from.split(" ")[1].split(":") # hour = '09:00'
        index = hour[0] * 2  # 09:00 --> index = 18
        if hour[1] != '00' then index += 1  # 09:30 --> index = 19

        $.tmpl("event", event).appendTo(daydiv.find('ul').children()[index])

  @bi = (event, divs) ->
    # binary search
    value = event.from.split(" ")[0]
    min = 0
    max = divs.length - 1
    elem = null
    edate = null

    if not $(divs[min]).data('date')? then return

    until edate == value or min > max
      mid = Math.floor(min + (max - min) / 2)

      elem = $(divs[mid])
      edate = elem.data('date')

      if edate < value then min = mid + 1
      else if edate > value then max = mid - 1

    if edate == value
      return elem

    return null

  @nextRange = ->
    currentDate.setDate(currentDate.getDate() + @days)
    self.refresh()

  @prevRange = ->
    currentDate.setDate(currentDate.getDate() - @days)
    self.refresh()

  @refresh = ->
    self = this
    self.refreshTitle()
    # TODO redraw the WHOLE calendar everytime? i dont think so
    self.draw()
    self.refreshEvents()


  @refreshEvents = (range) ->
    range = 7 unless range?

    $.getJSON('calendar', {
        "from": currentDate.nice(),
        "range": range,
        "group": currentGroup
        }, (data) -> self.distributeEvents(data))


  @refreshTitle = ->
    t = document.getElementById('caltitle')
    to = new Date(currentDate)
    to.setDate(to.getDate() + @days)

    pattern = /From \d{4}-\d{2}-\d{2}/
    t.innerHTML = t.innerHTML.replace(pattern, "From #{currentDate.nice()}")

    pattern = /to \d{4}-\d{2}-\d{2}/
    t.innerHTML = t.innerHTML.replace(pattern, "to #{to.nice()}")


  @draw = () ->
    # FIXME really ? i have to do that ?
    days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

    timeslots = ""
    for i in [1 .. 24]
      timeslots += '<li class="row"></li><li class="row"></li>'

    cal = ""
    # drawing first column (hours)
    cal += "<div id=\"first_calumn\" class=\"calumn\">
        <h5>#{currentDate.getFullYear()}</h5>
        <ul>"

    for i in [0 .. 23]
      cal += "<li class=\"row\">#{i}:00</li>"
      cal += "<li class=\"row\">#{i}:30</li>"

    cal += '</ul></div>'

    # drawing other columns
    for i in [0 .. @days - 1]
      d = new Date(currentDate)
      d.setDate(d.getDate() + i)
      cal += "<div class=\"calumn\" data-date=\"#{d.nice()}\">
              <h5>#{days[d.getDay()]} #{d.getDate()}</h5>
              <ul>#{timeslots}</ul>
              </div>"

    calDiv = $('#calendar')

    calDiv.html(cal)

    first = $('#first_calumn')
    # TODO use relative measures (% instead of px)
    # resizing columns
    calDiv.find('.calumn').width((calDiv.outerWidth() - first.outerWidth() - @days) / @days)
  false


Calendarm = ->
  @columns = 7
  @rows = 5

  @distributeEvents = (data) ->
    self = this
    divs = $('#calendar .daybox')

    for event in data
      daydiv = self.bi(event, divs)

      if daydiv?
        daydiv.find('ul').append("<li>#{event.title}</li>")

  @draw = () ->
    # FIXME really ? i have to do that ?
    days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

    cal = ""
    for i in [0 .. @columns - 1]
      cal += "<div class=\"dayofweek\">
              #{days[(currentDate.getDay() + i) % days.length]}
              </div>"

    cal += '<div>'
    for i in [0 .. @columns * @rows - 1]
      d = new Date(currentDate)
      d.setDate(d.getDate() + i)
      cal += "
          <div class=\"daybox\" data-date=\"#{d.nice()}\">
          <span class=\"dayofmonth\">#{d.getDate()}</span>
          <ul></ul>
          </div>"

    cal += '</div>'

    calDiv = $('#calendar')
    calDiv.html(cal)

    # TODO use relative measures (% instead of px)
    # resizing columns
    boxwidth = (calDiv.outerWidth() - @columns) / @columns
    boxheight = (calDiv.outerHeight() - @rows) / @rows

    calDiv.find('.dayofweek').width(boxwidth)
    calDiv.find('.daybox').width(boxwidth)
    calDiv.find('.daybox').height(boxheight)

  @refreshEvents = (range) ->
    Calendarm.prototype.refreshEvents(35)

  @prevRange = ->
    self = this
    currentDate.setDate(currentDate.getDate() - @columns * @rows)
    self.refresh()

  @nextRange = ->
    self = this
    currentDate.setDate(currentDate.getDate() + @columns * @rows)
    self.refresh()

  false

Calendarm.prototype = new Calendarw()


$(document).ready ->
  # TODO where can i put this ? ^-^
  Date.prototype.nice = ->
    year = this.getFullYear()
    month = this.getMonth() + 1
    month = "0#{month}" if month < 10
    day = this.getDate()
    day = "0#{day}" if day < 10
    return "#{year}-#{month}-#{day}"

  cal = new Calendarw()
  cal.refresh()

  $('#next').bind 'click', (event) ->
    cal.nextRange()
    return false

  $('#prev').bind 'click', (event) ->
    cal.prevRange()
    return false

  $('#week').bind 'click', (event) ->
    cal = new Calendarw()
    cal.refresh()
    return false

  $('#month').bind 'click', (event) ->
    cal = new Calendarm()
    cal.refresh()
    return false

  $('#today').bind 'click', (event) ->
    currentDate = new Date()
    cal.refresh()
    return false
