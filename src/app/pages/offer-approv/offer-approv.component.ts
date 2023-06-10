import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormGroup, FormBuilder, Validators, FormControl, FormArray } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { main } from '@popperjs/core';

@Component({
  selector: 'app-offer-approv',
  templateUrl: './offer-approv.component.html',
  styleUrls: ['./offer-approv.component.scss']
})
export class OfferApprovComponent implements OnInit {

  creatForm:FormGroup;

  doctype_list: any = [];
  deptgovernment_list: any = [];
  takeposition_list: any = [];
  position_list: any = [];

  doc: docDetail = {
    edocId: 0,
    docDate: '',
    docTypeName: '',
    Secrets: '',
    Rapid: '',
    departId_edoc: 0,
    departName: '',
    departGovernmentName: '',
    Contact: '',
    Comment: '',
    Headline: '',
    Receiver: '',
    docContent: '',
    docContentWish: '',
    docContentConc: '',
    tpositionId: '',
    userMaker: '',
    senderDepart: '',
    destroyYear: '',
  }
 
  constructor(private route: ActivatedRoute,
    private fb: FormBuilder,
    private dataService: ApiService,
    private router: Router,
    private httpClient: HttpClient,
    private sanitizer: DomSanitizer) {

      const token: any = this.dataService.getToken();
      let user = JSON.parse(token);
      var userId = user.userId;//รหัสผู้ใช้
      var departId = user.departId; // รหัสหน่วยงาน
      var userType = user.userType; //ประเภทผู้ใช้
      var univId = user.univId; // รหัสพื้นที่
      var departName = user.departName; // ชื่อหน่วยงาน
      var Sender = user.Fname + ' ' + user.Lname; // ผู้ส่ง

      this.creatForm = this.fb.group({
        userid: [userId, Validators.required],
        docdate: ['', Validators.required],
        secrets: ['1', Validators.required],
        rapid: ['1', Validators.required],
        doctype: ['1', Validators.required],
        depart_government_id: [departId, Validators.required],
        contact: ['', Validators.required],
        depart_id_user: [departId, Validators.required],
        receivers: this.fb.array([]),
        headline: ['', Validators.required],
        doc_content: ['', Validators.required],
        content_wishs: this.fb.array([]),
        doc_content_conc: ['', Validators.required],
        comment: ['', Validators.required],
        tposition_id: ['', Validators.required],
        sender: [Sender, Validators.required],
        senderdepart: [departName, Validators.required],
        destroy_year: ['1', Validators.required],
      });

      //ผู้ลงนาม และตำแหน่ง
      this.takePosition(departId);

    }

  ngOnInit(): void {

    var mainId = this.route.snapshot.paramMap.get('id');
    console.log('param mainId: ', mainId);
    this.apporvDocDetail(mainId);
  }

  // ผู้ลงนาม และตำแหน่ง
  takePosition(departid: any) {
    //default depart_id
    this.dataService.apiTakePosition(departid)
      .subscribe((res: any) => {
        this.takeposition_list = res;
        console.log('takeposition_list: ', res);
      })
  }

  // รายละเอียดหนังสือ
  apporvDocDetail(mainid: any) {
    this.dataService.apiApporvDocDetail(mainid)
    .subscribe((res: any) => {
      this.doc = res[0];
      console.log('รายละเอียดหนังสือ: ', this.doc);
    })
  }

}

export interface docDetail {
 
  edocId: Number;
  docDate: String;
  docTypeName: String;
  Secrets: String;
  Rapid: String;
  departId_edoc: Number;
  departName: String;
  departGovernmentName: string;
  Contact: string;
  Comment: string;
  Headline: String;
  Receiver: String;
  docContent: any;
  docContentWish: any;
  docContentConc: any;
  tpositionId: any;
  userMaker: String;
  senderDepart: String;
  destroyYear: String;
}
