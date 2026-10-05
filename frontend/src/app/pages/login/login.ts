import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { Auth } from '../../services/auth';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [
    CommonModule,
    FormsModule
  ],
  templateUrl: './login.html',
  styleUrl: './login.css'
})
export class Login {

  email = '';
  password = '';

  errorMessage = '';
  successMessage = '';
  loading = false;

  constructor(
    private auth: Auth,
    private router: Router
  ) {}

  login(): void {

    this.errorMessage = '';
    this.successMessage = '';

    if (!this.email || !this.password) {
      this.errorMessage = 'Please enter your email and password.';
      return;
    }

    this.loading = true;

    this.auth.login({
      email: this.email,
      password: this.password
    }).subscribe({
      next: (response) => {

        this.loading = false;

        if (response.success && response.data?.token) {

          this.auth.saveToken(
            response.data.token
          );

          this.successMessage = 'Login successful!';

          setTimeout(() => {
            this.router.navigate(['/dashboard']);
          }, 500);

        } else {
          this.errorMessage = 'Login failed.';
        }
      },

      error: (error) => {

        this.loading = false;

        if (error.status === 422) {
          this.errorMessage =
            error.error?.message ||
            'Invalid email or password.';
        } else if (error.status === 401) {
          this.errorMessage =
            'Invalid email or password.';
        } else {
          this.errorMessage =
            'Unable to connect to the server.';
        }
      }
    });
  }
}