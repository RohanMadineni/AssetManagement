// import { HttpClient } from '@angular/common/http';
import { Injectable, Component, PLATFORM_ID } from '@angular/core';
import { RequestService } from './request-service';
import { environment } from '../environment';
// import { signal } from '@angular/core';
@Injectable({
  providedIn: 'root',
})
export class AssetService {
  constructor(private requestService: RequestService){
    
  }

  getStats() {
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/stats');
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/stats`);
  }
  
  getallStats() {
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/allstats');
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/allstats`);
  }

  getCategories() {
    // return this.requestService.GetRequest('http://localhost:8000/api/category');
    return this.requestService.GetRequest(`${environment.apiUrl}/category`);
  }

  getCategoryParameters(categoryId: number) {
    // return this.requestService.GetRequest(`http://localhost:8000/api/categories/${categoryId}/parameters`);
    return this.requestService.GetRequest(`${environment.apiUrl}/categories/${categoryId}/parameters`);
  }
  createCategory(data:any){
    // return this.requestService.PostRequest('http://localhost:8000/api/category', data);
    return this.requestService.PostRequest(`${environment.apiUrl}/category`, data);
  }
  createAsset(data: any) {
    // return this.requestService.PostRequest('http://localhost:8000/api/assets', data);
    return this.requestService.PostRequest(`${environment.apiUrl}/assets`, data);
  }

  getAssets(params: any) {
    // return this.requestService.GetRequest('http://localhost:8000/api/assets', params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets`, params);
  }
  getAllAssets(params: any) {
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/all', params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/all`, params);
  }
  getUpcomingAssets(params: any){
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/warranty/upcoming', params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/warranty/upcoming`, params);
  }
  getAllUpcomingAssets(params: any){
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/allwarranty/upcoming', params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/allwarranty/upcoming`, params);
  }
  updateAsset(id: number, data:any){
    // return this.requestService.PutRequest(`http://localhost:8000/api/assets/${id}`, data);
    return this.requestService.PutRequest(`${environment.apiUrl}/assets/${id}`, data);
  }
  deleteAsset(id: number) {
    // return this.requestService.DeleteRequest(`http://localhost:8000/api/assets/${id}`);
    return this.requestService.DeleteRequest(`${environment.apiUrl}/assets/${id}`);
  }

  createParam(id: number, data: any){
    // return this.requestService.PostRequest(`http://localhost:8000/api/categories/${id}/parameters`, data);
    return this.requestService.PostRequest(`${environment.apiUrl}/categories/${id}/parameters`, data);
  }

  updateParam(id: number, data: any){
    // return this.requestService.PutRequest(`http://localhost:8000/api/parameters/${id}`, data);
    return this.requestService.PutRequest(`${environment.apiUrl}/parameters/${id}`, data);
  }

  assignAsset(payload: any) {
    // return this.requestService.PostRequest('http://localhost:8000/api/assets/assign', payload);
    return this.requestService.PostRequest(`${environment.apiUrl}/assets/assign`, payload);
  }

  returnAsset(assetId: number) {
    // return this.requestService.PostRequest('http://localhost:8000/api/assets/return', { asset_id: assetId });
    return this.requestService.PostRequest(`${environment.apiUrl}/assets/return`, { asset_id: assetId });
  }

  getHistory(assetId: number) {
    // return this.requestService.GetRequest(`http://localhost:8000/api/assets/${assetId}/history`);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/${assetId}/history`);
  }
  getRecentlyAssignedAssets(params: any){
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/recentlyAssigned', params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/recentlyAssigned`, params);
  }
  getRecentlyAllAssignedAssets(params: any){
    // return this.requestService.GetRequest('http://localhost:8000/api/assets/allrecentlyAssigned', params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/allrecentlyAssigned`, params);
  }
  getAssetHistory(params: any){
    const id = params.asset_id;
    // return this.requestService.GetRequest(`http://localhost:8000/api/assets/history/${id}`, params);
    return this.requestService.GetRequest(`${environment.apiUrl}/assets/history/${id}`, params);
  }

  searchAssets(query: string) {
    return this.requestService.GetRequest(`${environment.apiUrl}/search?q=${query}`);
  }
}
