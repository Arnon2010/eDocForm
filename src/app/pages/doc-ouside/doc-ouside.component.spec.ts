import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocOusideComponent } from './doc-ouside.component';

describe('DocOusideComponent', () => {
  let component: DocOusideComponent;
  let fixture: ComponentFixture<DocOusideComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DocOusideComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocOusideComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
