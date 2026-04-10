<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>اختبار Real-Time Socket.io</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; padding: 50px; }
        #messages-container { height: 300px; overflow-y: auto; background: white; border-radius: 8px; padding: 15px; border: 1px solid #ddd; }
        .message-item { background: #e9ecef; border-radius: 5px; padding: 10px; margin-bottom: 10px; border-right: 5px solid #007bff; text-align: right; }
        .status-online { color: green; font-weight: bold; }
        .status-offline { color: red; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="card shadow">
        <div class="card-header bg-dark text-white text-center">
            <h5 class="mb-0">نظام استقبال الرسائل (ChatEvent)</h5>
        </div>
        <div class="card-body">
            <p>حالة الاتصال بالسيرفر: <span id="connection-status" class="status-offline">جاري الاتصال...</span></p>
            <hr>
            <div id="messages-container">
                <div class="text-muted text-center">في انتظار وصول رسائل عبر Tinker...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
    // إعداد Laravel Echo
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':3000', 
        transports: ['websocket', 'polling'] 
    });

    const statusEl = document.getElementById('connection-status');
    const container = document.getElementById('messages-container');
    
    window.Echo.connector.socket.on('connect', () => {
        statusEl.innerText = "متصل بنجاح ✅";
        statusEl.className = "status-online";
    });

    window.Echo.connector.socket.on('disconnect', () => {
        statusEl.innerText = "انقطع الاتصال ❌";
        statusEl.className = "status-offline";
    });

    // التعديل هنا ليتناسب مع الكود الخاص بك:
    // 1. اسم القناة: 'ChatEvent' (مع إضافة Prefix لارافيل الافتراضي)
    // 2. اسم الحدث: '.chat_event' (النقطة ضرورية لأنك استخدمت broadcastAs)
    window.Echo.channel('laravel_database_ChatEvent')
        .listen('.chat_event', (data) => {
            console.log("وصلت بيانات:", data);
            
            if(container.querySelector('.text-muted')) container.innerHTML = '';

            const newMessage = document.createElement('div');
            newMessage.className = 'message-item';
            
            // تأكد أن 'message' هو اسم المتغير العام في الـ Event الخاص بك
            const content = data.message ? data.message : JSON.stringify(data);
            
            newMessage.innerHTML = `<strong>الرسالة المستلمة:</strong> ${content} <br> <small class="text-secondary">${new Date().toLocaleTimeString()}</small>`;
            container.prepend(newMessage);
        });
</script>

</body>
</html>