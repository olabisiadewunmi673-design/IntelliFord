# IntelliFord QR Code Attendance System

--Project Overview

The IntelliFord QR Code Attendance System is a web-based application designed to automate and improve the process of attendance tracking in academic environments. The system uses QR code technology to allow students to mark attendance securely and efficiently, while enabling lecturers and administrators to monitor attendance records in real time.

---
--Objectives

* To eliminate manual attendance taking
* To reduce attendance fraud (proxy attendance)
* To provide real-time attendance tracking
* To enable automated reporting and monitoring

---

--System Features

--Student Module

* Login to student dashboard
* Scan QR code using device camera
* View personal attendance history
* Search attendance by course

--Lecturer Module

* Generate QR codes for each class session
* Set session expiry (time-based attendance)
* View list of students present
* Track attendance records per session

--Admin Module

* Manage students, lecturers, and courses
* Monitor attendance statistics
* View system-wide attendance trends
* Access dashboard with charts and analytics

---

--How the System Works

1. Lecturer logs in and creates an attendance session
2. The system generates a unique QR code for that session
3. Students log in and scan the QR code using their dashboard
4. The system validates the session token
5. Attendance is recorded in the database
6. Data is updated in real time for lecturers and administrators

---

--Attendance Logic

* Students who successfully scan the QR code are marked as **Present**
* Students who do not scan are considered **Absent**
* Absentee status is determined dynamically by comparing registered students with recorded attendance

---

--Network Requirement

This system currently operates within a local network environment. This means that both the lecturer generating the QR code and the students scanning it must be connected to the same network (e.g., the same Wi-Fi or hotspot).

This is because the system is hosted locally using a local IP address (e.g., 192.168.x.x), which is only accessible within that network.

--Future Improvement

For full deployment, the system can be hosted on a public server or cloud platform. This would allow users to access the system from any location without needing to be on the same network.

This enhancement improves scalability, accessibility, and real-world usability.

---

---Technologies Used

* **Frontend:** HTML, CSS, Bootstrap, JavaScript
* **Backend:** PHP
* **Database:** MySQL (phpMyAdmin)
* **Libraries:**

  * HTML5 QR Code Scanner
  * Chart.js (for analytics)

---

--System Capabilities

* Real-time attendance tracking
* Duplicate attendance prevention
* Course-based attendance management
* Attendance analytics and visualization
* Role-based access control

---

---Installation Guide

1. Install XAMPP or any local server
2. Copy the project folder into:

   ```
   htdocs/
   ```
3. Start Apache and MySQL
4. Import the database into phpMyAdmin
5. Open browser and navigate to:

   ```
   http://localhost/intelliford_attendance
   ```
---Default Roles

* Admin
* Lecturer
* Student

--Notes

* Students must be registered and assigned to courses before marking attendance
* QR codes are session-based and expire after a set time
* Only logged-in users can access system features

---Conclusion

The IntelliFord QR Code Attendance System provides a secure, efficient, and scalable solution for managing attendance in academic institutions. It minimizes manual effort, improves accuracy, and enhances monitoring through real-time data and analytics.

The system is designed with scalability in mind and can be deployed on cloud infrastructure for wider accessibility.

---
