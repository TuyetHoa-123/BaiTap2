<?php
header('Content-Type: text/html; charset=utf-8');
?>
<?php
include('class.php');
$p = new user();

// Kiểm tra xem có truy vấn tìm kiếm không
// Kiểm tra xem có truy vấn tìm kiếm không
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Lấy dữ liệu dựa trên truy vấn tìm kiếm hoặc truy vấn mặc định
if (!empty($searchQuery)) {

    $sql = "SELECT * FROM M_User
    WHERE Deleted = 0 AND (
        LoginId LIKE '%$searchQuery%' OR
        UserName LIKE '%$searchQuery%' OR
        Mail LIKE '%$searchQuery%' OR
        (CASE WHEN Male = 0 THEN '男' ELSE '女' END) LIKE '%$searchQuery%' OR
        (CASE WHEN UserRole = 9 THEN 'Admin' ELSE 'User' END) LIKE '%$searchQuery%' OR
        Address LIKE '%$searchQuery%' OR
        DATE_FORMAT(BirthDate, '%Y-%m-%d') LIKE '%$searchQuery%'
    );
    ";
    $searchResults = $p->layDuLieu($sql);
    
    // Kiểm tra xem có dữ liệu tìm kiếm không
    if (empty($searchResults)) {
        echo '<script>alert("Không tìm thấy dữ liệu!"); window.location.href = "index.php";</script>';
        exit; // Dừng việc thực hiện mã PHP để tránh hiển thị trang với dữ liệu không tồn tại
    }

} else {
  
    $searchResults = $p->layDuLieu('SELECT * FROM M_User WHERE Deleted = 0');
}


// Tính toán số trang và phân chia dữ liệu thành các trang
$itemsPerPage = 3;
$totalItems = count($searchResults);
$totalPages = ceil($totalItems / $itemsPerPage);

// Lấy trang hiện tại từ tham số truy vấn
$currentpage = isset($_GET['page']) ? $_GET['page'] : 1;
$currentPage = max(1, min($totalPages, intval($currentpage)));

// Xác định vị trí bắt đầu của dữ liệu trên trang hiện tại
$startIndex = ($currentPage - 1) * $itemsPerPage;

