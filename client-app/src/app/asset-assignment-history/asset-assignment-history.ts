import { Component, inject, signal, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MAT_DIALOG_DATA, MatDialogRef, MatDialogTitle } from '@angular/material/dialog';
import { MatTableModule } from '@angular/material/table';
// import { MatIcon } from '@angular/material/icon';
import { MatPaginator } from '@angular/material/paginator';
import { AssetService } from '../asset-service';

@Component({
  selector: 'app-asset-assignment-history',
  imports: [MatTableModule, MatDialogTitle, MatPaginator, CommonModule],
  templateUrl: './asset-assignment-history.html',
  styleUrl: './asset-assignment-history.scss',
})
export class AssetAssignmentHistory implements OnInit {

  assetHistory = signal<any[]>([]);
  page = signal(1);
  total = signal(0);
  readonly data = inject<any>(MAT_DIALOG_DATA);
  readonly dialogRef = inject(MatDialogRef<AssetAssignmentHistory>);
  constructor(private assetService: AssetService){};
  
  ngOnInit(): void {
      
      this.loadAssets();
      
  }

  loadAssets(){
    const params: any = {
        page: (this.page()),
        asset_id: this.data.asset_id,
    };
    this.assetService.getAssetHistory(params).subscribe(res => {
        console.log(res);
        this.assetHistory.set(res.data);
        this.total.set(res.total);
      });
  }

  onNoClick(): void {
    this.dialogRef.close();
  }
  
  onPageChange(event: any) {
    this.page.set(event.pageIndex + 1);
    this.loadAssets();
  }
}
