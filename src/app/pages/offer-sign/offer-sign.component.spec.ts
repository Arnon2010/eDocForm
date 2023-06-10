import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OfferSignComponent } from './offer-sign.component';

describe('OfferSignComponent', () => {
  let component: OfferSignComponent;
  let fixture: ComponentFixture<OfferSignComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ OfferSignComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OfferSignComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
