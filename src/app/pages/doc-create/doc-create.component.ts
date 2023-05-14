import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { AngularEditorConfig } from '@kolkov/angular-editor';

import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'

declare var require: any;

@Component({
  selector: 'app-doc-create',
  templateUrl: './doc-create.component.html',
  styleUrls: ['./doc-create.component.scss']
})
export class DocCreateComponent implements OnInit {

  @ViewChild('pdfTable') pdfTable!: ElementRef;

  creatForm: FormGroup;
  // secrets:boolean = true;
  // rapid:boolean = true;
  doctype_list: any = [];
  deptgovernment_list: any = [];

  docNew: createDocForm = {
    userid: 0,
    docdate: '10/05/2023',
    doctype: '1',
    secrets: '1',
    rapid: '1',
    depart_id_user: 0,
    depart_government_id: 0,
    comment: '',
    headline: '',
    receiver: '',
    doc_content: '',
    sender: '',
    senderdepart: '',
    destroy_year: '1',
  }

  constructor(
    private fb: FormBuilder,
    private dataService: ApiService,
    private router: Router,
    private elementRef: ElementRef,
    private httpClient: HttpClient,
  ) {
    //get user profile
    const token: any = this.dataService.getToken();
    let user = JSON.parse(token);
    var userId = user.userId;//รหัสผู้ใช้
    var departId = user.departId; // รหัสหน่วยงาน
    var userType = user.userType; //ประเภทผู้ใช้
    var univId = user.univId; // รหัสพื้นที่
    var departName = user.departName; // ชื่อหน่วยงาน
    var Sender = user.Fname + ' ' + user.Lname; // ผู้ส่ง

    //set default form create new document.
    this.docNew.userid = userId;
    this.docNew.depart_government_id = departId;
    this.docNew.depart_id_user = departId;
    this.docNew.senderdepart = departName;
    this.docNew.sender = Sender;

    this.creatForm = this.fb.group({
      docdate: ['', Validators.required],
      secrets: ['1', Validators.required],
      rapid: ['1', Validators.required],
      doctype: ['1', Validators.required],
      depart_government_id: [departId, Validators.required],
      receiver: ['', Validators.required],
      headline: ['', Validators.required],
      doc_content: ['', Validators.required],
      comment: ['', Validators.required],
      //sender: ['', Validators.required],
      destroy_year: ['1', Validators.required]

    });

    //ประเภทหนังสือ
    this.docTypeList();

    // หน่วยงานส่วนราชการ
    this.deptGovernment(departId, univId, userType);
  }

  ngOnInit(): void {
      
  }

  async createPdf() {
    const pdfDoc = await PDFDocument.create()
    const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
  
    const page = pdfDoc.addPage()
    const { width, height } = page.getSize()
    const fontSize = 16
    page.drawText('Creating PDFs in JavaScript is awesome!', {
      x: 50,
      y: height - 4 * fontSize,
      size: fontSize,
      font: timesRomanFont,
      color: rgb(0, 0.53, 0.71),
    })
  
    const pdfBytes = await pdfDoc.save();
    this.saveByteArray('test.pdf', pdfBytes);
  }

  saveByteArray(reportName:any, byte:any) {
    var blob = new Blob([byte], {type: "application/pdf"});
    var link = document.createElement('a');
    link.href = window.URL.createObjectURL(blob);
    var fileName = reportName;
    link.download = fileName;
    link.click();
  };

  name = 'Angular 15';
  doc_content = '';

  config: AngularEditorConfig = {
    editable: true,
    spellcheck: true,
    height: '15rem',
    minHeight: '5rem',
    placeholder: 'Enter text here...',
    translate: 'no',
    defaultParagraphSeparator: 'p',
    defaultFontName: 'Arial',
    toolbarHiddenButtons: [
      ['bold']
    ],
    customClasses: [
      {
        name: "quote",
        class: "quote",
      },
      {
        name: 'redText',
        class: 'redText'
      },
      {
        name: "titleText",
        class: "titleText",
        tag: "h1",
      },
    ]
  };

  //ประเภทหนังสือ
  docTypeList() {
    this.dataService.apiDocTypeList()
      .subscribe((res: any) => {
        //console.log('form type: ',res);
        this.doctype_list = res;
      }),
      console.error();
  }

  // หน่วยงานส่วนราชการ
  deptGovernment(depart: any, univ: any, usertype: any) {
    //default depart_id
    this.dataService.apiDeptGovernment(depart, univ, usertype)
      .subscribe((res: any) => {
        this.deptgovernment_list = res;
        console.log('deptgovernment_list: ', res);
      })
  }

  createNewDocument1(angFormNew: any) {
    console.log('create new form: ', angFormNew);
  }

  createNewDocument() {
    console.log('create new document: ', this.docNew);
    this.httpClient
      .post<createDocForm>(environment.baseUrl + '/send/_sendto_document.php', this.docNew, {
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .subscribe((res: any) => {
        console.log('resig Form: ', res);
       
      });
  }

  exportPDF2(){

  }

}


export interface createDocForm {
  userid: Number;
  docdate: String;
  doctype: String;
  secrets: String;
  rapid: String;
  depart_id_user: Number;
  depart_government_id: Number;
  comment: string;
  headline: String;
  receiver: String;
  doc_content: any;
  sender: String;
  senderdepart: String;
  destroy_year: String;
}

export interface updateDocForm {
  userid: Number;
  docdate: String;
  doctype: String;
  secrets: String;
  rapid: String;
  depart_id_user: String;
  depart_government_id: Number;
  comment: string;
  headline: String;
  receiver: String;
  doc_content: any;
  sender: String;
  senderdepart: String;
  destroy_year: String;
}