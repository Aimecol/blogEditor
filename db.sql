CREATE DATABASE IF NOT EXISTS blog_editor; 
USE blog_editor; 

CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) UNIQUE, password VARCHAR(255)); 

CREATE TABLE blog_posts (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, title VARCHAR(255), meta_description TEXT, content LONGTEXT, status ENUM('draft', 'published') DEFAULT 'draft', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, FOREIGN KEY (user_id) REFERENCES users(id)); 

CREATE TABLE comments (id INT AUTO_INCREMENT PRIMARY KEY, post_id INT, user_id INT, content TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY (post_id) REFERENCES blog_posts(id), FOREIGN KEY (user_id) REFERENCES users(id)); 

CREATE TABLE tags (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) UNIQUE); 

CREATE TABLE post_tags (post_id INT, tag_id INT, PRIMARY KEY(post_id, tag_id), FOREIGN KEY (post_id) REFERENCES blog_posts(id), FOREIGN KEY (tag_id) REFERENCES tags(id));

CREATE TABLE categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) UNIQUE); 

CREATE TABLE post_categories (post_id INT, category_id INT, PRIMARY KEY(post_id, category_id), FOREIGN KEY (post_id) REFERENCES blog_posts(id), FOREIGN KEY (category_id) REFERENCES categories(id));