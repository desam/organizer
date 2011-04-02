$(document).ready ->
  $('.switch').click ->
    sw = $(this)
    $('#main > div').fadeOut 'fast', ->
      console.log(sw.attr('rel'))
      $('#' + sw.attr('rel')).fadeIn 'slow'
    return false
