import { Injectable } from '@angular/core';
import { RequestService } from './request-service';
import { environment } from '../environment';
import { HttpClient } from '@angular/common/http';
@Injectable({
  providedIn: 'root',
})
export class ExportServiceTs {
  constructor (private requestService: RequestService, private http: HttpClient){

  }

  export_asset_list(){
    // return this.requestService.GetRequest(`${environment.apiUrl}/export-assets`);
    return this.http.get(`${environment.apiUrl}/export-assets`, {responseType: 'blob'});
  }

  export_user_list(){
    // return this.requestService.GetRequest(`${environment.apiUrl}/export-users`);
    return this.http.get(`${environment.apiUrl}/export-users`,{responseType: 'blob'});
  }

  export_asset_list_pdf(){
    // return this.requestService.GetRequest(`${environment.apiUrl}/export-assets`);
    return this.http.get(`${environment.apiUrl}/export-assets-pdf`, {responseType: 'blob'});
  }

  export_user_list_pdf(){
    // return this.requestService.GetRequest(`${environment.apiUrl}/export-users`);
    return this.http.get(`${environment.apiUrl}/export-users-pdf`,{responseType: 'blob'});
  }
}
