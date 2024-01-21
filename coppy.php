<?php
 include('class.php');
 $p = new user();
 if(isset($_REQUEST['delete'])){
    $delete = $_REQUEST['delete'];
    $p->deleteKyHan($delete);
 }

 if (isset($_REQUEST['coppy'])) {
    $editUserId = $_REQUEST['coppy'];
    $sql = "SELECT * FROM m_user WHERE UserId = $editUserId";
    $p->connect();
    $result = mysqli_query($p->connect(), $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);

        $loginId = $userData['LoginId'];
        $userName = $userData['UserName'];
        $mail = $userData['Mail'];
        $userRole = $userData['UserRole'];
        $gender = $userData['Male'];
        $birthdate = $userData['BirthDate'];
        $address = $userData['Address'];
        $birthDate = date('Y-m-d', strtotime($birthdate));

        $sqlDates = "SELECT UserPerId, StartDt, EndDt FROM m_user_period WHERE UserId = $editUserId";
        $resultDates = mysqli_query($p->connect(), $sqlDates);

        $datePairs = [];

        if ($resultDates && mysqli_num_rows($resultDates) > 0) {
            while ($datesData = mysqli_fetch_assoc($resultDates)) {
                $dateFirst = date('Y-m-d', strtotime($datesData['StartDt']));
                $dateAfter = date('Y-m-d', strtotime($datesData['EndDt']));
                $userPerId = $datesData['UserPerId'];
                $datePairs[] = [
                    'dateFirst' => $dateFirst,
                    'dateAfter' => $dateAfter,
                    'userPerId' => $userPerId,
                ];
            }
        }
    }
}
if(isset($_REQUEST['btnSubmit'])){
    $UserId=$p->incrementUserID();
    $loginId=$_REQUEST['LoginId'];
    $username=$_REQUEST['UserName'];
    $mail=$_REQUEST['Mail'];
    $userRole=$_REQUEST['UserRole'];
    $gender=$_REQUEST['gender'];
    $birthDate=$_REQUEST['BirthDate'];
    $address=$_REQUEST['Address'];
    $currentDate = date("Y/m/d");
    $editUserId = $_REQUEST['coppy'];
   
    $newDateFirst = $_REQUEST['inp-dateFirst'];
    $newDateAfter = $_REQUEST['inp-dateAfter'];
    $UserPerId=$p->incrementUserPerId();
    // Check if the new user period already exists
    $existingUserPeriodSQL = "SELECT COUNT(*) AS count FROM m_user_period WHERE UserId = $editUserId AND StartDt = '$newDateFirst' AND EndDt = '$newDateAfter'";
    $resultExistingUserPeriod = mysqli_query($p->connect(), $existingUserPeriodSQL);
    $existingUserPeriodData = mysqli_fetch_assoc($resultExistingUserPeriod);

    // If the user period doesn't exist, insert it into the database
    if ($existingUserPeriodData['count'] == 0) {
        $insertUserPeriodSQL = "INSERT INTO m_user_period (UserPerId,UserId, StartDt, EndDt) VALUES ('$UserPerId','$editUserId', '$newDateFirst', '$newDateAfter')";
        if (!$p->InsertUpdate($insertUserPeriodSQL)) {
            mysqli_rollback($p->connect());
            echo "Error inserting new data into m_user_period: " . mysqli_error($p->connect());
        }
    }
    $sqlkq=$p->InsertUpdate("INSERT INTO m_user (UserId, InsBy, InsDt, Deleted, LoginId, LoginPassword, UserName, Mail, UserRole, Male, BirthDate, Address) 
    VALUES ('$UserId', 'Admin', '$currentDate', 0,  '$loginId', '1111','$username', '$mail', '$userRole', '$gender', '$birthDate', '$address')");
    if ($sqlkq) {

//        $UserPerId=$p->incrementUserPerId();
//        $datesArray = json_decode($_POST['datesArray'], true);
//        if (is_array($datesArray)) {
//         foreach ($datesArray as $dates) {
//             $dateFirst = $dates['dateFirst'];
//             $dateAfter = $dates['dateAfter'];
//             // Thực hiện truy vấn thêm ngày và kiểm tra kết quả
//             if( $p->InsertUpdate("INSERT INTO m_user_period(UserPerId,InsBy,InsDt,StartDt,EndDt,UserId) 
//              VALUES('$UserPerId','Admin','$currentDate','$dateFirst','$dateAfter','$UserId')") ){
//                 echo '<script language="javascript">alert("Thêm Kỳ hạn thành công");</script>';
//             }else {
//                 echo '<script language="javascript">alert("Thêm thất bại khi thêm vào bảng m_user_period");</script>';
//             }
            
//         }

//     echo '<script language="javascript">alert("Thêm thành công");</script>';
// } else {
//     echo '<script language="javascript">alert("Thêm thất bại khi thêm vào bảng m_user");</script>';
// }
      
    

 }echo '<script language="javascript">alert("Thêm thành công");</script>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coppy</title>
    <link rel="stylesheet" href="./CSS/addUser.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" /> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>
<style>
    .duration-date {
        display: flex;
        align-items: center;
    }

    .duration-date i {
        margin-left: 10px; /* Adjust the margin as needed */
    }
    .dateFirst {
    width: 110px;
  }
  
  .dateAfter{
    width: 100px;
    margin-left:50px;
  }
  
</style>


<body>
    <div class="container">
        <div class="section">
            <h1> ユーザー登録</h1>
            <form action="#" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="common-section">
            <input type="number" id="UserId" name="UserId" value="<?php echo $p->incrementUserID(); ?> "hidden>

                </div>
                <div class="common-section">
                    <label for="LoginId">ログインID</label><br>
                    <input type="text" id="LoginId" name="LoginId" value="<?php echo isset($loginId) ? $loginId : ''; ?>" placeholder="ログインID" style="border-radius: 4px 4px 4px 4px;"><br>
                </div>
                <div class="common-section">
                    <label for="UserName">ユーザー名</label><br>
                    <input type="text" id="UserName" name="UserName" value="<?php echo isset($userName) ? $userName : ''; ?>" placeholder="ユーザー名"
                        style="border-radius: 4px 4px 4px 4px;"><br>
                </div>
                <div class="common-section">
                    <label for="Mail">メール</label><br>
                    <input type="text" id="Mail" name="Mail" value="<?php echo isset($mail) ? $mail : ''; ?>" placeholder="メール"
                        style="border-radius: 4px 4px 4px 4px;"><br>
                </div>
                <div class="common-section">
                    <label for="UserRole">自社ユーザーロール</label><br>
                    <input type="radio" name="UserRole"  value="9" <?php echo isset($userRole) && $userRole == 9 ? 'checked' : ''; ?>>管理者 
                    <input type="radio" name="UserRole" value="0" <?php echo isset($userRole) && $userRole == 0 ? 'checked' : ''; ?>>一 般
                </div>
                <div class="common-section">
                    <label for="gender">性別</label><br>
                    <input type="radio" name="gender"  value="0" <?php echo isset($gender) && $gender == 0 ? 'checked' : ''; ?>> 男
                    <input type="radio" name="gender"  value="1" <?php echo isset($gender) && $gender == 1 ? 'checked' : ''; ?>>女
                </div>

                <div class="common-section">
                    <label for="BirthDate">誕生日</label><br>
                    <div class="input-with-icon">
                        <input type="text" id="BirthDate" name="BirthDate" value="<?php echo isset($birthDate) ? $birthDate : ''; ?>" placeholder="YYYY/MM/DD" >
                        <i class="fa fa-calendar" id="datepicker-icon"></i>
                    </div>
                </div>
                <div class="common-section">
                    <label for="Address">住所</label><br>
                    <input type="text" id="Address" name="Address" value="<?php echo isset($address) ? $address : ''; ?>" placeholder="住所"
                        style="border-radius: 4px 4px 4px 4px;"><br>
                </div>
               
                
                <div class="common-section">
                    
                    <div class="period">
                    <label for="">使用期間</label>
                    <div class="duration">
                        <div class="duration-inpdate">
                        <div class="input-with-icon-count">
                                <input type="text" placeholder="YYYY/MM/DD" id="fromDate" name="fromDate"class="inp-dateFirst">
                                <i class="fa fa-calendar" id="fromDate-icon"></i> 
                            </div>
                            <div class="input-with-icon-count">
                                <input type="text" placeholder="YYYY/MM/DD" id="toDate" name="toDate"class="inp-dateAfter">
                                <i class="fa fa-calendar" id="toDate-icon"></i> 
                            </div>
                            <button id="dateButton" style="background-color: #d7e1f2; border: none;margin-bottom: 10px;width: 50px;">住所</button>
                            
                        </div>
                        <div class="all-durations">
                        <?php
                                if (isset($datePairs) && count($datePairs) > 0) {
                                    foreach ($datePairs as $datePair) {
                                        $dates = explode('~', $datePair['dateFirst'] . '~' . $datePair['dateAfter']);
                                        $userPerId = $datePair['userPerId'];
                                ?>
                                        <div class="duration-date">
                                            <div class="dateFirst"><?php echo $dates[0]; ?></div>
                                            <p>~</p>
                                            <div class="dateAfter"><?php echo $dates[1]; ?></div>
                                            <i class="fa fa-trash-o" data-action="delete" data-userperid="<?php echo $userPerId; ?>" style="color: red; cursor: pointer;"></i>
                                            
                                            <input type="hidden" name="deletedUserPerId" class="userPerIdInput" value="<?php echo $userPerId; ?>" readonly>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                        </div>
                    </div>
                    <!-- Ô input ẩn để lưu trữ các ngày đã chọn -->
                        <input type="hidden" name="datesArray" id="datesArray" value="">
                        
                    </div>
                </div>
                
                <div class="btn">
                    <a href="addUser.php" style=" border: 1px solid black;padding: 2px;color:black;text-decoration:none;">閉じる </a>&ensp;
                    <input type="submit" name="btnSubmit" value="保存"
                        style="background-color: #1d92af; color: #fff;">
                </div>

            </form>
        </div>
    </div>
</body>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            function deleteDuration(element) {
                var allDurations = document.querySelector('.all-durations');
                allDurations.removeChild(element);

                var datesArray = [];
                allDurations.querySelectorAll('.duration-date').forEach(function (duration) {
                    var dateFirst = duration.querySelector('.dateFirst').textContent;
                    var dateAfter = duration.querySelector('.dateAfter').textContent;
                    datesArray.push({
                        dateFirst: dateFirst,
                        dateAfter: dateAfter
                    });
                });

                document.getElementById('datesArray').value = JSON.stringify(datesArray);
            }

            document.querySelectorAll('.fa-trash-o').forEach(function (trashCan) {
                trashCan.addEventListener('click', function () {
                    deleteDuration(trashCan.parentElement);
                });
            });

            document.getElementById('dateButton').addEventListener('click', function (event) {
                event.preventDefault();
                var dateFirst = document.querySelector('.inp-dateFirst').value;
                var dateAfter = document.querySelector('.inp-dateAfter').value;

                var uniqueId = Date.now();

                var allDurations = document.querySelector('.all-durations');
                var newDuration = document.createElement('div');
                newDuration.classList.add('duration-date');
                newDuration.innerHTML = '<div class="dateFirst">' + dateFirst + '</div>' +
                    '<p>~</p>' +
                    '<div class="dateAfter">' + dateAfter + '</div>' +
                    '<i class="fa fa-trash-o" onclick="deleteDurationById(' + uniqueId + ')" style="color: red; cursor: pointer;"></i>';

                newDuration.setAttribute('data-unique-id', uniqueId);

                allDurations.appendChild(newDuration);
                allDurations.appendChild(document.createElement('br'));

                document.querySelector('.inp-dateFirst').value = '';
                document.querySelector('.inp-dateAfter').value = '';
            });

            window.deleteDurationById = function (uniqueId) {
                var elementToDelete = document.querySelector('.duration-date[data-unique-id="' + uniqueId + '"]');
                if (elementToDelete) {
                    deleteDuration(elementToDelete);
                }
            };
        });
        
    </script>

    <script>
        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('fa-trash-o')) {
                var durationDate = event.target.closest('.duration-date');
                var userPerIdToDelete = event.target.getAttribute('data-userperid');
                durationDate.remove();

                var dateFirst = durationDate.querySelector('.dateFirst').innerText;
                var dateAfter = durationDate.querySelector('.dateAfter').innerText;
                var datesArray = JSON.parse(document.getElementById('datesArray').value || '[]');

                for (var i = 0; i < datesArray.length; i++) {
                    if (datesArray[i].dateFirst === dateFirst && datesArray[i].dateAfter === dateAfter) {
                        datesArray.splice(i, 1);
                        break;
                    }
                }

                document.getElementById('datesArray').value = JSON.stringify(datesArray);

                var userPerIdInput = document.createElement('input');
                userPerIdInput.type = 'hidden';
                userPerIdInput.name = 'userPerIdToDelete[]';
                userPerIdInput.value = userPerIdToDelete;
                document.querySelector('form').appendChild(userPerIdInput);
                
            }
        });
        
    </script>
    <script>
    document.getElementById('datepicker-icon').addEventListener('click', function () {
        // Mở datepicker khi icon được click
        document.getElementById('BirthDate').flatpickr.open();
    });

    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#datepicker-icon", {
            dateFormat: "Y/m/d", // Định dạng ngày
            allowInput: true, // Cho phép nhập trực tiếp vào ô input
            onChange: function (selectedDates, dateStr, instance) {
                // Đổ ngày đã chọn vào trường nhập liệu birthday khi có thay đổi
                document.getElementById('BirthDate').value = dateStr;
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#fromDate-icon", {
        dateFormat: "Y/m/d", // Định dạng ngày
        allowInput: true, // Cho phép nhập trực tiếp vào ô input
        onChange: function (selectedDates, dateStr, instance) {
            // Đổ ngày đã chọn vào trường nhập liệu birthday khi có thay đổi
            document.getElementById('fromDate').value = dateStr;
        }
    });
});
// toDate
document.getElementById('toDate-icon').addEventListener('click', function () {
    // Mở datepicker khi icon được click
    document.getElementById('toDate').flatpickr.open();
});

document.addEventListener('DOMContentLoaded', function () {
    flatpickr("#toDate-icon", {
        dateFormat: "Y/m/d", // Định dạng ngày
        allowInput: true, // Cho phép nhập trực tiếp vào ô input
        onChange: function (selectedDates, dateStr, instance) {
            // Đổ ngày đã chọn vào trường nhập liệu birthday khi có thay đổi
            document.getElementById('toDate').value = dateStr;
        }
    });
});
</script>

</html>