import { Component } from '@angular/core';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatButtonModule } from '@angular/material/button';
import { MatInputModule } from '@angular/material/input';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormControl } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth-service';
// import { CommonModule } from '@angular/common';
@Component({
  selector: 'app-login',
  templateUrl: './login.html',
  styleUrls: ['./login.scss'],
  // standalone: true,
  imports: [MatCardModule, MatFormFieldModule, MatButtonModule, ReactiveFormsModule, MatInputModule],
})
export class LoginPage {
  loginform: FormGroup;

  constructor(private fb: FormBuilder,
              private authService: AuthService,
              private router: Router
              ) {
      console.log('LoginPage constructor running');
    this.loginform = this.fb.group({
      username: ['', [Validators.required, Validators.minLength(3)]],
      password: ['', [Validators.required, Validators.minLength(6)]]
    });
  }

  onLogin(): void {
    const val = this.loginform.value;
    if (this.loginform.valid) {  
      this.authService.login(val.username, val.password).subscribe({
        next: () => {
          console.log("user is logged in");
          // setTimeout(() => {
          this.router.navigateByUrl('/dashboard');
        // });
        },
        error: (err) => {
          console.error("login failed", err);
          console.error("status:", err.status);
          console.error("message:", err.message);
        }
      });
    } else {
      console.error("Form is invalid");
      
    }

  }
}