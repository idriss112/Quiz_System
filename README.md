#  Quiz Management System

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white)
![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![AJAX](https://img.shields.io/badge/AJAX-0769AD?style=for-the-badge&logo=ajax&logoColor=white)

A modern, secure, and user-friendly web-based quiz management system built with pure PHP and MySQL.

[Features](#-features) • [Technologies](#-technologies-used) • [Installation](#-installation) • [Usage](#-usage) • [Security](#-security-features)

</div>

---

##  Table of Contents

- [About](#-about)
- [Features](#-features)
- [Technologies Used](#-technologies-used)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Database Setup](#-database-setup)
- [Usage](#-usage)
- [Key Pages](#-key-pages)
- [Security Features](#-security-features)
- [API Endpoints](#-api-endpoints)
- [Contributing](#-contributing)
- [Future Enhancements](#-future-enhancements)
- [License](#-license)
- [Author](#-author)

---

##  About

The **Quiz Management System** is a full-featured web application designed to facilitate online quiz creation, management, and participation. Built entirely with pure PHP, MySQL, and vanilla JavaScript, this system demonstrates clean coding practices and modern web development techniques without relying on frameworks.

### Key Highlights

-  **Secure Authentication**: Bcrypt password hashing with role-based access control
-  **Multi-Role System**: Separate dashboards for administrators and participants
-  **Real-Time Updates**: AJAX-powered interactions for seamless user experience
-  **Quiz Management**: Complete CRUD operations for quizzes and questions
-  **Results Analytics**: Detailed performance tracking and export functionality
-  **Modern UI/UX**: Clean, responsive design with custom CSS
-  **Session & Cookie Management**: Persistent login with secure session handling
-  **Responsive Design**: Mobile-first approach for all devices

---

##  Features

### For Administrators 

-  **User Management**
  - Create, view, update, and delete user accounts
  - Assign roles (Admin/Participant)
  - View paginated user lists with navigation
  - Monitor user activity and statistics
  - Bulk user operations

-  **Quiz Management**
  - Create unlimited quizzes with custom titles
  - Add, edit, and delete questions dynamically
  - Import questions from external sources
  - Manage quiz settings (time limits, attempts, visibility)
  - Organize quizzes by categories

-  **Question Bank**
  - Build comprehensive question libraries
  - Support for multiple question types
  - Import/export questions functionality
  - Reusable questions across multiple quizzes

-  **Results & Analytics**
  - View all participant results
  - Export results to various formats
  - Generate detailed performance reports
  - Track quiz completion rates
  - Monitor individual and group performance

-  **Dashboard**
  - Real-time statistics overview
  - Total users, quizzes, and participants count
  - Quick access to key functions
  - Activity monitoring

### For Participants 

-  **Quiz Taking Experience**
  - Browse available quizzes in quiz list
  - Take quizzes with clean interface
  - Auto-save progress during quiz
  - Timer functionality (if enabled)
  - Immediate feedback on submission

-  **Personal Dashboard**
  - View assigned and available quizzes
  - Track completed quizzes history
  - See scores and performance
  - Access quiz results and feedback

-  **Profile Management**
  - Update personal information
  - Change password securely
  - View quiz history
  - Track personal statistics

### Common Features 

-  **Authentication System**
  - Secure login and registration
  - Password recovery (if implemented)
  - Role-based access control
  - Session persistence with "Remember Me"
  - Auto-logout on inactivity

-  **Responsive Interface**
  - Works on desktop, tablet, and mobile
  - Consistent design across all pages
  - Intuitive navigation
  - Modern and clean UI

-  **AJAX Integration**
  - Seamless page updates without reload
  - Real-time form validation
  - Dynamic content loading
  - Smooth user interactions

---

##  Technologies Used

### Backend Technologies
- **PHP 7.4+**: Core server-side programming language
- **MySQL 5.7+**: Relational database management system
- **PDO (PHP Data Objects)**: Database abstraction layer for secure queries
- **Object-Oriented Programming**: Clean, maintainable code structure

### Frontend Technologies
- **HTML5**: Semantic markup and structure
- **CSS3**: Modern styling with custom properties and animations
- **JavaScript (Vanilla)**: Client-side interactivity and validation
- **AJAX**: Asynchronous data loading without page refresh
- **jQuery**: DOM manipulation and AJAX requests (if used)

### Design & UI
- **Custom CSS**: Handcrafted responsive design
- **Google Fonts (Inter)**: Clean, modern typography
- **Responsive Layout**: Mobile-first approach
- **CSS Flexbox/Grid**: Modern layout techniques

### Architecture & Patterns
- **OOP (Object-Oriented Programming)**: Classes for User, Auth, Database
- **MVC-inspired Structure**: Separation of concerns
- **Session Management**: Custom SessionManager class
- **Cookie Management**: Custom CookieManager class
- **Authentication Layer**: Dedicated Auth class

### Development Tools
- **XAMPP/WAMP**: Local development environment
- **phpMyAdmin**: Database administration
- **Git**: Version control
- **VS Code**: Code editor

---

##  System Requirements

- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher
- **Apache/Nginx**: Web server with mod_rewrite enabled
- **Browser**: Modern browser (Chrome, Firefox, Safari, Edge)

---

##  Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/idriss112/quiz-system.git
cd quiz-system
```

### Step 2: Set Up Local Server

If using XAMPP:
1. Move the project folder to `C:\xampp\htdocs\quiz-system`
2. Start Apache and MySQL from XAMPP Control Panel

If using WAMP:
1. Move the project folder to `C:\wamp64\www\quiz-system`
2. Start all services from WAMP Manager

### Step 3: Configure Database Connection

Edit `config/Database.php` with your database credentials:

```php
<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'quiz_system';
    private $username = 'root';
    private $password = '';  // Your MySQL password
    public $conn;

    // ... rest of the code
}
```

---

##  Database Setup

### Option 1: Import SQL File

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `quiz_system`
3. Import the `database/quiz_system.sql` file
4. Verify tables are created successfully

### Option 2: Manual Setup

Run the following SQL commands in phpMyAdmin:

```sql
-- Create Database
CREATE DATABASE quiz_system;
USE quiz_system;

-- Create Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'participant') DEFAULT 'participant',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create initial admin user (password: admin123)
INSERT INTO users (nom, email, mot_de_passe, role) VALUES
('Admin', 'admin@quiz.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Add more tables as needed for quizzes, questions, results, etc.
```

---

##  Usage

### Access the Application

1. Navigate to: `http://localhost/quiz-system`
2. You'll see the login page

### Default Credentials

**Administrator Account:**
- Email: `admin@quiz.com`
- Password: `admin123`
- Role: Select "Administrateur"

**Test Participant Account:**
- Email: `participant@quiz.com`
- Password: `participant123`
- Role: Select "Utilisateur"

### First Steps

1. **Login** with admin credentials
2. **Navigate to User Management** to create more users
3. **Create Quizzes** from the admin dashboard
4. **Add Questions** to your quizzes
5. **Test** by logging in as a participant

##  Security Features

### Password Security
```php
// Passwords are hashed using bcrypt with cost factor 10
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Secure password verification
if (password_verify($inputPassword, $storedHash)) {
    // Authentication successful
}
```

### SQL Injection Prevention
```php
// All database queries use PDO prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
```

### XSS (Cross-Site Scripting) Protection
```php
// All user inputs are sanitized and escaped
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

### Session Security
- **Session Regeneration**: New session ID on login
- **Session Timeout**: Auto-logout after inactivity
- **Secure Session Storage**: HttpOnly and Secure flags
- **Role Verification**: Check user permissions on every request

### Additional Security Measures
- **CSRF Protection**: Token validation on form submissions (if implemented)
- **Input Validation**: Server-side validation for all inputs
- **Error Handling**: Generic error messages to prevent information disclosure
- **Access Control**: Role-based permissions enforced on all pages

---

##  Key Pages

### Public Pages
- **`index.php`** - Homepage and entry point
- **`login.php`** - User authentication page
- **`register.php`** - New user registration
- **`logout.php`** - Session termination handler

### Admin Pages
- **`admin_dashboard.php`** - Administrator control panel
- **`admin_manage_users.php`** - User management interface
- **`admin_manage_questions.php`** - Question bank management
- **`admin_manage_quiz.php`** - Quiz creation and editing
- **`admin_results.php`** - View all quiz results

### Participant Pages
- **`dashboard.php`** - Participant home dashboard
- **`quiz_list.php`** - Browse available quizzes
- **`select_quiz.php`** - Choose a quiz to attempt
- **`play_quiz.php`** - Take quiz interface
- **`user_results.php`** - View personal results

### Shared Components
- **`header.php`** - Main navigation header
- **`header_guest.php`** - Header for non-authenticated users
- **`header_user.php`** - Header for authenticated users

### Utility Pages
- **`profile.php`** - User profile management
- **`profile-user.php`** - Participant profile view
- **`profile-test.php`** - Profile testing page
- **`manage-quiz.php`** - Quiz management interface
- **`import_questions.php`** - Bulk question import
- **`export_results.php`** - Results export functionality
- **`qsttest.php`** - Question testing page

---

##  API Endpoints

### AJAX Integration
The system uses AJAX for dynamic interactions located in the `ajax/` directory:

```javascript
// Example: Check if email exists during registration
$.ajax({
    url: 'ajax/check_email.php',
    method: 'POST',
    data: { email: userEmail },
    success: function(response) {
        // Handle response
    }
});

// Example: Load quiz questions dynamically
$.ajax({
    url: 'ajax/load_questions.php',
    method: 'GET',
    data: { quiz_id: quizId },
    success: function(questions) {
        // Render questions
    }
});
```

**Common AJAX Endpoints** (examples):
- **Email Validation**: Check email availability
- **Quiz Loading**: Fetch quiz data without reload
- **Auto-save**: Save quiz progress periodically
- **Live Search**: Filter users/quizzes in real-time
- **Status Updates**: Update quiz/user status

---

##  Contributing

Contributions are welcome! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch**: `git checkout -b feature/AmazingFeature`
3. **Commit your changes**: `git commit -m 'Add some AmazingFeature'`
4. **Push to the branch**: `git push origin feature/AmazingFeature`
5. **Open a Pull Request**

### Contribution Guidelines

- Follow PSR-12 coding standards for PHP
- Write clear, commented code
- Test your changes thoroughly
- Update documentation as needed
- Keep commits atomic and well-described

---

##  Future Enhancements

- [ ] **Email Notifications** - Send quiz reminders and results
- [ ] **Password Reset** - Forgot password functionality
- [ ] **Quiz Timer** - Add countdown timer for timed quizzes
- [ ] **Question Types** - Multiple choice, true/false, essay questions
- [ ] **Quiz Categories** - Organize quizzes by subject/topic
- [ ] **Leaderboard** - Top performers ranking system
- [ ] **Quiz Attempts** - Limit or allow multiple attempts
- [ ] **Certificate Generation** - Automatic PDF certificates for passing scores
- [ ] **Dark Mode** - Theme switcher for better UX
- [ ] **Advanced Analytics** - Detailed performance graphs and insights
- [ ] **Question Randomization** - Shuffle questions and answers
- [ ] **Quiz Scheduling** - Set start/end dates for quizzes
- [ ] **Bulk Operations** - Import users and questions via CSV
- [ ] **API Development** - RESTful API for mobile apps
- [ ] **Real-time Updates** - WebSocket for live quiz sessions
- [ ] **Multi-language Support** - Internationalization (i18n)
- [ ] **Quiz Templates** - Pre-made quiz formats
- [ ] **Social Sharing** - Share results on social media

---

##  Known Issues

- Session timeout may need adjustment based on server settings
- Large file uploads may require PHP configuration changes
- Some older browsers may not support all CSS features

---

##  License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

##  Author

**Driss Laaziri**

- Email: idrsslaaziri@gmail.com
- GitHub: [@idriss112](https://github.com/idriss112)
- LinkedIn: [Connect with me](https://www.linkedin.com/in/idrisslzr/)

---

##  Acknowledgments

- **PHP Community** for excellent documentation and resources
- **MySQL** for robust database management
- **Stack Overflow** for community support and solutions
- **Google Fonts** for the Inter font family
- **Open Source Community** for inspiration and best practices


---

##  Show Your Support

If you found this project helpful or interesting, please consider:

-  **Starring the repository** on GitHub
-  **Forking the project** to create your own version
-  **Sharing with others** who might find it useful
-  **Reporting bugs** to help improve the system
-  **Suggesting features** for future enhancements

---

<div align="center">

**Made with ❤️ for education and learning**

**Built with pure PHP, MySQL, and passion for clean code**

[⬆ Back to Top](#-quiz-management-system)

</div>
