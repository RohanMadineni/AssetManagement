import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
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
  constructor() {}
}
