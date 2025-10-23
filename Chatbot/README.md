# CleanWash Laundry Service Chatbot

A modern web-based customer service chatbot integration for CleanWash, a laundry service business. The project includes both customer-facing chat widget and an admin dashboard for managing customer interactions.

## Features

- 💬 Real-time chat widget integration
- 🔐 Secure admin dashboard
- 📱 Responsive design (mobile-friendly)
- 🔄 Auto-refresh messages
- 💾 Persistent chat history
- 👥 Multi-session support

## Tech Stack

- PHP 7.4+
- MySQL 5.7+
- jQuery 3.6.0
- TailwindCSS
- Remix Icons

## Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/cleanwash-chatbot.git
```

2. Import SQL schema
```bash
mysql -u root -p < sql/install.sql
mysql -u root -p < sql/users.sql
```

3. Configure database
```php
// Edit src/db_connect.php with your credentials
$conn = new mysqli("localhost", "your_user", "your_password", "cleanwash_db");
```

4. Set up in XAMPP
- Copy project to `C:\xampp\htdocs\Chatbot`
- Start Apache and MySQL
- Access at `http://localhost/Chatbot/public`

## Project Structure

```
Chatbot/
├── public/           # Public facing files
│   ├── index.php    # Landing page + chat widget
│   └── send_message.php
├── admin/           # Admin dashboard
│   ├── admin_login.php
│   └── admin_panel.php
├── src/            # Core functionality
│   ├── db_connect.php
│   └── session_init.php
└── sql/            # Database schema
    └── install.sql
```

## Usage

### Customer Chat Widget
- Click chat icon to open widget
- Messages auto-refresh every 4 seconds
- Session persists across page reloads

### Admin Dashboard
- Access at `/admin/admin_login.php`
- View all active chat sessions
- Respond to customer messages
- Monitor chat statistics

## Security Features

- Prepared SQL statements
- Password hashing
- Session management
- XSS protection

## Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

## Contact

Your Name - [@yourusername](https://twitter.com/yourusername)
Project Link: [https://github.com/yourusername/cleanwash-chatbot](https://github.com/yourusername/cleanwash-chatbot)