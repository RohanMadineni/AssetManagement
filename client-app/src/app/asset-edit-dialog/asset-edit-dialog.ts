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

  // attributes = signal<any[]>([]);
  parameters = signal<any[]>([]);

  readonly data = inject<any>(MAT_DIALOG_DATA);
  readonly dialogRef = inject(MatDialogRef<AssetEditDialog>);

  editForm = new FormGroup({
    name: new FormControl(this.data.name),
    brand: new FormControl(this.data.brand),
    status: new FormControl(this.data.status),
    price: new FormControl(this.data.price), 
    Warranty: new FormControl(this.data.Warranty),
    attributesControls: new FormGroup({
      parameterID: new FormControl<number|null>(null),
      value: new FormControl<any|null>(null), 
      }
    )

  });

  constructor(private assetService: AssetService){}

  ngOnInit(): void {
    // this.attributes.set([]);
    this.parameters.set([]);
    // this.loadAttributes();
    this.loadParameters();
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  onSubmit(){
    const {attributesControls, ...form} = this.editForm.value;
    const payload:any = { 
      ...form
    };
    if(attributesControls?.parameterID){
      payload.attributes = {
        [attributesControls.parameterID]: attributesControls.value
      }
    }
  
    console.log(payload);
    
    this.assetService.updateAsset(Number(this.data.id), payload).subscribe(res=>{console.log(res);this.dialogRef.close(true);});
  }

  // loadAttributes(){

  // }
  
  loadParameters(){
    this.assetService.getCategoryParameters(this.data.category_id).subscribe(res => {this.parameters.set(res)});
  }

}
