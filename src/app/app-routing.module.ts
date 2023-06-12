import { NewdocComponent } from './pages/newdoc/newdoc.component';
import { DocCreateComponent } from './pages/doc-create/doc-create.component';
import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HomeComponent } from './pages/home/home.component';
import { LoginComponent } from './pages/login/login.component';
import { ReceiveComponent } from './pages/receive/receive.component';
import { SendComponent } from './pages/send/send.component';
import { SearchComponent } from './pages/search/search.component';
import { SettingComponent } from './pages/setting/setting.component';
import { FormNewComponent } from './pages/form-new/form-new.component';
import { DocInsideComponent } from './pages/doc-inside/doc-inside.component';
import { DocOusideComponent } from './pages/doc-ouside/doc-ouside.component';
import { OfferComponent } from './pages/offer/offer.component';
import { OfferSignComponent } from './pages/offer-sign/offer-sign.component';
import { OfferApprovComponent } from './pages/offer-approv/offer-approv.component';

const routes: Routes = [
  {
    path: '',
    redirectTo: 'offer-approv/479567/123',
    pathMatch: 'full',
  },
  {path: 'home', component: HomeComponent},
  {path: 'login', component: LoginComponent},
  {path: 'receive', component: ReceiveComponent},
  {path: 'send', component: SendComponent},
  {path: 'search', component: SearchComponent},
  {path: 'setting', component: SettingComponent},
  {path: 'form-new', component: FormNewComponent},
  {path: 'doc-create', component: DocCreateComponent},
  {path: 'newdoc', component:NewdocComponent},
  {path: 'doc-inside', component:DocInsideComponent},
  {path: 'doc-outside', component:DocOusideComponent},
  {path: 'offer', component:OfferComponent},
  {path: 'offer-sign/:id', component:OfferSignComponent},
  {path: 'offer-approv/:id/:tposition', component:OfferApprovComponent}
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
