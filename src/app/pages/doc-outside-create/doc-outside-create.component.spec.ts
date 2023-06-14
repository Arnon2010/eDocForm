import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocOutsideCreateComponent } from './doc-outside-create.component';

describe('DocOutsideCreateComponent', () => {
  let component: DocOutsideCreateComponent;
  let fixture: ComponentFixture<DocOutsideCreateComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DocOutsideCreateComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocOutsideCreateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
