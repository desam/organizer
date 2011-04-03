(function() {
  var chatdiv, chatinput, chatwindow, client, newMessage;
  client = new Faye.Client("http://" + http_host + ":8888/faye", {
    timeout: 120
  });
  chatdiv = $('#chat');
  chatwindow = $('#chatwindow');
  chatinput = $('#chatinput');
  newMessage = function(message) {
    var scroll;
    if (chatwindow[0].scrollHeight - chatwindow.scrollTop() === chatwindow.outerHeight()) {
      scroll = true;
    }
    $("<div class=\"chatmessage\" style=\"display: none\">      <strong>" + message.author + "</strong>: " + message.text + "</div>").appendTo(chatwindow).fadeIn();
    if (scroll) {
      return chatwindow.animate({
        scrollTop: chatwindow[0].scrollHeight
      });
    }
  };
  $(document).ready(function() {
    var chatsub;
    if (!(window.username != null)) {
      window.username = 'unnamed';
    }
    chatinput.keydown(function(e) {
      if (e.keyCode === 13 && $(this).val() !== '') {
        client.publish('/chat', {
          text: $(this).val(),
          author: username
        });
        return $(this).val('');
      }
    });
    return chatsub = client.subscribe('/chat', function(message) {
      return newMessage(message);
    });
  });
}).call(this);
