if !currentDate then currentDate = new Date()
if !currentGroup then currentGroup = 'G1'

caldiv = $('#calendar')
token = $('#token').text().trim()
url = window.location.pathname.slice(0, window.location.pathname.lastIndexOf('/') + 1)

Calendarw = ->
  self = this
  @days = 7

  @draw = ->
    days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

    timeslots = ""
    for i in [0 .. 47]
      timeslots += "<li class=\"row\"></li>"

    cal = ""
    # drawing first column (hours)
    cal += "<div id=\"first_calumn\" class=\"calumn\">
        <h5>#{currentDate.getFullYear()}</h5>
        <ul>"

    for i in [0 .. 23]
      cal += "<li class=\"row\">#{i}:00</li>"
      cal += "<li class=\"row\"></li>"

    cal += '</ul></div>'

    # drawing other columns
    for i in [0 .. @days - 1]
      d = new Date(currentDate)
      d.setDate(d.getDate() + i)
      cal += "<div class=\"calumn\" data-date=\"#{d.nice()}\">
              <h5>#{days[d.getDay()]} #{d.getDate()}</h5>
              <ul>#{timeslots}</ul>
              </div>"


    caldiv.html(cal)

    first = $('#first_calumn')
    # TODO use relative measures (% instead of px)
    # resizing columns
    caldiv.find('.calumn')
      .width(
        Math.floor((caldiv.outerWidth() - first.outerWidth() - @days - 1) / @days))
      .droppable({
        drop: (event, obj) ->
          self.updateMovedEvent(obj, this)
      })

  @distributeEvents = (data) ->
    divs = $('#calendar > div').not('#first_calumn')

    # creating microtemplate "event"
    markup = '<div class="eventbox" data-id="${id}"><strong>${title}</strong><br />
        <span class="info">
                from ${from.split(" ")[1]} to ${to.split(" ")[1]}
                in ${location}
        </span></div>'
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

        tohour = event.to.split(" ")[1].split(":")

        # eventbox will cover a portion of ul > li
        eventbox = $($.tmpl("event", event))
        ul = daydiv.find('ul')
        li = $(ul.children()[index])

        eventbox.insertBefore(ul)

        # how many timeslots to span ?
        # A timeslot covers 30min
        eventboxSpan =
          ((tohour[0] - hour[0]) * 60 + parseInt(tohour[1]) - hour[1]) / 30

        # as high as ebSpan timeslots, minus eventbox padding
        ebpadding = parseInt(eventbox.css('padding-top')) + parseInt(eventbox.css('padding-bottom'))
        newheight = eventboxSpan * (li.outerHeight() + parseInt(li.css('margin-bottom'))) - ebpadding
        eventbox.height(newheight)

        newwidth = li.outerWidth() - (eventbox.outerWidth() - eventbox.width())
        # FIXME, 10 magical
        eventbox.width(newwidth - 10)

        eventbox.offset({top: li.offset().top})

    $('.eventbox')
    .draggable({
      grid: [divs.outerWidth(), caldiv.find('li.row').outerHeight()],
    })
    .resizable({
      handles: 'n,s',
      grid: [divs.outerWidth(), caldiv.find('li.row').outerHeight()],
      stop: (event, ui) ->
        self.updateResizedEvent(ui)
    })

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
    self.refreshTitle()
    # TODO redraw the WHOLE calendar everytime? i dont think so
    self.draw()
    self.refreshEvents()

  @refreshEvents = (range) ->
    range = 7 unless range?

    $.getJSON(url + 'calendar', {
        "from": currentDate.nice(),
        "range": range,
        "group": currentGroup
        }, (data) -> self.distributeEvents(data))

  @refreshTitle = ->
    t  = $('#caltitle > strong')
    to = new Date(currentDate)
    to.setDate(to.getDate() + @days)

    t[0].innerHTML = currentDate.nice()
    t[1].innerHTML = to.nice()


  # called when an event is drag n' dropped
  @updateMovedEvent = (obj) ->
    event = obj.draggable.tmplItem()

    dates = self.getEventboxDate(obj)

    event.data.from   = dates.newfrom
    event.data.to     = dates.newto
    event.data.refgrp = currentGroup
    event.data._token = token

    $.post(url + "calendar/update/#{event.data.id}", {event:event.data})

  # called when an event is resized
  @updateResizedEvent = (obj) ->
    event = obj.helper.tmplItem()

    dates = self.getEventboxDate(obj)

    event.data.from   = dates.newfrom
    event.data.to     = dates.newto
    event.data.refgrp = currentGroup
    event.data._token = token

    $.post(url + "calendar/update/#{event.data.id}", {event:event.data})

  # returns a date and an hour in function of the position of obj in the calendar
  @getEventboxDate = (obj) ->
    firstday = caldiv.find('.calumn').not('#first_calumn').first()

    # which day? get the horizontal distance from the firstday
    hoffset = Math.floor((obj.position.left - firstday.offset().left) / firstday.width())

    # so... what day is it?
    newdate = firstday.nextAll().andSelf().eq(hoffset).data('date')

    # when does the event start? get the vertical distance from the first row
    voffset = Math.floor(obj.position.top - caldiv.find('ul').offset().top) / 25

    # so... what time is it?
    hour = Math.floor(voffset / 2) % 24
    hour = if hour < 10 then '0' + hour else hour

    # offset 18 --> 09:00
    # offset 19 --> 09:30
    newfrom = "#{newdate} #{hour}:#{if Math.floor(voffset) % 2 then '30' else '00'}"

    # how many timeslot does it cover?
    # FIXME, 25 magical number, timeslot height
    voffset += Math.floor(obj.helper.outerHeight() / 25)
    hour = Math.floor(voffset / 2) % 24
    hour = if hour < 10 then '0' + hour else hour
    newto = "#{newdate} #{hour}:#{if Math.floor(voffset) % 2 then '30' else '00'}"

    return {newto: newto, newfrom: newfrom}
  false


Calendarm = ->
  @columns = 7
  @rows = 5
  self = this

  @distributeEvents = (data) ->
    divs = $('#calendar .daybox')

    for event in data
      daydiv = self.bi(event, divs)

      if daydiv?
        daydiv.find('ul').append("<li>#{event.title}</li>")

  @draw = () ->
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

    caldiv.html(cal)

    # TODO use relative measures (% instead of px)
    # resizing dayboxes
    boxwidth = (caldiv.outerWidth() - @columns) / @columns
    boxheight = (caldiv.outerHeight() - @rows) / @rows

    caldiv.find('.dayofweek').width(boxwidth)
    caldiv.find('.daybox').width(boxwidth)
    caldiv.find('.daybox').height(boxheight)

  @refreshEvents = (range) ->
    Calendarm.prototype.refreshEvents(35)

  @prevRange = ->
    currentDate.setDate(currentDate.getDate() - @columns * @rows)
    self.refresh()

  @nextRange = ->
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
