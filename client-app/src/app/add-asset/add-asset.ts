// import { Component } from '@angular/core';
// import { MatCardModule } from '@angular/material/card';
// import { MatFormFieldModule } from '@angular/material/form-field';
// @Component({
//   selector: 'app-add-asset',
//   imports: [],
//   templateUrl: './add-asset.html',
//   styleUrl: './add-asset.css',
// })
// export class AddAsset {

// }

import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { MatCardModule } from '@angular/material/card';

import { AssetService } from '../asset-service';

@Component({
  selector: 'app-add-asset',
  standalone: true,
  templateUrl: './add-asset.html',
  styleUrls: ['./add-asset.scss'],
  imports: [
    CommonModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatSelectModule,
    MatCardModule,
    ReactiveFormsModule
  ]
})
export class AddAssetComponent implements OnInit {

  assetForm!: FormGroup;
  categories: any[] = [];
  parameters: any[] = [];

  constructor(
    private fb: FormBuilder,
    private assetService: AssetService
  ) {}

  ngOnInit(): void {
    this.initForm();
    this.loadCategories();
  }

  initForm() {
    this.assetForm = this.fb.group({
      name: ['', Validators.required],
      category_id: ['', Validators.required],
      status: ['available', Validators.required],
      attributes: this.fb.array([])
    });
  }

  get attributes(): FormArray {
    return this.assetForm.get('attributes') as FormArray;
  }

  loadCategories() {
    this.assetService.getCategories().subscribe(res => {
      this.categories = res;
    });
  }

  onCategoryChange(categoryId: number) {
    this.assetService.getCategoryParameters(categoryId).subscribe(res => {
    //   console.log(res);
    //   console.log('FULL RESPONSE:', res);
    // console.log('TYPE:', typeof res);
    // console.log('IS ARRAY:', Array.isArray(res));

      const params = res.pendingAttributes; 
      this.parameters = params;

      this.attributes.clear();

      params.forEach((param: any) => {
        this.attributes.push(
          this.fb.group({
            parameter_id: [param.id],
            value: ['', param.is_required ? Validators.required : []]
          })
        );
      });
    });
  }

  onSubmit() {
    if (this.assetForm.invalid) return;

    const payload = this.transformPayload();
    console.log(payload);
    this.assetService.createAsset(payload).subscribe({next: (res) => {
      console.log('Asset created', res);
       this.assetForm.reset();
       this.attributes.clear();
    }});
  }

  transformPayload() {
    const formValue = this.assetForm.value;

    const attributes: any = {};

    formValue.attributes.forEach((attr: any) => {
      attributes[attr.parameter_id] = attr.value;
    });

    return {
      name: formValue.name,
      category_id: formValue.category_id,
      status: formValue.status,
      attributes
    };
  }
}