<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Aplikasi</title>
    <style>
        body { font-family: system-ui, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; background: #f3f4f6; color: #1f2937; }
        .card { background: white; padding: 2rem; border-radius: 1rem; shadow: 0 4px 6px -1px rgba(0,0,0,0.1); text-align: center; max-width: 400px; }
        h1 { font-size: 1.5rem; margin-bottom: 1rem; }
        p { margin-bottom: 1.5rem; color: #4b5563; }
        .btn { background: #2563eb; color: white; padding: 0.75rem 1.5rem; border-radius: 0.5rem; text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Memperbarui Aplikasi...</h1>
        <p>Sedang membersihkan cache dan memperbarui sistem ke versi terbaru.</p>
        <div id="status">Menunggu...</div>
    </div>
    <script>
        const status = document.getElementById('status');
        async function reset() {
            try {
                if ('serviceWorker' in navigator) {
                    status.textContent = "Mencari Service Worker...";
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    for(let registration of registrations) {
                        await registration.unregister();
                        status.textContent = "Service Worker dihapus...";
                    }
                }
                if ('caches' in window) {
                    status.textContent = "Membersihkan Cache...";
                    const keys = await caches.keys();
                    for (let key of keys) {
                        await caches.delete(key);
                    }
                }
                status.textContent = "Selesai! Mengalihkan...";
                setTimeout(() => {
                    window.location.href = 'peta.php?v=' + Date.now();
                }, 1000);
            } catch (e) {
                status.textContent = "Error: " + e.message;
                alert("Gagal reset otomatis. Silakan hapus history browser Anda.");
            }
        }
        reset();
    </script>
</body>
</html>