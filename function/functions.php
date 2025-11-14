<?php
    // //Hàm xử lý mã món ăn. Vd: Biến đổi 1 => GR001
    // function ChuanHoaMa_GR($mma)
    // {
    //     $kq = str_pad($mma, 3, '0', STR_PAD_LEFT);
    //     $kq = "GR" . $kq; 
    //     return $kq;
    // }   
    
    // //Hàm xử lý mã món ăn. Vd: Biến đổi 1 => GR001
    // function ChuanHoaMa_MY($mma)
    // {
    //     $kq = str_pad($mma, 3, '0', STR_PAD_LEFT);
    //     $kq = "MY" . $kq; 
    //     return $kq;
    // }    

    //Chuẩn hóa mã theo định dạng 3 số
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


?>