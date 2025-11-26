<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/detail.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once "includes/header2.php"; ?>

    <?php
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $dbname = "quanly_cua_hang";

        $conn = mysqli_connect($hostname, $username, $password, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        
        if( isset($_GET['mabienthe']) )
        {
            $maBienThe = $_GET['mabienthe'];
            $strSQL = "SELECT * FROM loaimonan, kichthuoc, monan, bienthemonan 
                    WHERE loaimonan.MaLoai = monan.MaLoai 
                    AND kichthuoc.MaSize = bienthemonan.MaSize
                    AND monan.MaMonAn = bienthemonan.MaMonAn
                    AND bienthemonan.MaBienThe = '$maBienThe'";
            $result = mysqli_query($conn, $strSQL);

            // Lấy dữ liệu
            while( $row = mysqli_fetch_array($result) )
            {
                $maMonAn = $row['MaMonAn'];
                $maBienThe = $row['MaBienThe'];
                $tenMonAn = $row['TenMonAn'];
                $hinhAnh = $row['HinhAnh'];
                $tenLoai = $row['TenLoai'];
                $tenSize = $row['TenSize'];
                $maSize = $row['MaSize'];
                $donGia = $row['DonGia'];
                $moTa = $row['MoTa'];
            }
        }
    ?>


<?php 
        // SỬA: Đổi từ $_GET sang $_POST và thêm logic upload ảnh
        if( isset($_POST['btnedit']) )
        {
            $maBienThe = $_POST['mabienthe'];
            $maLoai = $_POST['loaimonan'];
            $tenMonAn = $_POST['tenmonan'];
            
            // SỬA: Logic xử lý ảnh tương tự insert.php
            // Lấy ảnh cũ mặc định
            $hinhAnh = $_POST['hinhanh_cu'];
            
            // Kiểm tra nếu có upload ảnh mới
            if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] == 0) 
            {
                $target_dir = "img/";
                $target_file = $target_dir . basename($_FILES["hinhanh"]["name"]);
                if (move_uploaded_file($_FILES["hinhanh"]["tmp_name"], $target_file)) {
                    $hinhAnh = basename($_FILES["hinhanh"]["name"]);
                }
            }

            $tenLoai = $_POST['loaimonan']; // Lưu ý: biến này có thể cần xử lý lại nếu muốn lấy tên hiển thị, nhưng giữ nguyên theo code gốc
            $maSize = $_POST['size'];
            $donGia = $_POST['gia'];
            $moTa = $_POST['mota'];

            $strSQL_Update_MonAn = "UPDATE monan 
                                SET TenMonAn='$tenMonAn',
                                    HinhAnh='$hinhAnh',
                                    MoTa='$moTa',
                                    MaLoai='$maLoai'
                                WHERE MaMonAn='$maMonAn'";
            
            $strSQL_Update_BienTheMonAn = "UPDATE bienthemonan 
                                SET DonGia='$donGia',
                                MaSize='$maSize'
                                WHERE MaBienThe='$maBienThe'";
            
            $result_Update1 = mysqli_query($conn, $strSQL_Update_MonAn);
            $result_Update2 = mysqli_query($conn, $strSQL_Update_BienTheMonAn);

            if($result_Update1 && $result_Update2)
            {
                // Lấy lại dữ liệu sau khi update
                $strSQL = "SELECT * FROM loaimonan, kichthuoc, monan, bienthemonan 
                    WHERE loaimonan.MaLoai = monan.MaLoai 
                    AND kichthuoc.MaSize = bienthemonan.MaSize
                    AND monan.MaMonAn = bienthemonan.MaMonAn
                    AND bienthemonan.MaBienThe = '$maBienThe'";
                $result = mysqli_query($conn, $strSQL);
                $row = mysqli_fetch_assoc($result);

                $tenSize = $row['TenSize']; // cập nhật size mới
                $maSize = $row['MaSize'];
                $tenMonAn = $row['TenMonAn'];
                $hinhAnh = $row['HinhAnh'];
                $donGia = $row['DonGia'];
                $moTa = $row['MoTa'];
                $tenLoai = $row['TenLoai'];

                echo "<p id='message' class='success-message'>Chỉnh sửa thành công!</p>";
            }

            else
            {
                echo "<p id='message' class='error-message'>Chỉnh sửa thất bại. Vui lòng thử lại.</p>";
            }
        }
        // SỬA: Đổi sang $_REQUEST hoặc $_POST vì form đã chuyển sang POST
        else if( isset($_REQUEST['btndelete']) )
        {
            $maBienThe = $_REQUEST['mabienthe'];
            header("Location: delete.php?mabienthe=$maBienThe");
        }
        else if( isset($_REQUEST['btnback']) )
        {
            $page = (int)$_REQUEST['page'];
            header("Location: home.php?page=$page");
        }
    ?>



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
        if( isset($_REQUEST['tensize']) ) // Dùng REQUEST để bắt cả GET/POST
        {
            if(isset($_REQUEST['tensize']) && ($_REQUEST['tensize']) != "")
            {
                $tenSize = $_REQUEST['tensize'];

                $strSQL_tenSize = "INSERT INTO kichthuoc(TenSize) VALUES('$tenSize')";
                $result_Insert_tenSize = mysqli_query($conn, $strSQL_tenSize);

                if( $result_Insert_tenSize )
                {
                    header("Location: detail.php?mabienthe=$maBienThe");
                    exit();
                }
            }
        }

        // Nút xóa size
        if(isset($_REQUEST['btnDeleteSize'])) // Dùng REQUEST
        {
            $sizeToDelete = $_REQUEST['size'];

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
                    header("Location: detail.php?mabienthe=$maBienThe"); // reload trang
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
        if( isset($_REQUEST['tenloai']) ) // Dùng REQUEST
        {
            if($_REQUEST['tenloai'] != "")
            {
                $tenLoaiMoi = $_REQUEST['tenloai'];

                $sqlThemLoai = "INSERT INTO loaimonan(TenLoai) VALUES('$tenLoaiMoi')";
                $resLoai = mysqli_query($conn, $sqlThemLoai);

                if($resLoai)
                {
                    header("Location: detail.php?mabienthe=$maBienThe");
                    exit();
                }
            }
        }

        // Nút xóa loại món ăn
        if(isset($_REQUEST['btnDeleteLoai'])) // Dùng REQUEST
        {
            $loaiToDelete = $_REQUEST['loaimonan'];

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
                    header("Location: detail.php?mabienthe=$maBienThe");
                    exit();
                }
                else 
                {
                    echo "Xóa thất bại: " . mysqli_error($conn);
                }
            }
        }


    ?>

