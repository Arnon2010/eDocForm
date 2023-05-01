import { first } from 'rxjs/operators';
import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from './api.service';
import { HttpHeaders, HttpClient } from '@angular/common/http';

declare interface RouteInfo {
  path: string;
  title: string;
  icon: string;
  class: string;
}

export const ROUTES: RouteInfo[] = [
{ path: '/home', title: 'หน้าหลัก',  icon: 'ni-tv-2 text-primary', class: '' },
{ path: '/receive', title: 'หนังสือรับ',  icon:'ni-paper-diploma text-blue', class: '' },
{ path: '/send', title: 'หนังสือส่ง',  icon:'fa fa-paper-plane-o text-orange', class: '' },
{ path: '/setting', title: 'แก้ไขและบันทึก',  icon:'ni-settings-gear-65 text-yellow', class: '' },
{ path: '/search', title: 'ค้นหา',  icon:'fa fa-search text-red', class: '' },
//{ path: '/register', title: 'จองเลขหนังสือ',  icon:'ni-key-25 text-info', class: '' }
];

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {
  
  public menuItems: any[] | undefined;
  public isCollapsed = true;

  loginbtn: boolean;
  logoutbtn: boolean;
  userRole:any;

  // public get loggedIn() {
  //   return this.userRole = 'A';
  // }

  facName:string = '';
  univName:string = '';
  firstName:string = '';
  lastName:string = '';

  dateDeadline: boolean = false;
  
  title = 'eDocForm';

  constructor(
    private dataService: ApiService,
    private router: Router,
    private httpClient: HttpClient
    ) { 
      dataService.getLoggedInName.subscribe((name) => this.changeName(name));

      if (this.dataService.isLoggedIn()) {
        this.loginbtn = false;
        this.logoutbtn = true;

        const token:any = this.dataService.getToken();
        let user = JSON.parse(token);
        console.log('token user: ', user);
        var _user_role = user.user_role;
        this.userRole = _user_role;
        this.facName = user.fac_name;
        this.univName = user.univ_name;
        this.firstName = user.user_firstname;
        this.lastName = user.user_lastname;

        if(this.userRole != 'A'){
          this.dateDeadline = true;
        }

      } else {
        this.loginbtn = true;
        this.logoutbtn = false;

        this.userRole = '';
      }
    }

  ngOnInit() {

    this.initializeApp();

    this.menuItems = ROUTES.filter(menuItem => menuItem);
    this.router.events.subscribe((event) => {
      this.isCollapsed = true;
   });


  }

  initializeApp() {
    if (this.dataService.isLoggedIn()) {
      console.log('loggedin');
      this.router.navigate(['/']);
    } else {
      console.log('loggedin again...');
      this.router.navigate(['/login']);
    }
  }

  private changeName(name: boolean): void {
    this.logoutbtn = name;
    this.loginbtn = !name;
  }

  logout() {
    this.dataService.deleteToken();
    window.location.reload();
  }


}
