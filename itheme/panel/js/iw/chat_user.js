$(document).ready(function () {
    var receiverId = $('#receiver-id').val();
    var senderId = $('#sender-id').val();
    var userId = $('#user-id').val();
    var receiverType = $('#receiver-type').val();
    var senderType = $('#sender-type').val();

    // بارگذاری پیام‌های قبلی هنگام بارگذاری صفحه
    loadChat(receiverId, senderId, receiverType, senderType, userId);

  

    // ارسال پیام با کلیک دکمه ارسال
    $('#send-button').on('click', function () {
        var message = $('#message-input').val().trim();

        if (message && receiverId && senderId) {
            $.ajax({
                url: './icore/json/send_message.php',
                type: 'POST',
                data: {
                    message_content: message,
                    receiver_id: receiverId,
                    sender_id: senderId,
                    receiver_type: receiverType,
                    sender_type: senderType
                },
                success: function () {
                    $('#message-input').val(''); // پاک کردن ورودی
                    loadChat(receiverId, senderId, receiverType, senderType, userId); // بارگذاری دوباره چت
                },
                error: function (xhr, status, error) {
                    console.error("Error sending message: " + error);
                }
            });
        } else {
            alert("لطفاً پیام خود را وارد کنید.");
        }
    });

    // ارسال پیام با فشار دادن دکمه Enter
    $('#message-input').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#send-button').click();
        }
    });

    function loadChat(receiverId, senderId, receiverType, senderType, userId) {
        $.ajax({
            url: './icore/json/chat_box.php',
            type: 'POST',
            data: {
                receiver_id: receiverId,
                sender_id: senderId,
                receiver_type: receiverType,
                sender_type: senderType
            },
            success: function (data) {
                var messages = JSON.parse(data);
                var conversationList = $('.conversation-list');
                conversationList.empty();

                messages.forEach(function (message, index) {
                    var messageTime = $('<i></i>').text(message.creation_date);
                    var messageSender = $('<i></i>').text(message.sender_name);
                    var messageText = $('<p></p>').text(message.message_content);

                    var chatAvatar = $('<div></div>').addClass('chat-avatar').append(messageTime);
                    var ctextWrap = $('<div></div>').addClass('ctext-wrap').append(messageSender).append(messageText);
                    var conversationText = $('<div></div>').addClass('conversation-text').append(ctextWrap);
                    var messageElement = $('<li></li>').addClass('clearfix').append(chatAvatar).append(conversationText);

                    // Alternate alignment
                    if (message.sender_id != userId) {
                        messageElement.addClass('odd');
                    }

                    conversationList.append(messageElement);
                });

                // Scroll to bottom
                conversationList.animate({ scrollTop: conversationList[0].scrollHeight }, 'fast');
            },
            error: function (xhr, status, error) {
                console.error("Error loading chat: " + error);
            }
        });
    }
    
    // Load chat every 7 seconds
    setInterval(function () {
        loadChat(receiverId, senderId, receiverType, senderType, userId);
    }, 7000);
});
                                              
