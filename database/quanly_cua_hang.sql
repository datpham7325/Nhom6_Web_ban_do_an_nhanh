USE quanly_cua_hang;

-- --------------------------------------------------------
-- PHẦN 1: XÓA BẢNG CŨ (NẾU TỒN TẠI)
-- Xóa theo thứ tự ngược (từ con tới cha)
-- --------------------------------------------------------

DROP TABLE IF EXISTS LienHe;        -- Bảng mới
DROP TABLE IF EXISTS UserThongBao;  -- Xóa bảng cũ
DROP TABLE IF EXISTS ThongBao;
DROP TABLE IF EXISTS DatSuKien;
DROP TABLE IF EXISTS DatBan;
DROP TABLE IF EXISTS DanhGia;
DROP TABLE IF EXISTS ChiTietDonHang;
DROP TABLE IF EXISTS DonHang;
DROP TABLE IF EXISTS GioHang;
DROP TABLE IF EXISTS BienTheMonAn;
DROP TABLE IF EXISTS MonAn;
DROP TABLE IF EXISTS KichThuoc;
DROP TABLE IF EXISTS LoaiMonAn;
DROP TABLE IF EXISTS Users;

-- --------------------------------------------------------
-- PHẦN 2: TẠO BẢNG MỚI
-- Tạo theo thứ tự chuẩn (từ cha tới con)
-- --------------------------------------------------------

