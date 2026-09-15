# 🎯 Habit Tracker Web Application

An interactive web application to track daily habits and monitor progress.

## 🛠️ Technologies Used
- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend:** PHP, MySQL
- **Server:** XAMPP (Apache + MySQL)

## ✨ Features
- User Registration & Login (with password hashing)
- Add, track, and delete personal habits
- Real-time progress bar updates using AJAX
- Contact form with data storage
- Responsive design for all screen sizes
- JavaScript validation, animations, and dynamic content

## 📂 Folder Structure
habit_tracker/
├── css/
│ └── style.css
├── js/
│ └── script.js
├── images/
├── includes/
│ └── db.php
├── auth/
│ ├── register.php
│ ├── login.php
│ └── logout.php
├── index.php
├── dashboard.php
├── contact.php
├── database.sql
└── README.md


## 🚀 Setup Instructions

### 1. Install XAMPP
Download and install [XAMPP](https://www.apachefriends.org/).

### 2. Start Services
Open XAMPP Control Panel → Start **Apache** and **MySQL**.

### 3. Copy Project
Place the `habit_tracker` folder inside `C:\xampp\htdocs\`.

### 4. Import Database
- Open `http://localhost/phpmyadmin`
- Create a new database named `habit_tracker`
- Click **Import** → Choose `database.sql` → Click **Go**

### 5. Configure Database Connection
If needed, edit `includes/db.php`:
```php
$host = "localhost";
$username = "root";
$password = "";
$database = "habit_tracker";