import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { environment } from '../../../environments/environment';
import { ApiService } from '../../api.service';
import { first, pipe, retry } from 'rxjs';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  public datasets: any;
  public data: any;
  public clicked: boolean = true;
  public clicked1: boolean = false;


  orderApprovReceive: any; //จำนวนหนังสือเกษียณที่ยังดำเนินการ
  orderForward: any; // จำนวนหนังสือส่งต่อ
  orderIncome: any; //จำนวนหนังสือเข้า

  orderApplyUnivNumber: any; //จำนวนหนังสือขอเลขมหาลัย
  orderApprovSend: any; // จำนวนหนังสือลงนามที่ยังดำเนินการ
  
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
    
    //console.log('user: ', user);
    this.amountOrderApprovReceive(departId);
    this.amountOrder(departId);
    this.amountSendto(departId);
    this.amountOrderApplyUnivNumber('2566');
    this.amountOrderApprovSend('2566', departId);


  }

  public updateOptions() {
    // this.salesChart.data.datasets[0].data = this.data;
    // this.salesChart.update();
  }

  /* จำนวนหนังสือกำลังดำเนินการเกษียณ */
  amountOrderApprovReceive(depart_id:any) {
    this.httpClient.get(environment.baseUrl + '/receive/_approv_doc_amount.php?depart_id=' + depart_id)
    .subscribe((res:any) => {
      this.orderApprovReceive = res.data[0].amountOrder;
      //console.log('orderApprovReceive: ',this.orderApprovReceive);
    })
  }

   /* จำนวนหนังสือเข้า */
  amountOrder(depart_id:any){
      this.httpClient.get(environment.baseUrl + "/receive/_edoc_amount_order.php?depart_iduser=" + depart_id)
      .subscribe((res:any) => {
        //console.log('Order: ',res);
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

  /* จำนวนหนังสือขอเลขมหาลัย */
  amountOrderApplyUnivNumber(year:any){
    this.httpClient.get(environment.baseUrl + "/send/_apply_number_univ_amount.php?year=" + year)
    .subscribe((res: any) => {
       this.orderApplyUnivNumber = res.data[0].amountOrder;
    });
  }

  /* จำนวนหนังสือกำลังดำเนินการลงนาม */
  amountOrderApprovSend(year:any, depart_id:any){
      this.httpClient.get(environment.baseUrl + "/send/_approv_doc_amount.php?year=" + year 
          + "&depart_id=" + depart_id).subscribe((res:any) => {
          //console.log('res: ', res);
          this.orderApprovSend = res.data[0].amountOrder;
      });
  }

}
