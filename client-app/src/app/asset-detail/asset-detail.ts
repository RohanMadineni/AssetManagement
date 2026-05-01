import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { FormsModule, ReactiveFormsModule, FormGroup, FormBuilder, Validators, FormControl } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { AssetService } from '../asset-service';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { CommonModule } from '@angular/common';
import { MatInputModule } from '@angular/material/input';
import {signal} from '@angular/core';
@Component({
  selector: 'app-asset-detail',
  imports: [ReactiveFormsModule, MatFormFieldModule, MatSelectModule, CommonModule, FormsModule, MatButtonModule, MatIconModule, MatCardModule, MatInputModule],
  templateUrl: './asset-detail.html',
  styleUrl: './asset-detail.scss',
})
export class AssetDetail implements OnInit{
  
  categories = signal<any[]>([]);
  assets = signal<any[]>([]);
  filters = {
    category_id: '',
    status: '',
    asset: '',
  };
  assetForm = new FormGroup({
      param_name: new FormControl(''),
      param_value: new FormControl('')
  });
  constructor(private assetService: AssetService, private fb: FormBuilder) {};
  
  ngOnInit(): void {
    this.initForm();
    setTimeout(() => {
      this.loadCategories();
      this.loadAssets();
      
    }, 1000);
  }
  initForm(){
    this.categories.set([[]])
     this.assets.set([]);
    // this.cd.detectChanges()
  }
  loadCategories(){{
    this.assetService.getCategories().subscribe(res => {this.categories.set(res)});
    // this.cd.detectChanges();
  
  }}
  onSubmit()
  {this.assetForm.reset();}

  loadAssets(){
    const params: any = {
      ...this.filters
    };
    this.assetService.getAssets(params).subscribe(res=>{
      this.assets.set(res.data);
      console.log(res);
      console.log(this.assets);
    });
    
    // this.cd.detectChanges();
  }
}
