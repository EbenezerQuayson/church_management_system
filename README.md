# Methodist Church Management System - PHP Version

A comprehensive church management system built with PHP and MySQL, featuring authentication, member management, event tracking, donations, attendance, and ministry organization.

## System Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx with mod_rewrite
- OpenSSL for password hashing

## Installation

1. **Set up the Database**
   - Create a new MySQL database: `church_management_system`
   - Import the database schema: `scripts/01-database-schema.sql`

2. **Configure Database Connection**
   - Edit `config/database.php` with your database credentials:
     \`\`\`php
     private $host = 'localhost';
     private $db_name = 'church_management_system';
     private $user = 'root';
     private $password = '';
     \`\`\`

3. **Set File Permissions**
   - Ensure the `/assets` directory is writable
   - Set proper permissions on directories

4. **Access the Application**
   - Login page: `http://yourserver.com/public/login.php`
   - Default credentials to be created by the administrator

## Folder Structure

\`\`\`
/
├── app/
│   ├── controllers/          # Business logic
│   ├── models/              # Database models
│   └── views/               # PHP templates
├── assets/
│   ├── css/                 # Stylesheets
│   ├── js/                  # JavaScript files
│   └── images/              # Images and logos
├── config/
│   ├── database.php         # Database connection
│   └── session.php          # Session management
├── public/
│   └── login.php            # Login page
├── scripts/
│   └── 01-database-schema.sql  # Database schema
└── README.md                # This file
\`\`\`

## Features

### Authentication
- Secure login with bcrypt password hashing
- Session management with timeout
- Role-based access control (Admin, Pastor, Leader, Member, Staff)

### Member Management
- Add, edit, delete member profiles
- Track member information (contact, address, emergency contacts)
- Member status tracking

### Attendance
- Quick attendance marking
- Date-based attendance records
- Attendance statistics and reporting

### Events
- Create and manage church events
- Set event capacity and location
- Track event status

### Donations
- Record member donations
- Track donation types (tithe, general offering, etc.)
- Monthly donation reports

### Ministries
- Manage church ministries
- Track ministry leaders and meeting times
- Ministry member associations

### Settings
- Customize church information
- Methodist Church branding (navy blue, red, gold colors)
- User profile management

## Security Features

- PDO prepared statements to prevent SQL injection
- Password hashing with bcrypt (PASSWORD_BCRYPT)
- Session timeout after 30 minutes of inactivity
- Role-based access control
- Input validation and sanitization
- CSRF protection ready (can be implemented)

## Database Schema

- **users** - User accounts with roles
- **roles** - User roles and permissions
- **members** - Church member information
- **attendance** - Attendance records
- **events** - Church events
- **ministries** - Ministry groups
- **ministry_members** - Ministry member associations
- **donations** - Donation records
- **settings** - System settings

## Default Colors

- Primary: #003DA5 (Methodist Navy Blue)
- Secondary: #CC0000 (Red)
- Accent: #F4C43F (Gold/Yellow)

## Support

For issues or feature requests, please contact the development team.

## License

© The Methodist Church Ghana - All Rights Reserved
