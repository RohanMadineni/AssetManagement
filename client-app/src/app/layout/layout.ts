import { Component } from '@angular/core';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';
import { MatListModule } from '@angular/material/list';
import { MatTableModule } from '@angular/material/table';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { AuthService } from '../auth-service';
import { Router, RouterOutlet, RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';
import { signal } from '@angular/core';
// import { User } from '../user';
@Component({
  selector: 'app-layout',
  imports: [MatCardModule, MatListModule, MatSidenavModule, MatTableModule, MatToolbarModule, MatIconModule, RouterOutlet, RouterLink, RouterLinkActive, CommonModule],
  templateUrl: './layout.html',
  styleUrl: './layout.scss',
})
export class LayoutPage {
  
  role: string = "";
  isadmin = signal(false);
  name: string = "";
  constructor(private authService: AuthService, private router: Router){
   
    this.authService.getRole().subscribe(
      res => {
        console.log(res);
        this.role=res.role;
        this.name=res.username;
        this.isAdmin();
      }
    );
    
  }

  isAdmin(){
    console.log(this.role);
    if(this.role=='admin') this.isadmin.set(true);
    
  }
 onLogout(): void{
  console.log(this.authService.logout());
  this.router.navigateByUrl('/login');
 }
 
}
