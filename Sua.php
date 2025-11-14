<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/gagionvuive_Sua.css">

    <!-- Thêm font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <?php
        include '../function/functions.php';
        $hostname = "localhost";
        $username = "root";
        $password = "";
        $database = "quanly_cua_hang";

        $conn = mysqli_connect($hostname, $username, $password, $database);
        
        if( isset( $_GET['mabienthe']) )
        {
            $maBienThe = $_GET['mabienthe'];
            $strSQL = "SELECT * FROM monan, bienthemonan WHERE monan.MaMonAn = bienthemonan.MaMonAN AND bienthemonan.MaBienThe = '$maBienThe'";
            $result = mysqli_query($conn, $strSQL);
        }

        
        //Lấy dữ liệu về lưu
        if( mysqli_num_rows($result) > 0 )
        {
            if( $row = mysqli_fetch_array($result) )
            {
                $maMonAn = ChuanHoaMa( $row['MaMonAn'] );
                $tenMonAn = $row['TenMonAn'];
                $maSize = $row['MaSize'];
                $moTa = $row['MoTa'];
                $donGia = $row['DonGia'];
                $hinhAnh = "../img/". $row['HinhAnh'];
            }
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
                $tenSize = mb_strtoupper( $tenSize, 'UTF-8' );

                $strSQL_tenSize = "INSERT INTO kichthuoc(TenSize) VALUES('$tenSize')";
                $result_Insert_tenSize = mysqli_query($conn, $strSQL_tenSize);

                if( $result_Insert_tenSize )
                {
                    header("Location: Sua.php?mabienthe=$maBienThe");
                    exit();
                }
            }
        }

        //Xử lý nút hủy thay đổi
        if( isset($_GET['btnhuy']) )
        {
            header("Location: ChiTiet.php?mabienthe=$maBienThe");
            exit(); 
        }

        //Xử lý nút xác nhận
        if( isset($_GET['btnsua']) )
        {
            $maBienThe = $_GET['mabienthe'];
            $tenMonAn = $_GET['tenmonan'];
            $tenMonAn = mb_strtoupper( $tenMonAn, 'UTF-8' );
            $maSize = $_GET['size'];
            $moTa = $_GET['mota'];
            $donGia = $_GET['dongia'];
            $donGia = (int)$donGia;

            //Update tên món và mô tả
            $strSQL_Update_monAn = "UPDATE monan
                                    SET TenMonAn = '$tenMonAn',
                                        MoTa = '$moTa'
                                    WHERE MaMonAn = '$maMonAn'";
            $result_Update_monAn = mysqli_query($conn, $strSQL_Update_monAn);
            
            //Update mã size và đơn giá
            $strSQL_Update_bienTheMonAn = "UPDATE bienthemonan
                                           SET MaSize =  '$maSize',
                                                DonGia = '$donGia'
                                            WHERE MaBienThe = '$maBienThe'";
            $result_Update_bienTheMonAn = mysqli_query($conn, $strSQL_Update_bienTheMonAn);


            if( $result_Update_bienTheMonAn && $result_Update_monAn )
                $noti_success = "Chỉnh sửa thành công";
            else
                $noti_failed = "Chỉnh sửa không thành công";
        }
    ?>

    <nav>
        <div class="menu">
            <div class="menu-item">
                <a href="./Home.php?maloaimonan=1"><img class="menu_img" src="../img/gagionvuive1.jpg" alt="Gà giòn vui vẻ"></a>
                <p class="ggvv">GÀ GIÒN VUI VẺ</p>
            </div>
            <div class="menu-item">
                <a href="./Home.php?maloaimonan=2"><img class="menu_img" src="../img/miy1.jpg" alt="Mì Ý Jolly"></a>
                <p class="my">MÌ Ý JOLLY</p>
            </div>
            <div class="menu-item">
                <a href="./Home.php?maloaimonan=3"><img class="menu_img" src="../img/gasot1.jpg" alt="Gà sốt cay"></a>
                <p class="gs">GÀ SỐT</p>
            </div>
            <div class="menu-item">
                <a href="./Home.php?maloaimonan=4"><img class="menu_img" src="../img/burger1.jpg" alt="Burger/Cơm"></a>
                <p class="bg">BURGER/CƠM</p>
            </div>
            <div class="menu-item">
                <a href="./Home.php?maloaimonan=5"><img class="menu_img" src="../img/trangmieng1.jpg" alt="Tráng miệng"></a>
                <p class="tm">TRÁNG MIỆNG</p>
            </div>
            <div class="menu-item">
                <a href="./Home.php?maloaimonan=6"><img class="menu_img" src="../img/nuoc1.jpg" alt="Nước"></a>
                <p class="nuoc">NƯỚC</p>
            </div>
        </div>
    </nav>
    
    <?php
        echo "<h2 class='noti_success'>" . (isset($noti_success) ? $noti_success : '') . "</h2>";
        echo "<h2 class='noti_failed'>" . (isset($noti_failed) ? $noti_failed : '') . "</h2>";
    ?>


    <form >
        <table>
            <!-- Lưu trữ mã biến biến thể -->
            <input type="hidden" name="mabienthe" value="<?php echo $maBienThe; ?>"> 
            <tr>
                <td colspan="3">
                    <p class="tieude">
                        <?php
                        echo isset($tenMonAn) ? $tenMonAn : "Lỗi!";
                        ?>
                    </p>
                </td>
            </tr>
            <tr>
                <td class="ndtrai" rowspan="2">
                    <img src="<?php echo $hinhAnh; ?>">
                </td>
                <td class="ndphai" colspan="2">
                    <div class="ndphai-con">
                        <b>Mã món ăn: </b>&nbsp;
                         <span><?php echo isset($maMonAn) ? $maMonAn : "" ?></span> 
                    </div>

                    <div class="ndphai-con">
                        <b>Tên món ăn: </b> 
                        <input type="text" name="tenmonan" value="<?php echo isset($tenMonAn) ? $tenMonAn : "" ?>">
                    </div>

                    <div class="ndphai-con">
                        <b>Kích thước: </b>&nbsp;
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
                    </div>

                    <div class="ndphai-con">
                        <input type="text" name="tensize" placeholder="Nhập size mới">
                        <input type="submit" name="btnthemsize" value="Thêm">
                    </div>

                    <div class="ndphai-con">
                        <b>Mô tả: </b><br>
                        <textarea name="mota"><?php echo isset($moTa) ? $moTa : "" ?></textarea>
                    </div>
                    <div class="ndphai-con">
                        <b>Giá: </b> &nbsp;
                        <input type="text" name="dongia" value="<?php echo isset($donGia) ? $donGia : "" ?>">
                    </div>
                </td>
                
            </tr>
            <tr>
                <td class="btn">
                    <input type="submit" name="btnhuy" value="Hủy thay đổi">
                </td>
                <td class="btn">
                    <input type="submit" name="btnsua" value="Xác nhận">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>