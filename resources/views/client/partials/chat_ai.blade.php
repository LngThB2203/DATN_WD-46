
<div id="chat-widget">
    <div id="chat-toggle">✨</div>

    <div id="chat-box" class="hidden">
        <div id="chat-header">
            <span>Tư vấn 46 Perfume</span>
            <button id="chat-close" type="button" title="Đóng">&times;</button>
        </div>

        <div id="chat-messages"></div>

        

        <div id="chat-input">
            <input type="text" id="message-input" placeholder="Hỏi về mùi hương..." autocomplete="off">
            <button id="send-btn">Gửi</button>
        </div>
    </div>
</div>

<style>
    /* Tổng thể */
    #chat-widget { position: fixed; bottom: 20px; right: 20px; z-index: 999999; font-family: 'Poppins', sans-serif; }
    .hidden { display: none !important; }

    /* Nút tròn nhỏ gọn */
    #chat-toggle { 
        background: #ce8460; color: white; width: 55px; height: 55px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-size: 24px; cursor: pointer;
        box-shadow: 0 4px 15px rgba(206, 132, 96, 0.4); transition: transform 0.3s;
    }
    #chat-toggle:hover { transform: scale(1.1); }

    /* Cửa sổ chat bé lại & sang trọng */
    #chat-box { 
        position: absolute; bottom: 70px; right: 0; width: 300px; height: 450px; 
        background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); 
        display: flex; flex-direction: column; overflow: hidden;
    }

    /* Header */
    #chat-header { background: #333; color: #ce8460; padding: 12px 15px; display: flex; justify-content: space-between; align-items: center; font-weight: bold; border-bottom: 2px solid #ce8460; }
    #chat-close { background: none; border: none; color: #ce8460; font-size: 28px; cursor: pointer; line-height: 1; }
    #chat-close:hover { color: #fff; }

    /* Tin nhắn */
    #chat-messages { flex: 1; padding: 15px; overflow-y: auto; background: #fafafa; display: flex; flex-direction: column; gap: 10px; scroll-behavior: smooth; }
    .bot-msg, .user-msg { padding: 8px 14px; font-size: 13.5px; max-width: 85%; line-height: 1.5; position: relative; }
    .bot-msg { background: white; border-radius: 15px 15px 15px 4px; border: 1px solid #eee; align-self: flex-start; box-shadow: 0 2px 5px rgba(0,0,0,0.02); }
    .user-msg { background: #ce8460; color: white; border-radius: 15px 15px 4px 15px; align-self: flex-end; }

    /* Hiệu ứng đang gõ */
    .typing { display: flex; gap: 4px; padding: 12px !important; width: fit-content; }
    .typing span { width: 6px; height: 6px; background: #ce8460; border-radius: 50%; animation: blink 1.4s infinite both; }
    .typing span:nth-child(2) { animation-delay: 0.2s; }
    .typing span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes blink { 0%, 80%, 100% { opacity: 0.2; } 40% { opacity: 1; } }

    /* Nút gợi ý vuốt ngang */
    #chat-suggestions { padding: 8px; display: flex; gap: 6px; overflow-x: auto; white-space: nowrap; background: #fff; border-top: 1px solid #f2f2f2; }
    #chat-suggestions::-webkit-scrollbar { display: none; }
    .suggest-btn { background: #fff4f0; border: 1px solid #ce8460; color: #ce8460; padding: 5px 12px; border-radius: 20px; font-size: 11px; cursor: pointer; transition: 0.2s; }
    .suggest-btn:hover { background: #ce8460; color: white; }

    /* Input */
    #chat-input { padding: 10px; display: flex; border-top: 1px solid #eee; gap: 8px; background: #fff; }
    #message-input { flex: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 15px; font-size: 13px; outline: none; }
    #send-btn { background: #ce8460; color: white; border: none; padding: 0 18px; border-radius: 20px; cursor: pointer; font-weight: bold; }
    
    .chat-link { display: inline-block; margin-top: 5px; color: #ce8460; text-decoration: underline; font-weight: bold; }
</style>

<script>
$(document).ready(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    $("#chat-toggle").on("click", function(e) {
        e.stopPropagation();
        const box = $("#chat-box");
        if (box.hasClass("hidden")) {
            box.removeClass("hidden");
            if ($("#chat-messages").is(':empty')) { loadMessages(); }
        } else {
            box.addClass("hidden");
        }
    });

    // NÚT ĐÓNG (Dứt khoát)
    $("#chat-close").on("click", function(e) {
        e.stopPropagation();
        $("#chat-box").addClass("hidden");
    });

    // GỬI TIN NHẮN
    $("#send-btn").on("click", function() {
        const input = $("#message-input");
        const msg = input.val().trim();
        if (!msg) return;

        $(this).prop('disabled', true);
        appendOne({ sender: 'user', message: msg });
        input.val('');
        
        // Hiện hiệu ứng đang gõ
        const loaderId = "typing-" + Date.now();
        $("#chat-messages").append(`<div id="${loaderId}" class="bot-msg typing"><span></span><span></span><span></span></div>`);
        scrollBottom();

        $.ajax({
            url: '/chat/send',
            method: 'POST',
            data: { message: msg },
            success: function(res) {
                $(`#${loaderId}`).remove();
                if (res && res.bot) {
                    appendOne(res.bot);
                } else {
                    appendOne({ sender: 'bot', message: 'Không nhận được phản hồi từ hệ thống. Vui lòng thử lại!' });
                }
            },
            error: function(xhr, status, error) {
                $(`#${loaderId}`).remove();
                console.error('Chat error:', { xhr: xhr, status: status, error: error });
                let errorMsg = 'Hệ thống đang bận, bạn vui lòng thử lại sau nhé!';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 0) {
                    errorMsg = 'Không thể kết nối đến server. Vui lòng kiểm tra kết nối mạng!';
                } else if (xhr.status === 500) {
                    errorMsg = 'Lỗi server. Vui lòng thử lại sau!';
                }
                appendOne({ sender: 'bot', message: errorMsg });
            },
            complete: function() { 
                $("#send-btn").prop('disabled', false); 
            }
        });
    });

    // CLICK NÚT GỢI Ý
    $(document).on("click", ".suggest-btn", function() {
        $("#message-input").val($(this).text());
        $("#send-btn").click();
    });

    $("#message-input").on("keypress", function(e) { if (e.which === 13) $("#send-btn").click(); });
});

function loadMessages() {
    $("#chat-messages").html('<div class="text-center small" style="color:#888; padding-top:20px;">Đang kết nối chuyên gia...</div>');
    $.get('/chat/messages', function(msgs) {
        $("#chat-messages").empty();
        if (!msgs || msgs.length === 0) {
            appendOne({ sender: 'bot', message: "Xin chào ✨! Tôi là trợ lý ảo của 46 Perfume. Tôi có thể giúp gì cho bạn?" });
        } else {
            msgs.forEach(m => appendOne(m));
        }
    });
}

function appendOne(m) {
    let cls = m.sender === 'user' ? 'user-msg' : 'bot-msg';
    let message = m.message;

    if (m.sender === 'bot') {
        const linkRegex = /\/products\/([a-z0-9\-]+)/gi;

        message = message.replace(linkRegex, function (match, productId) {
    return `<br>👉 <a href="/product/${productId}" class="chat-link" target="_blank">Xem sản phẩm ↗</a>`;
});

        message = message.replace(/\n/g, '<br>');
    }

    $("#chat-messages").append(
        $('<div class="' + cls + '"></div>').html(message)
    );

    scrollBottom();
}


function scrollBottom() {
    const container = $("#chat-messages");
    container.animate({ scrollTop: container[0].scrollHeight }, 300);
}
</script>
