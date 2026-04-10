// import Echo from 'laravel-echo';
// import io from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.protocol + '//' + window.location.hostname, 
    transports: ['websocket', 'polling'], // تحديد وسيلة النقل لضمان التوافقية
    forceTLS: true, 
});