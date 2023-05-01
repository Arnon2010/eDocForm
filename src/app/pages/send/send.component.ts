import { HttpClient } from '@angular/common/http';
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { environment } from '../../../environments/environment';
import { ApiService } from '../../api.service';
import { first, pipe, retry } from 'rxjs';
import { ModalDismissReasons, NgbDatepickerModule, NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-send',
  templateUrl: './send.component.html',
  styleUrls: ['./send.component.scss']
})
export class SendComponent implements OnInit {

  closeResult = '';

  public datasets: any;
  public data: any;
  public clicked: boolean = true;
  public clicked1: boolean = false;


  orderApplyUnivNumber: any; //จำนวนหนังสือเกษียณที่ยังดำเนินการ
  orderApprovSend: any; // จำนวนหนังสือลงนามที่ยังดำเนินการ
  
  docSendList: any;
  row:any = 0;
  rowperpage:any = 15;
  postsData:any = [];
  busy:boolean = false;
  loading:boolean = false;
  buttonText: string = 'Loading...';

  constructor(
    private httpClient:HttpClient,
    private dataService: ApiService,
    private modalService: NgbModal
  ){}
  ngOnInit(): void {
    //throw new Error('Method not implemented.');

    //get user profile
    const token:any = this.dataService.getToken();
    let user = JSON.parse(token);
    var departId = user.depart_id;
    var userType = user.usertype_code;
    //console.log('user: ', user);

    this.dataSend(userType, departId,'2566', 'all', 'notSearch');

    this.amountOrderApplyUnivNumber('2566');
    this.amountOrderApprovSend('2566', departId);


  }

  open(content:any) {
		this.modalService.open(content, { ariaLabelledBy: 'modal-basic-title' }).result.then(
			(result) => {
				this.closeResult = `Closed with: ${result}`;
			},
			(reason) => {
				this.closeResult = `Dismissed ${this.getDismissReason(reason)}`;
			},
		);
	}

	private getDismissReason(reason: any): string {
		if (reason === ModalDismissReasons.ESC) {
			return 'by pressing ESC';
		} else if (reason === ModalDismissReasons.BACKDROP_CLICK) {
			return 'by clicking on a backdrop';
		} else {
			return `with: ${reason}`;
		}
	}

  
  // รายการหนังสือระบ
  dataSend(userType:any, departId:any, Year:any, doc_type:any, qSearch:any) {
    this.httpClient
    .get(environment.baseUrl + '/send/_edoc_sent_data_new.php?depart=' + departId 
    + '&year=' + Year
    + '&depart_iduser=' + departId
    + '&doctype=' + doc_type
    + '&usertype=' + userType
    + '&row=' + this.row 
    + '&rowperpage=' + this.rowperpage
    + '&qsearch=' + qSearch)
    .subscribe(res => {
      this.docSendList = res;
      console.log('data docSendList: ',this.docSendList);
    })
  }

  public updateOptions() {
    // this.salesChart.data.datasets[0].data = this.data;
    // this.salesChart.update();
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
