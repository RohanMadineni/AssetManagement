import { Injectable } from '@angular/core';
import { RequestService } from './request-service';

@Injectable({
  providedIn: 'root',
})
export class UserService {

  constructor(private requestService: RequestService){}

  getUsers(){
    return this.requestService.GetRequest('http://localhost:8000/api/users');
  }

  deleteUser(id: number){
    return this.requestService.DeleteRequest(`http://localhost:8000/api/users/${id}`);
  }
  updateUser(id: number, data:any){
    console.log(data);  
    return this.requestService.PutRequest(`http://localhost:8000/api/putusers/${id}`, data);
  }

  getUserProfile(){
    return this.requestService.GetRequest('http://localhost:8000/api/user/profile');
  }

  updateUserProfile(data:any){
    return this.requestService.PutRequest('http://localhost:8000/api/user/profile', data);
  }
  setUserPassword(data:any){
    return this.requestService.PutRequest('http://localhost:8000/api/user/password', data);
  }


}
