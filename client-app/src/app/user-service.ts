import { Injectable } from '@angular/core';
import { RequestService } from './request-service';
import { environment } from '../environment';
@Injectable({
  providedIn: 'root',
})
export class UserService {

  constructor(private requestService: RequestService){}

  getUsers(){
    return this.requestService.GetRequest(`${environment.apiUrl}/users`);
  }

  deleteUser(id: number){
    return this.requestService.DeleteRequest(`${environment.apiUrl}/users/${id}`);
  }
  updateUser(id: number, data:any){
    console.log(data);  
    return this.requestService.PutRequest(`${environment.apiUrl}/putusers/${id}`, data);
  }

  getUserProfile(){
    return this.requestService.GetRequest(`${environment.apiUrl}/user/profile`);
  }

  updateUserProfile(data:any){
    return this.requestService.PutRequest(`${environment.apiUrl}/user/profile`, data);
  }
  setUserPassword(data:any){
    return this.requestService.PutRequest(`${environment.apiUrl}/user/password`, data);
  }


}
