import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormGroup, FormBuilder, Validators, FormControl, FormArray } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { DomSanitizer, SafeHtml } from '@angular/platform-browser';
import { main } from '@popperjs/core';
import { JsonPipe } from '@angular/common';

import { Observable, of } from "rxjs";
import { switchMap, map } from "rxjs/operators";

@Component({
  selector: 'app-offer-approv',
  templateUrl: './offer-approv.component.html',
  styleUrls: ['./offer-approv.component.scss']
})
export class OfferApprovComponent implements OnInit {

  apporvForm: FormGroup;

  public isCollapsed = true; //ซ่อนรายละเอียดหนังสือ

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
  mainId: string | null;
  tpositionId: string | null | undefined;
  static dataService: any;

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

    this.mainId = this.route.snapshot.paramMap.get('id'); // sign main id
    let position_id = this.route.snapshot.paramMap.get('tposition'); // take position id
    this.doc.tpositionId = position_id;
    
    this.apporvForm = this.fb.group({
      userid: [userId, Validators.required],
      main_id: [this.mainId, Validators.required],
      depart_id: [departId, Validators.required],
      take_positions: this.fb.array([])
    });

    //ผู้ลงนาม และตำแหน่ง
    this.takePosition(departId);


  }

  ngOnInit(): void {
    this.addTakePosition();
    this.docDetail(this.mainId);
  }

  //  getData(main_id:any): Observable<string> {
  //   return this.dataService.apiApporvDocDetail(main_id)
  //   .subscribe((res: any) => {
  //     var temp = res[0];
     
  //   });
  // }

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
  docDetail(mainid: any) {
    this.dataService.apiApporvDocDetail(mainid)
      .subscribe((res: any) => {
        var temp = res[0];
        this.doc = temp;
        //this.doc.tpositionId = temp.tpositionId;//ผบ.
        console.log('รายละเอียดหนังสือ: ', this.doc);
      })
  }



  /** Form ctrl tpositionId เพิ่มผู้บังคับบัญชา */
  get take_positions(): FormArray {
    return this.apporvForm.get('take_positions') as FormArray
  }

  // เพิ่ม form ctrl receiver
  addTakePosition() {
    console.log('this.doc.tpositionId: ',this.tpositionId);
    const arrayForm = this.fb.group({
      tposition_id: [this.doc.tpositionId, Validators.required],
     
    })
    this.take_positions.push(arrayForm);
  }

  // เพิ่ม
  addTakePosition_test() {
    const creds = this.apporvForm.controls['take_positions'] as FormArray;
    creds.push(
      this.fb.group({
        tposition_id: [this.tpositionId, [Validators.required]]
      })
    );
  }

  // ลบ
  removeTakePosition(i: number) {
    this.take_positions.removeAt(i);
  }

  

  // getTotalQuestions(mainid:string): Observable<string> {
  //   let totalQuestions:number;
  //   var subject = new Subject<string>();
  //   this.dataService.apiApporvDocDetail(mainid)
  //   .subscribe(items => {
  //       items.map((item: { Total: number; }) => {
    
  //         totalQuestions=item.Total;
  //         console.log(totalQuestions);
  //         subject.next(totalQuestions);
  //       });
  //     }
  //   );
  //     return subject.asObservable();
  //   }

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
