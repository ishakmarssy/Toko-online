<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <title>Tidak Ada Koneksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(84, 84, 84);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            padding: 20px;
            background-color:rgb(0, 0, 0);
            border: 1px solidrgb(132, 132, 132);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        h1 {
            color:rgb(255, 255, 255);
            font-size: 24px;
            margin-bottom: 10px;
        }

        p {
            color:rgb(255, 255, 255);
            font-size: 16px;
            margin-bottom: 20px;
        }

        button {
            background-color: #ff5c5c;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #e53935;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Tidak Ada Koneksi Internet</h1>
    <p>Periksa kembali koneksi internet Anda!</p>
    <button onclick="retryConnection()"><i class="bi bi-arrow-clockwise"></i> Coba Lagi</button>
</div>

<script>
    // Fungsi untuk mencoba menyambungkan kembali ke halaman utama
    function retryConnection() {
        if (navigator.onLine) {
            window.location.href = 'javascript:history.back()'; // Kembali ke halaman utama jika koneksi pulih
        } else {
            alert('Koneksi masih terputus! Coba lagi nanti.');
        }
    }

    // Cek koneksi otomatis setiap 2 detik
    setInterval(() => {
        if (navigator.onLine) {
            window.location.href = 'javascript:history.back()'; // Kembali ke halaman utama jika koneksi pulih
        }
    }, 2000);
</script>

</body>
</html>
