import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogModule, MatDialogTitle, MAT_DIALOG_DATA, MatDialogContent, MatDialogRef, MatDialogActions, MatDialogClose } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
// import { AssetService } from '../asset-service';
import { UserService } from '../user-service';
import { FormsModule, ReactiveFormsModule, FormGroup, FormControl } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
@Component({
  selector: 'app-user-edit-dialog',
  imports: [CommonModule, FormsModule, ReactiveFormsModule, MatFormFieldModule, MatDialogModule, MatDialogTitle, MatDialogContent, MatDialogActions, MatButtonModule, MatInputModule, MatSelectModule],
  templateUrl: './user-edit-dialog.html',
  styleUrl: './user-edit-dialog.scss',
})
export class UserEditDialog {
  // parameters = signal<any[]>([]);

  readonly data = inject<any>(MAT_DIALOG_DATA);
  readonly dialogRef = inject(MatDialogRef<UserEditDialog>);

  editForm = new FormGroup({
    username: new FormControl(this.data.username),
    role: new FormControl(this.data.role),
    // status: new FormControl(this.data.status),

    // attributesControls: new FormGroup({
    //   parameterID: new FormControl<number|null>(null),
    //   value: new FormControl<any|null>(null), 
    //   }
    // )

  });

  constructor(private userService: UserService){}

  ngOnInit(): void {
    // this.attributes.set([]);
    // this.parameters.set([]);
    // this.loadAttributes();
    // this.loadParameters();
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  onSubmit(){
    // const {attributesControls, ...form} = this.editForm.value;
    const payload:any = { 
      ...this.editForm.value
    };
    // if(attributesControls?.parameterID){
    //   payload.attributes = {
    //     [attributesControls.parameterID]: attributesControls.value
    //   }
    // }
  
    console.log(payload);
    
    this.userService.updateUser(Number(this.data.id), payload).subscribe(res=>{console.log(res);this.dialogRef.close(true);});
  }

  // loadAttributes(){

  // }
  
  // loadParameters(){
  //   this.userService.getCategoryParameters(this.data.category_id).subscribe(res => {this.parameters.set(res)});
  // }

  

}
