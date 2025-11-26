CREATE DATABASE QL_Ban_Sua;
USE QL_Ban_Sua;

-- BẢNG HÃNG SỮA
CREATE TABLE HANG_SUA(
  Ma_Hang_Sua VARCHAR(20) PRIMARY KEY,
  Ten_Hang_Sua VARCHAR(100) NOT NULL,
  Dia_chi VARCHAR(200),
  Dien_thoai VARCHAR(20),
  Email VARCHAR(100)
);

-- BẢNG LOẠI SỮA
CREATE TABLE LOAI_SUA(
  Ma_Loai_Sua VARCHAR(3) PRIMARY KEY,
  Ten_loai VARCHAR(50) NOT NULL
);

-- BẢNG SỮA
CREATE TABLE SUA(
  Ma_Sua VARCHAR(6) PRIMARY KEY,
  Ten_sua VARCHAR(100) NOT NULL,
  Ma_Hang_Sua VARCHAR(20) NOT NULL,
  Ma_Loai_Sua VARCHAR(3) NOT NULL,
  Trong_luong INT,
  Don_gia INT,
  TP_Dinh_Duong TEXT,
  Loi_ich TEXT,
  Hinh VARCHAR(200),
  FOREIGN KEY (Ma_Hang_Sua) REFERENCES HANG_SUA(Ma_Hang_Sua),
  FOREIGN KEY (Ma_Loai_Sua) REFERENCES LOAI_SUA(Ma_Loai_Sua)
);

-- BẢNG KHÁCH HÀNG
CREATE TABLE KHACH_HANG(
  Ma_Khach_Hang VARCHAR(5) PRIMARY KEY,
  Ten_Khach_Hang VARCHAR(100) NOT NULL,
  Phai ENUM('Nam','Nữ') NOT NULL,
  Dia_chi VARCHAR(200),
  Dien_thoai VARCHAR(20),
  Email VARCHAR(100)
);

-- BẢNG HÓA ĐƠN
CREATE TABLE HOA_DON(
  So_Hoa_Don VARCHAR(5) PRIMARY KEY,
  Ngay_HD DATE NOT NULL,
  Ma_Khach_Hang VARCHAR(5) NOT NULL,
  Tri_gia DOUBLE,
  FOREIGN KEY (Ma_Khach_Hang) REFERENCES KHACH_HANG(Ma_Khach_Hang)
);

-- BẢNG CHI TIẾT HÓA ĐƠN
CREATE TABLE CT_HOADON(
  So_Hoa_Don VARCHAR(5),
  Ma_Sua VARCHAR(6),
  So_luong INT,
  Don_gia INT,
  PRIMARY KEY (So_Hoa_Don, Ma_Sua),
  FOREIGN KEY (So_Hoa_Don) REFERENCES HOA_DON(So_Hoa_Don),
  FOREIGN KEY (Ma_Sua) REFERENCES SUA(Ma_Sua)
);

-- DỮ LIỆU MẪU

INSERT INTO HANG_SUA VALUES
('AB', 'Abbott', 'KCN Biên Hòa - Đồng Nai', '8741258', 'abbott@ab.com'),
('DL', 'Dutch Lady', 'KCN Biên Hòa - Đồng Nai', '7826451', 'dutchlady@dl.com'),
('DM', 'Dumex', 'KCN Sóng Thần - Bình Dương', '6258943', 'dumex@dm.com'),
('DS', 'Daisy', 'KCN Sóng Thần - Bình Dương', '5789321', 'daisy@ds.com'),
('MJ', 'Mead Jonhson', 'Cty nhập khẩu VN', '8741258', 'meadjohn@mj.com'),
('NTF', 'Nutifood', 'KCN Sóng Thần - Bình Dương', '7895632', 'nutifood@ntf.com'),
('VNM', 'Vinamilk', '123 Nguyễn Du - Q1 - TP.HCM', '8794561', 'vinamilk@vnm.com');

INSERT INTO LOAI_SUA VALUES
('SB', 'Sữa bột'),
('SC', 'Sữa chua'),
('SD', 'Sữa đặc'),
('ST', 'Sữa tươi');

