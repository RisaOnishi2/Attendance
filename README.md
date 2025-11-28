# 勤怠管理
## 概要
「coachtech 勤怠管理アプリ」の開発
## 環境構築
### Dockerビルド
1. docker-compose up -d --build
### Laravel環境構築
1. docker-compose exec php bash
2. composer -v
3. composer create-project "laravel/laravel=8.*" . --prefer-dist
4. app.phpを編集して時刻設定
5. データベースの存在を確認後、.envを編集してデータベース接続
## 使用技術(実行環境)
PHP 7.4.9,
Laravel Framework 8.83.29,
MySQL 8.0.26
## ログイン情報
・管理者
　名前：管理者、E-MAIL：admin@example.com、PASSWORD：password123
・一般ユーザー
  名前：山田 太郎、E-MAIL：user1@example.com、PASSWORD：password1
  名前：西 怜奈、E-MAIL：user2@example.com、PASSWORD：password2
  名前：増田 一世、E-MAIL：user3@example.com、PASSWORD：password3
  名前：山本 敬吉、E-MAIL：user4@example.com、PASSWORD：password4
  名前：秋田 朋美、E-MAIL：user5@example.com、PASSWORD：password5
  名前：中西 敦夫、E-MAIL：user6@example.com、PASSWORD：password6
## ER図
<img width="701" height="331" alt="Attendance" src="https://github.com/user-attachments/assets/31fd07fb-24c4-4eee-a5f1-db909d412857" />

## URL
開発環境：http://localhost/ ,
pypMyAdmin：http://localhost:8080/
