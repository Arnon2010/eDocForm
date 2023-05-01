import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-form-new',
  templateUrl: './form-new.component.html',
  styleUrls: ['./form-new.component.scss']
})
export class FormNewComponent implements OnInit {

  NavbarMenu:boolean = true;

  ngOnInit(): void {
    this.NavbarMenu = false; //แสดงเมนูด้านข้าง
  }

}
