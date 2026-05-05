import {Component, OnInit, signal} from '@angular/core';
import { MatCardModule } from '@angular/material/card';
import { MatIcon } from '@angular/material/icon';
import { AssetService } from '../asset-service';
import { CanvasJSAngularChartsModule } from '@canvasjs/angular-charts';
import { CommonModule } from '@angular/common';
import { MatTableModule } from '@angular/material/table';
import { MatDialog } from '@angular/material/dialog';
import { AssetViewDialog } from '../asset-view-dialog/asset-view-dialog';
@Component({
  selector: 'app-system',
  imports: [ MatCardModule, MatIcon, CanvasJSAngularChartsModule, CommonModule, MatTableModule],
  templateUrl: './system.html',
  styleUrl: './system.scss',
})
export class SystemPage implements OnInit{

  public totalAssets = signal(0);
  assignedAssets = signal(0);
  availableAssets = signal(0);
  maintenanceAssets = signal(0);
  categories = signal(0); 
  totalvalue = signal(0);
  upcomingWarranties = signal<any[]>([]);
  labels = signal<any[]>([]);
  series = signal<any[]>([]);
  catarry = signal<any[]>([]);
  stats = signal<any[]>([]);
  series2 = signal<any[]>([]);

  ngOnInit(): void {
      
      this.initForm();
            
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

    this.assetService.getallStats().subscribe(res=>{
          this.totalAssets.set(res.total_assets);
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
                {y:this.assignedAssets(), name: "Assigned", color: "#F59E0B",},
                {y:this.availableAssets(), name: "Available", color: "#22C55E",},
                {y:this.maintenanceAssets(), name: "Under Maintenance", color: "#A855F7",}
              ]
             }]
          });        
    });
    
    this.assetService.getAllUpcomingAssets().subscribe(res => {
      this.upcomingWarranties.set(res);
      console.log(this.upcomingWarranties());
    });
  }
  viewAsset(view_Asset:any){
    this.dialog.open(AssetViewDialog, {data: view_Asset, height: '350px', width: '350px'});
  }
}
