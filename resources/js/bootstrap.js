import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    broadcaster: 'socket.io',
    // نستخدم العنوان الكامل لضمان عدم حدوث Redirect Loops
    host: window.location.protocol + '//' + window.location.hostname, 
    transports: ['websocket', 'polling'],
    forceTLS: true,
    // التعديل الضروري جداً:
    path: '/socket.io' 
});