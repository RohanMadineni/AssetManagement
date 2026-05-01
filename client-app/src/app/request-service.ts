import { HttpClient } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
@Injectable({
  providedIn: 'root',
})
export class RequestService {
  
  val_res: any;
  private readonly http = inject(HttpClient);
  
  constructor(){};

  public GetRequest(url: string, params?: any){
  
    if(params){
      return this.http.get<any>(url,{params});
    }

    return this.http.get<any>(url);

    
  }  

  public PostRequest(url: any, data: any){
      // console.log({url, data});
      return this.http.post(url, data);
    
  } 
  public PutRequest(url: any, data: any){
      console.log({url, data});
      return this.http.put(url, data);
    
  } 
  public DeleteRequest(url: any, data?: any){

    if(typeof data!="undefined"){
      return this.http.delete(url, data);
    }

    return this.http.delete(url);
  }
}
