# College Event Management System (EventMS)

**Open Source Systems Lab – CAT II Project**

| Item | Details |
|---|---|
| Title | College Event Management System |
| Objective | To develop a web-based application that lets the college admin create and manage events, and lets students view events and register for them online, replacing manual paper-based registration. |
| Frontend | HTML5, CSS3, Bootstrap 5, JavaScript |
| Backend | PHP 8 |
| Database | MongoDB (NoSQL, document database) |
| Server | XAMPP (Apache + PHP) |
| Connector | `mongodb` PHP extension + `mongodb/mongodb` library (already included in `vendor/`) |

---

## 1. Software to install (all free and open source)

1. **XAMPP** (PHP 8.1 – 8.4) – https://www.apachefriends.org
2. **MongoDB Community Server** – https://www.mongodb.com/try/download/community
   (tick "Install MongoDB Compass" during setup to view your data)

## 2. Enable MongoDB in PHP (most important step)

1. Open `http://localhost/dashboard/phpinfo.php` and note:
   - **PHP Version** (e.g. 8.2)
   - **Thread Safety** → `enabled` means TS
   - **Architecture** → x64
2. Go to https://github.com/mongodb/mongo-php-driver/releases and open release **1.21.x**
   (the bundled library needs extension version **1.21 or newer, below 2.0**).
3. Download the Windows zip matching your PHP, e.g. `php_mongodb-1.21.x-8.2-ts-vs16-x64.zip`
   (8.2 = your PHP version, ts = Thread Safe, x64).
4. Extract and copy **`php_mongodb.dll`** into `C:\xampp\php\ext\`
5. Open `C:\xampp\php\php.ini`, add this line near the other `extension=` lines:
   ```
   extension=mongodb
   ```
6. Restart **Apache** from the XAMPP Control Panel.
7. Check phpinfo again — you should now see a **mongodb** section.

> If Apache fails to start, the DLL does not match your PHP version / TS / x64. Download the correct one.

## 3. Run the project

1. Copy the `eventms` folder into `C:\xampp\htdocs\`
2. Make sure the MongoDB service is running (it starts automatically on Windows after install).
3. Open **http://localhost/eventms/setup.php** once. It creates:
   - the database `eventms` and collections `users`, `events`, `registrations`
   - unique indexes
   - the admin account and 5 sample events
4. Open **http://localhost/eventms/** and login.

**Admin login:** `admin@college.edu` / `admin123`
**Student:** click *Sign Up* to create an account.

> The `vendor/` folder is already included, so Composer is optional.
> If you prefer, delete `vendor/` and run `composer install`.

---

## 4. Modules

**Admin**
- Dashboard: total events, upcoming events, students, registrations, most popular events, recent registrations
- Manage Events: add, view, search, filter, edit, delete
- Participants: view list per event, mark attendance, remove participant, print list
- Students: view all students with event counts, delete student

**Student**
- Sign up / Login / Logout
- View upcoming events with seats left (live progress bar)
- Register for an event (blocks duplicates and full events)
- My Registrations: view and cancel
- Profile: update name, phone, department, password

## 5. CRUD operations (for viva)

| Operation | Where in the app | MongoDB method used |
|---|---|---|
| **Create** | Sign up, Add Event, Register for event | `insertOne()` |
| **Read** | Event list, dashboard, participants, my registrations | `find()`, `findOne()`, `countDocuments()`, `aggregate()` |
| **Update** | Edit Event, Edit Profile, Mark Attendance | `updateOne()` with `$set` |
| **Delete** | Delete Event, Cancel Registration, Remove Participant/Student | `deleteOne()`, `deleteMany()` |

## 6. Database design (MongoDB collections)

**users**
```json
{ "_id": ObjectId, "name": "Priya", "email": "priya@college.edu", "regno": "23CS001",
  "department": "Computer Science", "phone": "9876543210",
  "password": "<bcrypt hash>", "role": "student", "created_at": ISODate }
```

**events**
```json
{ "_id": ObjectId, "title": "Tech Quiz 2026", "category": "Technical",
  "description": "...", "date": "2026-11-05", "time": "10:00",
  "venue": "Seminar Hall A", "max_seats": 60, "created_at": ISODate }
```

**registrations**
```json
{ "_id": ObjectId, "user_id": ObjectId, "event_id": ObjectId,
  "reg_date": ISODate, "attended": false }
```

Relationships use `ObjectId` references. Joins are done with the `$lookup`
aggregation stage (e.g. participants list = registrations + users).

## 7. Folder structure

```
eventms/
├── index.php              Home page
├── login.php              Login (admin + student)
├── register.php           Student sign up
├── logout.php
├── setup.php              One-time setup (admin + sample data)
├── composer.json
├── includes/
│   ├── db.php             MongoDB connection
│   ├── auth.php           Sessions, access control, flash messages
│   ├── event_form.php     Shared event form + validation
│   ├── header.php         Navbar (Bootstrap)
│   └── footer.php
├── admin/
│   ├── dashboard.php
│   ├── events.php         List / search / filter
│   ├── event_add.php      Create
│   ├── event_edit.php     Update
│   ├── event_delete.php   Delete
│   ├── participants.php
│   └── students.php
├── student/
│   ├── dashboard.php      Browse events + register
│   ├── register_event.php
│   ├── cancel.php
│   ├── my_registrations.php
│   └── profile.php
├── assets/css/style.css   Custom theme
├── database/              Sample JSON data (optional import)
└── vendor/                MongoDB PHP library
```

## 8. Security features (good viva points)
- Passwords stored with `password_hash()` (bcrypt), checked with `password_verify()`
- Output escaped with `htmlspecialchars()` to prevent XSS
- MongoDB queries built from PHP arrays (no string-built queries, so no injection)
- Role-based access (students cannot open admin pages)
- Delete actions only through POST with a confirmation dialog
- Unique index prevents duplicate email and duplicate registration

## 9. Viva quick answers
- **Why MongoDB?** Open source NoSQL database; stores JSON-like documents; flexible schema, so new fields can be added without altering tables.
- **Table vs Collection?** A collection holds documents (like rows) but documents need not have identical fields.
- **What is `_id`?** A unique 12-byte ObjectId MongoDB adds to every document automatically.
- **How are tables joined?** Using `$lookup` in an aggregation pipeline.
- **What is Bootstrap?** An open-source CSS framework for responsive, mobile-first design.
