import { Component, inject, ChangeDetectionStrategy, viewChild } from '@angular/core';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatListModule } from '@angular/material/list';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatMenuModule } from '@angular/material/menu';
import { MatBadgeModule } from '@angular/material/badge';
import { AuthService } from '../auth-service';
import { NotificationService } from '../services/notification';
import { Router, RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';
import { signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { SocketService } from '../services/socket';
import { ToastrModule, ToastrService, ToastContainerDirective } from 'ngx-toastr';
@Component({
  selector: 'app-layout',
  imports: [MatCardModule, MatListModule, MatSidenavModule, MatTableModule, MatToolbarModule, MatIconModule, MatMenuModule, MatBadgeModule, RouterOutlet, RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './layout.html',
  styleUrl: './layout.scss',
  changeDetection: ChangeDetectionStrategy.OnPush,
})
export class LayoutPage {
  
  role = signal<string>("");
  isadmin = signal(false);
  name = signal<string>("");
  notificationService = inject(NotificationService);
  socketService = inject(SocketService);
  toastContainer = viewChild(ToastContainerDirective);
  toastr = inject(ToastrService)!;
  showSuccess() {
    this.toastr.success('Hello world!', 'Toastr fun!');
}
  
  constructor(private authService: AuthService, private router: Router, private http:HttpClient){
   
    this.authService.getRole().subscribe(
      res => {
        console.log(res);
        console.log(this.notificationService.notifications());
        this.role.set(res.role);
        this.name.set(res.username);
        if (res) {
        this.isAdmin();   
      }
      }
    );
    this.toastr.overlayContainer = this.toastContainer();
    this.notificationService.loadNotifications();
  }

  isAdmin(){
    console.log(this.role());
    if(this.role()=='admin') this.isadmin.set(true);
    
  }

  onLogout(): void{
    console.log(this.authService.logout());
    this.router.navigateByUrl('/login');
  }

  onRead(id:number){
    this.notificationService.markAsRead(id);
  
  }
  markallRead(){
    this.notificationService.notifications().forEach(n => {this.onRead(n.id)});
  }
  // onClick() {
  //   this.toastr.success('in div');
  // }
}
