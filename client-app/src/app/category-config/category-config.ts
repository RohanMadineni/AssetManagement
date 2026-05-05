import { Component, OnInit } from '@angular/core';
import { FormsModule, ReactiveFormsModule, FormGroup, FormBuilder, FormControl } from '@angular/forms';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatCardModule } from '@angular/material/card';
import { CommonModule } from '@angular/common';
import { MatInputModule } from '@angular/material/input';
import {signal} from '@angular/core';
import { AssetService } from '../asset-service';


// interface categoryForm{
//     name?: string | null | undefined,
//     data_type?: string | null | undefined,
//     is_required?: boolean | null | undefined,
// }

@Component({
  selector: 'app-category-config',
  imports: [ReactiveFormsModule, MatFormFieldModule, MatSelectModule, CommonModule, FormsModule, MatButtonModule, MatIconModule, MatCardModule, MatInputModule],
  templateUrl: './category-config.html',
  styleUrl: './category-config.scss',
  // standalone: true,
})
export class CategoryConfig implements OnInit{
  categories = signal<any[]>([]);
  parameters = signal<any[]>([]);
  createcatForm = new FormGroup({
    name: new FormControl(''),
    description: new FormControl(''),
  });
  categoryForm = new FormGroup({
    name: new FormControl(''),
    data_type: new FormControl(''),
    is_required: new FormControl<boolean|null>(null),
    category_id : new FormControl(<number>0),
    param_id : new FormControl(<number>0)
  });
  editForm = new FormGroup({
    name: new FormControl(''),
    data_type: new FormControl(''),
    is_required: new FormControl<boolean|null>(null),
    edit_category_id: new FormControl(0),
    edit_param_id: new FormControl(0),
  });
  constructor(private assetService: AssetService, private fb: FormBuilder){};

  ngOnInit(): void {
    this.initForm();
    setTimeout(() => {
      this.loadCategories();
    }, 1000); 
  }

  initForm(){
      this.categories.set([]);
      this.parameters.set([]);
  }

  loadCategories(){
    this.assetService.getCategories().subscribe(res => {this.categories.set(res)});
  }
  loadParameters(){
    this.assetService.getCategoryParameters(<number>this.editForm.value.edit_category_id).subscribe(res => {this.parameters.set(res), console.log(res)});
  }
  onSubmit(mode: 'create' | 'edit' | 'createCat'){
    if(mode==='create'){

      const payload = {
        ...this.categoryForm.value,
        is_required: this.categoryForm.value.is_required === true
      };
      this.assetService.createParam(Number(this.categoryForm.value.category_id), payload).subscribe();
      this.categoryForm.reset();
    }

    if(mode==='edit'){
      const payload = {
        ...this.editForm.value,
        is_required: this.editForm.value.is_required === true
      };
      console.log(payload);
      this.assetService.updateParam(Number(this.editForm.value.edit_param_id), payload).subscribe();
      
      this.editForm.reset();
      // this.initForm();
    }

    if(mode==='createCat'){
      const payload = {
        ...this.createcatForm.value,
      };
      console.log(payload);
      this.assetService.createCategory(payload).subscribe();
      this.createcatForm.reset();
    }
  }
  
  onParamChange() {
    const param = this.parameters().find(p => p.id == this.editForm.value.edit_param_id);
    console.log(param);
    this.editForm.patchValue({name: param.name}); 
  }
}
