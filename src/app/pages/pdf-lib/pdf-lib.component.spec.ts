import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PdfLibComponent } from './pdf-lib.component';

describe('PdfLibComponent', () => {
  let component: PdfLibComponent;
  let fixture: ComponentFixture<PdfLibComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PdfLibComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PdfLibComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
