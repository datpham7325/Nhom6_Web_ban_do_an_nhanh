<?php
include_once "includes/header.php";
include_once "function/functions.php";

include_once("includes/myenv.php");
$conn = mysqli_connect($hostname, $username, $password, $database);


if(isset($_GET['mabienthe'])) {
    $maBienThe = $_GET['mabienthe'];
    $strSQL = "SELECT * FROM monan, bienthemonan WHERE monan.MaMonAn = bienthemonan.MaMonAN AND bienthemonan.MaBienThe = '$maBienThe'";
    $result = mysqli_query($conn, $strSQL);
}
?>

<link rel="stylesheet" href="css/gagionvuive_ChiTiet.css">

<form>
    <table>
        <input type="hidden" name="mabienthe" value="<?php echo $maBienThe; ?>"> 
        <?php
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td colspan='2' class='tieude'><p class='tenmon'>". $row['TenMonAn'] ."</p></td>";
                echo "</tr>";
                echo "<tr>";
                $anh = "img/". $row['HinhAnh'];
                echo "<td class='ndtrai' rowspan='2'>";
                echo "<img src='$anh'>";
                echo "</td>";
                echo "<td class='ndphai'>";
                echo "<p><b>Mã món ăn: </b>". ChuanHoaMa($row['MaBienThe']) ."</p>";
                echo "<p><b>Kích thước: </b>". SizeMonAn($row['MaSize']) ."</p>";
                echo "<p><b>Mô tả: </b><br>". $row['MoTa'] ."</p>";
                echo "<p><b>Giá bán: </b><span class='gia'>". number_format($row['DonGia'], 0, ",", ".") ." VND</span></p>";
                echo "</td>";
                echo "</tr>";
                echo "<tr>";
                echo "<td class='nut-bam'>";
                echo "<input class='btnedit' type='submit' name='btnsua' value='Chỉnh sửa'>";
                echo "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>
</form>

<?php include_once "includes/footer.php"; ?>