(function() {
  $(document).ready(function() {
    return $('.switch').click(function() {
      var sw;
      sw = $(this);
      $('#main > div.active').removeClass('active').fadeOut('fast', function() {
        var caldiv, cw;
        $('#' + sw.attr('rel')).addClass('active').fadeIn('slow');
        switch (sw[0].id) {
          case 'calwidget':
            caldiv = $('#calendar');
            return caldiv.height($(window).height() - caldiv.offset().top - 20);
          case 'chatwidget':
            cw = $('#chatwindow');
            return cw.height($('#chatinput').offset().top - cw.offset().top);
        }
      });
      return false;
    });
  });
}).call(this);
