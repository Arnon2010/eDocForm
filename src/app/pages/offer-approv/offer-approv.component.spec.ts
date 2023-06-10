import { ComponentFixture, TestBed } from '@angular/core/testing';

import { OfferApprovComponent } from './offer-approv.component';

describe('OfferApprovComponent', () => {
  let component: OfferApprovComponent;
  let fixture: ComponentFixture<OfferApprovComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ OfferApprovComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(OfferApprovComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
