# LawyerConnect

A web-based platform for connecting clients with qualified lawyers, managing appointments, and handling legal service reviews. Built with plain PHP (procedural style) and MySQL.

---

## Features

### For Clients/Users
- **Register & Login:** Create an account and securely log in.
- **Search Lawyers:** Filter by location, service type, and experience.
- **View Lawyer Profiles:** See details, experience, ratings, and services.
- **Book Appointments:** Schedule consultations with lawyers.
- **Manage Appointments:** View, reschedule, or cancel your bookings.
- **Leave Reviews:** Rate and review lawyers after appointments.
- **Edit Profile:** Update your personal information and contact details.

### For Lawyers
- **Lawyer Dashboard:** Manage your appointments and availability.
- **Profile Management:** Update specialization, experience, and bio.
- **Service Management:** List legal services you offer.
- **View Reviews:** See client feedback and ratings.
- **Set Availability:** Define time slots for client bookings.

### For Admins
- **Admin Dashboard:** Overview of platform activity.
- **Manage Users:** Add, edit, or remove users and lawyers.
- **Manage Appointments:** View, edit, or delete any appointment.
- **Moderate Reviews:** Approve or remove client reviews.
- **Manage Services:** Add or update legal service categories.

### General
- **Contact & About Pages:** Static pages for information and inquiries.
- **Responsive Design:** Works on desktop and mobile devices.

## Database Schema

The app uses a MySQL database. Main tables include:

- **users:** Stores all users (clients, lawyers, admins) with roles.
- **lawyer_profiles:** Extended info for lawyers (specialization, experience, etc.).
- **lawyer_services:** Services offered by each lawyer.
- **appointments:** Booking records between clients and lawyers.
- **time_slots:** Lawyer availability for appointments.
- **reviews:** Client reviews for lawyers.
- **contact_inquiries:** Messages sent via the contact form.

See `schema.sql` for full details and sample data.

## Setup Instructions

### Prerequisites
- PHP 7.x or newer
- MySQL/MariaDB
- Web server (Apache, Nginx, etc.)

### Installation

1. **Clone the repository:**
   ```sh
   git clone <repo-url>
   cd lawyer-app
   ```

2. **Set up the database:**
   - Create a new MySQL database (default: `lawyer_db`).
   - Import the schema:
     ```sh
     mysql -u youruser -p lawyer_db < schema.sql
     ```

3. **Configure database connection:**
   - Edit `app/Models/db.php` with your DB credentials:
     ```php
     $host = 'localhost';
     $db   = 'lawyer_db';
     $user = 'root'; // change as needed
     $pass = '';
     ```

4. **Set up your web server:**
   - Point your document root to the project root or `public/` for assets.
   - Ensure PHP is enabled.

5. **Access the app:**
   - Open your browser and go to `http://localhost/` (or your configured domain).

## Usage
- Register as a user, lawyer, or log in as admin (default admin: `admin@lawyerapp.com`).
- Search for lawyers, book appointments, and manage your profile.
- Admins can manage users, appointments, and reviews from the admin dashboard.
---

## How to Run the Project Locally

You can use PHP's built-in web server for local development:

```sh
cd lawyer-app
php -S localhost:8000 -t .
```

- Make sure your database is running and configured as described above.
- Visit [http://localhost:8000](http://localhost:8000) in your browser.

For production, configure your web server (Apache, Nginx, etc.) to point to the project root or the appropriate public directory.
---

## File Structure

```
app/
  Controllers/   # PHP files for business logic and routing
    admin/       # Admin-specific controllers
    api/         # API endpoints for AJAX/JS
  Models/        # Database connection
  Views/         # Header/footer components
public/
  css/           # Stylesheets
  img/           # Images
  js/            # JavaScript files
schema.sql       # Database schema and sample data
index.php        # Entry point
```

## License
MIT (or your chosen license)
