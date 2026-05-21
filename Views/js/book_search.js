function ajaxSearchBooks() {
    const searchVal = document.getElementById("bookSearch").value;
    const genreVal = document.getElementById("bookGenre").value;
    const branchVal = document.getElementById("bookBranch").value;
    const yearVal = document.getElementById("bookYear").value;
    const tbody = document.getElementById("bookTableBody");

    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
        if (xhttp.status === 200) {
            const books = JSON.parse(xhttp.responseText);
            let display = "";

            function getCoverCell(book) {
                if (book.cover_image_path && String(book.cover_image_path).trim() !== "") {
                    return "<img src='/LibraryManagementSystem/" + book.cover_image_path + "' alt='Book Cover' width='52' style='border-radius:6px;object-fit:cover;'>";
                }

                return "<span style='font-size:12px;color:#9ca3af;'>No cover</span>";
            }

            if (books.length === 0) {
                display = "<tr><td colspan='7'>No books found.</td></tr>";
            } else {
                for (let i = 0; i < books.length; i++) {
                    display += "<tr>";
                    display += "<td>" + getCoverCell(books[i]) + "</td>";
                    display += "<td>" + books[i].title + "</td>";
                    display += "<td>" + books[i].author + "</td>";
                    display += "<td>" + books[i].genre_name + "</td>";
                    display += "<td>" + books[i].isbn + "</td>";
                    display += "<td>" + books[i].published_year + "</td>";
                    display += "<td>" +
                        "<form method='POST' action='../../Controllers/BookDetailsController.php' style='display:inline;'>" +
                        "<input type='hidden' name='id' value='" + books[i].id + "'>" +
                        "<button type='submit' class='btn-link'>View Details</button>" +
                        "</form>" +
                        "</td>";
                    display += "</tr>";
                }
            }
            tbody.innerHTML = display;
        }
    };
    xhttp.open("POST", "../../Controllers/BookSearchApiController.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send("search=" + encodeURIComponent(searchVal) + 
               "&genre_id=" + encodeURIComponent(genreVal) + 
               "&branch_id=" + encodeURIComponent(branchVal) + 
               "&year=" + encodeURIComponent(yearVal));
}
