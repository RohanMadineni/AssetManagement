import { Component, OnInit, NgModule, ChangeDetectorRef } from '@angular/core';
// import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; // 🔥 for ngModel

// Angular Material
import { MatTableModule } from '@angular/material/table';
import { MatPaginatorModule } from '@angular/material/paginator';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { AssetService } from '../asset-service';
import { MatSelectModule } from '@angular/material/select';
import { MatIconModule } from '@angular/material/icon';
import { signal } from '@angular/core';
import { MatCardModule } from '@angular/material/card';
import { MatDialog, MatDialogRef } from '@angular/material/dialog';
import { AssetViewDialog } from '../asset-view-dialog/asset-view-dialog';
import { AssetEditDialog } from '../asset-edit-dialog/asset-edit-dialog';
@Component({
  selector: 'app-asset-list',
  imports: [MatTableModule, MatButtonModule, MatFormFieldModule, MatInputModule, MatPaginatorModule, FormsModule, CommonModule, MatSelectModule, MatIconModule, MatCardModule],
  templateUrl: './asset-list.html',
  styleUrl: './asset-list.scss',
})
// export class AssetListPage {
//   constructor(private assetService: AssetService){}
  
// }
export class AssetListPage implements OnInit {
  assets= signal<any[]>([]);
  total = signal(0);
  page = signal(1);
  categories = signal<any[]>([]);
  filters = {
    category_id: '',
    status: ''
  };

  displayedColumns: string[] = ['name', 'actions'];

  constructor(private assetService: AssetService, private dialog: MatDialog) {}

  ngOnInit() {
    this.loadAssets();
    this.loadCategories();
  }
  
  loadAssets() {
    const params: any = {
      page: (this.page()),
      ...this.filters
    };
    console.log(params);
    this.assetService.getAssets(params).subscribe(res => {
      setTimeout(()=>{
        this.assets.set(res.data);
      this.total.set(res.total);
      // this.cdr.detectChanges();
      });
    });
  }
  loadCategories() {
    this.assetService.getCategories().subscribe(res => {
      this.categories.set(res);
    });
  }
  onPageChange(event: any) {
    this.page.set(event.pageIndex + 1);
    this.loadAssets();
  }

  onFilter() {
    this.page.set(1);
    this.loadAssets();
  }
 
  deleteAsset(id: any) {
    this.assetService.deleteAsset(id).subscribe(() => {
      this.loadAssets(); // 🔥 refresh list after delete    
    });
  }

  viewAsset(view_Asset:any){
    this.dialog.open(AssetViewDialog, {data: view_Asset, height: '350px', width: '350px'});
  }

  editAsset(edit_Asset: any){
    const dialogRef = this.dialog.open(AssetEditDialog, {data: edit_Asset, height: '380px', width: '370px'});

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadAssets();   // refresh table
      }
    });
  }
  
}