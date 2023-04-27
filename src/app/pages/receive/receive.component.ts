import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { environment } from '../../../environments/environment';
import { ApiService } from '../../api.service';

@Component({
  selector: 'app-receive',
  templateUrl: './receive.component.html',
  styleUrls: ['./receive.component.scss']
})
export class ReceiveComponent implements OnInit {

  constructor(
    private httpClient:HttpClient,
    private dataService: ApiService
  ){}
  ngOnInit(): void {
    //throw new Error('Method not implemented.');
    this.dataReceive('SA','26','2565','notSearch');
  }

  docReceiveList: any;
  row:any = 0;
  rowperpage:any = 15;
  postsData:any = [];
  busy:boolean = false;
  loading:boolean = false;
  buttonText: string = 'Loading...';

  // รายการหนังสือระบ

  dataReceive(userType:any, departId:any, Year:any, qSearch:any) {
    this.httpClient
    .get(environment.baseUrl + '/receive/_edoc_receive_data_new.php?depart=' + departId 
    + '&year=' + Year
    + '&depart_iduser=' + departId
    + '&usertype=' + userType
    + '&row=' + this.row 
    + '&rowperpage=' + this.rowperpage
    + '&qsearch=' + qSearch)
    .subscribe(res => {
      this.docReceiveList = res;
      console.log('data receive: ',this.docReceiveList);
    })
  }

}
