$(document).ready(function() { // memastikan dokumen siap sebelum menjalankan skrip 
    $.ajax({ // melakukan permintaan AJAX (data asynchronous) ke server
        url: "https://jsonplaceholder.typicode.com/users", // URL API yang akan diakses
        method: "GET", 
        datatype: "json",

        success: function(data) { // fungsi yang dijalankan jika permintaan berhasil (tampilkan data di tabel)
            data.forEach((user) => { // iterasi setiap user dalam data yang diterima
                $("tbody").append(` 
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.address.city}</td>
                        <td>${user.company.name}</td>                    
                    </tr>
                `);
            });
        },

        error: function(error) { // fungsi yang dijalankan jika permintaan gagal
            console.log("Error:", error); // menampilkan pesan error di konsol
        },
    });
});