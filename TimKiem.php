<?php include_once "includes/header.php"; ?>

<div class="container">
    <div class="page-header">
        <h1>TÌM KIẾM MÓN ĂN</h1>
        <p>Khám phá hương vị yêu thích của bạn!</p>
    </div>

    <div class="content-container">

        <?php
        // Xử lý tìm kiếm khi có keyword từ form
        if(isset($_GET['keyword'])) {
            // Lấy và làm sạch dữ liệu từ form
            $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
            $maloai = $_GET['maloai'] ?? 0;
            $min_price = $_GET['min_price'] ?? 0;
            $max_price = $_GET['max_price'] ?? 999999999;
            
            // Xây dựng câu truy vấn SQL tìm kiếm
            $sql = "SELECT m.*, b.MaBienThe, b.DonGia, b.MaSize 
                    FROM MonAn m 
                    JOIN BienTheMonAn b ON m.MaMonAn = b.MaMonAn 
                    WHERE m.TenMonAn LIKE '%$keyword%'";
            
            // Thêm điều kiện lọc theo loại món nếu có
            if($maloai > 0) {
                $sql .= " AND m.MaLoai = $maloai";
            }
            
            // Thêm điều kiện lọc theo khoảng giá
            $sql .= " AND b.DonGia BETWEEN $min_price AND $max_price";
            
            // Thực thi truy vấn
            $result = mysqli_query($conn, $sql);
            
            // Kiểm tra và hiển thị kết quả
            if(mysqli_num_rows($result) > 0) {
                echo '<div class="search-results">';
                echo '<h3>Kết quả tìm kiếm:</h3>';
                echo '<table class="bang-mon">';
                echo '<tr>';
                $count = 0;
                // Lặp qua từng kết quả tìm kiếm
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<td>";
                    $anh = "img/". $row['HinhAnh'];
                    $mbt = $row['MaBienThe'];
                    // Hiển thị hình ảnh món ăn với link đến trang chi tiết
                    echo "<a href='ChiTiet.php?mabienthe=$mbt'><img src='$anh'></a><br>";
                    echo "<p class='tenmon'>". $row['TenMonAn'] ."</p>";
                    // Định dạng và hiển thị giá tiền
                    echo "<p class='gia'>". number_format($row['DonGia'], 0, ",", ".") ." VND</p>";
                    
                    // Hiển thị nút thêm vào giỏ hàng nếu người dùng đã đăng nhập
                    if(isset($_SESSION['loggedin'])) {
                        echo "<button class='btn-add-to-cart' data-mabienthe='$mbt'>Thêm vào giỏ</button>";
                    }
                    
                    echo "</td>";
                    
                    $count++;
                    // Xuống dòng sau mỗi 3 món ăn
                    if($count % 3 == 0) echo "</tr><tr>";
                }
                echo "</tr>";
                echo "</table>";
                echo '</div>';
            } else {
                // Hiển thị thông báo khi không tìm thấy kết quả
                echo '<p class="no-results">Không tìm thấy món ăn phù hợp.</p>';
            }
        }
        ?>
    </div>
</div>

<script>
// Xử lý sự kiện click cho các nút "Thêm vào giỏ"
document.querySelectorAll('.btn-add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        const maBienThe = this.dataset.mabienthe;
        
        // Gửi yêu cầu AJAX để thêm vào giỏ hàng
        fetch('ajax/themgiohang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'mabienthe=' + maBienThe + '&soluong=1'
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert('Đã thêm vào giỏ hàng!');
                updateCartCount();
            } else {
                alert('Lỗi: ' + data.message);
            }
        });
    });
});

// Hàm cập nhật số lượng giỏ hàng trong header
function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if(cartCount) {
        cartCount.textContent = parseInt(cartCount.textContent) + 1;
    }
}
</script>

<?php include_once "includes/footer.php"; ?>