import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class Auth {

  private apiUrl = 'http://127.0.0.1:8000/api';

  constructor(private http: HttpClient) {}

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/register`, data);
  }

  login(data: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}/login`, data).pipe(
      tap((response) => {
        if (response?.token) {
          this.saveToken(response.token);
        }
      })
    );
  }

  me(): Observable<any> {
    const token = this.getToken();

    return this.http.get<any>(`${this.apiUrl}/me`, {
      headers: {
        Authorization: `Bearer ${token}`,
        Accept: 'application/json'
      }
    });
  }

  logout(): Observable<any> {
    const token = this.getToken();

    return this.http.post(
      `${this.apiUrl}/logout`,
      {},
      {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: 'application/json'
        }
      }
    );
  }

  saveToken(token: string): void {
    localStorage.setItem('token', token);
  }

  getToken(): string | null {
    return localStorage.getItem('token');
  }

  clearToken(): void {
    localStorage.removeItem('token');
  }

  isLoggedIn(): boolean {
    return !!this.getToken();
  }
}