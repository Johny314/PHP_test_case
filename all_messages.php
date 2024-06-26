<?php
require "db.php";

global $mysqli;

?>
<!doctype html>
<html lang="en" data-bs-theme="dark" class="h-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Все сообщения</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body class="d-flex flex-column h-100">
<div class="container">
    <?php include "inc/header.html"; ?>

    <div>
        <h2>Все сообщения</h2>
    </div>

    <div class="spinner-border text-primary d-none" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>

    <div class="all_messages"></div>

    <div class="pagination_container"></div>

    <?php include "inc/footer.html"; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btn = document.getElementById('2');
        btn.classList.add('active');
        const messagesContainer = document.querySelector(".all_messages");
        const spinner = document.querySelector(".spinner-border");
        const paginationContainer = document.querySelector(".pagination_container");

        function showSpinner() {
            spinner.classList.remove("d-none");
        }

        function hideSpinner() {
            spinner.classList.add("d-none");
        }

        function createTableHeader() {
            const headerRow = document.createElement("tr");
            const columns = ['ID', 'User Name', 'Email', 'Message Text', 'Date'];
            const sortFields = ['id', 'user_name', 'email', 'message_text', 'date'];

            columns.forEach((column, index) => {
                const th = document.createElement("th");
                const columnHeader = document.createElement("div");
                columnHeader.textContent = column;
                th.appendChild(columnHeader);
                columnHeader.classList.add("sortButtons");
                const sortButtonAsc = document.createElement("button");
                sortButtonAsc.classList.add("btn", "btn-link", "sortButton");
                sortButtonAsc.dataset.sort = sortFields[index];
                sortButtonAsc.dataset.order = "asc";
                sortButtonAsc.innerHTML = '<i class="bi bi-arrow-up"></i>';
                const sortButtonDesc = document.createElement("button");
                sortButtonDesc.classList.add("btn", "btn-link", "sortButton");
                sortButtonDesc.dataset.sort = sortFields[index];
                sortButtonDesc.dataset.order = "desc";
                sortButtonDesc.innerHTML = '<i class="bi bi-arrow-down"></i>';
                columnHeader.appendChild(sortButtonAsc);
                columnHeader.appendChild(sortButtonDesc);
                th.appendChild(columnHeader);
                headerRow.appendChild(th);

                // Добавление обработчика события на кнопки сразу при создании
                sortButtonAsc.addEventListener("click", function() {
                    handleSortClick(sortFields[index], "asc");
                });

                sortButtonDesc.addEventListener("click", function() {
                    handleSortClick(sortFields[index], "desc");
                });
            });

            return headerRow;
        }

        function handleSortClick(sortBy, sortOrder) {
            const currentSortOrder = sortOrder === "asc" ? "desc" : "asc";
            fetchMessages(sortBy, currentSortOrder, 1, 5);
        }

        function createPagination(totalMessages, currentPage, perPage , sort, order) {
            paginationContainer.innerHTML = ""; // Clear existing pagination buttons
            const pageCount = Math.ceil(totalMessages / perPage);
            for (let i = 1; i <= pageCount; i++) {
                const pageButton = document.createElement("button");
                pageButton.textContent = i;
                pageButton.classList.add("btn", "btn-secondary", "mx-1");
                pageButton.dataset.page = i;
                if (currentPage === i) {
                    pageButton.classList.add("active");
                }
                pageButton.addEventListener("click", function() {
                    fetchMessages(sort, order, i, perPage);
                });
                paginationContainer.appendChild(pageButton);
            }
        }

        function fetchMessages(sortBy, sortOrder, page, perPage) {
            showSpinner();
            const formData = new FormData();
            formData.append('event', 'all_messages');
            formData.append('sort', sortBy);
            formData.append('order', sortOrder);
            formData.append('page', page);
            formData.append('perPage', perPage);

            fetch('handler.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    messagesContainer.innerHTML = "";
                    const table = document.createElement("table");
                    table.classList.add("table");
                    table.classList.add("table-bordered");
                    const headerRow = createTableHeader();
                    table.appendChild(headerRow);
                    data.messages.forEach(message => {
                        const row = document.createElement("tr");
                        row.innerHTML = `
                        <td style="min-width: 140px;">${message.id}</td>
                        <td style="min-width: 200px;">${message.user_name}</td>
                        <td style="min-width: 200px;">${message.email}</td>
                        <td style="min-width: 100px;">${message.message_text}</td>
                        <td style="min-width: 200px;">${message.date}</td>`;
                        table.appendChild(row);
                    });
                    messagesContainer.appendChild(table);
                    createPagination(data.totalMessages, page, perPage , sortBy, sortOrder); // Update pagination
                    hideSpinner();
                });
        }

        fetchMessages("id", "asc", 1, 5);
    });
</script>
</body>
</html>