<?php
require_once 'koneksi.php';

// Hapus data jika ada request delete
$pesan = '';
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM mahasiswa WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $pesan = '<div class="notif sukses">✅ Data berhasil dihapus.</div>';
    } else {
        $pesan = '<div class="notif error">❌ Gagal menghapus data: ' . mysqli_error($conn) . '</div>';
    }
    mysqli_stmt_close($stmt);
}

// Ambil semua data mahasiswa
$query = "SELECT * FROM mahasiswa ORDER BY id DESC";
$result = mysqli_query($conn, $query);
if (!$result) {
    die('<div class="notif error">❌ Query gagal: ' . mysqli_error($conn) . '</div>');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0d0d0f;
            --surface: #151518;
            --surface2: #1c1c21;
            --border: #2a2a32;
            --accent: #c8f135;
            --accent2: #6c63ff;
            --text: #e8e8f0;
            --muted: #6b6b7e;
            --danger: #ff4d6d;
            --success: #22c55e;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Syne', sans-serif;
            min-height: 100vh;
        }
        /* Noise texture overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.5;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 48px 24px;
            position: relative;
            z-index: 1;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 48px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
        }
        .header-title {
            font-size: 13px;
            letter-spacing: 0.15em;
            color: var(--accent);
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .header h1 {
            font-size: 38px;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .btn-tambah {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--accent);
            color: #0d0d0f;
            text-decoration: none;
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.05em;
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
        }
        .btn-tambah:hover {
            background: #d9ff50;
            transform: translateY(-2px);
        }

        /* Notifikasi */
        .notif {
            padding: 14px 20px;
            border-radius: 0;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 28px;
            border-left: 3px solid;
        }
        .notif.sukses { background: rgba(34,197,94,0.1); border-color: var(--success); color: var(--success); }
        .notif.error  { background: rgba(255,77,109,0.1); border-color: var(--danger); color: var(--danger); }

        /* Stats bar */
        .stats-bar {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
        }
        .stat-item {
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 16px 24px;
            font-size: 13px;
            color: var(--muted);
        }
        .stat-item strong {
            display: block;
            font-family: 'DM Mono', monospace;
            font-size: 28px;
            color: var(--text);
            font-weight: 500;
            line-height: 1.1;
        }

        /* Table */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead {
            background: var(--surface2);
        }
        thead th {
            padding: 14px 20px;
            text-align: left;
            font-size: 11px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 600;
            border-bottom: 1px solid var(--border);
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background 0.15s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--surface2); }
        tbody td {
            padding: 16px 20px;
            font-size: 14px;
            vertical-align: middle;
        }
        .td-nim {
            font-family: 'DM Mono', monospace;
            color: var(--accent);
            font-size: 13px;
            font-weight: 500;
        }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: rgba(108,99,255,0.15);
            color: #a89fff;
            border: 1px solid rgba(108,99,255,0.3);
        }
        .badge-jk {
            background: rgba(200,241,53,0.1);
            color: var(--accent);
            border-color: rgba(200,241,53,0.25);
        }

        /* Action buttons */
        .actions { display: flex; gap: 8px; }
        .btn-aksi {
            padding: 7px 14px;
            font-family: 'Syne', sans-serif;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            letter-spacing: 0.05em;
            border: 1px solid;
            transition: all 0.15s;
            cursor: pointer;
            background: transparent;
        }
        .btn-edit {
            color: var(--accent2);
            border-color: rgba(108,99,255,0.4);
        }
        .btn-edit:hover {
            background: rgba(108,99,255,0.15);
        }
        .btn-hapus {
            color: var(--danger);
            border-color: rgba(255,77,109,0.4);
        }
        .btn-hapus:hover {
            background: rgba(255,77,109,0.15);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 72px 24px;
            color: var(--muted);
        }
        .empty-state .icon { font-size: 48px; margin-bottom: 16px; }
        .empty-state p { font-size: 16px; }

        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 20px; align-items: flex-start; }
            .header h1 { font-size: 28px; }
            table { font-size: 13px; }
            tbody td, thead th { padding: 12px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <div class="header-title">&#9632; Sistem Manajemen</div>
            <h1>Data Mahasiswa</h1>
        </div>
        <a href="tambah.php" class="btn-tambah">+ Tambah Mahasiswa</a>
    </div>

    <?= $pesan ?>

    <div class="stats-bar">
        <div class="stat-item">
            <strong><?= mysqli_num_rows($result) ?></strong>
            Total Mahasiswa
        </div>
    </div>

    <div class="table-wrap">
        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Jurusan</th>
                    <th>Semester</th>
                    <th>Jenis Kelamin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td style="color:var(--muted); font-family:'DM Mono',monospace; font-size:12px;"><?= $no++ ?></td>
                    <td class="td-nim"><?= htmlspecialchars($row['nim']) ?></td>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['nama']) ?></td>
                    <td><span class="badge"><?= htmlspecialchars($row['jurusan']) ?></span></td>
                    <td style="font-family:'DM Mono',monospace; text-align:center;"><?= htmlspecialchars($row['semester']) ?></td>
                    <td><span class="badge badge-jk"><?= htmlspecialchars($row['jenis_kelamin']) ?></span></td>
                    <td>
                        <div class="actions">
                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn-aksi btn-edit">Edit</a>
                            <a href="index.php?hapus=<?= $row['id'] ?>"
                               class="btn-aksi btn-hapus"
                               onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <div class="icon">📂</div>
            <p>Belum ada data mahasiswa. Tambah sekarang!</p>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
