// import { Component, OnInit } from '@angular/core';
// import { MatSidenavModule } from '@angular/material/sidenav';
// import { MatToolbarModule } from '@angular/material/toolbar';

// import {signal} from '@angular/core';

// import { MatCardModule } from '@angular/material/card';
// import { MatIconModule } from '@angular/material/icon';
// import { AuthService } from '../auth-service';
// import { Router } from '@angular/router';
// import { AssetService } from '../asset-service';

// import { ChangeDetectorRef, NgZone  } from '@angular/core';
// @Component({
//   selector: 'app-dashboard',
//   imports: [MatCardModule, MatSidenavModule, MatToolbarModule, MatIconModule],
//   templateUrl: './dashboard.html',
//   styleUrl: './dashboard.scss',
 
// })
// export class DashboardPage implements OnInit {
//   // private apiUrl = 'https://localhost:8000/api/assets/stats';
//   public totalAssets = signal(0);
//   assignedAssets = signal(0);
//   unassignedAssets = signal(0);
 
//   constructor( private assetService: AssetService, private cd: ChangeDetectorRef){


//   }

//   ngOnInit(): void {
//     setTimeout(() => {
//       this.initForm();

//     }, 100);

      
//       // });
    
//   }

//   initForm(){
//     this.assetService.getStats().subscribe(res=>{
//           // this.zone.run(()=>{
//           console.log('API RESPONSE:', res);
//           this.totalAssets.set(res.total_assets) ;
//           this.assignedAssets.set(res.assigned_assets);
//           this.unassignedAssets.set(res.unassigned_assets);
          
//         //  this.cd.detectChanges();
//         }
//       );
//   }
// }

// import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
// import { FormsModule, ReactiveFormsModule, FormGroup, FormBuilder, Validators, FormControl } from '@angular/forms';
// import { MatFormFieldModule } from '@angular/material/form-field';
// import { AssetService } from '../asset-service';
// import { MatSelectModule } from '@angular/material/select';
// import { MatButtonModule } from '@angular/material/button';
// import { MatIconModule } from '@angular/material/icon';
// import { MatCardModule } from '@angular/material/card';
// import { CommonModule } from '@angular/common';
// import { MatInputModule } from '@angular/material/input';
// import {signal} from '@angular/core';
// @Component({
//   selector: 'app-asset-detail',
//   imports: [ReactiveFormsModule, MatFormFieldModule, MatSelectModule, CommonModule, FormsModule, MatButtonModule, MatIconModule, MatCardModule, MatInputModule],
//   templateUrl: './asset-detail.html',
//   styleUrl: './asset-detail.scss',
// })
// export class AssetDetail implements OnInit{
  
//   categories = signal<any[]>([]);
//   assets = signal<any[]>([]);
//   filters = {
//     category_id: '',
//     status: '',
//     asset: '',
//   };
//   assetForm = new FormGroup({
//       param_name: new FormControl(''),
//       param_value: new FormControl('')
//   });
//   constructor(private assetService: AssetService, private fb: FormBuilder) {};
  
//   ngOnInit(): void {
//     this.initForm();
//     setTimeout(() => {
//       this.loadCategories();
//       this.loadAssets();
      
//     }, 1000);
//   }
//   initForm(){
//     this.categories.set([[]])
//      this.assets.set([]);
//     // this.cd.detectChanges()
//   }
//   loadCategories(){{
//     this.assetService.getCategories().subscribe(res => {this.categories.set(res)});
  
//   }}
//   onSubmit()
//   {this.assetForm.reset();}

//   loadAssets(){
//     const params: any = {
//       ...this.filters
//     };
//     this.assetService.getAssets(params).subscribe(res=>{
//       this.assets.set(res.data);
//       console.log(res);
//       console.log(this.assets);
//     });
    
//   }
// }

import {Component, OnInit, signal} from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { MatTableModule } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { AssetService } from '../asset-service';
import { CanvasJSAngularChartsModule } from '@canvasjs/angular-charts';
import { AssetViewDialog } from '../asset-view-dialog/asset-view-dialog';
@Component({
  selector: 'app-dashboard',
  imports: [ MatCardModule, MatIconModule, CanvasJSAngularChartsModule, CommonModule, MatTableModule],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class DashboardPage implements OnInit{

  public totalAssets = signal(0);
  assignedAssets = signal(0);
  availableAssets = signal(0);
  maintenanceAssets = signal(0);
  upcomingWarranties = signal<any[]>([]);
  totalvalue = signal(0);
  categories = signal(0); 
  labels = signal<any[]>([]);
  series = signal<any[]>([]);
  catarry = signal<any[]>([]);
  stats = signal<any[]>([]);
  series2 = signal<any[]>([]);

  ngOnInit(): void {
      // setTimeout(() => {
      this.initForm();

    // });
            
  }
  colors = [
  "#22C55E",
  "#F59E0B",
  "#06B6D4",
  "#A855F7",
  "#EF4444",
];
  chartOptions = signal<any>({
              animationEnabled: true,
              height: 160,
              data: [{
              type: "doughnut",
              yValueFormatString: "#,###.##",
              indexLabel: "{name}",
              dataPoints:null,
              }	]
              
        });
  
  chartOptions2 = signal<any>({
              animationEnabled: true,
              height: 160,
              data: [{
              type: "doughnut",
              yValueFormatString: "#,###.##",
              indexLabel: "{name}",
              dataPoints:null,
              }	]
        });
          
  constructor(private assetService: AssetService, private dialog: MatDialog){}

  initForm(){

    this.assetService.getStats().subscribe(res=>{
          this.totalAssets.set(res.total_assets) ;
          this.assignedAssets.set(res.assigned_assets);
          this.availableAssets.set(res.unassigned_assets);
          this.maintenanceAssets.set(res.under_maintenance);
          this.labels.set(res.catNames);
          this.totalvalue.set(res.totalvalue);
          this.series.set(Object.values(res.cat_Array));
          this.categories.set(Object.keys(res.cat_Array).length);
          this.chartOptions.set({
            ...this.chartOptions(),
            // title: {text: "Assets By Category"},
              data : [{
              ...this.chartOptions().data[0],
              dataPoints: this.series().map((y, i) => ({
                          y,
                          name: this.labels()[i+1],
                          color: this.colors[i % this.colors.length]
                        }))
             }]
          });   
          this.chartOptions2.set({
            ...this.chartOptions(),
            // title: {text: "Assets By Status"},
              data : [{
              ...this.chartOptions().data[0],
              dataPoints: [
                {y:this.assignedAssets(), name: "Assigned", color:  "#F59E0B",},
                {y:this.availableAssets(), name: "Available", color: "#22C55E",},
                {y:this.maintenanceAssets(), name: "Under Maintenance", color: "#A855F7",}
              ]
             }]
          });        
    });
    
    this.assetService.getUpcomingAssets().subscribe(res => {
      this.upcomingWarranties.set(res);
      console.log(this.upcomingWarranties());
    });
    
  }
  viewAsset(view_Asset:any){
    this.dialog.open(AssetViewDialog, {data: view_Asset, height: '350px', width: '350px'});
  }
  
}