import { Injectable, Output, EventEmitter } from '@angular/core';
import { catchError, map } from 'rxjs/operators';
import { HttpClient, HttpErrorResponse, HttpHeaders } from '@angular/common/http';
//import { Users } from './users';
import { environment } from '../environments/environment';
import { BehaviorSubject, Observable, throwError } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {

  redirectUrl: string | undefined;
  headers = new HttpHeaders().set('Content-Type', 'application/json');

  private _isLoggedIn = new BehaviorSubject<boolean>(false);

  @Output() getLoggedInName: EventEmitter<any> = new EventEmitter();
  constructor(private httpClient: HttpClient) { }

  /** LOGIN */
  public userlogin(User: string, Pass: string) {

    const httpOptions = {
 	 	  headers: new HttpHeaders()
	  }

    httpOptions.headers.append('Access-Control-Allow-Origin', '*');
    httpOptions.headers.append('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    httpOptions.headers.append('Content-Type', 'application/json');
    //httpOptions.headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	  //this.httpClient.post(<url>, <body>, httpOptions);
    return this.httpClient
      .post<any>(environment.baseUrl + '/login/login.php', { User, Pass }, httpOptions)
      .pipe(
        map((Users) => {
          this.setToken(Users[0]);
          this.getLoggedInName.emit(true);
          return Users;
        })
      );
  }

  // User profile
  //  getUserProfile(id: any) {
  //   let api = environment.baseUrl + '/userProfile.php?id=' + id;
  //   return this.httpClient.get(api, { headers: this.headers }).pipe(
  //     map((res) => {
  //       return res || {};
  //     }),
  //     //catchError(this.handleError)
  //   );
  // }

  // User profile
  // getSetting() {
  //   let api = environment.baseUrl + '/getSetting.php';
  //   return this.httpClient.get(api, { headers: this.headers }).pipe(
  //     map((res) => {
  //       return res || {};
  //     }),
  //     //catchError(this.handleError)
  //   );
  // }

  /** Check date deadline */
  // checkDateDeadline() {
  //   let api = environment.baseUrl + '/setting/checkDateDeadline.php';
  //   return this.httpClient
  //   .get<any>(api, { headers: this.headers })
  //   .pipe(map((res) => {
  //     return res;
  //     })
  //   );
  // }

  /** REGISTRATION */
  // public userregistration(name: string, email: string, pwd: string) {
  //   return this.httpClient
  //     .post<any>(environment.baseUrl + '/register.php', { name, email, pwd })
  //     .pipe(
  //       map((Users) => {
  //         return Users;
  //       })
  //     );
  // }

  // Error
  handleError(error: HttpErrorResponse) {
    let msg = '';
    if (error.error instanceof ErrorEvent) {
      // client-side error
      msg = error.error.message;
    } else {
      // server-side error
      msg = `Error Code: ${error.status}\nMessage: ${error.message}`;
    }
    return throwError(msg);
  }

  //token
  setToken(token: any) {
    localStorage.setItem('token', JSON.stringify(token));
  }
  getToken() {
    return localStorage.getItem('token');
  }

  getTokenUser() {
    return localStorage.getItem('token');
  }

  deleteToken() {
    localStorage.removeItem('token');
  }

  isLoggedIn() {
    const usertoken = this.getToken();
    if (usertoken != null) {
      return this._isLoggedIn.asObservable();
    }
    return false;
  }
}
