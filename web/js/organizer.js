(function() {
  $(document).ready(function() {
    return $('.switch').click(function() {
      var sw;
      sw = $(this);
      $('#main > div').fadeOut('fast', function() {
        console.log(sw.attr('rel'));
        return $('#' + sw.attr('rel')).fadeIn('slow');
      });
      return false;
    });
  });
}).call(this);
