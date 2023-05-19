import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { HomeComponent } from './pages/home/home.component';
import { LoginComponent } from './pages/login/login.component';
import { SendComponent } from './pages/send/send.component';
import { ReceiveComponent } from './pages/receive/receive.component';
import { SearchComponent } from './pages/search/search.component';
import { SystemComponent } from './pages/system/system.component';
import { SettingComponent } from './pages/setting/setting.component';

import { HttpClientModule } from '@angular/common/http';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { HashLocationStrategy, LocationStrategy } from '@angular/common';

import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { RouterModule } from '@angular/router';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { FormNewComponent } from './pages/form-new/form-new.component';

import { AngularEditorModule } from '@kolkov/angular-editor';
import { DocCreateComponent } from './pages/doc-create/doc-create.component';
import { PdfLibComponent } from './pages/pdf-lib/pdf-lib.component';
import { NewdocComponent } from './pages/newdoc/newdoc.component';
import { DocInsideComponent } from './pages/doc-inside/doc-inside.component';
import { DocOusideComponent } from './pages/doc-ouside/doc-ouside.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    SendComponent,
    ReceiveComponent,
    SearchComponent,
    SystemComponent,
    SettingComponent,
    FormNewComponent,
    DocCreateComponent,
    PdfLibComponent,
    NewdocComponent,
    DocInsideComponent,
    DocOusideComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule,
    HttpClientModule,
    FormsModule,
    ReactiveFormsModule,
    BrowserAnimationsModule,
    RouterModule,
    NgbModule,
    AngularEditorModule
  ],
  providers: [{provide:LocationStrategy, useClass:HashLocationStrategy}],
  bootstrap: [AppComponent]
})
export class AppModule { }
