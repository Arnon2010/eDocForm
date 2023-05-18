import { Component, ViewChild, ElementRef } from '@angular/core';
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib';


@Component({
  selector: 'app-pdf-lib',
  templateUrl: './pdf-lib.component.html',
  styleUrls: ['./pdf-lib.component.scss']
})
export class PdfLibComponent {

  @ViewChild('textEditor') textEditor: ElementRef | undefined;


  
}
