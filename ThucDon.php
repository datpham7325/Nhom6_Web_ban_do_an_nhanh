<?php
include_once "includes/header.php";
include_once "function/functions.php";

include_once("includes/myenv.php");
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_db,$db_port);

// Xử lý truy vấn
if(isset($_GET['maloaimonan'])) {
    $maLoai = $_GET['maloaimonan'];
    if($maLoai == 0) {
        $strSQL = "SELECT * FROM monan, bienthemonan WHERE monan.MaMonAn = bienthemonan.MaMonAn";
    } else {
        $strSQL = "SELECT * FROM monan, bienthemonan WHERE monan.MaMonAn = bienthemonan.MaMonAn AND monan.MaLoai = '$maLoai'";
    }
} else {
    $maLoai = 0;
    $strSQL = "SELECT * FROM monan, bienthemonan WHERE monan.MaMonAn = bienthemonan.MaMonAn";
}

$result = mysqli_query($conn, $strSQL);
?>

<div class="container">
    <div class="page-header">
        <h1>THỰC ĐƠN JOLLIBEE</h1>
        <p>Đa dạng món ngon - Hương vị khó quên!</p>
    </div>

    <div class="content-container">
        <table class="bang-mon">
            <?php
            if(mysqli_num_rows($result) > 0) {
                echo "<tr>";
                $count = 0;
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<td>";
                    $anh = "img/". $row['HinhAnh'];
                    $mbt = $row['MaBienThe'];
                    echo "<a href='ChiTiet.php?mabienthe=$mbt'><img src='$anh'></a><br>";
                    
                    if($row['MaLoai'] == 6) {
                        $tenSize = SizeMonAn($row['MaSize']);
                        echo "<p class='tenmon'>". $row['TenMonAn']. " " . $tenSize ."</p>";
                    } else {
                        echo "<p class='tenmon'>". $row['TenMonAn'] ."</p>";
                    }
                    
                    echo "<p class='gia'>". number_format($row['DonGia'], 0, ",", ".") ." VND</p>";
                    echo "<a href='Sua.php?mabienthe=$mbt' class='btn-edit'>Chỉnh sửa</a>";
                    echo "</td>";

                    $count++;
                    if($count % 3 == 0) echo "</tr><tr>";
                }
                echo "</tr>";
            } else {
                echo "<tr><td colspan='3' style='text-align:center; padding:3rem;'><p>Không có món ăn nào trong danh mục này.</p></td></tr>";
            }
            ?>
        </table>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>