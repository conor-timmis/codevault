# CodeVault 📋

A modern, feature-rich pastebin application built with PHP that allows users to share code snippets and text content with syntax highlighting, privacy controls, and expiration options.

## 🚀 Features

### Core Functionality
- **User Authentication**: Secure registration, login, and session management
- **Paste Creation**: Create and share code snippets or text content
- **Syntax Highlighting**: Support for 20+ programming languages
- **Privacy Controls**: Choose between public, private, or unlisted visibility
- **Expiration Management**: Set custom expiration times or keep pastes forever
- **User Profiles**: Manage personal information and view paste history

### Advanced Features
- **View Tracking**: Monitor how many times your pastes have been viewed
- **Recent Pastes**: Browse recently created public pastes
- **Responsive Design**: Mobile-friendly interface using Bootstrap 5
- **Character Counter**: Real-time character counting for paste content
- **Secure Sessions**: Enhanced session security with regeneration and timeouts

## 🛠️ Technology Stack

- **Backend**: PHP 8.1+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Dependencies**: 
  - `vlucas/phpdotenv` - Environment variable management
- **Containerization**: Docker support included

## 📦 Installation & Setup

### Prerequisites
- PHP 8.1 or higher
- MySQL/MariaDB database
- Composer (for dependency management)
- Web server (Apache/Nginx) or use PHP's built-in server

### Method 1: Local Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd php-auth
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   ```bash
   cp .env.example .env
   # Edit .env with your database credentials
   ```

4. **Set up the database**
   ```bash
   # Create your database first, then run:
   php setup-database.php
   ```

5. **Start the development server**
   ```bash
   php -S localhost:8080 -t public
   ```

### Method 2: Docker Setup

1. **Build and run with Docker**
   ```bash
   docker build -t codevault .
   docker run -p 8080:8080 codevault
   ```

### Environment Configuration

Create a `.env` file in the root directory:

```env
DB_HOST=localhost
DB_NAME=php_auth
DB_USER=your_username
DB_PASS=your_password
DB_PORT=3306
```

## 🗄️ Database Schema

The application uses two main tables:

### Users Table
- `id` - Primary key
- `username` - Unique username (50 chars)
- `email` - Unique email address
- `password_hash` - Bcrypt hashed password
- `display_name` - Optional display name
- `last_login` - Last login timestamp
- `created_at/updated_at` - Timestamps
- `is_active` - Account status

### Pastes Table
- `id` - Primary key
- `paste_id` - Unique 8-character identifier
- `title` - Paste title
- `content` - Paste content (LONGTEXT)
- `language` - Programming language for syntax highlighting
- `visibility` - public/private/unlisted
- `expires_at` - Optional expiration timestamp
- `user_id` - Foreign key to users table
- `views` - View counter
- `created_at/updated_at` - Timestamps

## 🎯 Usage

### For Users
1. **Register/Login**: Create an account or log in to start creating pastes
2. **Create Paste**: Use the "New Paste" button to share your code or text
3. **Set Options**: Choose language, visibility, and expiration settings
4. **Share**: Get a direct link to share your paste with others
5. **Manage**: View and manage all your pastes from your profile

### For Visitors
- Browse recent public pastes on the homepage
- View public and unlisted pastes via direct links
- No account required for viewing content

## 📁 Project Structure

```
php-auth/
├── public/                 # Web-accessible files
│   ├── index.php          # Homepage
│   ├── create.php         # Paste creation
│   ├── paste.php          # View individual pastes
│   ├── login.php          # User login
│   ├── register.php       # User registration
│   ├── profile.php        # User profile
│   ├── my-pastes.php      # User's paste management
│   └── assets/            # CSS/JS files
├── includes/              # Core PHP libraries
│   ├── config.php         # Database configuration
│   ├── functions.php      # Core functions
│   ├── paste-functions.php # Paste-related functions
│   └── auth.php           # Authentication functions
├── sql/                   # Database schema files
│   ├── create_users_table.sql
│   └── create_pastes_table.sql
├── composer.json          # PHP dependencies
└── Dockerfile            # Docker configuration
```

## 🔧 Key Features Explained

### Authentication System
- Secure password hashing using PHP's `password_hash()`
- Session management with security enhancements
- Protected routes that require authentication

### Paste Management
- **Public**: Listed on homepage, searchable
- **Unlisted**: Accessible via direct link only
- **Private**: Only visible to the creator
- **Expiration**: Automatic cleanup of expired content

### Security Features
- Input sanitization and validation
- CSRF protection through session management
- SQL injection prevention using PDO prepared statements
- XSS protection through proper HTML escaping

## 🚀 Deployment

### Production Considerations
1. **Web Server**: Configure Apache/Nginx to serve from the `public/` directory
2. **Environment**: Set up production `.env` file with secure credentials
3. **Database**: Use a dedicated MySQL/MariaDB instance
4. **SSL**: Configure HTTPS for secure authentication
5. **Cleanup**: Set up a cron job to remove expired pastes

### Heroku Deployment
The project includes a `Procfile` for easy Heroku deployment:
```bash
git push heroku main
```

## 🤝 Contributing

This project was created as a learning exercise for PHP development. Feel free to:
- Report bugs or issues
- Suggest new features
- Submit pull requests
- Use it as a learning resource

## 📝 License

This project is open source and available under the MIT License.

---

**CodeVault** - Share code and text instantly with privacy and style! 🚀
