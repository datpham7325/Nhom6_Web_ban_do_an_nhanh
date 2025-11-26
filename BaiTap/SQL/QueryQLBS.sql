USE QL_Ban_Sua;
-- Phần 1: Truy vấn lọc và sắp xếp dữ liệu
-- 1. Liệt kê danh sách hãng sữa gồm có tên hãng sữa, địa chỉ, điện thoại.
SELECT Ten_Hang_Sua, Dia_chi, Dien_thoai
FROM HANG_SUA;
-- 2. Liệt kê danh sách khách hàng gồm có các thông tin sau: tên khách hàng, địa chỉ, điện thoại, danh sách sẽ được sắp theo thứ tự tên khách hàng tăng dần.
SELECT Ten_Khach_Hang, Dia_chi, Dien_thoai
FROM KHACH_HANG
ORDER BY Ten_Khach_Hang ASC;
-- 3. Liệt kê danh sách khách hàng gồm có các thông tin sau: tên khách hàng, phái, địa chỉ, điện thoại, danh sách sẽ được sắp theo thứ tự nam trước, nữ sau.
SELECT Ten_Khach_Hang, Phai, Dia_chi, Dien_thoai
FROM KHACH_HANG
ORDER BY FIELD(Phai, "Nam", "Nữ") ASC;
-- 4. Liệt kê danh sách sữa gồm có: tên sữa, trọng lượng, đơn giá. Có sắp tăng theo cột tên sữa, và sắp giảm theo cột đơn giá
SELECT Ten_sua, Trong_luong, Don_gia
FROM SUA
ORDER BY Ten_sua ASC, Don_gia DESC;
-- 5. Liệt kê danh sách sữa gồm có: tên sữa, trọng lượng, đơn giá, thành phần dinh dưỡng. Chỉ liệt kê các sữa có tên bắt đầu là 'S'.
SELECT Ten_sua, Trong_luong, Don_gia, TP_Dinh_Duong
FROM SUA
WHERE Ten_sua LIKE 'S%';
-- 6. Liệt kê danh sách các hãng sữa có ký tự cuối cùng của mã hãng sữa là 'M', gồm có các thông tin sau: mã hãng sữa, tên hãng sữa, địa chỉ, điện thoại.
SELECT Ma_Hang_Sua, Ten_Hang_Sua, Dia_chi, Dien_thoai
FROM HANG_SUA
WHERE Ma_Hang_Sua LIKE '%M';
-- 7. Liệt kê danh sách sữa mà trong tên sữa có từ 'grow'
SELECT *
FROM SUA
WHERE Ten_sua LIKE '%grow%';
-- 8. Liệt kê danh sách sữa có đơn giá lớn hơn 100.000 VNĐ, gồm các thông tin: tên sữa, đơn giá, trọng lượng, danh sách được xếp theo thứ tự tên sữa giảm dần.
SELECT Ten_sua, Don_gia,Trong_luong
FROM SUA
WHERE Don_gia > 100000
ORDER BY Ten_sua DESC;
-- 9. Cho biết các sữa có mã loại sữa là 'SC' và có mã hãng sữa là 'VNM' gồm các thông tin sau: tên sữa, thành phần dinh dưỡng, lợi ích, trong đó tên sữa sắp theo thứ tự tăng dần
SELECT Ten_sua, TP_Dinh_Duong, Loi_ich
FROM SUA
WHERE Ma_Loai_Sua LIKE 'SC' AND Ma_Hang_Sua LIKE 'VNM'
ORDER BY Ten_sua ASC;
-- 10. Liệt kê danh sách sữa có trọng lượng lớn hơn hay bằng 900 gr hoặc mã hãng sữa là 'DS'
SELECT *
FROM SUA
WHERE Trong_luong >= 900 OR Ma_Hang_Sua LIKE 'DS';
-- 11. Liệt kê danh sách các sữa có đơn giá từ 100.000 VNĐ đến 150.000 VNĐ
SELECT *
FROM SUA
WHERE Don_gia BETWEEN 100000 AND 150000;
-- 12. Liệt kê các sữa có mã hãng sữa là 'DM' hay 'DL' hay 'DS' và có trọng lượng lớn hơn hay bằng 800 gr, sắp tăng dần theo trọng lượng.
SELECT *
FROM SUA
WHERE (Ma_Hang_Sua IN ('DM','DL','DS')) AND Trong_luong > 800
ORDER BY Trong_luong ASC;
-- 13. Liệt kê các sữa có mã loại là 'SD' hoặc có giá tiền nhỏ hơn hay bằng 12.000 VNĐ
SELECT *
FROM SUA
WHERE Ma_Loai_Sua LIKE 'SD' OR Don_gia <= 12000;
-- 14. Liệt kê những khách hàng nam, và có họ tên bắt đầu là 'N'
SELECT *
FROM KHACH_HANG
WHERE Phai = "Nam" AND Ten_Khach_Hang LIKE 'N%'
-- 15. Liệt kê tên các hãng sữa mà mã hãng sữa không có ký tự 'M'
SELECT Ten_Hang_Sua
FROM HANG_SUA
WHERE Ma_Hang_Sua NOT LIKE '%M%';
-- 16. Liệt kê các sữa có thành phần dinh dưỡng chứa 'canxi' và 'vitamin', gồm các thông tin: tên sữa, thành phần dinh dưỡng.
SELECT Ten_sua, TP_Dinh_Duong
FROM SUA
WHERE TP_Dinh_Duong LIKE '%canxi%' AND TP_Dinh_Duong LIKE '%vitamin%';
-- 17. Liệt kê các sản phẩm sữa có trọng lượng là 180gr, 200gr hoặc 900 gr
SELECT *
FROM SUA
WHERE Trong_luong IN (180,200,900);
-- 18. Liệt kê các sản phẩm sữa có trọng lượng không là 400gr, 800gr,900gr
SELECT *
FROM SUA
WHERE Trong_luong NOT IN (400,800,900);
-- 19. Cho biết tên sữa, đơn giá, thành phần dinh dưỡng của 10 sữa có đơn giá cao nhất
SELECT Ten_sua, Don_gia, TP_Dinh_Duong
FROM SUA
ORDER BY Don_gia DESC
LIMIT 10;
-- 20. Cho biết 3 sản phẩm sữa của hãng Vinamilk có trọng lượng nặng nhất, gồm các thông tin: Tên sữa, trọng lượng
SELECT Ten_sua, Trong_luong
FROM SUA
WHERE Ma_Hang_Sua IN (
  SELECT Ma_Hang_Sua 
  FROM HANG_SUA 
  WHERE Ten_Hang_Sua LIKE '%Vinamilk%'
  )
