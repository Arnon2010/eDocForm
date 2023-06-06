import { fonts } from './../../config/pdfFonts';
import { Component, ElementRef, OnInit, ViewChild } from '@angular/core';
import { FormGroup, FormBuilder, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { AngularEditorConfig } from '@kolkov/angular-editor';

import { DomSanitizer, SafeHtml } from '@angular/platform-browser';

declare var require: any;

import { PDFDocument, PDFForm, StandardFonts, PDFFont, rgb } from 'pdf-lib';
const fontkit = require("@pdf-lib/fontkit");
const htmlToPdfmake = require("html-to-pdfmake");
//const fs = require('file-system');
//const fs = require("fs");

import { jsPDF } from "jspdf";
import "jspdf-autotable";

import html2canvas from 'html2canvas';

@Component({
  selector: 'app-doc-create',
  templateUrl: './doc-create.component.html',
  styleUrls: ['./doc-create.component.scss']
})
export class DocCreateComponent implements OnInit {

  @ViewChild('pdfTable') pdfTable!: ElementRef;
  @ViewChild('textEditor') textEditor!: ElementRef;

  creatForm: FormGroup;
  // secrets:boolean = true;
  // rapid:boolean = true;
  doctype_list: any = [];
  deptgovernment_list: any = [];
  takeposition_list: any = [];
  position_list: any = [];

  isChecked: boolean = true; // Set the initial state of the checkbox

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
    doc_content_wish: '',
    doc_content_conc: '',
    tposition_id: '',
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
    private sanitizer: DomSanitizer
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
      doc_content_wish: ['', Validators.required],
      doc_content_conc: ['', Validators.required],
      comment: ['', Validators.required],
      tposition_id: ['', Validators.required],
      //sender: ['', Validators.required],
      destroy_year: ['1', Validators.required]

    });

    //ประเภทหนังสือ
    this.docTypeList();

    // หน่วยงานส่วนราชการ
    this.deptGovernment(departId, univId, userType);

    // ผู้ลงนาม
    this.takePosition(departId);


    this.testStd();
  }

  ngOnInit(): void {

  }

  sanitizeHtml(html: string): SafeHtml {
    return this.sanitizer.bypassSecurityTrustHtml(html);
  }

  public openPDF(): void {
    let DATA: any = document.getElementById('htmlData');
    html2canvas(DATA).then((canvas) => {
      let fileWidth = 208;
      let fileHeight = (canvas.height * fileWidth) / canvas.width;
      const FILEURI = canvas.toDataURL('image/png');
      let PDF = new jsPDF('p', 'mm', 'a4');
      let position = 0;
      PDF.addImage(FILEURI, 'PNG', 0, position, fileWidth, fileHeight);
      PDF.save('angular-demo.pdf');
    });
  }


  header = [['ID', 'Name', 'Email', 'Profile']]
  tableData = [[1, 'Bhuban', 'Bhuban@gmail.com', 'Developer'],
  [2, 'rinkesh', 'rinkesh@yahoo.com', 'Sales'],
  [3, 'arpit', 'arpit@yahoo.com', 'Sales'],
  [4, 'abdul', 'abdul@yahoo.com', 'Finance'],
  [5, 'Angel', 'Angel@yahoo.com', 'Marketing'],
  [6, 'อานนท์', 'arnn.l@rmutsv.ac.th', 'โปรแกรมเมอร์']]

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

  saveByteArray(reportName: any, byte: any) {
    var blob = new Blob([byte], { type: "application/pdf" });
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

  countWordsInLine() {
    const lines = this.docNew.comment.split('\n');
    const lineCount = lines.length;

    for (let i = 0; i < lineCount; i++) {
      const words = lines[i].trim().split(' ');
      const wordCount = words.length;
      console.log(`Number of words in line ${i + 1}: ${wordCount}`);
    }
  }

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

  // ผู้ลงนาม และตำแหน่ง
  takePosition(departid: any) {
    //default depart_id
    this.dataService.apiTakePosition(departid)
      .subscribe((res: any) => {
        this.takeposition_list = res;
        console.log('takeposition_list: ', res);
      })
  }

  // ตำแหน่ง
  Position(tposition_id: any, depart_id: any) {
    //default depart_id
    this.dataService.apiPosition(tposition_id, depart_id)
      .subscribe((res: any) => {
        this.position_list = res;
        console.log('position_list: ', res);
      })
  }

  // เพิ่มเรียนหน่วยงานที่เกี่ยวข้อง
  addReceiver() {
    
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
        //this.generatePdfFile();
        this.createPDF();
        this.router.navigate(['/doc-ouside']);

      });
  }

  createPDF() {
    //console.log('create new document: ', this.docNew);
    this.httpClient
      .post<createDocForm>(environment.baseUrl + '/send/_create_pdf_document.php', this.docNew, {
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .subscribe((res: any) => {
        console.log('resig Form: ', res);
        //this.generatePdfFile();

        this.router.navigate(['/doc-ouside']);

      });
  }

  generatePdfFile() {
    var pdf = new jsPDF("p", "mm", "a4");

    var doc_content = htmlToPdfmake(this.docNew.doc_content);
    const lines = this.docNew.comment.split('\n'); //count line
    const lineCount = lines.length;
    console.log('doc_content :', doc_content);

    //const editorText = this.textEditor.nativeElement.innerText;
    //console.log('doc_content: ',doc_content[0].text);
    const fontUrl = './../../../assets/fonts/ThaiFonts/THSarabun.ttf'; // Adjust the path to your font file
    // Load the font

    pdf.addFont(fontUrl, 'THSarabun', 'normal');
    // Set the font for the text
    pdf.setFont('THSarabun');
    pdf.setFontSize(29);
    pdf.text('บันทึกข้อความ', 60, 20);
    pdf.setFontSize(16);

    pdf.text('ส่วนราชการ', 10, 30);
    pdf.text('ที่', 10, 40);
    pdf.text('เรื่อง', 10, 50);
    pdf.text('เรียน', 10, 60);

    // pdf.text('This is a new line.\n', 10, 35); // Add a new line
    // pdf.text('This is another line.', 10, 40);
    // pdf.text('\n', 10, 45); // Add a new line

    var splitTitle = pdf.splitTextToSize(this.docNew.doc_content, 180);
    pdf.text(splitTitle, 20, 70);

    this.countWordsInLine();

    // (pdf as any).autoTable({
    //   head: this.header,
    //   body: this.tableData,
    //   theme: 'plain',
    //   didDrawCell: (data: {column: {index: any;};}) =>{
    //     console.log(data.column.index);
    //   }
    // });

    // Open PDF document in browser's new tab
    pdf.output('dataurlnewwindow');
    // Download PDF doc
    pdf.save('table.pdf');
  }

  //
  testStd() {

    this.httpClient
      .post<createDocForm>('https://sis.rmutsv.ac.th/sis/api/pdo_mysql_std_dev.php', { "opt": "readone", "g_student": "163401040079" }, {
        headers: {
          'Content-Type': 'application/json',
        },
      })
      .subscribe((res: any) => {
        console.log('resig student: ', res);


      });
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
  doc_content_wish: any;
  doc_content_conc: any;
  tposition_id: any;
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
  doc_content_wish: any;
  doc_content_conc: any;
  tposition_id: any;
  sender: String;
  senderdepart: String;
  destroy_year: String;
}