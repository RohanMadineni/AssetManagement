import { Injectable, signal } from '@angular/core';
import { io, Socket } from 'socket.io-client';
import { NotificationService } from './notification';
import { environment } from '../../environment';
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
    
    // this.socket = io('http://localhost:3000', {query: {user_id: this.currentUser().id}});
    // this.socket = io('http://realtime-server:3000', {query: {user_id: this.currentUser().id}});
    this.socket = io(`${environment.socketUrl}`, {query: {user_id: this.currentUser().id}});
    // this.socket = io({query: {user_id: this.currentUser().id}});
    this.socket.on('notification', (body) => {
      console.log(body);
      this.notificationService.add({...body});
    });
    
  }

 
  
}
