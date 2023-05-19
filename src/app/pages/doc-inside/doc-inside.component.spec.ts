import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DocInsideComponent } from './doc-inside.component';

describe('DocInsideComponent', () => {
  let component: DocInsideComponent;
  let fixture: ComponentFixture<DocInsideComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ DocInsideComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(DocInsideComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
