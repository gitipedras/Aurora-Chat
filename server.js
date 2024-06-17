// server.js
const express = require('express');
const http = require('http');
const socketIo = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

// Serve static files from the public directory
app.use(express.static('public'));

// Handle socket connections
io.on('connection', (socket) => {
  console.log('A user connected');

  // Get channel from query params
  const { channel } = socket.handshake.query;
  if (channel) {
    socket.join(channel);
    console.log(`User joined channel: ${channel}`);
  }

  // Set the username
  socket.on('set username', (username) => {
    socket.username = username;
    console.log(`Username set to ${username}`);
  });

  // Handle chat messages
  socket.on('chat message', (data) => {
    const { channel } = socket.handshake.query;
    if (channel) {
      io.to(channel).emit('chat message', data);
    }
  });

  // Handle disconnections
  socket.on('disconnect', () => {
    console.log('User disconnected');
  });
});

// Start the server
const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`);
});
