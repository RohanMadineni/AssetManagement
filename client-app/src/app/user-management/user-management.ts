import { Component, signal, OnInit } from '@angular/core';
import { MatCardModule } from '@angular/material/card';
import { MatTableModule } from '@angular/material/table';
// import { MatPaginator } from '@angular/material/paginator';
import { MatButtonModule } from '@angular/material/button';
import { MatIcon } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router } from '@angular/router';

import { UserService } from '../user-service';
import { UserEditDialog } from '../user-edit-dialog/user-edit-dialog';

import { AddAssetComponent } from '../add-asset/add-asset';
import { AssetListPage } from '../asset-list/asset-list';

import { ExportServiceTs } from '../export-service';
@Component({
  selector: 'app-user-management',
  imports: [MatCardModule, MatTableModule, MatButtonModule, MatIcon, CommonModule, ReactiveFormsModule, AddAssetComponent, AssetListPage],
  templateUrl: './user-management.html',
  styleUrl: './user-management.scss',
})
export class UserManagement implements OnInit{

  users = signal<any[]>([]);
  displayedColumns: string[] = ['id', 'Name', 'Role', 'Actions', 'add'];
  page = signal(1);
  total = signal(0);
  view = signal<'user-page'|'asset-page'|'assetlist-page'>('user-page');
  selectedUser = signal<any|null>(null);
  selectedName = signal<any|null>(null);
  ngOnInit(): void {
    this.initTable();
  }


  constructor(private userservice: UserService, private exportService: ExportServiceTs, private router: Router, private dialog: MatDialog){};

  initTable(){
    this.userservice.getUsers().subscribe(res => {
      // setTimeout(()=>{
      console.log(res);
      this.users.set(res);
      this.total.set(res.length);
      console.log(this.total());
      // })
    });
  }

  // onPageChange(event: any) {
  //   this.page.set(event.pageIndex + 1);
  //   this.initTable();
  // }

  deleteUser(id: any) {
    this.userservice.deleteUser(id).subscribe({
    next: () => this.initTable(),
    error: err => console.log(err)
  });
  }

  NewUser(): void{
    this.router.navigateByUrl('/register');
  }
  editUser(edit_User: any){
      const dialogRef = this.dialog.open(UserEditDialog, {data: edit_User, height: '380px', width: '370px'});
  
      dialogRef.afterClosed().subscribe(result => {
        if (result) {
          this.initTable();   
        }
      });
  }
  addAsset(user_:any, user_name: any){
    this.selectedUser.set(user_);
    this.selectedName.set(user_name);
    this.view.set('asset-page');
  }
  viewAssets(user_:number, user_name: any){
    this.selectedUser.set(user_);
    this.selectedName.set(user_name);
    this.view.set('assetlist-page');
  }
  exportUsers() {
    this.exportService.export_user_list().subscribe((blob: Blob) => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'user-list.xlsx';
      a.click();
      window.URL.revokeObjectURL(url);
    });
  }
  exportUsersPdf() {
    this.exportService.export_user_list_pdf().subscribe((blob: Blob) => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'user-list.pdf';
      a.click();
      window.URL.revokeObjectURL(url);
    });
  }
}
