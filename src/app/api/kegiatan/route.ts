import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const q = searchParams.get('q') || '';
    const page = Math.max(1, parseInt(searchParams.get('page') || '1'));
    const perPage = Math.max(1, Math.min(100, parseInt(searchParams.get('per') || '9')));

    const db = getDb();

    // Ensure news table exists
    await db.query(`
      CREATE TABLE IF NOT EXISTS berita (
        id INT AUTO_INCREMENT PRIMARY KEY,
        judul VARCHAR(255) NOT NULL,
        isi TEXT NOT NULL,
        gambar VARCHAR(255) DEFAULT NULL,
        dibuat_pada DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        published TINYINT(1) NOT NULL DEFAULT 1,
        author VARCHAR(100) DEFAULT 'Clasnet Group',
        tags VARCHAR(255) DEFAULT NULL,
        related_desa VARCHAR(255) DEFAULT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    `);

    // Build conditions
    const where = ['published = 1'];
    const params: any[] = [];

    if (q) {
      where.push('(judul LIKE ? OR isi LIKE ? OR tags LIKE ?)');
      const like = `%${q}%`;
      params.push(like, like, like);
    }

    const whereClause = ` WHERE ${where.join(' AND ')}`;

    // Count
    const countSql = `SELECT COUNT(*) AS total FROM berita${whereClause}`;
    const [countRows] = await db.query(countSql, params) as any[];
    const totalItems = countRows[0]?.total || 0;

    const totalPages = Math.ceil(totalItems / perPage);
    const offset = (page - 1) * perPage;

    // Fetch articles
    const dataSql = `
      SELECT id, judul, isi, gambar, dibuat_pada, author, tags, related_desa 
      FROM berita${whereClause} 
      ORDER BY dibuat_pada DESC 
      LIMIT ? OFFSET ?
    `;
    const [rows] = await db.query(dataSql, [...params, perPage, offset]) as any[];

    const formattedRows = rows.map((r: any) => ({
      ...r,
      gambar: r.gambar ? (r.gambar.startsWith('/') ? r.gambar : `/${r.gambar}`) : null
    }));

    return NextResponse.json({
      success: true,
      items: formattedRows,
      pagination: {
        page,
        perPage,
        totalItems,
        totalPages
      }
    });
  } catch (error: any) {
    console.error('Error fetching kegiatan:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
