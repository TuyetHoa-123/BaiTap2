<?php
 include('class.php');
 $p = new user();
 if(isset($_REQUEST['delete'])){
    $delete = $_REQUEST['delete'];
    if ($p->deleteKyHan($delete)) {
        echo "Deletion successful";
    } else {
        echo "Deletion failed";
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
    $PerId=$p->reduceUserPerId();
    if ($p->InsertUpdate("INSERT INTO m_user (UserId, InsBy, InsDt, Deleted, LoginId, LoginPassword, UserName, Mail, UserRole, Male, BirthDate, Address) 
    VALUES ('$UserId', 'Admin', '$currentDate', 0, '1111', '$loginId', '$username', '$mail', '$userRole', '$gender', '$birthDate', '$address')")) {
       $datesArray = json_decode($_POST['datesArray'], true);
       if (is_array($datesArray)) {
        foreach ($datesArray as $dates) {
            $dateFirst = $dates['dateFirst'];
            $dateAfter = $dates['dateAfter'];
            $UserPerId=$p->incrementUserPerId();
            // Thực hiện truy vấn thêm ngày và kiểm tra kết quả
            if( $p->InsertUpdate("INSERT INTO m_user_period(UserPerId,InsBy,InsDt,StartDt,EndDt,UserId) 
             VALUES('$UserPerId','Admin','$currentDate','$dateFirst','$dateAfter','$UserId')") ){
            //     echo '<script language="javascript">alert("Thêm Kỳ hạn thành công");</script>';
            // }else {
            //     echo '<script language="javascript">alert("Thêm thất bại khi thêm vào bảng m_user_period");</script>';
            }
            
        }

    echo '<script language="javascript">alert("Data added successfully");</script>';
}
      
    

 }}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AddUser</title>
    <link rel="stylesheet" href="./CSS/addUser.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" /> -->
    <!-- Thêm thư viện flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

</head>

<script>
function validateForm() {
    // Get references to the input elements
    var LoginIdInput = document.getElementById("LoginId");
    var UserNameInput = document.getElementById("UserName");
    var MailInput = document.getElementById("Mail");
    var BirthDateInput = document.getElementById("BirthDate");
    var fromDateInput = document.getElementById("fromDate");
    var toDateInput = document.getElementById("toDate");
    // Check LoginId length
    if (LoginIdInput.value.trim() === "") {
        alert("LoginId cannot be empty.");
        return false;
    }
    if (LoginIdInput.value.length > 50) {
        alert("LoginId must be less than or equal to 50 characters.");
        return false;
    }

    // Check UserName length
    if (UserNameInput.value.trim() === "") {
        alert("UserName cannot be empty.");
        return false;
    }
    if (UserNameInput.value.length > 100) {
        alert("UserName must be less than or equal to 100 characters.");
        return false;
    }
    // Check Mail length
    if (MailInput.value.trim() === "") {
        alert("Mail cannot be empty.");
        return false;
    }
    if (MailInput.value.length > 256) {
        alert("Mail must be less than or equal to 256 characters.");
        return false;
    }

    // Validate email format
    var emailRegex = /^[a-zA-Z0-9._-]+@imlink\.co\.jp$/;
    if (!emailRegex.test(MailInput.value)) {
        alert("Please enter a valid email format: @imlink.co.jp");
        return false;
    }

    // Check if a UserRole is selected
    var userRoleInputs = document.getElementsByName("UserRole");
    var userRoleSelected = false;
    userRoleInputs.forEach(function (input) {
        if (input.checked) {
            userRoleSelected = true;
        }
    });
    if (!userRoleSelected) {
        alert("Please select a value for User Role.");
        return false;
    }

    // Check if a gender is selected
    var genderInputs = document.getElementsByName("gender");
    var genderSelected = false;
    genderInputs.forEach(function (input) {
        if (input.checked) {
            genderSelected = true;
        }
    });
    if (!genderSelected) {
        alert("Please select a value for Gender.");
        return false;
    }
    // Check BirthDate
    if (BirthDateInput.value.trim() === "") {
        alert("Birthdate cannot be empty.");
        return false;
    }
    // Validate date format (YYYY/MM/DD)
    var dateRegex = /^\d{4}\/\d{2}\/\d{2}$/;
    if (!dateRegex.test(BirthDateInput.value)) {
        alert("Please enter a valid date format (YYYY/MM/DD) for Birthdate.");
        return false;
    }
    // Check if the entered date is in the past
    var currentDate = new Date();
    if (enteredBirthDate >= currentDate) {
        alert("Birthdate must be a date in the past.");
        return false;
    }

    // Check fromDate
    if (fromDateInput.value.trim() === "") {
        alert("Start date cannot be empty.");
        return false;
    }

    // Validate date format (YYYY/MM/DD) for fromDate
    if (!dateRegex.test(fromDateInput.value)) {
        alert("Please enter a valid date format (YYYY/MM/DD) for Start date.");
        return false;
    }
    // Check toDate
    if (toDateInput.value.trim() === "") {
        alert("End date cannot be empty.");
        return false;
    }
// Validate date format (YYYY/MM/DD) for toDate
    if (!dateRegex.test(toDateInput.value)) {
        alert("Please enter a valid date format (YYYY/MM/DD) for End date.");
        return false;
    }
    // Check if fromDate is before toDate
    if (enteredFromDate >= enteredToDate) {
        alert("Start date must be before End date.");
        return false;
    }
    // Additional checks for fromDate and toDate if needed

    return true;
}
</script>

<body>
    <div class="container">
        <div class="section">
            <h1> ユーザー登録</h1>
            <form action="#" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="common-section">
            <input type="number" id="UserId" name="UserId" value="<?php echo $p->incrementUserID(); ?>" hidden >

                </div>
                <div class="common-section">
                    <label for="LoginId">ログインID</label><br>
                    <input type="text" id="LoginId" name="LoginId" placeholder="ログインID"style="border-radius: 4px 4px 4px 4px;border:1px solid #dedede; width: 100%" ><br>
                </div>
                <div class="common-section">
                    <label for="UserName">ユーザー名</label><br>
                    <input type="text" id="UserName" name="UserName" placeholder="ユーザー名"
                        style="border-radius: 4px 4px 4px 4px;border:1px solid #dedede; width: 100%"><br>
                </div>
                <div class="common-section">
                    <label for="Mail">メール</label><br>
                    <input type="text" id="Mail" name="Mail" placeholder="メール"
                    style="border-radius: 4px 4px 4px 4px;border:1px solid #dedede; width: 100%"><br>
                </div>
                <div class="common-section">
                    <label for="UserRole">自社ユーザーロール</label><br>
                    <input type="radio" name="UserRole">管理者 
                    <input type="radio" name="UserRole">一 般
                </div>
                <div class="common-section">
                    <label for="gender">性別</label><br>
                    <input type="radio" name="gender"> 男
                    <input type="radio" name="gender">女
                </div>
                <div class="common-section">
                    <label for="BirthDate">誕生日</label><br>
                    <div class="input-with-icon">
                    <input type="text" id="BirthDate" name="BirthDate" placeholder="YYYY/MM/DD">
                        <i class="fa fa-calendar"  id="datepicker-icon"></i>

                    </div>
                </div>

                <div class="common-section">
                    <label for="Address">住所</label><br>
                    <input type="text" id="Address" name="Address" placeholder="住所"
                    style="border-radius: 4px 4px 4px 4px;border:1px solid #dedede; width: 100%"><br>
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

                            <button id="dateButton" style="background-color: #dedede; border: none;margin-bottom: 10px;width: 50px;">住所</button>
                        </div>
                        <div class="all-durations">
                        </div>
                    </div>
                    <!-- Ô input ẩn để lưu trữ các ngày đã chọn -->
                <input type="hidden" name="datesArray" id="datesArray" value="">
                        
                    </div>
                </div>
                <div class="btn">
                    <input type="reset" name="btnReset" style=" border:none;background-color:#626262;height:30px" value="閉じる">
                    <input type="submit" name="btnSubmit" value="保存"
                        style="background-color: #1d92af; color: #fff; border:none; width:50px;height:30px">
                </div>
                

            </form>
        </div>
    </div>

</body><script>
document.getElementById('dateButton').addEventListener('click', function (event) {
    event.preventDefault();
    var dateFirst = document.querySelector('.inp-dateFirst').value;
    var dateAfter = document.querySelector('.inp-dateAfter').value;

    // Kiểm tra nếu dateFirst sau dateAfter
    if (new Date(dateFirst) >= new Date(dateAfter)) {
        alert("Ngày bắt đầu phải trước ngày kết thúc.");
        return;
    }

    // Kiểm tra chồng lấn với các kỳ hạn hiện tại
    var datesArray = JSON.parse(document.getElementById('datesArray').value || '[]');
    for (var i = 0; i < datesArray.length; i++) {
        if (new Date(dateFirst) <= new Date(datesArray[i].dateAfter) && new Date(dateAfter) >= new Date(datesArray[i].dateFirst)) {
            alert("Kỳ hạn mới chồng lấn với một kỳ hạn hiện tại. Hãy chọn các ngày khác nhau.");
            return;
        }
        // Kiểm tra nếu thời gian bắt đầu và kết thúc của kỳ hạn mới nằm trong một kỳ hạn hiện tại
        if (new Date(dateFirst) >= new Date(datesArray[i].dateFirst) && new Date(dateAfter) <= new Date(datesArray[i].dateAfter)) {
            alert("Kỳ hạn mới nằm trong một kỳ hạn hiện tại. Hãy chọn các ngày khác nhau.");
            return;
        }
        // Kiểm tra nếu thời gian bắt đầu hoặc kết thúc của kỳ hạn mới nằm giữa một kỳ hạn hiện tại
        if (new Date(datesArray[i].dateFirst) <= new Date(dateFirst) && new Date(dateFirst) <= new Date(datesArray[i].dateAfter)
            || new Date(datesArray[i].dateFirst) <= new Date(dateAfter) && new Date(dateAfter) <= new Date(datesArray[i].dateAfter)) {
            alert("Thời gian bắt đầu hoặc kết thúc của kỳ hạn mới nằm giữa một kỳ hạn hiện tại. Hãy chọn các ngày khác nhau.");
            return;
        }
    }
    // Lưu các ngày đã chọn vào mảng
    datesArray.push({ dateFirst: dateFirst, dateAfter: dateAfter });
    document.getElementById('datesArray').value = JSON.stringify(datesArray);

    // Hiển thị các ngày đã chọn trên giao diện
    var allDurations = document.querySelector('.all-durations');
    var newDuration = document.createElement('div');
    newDuration.classList.add('duration-date');
    newDuration.innerHTML = '<div class="dateFirst">' + dateFirst + '</div><p>~</p><div class="dateAfter">' + dateAfter + '</div><i class="fa fa-trash-o" aria-hidden="true" style="color: red; cursor: pointer;"></i></a>';

    // Attach click event to the trash can icon for deletion
    newDuration.querySelector('.fa-trash-o').addEventListener('click', function () {
        // Ask for confirmation
        if (confirm('Bạn có chắc xóa!')) {
            // Remove the selected duration from the array
            datesArray = datesArray.filter(function (item) {
                return !(item.dateFirst === dateFirst && item.dateAfter === dateAfter);
            });

            // Update the hidden input value
            document.getElementById('datesArray').value = JSON.stringify(datesArray);

            // Remove the duration from the display
            allDurations.removeChild(newDuration);
        }
    });

    allDurations.appendChild(newDuration);
    allDurations.appendChild(document.createElement('br'));
});
// Birthday
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
// fromDate
document.getElementById('fromDate-icon').addEventListener('click', function () {
    // Mở datepicker khi icon được click
    document.getElementById('fromDate').flatpickr.open();
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