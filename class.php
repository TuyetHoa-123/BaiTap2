<?php

class User
{
    private $con;

    public function connect()
    {
        $con = mysqli_connect("localhost", "root", "");

        if (!$con) {
            die('Không kết nối được csdl: ' . mysqli_connect_error());
            exit();
        }else{
            mysqli_select_db($con, "db_user");
            mysqli_query($con, "SET NAMES UTF8");
            return $con;
        }

       
    }

    public function layDuLieu($sql)
    {
        $link = $this->connect();
        $ketqua = mysqli_query($link, $sql);
        $data = [];

        if ($ketqua) {
            while ($row = mysqli_fetch_assoc($ketqua)) {
                $data[] = $row;
            }
        } else {
            // Log error instead of echoing
            error_log('Lỗi truy vấn: ' . mysqli_error($link));
        }mysqli_close($link);

        return $data;
    }

    public function xuatTableUser($sql)
    {
        $link = $this->connect();
        $ketqua = mysqli_query($link, $sql);

        if ($ketqua) {
            while ($row = mysqli_fetch_array($ketqua)) {
                // Output HTML here
                // ...
            }
        } else {
            // Log error instead of echoing
            error_log('Lỗi truy vấn: ' . mysqli_error($link));
        }
    }

    public function InsertUpdate($sql)
    {
        $link = $this->connect();
        if (mysqli_query($link, $sql)) {
            return 1;
        } else {
            return 0;
        }
    }
/* mã tự động tăng*/ 
    public function incrementUserID()
    {
        $link = $this->connect();
        // Select the maximum value from the UserId column
        $sql = "SELECT MAX(UserId) as maxNumber FROM m_user";

        $result = mysqli_query($link, $sql);

        if (!$result) {
            die('Lỗi truy vấn: ' . mysqli_error($link));
        } else {
            $row = mysqli_fetch_assoc($result);
            $currentNumber = $row['maxNumber'];

            // Increment the current number
            $nextNumber = $currentNumber + 1;

            return $nextNumber;
        }
    }
    public function incrementUserPerId()
    {
        $link = $this->connect();
        // Select the maximum value from the UserId column
        $sql = "SELECT MAX(UserPerId) as maxNumber FROM m_user_period";

        $result = mysqli_query($link, $sql);

        if (!$result) {
            die('Lỗi truy vấn: ' . mysqli_error($link));
        } else {
            $row = mysqli_fetch_assoc($result);
            $currentNumber = $row['maxNumber'];

            // Increment the current number
            $nextNumber = $currentNumber + 1;

            return $nextNumber;
        }
    }
    public function reduceUserPerId()
    {
        $link = $this->connect();
        // Select the maximum value from the UserId column
        $sql = "SELECT MAX(UserPerId) as maxNumber FROM m_user_period";

        $result = mysqli_query($link, $sql);

        if (!$result) {
            die('Lỗi truy vấn: ' . mysqli_error($link));
        } else {
            $row = mysqli_fetch_assoc($result);
            $currentNumber = $row['maxNumber'];

            // Increment the current number
            $nextNumber = $currentNumber ;

            return $nextNumber;
        }
    }
    /*Xoa*/
    public function deleteKyHan($delete) {
        if (isset($_REQUEST['delete'])) {
            $delete = $_REQUEST['delete'];
            $link = $this->connect();
            $sql = "DELETE FROM m_user_period WHERE UserPerId = $delete";
    
            $ketqua = mysqli_query($link, $sql);
    
            if ($ketqua) {    
                echo "<script>alert('Xóa thành công');</script>";
                header('Content-Type: text/html; charset=utf-8');
                echo header("refresh:0;url='addUser.php'");
                exit();
            } else {
                echo 'Xóa thất bại.';
            }
    
            mysqli_close($link);
        }
    }
    public function deleteUser($delete) {
        if (isset($_REQUEST['delete'])) {
            $delete = $_REQUEST['delete'];
            $link = $this->connect();
            $sql = "UPDATE m_user
            SET Deleted = 1
             WHERE UserId = $delete";
    
            $ketqua = mysqli_query($link, $sql);
    
            if ($ketqua) {    
                echo "<script>alert('Xóa thành công');</script>";
                header('Content-Type: text/html; charset=utf-8');
                echo header("refresh:0;url='index.php'");
                exit();
            } else {
                echo 'Xóa thất bại.';
            }
    
            mysqli_close($link);
        }
    }
    public function UserPerId($sql) {
        $link = $this->connect();
        $ketqua = mysqli_query($link, $sql);
    
        if (!$ketqua) {
            die('Lỗi truy vấn: ' . mysqli_error($link));
        }
    
        $userPerIds = []; // Initialize an array to store UserPerIds
    
        while ($row = mysqli_fetch_assoc($ketqua)) {
            $userPerIds[] = $row['UserPerId'];
        }
    
        mysqli_free_result($ketqua);
        mysqli_close($link);
    
        return $userPerIds;
    }
    
    
    public function copyUser($copiedUserId)
    {
        $copiedUserData = [];

        if (isset($_REQUEST['coppy'])) {
            $copiedUserId = $_REQUEST['coppy'];
            $link = $this->connect();
            $sql = "SELECT * FROM m_user WHERE UserId = $copiedUserId";

            $ketqua = mysqli_query($link, $sql);

            if (!$ketqua) {
                die('Lỗi truy vấn: ' . mysqli_error($link));
            } else {
                $row = mysqli_fetch_assoc($ketqua);
                $copiedUserData['LoginId'] = $row['LoginId'];
                $copiedUserData['UserName'] = $row['UserName'];
                $copiedUserData['Mail'] = $row['Mail'];
                $copiedUserData['Male'] = $row['Male'];
                $copiedUserData['UserRole'] = $row['UserRole'];
                $copiedUserData['BirthDate'] = $row['BirthDate'];
                $copiedUserData['Address'] = $row['Address'];
            }

            mysqli_close($link);
        }

        return $copiedUserData;
    }
    // public function editUser($editUserId)
    // {
    //     $editUserData = [];
    
