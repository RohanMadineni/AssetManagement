import { Component, signal, OnInit } from '@angular/core';
import { MatFormFieldModule } from '@angular/material/form-field';
import { FormGroup, FormControl, ReactiveFormsModule } from '@angular/forms';

import { UserService } from '../user-service';
@Component({
  selector: 'app-settings',
  imports: [ReactiveFormsModule, MatFormFieldModule],
  templateUrl: './settings.html',
  styleUrl: './settings.scss',
})
export class Settings implements OnInit {
  
  profileForm = new FormGroup({
    Name: new FormControl(''),
    Email: new FormControl(''),
    // Department: new FormControl(''),
  });
  passwordForm = new FormGroup({
    CurrentPassword: new FormControl(''),
    NewPassword: new FormControl(''),
    ConfirmPassword: new FormControl(''),
  });
  ngOnInit(): void {
      this.userService.getUserProfile().subscribe((data)=>{
        this.profileForm.patchValue({
        Name: data.username,
        Email: data.email
      });
        // this.profileForm.value.Department = data.department;
      });
  }

  constructor(private userService: UserService){}

  updateProfile(){
    const value = {
      ...this.profileForm.value
    };
    if(this.profileForm.valid){
      this.userService.updateUserProfile(value).subscribe((data)=>{console.log(data);});
      this.profileForm.reset();
    }
    else {
      console.error("Form is invalid");
    }
  }

  updatePassword(){
    if (this.passwordForm.invalid) {
      console.error("Form invalid");
      return;
    }

    const formValue = this.passwordForm.value;

    // Check confirm password
    if (formValue.NewPassword !== formValue.ConfirmPassword) {
      console.error("Passwords do not match");
      return;
    }

    this.userService.setUserPassword({
      current_password: formValue.CurrentPassword,
      new_password: formValue.NewPassword
    }).subscribe({

      next: (response) => {
        console.log("Password updated successfully");
        this.passwordForm.reset();
      },

      error: (err) => {
        console.log(err);
        // console.error(err.error.message);
      }

    });
  }
}
