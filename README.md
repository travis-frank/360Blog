# JTD Blog

https://cosc360.ok.ubc.ca/dsokic

# Overview

**JTD Blog** is a dynamic blogging platform developed using PHP, MySQL, JavaScript, and Bootstrap. The project supports user authentication, blog post creation, profile management, category-based filtering, post likes, and administrative moderation tools. The frontend is user-friendly and styled to offer a clean, engaging experience, while the backend ensures data integrity and access control.

---

# Key Features Implemented

## User Authentication and Authorization

- Users can register, log in, and log out securely.  
- Admin users gain access to administrative dashboards.

## Post Management

- Authenticated users can create blog posts, upload banner images, and categorize them.  
- Image upload is required, with validation and error handling.  
- Posts are displayed with preview cards showing title, snippet, author, and date.

## Feed and Filtering

- The feed (`feed.php`) displays all posts or filters them by category.  
- Posts are styled using Bootstrap cards and displayed in a responsive layout.

## User Dashboard

- Users can edit their profiles, change passwords, and upload profile pictures.  
- Tabs allow switching between "My Blogs" and "My Likes" with Bootstrap styling.

## Likes and Favorites

- Logged-in users can like posts.  
- A list of liked posts is available on the user dashboard.

## Admin Panel

- Admins can view, delete, or disable users and manage topics.  
- Admins can view analytics such as:
  - Posts over time  
  - Number of posts per category  
  - Number of new users  
  - Posts/topics/comments summary  
- JavaScript and PHP are used for real-time interactions (AJAX-based updates).

## Search Functionality

- Search bar in the navbar searches for posts and users.  
- Results are displayed with titles and content previews.

---

# Main PHP Files

- `feed.php` – Renders the main blog feed with filters and previews.  
- `createPost.php` – UI form for creating new posts. Validates required fields.  
- `php/createPost.php` – Handles post submission, image upload, and validation.  
- `userDash.php` – Displays user profile, editable fields, and tabbed views for posts and likes.  
- `php/updateProfile.php` – Saves updated profile info to the database.  
- `php/updatePicture.php` – Saves new profile picture to the database.  
- `blogPost.php` – Displays a full post including image, content, author, and like button.  
- `php/DBConnect.php` – Database connection logic reused across pages.

---

# JavaScript Files

- `js/feed.js` – Manages dynamic interactions on the feed (e.g., filtering).  
- `js/loginValidation.js` – Validates login form fields before submission.  
- `js/adminDash.js` – Handles user and topic management actions in the admin panel.  
- `js/navbar.js` – Adds logic for showing/hiding elements based on login status.

---

# High-Level Site Operation

1. On visiting the site, users can view public blog posts.  
2. Authenticated users get access to personalized dashboards and blog creation.  
3. Posts are saved to a MySQL database and include binary image storage.  
4. PHP handles form submissions and database transactions.  
5. JavaScript improves UX with form validation and dashboard interactivity.  
6. Admin users get advanced control over users and content.
