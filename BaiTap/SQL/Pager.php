<?php
class Pager {
  public $limit;
  public $start;
  public $total;

  public function __construct($total, $limit = 2) {
    $this->total = $total;
    $this->limit = $limit;
    $this->start = isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0;
  }

  public function createLinks() {
    $pages = ceil($this->total / $this->limit);
    if ($pages <= 1) return ""; // Không cần pager nếu chỉ 1 trang

    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $links = "<div style='margin-top:10px;text-align:center'>";

    // Nếu không phải trang 1 → Hiện liên kết quay lại
    if ($currentPage > 1) {
        $links .= "<a href='?page=1' style='margin:0 5px;'>&laquo;</a>";
        $links .= "<a href='?page=".($currentPage - 1)."' style='margin:0 5px;'>&lt;</a>";
    }

    // Hiển thị số trang
    for ($i = 1; $i <= $pages; $i++) {
        $active = $i == $currentPage ? "font-weight:bold;color:red;" : "";
        $links .= "<a href='?page=$i' style='margin:0 5px;$active'>$i</a>";
    }

    // Nếu không phải trang cuối → Hiện liên kết tiến
    if ($currentPage < $pages) {
        $links .= "<a href='?page=".($currentPage + 1)."' style='margin:0 5px;'>&gt;</a>";
        $links .= "<a href='?page=$pages' style='margin:0 5px;'>&raquo;</a>";
    }

    $links .= "</div>";
    return $links;
}

}
?>