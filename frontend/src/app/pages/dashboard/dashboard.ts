import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import {
  Router,
  RouterLink,
  RouterLinkActive
} from '@angular/router';
import { Auth } from '../../services/auth';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [
    CommonModule,
    RouterLink,
    RouterLinkActive
  ],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.css'
})
export class Dashboard implements OnInit {

  user: any = null;

  loading = true;
  errorMessage = '';

  constructor(
    private auth: Auth,
    private router: Router
  ) {}

  ngOnInit(): void {

    // Check login immediately
    if (!this.auth.getToken()) {
      this.router.navigate(['/login']);
      return;
    }

    // Load user
    this.loadUser();
  }

  loadUser(): void {

    this.loading = true;
    this.errorMessage = '';

    this.auth.me().subscribe({

      next: (response) => {

        console.log('User loaded:', response);

        this.user = response?.data ?? response;

        this.loading = false;
      },

      error: (error) => {

        console.error('Dashboard error:', error);

        this.loading = false;

        // Unauthorized
        if (error.status === 401) {

          this.auth.clearToken();

          this.router.navigate(['/login']);

          return;
        }

        // Backend unavailable
        if (error.status === 0) {

          this.errorMessage =
            'Cannot connect to Laravel. Make sure the backend is running on port 8000.';

          return;
        }

        // Server error
        if (error.status >= 500) {

          this.errorMessage =
            'Laravel returned a server error. Please check the backend.';

          return;
        }

        this.errorMessage =
          'Unable to load your dashboard.';
      }
    });
  }


  // =========================
  // Navigation
  // =========================

  goToMentors(): void {
    this.router.navigate(['/mentors']);
  }

  goToRequests(): void {
    this.router.navigate(['/requests']);
  }

  goToSessions(): void {
    this.router.navigate(['/sessions']);
  }


  // =========================
  // Logout
  // =========================

  logout(): void {

    this.auth.logout().subscribe({

      next: () => {
        this.auth.clearToken();
        this.router.navigate(['/login']);
      },

      error: () => {
        this.auth.clearToken();
        this.router.navigate(['/login']);
      }
    });
  }

}