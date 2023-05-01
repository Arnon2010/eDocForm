import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { environment } from '../../../environments/environment';
import { ApiService } from '../../api.service';
import { first, pipe, retry } from 'rxjs';

@Component({
  selector: 'app-receive',
  templateUrl: './receive.component.html',
  styleUrls: ['./receive.component.scss']
})
export class ReceiveComponent implements OnInit {

  public datasets: any;
  public data: any;
  public clicked: boolean = true;
  public clicked1: boolean = false;


  orderApprovReceive: any; //จำนวนหนังสือเกษียณที่ยังดำเนินการ
  orderForward: any; // จำนวนหนังสือส่งต่อ
  orderIncome: any; //จำนวนหนังสือเข้า
  
  docReceiveList: any;
  row:any = 0;
  rowperpage:any = 15;
  postsData:any = [];
  busy:boolean = false;
  loading:boolean = false;
  buttonText: string = 'Loading...';

  constructor(
    private httpClient:HttpClient,
    private dataService: ApiService
  ){}
  ngOnInit(): void {
    //throw new Error('Method not implemented.');

    //get user profile
    const token:any = this.dataService.getToken();
    let user = JSON.parse(token);
    var departId = user.depart_id;
    var userType = user.usertype_code;

    console.log('user: ', user);

    this.dataReceive(userType, departId,'2565','notSearch');

    this.amountOrderApprovReceive(departId);
    this.amountOrder(departId);
    this.amountSendto(departId);


  }

  // รายการหนังสือระบ

  public dataReceive(userType:any, departId:any, Year:any, qSearch:any) {
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

  public updateOptions() {
    // this.salesChart.data.datasets[0].data = this.data;
    // this.salesChart.update();
  }

  /* จำนวนหนังสือกำลังดำเนินการเกษียณ */
  amountOrderApprovReceive(depart_id:any) {
    this.httpClient.get(environment.baseUrl + '/receive/_approv_doc_amount.php?depart_id=' + depart_id)
    .subscribe((res:any) => {
      //console.log('amount order apporv receive: ',res);
      this.orderApprovReceive = res.data[0].amountOrder;
      console.log('orderApprovReceive: ',this.orderApprovReceive);
    })
  }

   /* จำนวนหนังสือเข้า */
  amountOrder(depart_id:any){
      this.httpClient.get(environment.baseUrl + "/receive/_edoc_amount_order.php?depart_iduser=" + depart_id)
      .subscribe((res:any) => {
        console.log('Order: ',res);
        this.orderIncome = res.data[0].amountOrder;
      })
  }

  /* จำนวนหนังสือส่งต่อจากหน่วยงาน */
  amountSendto(depart_id:any){
      this.httpClient.get(environment.baseUrl + "/receive/_edoc_amount_sendto.php?depart_iduser=" + depart_id)
      .subscribe((res:any) => {
        this.orderForward = res.data[0].amountSendto;

      })
  }

}
