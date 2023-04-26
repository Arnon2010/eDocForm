import { Component, OnInit, ElementRef } from '@angular/core';
import { FormGroup, FormBuilder, Validators, NgForm } from '@angular/forms';
import { first } from 'rxjs/operators';
import { Router } from '@angular/router';
import { ApiService } from '../../api.service';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  angForm: FormGroup;
  constructor(
    private fb: FormBuilder,
    private dataService: ApiService,
    private router: Router,
    private elementRef: ElementRef
  ) {
    this.angForm = this.fb.group({
      ePassport: ['',Validators.required],
      Password: ['', Validators.required]
    });
  }
  ngAfterViewInit() {
    this.elementRef.nativeElement.ownerDocument.body.style.backgroundColor =
      '#f0f8ff ';
  }

  ngOnInit() {
    
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
      .userlogin(angForm1.value.ePassport, angForm1.value.Password)
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

  get email() {
    return this.angForm.get('email');
  }

  get password() {
    return this.angForm.get('password');
  }
}
