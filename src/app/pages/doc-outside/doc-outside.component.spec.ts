import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocOutsideComponent } from './doc-outside.component';

describe('DocOutsideComponent', () => {
  let component: DocOutsideComponent;
  let fixture: ComponentFixture<DocOutsideComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DocOutsideComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocOutsideComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
