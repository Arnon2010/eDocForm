import { Component, ElementRef, OnInit } from '@angular/core';
import { FormGroup, FormBuilder, Validators, NgForm } from '@angular/forms';
import { first, map } from 'rxjs/operators';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';
import { HttpClient } from '@angular/common/http';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-form-new',
  templateUrl: './form-new.component.html',
  styleUrls: ['./form-new.component.scss']
})
export class FormNewComponent implements OnInit {

  NavbarMenu:boolean = true;

  creatForm: FormGroup;
  // secrets:boolean = true;
  // rapid:boolean = true;
  doctype_list:any = [];
  deptgovernment_list:any = [];

  constructor(
    private fb: FormBuilder,
    private dataService: ApiService,
    private router: Router,
    private elementRef: ElementRef,
    private httpClient:HttpClient,
  ) {
    this.creatForm = this.fb.group({
      docDate: ['', Validators.required],
      Secrets: ['1',Validators.required],
      Rapid: ['1', Validators.required],
      doctypeId: ['1', Validators.required],
      departId_Government: ['1', Validators.required]
      
    });
  }

  ngOnInit(): void {
    this.NavbarMenu = false; //แสดงเมนูด้านข้าง

    //get user profile
    const token:any = this.dataService.getToken();
    let user = JSON.parse(token);
    var departId = user.depart_id;
    var userType = user.usertype_code;

    //ประเภทหนังสือ
    this.docTypeList();

    // หน่วยงานส่วนราชการ
    this.deptGovernment(departId);
  }

  //ประเภทหนังสือ
  docTypeList() {
    this.dataService.apiDocTypeList()
    .subscribe((res:any) =>{
      //console.log('form type: ',res);
      this.doctype_list = res; 
    }),
    console.error();
  }

  // หน่วยงานส่วนราชการ
  deptGovernment(depart:any) {
    this.dataService.apiDeptGovernment(depart)
    .subscribe((res:any) => {
      this.deptgovernment_list = res;
      console.log('deptgovernment_list: ',res);
    })
  }

  successNotification() {
    Swal.fire('', 'บันทึกข้อมูลสำเร็จ!', 'success');
  }

  notSuccessNotigication() {
    Swal.fire('', 'ขออภัยในความไม่สะดวกระบบลงทะเบียนไม่สามารถใช้งานได้ในขณะนี้ !', 'success');
  }

  infoAlertBox() {
    Swal.fire('ขออภัยในความไม่สะดวก!', 'ระบบลงทะเบียนไม่สามารถใช้งานได้ในขณะนี้', 'info')
  }

  errorAlertBox(a:any, b:any) {
    Swal.fire(a, b, 'error');
  }

  postdata(angForm1: any) {
    this.dataService
      .userlogin(angForm1.value.ePassport, angForm1.value.Password, angForm1.value.depart_allow)
      .pipe(first())
      .subscribe(
        (data) => {

          console.log(data);
          const redirect = this.dataService.redirectUrl
            ? this.dataService.redirectUrl
            : '/';

          //const redirect = '/register';
          this.router.navigate([redirect]);
          window.location.reload();
          //window.location.href = window.location.href; //reload page
        },
        (error) => {
          this.errorAlertBox('ชื่อผู้ใช้ หรือรหัสผ่านของท่านไม่ถูกต้อง!', 'กรุณาลองใหม่อีกครั้ง');
        }
      );
  }

}