// Lấy một phần của dữ liệu để hiển thị trên trang hiện tại
$currentPageData = array_slice($searchResults, $startIndex, $itemsPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="./CSS/index.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
</head>
<body>

<div class="container">

    <header>
        <div class="left" >
        <div class="user" style =" float: left; padding-right:10px;padding-top:7px"> ユーザー</div>
        <div  class= "add" style ="border-left:1px solid #e1e1e1;padding-left:20px; float: left;">
             <button>
           <a href="addUser.php" style=""><i class="fa fa-plus-square" aria-hidden="true" style="background-color: #49a749;"></i> 新規作成 </a></button></div>
        </div>
        <div class="right"><i class="fa fa-cog" aria-hidden="true"></i>  &nbsp;管理 
        <i class="fa fa-angle-right" aria-hidden="true"></i>  ユーザー</div>
    </header>
    <section>
    <div class="userlist">ユーザ一覧</div>
    <div class="Comit">
            <button onclick="exportToXML()" class="xml">XML出力</button>
                <div class="search">
                    <form action="search.php" method="get">
                        <div class="search">
                            <input type="search" name="search" placeholder="キーワード" class="search-input">
                       <button class="search-icon" style="border:none; background-color:white"><i class="fa fa-search" aria-hidden="true" ></i></button>
                            
                        </div>
                       
                    </form>

                </div>

            </div>
    <div class="list">
        <table id="myTable">
            <thead>
                <tr>
                    <th></th>
                    <th>ログインID</th>
                    <th>ユーザー名</th>
                    <th>メール</th>
                    <th>自社ユーザーロール</th>
                    <th>性別</th>
                    <th>誕生日</th>
                    <th>住所</th>
                </tr>
            </thead>
            <tbody id="myTableBody">
                <?php foreach ($currentPageData as $user) : ?>
                    <tr>
                        <td style="text-align: left;">
                            <a href="edit.php?edit=<?php echo $user['UserId']; ?>"><i class="fa-solid fa-pen" style="color: #ff9f4a;"></i></a>
                            <a href="coppy.php?coppy=<?php echo $user['UserId']; ?>"><i class="fa-regular fa-copy"></i></a>
                            <a href="index.php?delete=<?php echo $user['UserId']; ?>" onclick="return confirm('Bạn có chắc xóa!')"><i class="fa-solid fa-trash-can" style="color: red;"></i></a>
                            &nbsp;&nbsp;&nbsp;
                        </td>
                        <td><?php echo $user['LoginId']; ?></td>
                        <td><?php echo $user['UserName']; ?></td>
                        <td><?php echo $user['Mail']; ?></td>
                        <td><?php echo $user['UserRole'] == 0 ? '一般' : ($user['UserRole'] == 9 ? '管理者' : $user['UserRole']); ?></td>
                        <td><?php echo $user['Male'] == 0 ? '男' : ($user['Male'] == 1 ? '女' : $user['Male']); ?></td>
                        <td><?php echo date('Y/m/d', strtotime($user['BirthDate'])); ?></td>
                        <td><?php echo $user['Address']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <ul class="pagination" id="pagination" style="float: right; margin-right: 80px;"></ul>
    </div>
</div>

</section>
<script>
     const itemsPerPage = 3; // Hiển thị 3 mục trên mỗi trang
    let currentPage = 1;

    const tableBody = document.getElementById('myTableBody');
    const paginationContainer = document.getElementById('pagination');
    // const data = <?php echo json_encode($p->layDuLieu('SELECT * FROM M_User WHERE Deleted = 0')); ?>;
    const searchData = <?php echo isset($searchResults) ? json_encode($searchResults) : '[]'; ?>;
    const data = searchData.length > 0 ? searchData : <?php echo json_encode($p->layDuLieu('SELECT * FROM M_User WHERE Deleted = 0')); ?>;
    function renderTable() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const slicedData = data.slice(startIndex, endIndex);

        tableBody.innerHTML = '';
        slicedData.forEach(user => {
            const row = document.createElement('tr');
            const birthDate = new Date(user.BirthDate);
            const formattedBirthDate = `${birthDate.getFullYear()}/${(birthDate.getMonth() + 1).toString().padStart(2, '0')}/${birthDate.getDate().toString().padStart(2, '0')}`;

            row.innerHTML = `<td style="text-align: left;">
                <a href="edit.php?edit=${user.UserId}"><i class="fa-solid fa-pen" style="color: #ff9f4a;"></i><i class="fa fa-pencil" aria-hidden="true" style="color: #ff9f4a;"></i></a>
                <a href="coppy.php?coppy=${user.UserId}"><i class="fa fa-files-o" aria-hidden="true" style="color: #8c8f96;"></i></a>
                <a href="index.php?delete=${user.UserId}" onclick="return confirm('Bạn có chắc xóa!')"><i class="fa fa-trash" aria-hidden="true" style="color: #f42f2f;"></i></a>
                &emsp;
            </td>
            <td>${user.LoginId}</td>
            <td>${user.UserName}</td>
            <td>${user.Mail}</td>
            <td>${user.UserRole == 0 ? 'User' : (user.UserRole == 9 ? 'Admin' : user.UserRole)}</td>
            <td>${user.Male == 0 ? '男' : (user.Male == 1 ? '女' : user.Male)}</td>
            <td>${formattedBirthDate}</td>
            <td>${user.Address}</td>`;
            tableBody.appendChild(row);
        });
    }
    function renderPagination(totalPages) {
    paginationContainer.innerHTML = '';
    const createPaginationButton = (html, clickHandler, isDisabled = false) => {
        const button = document.createElement('li');
        button.innerHTML = html;
        button.addEventListener('click', clickHandler);
        if (isDisabled) {
            button.classList.add('disabled');
        }
        if (html === currentPage || (html === '...' && currentPage > totalPages - 3)) {
            button.classList.add('active');
        }
        paginationContainer.appendChild(button);
    };

    const showEllipsis = totalPages > 5;
    // Show "<<"
    createPaginationButton('<i class="fa fa-angle-double-left" aria-hidden="true"></i>', () => handlePaginationClick(1), currentPage === 1);
    createPaginationButton('<i class="fa fa-angle-left" aria-hidden="true"></i>', () => handlePaginationClick(currentPage - 1), currentPage === 1);

    if (currentPage === totalPages - 1 || currentPage === totalPages) {
        // Show the first two pages
        for (let i = 1; i <= 2; i++) {
            createPaginationButton(i, () => handlePaginationClick(i), i == currentPage);
        }
        // Show "..."
        if (showEllipsis) {
            const ellipsisItem = document.createElement('li');
            ellipsisItem.textContent = '...';
            paginationContainer.appendChild(ellipsisItem);
        }
        // Show the last two pages
        for (let i = totalPages - 1; i <= totalPages; i++) {
            createPaginationButton(i, () => handlePaginationClick(i), i === currentPage);
        }
    } else {
        // Show pages close to the current page
        if (currentPage <= totalPages - 3) {
            for (let i = currentPage; i <= currentPage + 1; i++) {
                createPaginationButton(i, () => handlePaginationClick(i), i == currentPage);
            }
            // Show "..."
            if (showEllipsis) {
                const ellipsisItem = document.createElement('li');
                ellipsisItem.textContent = '...';
                paginationContainer.appendChild(ellipsisItem);
            }
        } else {
            // Show pages up to the current page
            for (let i = totalPages - 3; i <= totalPages - 2; i++) {
                createPaginationButton(i, () => handlePaginationClick(i), i === currentPage);
            }
        }
        // Show last two pages
        if (currentPage === totalPages - 1) {
            createPaginationButton(currentPage, () => handlePaginationClick(totalPages - 1));
        } else {
            createPaginationButton(totalPages - 1, () => handlePaginationClick(totalPages - 1));
        }
        if (currentPage === totalPages) {
            createPaginationButton(currentPage, () => handlePaginationClick(totalPages));
        } else {
            createPaginationButton(totalPages, () => handlePaginationClick(totalPages));
        }
    }

    // Show ">>"
    createPaginationButton('<i class="fa fa-angle-right" aria-hidden="true"></i>', () => handlePaginationClick(currentPage + 1), currentPage === totalPages);
    // Show ">>>"
    createPaginationButton('<i class="fa fa-angle-double-right" aria-hidden="true"></i>', () => handlePaginationClick(totalPages), currentPage === totalPages);

    renderTable();
}
    function handlePaginationClick(pageNumber) {
        if (pageNumber >= 1 && pageNumber <= totalPages) {
            currentPage = pageNumber;
            renderPagination(totalPages);
        }
    }

    const totalPages = Math.ceil(data.length / itemsPerPage);
    renderPagination(totalPages);

    function exportToXML() {
        const xmlData = document.implementation.createDocument(null, 'users');

        data.forEach(user => {
            const userElement = xmlData.createElement('user');
            const birthDate = new Date(user.BirthDate);
            const formattedBirthDate = `${birthDate.getFullYear()}/${(birthDate.getMonth() + 1).toString().padStart(2, '0')}/${birthDate.getDate().toString().padStart(2, '0')}`;

            userElement.innerHTML = `
                <LoginId>${user.LoginId}</LoginId>
                <UserName>${user.UserName}</UserName>
                <Mail>${user.Mail}</Mail>
                <UserRole>${user.UserRole}</UserRole>
                <Male>${user.Male}</Male>
                <BirthDate>${formattedBirthDate}</BirthDate>
                <Address>${user.Address}</Address>
            `;
            xmlData.documentElement.appendChild(userElement);
        });

        // Create a Blob and trigger a download
        const blob = new Blob([new XMLSerializer().serializeToString(xmlData)], { type: 'application/xml' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'users.xml';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
</body>

</html>