    //     if (isset($_REQUEST['edit'])) {
    //         $editUserId = $_REQUEST['edit'];
    //         $link = $this->connect();
    //         $sql = "SELECT * FROM m_user
    //                 LEFT JOIN m_user_period ON m_user.UserId = m_user_period.UserId
    //                 WHERE m_user.UserId = $editUserId";
    
    //         $ketqua = mysqli_query($link, $sql);
    
    //         if (!$ketqua) {
    //             die('Lỗi truy vấn: ' . mysqli_error($link));
    //         } else {
    //             $editUserData['m_user'] = [];
    //             $editUserData['m_user_period'] = [];
    
    //             while ($row = mysqli_fetch_assoc($ketqua)) {
    //                 if (!isset($editUserData['UserId'])) {
    //                     $editUserData['UserId'] = $row['UserId'];
    //                     $editUserData['LoginId'] = $row['LoginId'];
    //                     $editUserData['UserName'] = $row['UserName'];
    //                     $editUserData['Mail'] = $row['Mail'];
    //                     $editUserData['Male'] = $row['Male'];
    //                     $editUserData['UserRole'] = $row['UserRole'];
    //                     $editUserData['BirthDate'] = $row['BirthDate'];
    //                     $editUserData['Address'] = $row['Address'];
    //                 }
    
    //                 $editUserData['m_user_period'][] = [
    //                     'UserPerId' => $row['UserPerId'],
    //                     'StartDt' => $row['StartDt'],
    //                     'EndDt' => $row['EndDt']
    //                     // Thêm các trường khác từ m_user_period nếu cần
    //                 ];
    //             }
    //         }
    
    //         mysqli_close($link);
    //     }
    
    //     return $editUserData;
    // }
    
    public function xuatKyHan($sql){
        $link = $this->connect();
        $ketqua = mysqli_query($link, $sql);
        if ($ketqua) {
            while ($row = mysqli_fetch_array($ketqua)) {
                $StartDt=$row['StartDt'];
                $EndDt=$row['EndDt'];
                echo'
                <tr>
                    <td>'.$StartDt.'&nbsp ~</td> 
                    <td>'.$EndDt.'</td>
                    <td><a href="addUser.php?delete='.$row['UserPerId'].'
                    " onclick="return confirm(\'Bạn có chắc xóa!\')"><i class="fa-solid fa-trash-can" style="color: red;"></i></a></td>
                </tr>
                ';
            }
        } else {
            // Log error instead of echoing
            error_log('Lỗi truy vấn: ' . mysqli_error($link));
        }
    }
    public function checkOverlapPeriod($userId, $newStartDt, $newEndDt) {
    $query = "SELECT * FROM m_user_period WHERE UserId = '$userId' 
              AND ((StartDt <= '$newStartDt' AND EndDt >= '$newStartDt') 
              OR (StartDt <= '$newEndDt' AND EndDt >= '$newEndDt')
              OR (StartDt >= '$newStartDt' AND EndDt <= '$newEndDt')
              OR ('$newStartDt' BETWEEN StartDt AND EndDt)
              OR ('$newEndDt' BETWEEN StartDt AND EndDt))";

    $result = $this->query($query);

    return $result->num_rows > 0;
}
public function deleteDurationFromDatabase($userId, $startDt, $endDt) {
    $sql = "DELETE FROM m_user_period WHERE UserId = $userId AND StartDt = '$startDt' AND EndDt = '$endDt'";
    $result = mysqli_query($this->connect(), $sql);

    if ($result) {
        // Deletion successful
        echo '<script language="javascript">alert("Đã xóa thành công khỏi cơ sở dữ liệu");</script>';
    } else {
        // Deletion failed
        echo '<script language="javascript">alert("Xóa khỏi cơ sở dữ liệu thất bại");</script>';
    }
}
public function xuatLoKHSXNVL($sql)
	{
		$link = $this->connect();
		$ketqua = mysql_query($sql, $link);

		if ($ketqua) 
		{
			while ($row=mysql_fetch_array($ketqua))
			{
			// Lấy thông tin từ dữ liệu
			$tenNguyenVatLieu = $row['tenNguyenVatLieu'];
			$donViTinh = $row['donViTinh'];
			$soLuong = $row['soLuongTonnvl'];
			// In ra thông tin trong mẫu HTML
			echo '
            <tr>
                        <td> '.$tenNguyenVatLieu.'</td>
                        <td> '.$soLuong.'</td>
                        <td>'.$donViTinh.'</td>
                    </tr>';
			}
		} 
			else {
				echo 'Không có dữ liệu';
			}

		mysql_close($link);
	}
}