import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogModule, MatDialogTitle, MAT_DIALOG_DATA, MatDialogContent, MatDialogRef, MatDialogActions} from '@angular/material/dialog';
import { FormsModule, ReactiveFormsModule, FormGroup, FormControl, Validators } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { AssetService } from '../asset-service';
import { UserService } from '../user-service';
@Component({
  selector: 'app-assign-asset-dialog',
  imports: [MatDialogModule, FormsModule, MatFormFieldModule, MatButtonModule, ReactiveFormsModule, MatSelectModule, MatDialogTitle, MatDialogContent, CommonModule],
  templateUrl: './assign-asset-dialog.html',
  styleUrl: './assign-asset-dialog.scss',
})
export class AssignAssetDialog implements OnInit{
  
  users = signal<any[]>([]);

  readonly data = inject<any>(MAT_DIALOG_DATA);
  readonly dialogRef = inject(MatDialogRef<AssignAssetDialog>);

  assignForm = new FormGroup({
    user_id: new FormControl('', Validators.required),
    // username: new FormControl('', Validators.required),
  });
  constructor(private assetService: AssetService, private userService: UserService){}

  ngOnInit(): void {
    this.loadUsers();
  }

  loadUsers(){
    this.userService.getUsers().subscribe(res => {
      this.users.set(res);
    });
  }
  onSubmit(){
    const userId = this.assignForm.value.user_id;
    const find = this.users().find(user=>user.id===userId);
    const uname = find.username;
    console.log(uname);
    this.assetService.assignAsset({asset_id: this.data.asset_id, user_id:this.assignForm.value.user_id, username: uname}).subscribe(()=>{
      this.dialogRef.close("closed");
    });
  }
}
