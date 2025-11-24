<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/delete.css">

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


        //Nút chi tiết
        if( isset($_GET['btnchitiet']) )
        {
            $maBienThe = $_GET['mabienthe'];
            header("Location: detail.php?mabienthe=$maBienThe");
            exit(); 
        }

        //Nút Xóa
        if( isset($_GET['btnxoa']) )
        {
            $maBienThe = $_GET['mabienthe'];
            $strSQL = "DELETE FROM bienthemonan WHERE bienthemonan.MaBienThe = '$maBienThe'";
            $result = mysqli_query($conn, $strSQL);

            if($result)
            {
                header("Location: home.php?deleted=1");
                exit(); 
            }
            else
            {
                header("Location: home.php?deleted=0");
                exit(); 
            }
        }

        //Nút Trở về
        if( isset($_GET['btnquayve']) )
        {
            header("Location: home.php");
            exit(); 
        }


    ?>



    <form >
        <h2>BẠN CÓ XÁC NHẬN MUỐN XÓA MÓN ĂN NÀY !!</h2>
        <table>
            <!-- Lưu trữ mã biến biến thể -->
            <input type="hidden" name="mabienthe" value="<?php echo $maBienThe; ?>"> 
            
            <?php
                if( mysqli_num_rows($result) > 0 )
                {
                    while( $row = mysqli_fetch_array($result) )
                    {
                        echo "<tr>";
                            echo "<td colspan='4'class='tieude'>". "<p class='tenmon'>". $row['TenMonAn'] ."</p>" ."</td>";
                        echo "</tr>";
                        echo "<tr>";
                            $anh = "../img/". $row['HinhAnh'];
                            echo "<td class='ndtrai' rowspan='2'>";
                                echo "<img src='$anh'>";
                            echo "</td>";
                            echo "<td class='ndphai' colspan='3'>";
                                echo "<p>";
                                    echo "<b>Mã món ăn: </b>". ChuanHoaMa($row['MaBienThe']);
                                echo "</p>";
                                echo "<p>";
                                    echo "<b>Kích thước: </b>". SizeMonAn($row['MaSize']);
                                echo "</p>";
                                echo "<p>";
                                    echo "<b>Mô tả: </b><br>". $row['MoTa'];
                                echo "</p>";
                                echo "<p>";
                                    echo "<b>Giá bán: </b>". "<span class='gia'>". number_format($row['DonGia'], 0, ",", ".") ." VND" ."</span>";
                                echo "</p>" ;
                            echo "</td>";
                            
                        echo "</tr>";
                        echo "<tr>";
                            echo "<td class='nut-bam'>";
                                echo "<input class='btndetail' type='submit' name='btnchitiet' value='Chi tiết'>";
                            echo "</td>";
                            echo "<td class='nut-bam'>";
                                echo "<input class='btndelete' type='submit' name='btnxoa' value='Xác nhận'>";
                            echo "</td>";
                            echo "<td class='nut-bam'>";
                                echo "<input type='submit' class='btnback' name='btnquayve' value='Quay về'>";
                            echo "</td>";
                        echo "</tr>";
                    }
                   
                }
            ?>
    
    
        </table>
    </form>
</body>
</html>