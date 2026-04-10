const server = require('http').createServer();
const io = require('socket.io')(server, {
    cors: { 
        origin: "*", // في الإنتاج يفضل وضع https://mazoom.online
        methods: ["GET", "POST"]
    },
    path: '/socket.io' // ضروري جداً ليتوافق مع إعدادات Nginx
});

const Redis = require('ioredis');
const redis = new Redis(); 

redis.psubscribe('*', (err, count) => {
    if (err) {
        console.error('Redis subscription error:', err);
    } else {
        console.log(`Subscribed to ${count} channels. Listening for updates...`);
    }
});

redis.on('pmessage', (pattern, channel, message) => {
    console.log('Message Received from Channel: ' + channel);
    console.log('--- وصل شيء من Redis! ---');
    console.log('القناة:', channel);
    console.log('البيانات:', message);
    try {
        const parsedMessage = JSON.parse(message);
        io.to(channel).emit(parsedMessage.event, parsedMessage.data);
        
        io.emit(channel, parsedMessage.data); 
        
        console.log('Event Emitted:', parsedMessage.event);
    } catch (e) {
        console.error('Error parsing Redis message:', e);
    }
});

const PORT = 3001; 
server.listen(PORT, () => {
    console.log(`Socket server is running on port ${PORT}`);
});