import { Injectable, signal } from '@angular/core';
import { io, Socket } from 'socket.io-client';
import { NotificationService } from './notification';
// import { HttpClient } from '@angular/common/http';
@Injectable({
  providedIn: 'root',
})
export class SocketService {

  
  currentUser = signal<any>(null);
  private socket!: Socket;
  constructor(private notificationService: NotificationService){
    
    const user = localStorage.getItem('user');
    if (user) {
              this.currentUser.set(JSON.parse(user));
    }   
    // console.log(this.currentUser());
    this.socket = io('http://localhost:3000', {query: {user_id: this.currentUser().id}});
    // this.socket.on('connection', ()=>{
    //   this.notificationService.loadNotifications();
    // })
    this.socket.on('notification', (body) => {
      console.log(body);
      this.notificationService.add({...body});
      // this.notificationService.add({
      //   id: data.id,
      //   title: data.title,
      //   message: data.message,
      //   type: data.type,
      //   is_read: 0,
      //   created_at: data.created_at
      // });

    });
    
  }

 
  
}
