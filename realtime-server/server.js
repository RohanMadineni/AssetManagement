import express from 'express';
import { createServer } from 'node:http';
import { Server } from 'socket.io';


const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: { origin: '*' } // Allow CORS for development
});
io.on('connection', (socket) => {
//   socket.broadcast.emit('hi');
  console.log('hi');
  socket.join(socket.handshake.query.user_id);
  console.log(socket.handshake.query.user_id);
});

app.use(express.json());

app.post('/AssetCreated', (req, res)=>{
    const body = req.body;

    io.to(`${body.user_id}`).emit('notification', body);
    
    res.json({
        success: true,
        notification: body
    });

});

app.post('/AssetDeleted', (req, res)=>{
    const body = req.body;

    io.to(`${body.user_id}`).emit('notification', body);
    
    res.json({
        success: true,
        notification: body
    });

});

app.post('/AssetAssigned', (req, res)=>{
  const body = req.body;
    
    io.to(`${body.user_id}`).emit('notification', body);
    console.log(body);
    res.json({
        success: true,
        notification: body
    });
})

app.post('/AssetReturned', (req, res)=>{
  const body = req.body;
//   console.log(req.body);

  io.to(`${body.user_id}`).emit('notification', body);

  res.json({
      success: true,
      notification: body
  });
})

app.post('/UserCreated', (req, res)=>{
    const body = req.body;

    io.to(`${body.user_id}`).emit('notification', body);
    
    res.json({
        success: true,
        notification: body
    });

});

app.post('/UserDeleted', (req, res)=>{
    const body = req.body;

    io.to(`${body.user_id}`).emit('notification', body);
    
    res.json({
        success: true,
        notification: body
    });

});
server.listen(3000);