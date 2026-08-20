# Online eBook Store & Reader

Skeleton cho ứng dụng PHP thuần, MySQL và JavaScript thuần; không dùng framework hoặc package frontend. Môi trường chạy chính thức sử dụng Docker Compose.

## Yêu cầu

- Docker Desktop (Docker Compose v2+)
- Node.js 20+ chỉ để chạy JavaScript unit test

## Cài đặt

1. Tạo file môi trường: `Copy-Item .env.example .env`.
2. (Tuỳ chọn) Đổi mật khẩu và cổng trong `.env`.
3. Khởi động toàn bộ ứng dụng: `docker compose up --build -d`.
4. Mở ứng dụng web: `http://localhost:8000`.
5. Mở công cụ quản trị CSDL trên web (Adminer): `http://localhost:8080`.
   - **Hệ thống (System)**: `MySQL`
   - **Máy chủ (Server)**: `db`
   - **Tài khoản (Username)**: `ebook_user` (hoặc `root`)
   - **Mật khẩu (Password)**: `ebook_password` (hoặc `root_password`)
   - **Cơ sở dữ liệu (Database)**: `ebook_store`
6. Dừng dịch vụ: `docker compose down`.

Schema trong `database/schema.sql` tự chạy khi MySQL tạo volume lần đầu. Muốn tạo lại database từ đầu, dùng `docker compose down -v` rồi chạy lại bước 3 (lệnh này xoá dữ liệu database Docker).

## Kiểm thử JavaScript

Chạy `npm test`. Dự án dùng Node test runner có sẵn, nên không cần cài package npm.

`bootstrap/app.php` nạp `.env` và cung cấp `db()` cho kết nối PDO. PDO đã bật exception, prepared statements native và `utf8mb4`. Trong Docker, biến môi trường của service PHP được ưu tiên và trỏ `DB_HOST` đến service `db`.

## Cấu trúc

- `config/`: cấu hình ứng dụng và database
- `bootstrap/`: nạp môi trường và helper dùng chung
- `public/`: entry point và static assets
- `views/`: giao diện PHP
- `database/schema.sql`: schema cơ sở dữ liệu và seed data hoàn chỉnh (17 bảng)
- `database/DATABASE_PLAN.md`: kế hoạch và thiết kế chi tiết CSDL
- `database/migrate.php`: script PHP chạy migration và seed dữ liệu
