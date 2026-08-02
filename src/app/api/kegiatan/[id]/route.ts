import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export async function GET(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  const { id } = await params;
  const newsId = parseInt(id);

  try {
    const db = getDb();

    // 1. Fetch news article
    const [newsRows] = await db.query(
      'SELECT id, judul, isi, gambar, dibuat_pada, author, tags, related_desa FROM berita WHERE published = 1 AND id = ?',
      [newsId]
    ) as any[];

    if (newsRows.length === 0) {
      return NextResponse.json({ success: false, error: 'Berita tidak ditemukan' }, { status: 404 });
    }

    const post = newsRows[0];

    // 2. Fetch related villages
    let relatedDesaList: any[] = [];
    if (post.related_desa) {
      const ids = post.related_desa.split(',').map((x: string) => parseInt(x.trim())).filter((x: number) => !isNaN(x));
      if (ids.length > 0) {
        const [desaRows] = await db.query(
          `SELECT id, nama_desa, nama_kecamatan FROM desa WHERE id IN (${ids.join(',')}) ORDER BY nama_kecamatan, nama_desa`
        ) as any[];
        relatedDesaList = desaRows;
      }
    }

    // 3. Fetch gallery photos
    // Check if table berita_foto exists first
    await db.query(`
      CREATE TABLE IF NOT EXISTS berita_foto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        berita_id INT NOT NULL,
        path VARCHAR(255) NOT NULL,
        urutan INT NOT NULL DEFAULT 0,
        FOREIGN KEY (berita_id) REFERENCES berita(id) ON DELETE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    `);

    const [galleryRows] = await db.query(
      'SELECT id, path FROM berita_foto WHERE berita_id = ? ORDER BY urutan ASC, id ASC',
      [newsId]
    ) as any[];

    return NextResponse.json({
      success: true,
      post,
      relatedDesaList,
      gallery: galleryRows
    });
  } catch (error: any) {
    console.error('Error fetching single news:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
