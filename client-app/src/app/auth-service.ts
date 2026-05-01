import { HttpClient } from '@angular/common/http';
import { Component, Injectable } from '@angular/core';
import { User } from './user';
import { tap, shareReplay } from 'rxjs';
import moment from 'moment';


@Injectable({
  providedIn: 'root'
})

export class AuthService {
 
  constructor(private http:HttpClient){

  }

  login(username:string, password:string){
    return this.http.post<User>('http://localhost:8000/api/auth/login', {username, password}).pipe(
        tap(response=>{this.setSession(response);})
    );
  }

  register(username:any, password:any, role:any){
    // const token = localStorage.getItem('token');

    // const headers = {
    //   Authorization: `Bearer ${token}`
    // };

    return this.http.post<User>('http://localhost:8000/api/auth/register', {username, password, role});
  }

  logout(){
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    localStorage.removeItem('expiresAt');
    return this.http.post<User>('http://localhost:8000/api/auth/logout', {});
  }

  private setSession(authResult: any){
      const expiresAt = moment().add(authResult.authorisation.expires_in,'seconds');
      localStorage.setItem('token', authResult.authorisation.token);
      localStorage.setItem('expiresAt', JSON.stringify(expiresAt.valueOf()));
      localStorage.setItem('user', JSON.stringify(authResult.user));
  }

  public isLoggedIn() {
   
      const token = localStorage.getItem('token');
      
      const expiration = this.getExpiration();

      if (!token || !expiration || !expiration.isValid()) {
        return false;
      }
     
      return moment().isBefore(expiration);
  }
  
  isLoggedOut() {
      return !this.isLoggedIn();
  }

  getExpiration() {
      const expiration = localStorage.getItem("expiresAt");
      
      if(!expiration) return null;
      const expiresAt = JSON.parse(expiration);

      return moment(Number(expiresAt));
      
  }  

  getRole(){
    return this.http.get<any>('http://localhost:8000/api/auth/role');
  }
}
