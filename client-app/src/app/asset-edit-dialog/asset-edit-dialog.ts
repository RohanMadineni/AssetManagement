import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogModule, MatDialogTitle, MAT_DIALOG_DATA, MatDialogContent, MatDialogRef, MatDialogActions, MatDialogClose } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { AssetService } from '../asset-service';
import { FormsModule, ReactiveFormsModule, FormGroup, FormControl } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
@Component({
  selector: 'app-asset-view-dialog',
  imports: [CommonModule, FormsModule, ReactiveFormsModule, MatFormFieldModule, MatDialogModule, MatDialogTitle, MatDialogContent, MatDialogActions, MatButtonModule, MatInputModule, MatSelectModule],
  templateUrl: './asset-edit-dialog.html',
  styleUrl: './asset-edit-dialog.scss',
})
export class AssetEditDialog implements OnInit{

  attributes = signal<any[]>([]);

  readonly data = inject<any>(MAT_DIALOG_DATA);
  readonly dialogRef = inject(MatDialogRef<AssetEditDialog>);

  editForm = new FormGroup({
    name: new FormControl(this.data.name),
    brand: new FormControl(this.data.brand),
    status: new FormControl(this.data.status),
  });

  constructor(private assetService: AssetService){}

  ngOnInit(): void {
    this.attributes.set([]);
    this.loadAttributes();
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  onSubmit(){
    const payload = {
      ...this.editForm.value,
    }
    console.log(payload);
    
    this.assetService.updateAsset(Number(this.data.id), payload).subscribe(()=>{this.dialogRef.close(true);});
    // this.editForm.reset();
  }

  loadAttributes(){

  }

}
