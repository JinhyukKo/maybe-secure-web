-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS board_system;
USE board_system;


DROP TABLE IF EXISTS files;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS users; 


-- 사용자 테이블
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    profile VARCHAR(255) DEFAULT NULL,
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 게시글 테이블
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    user_id INT NOT NULL,
    filename VARCHAR(255),
    role ENUM('user','admin') NOT NULL DEFAULT 'user',
    isSecret BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO users (username, email, password, role)
VALUES
('alice', 'alice@example.com', '$2a$12$z48Ha4BLyQFUm/ecFLiE1Ot7T4QOPW5GKRt8nEU8X32J6txRrirH.', 'user'), 
('bob', 'bob@example.com', '$2a$12$z48Ha4BLyQFUm/ecFLiE1Ot7T4QOPW5GKRt8nEU8X32J6txRrirH.', 'admin');


-- posts 더미 데이터 2개
INSERT INTO posts (title, content, user_id, filename,role)
VALUES
('첫 번째 게시글', '이것은 첫 번째 게시글 내용입니다.', 1, 'file1.txt','user'),
('관리자 게시글', '관리자가 작성한 게시글 내용입니다.', 2, 'file2.txt','admin');

INSERT INTO comments (post_id, user_id, content)
VALUES
(1, 1, '첫 번째 게시글에 대한 댓글입니다.'),
(1, 2, '관리자의 댓글입니다.'),
(2, 1, '관리자 게시글에 대한 일반 사용자 댓글입니다.');