<form class="form1">
        <table>
            <tr>
                <td colspan="4">
                    <h2>THÔNG TIN CHI TIẾT</h2>
                </td>
            </tr>
            <tr class="tieude">
                <td>Mã món ăn</td>
                <td>Tên</td>
                <td>Hình ảnh</td>
                <td>Loại món ăn</td>
                <td>Size</td>
                <td>Giá (VNĐ)</td>
                <td>Mô tả</td>
            </tr>

            <?php 
                echo "<tr class='noidung'>";
                    echo "<td>" . ChuanHoaMa($maBienThe) . "</td>";
                    echo "<td>" . $tenMonAn . "</td>";
                            
                    $anh = "img/". $hinhAnh;
                    echo "<td>" . "<img src='$anh' width='50px' height='50px'>" . "</td>";
                            
                    echo "<td>" . $tenLoai . "</td>";
                    echo "<td>" . $tenSize . "</td>";
                    echo "<td>" . number_format($donGia, 0, ',', '.') . " VND" . "</td>";
                    echo "<td>" . $moTa . "</td>";
                echo "</tr>";
            ?>
        </table>
    </form>



<form class="form2" method="post" enctype="multipart/form-data">
        <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? (int)$_GET['page'] : 1; ?>">
        <table>

            <tr>
                <td class="tieude">Mã món ăn:</td>
                <td class="noidung">
                    <input type="text" name="mabienthe"
                        value="<?php echo isset($maBienThe) ? ChuanHoaMa($maBienThe) : '' ?>" readonly>
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
                    <input type="file" name="hinhanh" accept="image/*">
                    <input type="hidden" name="hinhanh_cu" value="<?php echo isset($hinhAnh) ? $hinhAnh : '' ?>">
                    <br>
                    <span style="font-size: 0.8em;">(Hiện tại: <?php echo isset($hinhAnh) ? $hinhAnh : '' ?>)</span>
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
                    <input type="submit" class="btnEdit" name="btnedit" value="Chỉnh Sửa">
                    <input type="submit" class="btnDelete" name="btndelete" value="Xóa">
                    <input type="submit" class="btnBack" name="btnback" value="Quay Lại">
                </td>
            </tr>

        </table>
    </form>


    
</body>

<script>
    // Ẩn thông báo sau 3 giây
    setTimeout(function() {
        var msg = document.getElementById('message');
        if(msg) {
            msg.style.display = 'none';
        }
    }, 3000);
</script>
</html>