-- 1. Bảng Loại Món Ăn
CREATE TABLE LoaiMonAn (
    MaLoai INT AUTO_INCREMENT PRIMARY KEY,
    TenLoai VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Kích Thước
CREATE TABLE KichThuoc (
    MaSize INT AUTO_INCREMENT PRIMARY KEY,
    TenSize VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Món Ăn (Đã thêm lại cột 'MoTa')
CREATE TABLE MonAn (
    MaMonAn INT AUTO_INCREMENT PRIMARY KEY,
    TenMonAn VARCHAR(255) NOT NULL,
    HinhAnh VARCHAR(500),
    MoTa TEXT,
    MaLoai INT,
    FOREIGN KEY (MaLoai) REFERENCES LoaiMonAn(MaLoai)
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Biến Thể Món Ăn (Kết hợp Món Ăn + Size + Giá)
CREATE TABLE BienTheMonAn (
    MaBienThe INT AUTO_INCREMENT PRIMARY KEY,
    MaMonAn INT NOT NULL,
    MaSize INT NOT NULL,  
    DonGia DECIMAL(18, 2) NOT NULL CHECK (DonGia >= 0),
    FOREIGN KEY (MaMonAn) REFERENCES MonAn(MaMonAn) ON DELETE CASCADE,
    FOREIGN KEY (MaSize) REFERENCES KichThuoc(MaSize) ON DELETE CASCADE,
    UNIQUE(MaMonAn, MaSize)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng Users có kết hợp với hash password
CREATE TABLE Users (
    MaUser INT AUTO_INCREMENT PRIMARY KEY,
    Ho VARCHAR(100) NOT NULL,
    Ten VARCHAR(100) NOT NULL,
    SDT VARCHAR(15) NOT NULL UNIQUE,
    Email VARCHAR(255) NOT NULL UNIQUE,
    DiaChi TEXT,
    QuyenHan ENUM('admin', 'nhanvien', 'khachhang') NOT NULL DEFAULT 'khachhang',
    MatKhau BINARY(32) NOT NULL,
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (Email),
    INDEX idx_sdt (SDT)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng Giỏ hàng
CREATE TABLE GioHang (
    MaGioHang INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL,
    MaBienThe INT NOT NULL,
    SoLuong INT NOT NULL DEFAULT 1,
    NgayThem DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaUser) REFERENCES Users(MaUser) ON DELETE CASCADE,
    FOREIGN KEY (MaBienThe) REFERENCES BienTheMonAn(MaBienThe) ON DELETE CASCADE,
    UNIQUE(MaUser, MaBienThe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng Đơn hàng
CREATE TABLE DonHang (
    MaDonHang INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL,
    TongTien DECIMAL(18,2) NOT NULL,
    TrangThai ENUM('cho_xac_nhan', 'dang_xu_ly', 'dang_giao', 'hoan_thanh', 'da_huy') DEFAULT 'cho_xac_nhan',
    PhuongThucThanhToan ENUM('tien_mat', 'chuyen_khoan', 'the', 'vi_dien_tu', 'momo'),
    DiaChiGiaoHang TEXT,
    SDTGiaoHang VARCHAR(15),
    GhiChu TEXT,
    NgayDat DATETIME DEFAULT CURRENT_TIMESTAMP,
    NgayCapNhat DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (MaUser) REFERENCES Users(MaUser) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bảng Chi tiết đơn hàng
CREATE TABLE ChiTietDonHang (
    MaChiTiet INT AUTO_INCREMENT PRIMARY KEY,
    MaDonHang INT NOT NULL,
    MaBienThe INT NOT NULL,
    SoLuong INT NOT NULL,
    DonGia DECIMAL(18,2) NOT NULL,
    ThanhTien DECIMAL(18,2) NOT NULL,
    FOREIGN KEY (MaDonHang) REFERENCES DonHang(MaDonHang) ON DELETE CASCADE,
    FOREIGN KEY (MaBienThe) REFERENCES BienTheMonAn(MaBienThe) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Bảng Đánh giá
CREATE TABLE DanhGia (
    MaDanhGia INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL,
    MaMonAn INT,
    Diem INT NOT NULL CHECK (Diem BETWEEN 1 AND 5),
    NoiDung TEXT,
    AnhReview VARCHAR(500),
    TrangThai ENUM('cho_duyet', 'da_duyet', 'tu_choi') DEFAULT 'cho_duyet',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaUser) REFERENCES Users(MaUser) ON DELETE CASCADE,
    FOREIGN KEY (MaMonAn) REFERENCES MonAn(MaMonAn) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Bảng Đặt bàn
CREATE TABLE DatBan (
    MaDatBan INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL,
    HoTen VARCHAR(100) NOT NULL,
    SDT VARCHAR(15) NOT NULL,
    SoNguoi INT NOT NULL,
    NgayDat DATE NOT NULL,
    GioDat TIME NOT NULL,
    GhiChu TEXT,
    TrangThai ENUM('cho_xac_nhan', 'da_xac_nhan', 'da_huy') DEFAULT 'cho_xac_nhan',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaUser) REFERENCES Users(MaUser) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Bảng Đặt sự kiện
CREATE TABLE DatSuKien (
    MaSuKien INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL,
    TenSuKien VARCHAR(255) NOT NULL,
    HoTenNguoiDaiDien VARCHAR(100) NOT NULL,
    SDT VARCHAR(15) NOT NULL,
    Email VARCHAR(255),
    SoNguoi INT NOT NULL,
    NgaySuKien DATE NOT NULL,
    GioBatDau TIME NOT NULL,
    GioKetThuc TIME NOT NULL,
    LoaiSuKien ENUM('sinh_nhat', 'hoi_nghi', 'tiec_cuoi', 'gia_dinh', 'khac'),
    YeuCauDacBiet TEXT,
    TrangThai ENUM('cho_xac_nhan', 'da_xac_nhan', 'da_huy') DEFAULT 'cho_xac_nhan',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaUser) REFERENCES Users(MaUser) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Bảng Thông báo
CREATE TABLE ThongBao (
    MaThongBao INT AUTO_INCREMENT PRIMARY KEY,
    TieuDe VARCHAR(255) NOT NULL,
    NoiDung TEXT NOT NULL,
    LoaiThongBao ENUM('khuyen_mai', 'don_hang', 'he_thong', 'su_kien'),
    HinhAnh VARCHAR(500),
    NgayBatDau DATETIME,
    NgayKetThuc DATETIME,
    TrangThai ENUM('active', 'inactive') DEFAULT 'active',
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. 🔥 BẢNG LIÊN HỆ (Thay thế UserThongBao)
CREATE TABLE LienHe (
    MaLienHe INT AUTO_INCREMENT PRIMARY KEY,
    HoTen VARCHAR(100) NOT NULL,
    Email VARCHAR(255) NOT NULL,
    SDT VARCHAR(15) NOT NULL,
    NoiDung TEXT NOT NULL,
    TrangThai ENUM('moi', 'da_xem', 'da_phan_hoi') DEFAULT 'moi',
    NgayGui DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- PHẦN 0: XÓA DỮ LIỆU CŨ ĐỂ CẬP NHẬT
-- (Xóa theo thứ tự ngược, từ con tới cha)
-- --------------------------------------------------------

DELETE FROM LienHe;
DELETE FROM ThongBao;
DELETE FROM DatSuKien;
DELETE FROM DatBan;
DELETE FROM DanhGia;
DELETE FROM ChiTietDonHang;
DELETE FROM DonHang;
DELETE FROM GioHang;
DELETE FROM BienTheMonAn;
DELETE FROM MonAn;
DELETE FROM KichThuoc;
DELETE FROM LoaiMonAn;
DELETE FROM Users;

-- Đặt lại AUTO_INCREMENT về 1
ALTER TABLE LoaiMonAn AUTO_INCREMENT = 1;
ALTER TABLE KichThuoc AUTO_INCREMENT = 1;
ALTER TABLE MonAn AUTO_INCREMENT = 1;
ALTER TABLE BienTheMonAn AUTO_INCREMENT = 1;
ALTER TABLE Users AUTO_INCREMENT = 1;
ALTER TABLE GioHang AUTO_INCREMENT = 1;
ALTER TABLE DonHang AUTO_INCREMENT = 1;
ALTER TABLE ChiTietDonHang AUTO_INCREMENT = 1;
ALTER TABLE DanhGia AUTO_INCREMENT = 1;
ALTER TABLE DatBan AUTO_INCREMENT = 1;
ALTER TABLE DatSuKien AUTO_INCREMENT = 1;
ALTER TABLE ThongBao AUTO_INCREMENT = 1;
ALTER TABLE LienHe AUTO_INCREMENT = 1;

-- --------------------------------------------------------
-- PHẦN 1: CHÈN 6 LOẠI MÓN ĂN
-- --------------------------------------------------------

INSERT INTO LoaiMonAn (TenLoai) VALUES
('Gà giòn'),      -- MaLoai = 1
('Mì ý'),         -- MaLoai = 2
('Gà sốt'),       -- MaLoai = 3
('Burger'),       -- MaLoai = 4
('Tráng miệng'),  -- MaLoai = 5
('Nước');         -- MaLoai = 6

-- --------------------------------------------------------
-- PHẦN 2: CHÈN CÁC KÍCH THƯỚC (SIZE)
-- --------------------------------------------------------

INSERT INTO KichThuoc (TenSize) VALUES
('Vừa'),          -- MaSize = 1
('Lớn');          -- MaSize = 2

-- --------------------------------------------------------
-- PHẦN 3: CHÈN BẢNG MÓN ĂN (MONAN)
-- --------------------------------------------------------

-- Loai 1: Gà giòn
INSERT INTO MonAn (TenMonAn, HinhAnh, MaLoai) VALUES
('2 MIẾNG GÀ GIÒN VUI VẺ', 'gagionvuive/gagionvuive1.jpg', 1),
('4 MIẾNG GÀ GIÒN VUI VẺ', 'gagionvuive/gagionvuive2.jpg', 1),
('6 MIẾNG GÀ GIÒN VUI VẺ', 'gagionvuive/gagionvuive3.jpg', 1),
('2 GÀ GIÒN VUI VẺ + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'gagionvuive/gagionvuive4.jpg', 1),
('1 GÀ GIÒN VUI VẺ + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'gagionvuive/gagionvuive5.jpg', 1),
('1 CƠM GÀ GIÒN VUI VẺ + 1 SÚP BÍ ĐỎ + 1 NƯỚC NGỌT', 'gagionvuive/gagionvuive6.jpg', 1),
('1 CƠM GÀ GIÒN VUI VẺ + 1 NƯỚC NGỌT + 1 TƯƠNG CHUA NGỌT', 'gagionvuive/gagionvuive7.jpg', 1),
('1 CƠM GÀ GIÒN VUI VẺ', 'gagionvuive/gagionvuive8.jpg', 1),
('1 MIẾNG GÀ GIÒN VUI VẺ', 'gagionvuive/gagionvuive9.jpg', 1),

-- Loai 2: Mì ý
('MÌ Ý SỐT CAY VỪA', 'miy/miy1.jpg', 2),
('1 MÌ Ý SỐT CAY VỪA + 1 NƯỚC NGỌT', 'miy/miy2.webp', 2),
('MÌ Ý SỐT CAY VỪA + 1 GÀ GIÒN VUI VẺ + 1 NƯỚC NGỌT', 'miy/miy3.webp', 2),
('1 MÌ Ý JOLLY VỪA + 1 GÀ GIÒN VUI VẺ + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'miy/miy4.jpg', 2),
('1 MÌ Ý JOLLY VỪA + 2 GÀ KHÔNG XƯƠNG + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'miy/miy5.jpg', 2),
('1 GÀ GIÒN VUI VẺ + 1 MÌ Ý JOLLY + 1 NƯỚC NGỌT', 'miy/miy6.webp', 2),
('1 MÌ Ý JOLLY VỪA + 2 GÀ KHÔNG XƯƠNG + 1 NƯỚC NGỌT', 'miy/miy7.webp', 2),
('1 MÌ Ý JOLLY VỪA + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'miy/miy8.jpg', 2),
('1 MÌ Ý JOLLY VỪA + 1 NƯỚC NGỌT', 'miy/miy9.jpg', 2),

-- Loai 3: Gà sốt
('2 MIẾNG GÀ SỐT CAY', 'gasot/gasot1.jpg', 3),
('2 GÀ SỐT CAY + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'gasot/gasot2.jpg', 3),
('1 GÀ SỐT CAY + 1 KHOAI TAY CHIÊN VỪA + 1 NƯỚC NGỌT', 'gasot/gasot3.jpg', 3),
('1 COM GÀ SỐT CAY + 1 SÚP BÍ ĐỎ + 1 NƯỚC NGỌT', 'gasot/gasot4.jpg', 3),
('1 GÀ SỐT CAY + 1 NƯỚC NGỌT', 'gasot/gasot5.jpg', 3),
('1 CƠM GÀ SỐT CAY', 'gasot/gasot6.jpg', 3),
('1 MIẾNG GÀ SỐT CAY', 'gasot/gasot7.jpg', 3),

-- Loai 4: Burger
('CƠM GÀ MẮM TỎI', 'burger/burger1.jpg', 4),
('1 CƠM GÀ MẮM TỎI + 1 NƯỚC NGỌT', 'burger/burger2.jpg', 4),
('1 BURGER TÔM + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'burger/burger3.webp', 4),
('1 BURGER TÔM + 1 NƯỚC NGỌT', 'burger/burger4.webp', 4),
('1 JOLLY HOTDOG + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'burger/burger5.webp', 4),
('1 JOLLY HOTDOG + 1 NƯỚC NGỌT', 'burger/burger6.webp', 4),
('1 SANDWICH GÀ GIÒN + 1 KHOAI TÂY CHIÊN VỪA + 1 NƯỚC NGỌT', 'burger/burger7.webp', 4),
('1 SANDWICH GÀ GIÒN + 1 NƯỚC NGỌT', 'burger/burger8.webp', 4),
('BURGER TÔM', 'burger/burger9.webp', 4),

-- Loai 5: Tráng miệng
('BÁNH XOÀI ĐÀO', 'trangmieng/trangmieng1.webp', 5),
('TROPICAL SUNDAE', 'trangmieng/trangmieng2.webp', 5),
('KEM SUNDAE DÂU', 'trangmieng/trangmieng3.webp', 5),
('KEM SUNDAE SOCOLA', 'trangmieng/trangmieng4.webp', 5),
('KEM SÔCÔLA', 'trangmieng/trangmieng5.webp', 5),
('KEM SỮA TƯƠI', 'trangmieng/trangmieng6.webp', 5),

-- Loai 6: Nước
('TRÀ CHANH HẠT CHIA', 'nuoc/nuoc1.webp', 6),
('NƯỚC ÉP XOÀI ĐÀO', 'nuoc/nuoc2.webp', 6),
('PEPSI', 'nuoc/nuoc3.webp', 6),
('MIRINDA', 'nuoc/nuoc5.webp', 6),
('7UP', 'nuoc/nuoc7.webp', 6),
('CACAO', 'nuoc/nuoc9.webp', 6);

-- --------------------------------------------------------
-- PHẦN 4: CHÈN BIẾN THỂ MÓN ĂN (BIENTHEMONAN)
-- --------------------------------------------------------

INSERT INTO BienTheMonAn (MaMonAn, MaSize, DonGia) VALUES
-- Gà giòn (MaMonAn 1-9) -> Dùng MaSize = 1 (Vừa)
(1, 1, 66000), (2, 1, 126000), (3, 1, 188000), (4, 1, 91000), (5, 1, 58000),
(6, 1, 63000), (7, 1, 58000), (8, 1, 48000), (9, 1, 33000),

-- Mì ý (MaMonAn 10-18) -> Dùng MaSize = 1 (Vừa)
(10, 1, 40000), (11, 1, 50000), (12, 1, 83000), (13, 1, 93000), (14, 1, 80000),
(15, 1, 78000), (16, 1, 70000), (17, 1, 55000), (18, 1, 45000),

-- Gà sốt (MaMonAn 19-25) -> Dùng MaSize = 1 (Vừa)
(19, 1, 70000), (20, 1, 95000), (21, 1, 60000), (22, 1, 65000), (23, 1, 60000), (24, 1, 50000), (25, 1, 35000),

-- Burger (MaMonAn 26-34) -> Dùng MaSize = 1 (Vừa)
(26, 1, 35000), (27, 1, 45000), (28, 1, 65000), (29, 1, 50000), (30, 1, 50000),
(31, 1, 35000), (32, 1, 55000), (33, 1, 40000), (34, 1, 40000),

-- Tráng miệng (MaMonAn 35-40) -> Dùng MaSize = 1 (Vừa)
(35, 1, 15000), (36, 1, 20000), (37, 1, 15000), (38, 1, 15000), (39, 1, 7000), (40, 1, 5000),

-- Nước (MaMonAn 41-46) -> Dùng size Vừa (1) và Lớn (2)
(41, 1, 20000), (42, 1, 20000), (43, 2, 17000), (43, 1, 12000), (44, 2, 17000), (44, 1, 12000),
(45, 2, 17000), (45, 1, 12000), (46, 2, 25000);

-- --------------------------------------------------------
-- PHẦN 5: CẬP NHẬT MÔ TẢ CHO MÓN ĂN
-- --------------------------------------------------------

-- Gà giòn
UPDATE MonAn SET MoTa = 'Gồm 2 miếng gà giòn rụm truyền thống, vị nguyên bản.' WHERE MaMonAn = 1;
UPDATE MonAn SET MoTa = 'Gồm 4 miếng gà giòn rụm truyền thống, vị nguyên bản.' WHERE MaMonAn = 2;
UPDATE MonAn SET MoTa = 'Gồm 6 miếng gà giòn rụm truyền thống, vị nguyên bản.' WHERE MaMonAn = 3;
UPDATE MonAn SET MoTa = 'Combo gồm 2 gà giòn, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 4;
UPDATE MonAn SET MoTa = 'Combo gồm 1 gà giòn, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 5;
UPDATE MonAn SET MoTa = 'Combo gồm 1 cơm gà giòn, 1 súp bí đỏ và 1 nước ngọt.' WHERE MaMonAn = 6;
UPDATE MonAn SET MoTa = 'Combo gồm 1 cơm gà giòn, 1 nước ngọt và 1 tương chua ngọt.' WHERE MaMonAn = 7;
UPDATE MonAn SET MoTa = 'Phần ăn gồm 1 cơm nóng dẻo ăn kèm 1 miếng gà giòn rụm.' WHERE MaMonAn = 8;
UPDATE MonAn SET MoTa = 'Gồm 1 miếng gà giòn rụm vị nguyên bản.' WHERE MaMonAn = 9;

-- Mì ý
UPDATE MonAn SET MoTa = '1 phần Mì Ý sốt bò bằm đậm đà, thêm chút vị cay nhẹ.' WHERE MaMonAn = 10;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý sốt cay vừa và 1 nước ngọt.' WHERE MaMonAn = 11;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý sốt cay, 1 gà giòn và 1 nước ngọt.' WHERE MaMonAn = 12;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý Jolly, 1 gà giòn, 1 khoai tây vừa và 1 nước ngọt.' WHERE MaMonAn = 13;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý Jolly, 2 gà không xương, 1 khoai tây vừa và 1 nước ngọt.' WHERE MaMonAn = 14;
UPDATE MonAn SET MoTa = 'Combo gồm 1 gà giòn, 1 Mì Ý Jolly và 1 nước ngọt.' WHERE MaMonAn = 15;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý Jolly, 2 gà không xương và 1 nước ngọt.' WHERE MaMonAn = 16;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý Jolly, 1 khoai tây vừa và 1 nước ngọt.' WHERE MaMonAn = 17;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Mì Ý Jolly và 1 nước ngọt.' WHERE MaMonAn = 18;

-- Gà sốt
UPDATE MonAn SET MoTa = 'Gồm 2 miếng gà giòn được phủ lớp sốt cay đặc trưng.' WHERE MaMonAn = 19;
UPDATE MonAn SET MoTa = 'Combo gồm 2 gà sốt cay, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 20;
UPDATE MonAn SET MoTa = 'Combo gồm 1 gà sốt cay, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 21;
UPDATE MonAn SET MoTa = 'Combo gồm 1 cơm gà sốt cay đậm đà, 1 súp bí đỏ và 1 nước ngọt.' WHERE MaMonAn = 22;
UPDATE MonAn SET MoTa = 'Combo gồm 1 gà sốt cay và 1 nước ngọt.' WHERE MaMonAn = 23;
UPDATE MonAn SET MoTa = 'Phần ăn gồm 1 cơm nóng dẻo dùng kèm gà sốt cay.' WHERE MaMonAn = 24;
UPDATE MonAn SET MoTa = 'Gồm 1 miếng gà giòn phủ sốt cay đậm vị.' WHERE MaMonAn = 25;

-- Burger
UPDATE MonAn SET MoTa = '1 phần cơm trắng ăn kèm gà sốt mắm tỏi thơm lừng.' WHERE MaMonAn = 26;
UPDATE MonAn SET MoTa = 'Combo gồm 1 cơm gà mắm tỏi và 1 nước ngọt.' WHERE MaMonAn = 27;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Burger Tôm, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 28;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Burger Tôm và 1 nước ngọt.' WHERE MaMonAn = 29;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Jolly Hotdog, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 30;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Jolly Hotdog và 1 nước ngọt.' WHERE MaMonAn = 31;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Sandwich Gà giòn, 1 khoai tây chiên vừa và 1 nước ngọt.' WHERE MaMonAn = 32;
UPDATE MonAn SET MoTa = 'Combo gồm 1 Sandwich Gà giòn và 1 nước ngọt.' WHERE MaMonAn = 33;
UPDATE MonAn SET MoTa = '1 Burger nhân tôm chiên giòn, kẹp rau tươi và sốt mayonnaise.' WHERE MaMonAn = 34;

-- Tráng miệng
UPDATE MonAn SET MoTa = '1 Bánh ngọt mềm xốp với nhân mứt xoài và đào.' WHERE MaMonAn = 35;
UPDATE MonAn SET MoTa = '1 ly kem vani mát lạnh kết hợp sốt trái cây nhiệt đới.' WHERE MaMonAn = 36;
UPDATE MonAn SET MoTa = '1 ly kem vani phủ sốt dâu tây ngọt ngào.' WHERE MaMonAn = 37;
UPDATE MonAn SET MoTa = '1 ly kem vani phủ sốt sôcôla đậm đặc.' WHERE MaMonAn = 38;
UPDATE MonAn SET MoTa = '1 ly kem vị sôcôla truyền thống.' WHERE MaMonAn = 39;
UPDATE MonAn SET MoTa = '1 ly kem sữa tươi (vani) mát lạnh.' WHERE MaMonAn = 40;

-- Nước
UPDATE MonAn SET MoTa = '1 ly trà chanh thanh mát, thêm hạt chia tốt cho sức khỏe.' WHERE MaMonAn = 41;
UPDATE MonAn SET MoTa = '1 ly nước ép kết hợp vị xoài và đào tươi mát.' WHERE MaMonAn = 42;
UPDATE MonAn SET MoTa = '1 ly nước ngọt giải khát có gas vị cola.' WHERE MaMonAn = 43;
UPDATE MonAn SET MoTa = '1 ly nước ngọt giải khát có gas vị cam.' WHERE MaMonAn = 44;
UPDATE MonAn SET MoTa = '1 ly nước ngọt giải khát có gas vị chanh.' WHERE MaMonAn = 45;
UPDATE MonAn SET MoTa = '1 ly thức uống cacao đậm vị, pha với sữa và đá.' WHERE MaMonAn = 46;

-- --------------------------------------------------------
-- PHẦN 6: CHÈN TÀI KHOẢN ADMIN VÀ KHÁCH HÀNG
-- --------------------------------------------------------

INSERT INTO Users (Ho, Ten, SDT, Email, DiaChi, QuyenHan, MatKhau) VALUES
('Quản', 'Trị Viên', '0901234567', 'admin@quanlycuahang.com', '123 Đường Quản Lý, Quận 1, TP.HCM', 'admin', UNHEX(SHA2('Admin@123', 256))),
('Nguyễn Văn', 'An', '0912345678', 'nguyenvanan@gmail.com', '456 Nguyễn Văn Linh, Quận 7, TP.HCM', 'khachhang', UNHEX(SHA2('123456', 256))),
('Trần Thị', 'Bình', '0923456789', 'tranthibinh@gmail.com', '789 Lê Văn Việt, Quận 9, TP.HCM', 'khachhang', UNHEX(SHA2('123456', 256)));

-- --------------------------------------------------------
-- PHẦN 7: CHÈN DỮ LIỆU GIỎ HÀNG
-- --------------------------------------------------------

INSERT INTO GioHang (MaUser, MaBienThe, SoLuong) VALUES
(2, 1, 2),
(2, 43, 1),
(2, 35, 1);

INSERT INTO GioHang (MaUser, MaBienThe, SoLuong) VALUES
(3, 5, 1),
(3, 37, 2);

-- --------------------------------------------------------
-- PHẦN 8: CHÈN DỮ LIỆU ĐƠN HÀNG
-- --------------------------------------------------------

INSERT INTO DonHang (MaUser, TongTien, TrangThai, PhuongThucThanhToan, DiaChiGiaoHang, SDTGiaoHang, GhiChu) 
VALUES (2, 147000, 'hoan_thanh', 'tien_mat', '456 Nguyễn Văn Linh, Quận 7, TP.HCM', '0912345678', 'Giao hàng giờ hành chính');

INSERT INTO ChiTietDonHang (MaDonHang, MaBienThe, SoLuong, DonGia, ThanhTien) VALUES
(1, 1, 1, 66000, 66000),
(1, 43, 2, 12000, 24000),
(1, 35, 1, 15000, 15000),
(1, 10, 1, 40000, 40000);

INSERT INTO DonHang (MaUser, TongTien, TrangThai, PhuongThucThanhToan, DiaChiGiaoHang, SDTGiaoHang) 
VALUES (3, 88000, 'dang_xu_ly', 'chuyen_khoan', '789 Lê Văn Việt, Quận 9, TP.HCM', '0923456789');

INSERT INTO ChiTietDonHang (MaDonHang, MaBienThe, SoLuong, DonGia, ThanhTien) VALUES
(2, 5, 1, 58000, 58000),
(2, 37, 2, 15000, 30000);

-- --------------------------------------------------------
-- PHẦN 9: CHÈN DỮ LIỆU ĐÁNH GIÁ
-- --------------------------------------------------------

INSERT INTO DanhGia (MaUser, MaMonAn, Diem, NoiDung, TrangThai) VALUES
(2, 1, 5, 'Gà giòn rất ngon, da giòn thịt mềm. Sẽ quay lại ủng hộ!', 'da_duyet'),
(2, 10, 4, 'Mì Ý sốt cay vừa miệng, hương vị đậm đà. Rất đáng thử!', 'da_duyet');

INSERT INTO DanhGia (MaUser, MaMonAn, Diem, NoiDung, TrangThai) VALUES
(3, 5, 5, 'Combo rất tiện lợi, đầy đủ và ngon miệng. Giá cả hợp lý!', 'da_duyet'),
(3, 37, 3, 'Kem sundae dâu ngon nhưng hơi ngọt. Có thể giảm đường một chút.', 'cho_duyet');

-- --------------------------------------------------------
-- PHẦN 10: CHÈN DỮ LIỆU ĐẶT BÀN
-- --------------------------------------------------------

INSERT INTO DatBan (MaUser, HoTen, SDT, SoNguoi, NgayDat, GioDat, GhiChu, TrangThai) VALUES
(2, 'Nguyễn Văn An', '0912345678', 4, '2024-02-15', '18:30:00', 'Có 2 trẻ em', 'da_xac_nhan');

INSERT INTO DatBan (MaUser, HoTen, SDT, SoNguoi, NgayDat, GioDat, GhiChu, TrangThai) VALUES
(3, 'Trần Thị Bình', '0923456789', 6, '2024-02-20', '19:00:00', 'Sinh nhật bé', 'cho_xac_nhan');

-- --------------------------------------------------------
-- PHẦN 11: CHÈN DỮ LIỆU ĐẶT SỰ KIỆN
-- --------------------------------------------------------

INSERT INTO DatSuKien (MaUser, TenSuKien, HoTenNguoiDaiDien, SDT, Email, SoNguoi, NgaySuKien, GioBatDau, GioKetThuc, LoaiSuKien, YeuCauDacBiet, TrangThai) VALUES
(2, 'Sinh nhật bé Minh 5 tuổi', 'Nguyễn Văn An', '0912345678', 'nguyenvanan@gmail.com', 20, '2024-02-25', '14:00:00', '16:00:00', 'sinh_nhat', 'Trang trí theo chủ đề siêu nhân, có bánh sinh nhật', 'da_xac_nhan');

INSERT INTO DatSuKien (MaUser, TenSuKien, HoTenNguoiDaiDien, SDT, Email, SoNguoi, NgaySuKien, GioBatDau, GioKetThuc, LoaiSuKien, YeuCauDacBiet, TrangThai) VALUES
(3, 'Tiệc liên hoan công ty', 'Trần Thị Bình', '0923456789', 'tranthibinh@gmail.com', 30, '2024-03-01', '18:00:00', '20:00:00', 'hoi_nghi', 'Cần khu vực riêng, có máy chiếu', 'cho_xac_nhan');

-- --------------------------------------------------------
-- PHẦN 12: CHÈN DỮ LIỆU THÔNG BÁO
-- --------------------------------------------------------

INSERT INTO ThongBao (TieuDe, NoiDung, LoaiThongBao, HinhAnh, NgayBatDau, NgayKetThuc, TrangThai) VALUES
('KHUYẾN MÃI ĐẶC BIỆT - COMBO GIA ĐÌNH', 'Ưu đãi đặc biệt combo gia đình 4 người chỉ 299.000 VND. Áp dụng từ 01/02/2024 đến 29/02/2024.', 'khuyen_mai', 'khuyenmai/combo-gia-dinh.jpg', '2024-02-01 00:00:00', '2024-02-29 23:59:59', 'active');

INSERT INTO ThongBao (TieuDe, NoiDung, LoaiThongBao, HinhAnh, NgayBatDau, NgayKetThuc, TrangThai) VALUES
('MUA 1 TẶNG 1 - THỨ 3 HÀNG TUẦN', 'Mỗi thứ 3 hàng tuần, mua 1 burger bất kỳ được tặng 1 burger cùng loại. Áp dụng cho tất cả chi nhánh.', 'khuyen_mai', 'khuyenmai/mua-1-tang-1.jpg', '2024-02-01 00:00:00', '2024-12-31 23:59:59', 'active');

INSERT INTO ThongBao (TieuDe, NoiDung, LoaiThongBao, TrangThai) VALUES
('NÂNG CẤP HỆ THỐNG', 'Hệ thống sẽ được nâng cấp từ 02:00 đến 04:00 ngày 15/02/2024. Xin lỗi vì sự bất tiện này.', 'he_thong', 'inactive');

-- --------------------------------------------------------
-- PHẦN 13: CHÈN DỮ LIỆU LIÊN HỆ
-- --------------------------------------------------------

INSERT INTO LienHe (HoTen, Email, SDT, NoiDung, TrangThai) VALUES
('Lê Văn Cường', 'cuongle@gmail.com', '0987654321', 'Cho mình hỏi quán có chỗ đậu xe hơi không?', 'moi'),
('Phạm Thị Dung', 'dungpham@yahoo.com', '0911223344', 'Mình muốn đặt tiệc sinh nhật cho công ty khoảng 50 người.', 'da_xem');

-- --------------------------------------------------------
-- PHẦN 14: KIỂM TRA DỮ LIỆU
-- --------------------------------------------------------

SELECT 
    'LoaiMonAn' as Table_Name, COUNT(*) as Record_Count FROM LoaiMonAn
UNION ALL SELECT 'KichThuoc', COUNT(*) FROM KichThuoc
UNION ALL SELECT 'MonAn', COUNT(*) FROM MonAn
UNION ALL SELECT 'BienTheMonAn', COUNT(*) FROM BienTheMonAn
UNION ALL SELECT 'Users', COUNT(*) FROM Users
UNION ALL SELECT 'GioHang', COUNT(*) FROM GioHang
UNION ALL SELECT 'DonHang', COUNT(*) FROM DonHang
UNION ALL SELECT 'ChiTietDonHang', COUNT(*) FROM ChiTietDonHang
UNION ALL SELECT 'DanhGia', COUNT(*) FROM DanhGia
UNION ALL SELECT 'DatBan', COUNT(*) FROM DatBan
UNION ALL SELECT 'DatSuKien', COUNT(*) FROM DatSuKien
UNION ALL SELECT 'ThongBao', COUNT(*) FROM ThongBao
UNION ALL SELECT 'LienHe', COUNT(*) FROM LienHe;


-- --------------------------------------------------------
-- BỔ SUNG: THÊM LOẠI MÓN ĂN VÀ 4 COMBO KHUYẾN MÃI
-- --------------------------------------------------------

INSERT INTO LoaiMonAn (TenLoai) VALUES ('Combo');

INSERT INTO MonAn (TenMonAn, HinhAnh, MoTa, MaLoai) VALUES
('COMBO KHUYẾN MÃI 1', 'khuyenmai/khuyenmai1.jpg', 'Combo tiết kiệm đặc biệt gồm gà giòn và nước ngọt, giảm giá cực sốc.', 7),
('COMBO KHUYẾN MÃI 2', 'khuyenmai/khuyenmai2.jpg', 'Phần ăn đầy đủ dinh dưỡng, hương vị tuyệt hảo dành cho 1 người.', 7),
('COMBO KHUYẾN MÃI 3', 'khuyenmai/khuyenmai3.jpg', 'Sự kết hợp hoàn hảo giữa các món best-seller với mức giá ưu đãi.', 7),
('COMBO KHUYẾN MÃI 4', 'khuyenmai/khuyenmai4.jpg', 'Bữa tiệc thịnh soạn với đầy đủ món ăn và tráng miệng.', 7);

INSERT INTO BienTheMonAn (MaMonAn, MaSize, DonGia) VALUES
(47, 1, 79000),   -- Giá cho Combo 1
(48, 1, 89000),   -- Giá cho Combo 2
(49, 1, 99000),   -- Giá cho Combo 3
(50, 1, 109000);  -- Giá cho Combo 4


-- 1. Loại bỏ các tiền tố thư mục (folder/)
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'gagionvuive/', '');
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'miy/', '');
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'gasot/', '');
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'burger/', '');
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'trangmieng/', '');
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'nuoc/', '');
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, 'khuyenmai/', '');

-- 2. Đổi tất cả đuôi .webp thành .jpg
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, '.webp', '.jpg');

-- 3. (Phụ trợ) Đổi đuôi .png thành .jpg (nếu có) để đồng bộ tuyệt đối
UPDATE MonAn SET HinhAnh = REPLACE(HinhAnh, '.png', '.jpg');

-- 1. Xóa bảng Đánh giá cũ
DROP TABLE IF EXISTS DanhGia;

-- 2. Tạo lại bảng Đánh giá mới (Đã bỏ cột TrangThai)
CREATE TABLE DanhGia (
    MaDanhGia INT AUTO_INCREMENT PRIMARY KEY,
    MaUser INT NOT NULL,
    MaDonHang INT NOT NULL,
    Diem INT NOT NULL CHECK (Diem BETWEEN 1 AND 5),
    NoiDung TEXT,
    AnhReview VARCHAR(500),
    NgayTao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (MaUser) REFERENCES Users(MaUser) ON DELETE CASCADE,
    FOREIGN KEY (MaDonHang) REFERENCES DonHang(MaDonHang) ON DELETE CASCADE,
    UNIQUE(MaDonHang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Thêm dữ liệu mẫu mới (Đã bỏ giá trị 'da_duyet')
INSERT INTO DanhGia (MaUser, MaDonHang, Diem, NoiDung) VALUES
(2, 1, 5, 'Giao hàng nhanh, đồ ăn vẫn còn nóng hổi. Rất hài lòng!'),
(3, 2, 4, 'Đóng gói cẩn thận, shipper thân thiện nhưng giao hơi trễ xíu.');