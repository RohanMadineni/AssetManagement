import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { FormBuilder, FormGroup, Validators, FormArray, ReactiveFormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatButtonModule } from '@angular/material/button';
import { MatSelectModule } from '@angular/material/select';
import { MatCardModule } from '@angular/material/card';
import { MatIcon } from '@angular/material/icon';
import { AssetService } from '../asset-service';

@Component({
  selector: 'app-add-asset',
  templateUrl: './add-asset.html',
  styleUrl: './add-asset.scss',
  imports: [
    CommonModule,
    MatFormFieldModule,
    MatInputModule,
    MatButtonModule,
    MatSelectModule,
    MatCardModule,
    MatIcon,
    ReactiveFormsModule
  ]
})
export class AddAssetComponent implements OnInit{

  assetForm!: FormGroup;
  categories: any[] = [];
  parameters: any[] = [];
  @Input() user: any;
  @Input() use_name: any;
  @Output() back = new EventEmitter<void>();

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
      status: ['', Validators.required],
      brand: [''],
      model: [''],
      description: [''],
      date: [''],
      warranty: [''],
      price: <number|null>(null),
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
    
      const params = res.pendingAttributes; 
      this.parameters = params;

      this.attributes.clear();

      // params.forEach((param: any) => {
      //   this.attributes.push(
      //     this.fb.group({
      //       parameter_id: [param.id],
      //       value: ['', param.is_required ? Validators.required : []]
      //     })
      //   );
      // });
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
      //  this.back.emit();
    }});
  }

  transformPayload() {
    const formValue = this.assetForm.value;

    const attributes: any = {};

    formValue.attributes.forEach((attr: any) => {
      attributes[attr.parameter_id] = attr.value;
    });

    return {
      selected_user: this.user?this.user:0,
      name: formValue.name,
      category_id: formValue.category_id,
      status: formValue.status,
      brand: formValue.brand,
      warranty: formValue.warranty,
      price: formValue.price,
      attributes
    };
  }

}