
import { Component } from '@angular/core';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatButtonModule } from '@angular/material/button';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { FormBuilder, FormGroup, ReactiveFormsModule, Validators, FormControl } from '@angular/forms';
import { Router } from '@angular/router';
import { AuthService } from '../auth-service';
// import { CommonModule } from '@angular/common';

interface loginform{
    username?: string | null | undefined,
    password?: string | null | undefined,
    role?: string | null | undefined,
}

@Component({
  selector: 'app-register',
  templateUrl: './register.html',
  styleUrls: ['./register.scss'],
  standalone: true,
  imports: [MatCardModule, MatFormFieldModule, MatButtonModule, ReactiveFormsModule, MatInputModule, MatSelectModule],
})
export class RegisterPage {

//loginform: FormGroup;
  loginform = new FormGroup({
    email: new FormControl(''),
    username: new FormControl(''),
    password: new FormControl(''),
    role: new FormControl(''),
  });

  constructor(private fb: FormBuilder,
              private authService: AuthService,
              private router: Router

              ) {
      console.log('RegisterPage constructor running');
    // this.loginform = this.fb.group({
    //   username: ['', [Validators.required, Validators.minLength(3)]],
    //   password: ['', [Validators.required, Validators.minLength(6)]],
    //   role: ['', [Validators.required]]
    // });
  }

  onLogin(): void {
    
    const val: loginform = this.loginform.value;

    if (this.loginform.valid) {  
      this.authService.register(val.username, val.password, val.role).subscribe({
        next: () => {
          console.log("user is registered");
          // this.router.navigateByUrl('/login');
        },
        error: (err) => {
          console.error("register failed", err);
          console.error("status:", err.status);
          console.error("message:", err.message);
        }
      });
      
      this.loginform.reset(); 
    } else {
      console.error("Form is invalid");
      
    }

  }
}