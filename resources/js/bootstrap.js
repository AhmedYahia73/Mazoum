window.Echo = new Echo({
    broadcaster: 'socket.io',
    // نستخدم العنوان الكامل مع تحديد البورت مباشرة للتأكد
    host: window.location.hostname, 
    transports: ['websocket', 'polling'],
    forceTLS: true,
    // هذا السطر مهم جداً إذا كان Nginx يوجه الطلبات
    path: '/socket.io' 
});

// أضف هذا الكود الصغير تحت Echo لمراقبة الأخطاء في الكونسول
window.Echo.connector.socket.on('connect_error', (error) => {
    console.error('Socket Connection Error:', error);
});