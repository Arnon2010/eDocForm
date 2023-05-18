import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NewdocComponent } from './newdoc.component';

describe('NewdocComponent', () => {
  let component: NewdocComponent;
  let fixture: ComponentFixture<NewdocComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ NewdocComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(NewdocComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