INSERT INTO KHACH_HANG VALUES
('kh001', 'Khuất Thùy Phương', 'Nữ', 'A21 Nguyễn Oanh Q.Gò Vấp', '9874125', 'ktphuong@hcmuns.edu.vn'),
('kh002', 'Đỗ Lâm Thiên', 'Nam', '357 Lê Hồng Phong Q.10', '8351056', 'dlthien@hcmuns.edu.vn'),
('kh003', 'Phạm Thị Nhung', 'Nữ', '56 Đinh Tiên Hoàng Q.1', '9745698', 'ptnhung@hcmuns.edu.vn'),
('kh004', 'Nguyễn Khắc Thiện', 'Nam', '12bis Đường 3-2 Q.10', '8769128', 'nkthien@hcmuns.edu.vn'),
('kh005', 'Tô Trần Hồ Giảng', 'Nam', '75 Nguyễn Kiệm Q.Gò Vấp', '5792564', 'tthgiang@hcmuns.edu.vn'),
('kh006', 'Nguyễn Kiến Thi', 'Nữ', '357 Lê Hồng Phong Q.10', '9874125', 'nkthi@hcmuns.edu.vn'),
('kh007', 'Trần Quốc Thông', 'Nam', '123 Trần Hưng Đạo', '8754123', 'tqthong@hcmuns.edu.vn'),
('kh008', 'Nguyễn Anh Tuấn', 'Nam', '1/2bis Nơ Trang Long Q.BT', '8753159', 'natuan@hcmuns.edu.vn');

INSERT INTO HOA_DON VALUES
('D001', '2024-01-01', 'kh001', 1480000),
('D002', '2024-02-01', 'kh002', 1656000),
('D003', '2024-03-01', 'kh003', 2484500),
('D004', '2024-04-01', 'kh002', 5573000);

-- Bảng SUA phải có dữ liệu trước khi thêm CT_HOADON
INSERT INTO SUA VALUES
('AB0001','Similac 1','AB','SB',400,107000,'DHA, Vitamin','Phát triển trí não','ab1.jpg'),
('AB0002','Similac 2','AB','SB',900,107000,'DHA, Canxi','Phát triển thể chất','ab2.jpg'),
('AB0003','Gain IQ','AB','SB',400,87000,'DHA, Lutein','Hỗ trợ thị lực','ab3.jpg'),
('DL0001','Dutch Lady Gold','DL','ST',180,41000,'Canxi, Vitamin D','Phát triển chiều cao','dl1.jpg'),
('DL0006','Yomilk','DL','SC',100,11500,'Men tiêu hóa, Canxi','Tốt cho tiêu hóa','dl6.jpg'),
('MJ0001','Enfa Grow','MJ','SB',900,196000,'DHA, MFGM','Hỗ trợ phát triển trí não','mj1.jpg'),
('MJ0004','Enfa A+','MJ','SB',900,198000,'DHA, Choline','Phát triển não bộ','mj4.jpg'),
('NTF001','Nuti IQ','NTF','SB',900,46500,'DHA, Taurin','Phát triển trí não','ntf1.jpg'),
('NTF002','Nuti Food Kid','NTF','SC',200,45000,'Canxi, Lysine','Giúp trẻ ăn ngon','ntf2.jpg'),
('VNM012','Vinamilk 100%','VNM','ST',220,103500,'Canxi, Vitamin A','Phát triển xương','vnm12.jpg');

INSERT INTO CT_HOADON VALUES
('D001', 'AB0001', 2, 107000),
('D001', 'DL0001', 12, 41000),
('D001', 'NTF002', 8, 45000),
('D001', 'VNM012', 4, 103500),
('D002', 'DL0001', 2, 41000),
('D002', 'MJ0001', 5, 196000),
('D002', 'MJ0004', 3, 198000),
('D003', 'AB0001', 8, 107000),
('D003', 'AB0003', 17, 87000),
('D003', 'DL0006', 13, 11500),
('D004', 'AB0001', 15, 107000),
('D004', 'AB0002', 25, 107000),
('D004', 'NTF001', 10, 46500),
('D004', 'VNM012', 8, 103500);
