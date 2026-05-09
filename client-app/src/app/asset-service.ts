// import { HttpClient } from '@angular/common/http';
import { Injectable, Component, PLATFORM_ID } from '@angular/core';
import { RequestService } from './request-service';
// import { signal } from '@angular/core';
@Injectable({
  providedIn: 'root',
})
export class AssetService {
  constructor(private requestService: RequestService){
    
  }
  // users = signal<any[]>([]);
  getStats() {
    // return this.http.get<any>('http://localhost:8000/api/assets/stats');
    
    return this.requestService.GetRequest('http://localhost:8000/api/assets/stats');
  }
  
  getallStats() {
    // return this.http.get<any>('http://localhost:8000/api/assets/stats');
    
    return this.requestService.GetRequest('http://localhost:8000/api/assets/allstats');
  }

  getCategories() {
    // return this.http.get<any>('http://localhost:8000/api/category');
    return this.requestService.GetRequest('http://localhost:8000/api/category');
  }

  getCategoryParameters(categoryId: number) {
    // return this.http.get<any>(`http://localhost:8000/api/categories/${categoryId}/parameters`);
    return this.requestService.GetRequest(`http://localhost:8000/api/categories/${categoryId}/parameters`);
  }
  createCategory(data:any){
    return this.requestService.PostRequest('http://localhost:8000/api/category', data);
  }
  createAsset(data: any) {
    // return this.http.post('http://localhost:8000/api/assets', data);
    return this.requestService.PostRequest('http://localhost:8000/api/assets', data);
  }

  getAssets(params: any) {
    // return this.http.get<any>('http://localhost:8000/api/assets', {params});
    return this.requestService.GetRequest('http://localhost:8000/api/assets', params);
  }
  getAllAssets(params: any) {
    // return this.http.get<any>('http://localhost:8000/api/assets', {params});
    return this.requestService.GetRequest('http://localhost:8000/api/assets/all', params);
  }
  getUpcomingAssets(params: any){
    return this.requestService.GetRequest('http://localhost:8000/api/assets/warranty/upcoming', params);
  }
  getAllUpcomingAssets(params: any){
    return this.requestService.GetRequest('http://localhost:8000/api/assets/allwarranty/upcoming', params);
  }
  updateAsset(id: number, data:any){
    return this.requestService.PutRequest(`http://localhost:8000/api/assets/${id}`, data);
  }
  deleteAsset(id: number) {
    
    // return this.http.delete(`http://localhost:8000/api/assets/${id}`);
    return this.requestService.DeleteRequest(`http://localhost:8000/api/assets/${id}`);
  }

  createParam(id: number, data: any){
    console.log({id, data});
    return this.requestService.PostRequest(`http://localhost:8000/api/categories/${id}/parameters`, data);
  }

  updateParam(id: number, data: any){
    console.log({id, data});
    return this.requestService.PutRequest(`http://localhost:8000/api/parameters/${id}`, data);
  }

  assignAsset(payload: any) {
    return this.requestService.PostRequest('http://localhost:8000/api/assets/assign', payload);
  }

  returnAsset(assetId: number) {
    return this.requestService.PostRequest('http://localhost:8000/api/assets/return', { asset_id: assetId });
  }

  // assignAsset(assetId: number){
  //   return this.requestService.PostRequest('http://localhost:8000/api/assets/assign', { asset_id: assetId });
  // }
  getHistory(assetId: number) {
    return this.requestService.GetRequest(`http://localhost:8000/api/assets/${assetId}/history`);
  }
  getRecentlyAssignedAssets(params: any){
    return this.requestService.GetRequest('http://localhost:8000/api/assets/recentlyAssigned', params);
  }
  getRecentlyAllAssignedAssets(params: any){
    return this.requestService.GetRequest('http://localhost:8000/api/assets/allrecentlyAssigned', params);
  }
  getAssetHistory(params: any){
    const id = params.asset_id;
    return this.requestService.GetRequest(`http://localhost:8000/api/assets/history/${id}`, params);
  }
}
