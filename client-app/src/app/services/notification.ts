import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
export interface Notification {
  id: number;
  title: string;
  message: string;
  type: 'info' | 'success' | 'warning' | 'error';
  is_read: number;
  created_at: string;
}

@Injectable({
  providedIn: 'root',
})

export class NotificationService {
  notifications = signal<Notification[]>([]);

  constructor(private http:HttpClient){
    this.loadNotifications();
  }

  loadNotifications() {

    this.http
      .get<Notification[]>(`http://localhost:8000/api/notifications/`)
      .subscribe(data => {

        console.log('loaded notifications', data);

        this.notifications.set(data);

      });

  }
  add(notification: Notification) {
    this.notifications.update(n => [notification, ...n]);
    
    console.log(this.notifications());
  }

  markAsRead(id: number) {
    this.notifications.update(list =>
      list.map(n =>
        n.id === id
          ? { ...n, is_read: 1 }
          : n
      )
    );
    this.http.put<any>(`http://localhost:8000/api/notifications/${id}`, {}).subscribe((res)=>{console.log(res);this.loadNotifications();});
    
  }

  unreadCount() {
    return this.notifications()
      .filter(n => !n.is_read)
      .length;
  }
}
