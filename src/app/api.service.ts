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
  public userlogin(User: string, Pass: string, depart_allow: string) {

    const httpOptions = {
 	 	  headers: new HttpHeaders()
	  }

    httpOptions.headers.append('Access-Control-Allow-Origin', '*');
    httpOptions.headers.append('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    httpOptions.headers.append('Content-Type', 'application/json');
    //httpOptions.headers.append('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
	  //this.httpClient.post(<url>, <body>, httpOptions);
    return this.httpClient
      .post<any>(environment.baseUrl + '/login/login.php', { User, Pass, depart_allow }, httpOptions)
      .pipe(
        map((Users) => {
          this.setToken(Users[0]);
          this.getLoggedInName.emit(true);
          return Users;
        })
      );
  }

  // check user allow more departments
  checkUser(id: any){
    let api = environment.baseUrl + '/login/_user_check_unique.php?user=' + id;
    return this.httpClient.get(api, { headers: this.headers}).pipe(
      map((res) => {
        return res || {};
      }),
      catchError(this.handleError)
    );
  }

  // multiply(factor: number | ((source: Example) => number)) {
  //   return (source: Observable<Example>) =>
  //     source.pipe(map(value => {
  //       const f = typeof factor === 'function' ? factor(value) : factor;
  //       return value.val * f;
  //     }))
  // }

  // 
  getOrderSignReceive(id: any) {
    let api = environment.baseUrl + '/receive/_approv_doc_amount.php?depart_id=' + id;
    return this.httpClient.get(api, { headers: this.headers }).pipe(
      map((res:any) => {
        return res[0].amountOrder;
      }),
      catchError(this.handleError)
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
