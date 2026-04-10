const express = require('express');
const app = express();
const server = require('http').createServer(app);
const io = require('socket.io')(server, {
    cors: { origin: "*" } 
});
const Redis = require('ioredis');
 
const redis = new Redis({
    host: '127.0.0.1',
    port: 6379,
});
 
redis.psubscribe('*', (err, count) => {
    if (err) console.error('Redis Subscribe Error:', err);
    console.log(`Subscribed to ${count} Redis channels`);
});


redis.on('pmessage', (pattern, channel, message) => {
    console.log(`Message received on channel: ${channel}`);
    const parsedMessage = JSON.parse(message);
    const eventName = parsedMessage.event;
    const eventData = parsedMessage.data;
    io.emit(`${channel}:${eventName}`, eventData);
});

server.listen(3000, () => {
    console.log('Socket.IO Server is running on port 3000');
});