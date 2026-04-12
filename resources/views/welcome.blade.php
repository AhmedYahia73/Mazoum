<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار Real-Time | mazoom.online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; padding: 50px; font-family: sans-serif; }
        #messages-container { height: 350px; overflow-y: auto; background: white; border-radius: 8px; padding: 15px; border: 1px solid #ddd; }
        .message-item { background: #e9ecef; border-radius: 5px; padding: 12px; margin-bottom: 10px; border-right: 5px solid #007bff; text-align: right; }
        .status-online { color: #28a745; font-weight: bold; }
        .status-offline { color: #dc3545; font-weight: bold; }
        .card-header { font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">نظام استقبال الرسائل المباشر (ChatEvent)</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>حالة الاتصال بالسيرفر:</span>
                        <span id="connection-status" class="status-offline">جاري محاولة الاتصال...</span>
                    </div>
                    
                    <div id="messages-container">
                        <div class="text-muted text-center mt-5">في انتظار وصول بيانات من السيرفر عبر Tinker...</div>
                    </div>
                </div>
                <div class="card-footer text-center text-muted small">
                    mazoom.online - Real Time Socket.IO
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    // جلب الـ ID من الرابط (مثلاً لو الرابط mazoom.online/test?id=5)
    const urlParams = new URLSearchParams(window.location.search);
    const event_user_id = {{ $event_user_id }}; 

    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: 'https://mazoom.online', 
        transports: ['websocket', 'polling'],
        forceTLS: true,
        path: '/socket.io',
        client: io
    });

    const statusEl = document.getElementById('connection-status');
    const container = document.getElementById('messages-container');

    if (event_user_id) {
        // الاستماع للقناة الخاصة بالـ ID الممرر فقط
        window.Echo.channel('laravel_database_chat.' + event_user_id)
        .listen('.chat_event', (data) => {
                console.log("وصلت بيانات لهذا المستخدم:", data);
                
                if(container.querySelector('.text-muted')) {
                    container.innerHTML = '';
                }

                const newMessage = document.createElement('div');
                newMessage.className = 'message-item shadow-sm';
                
                // بناء محتوى الرسالة من البيانات القادمة من broadcastWith
                newMessage.innerHTML = `
                    <strong>الرسالة المستلمة (ID: ${data.id}):</strong> 
                    <div class="mt-1">${data.msg}</div>
                    ${data.image ? `<img src="${data.image}" class="img-fluid mt-2 rounded" style="max-height:150px">` : ''}
                    <hr class="my-2">
                    <div class="d-flex justify-content-between small text-muted">
                        <span>بواسطة: ${data.user_sent}</span>
                        <span>${data.date} ${data.time}</span>
                    </div>
                `;
                
                container.prepend(newMessage);
            });
    } else {
        statusEl.innerText = "خطأ: الـ ID غير موجود في الرابط ⚠️";
    }

    // مراقبة حالة الاتصال
    window.Echo.connector.socket.on('connect', () => {
        statusEl.innerText = `متصل (مراقب للـ ID: ${event_user_id}) ✅`;
        statusEl.className = "status-online";
    });
</script>

</body>
</html>