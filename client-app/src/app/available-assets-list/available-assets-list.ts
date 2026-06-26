import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
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
import { AssignAssetDialog } from '../assign-asset-dialog/assign-asset-dialog';
import { AssetAssignmentHistory } from '../asset-assignment-history/asset-assignment-history';
@Component({
  selector: 'app-available-assets-list',
  imports: [MatTableModule, MatButtonModule, MatFormFieldModule, MatInputModule, MatPaginatorModule, FormsModule, CommonModule, MatSelectModule, MatIconModule, MatCardModule],
  templateUrl: './available-assets-list.html',
  styleUrl: './available-assets-list.scss',
})
export class AvailableAssetsList {
  assets= signal<any[]>([]);
  total = signal(0);
  page = signal(1);
  categories = signal<any[]>([]);
  filters = {
    category_id: '',
    status: ''
  };
  searchterm = "";
  @Input() user:any;
  @Input() use_name:any;
  @Output() back = new EventEmitter<void>();
  displayedColumns: string[] = ['name', 'actions'];

  constructor(private assetService: AssetService, private dialog: MatDialog) {}

  ngOnInit() {
    this.loadAssets();
    this.loadCategories();
    // this.onSearch();
  }
  
  loadAssets() {
    const params: any = {
      page: (this.page()),
      selected_user: this.user?this.user:0,
      ...this.filters
    };
    // console.log(params);
    // this.assetService.getAssets(params).subscribe(res => {
    //   setTimeout(()=>{
    //     console.log(res);
    //     this.assets.set(res.data);
    //     this.total.set(res.total);
    //   });
    // });
    this.assetService.getAllAssets(params).subscribe(res => {
      setTimeout(()=>{
        this.assets.set(res.data);
        console.log(this.assets());
        this.total.set(res.total);
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
      this.loadAssets(); 
    });
  }

  viewAsset(view_Asset:any){
    this.dialog.open(AssetViewDialog, {data: view_Asset, height: '350px', width: '350px'});
  }

  editAsset(edit_Asset: any){
    const dialogRef = this.dialog.open(AssetEditDialog, {data: edit_Asset, height: '730px', width: '700px'});

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.loadAssets();   
      }
    });
  }
  returnAsset(asset_id: any){
    this.assetService.returnAsset(asset_id).subscribe(()=>{
      this.loadAssets();
    });
  }
  assignAsset(asset: any){

    // this.assetService.assignAsset({asset_id: asset_id, user_id:this.user}).subscribe(()=>{
    //   this.loadAssets();
    // });
    const dialogRef = this.dialog.open(AssignAssetDialog, {data: {asset_id: asset.id, name: asset.name}, height: '350px', width: '350px'});

    dialogRef.afterClosed().subscribe(result=> {
      if (result) {
        this.loadAssets();   
      }
    });
  }
  assetHistory(asset: any){
    const dialogRef = this.dialog.open(AssetAssignmentHistory, {data: {asset_id: asset.id, name: asset.name}, height: '700px', width: '900px'});
    dialogRef.afterClosed().subscribe();
  }

  onSearch() {
    if(this.searchterm){
      this.assetService.searchAssets(this.searchterm)
        .subscribe(results => {
            // this.assets = results;
            const formattedResults = results.map((item:any)=>({
                id:item._id, ...item._source
              })
            );
            console.log(results);
            this.assets.set(formattedResults);
            this.total.set(formattedResults.length);
        });
    }
    else this.loadAssets();
  }
}
