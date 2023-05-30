
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { AngularEditorConfig } from '@kolkov/angular-editor';

declare var require: any;

//declare var vfsFonts:any;
//import * as variable from 'vfsFonts';

// import the pdfmake library
// import * as pdfMake from 'pdfmake/build/pdfmake';
// import * as pdfFonts from 'pdfmake/build/vfs_fonts';

// (pdfMake as any).vfs = pdfFonts.pdfMake.vfs;

import { PDFDocument, StandardFonts, rgb } from 'pdf-lib';

// window.pdfMake = require('pdfmake/build/pdfmake.min');
// var vfs = require('pdfmake/build/vfs_fonts');
// window.pdfMake.vfs = vfs.pdfMake.vfs;

// import { defaultStyle, styles } from './../../config/customStyle';
// import { fonts } from './../../config/pdfFonts';



const htmlToPdfmake = require("html-to-pdfmake");

// (pdfMake as any).fonts = {
//       // Default font should still be available
//       THSarabunNew: {
//         normal: 'THSarabunNew.ttf',
//         bold: 'THSarabunNew Bold.ttf',
//         italics: 'THSarabunNew Italic.ttf',
//         bolditalics: 'THSarabunNew BoldItalic.ttf'
//       },
//       THSarabun: {
//           normal: 'THSarabun.ttf',
//           bold: 'THSarabun Bold.ttf',
//           italics: 'THSarabun Italic.ttf',
//           bolditalics: 'THSarabun Bold Italic.ttf'
//       }
//     };

@Component({
  selector: 'app-form-new',
  templateUrl: './form-new.component.html',
  styleUrls: ['./form-new.component.scss']
})
export class FormNewComponent implements OnInit {

  @ViewChild('pdfTable') pdfTable!: ElementRef;
  @ViewChild('textEditor') textEditor!: ElementRef;


  creatForm: FormGroup;
  // secrets:boolean = true;
  // rapid:boolean = true;
  doctype_list: any = [];
  deptgovernment_list: any = [];


  docNew: createDocForm = {
    userid: 0,
    docdate: '19/05/2023',
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

  title: string | undefined;
  pdfContent: {
    content: {
      // layout: 'lightHorizontalLines', // optional
      // table: {
      //   // headers are automatically repeated if the table spans over multiple pages
      //   // you can declare how many rows should be treated as headers
      //   headerRows: 1,
      //   widths: ['*', 'auto', 100, '*'],
      //   body: [
      //     ['', 'ทดสอบระบบ', 'Third', 'The last one'],
      //     ['Value 1', 'Value 2', 'Value 3', 'Value 4'],
      //     [{ text: 'Bold value', bold: true }, 'Val 2', 'Val 3', 'Val 4']
      //   ]
      // }
      text: string; font: string;
    }[];
  } | undefined;

  
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

    // this.creatForm = new FormGroup({
    //   depart_government_id: new FormControl(null),
    // });

    // this.creatForm.controls['depart_government_id'].setValue(this.default, {onlySelf: true});

    //ประเภทหนังสือ
    this.docTypeList();

    // หน่วยงานส่วนราชการ
    this.deptGovernment(departId, univId, userType);

    //(window as any).pdfMake.vfs = pdfFonts.pdfMake.vfs;

  }

  ngOnInit(): void {

  }

  successNotification() {
    Swal.fire('', 'บันทึกข้อมูลสำเร็จ!', 'success');
  }

  infoAlertBox() {
    Swal.fire('ขออภัยในความไม่สะดวก!', 'ระบบลงทะเบียนไม่สามารถใช้งานได้ในขณะนี้', 'info')
  }

  errorAlertBox(a: any, b: any) {
    Swal.fire(a, b, 'error');
  }

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

    async createPdf () {
      // Create a new PDFDocument
      const pdfDoc = await PDFDocument.create();

      var html = htmlToPdfmake(this.docNew.doc_content);

      // Embed the Times Roman font
      const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)

      // Add a blank page to the document
      const page = pdfDoc.addPage()

      // Get the width and height of the page
      const { width, height } = page.getSize()

      // Draw a string of text toward the top of the page
      const fontSize = 30
      page.drawText('test', {
        x: 50,
        y: height - 4 * fontSize,
        size: fontSize,
        font: timesRomanFont,
        color: rgb(0, 0.53, 0.71),
      })

      // Serialize the PDFDocument to bytes (a Uint8Array)
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


    printPDF(){
      var pdfMake = require('pdfmake/build/pdfmake.min.js');
      var pdfFonts = require('pdfmake/build/vfs_fonts.js');
      
      pdfMake.vfs = pdfFonts.pdfMake.vfs;
    
      pdfMake.fonts = {
        THSarabunNew: {
          normal: 'THSarabunNew.ttf',
          bold: 'THSarabunNew-Bold.ttf',
          italics: 'THSarabunNew-Italic.ttf',
          bolditalics: 'THSarabunNew-BoldItalic.ttf'
        },
        Roboto: {
          normal: 'Roboto-Regular.ttf',
          bold: 'Roboto-Medium.ttf',
          italics: 'Roboto-Italic.ttf',
          bolditalics: 'Roboto-MediumItalic.ttf'
        }
      }

      var doc_content = htmlToPdfmake(this.docNew.doc_content);
    
      var docDefinition = {
        pageSize: 'A4', // Set the page size to A4
        content: [
          { text: 'บันทึกข้อความ', fontSize: 29,
            absolutePosition: { x: 100, y: 100 }, 
          },
          doc_content,
        ],defaultStyle: {
            font: 'THSarabunNew'
          }
      };
      pdfMake.createPdf(docDefinition).open()
    
    }
  
  public exportPDF() {
    const pdfTable = this.pdfTable.nativeElement;
    //var html = htmlToPdfmake(pdfTable.innerHTML);
    var html = htmlToPdfmake(this.docNew.doc_content);
    //console.log('html: ',html);
    const documentDefinition = {
      content: html, defaultStyle:{
        font: "THSarabun", // Any already loaded font
      }
    };

    //pdfMake.createPdf(documentDefinition).download();
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
