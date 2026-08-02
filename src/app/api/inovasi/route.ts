import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET() {
  try {
    const db = getDb();
    
    // Ensure table exists
    await db.query(`
      CREATE TABLE IF NOT EXISTS inovasi (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        deskripsi TEXT DEFAULT NULL,
        gambar VARCHAR(255) DEFAULT NULL,
        published TINYINT(1) NOT NULL DEFAULT 1,
        dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        diperbarui_pada DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    `);

    const [rows] = await db.query(
      'SELECT id, judul, deskripsi, gambar, dibuat_pada FROM inovasi WHERE published = 1 ORDER BY dibuat_pada DESC'
    ) as any[];

    let items = rows.map((r: any) => ({
      id: r.id,
      gambar: r.gambar ? (r.gambar.startsWith('/') ? r.gambar : `/${r.gambar}`) : '',
      judul: r.judul || '',
      deskripsi: r.deskripsi || '',
      dibuat_pada: r.dibuat_pada || ''
    }));

    if (items.length === 0) {
      items = [
        {
          id: 1,
          gambar: '/images/an1.jpg',
          judul: 'Integrasi LoRa untuk Desa',
          deskripsi: 'Jaringan LoRa untuk konektivitas jarak jauh berdaya rendah, menghubungkan sensor di wilayah desa.'
        },
        {
          id: 2,
          gambar: '/images/an2.jpg',
          judul: 'Sensor IoT Lingkungan',
          deskripsi: 'Pemantauan kualitas udara, banjir, dan cuaca lokal untuk respons cepat dan mitigasi risiko.'
        },
        {
          id: 3,
          gambar: '/images/an3.jpg',
          judul: 'Dashboard Kinerja Desa',
          deskripsi: 'Visualisasi data pelayanan, statistik penduduk, dan aktivitas desa dalam satu dashboard.'
        },
        {
          id: 4,
          gambar: '/images/an4.jpg',
          judul: 'Anjungan Pelayanan Mandiri',
          deskripsi: 'Terminal layanan warga mandiri, integrasi cetak dokumen, antrean, dan autentikasi aman.'
        }
      ];
    }

    return NextResponse.json({ success: true, items });
  } catch (error: any) {
    console.error('Error fetching inovasi:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
