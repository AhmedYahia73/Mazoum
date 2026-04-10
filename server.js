const server = require('http').createServer();
const io = require('socket.io')(server, {
    cors: { origin: "*" }
});
const Redis = require('ioredis');
const redis = new Redis(); // يفترض أن Redis يعمل على نفس السيرفر بورت 6379

redis.psubscribe('*', (err, count) => {
    console.log('Subscribed to all channels');
});

redis.on('pmessage', (pattern, channel, message) => {
    console.log('Message Received from: ' + channel);
    message = JSON.parse(message);
    // إرسال البيانات للمتصفح (تأكد من اسم الحدث)
    io.emit(channel + ':' + message.event, message.data);
});

server.listen(3001, () => {
    console.log('Socket server is running on port 3001');
});