ORDER BY Trong_luong DESC
LIMIT 3
-- 21. Liệt kê các sữa của hãng Vinamilk gồm các thông tin: tên sữa, lợi ích, đơn giá, trong đó đơn giá sắp giảm dần.
SELECT Ten_sua, Loi_ich, Don_gia
FROM SUA
WHERE Ma_Hang_Sua IN (
  SELECT Ma_Hang_Sua 
  FROM HANG_SUA 
  WHERE Ten_Hang_Sua LIKE '%Vinamilk%'
  )
ORDER BY Don_gia DESC
-- 22. Liệt kê danh sách các sữa của hãng Abbott có: tên sữa, trọng lượng, lợi ích, trong đó trọng lượng sắp tăng dần.
SELECT Ten_sua, Trong_luong, Loi_ich
FROM SUA
WHERE Ma_Hang_Sua IN (
    SELECT Ma_Hang_Sua 
    FROM HANG_SUA 
    WHERE Ten_Hang_Sua LIKE '%Abbott%'
  )
ORDER BY Trong_luong ASC;
-- Phần 2: Sử dụng hàm và biểu thức cho sẵn trong truy vấn dữ liệu
-- 1. Cho biết trị giá trung bình của các hóa đơn được làm tròn đến hàng nghìn.
SELECT ROUND(AVG(Tri_gia), -3) AS Tri_gia_TB
FROM HOA_DON;
-- 2. Liệt kê danh sách các hóa đơn trong tháng 7 năm 2007 (dùng hàm day, month, year)
SELECT *
FROM HOA_DON
WHERE MONTH(Ngay_HD) = 7 AND YEAR(Ngay_HD) = 2007;
-- 3. Liệt kê các hóa đơn và có thêm một cột là số ngày (bằng ngày hiện tại – ngày hóa đơn (datediff, hàm date, hàm curdate) sắp theo cột số ngày giảm dần 
SELECT So_Hoa_Don, Ngay_HD,
       DATEDIFF(CURDATE(), Ngay_HD) AS So_Ngay
FROM HOA_DON
ORDER BY So_Ngay DESC;
-- 4. Cho biết các sữa mà tên sữa có chiều dài nhỏ hơn hay bằng 10 ký tự (dùng hàm length)
SELECT Ma_Sua, Ten_sua, LENGTH(Ten_sua) AS Chieu_dai_ten
FROM SUA
WHERE LENGTH(Ten_sua) <= 10;
-- 5. Liệt kê danh sách các hãng sữa có tên hãng sữa, địa chỉ, điện thoại, trong đó tên hãng sữa in HOA (dùng hàm upper)
SELECT UPPER(Ten_Hang_Sua) AS Ten_Hang_Sua, Dia_chi, Dien_thoai
FROM HANG_SUA;

-- 6. Liệt kê danh sách hóa đơn kèm theo ngày được định dạng như sau "Thứ - ngày – tháng – năm" (theo dạng tiếng Anh)
SELECT So_Hoa_Don,
       DATE_FORMAT(Ngay_HD, '%W - %d - %M - %Y') AS Ngay_Dinh_Dang
FROM HOA_DON;

-- 7. Liệt kê danh sách sữa đã bán được trong tháng 8 năm 2007 có tên sữa, trọng lượng, đơn giá, trong đó: trọng lượng có thêm 'gr', đơn giá có định dạng tiền tệ và có thêm 'VNĐ'
SELECT S.Ten_sua,
       CONCAT(S.Trong_luong, ' gr') AS Trong_luong,
       CONCAT(FORMAT(S.Don_gia, 0), ' VNĐ') AS Don_gia
FROM SUA S
JOIN CT_HOADON C ON S.Ma_Sua = C.Ma_Sua
JOIN HOA_DON H ON C.So_Hoa_Don = H.So_Hoa_Don
WHERE MONTH(H.Ngay_HD) = 8 AND YEAR(H.Ngay_HD) = 2007;

-- 8. Liệt kê danh sách khách hàng gồm: MAKH – Tên khách hàng (thành 1 cột có tên là ma_ten_KH)(concat), phái (nam – nữ) (dùng if(…))
SELECT Ma_Khach_Hang,
       CONCAT(Ma_Khach_Hang, ' - ', Ten_Khach_Hang) AS ma_ten_KH,
       IF(Phai = 'Nam', 'Nam', 'Nữ') AS Gioi_tinh
FROM KHACH_HANG;

-- 9. Liệt kê danh sách sữa có trọng lượng từ 400gr đến 500 gr, có thêm cột đánh giá như sau: nếu giá sữa nhỏ hơn 100.000 VNĐ thì đánh giá là "Sữa giá trung bình", nếu giá trên 100.000 VNĐ thì đánh giá là"Sữa giá cao" (dùng if(…))
SELECT Ten_sua, Trong_luong, Don_gia,
       IF(Don_gia < 100000, 'Sữa giá trung bình', 'Sữa giá cao') AS Danh_gia
FROM SUA
WHERE Trong_luong BETWEEN 400 AND 500;

-- 10. Liệt kê danh sách hóa đơn kèm theo ngày được định dạng như sau "Thứ … (theo dạng tiếng Việt) ngày… tháng … năm …", sắp theo ngày tăng dần (dùng case dạng đơn giản: case … when … then)
SELECT So_Hoa_Don,
       CASE DAYOFWEEK(Ngay_HD)
            WHEN 1 THEN CONCAT('Chủ nhật, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
            WHEN 2 THEN CONCAT('Thứ hai, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
            WHEN 3 THEN CONCAT('Thứ ba, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
            WHEN 4 THEN CONCAT('Thứ tư, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
            WHEN 5 THEN CONCAT('Thứ năm, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
            WHEN 6 THEN CONCAT('Thứ sáu, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
            WHEN 7 THEN CONCAT('Thứ bảy, ngày ', DAY(Ngay_HD), ' tháng ', MONTH(Ngay_HD), ' năm ', YEAR(Ngay_HD))
       END AS Ngay_TiengViet
FROM HOA_DON
ORDER BY Ngay_HD ASC;

-- 11. Thống kê số khách hàng nam – số hàng nữ và tổng số khách hàng.
SELECT
    SUM(CASE WHEN Phai = 'Nam' THEN 1 ELSE 0 END) AS So_Nam,
    SUM(CASE WHEN Phai = 'Nữ' THEN 1 ELSE 0 END) AS So_Nu,
    COUNT(*) AS Tong_Khach_Hang
FROM KHACH_HANG;
-- Phần 3:  Truy vấn có nhóm và thống kê dữ liệu
-- 1. Thống kê tổng số sản phẩm theo hãng sữa, gồm các thông tin: tên hãng sữa, tổng số sản phẩm. Có sắp tăng theo tổng số sản phẩm
SELECT h.Ma_Hang_Sua,
       h.Ten_Hang_Sua,
       COUNT(s.Ma_Sua) AS So_San_Pham
FROM HANG_SUA h
LEFT JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua
ORDER BY So_San_Pham ASC;

-- 2. Cho biết đơn giá trung bình của sữa có trọng lượng là 800gr hay 900gr theo từng hãng sữa.
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua,
       AVG(s.Don_gia) AS Don_gia_TB
FROM HANG_SUA h
JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
WHERE s.Trong_luong IN (800, 900)
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua;

-- 3. Cho biết trọng lượng đóng gói nhỏ nhất của từng hãng sữa (làm tương tự cho lớn nhất).
-- nhỏ nhất
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua, MIN(s.Trong_luong) AS Trong_luong_nho_nhat
FROM HANG_SUA h
JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua;

-- lớn nhất
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua, MAX(s.Trong_luong) AS Trong_luong_lon_nhat
FROM HANG_SUA h
JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua;

-- 4. Cho biết tổng giá tiền và số sản phẩm của sữa có trọng lượng trong khoảng 400gr và 500 gr theo từng hãng sữa.
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua,
       COUNT(s.Ma_Sua) AS So_San_Pham,
       SUM(s.Don_gia) AS Tong_Don_gia_San_Pham
FROM HANG_SUA h
JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
WHERE s.Trong_luong BETWEEN 400 AND 500
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua;

-- 5. Thống kê hóa đơn gồm các thông tin sau: số hóa đơn - ngày hóa đơn – tổng số lượng – tổng thành tiền.
SELECT h.So_Hoa_Don,
       h.Ngay_HD,
       SUM(ct.So_luong) AS Tong_So_Luong,
       SUM(ct.So_luong * ct.Don_gia) AS Tong_Thanh_Tien
FROM HOA_DON h
JOIN CT_HOADON ct ON ct.So_Hoa_Don = h.So_Hoa_Don
GROUP BY h.So_Hoa_Don, h.Ngay_HD
ORDER BY h.Ngay_HD;

-- 6. Hãy cho biết những hóa đơn mua hàng có tổng trị giá lớn hơn 2.000.000 VNĐ
SELECT h.So_Hoa_Don,
       h.Ngay_HD,
       SUM(ct.So_luong * ct.Don_gia) AS Tong_Tri_Gia
FROM HOA_DON h
JOIN CT_HOADON ct ON ct.So_Hoa_Don = h.So_Hoa_Don
GROUP BY h.So_Hoa_Don, h.Ngay_HD
HAVING SUM(ct.So_luong * ct.Don_gia) > 2000000
ORDER BY Tong_Tri_Gia DESC;

-- 7. Cho biết tổng số sữa của mỗi loại sữa, gồm các thông tin: Tên loại sữa, tổng số sản phẩm.
SELECT l.Ma_Loai_Sua, l.Ten_loai, COUNT(s.Ma_Sua) AS So_San_Pham
FROM LOAI_SUA l
LEFT JOIN SUA s ON s.Ma_Loai_Sua = l.Ma_Loai_Sua
GROUP BY l.Ma_Loai_Sua, l.Ten_loai
ORDER BY So_San_Pham DESC;

-- 8. Cho biết đơn giá cao nhất của mỗi hãng sữa, gồm thông tin: tên hãng sữa, đơn giá.
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua, MAX(s.Don_gia) AS Don_gia_Max
FROM HANG_SUA h
JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua
ORDER BY Don_gia_Max DESC;

-- 9. Thống kê số sản phẩm bán được trong tháng 8-2007 của mỗi sữa
SELECT s.Ma_Sua, s.Ten_sua, SUM(ct.So_luong) AS So_Luong_Ban
FROM SUA s
JOIN CT_HOADON ct ON ct.Ma_Sua = s.Ma_Sua
JOIN HOA_DON h ON ct.So_Hoa_Don = h.So_Hoa_Don
WHERE YEAR(h.Ngay_HD) = 2007 AND MONTH(h.Ngay_HD) = 8
GROUP BY s.Ma_Sua, s.Ten_sua
ORDER BY So_Luong_Ban DESC;

-- 10. Cho biết danh sách những hãng sữa không có sản phẩm nào có đơn giá nhỏ hơn 50.000 VNĐ gồm các thông tin: tên hãng sữa, địa chỉ, số điện thoại
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua, h.Dia_chi, h.Dien_thoai
FROM HANG_SUA h
WHERE NOT EXISTS (
    SELECT 1 FROM SUA s
    WHERE s.Ma_Hang_Sua = h.Ma_Hang_Sua
      AND s.Don_gia < 50000
);

-- 11. Cho biết danh sách những hãng sữa có nhiều hơn 10 sản phẩm, gồm mã hãng sữa, tên hãng sữa, số sản phẩm.
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua, COUNT(s.Ma_Sua) AS So_San_Pham
FROM HANG_SUA h
JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua
HAVING COUNT(s.Ma_Sua) > 10
ORDER BY So_San_Pham DESC;

-- 12. Liệt kê danh sách các hãng sữa, số sản phẩm của từng hãng, bổ sung thêm cột ghi chú. Tùy thuộc vào số sản phẩm của từng hãng sữa – nếu số sản phẩm <5 thì sẽ ghi chú là "Có ít sản phẩm", từ 5 đến 10 sản phẩm thì ghi chú là "Có khá nhiều sản phẩm" và ngược lại thì ghi chú là "Có rất nhiều sản phẩm".(dùng case dạng biểu thức: case … when … then) -> đế qua phần thống kê
SELECT h.Ma_Hang_Sua, h.Ten_Hang_Sua,
       COUNT(s.Ma_Sua) AS So_San_Pham,
       CASE
         WHEN COUNT(s.Ma_Sua) < 5 THEN 'Có ít sản phẩm'
         WHEN COUNT(s.Ma_Sua) BETWEEN 5 AND 10 THEN 'Có khá nhiều sản phẩm'
         ELSE 'Có rất nhiều sản phẩm'
       END AS Ghi_Chu
FROM HANG_SUA h
LEFT JOIN SUA s ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
GROUP BY h.Ma_Hang_Sua, h.Ten_Hang_Sua
ORDER BY So_San_Pham;

-- 13. Hãy cho biết tổng số lượng bán của các mặt hàng sữa thuộc hãng ABBOTT từ tháng 7-2007 đến tháng 8-2007 (Câu này đưa lên phần thống kê)
SELECT SUM(ct.So_luong) AS Tong_So_Luong_Ban
FROM CT_HOADON ct
JOIN SUA s ON ct.Ma_Sua = s.Ma_Sua
JOIN HANG_SUA hs ON s.Ma_Hang_Sua = hs.Ma_Hang_Sua
JOIN HOA_DON h ON ct.So_Hoa_Don = h.So_Hoa_Don
WHERE hs.Ten_Hang_Sua LIKE '%Abbott%'
  AND YEAR(h.Ngay_HD) = 2007
  AND MONTH(h.Ngay_HD) BETWEEN 7 AND 8;
-- Phần 4: Truy vấn con
-- 1. Liệt kê hãng sữa không đóng gói sản phẩm có trọng lượng 900gr
SELECT Ten_Hang_Sua
FROM HANG_SUA
WHERE Ma_Hang_Sua NOT IN (
    SELECT DISTINCT Ma_Hang_Sua
    FROM SUA
    WHERE Trong_luong = 900
);

-- 2. Liệt kê các khách hàng chưa mua hàng
SELECT Ma_Khach_Hang, Ten_Khach_Hang
FROM KHACH_HANG
WHERE Ma_Khach_Hang NOT IN (
    SELECT DISTINCT Ma_Khach_Hang FROM HOA_DON
);

-- 3. Liệt kê danh sách sữa có cùng hãng sữa với sữa có mã sữa là ‘AB0002’
SELECT Ten_sua, Ma_Hang_Sua
FROM SUA
WHERE Ma_Hang_Sua = (
    SELECT Ma_Hang_Sua FROM SUA WHERE Ma_sua = 'AB0002'
);

-- 4. Liệt kê các hãng chưa có sản phẩm sữa
SELECT Ten_Hang_Sua
FROM HANG_SUA
WHERE Ma_Hang_Sua NOT IN (
    SELECT DISTINCT Ma_Hang_Sua FROM SUA
);

-- 5. Liệt kê các sữa có đơn giá cao nhất theo từng hãng sữa
SELECT s.Ten_sua, s.Don_gia, h.Ten_Hang_Sua
FROM SUA s
JOIN HANG_SUA h ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
WHERE Don_gia = (
    SELECT MAX(Don_gia)
    FROM SUA
    WHERE Ma_Hang_Sua = s.Ma_Hang_Sua
);

-- 6. Hãy cho biết loại sữa nào mà hãng Abbott không có sản phẩm
SELECT Ten_loai
FROM LOAI_SUA
WHERE Ma_loai_sua NOT IN (
    SELECT DISTINCT Ma_loai_sua
    FROM SUA s
    JOIN HANG_SUA h ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
    WHERE h.Ten_Hang_Sua = 'Abbott'
);

-- 7. Danh sách các sữa bột có giá tiền nhỏ hơn giá tiền nhỏ nhất của sữa bột thuộc hãng sữa Vinamilk
SELECT Ten_sua, Don_gia
FROM SUA s
JOIN HANG_SUA h ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
JOIN LOAI_SUA l ON s.Ma_loai_sua = l.Ma_loai_sua
WHERE l.Ten_loai = 'Sữa bột'
  AND Don_gia < (
        SELECT MIN(Don_gia)
        FROM SUA s2
        JOIN HANG_SUA h2 ON s2.Ma_Hang_Sua = h2.Ma_Hang_Sua
        JOIN LOAI_SUA l2 ON s2.Ma_loai_sua = l2.Ma_loai_sua
        WHERE h2.Ten_Hang_Sua = 'Vinamilk' AND l2.Ten_loai = 'Sữa bột'
  );

-- 8. Danh sách sữa có trọng lượng lớn nhất (nhỏ nhất) ứng với mỗi hãng sữa, gồm các thông tin: tên hãng sữa, tên sữa, trọng lượng
SELECT h.Ten_Hang_Sua, s.Ten_sua, s.Trong_luong
FROM SUA s
JOIN HANG_SUA h ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
WHERE s.Trong_luong = (
    SELECT MAX(Trong_luong)
    FROM SUA
    WHERE Ma_Hang_Sua = s.Ma_Hang_Sua
);

-- 9. Danh sách các sữa có giá tiền cao nhất theo từng hãng, có loại sữa là 'SB' và trọng lượng lớn hơn hoặc bằng 400 gr, gồm các thông tin: tên hãng sữa, tên sữa, trọng lượng, đơn giá
SELECT h.Ten_Hang_Sua, s.Ten_sua, s.Trong_luong, s.Don_gia
FROM SUA s
JOIN HANG_SUA h ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
WHERE s.Ma_loai_sua = 'SB'
  AND s.Trong_luong >= 400
  AND s.Don_gia = (
        SELECT MAX(Don_gia)
        FROM SUA
        WHERE Ma_Hang_Sua = s.Ma_Hang_Sua
          AND Ma_loai_sua = 'SB'
          AND Trong_luong >= 400
  );

-- 10. Cho biết sữa nào của hãng 'Dutch Lady' có trọng lượng cao nhất, gồm các thông tin: tên hãng, tên sữa, trọng lượng.
SELECT h.Ten_Hang_Sua, s.Ten_sua, s.Trong_luong
FROM SUA s
JOIN HANG_SUA h ON s.Ma_Hang_Sua = h.Ma_Hang_Sua
WHERE h.Ten_Hang_Sua = 'Dutch Lady'
  AND s.Trong_luong = (
        SELECT MAX(Trong_luong)
        FROM SUA s2
        JOIN HANG_SUA h2 ON s2.Ma_Hang_Sua = h2.Ma_Hang_Sua
        WHERE h2.Ten_Hang_Sua = 'Dutch Lady'
  );
-- Phần 5: Truy vấn tạo bảng
-- 1. Hãy tạo ra một bảng mới có tên là bang_tam có cấu trúc giống như bảng sữa
CREATE TABLE bang_tam LIKE SUA;

-- 2. Hãy tạo một bảng mới có tên là bang_vinamilk có cấu trúc giống như bảng sữa.
CREATE TABLE bang_vinamilk LIKE SUA;
-- Phần 6: Truy vấn thêm mới
-- 1. Thêm một khách hàng mới vào bảng khách hàng với những thông tin sau:KH007 – Mai Anh – 1 – 132 Quang Trung Q.GV TP.HCM – 8954671 – mai_anh@hotmail.com
INSERT INTO KHACH_HANG (Ma_Khach_Hang, Ten_Khach_Hang, Phai, Dia_Chi, Dien_Thoai, Email)
VALUES ('KH007', 'Mai Anh', 1, '132 Quang Trung Q.GV TP.HCM', '8954671', 'mai_anh@hotmail.com');

-- 2. Thêm một hãng sữa mới vào bảng hãng sữa với những thông tin sau:XO – XO – Công ty nhập khẩu Việt Nam – 8965874 – xo@xo.com
INSERT INTO HANG_SUA (Ma_Hang_Sua, Ten_Hang_Sua, Dia_Chi, Dien_Thoai, Email)
VALUES ('XO', 'XO', 'Công ty nhập khẩu Việt Nam', '8965874', 'xo@xo.com');

-- 3. Thêm các thông tin có trong bảng sữa vào bang_tam
INSERT INTO bang_tam
SELECT *
FROM SUA;

-- 4. Thêm các thông tin của sữa Vinamilk có trong bảng sữa vào bảng bang_Vinamilk
INSERT INTO bang_vinamilk
SELECT *
FROM SUA
WHERE Ma_Hang_Sua = (
    SELECT Ma_Hang_Sua FROM HANG_SUA WHERE Ten_Hang_Sua = 'Vinamilk'
);
-- Phần 7: Truy vấn cập nhật dữ liệu
-- 1. Hãy cập nhật lại giá tiền cho sữa trong bảng tạm có tên là 'canximex': giá mới là 116000 VNĐ
UPDATE bang_tam
SET Don_gia = 116000
WHERE Ten_sua = 'canximex';

-- 2. Hãy cập nhật lại tên cho khách hàng có mã khách hàng 'KH005': tên mới là 'Lê Duy Anh'
UPDATE KHACH_HANG
SET Ten_Khach_Hang = 'Lê Duy Anh'
WHERE Ma_Khach_Hang = 'KH005';

-- 3. Hãy cập nhật lại đơn giá của sữa trong bảng tạm theo công thức sau: đơn giá = đơn giá cũ + 3%
UPDATE bang_tam
SET Don_gia = Don_gia * 1.03;

-- 4. Hãy cập nhật lại tên của loại sữa chua thành sữa yaourt
UPDATE LOAI_SUA
SET Ten_Loai_Sua = 'Sữa yaourt'
WHERE Ten_Loai_Sua = 'Sữa chua';

-- 5. Hãy cập nhật lại đơn giá cho các sữa của hãng sữa Abbott: mỗi sữa có đơn giá tăng thêm 3000 VNĐ
UPDATE SUA
SET Don_gia = Don_gia + 3000
WHERE Ma_Hang_Sua IN (
    SELECT Ma_Hang_Sua FROM HANG_SUA WHERE Ten_Hang_Sua = 'Abbott'
);

-- 6. Hãy tạo thêm cột trị giá cho bảng hoa_don sau đó tính trị giá cho mỗi hóa đơn và cập nhật cho cột trị giá của bảng này
ALTER TABLE HOA_DON
ADD COLUMN Tri_gia DECIMAL(12,0);
UPDATE HOA_DON hd
SET Tri_gia = (
    SELECT SUM(ct.So_luong * ct.Don_gia)
    FROM CT_HOADON ct
    WHERE ct.So_Hoa_Don = hd.So_Hoa_Don
);
-- Phần 8: Truy vấn xóa dữ liệu
-- 1. Hãy xóa khách hàng có mã khách hàng là 'KH007' trong bảng khách hàng
DELETE FROM KHACH_HANG
WHERE Ma_Khach_Hang = 'KH007';

-- 2. Hãy xóa tất cả những sữa của hãng Dumex có trong bảng bang_tam
DELETE FROM bang_tam
WHERE Ma_Hang_Sua = (
    SELECT Ma_Hang_Sua
    FROM HANG_SUA
    WHERE Ten_Hang_Sua = 'Dumex'
);

-- 3. Hãy xóa những sữa có trọng lượng nhỏ hơn 200gr hoặc có đơn giá nhỏ hơn 10000 VNĐ trong bang_tam
DELETE FROM bang_tam
WHERE Trong_luong < 200
   OR Don_gia < 10000;

-- 4. Hãy xóa những sữa của hãng Vinamilk có đơn giá lớn hơn 80000 VNĐ trong bang_tam
DELETE FROM bang_tam
WHERE Don_gia > 80000
  AND Ma_Hang_Sua = (
      SELECT Ma_Hang_Sua
      FROM HANG_SUA
      WHERE Ten_Hang_Sua = 'Vinamilk'
  );

-- 5. Hãy xóa những sữa thành phần dinh dưỡng không có 'canxi' trong bang_tam
DELETE FROM bang_tam
WHERE TP_Dinh_Duong NOT LIKE '%canxi%';

-- 6. Hãy xoá hãng sữa không có sản phẩm sữa nào
DELETE FROM HANG_SUA
WHERE Ma_Hang_Sua NOT IN (
    SELECT DISTINCT Ma_Hang_Sua
    FROM SUA
);

