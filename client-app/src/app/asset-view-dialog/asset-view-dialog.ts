import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatDialogModule, MatDialogTitle, MAT_DIALOG_DATA, MatDialogContent, MatDialogRef, MatDialogActions } from '@angular/material/dialog';
import { MatButtonModule } from '@angular/material/button';
import { AssetService } from '../asset-service';
@Component({
  selector: 'app-asset-view-dialog',
  imports: [CommonModule, MatDialogModule, MatDialogTitle, MatDialogContent, MatDialogActions, MatButtonModule],
  templateUrl: './asset-view-dialog.html',
  styleUrl: './asset-view-dialog.scss',
})
export class AssetViewDialog implements OnInit{

  attributes = signal<any[]>([]);

  readonly data = inject<any>(MAT_DIALOG_DATA);
  readonly dialogRef = inject(MatDialogRef<AssetViewDialog>);

  constructor(private assetService: AssetService){}

  ngOnInit(){
    this.attributes.set([]);
    this.loadAttributes();
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  loadAttributes(){
    
  }
}
