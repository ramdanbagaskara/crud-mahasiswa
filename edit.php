<?php
require_once 'koneksi.php';

$pesan = '';
$error = [];


$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit;
}


$stmt = mysqli_prepare($conn, "SELECT * FROM mahasiswa WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    header('Location: index.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim     = trim($_POST['nim'] ?? '');
    $nama    = trim($_POST['nama'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');
    $semester= (int) ($_POST['semester'] ?? 0);
    $jk      = $_POST['jenis_kelamin'] ?? '';

    if (empty($nim))     $error[] = 'NIM wajib diisi.';
    if (empty($nama))    $error[] = 'Nama wajib diisi.';
    if (empty($jurusan)) $error[] = 'Jurusan wajib diisi.';
    if ($semester < 1 || $semester > 14) $error[] = 'Semester harus antara 1–14.';
    if (!in_array($jk, ['Laki-laki', 'Perempuan'])) $error[] = 'Jenis kelamin tidak valid.';

    if (empty($error)) {
        $stmt = mysqli_prepare($conn, "UPDATE mahasiswa SET nim=?, nama=?, jurusan=?, semester=?, jenis_kelamin=? WHERE id=?");
        if (!$stmt) {
            $pesan = '<div class="notif error">❌ Prepare statement gagal: ' . mysqli_error($conn) . '</div>';
        } else {
            mysqli_stmt_bind_param($stmt, "sssisi", $nim, $nama, $jurusan, $semester, $jk, $id);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: index.php?edit=1');
                exit;
            } else {
                $pesan = '<div class="notif error">❌ Gagal mengupdate data: ' . mysqli_stmt_error($stmt) . '</div>';
            }
            mysqli_stmt_close($stmt);
        }
    }

    
    $data['nim']          = $_POST['nim'] ?? $data['nim'];
    $data['nama']         = $_POST['nama'] ?? $data['nama'];
    $data['jurusan']      = $_POST['jurusan'] ?? $data['jurusan'];
    $data['semester']     = $_POST['semester'] ?? $data['semester'];
    $data['jenis_kelamin']= $_POST['jenis_kelamin'] ?? $data['jenis_kelamin'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 24px;
        }
        .card { width: 100%; max-width: 560px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 32px;
            transition: color 0.15s;
        }
        .back-link:hover { color: var(--text); }
        .form-header { margin-bottom: 36px; }
        .form-header span {
            font-size: 12px;
            letter-spacing: 0.15em;
            color: #ff9f43;
            text-transform: uppercase;
            font-weight: 600;
        }
        .form-header h2 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-top: 6px;
        }
        .id-badge {
            display: inline-block;
            margin-top: 10px;
            background: var(--surface2);
            border: 1px solid var(--border);
            padding: 4px 12px;
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            color: var(--muted);
        }
        .notif {
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
            border-left: 3px solid;
        }
        .notif.error { background: rgba(255,77,109,0.1); border-color: var(--danger); color: var(--danger); }
        .error-list {
            background: rgba(255,77,109,0.08);
            border: 1px solid rgba(255,77,109,0.25);
            border-left: 3px solid var(--danger);
            padding: 16px 20px;
            margin-bottom: 24px;
        }
        .error-list p { color: var(--danger); font-size: 13px; font-weight: 600; margin-bottom: 8px; }
        .error-list ul { list-style: none; }
        .error-list li { font-size: 13px; color: #ff8099; padding: 2px 0; }
        .error-list li::before { content: '— '; }
        .form-group { margin-bottom: 22px; }
        label {
            display: block;
            font-size: 11px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--muted);
            font-weight: 600;
            margin-bottom: 8px;
        }
        input, select {
            width: 100%;
            background: var(--surface);
            border: 1px solid var(--border);
            color: var(--text);
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            padding: 13px 16px;
            outline: none;
            transition: border-color 0.15s;
            appearance: none;
            -webkit-appearance: none;
        }
        input:focus, select:focus { border-color: #ff9f43; }
        input::placeholder { color: var(--muted); }
        .select-wrap { position: relative; }
        .select-wrap::after {
            content: '▾';
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .btn-submit {
            width: 100%;
            background: #ff9f43;
            color: #0d0d0f;
            border: none;
            font-family: 'Syne', sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.05em;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
            clip-path: polygon(0 0, calc(100% - 12px) 0, 100% 12px, 100% 100%, 12px 100%, 0 calc(100% - 12px));
        }
        .btn-submit:hover { background: #ffb347; transform: translateY(-2px); }
    </style>
</head>
<body>
<div class="card">
    <a href="index.php" class="back-link">← Kembali</a>
    <div class="form-header">
        <span>&#9632; Form Edit</span>
        <h2>Edit Data Mahasiswa</h2>
        <div class="id-badge">ID: <?= $id ?> &nbsp;|&nbsp; NIM: <?= htmlspecialchars($data['nim']) ?></div>
    </div>

    <?= $pesan ?>

    <?php if (!empty($error)): ?>
    <div class="error-list">
        <p>Perbaiki kesalahan berikut:</p>
        <ul>
            <?php foreach ($error as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="edit.php?id=<?= $id ?>">
        <div class="form-group">
            <label for="nim">NIM</label>
            <input type="text" id="nim" name="nim" maxlength="20"
                   value="<?= htmlspecialchars($data['nim']) ?>">
        </div>
        <div class="form-group">
            <label for="nama">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" maxlength="100"
                   value="<?= htmlspecialchars($data['nama']) ?>">
        </div>
        <div class="form-group">
            <label for="jurusan">Jurusan / Program Studi</label>
            <input type="text" id="jurusan" name="jurusan" maxlength="100"
                   value="<?= htmlspecialchars($data['jurusan']) ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="semester">Semester</label>
                <input type="number" id="semester" name="semester" min="1" max="14"
                       value="<?= htmlspecialchars($data['semester']) ?>">
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <div class="select-wrap">
                    <select id="jenis_kelamin" name="jenis_kelamin">
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" <?= $data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= $data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" class="btn-submit">Update Data →</button>
    </form>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>
