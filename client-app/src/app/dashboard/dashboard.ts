import { Component, OnInit } from '@angular/core';
import { MatSidenavModule } from '@angular/material/sidenav';
import { MatToolbarModule } from '@angular/material/toolbar';

import {signal} from '@angular/core';

import { MatCardModule } from '@angular/material/card';
import { MatIconModule } from '@angular/material/icon';
import { AuthService } from '../auth-service';
import { Router } from '@angular/router';
import { AssetService } from '../asset-service';

import { ChangeDetectorRef, NgZone  } from '@angular/core';
@Component({
  selector: 'app-dashboard',
  imports: [MatCardModule, MatSidenavModule, MatToolbarModule, MatIconModule],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
 
})
export class DashboardPage implements OnInit {
  // private apiUrl = 'https://localhost:8000/api/assets/stats';
  public totalAssets = signal(0);
  assignedAssets = signal(0);
  unassignedAssets = signal(0);
 
  constructor( private assetService: AssetService, private cd: ChangeDetectorRef){


  }

  ngOnInit(): void {
    setTimeout(() => {
      this.initForm();

    }, 100);

      
      // });
    
  }

  initForm(){
    this.assetService.getStats().subscribe(res=>{
          // this.zone.run(()=>{
          console.log('API RESPONSE:', res);
          this.totalAssets.set(res.total_assets) ;
          this.assignedAssets.set(res.assigned_assets);
          this.unassignedAssets.set(res.unassigned_assets);
          
        //  this.cd.detectChanges();
        }
      );
  }
}
