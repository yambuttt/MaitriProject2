<?php
/**
 * MaitriProject cPanel Symlink Helper
 * 
 * Script ini digunakan untuk menghapus/me-rename folder 'build' lama di public_html
 * dan membuat symlink ke folder 'MP2/public/build' secara otomatis.
 */

header('Content-Type: text/html; charset=utf-8');
echo "<div style='font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 25px; border-radius: 16px; background: #0f172a; color: #f8fafc; border: 1px solid #334155; box-shadow: 0 10px 30px rgba(0,0,0,0.5);'>";
echo "<h2 style='color: #8b5cf6; margin-top: 0;'>MaitriProject Symlink Creator</h2>";

// 1. Tentukan path target & shortcut
$homeDir = dirname(__DIR__); // /home/maitriproject
$target = $homeDir . '/MP2/public/build';
$shortcut = __DIR__ . '/build';

echo "<p style='font-size: 14px;'><strong>Home Directory:</strong> <code style='background: #1e293b; padding: 3px 6px; rounded: 6px; color: #38bdf8;'>$homeDir</code></p>";
echo "<p style='font-size: 14px;'><strong>Target (Source):</strong> <code style='background: #1e293b; padding: 3px 6px; rounded: 6px;'>$target</code></p>";
echo "<p style='font-size: 14px;'><strong>Shortcut (Link):</strong> <code style='background: #1e293b; padding: 3px 6px; rounded: 6px;'>$shortcut</code></p>";

// 2. Cek apakah target (MP2/public/build) ada
if (!is_dir($target)) {
    echo "<div style='padding: 12px; background: #991b1b; border: 1px solid #f87171; border-radius: 8px; font-size: 13px; color: #fef2f2; margin: 15px 0;'>";
    echo "❌ <strong>Error:</strong> Folder target <code style='background: rgba(0,0,0,0.2); padding: 2px 4px;'>$target</code> tidak ditemukan. Pastikan Anda sudah menjalankan <code style='background: rgba(0,0,0,0.2); padding: 2px 4px;'>npm run build</code> di folder MP2.";
    echo "</div>";
    echo "</div>";
    exit;
}

// 3. Rename folder build fisik lama jika ada di public_html
if (file_exists($shortcut)) {
    if (is_link($shortcut)) {
        echo "<p style='font-size: 13px; color: #fbbf24;'>⚠️ Shortcut <code style='background: #1e293b; padding: 2px 4px;'>build</code> sudah berupa symlink. Menghapus symlink lama...</p>";
        unlink($shortcut);
    } else if (is_dir($shortcut)) {
        $backupName = $shortcut . '_backup_' . date('Ymd_His');
        echo "<p style='font-size: 13px; color: #fbbf24;'>⚠️ Folder fisik <code style='background: #1e293b; padding: 2px 4px;'>build</code> terdeteksi. Membackup ke <code style='background: #1e293b; padding: 2px 4px;'>$backupName</code>...</p>";
        if (!rename($shortcut, $backupName)) {
            echo "<div style='padding: 12px; background: #991b1b; border-radius: 8px; font-size: 13px; color: #fef2f2;'>❌ Gagal membackup folder fisik build lama. Periksa perizinan file Anda.</div></div>";
            exit;
        }
    }
}

// 4. Buat Symlink
if (symlink($target, $shortcut)) {
    echo "<div style='padding: 15px; background: #065f46; border: 1px solid #34d399; border-radius: 8px; font-size: 14px; color: #ecfdf5; margin: 15px 0; font-weight: bold;'>";
    echo "🎉 Sukses! Symlink folder 'build' berhasil dibuat!";
    echo "</div>";
    echo "<p style='font-size: 13px; color: #94a3b8;'>Aset CSS/JS hasil compile Vite Anda sekarang otomatis sinkron antara folder <strong style='color: #fff;'>MP2</strong> dan website live di <strong style='color: #fff;'>public_html</strong>!</p>";
} else {
    echo "<div style='padding: 12px; background: #991b1b; border-radius: 8px; font-size: 13px; color: #fef2f2;'>❌ Gagal membuat symlink. Pastikan fungsi PHP <code style='background: rgba(0,0,0,0.2); padding: 2px 4px;'>symlink()</code> tidak dinonaktifkan oleh server hoster cPanel Anda.</div>";
}

echo "</div>";
