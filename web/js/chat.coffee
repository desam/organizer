client = new Faye.Client("http://#{http_host}:8888/faye", timeout: 120)

chatdiv = $('#chat')
chatwindow = $('#chatwindow')
chatinput = $('#chatinput')

# on new message, add msg to dom and scroll down if needed
newMessage = (message) ->
  if chatwindow[0].scrollHeight - chatwindow.scrollTop() == chatwindow.outerHeight()
    scroll = true

  $("<div class=\"chatmessage\" style=\"display: none\">
      <strong>#{message.author}</strong>: #{message.text}</div>")
  .appendTo(chatwindow).fadeIn()

  if scroll then chatwindow.animate({scrollTop: chatwindow[0].scrollHeight})


$(document).ready ->
  # unnamed is the default nickname (should not happen)
  if not window.username? then window.username = 'unnamed'

  # key to send msg
  chatinput.keydown (e) ->
    # if user press 'enter' and field isn't empty, send and reset field
    if e.keyCode == 13 and $(this).val() != ''
      client.publish '/chat', {
        text: $(this).val()
        , author: username
      }

      $(this).val('')

  # subscribe to chat channel
  chatsub = client.subscribe '/chat', (message) ->
    newMessage(message)
