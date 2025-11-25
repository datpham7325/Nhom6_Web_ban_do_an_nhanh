<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/insert.css">
    <!-- Thêm font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Mở kết nối -->
    <?php
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $dbname = "quanly_cua_hang";

        $conn = mysqli_connect($hostname, $username, $password, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
    ?>

    <?php 
        if (isset($_GET['btnaddnew'])) 
        {
            $tenMonAn = $_GET['tenmonan'];
            $hinhAnh = $_GET['hinhanh'];
            $maLoai = $_GET['loaimonan'];
            $maSize = $_GET['size'];
            $donGia = $_GET['gia'];
            $moTa = $_GET['mota'];

            // Thêm món ăn vào bảng monan
            $strSQL_Insert_MonAn = "INSERT INTO monan (TenMonAn, HinhAnh, MoTa, MaLoai) 
                                    VALUES ('$tenMonAn', '$hinhAnh', '$moTa', '$maLoai')";
            
            if (mysqli_query($conn, $strSQL_Insert_MonAn)) 
            {
                // Lấy MaMonAn vừa thêm để thêm vào bảng bienthemonan
                $maMonAn_New = mysqli_insert_id($conn);

                // Thêm biến thể món ăn vào bảng bienthemonan
                $strSQL_Insert_BienTheMonAn = "INSERT INTO bienthemonan (MaMonAn, MaSize, DonGia) 
                                                VALUES ('$maMonAn_New', '$maSize', '$donGia')";
                
                if (mysqli_query($conn, $strSQL_Insert_BienTheMonAn)) 
                {
                    // Thành công
                    echo "<p class='success-message'>Thêm món ăn thành công!</p>";
                } 
                else 
                {
                    // Thất bại khi thêm biến thể
                    echo "Lỗi khi thêm biến thể: " . mysqli_error($conn);
                }
            } 
            else 
            {
                // Thất bại khi thêm món ăn
                echo "Lỗi khi thêm món ăn: " . mysqli_error($conn);
            }
        }
        
        if( isset($_GET['btnback']) )
        {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            header("Location: home.php?page=$page");
            exit();
        }
    ?>

    <!-- /////////////////////////////////////////////////////////////////////////////////////////// -->
    <!-- Hàm xử lý -->
    <?php 
        // Chuẩn hóa mã
        function ChuanHoaMa($mma)
        {
            $kq = str_pad($mma, 3, '0', STR_PAD_LEFT);
            return $kq;
        }

        //Hàm xử lý kích thước món ăn
        function SizeMonAn($size)
        {
            $hostname = "localhost";
            $username = "root";
            $password = "";
            $database = "quanly_cua_hang";

            $conn = mysqli_connect($hostname, $username, $password, $database);
            $strSQL_Size = "SELECT * FROM kichthuoc";
            $result_Size = mysqli_query($conn, $strSQL_Size);

            while( mysqli_num_rows($result_Size)>0 )
            {
                if( $row = mysqli_fetch_assoc($result_Size) )
                {
                    if( $size == $row['MaSize'] )
                    {
                        $kq = $row['TenSize'];
                        break;
                    }
                }
            }

            return $kq;
        }

        //Xử lý list chọn size món
        $sizeList = [];
        $strSQL_AllSize = "SELECT MaSize FROM kichthuoc";
        $resultAllSize = mysqli_query($conn, $strSQL_AllSize);

        if(mysqli_num_rows($resultAllSize) > 0)
        {
            while($r = mysqli_fetch_assoc($resultAllSize))
            {
                $sizeList[] = $r['MaSize']; // Lưu tất cả size vào mảng
            }
        }

        //Xử lý nút thêm size mới
        if( isset($_GET['tensize']) )
        {
            if(isset($_GET['tensize']) && ($_GET['tensize']) != "")
            {
                $tenSize = $_GET['tensize'];

                $strSQL_tenSize = "INSERT INTO kichthuoc(TenSize) VALUES('$tenSize')";
                $result_Insert_tenSize = mysqli_query($conn, $strSQL_tenSize);

                if( $result_Insert_tenSize )
                {
                    header("Location: insert.php?mabienthe=$maBienThe");
                    exit();
                }
            }
        }

        // Nút xóa size
        if(isset($_GET['btnDeleteSize'])) 
        {
            $sizeToDelete = $_GET['size'];

            // Kiểm tra xem size có đang được sử dụng trong BienTheMonAn không
            $checkSQL = "SELECT COUNT(*) AS cnt FROM BienTheMonAn WHERE MaSize = '$sizeToDelete'";
            $resCheck = mysqli_query($conn, $checkSQL);
            $rowCheck = mysqli_fetch_assoc($resCheck);

            if($rowCheck['cnt'] > 0) 
            {
                // nếu size đang được dùng
                echo "Không thể xóa size này vì đang có biến thể món ăn sử dụng.";
            } 
            else 
            {
                // nếu size chưa được dùng, tiến hành xóa
                $deleteSQL = "DELETE FROM KichThuoc WHERE MaSize = '$sizeToDelete'";
                $resDelete = mysqli_query($conn, $deleteSQL);

                if($resDelete) 
                {
                    // nếu xóa thành công
                    echo "Xóa size thành công.";
                    header("Location: insert.php?mabienthe=$maBienThe"); // reload trang
                    exit();
                } 
                else 
                {
                    // nếu xóa thất bại
                    echo "Xóa thất bại: " . mysqli_error($conn);
                }
            }
        }


////////////////////////////////////////////////////////////////////////////////////////
        // Xử lý list chọn loại món ăn
        $loaiList = [];
        $strSQL_AllLoai = "SELECT MaLoai FROM loaimonan";
        $resultAllLoai = mysqli_query($conn, $strSQL_AllLoai);

        if(mysqli_num_rows($resultAllLoai) > 0)
        {
            while($r = mysqli_fetch_assoc($resultAllLoai))
            {
                $loaiList[] = $r['MaLoai']; 
            }
        }

        // Hàm xử lý loại món ăn
        function LoaiMonAn($maloai)
        {
            $hostname = "localhost";
            $username = "root";
            $password = "";
            $database = "quanly_cua_hang";

            $conn = mysqli_connect($hostname, $username, $password, $database);

            $sql = "SELECT TenLoai FROM loaimonan WHERE MaLoai='$maloai'";
            $result = mysqli_query($conn, $sql);

            if($row = mysqli_fetch_assoc($result))
                return $row['TenLoai'];

            return "";
        }

        // Nút thêm loại món ăn
        if( isset($_GET['tenloai']) )
        {
            if($_GET['tenloai'] != "")
            {
                $tenLoaiMoi = $_GET['tenloai'];

                $sqlThemLoai = "INSERT INTO loaimonan(TenLoai) VALUES('$tenLoaiMoi')";
                $resLoai = mysqli_query($conn, $sqlThemLoai);

                if($resLoai)
                {
                    header("Location: insert.php?mabienthe=$maBienThe");
                    exit();
                }
            }
        }

        // Nút xóa loại món ăn
        if(isset($_GET['btnDeleteLoai']))
        {
            $loaiToDelete = $_GET['loaimonan'];

            // Kiểm tra xem loại có đang được dùng
            $checkSQL = "SELECT COUNT(*) AS cnt FROM monan WHERE MaLoai = '$loaiToDelete'";
            $resCheck = mysqli_query($conn, $checkSQL);
            $rowCheck = mysqli_fetch_assoc($resCheck);

            if($rowCheck['cnt'] > 0) 
            {
                echo "Không thể xóa loại này vì đang được sử dụng.";
            } 
            else 
            {
                $deleteSQL = "DELETE FROM loaimonan WHERE MaLoai = '$loaiToDelete'";
                $resDelete = mysqli_query($conn, $deleteSQL);

                if($resDelete)
                {
                    header("Location: insert.php?mabienthe=$maBienThe");
                    exit();
                }
                else 
                {
                    echo "Xóa thất bại: " . mysqli_error($conn);
                }
            }
        }


    ?>



    <form class="form2">
        <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? (int)$_GET['page'] : 1; ?>">
        <table>

            <tr>
                <td colspan="2">
                    <h2>THÊM MÓN ĂN MỚI</h2>
                </td>
            </tr>

            <tr>
                <td class="tieude">Tên món ăn:</td>
                <td class="noidung">
                    <input type="text" name="tenmonan"
                        value="<?php echo isset($tenMonAn) ? $tenMonAn : '' ?>">
                </td>
            </tr>

            <tr>
                <td class="tieude">Hình ảnh:</td>
                <td class="noidung">
                    <input type="text" name="hinhanh"
                        value="<?php echo isset($hinhAnh) ? $hinhAnh : '' ?>">
                </td>
            </tr>

            <tr>
                <td class="tieude">Loại món ăn:</td>
                <td class="dropdownsize">
                    <select name="loaimonan">
                        <?php
                            foreach($loaiList as $maloai)
                            {
                                $tenLoaiList = LoaiMonAn($maloai);
                                $selected = ($tenLoai == $tenLoaiList) ? "selected" : "";
                                echo "<option value='$maloai' $selected>$tenLoaiList</option>";
                            }
                        ?>
                    </select>
                    <input type="submit" name="btnDeleteLoai" value="Xóa loại" onclick="return confirm('Bạn có chắc muốn xóa loại này?');">
                </td>
            </tr>

            <tr>
                <td class="tieude">Thêm loại món mới:</td>
                <td class="themloai">
                    <input type="text" class="loaimoi" name="tenloai" placeholder="Nhập loại món mới">
                    <input type="submit" class="btnloaimoi" name="btnthemloai" value="Thêm">
                </td>
            </tr>

            <tr>
                <td class="tieude">Size:</td>
                <td  class="dropdownsize">
                    <select name="size">
                        <?php
                            foreach($sizeList as $size)
                            {
                                $tenSizee = SizeMonAn($size); 
                                $selected = ($size == $maSize) ? "selected" : "";
                                echo "<option value='$size' $selected>$tenSizee</option>";
                            }
                        ?>
                    </select>
                    <input type="submit" name="btnDeleteSize" value="Xóa size" onclick="return confirm('Bạn có chắc muốn xóa size này?');">
                </td>
            </tr>

            <tr>
                <td class="tieude">
                    Thêm size mới:
                </td>
                <td class="themsize">
                    <input type="text" class="sizemoi" name="tensize" placeholder="Nhập size mới">
                    <input type="submit" class="btnsizemoi" name="btnthemsize" value="Thêm">
                </td>
            </tr>
            

            <tr>
                <td class="tieude">Giá (VNĐ):</td>
                <td class="noidung">
                    <input type="text" name="gia"
                        value="<?php echo isset($donGia) ? $donGia : '' ?>">
                </td>
            </tr>

            <tr>
                <td class="tieude">Mô tả:</td>
                <td class="noidung">
                    <textarea name="mota" rows="4" cols="50"><?php 
                        echo isset($moTa) ? $moTa : '';
                    ?></textarea>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="tdbtn">
                    <!-- Đổi class từ btnEdit sang btnAdd để dễ phân biệt -->
                    <input type="submit" class="btnAdd" name="btnaddnew" value="Thêm Mới">
                    <input type="submit" class="btnBack" name="btnback" value="Quay Lại">
                </td>
            </tr>

        </table>
    </form>

    <!-- ////////////////////////////////////////////////////////////////////////////////////////// -->
    <script>
        // Ẩn thông báo sau 3 giây
        setTimeout(function() {
            var msg = document.getElementById('message');
            if(msg) {
                msg.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>