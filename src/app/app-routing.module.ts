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

const routes: Routes = [
  {
    path: '',
    redirectTo: 'doc-create',
    pathMatch: 'full',
  },
  {path: 'home', component: HomeComponent},
  {path: 'login', component: LoginComponent},
  {path: 'receive', component: ReceiveComponent},
  {path: 'send', component: SendComponent},
  {path: 'search', component: SearchComponent},
  {path: 'setting', component: SettingComponent},
  {path: 'form-new', component: FormNewComponent},
  {path: 'doc-create', component: DocCreateComponent}

];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
