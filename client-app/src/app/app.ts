import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ToastrModule, ToastContainerDirective } from 'ngx-toastr';
// import { SocketService } from './services/socket';
// import { NotificationService } from './services/notification';
@Component({
  selector: 'app-root',
  imports: [RouterOutlet],
  templateUrl: './app.html',
  styleUrl: './app.scss'
})
export class App {
  protected readonly title = signal('client-app');
  constructor() {ToastrModule.forRoot({ positionClass: 'inline' });}
}
