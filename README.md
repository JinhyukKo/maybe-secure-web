# kshield-php-simple-website v1.0

```
 Secure Coding Vs Pentesting
    안전한 코딩과 모의해킹을 실습해보자
```


```bash
mysql -u root -p < database.sql
php -S localhost:4444
```
```
더미데이터 유저
alice:password (normal user)
bob:password (admin)fdd
```
## 구현한기능
- 로그인
- 로그아웃
- 회원가입
- 마이페이지
- 게시글 파일 업로드

- board.php
 1.목록별 최신순으로 갱신
 2.조건 검색으로 검색
 3.비밀글을 모든 유저가 제목을 확인 가능하게 구현

- view.php
1.삭제글 혹은 없는 파일에 접속시 404 구현
2.비밀글 열람시 본인과 어드민만 확인가능
3.댓글 구현

- write.php
 1.비밀글 체크박스 형식으로 비밀글 선택

## 미구현
 - view.php
  1.댓글삭제
 - mypage 이동.
 - 비밀번호 리셋 (이메일)
 







