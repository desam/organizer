# organizer related tasks

$(document).ready ->
# display corresponding widget when clicked
  $('.switch').click ->
    sw = $(this)
    $('#main > div.active').removeClass('active').fadeOut 'fast', ->
      $('#' + sw.attr('rel')).addClass('active').fadeIn 'slow'

      # resizing to viewport after fadeIn
      switch sw[0].id
        when 'calwidget'
          caldiv = $('#calendar')
          caldiv.height($(window).height() - caldiv.offset().top - 20)
        when 'chatwidget'
          cw = $('#chatwindow')
          cw.height($('#chatinput').offset().top - cw.offset().top)

    return false

