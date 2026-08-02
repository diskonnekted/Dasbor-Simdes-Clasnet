import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET(request: Request) {
  try {
    const { searchParams } = new URL(request.url);
    const q = searchParams.get('q') || '';
    const kec = searchParams.get('kec') || '';
    const sid = searchParams.get('sid') || '';
    const dbf = searchParams.get('db') || '';
    const page = Math.max(1, parseInt(searchParams.get('page') || '1'));
    const perPage = Math.max(1, Math.min(200, parseInt(searchParams.get('per') || '25')));

    const db = getDb();

    // Fetch distinct kecamatan list for filter dropdown
    const [kecamatanRows] = await db.query(
      "SELECT DISTINCT nama_kecamatan FROM desa WHERE TRIM(COALESCE(nama_kecamatan,'')) <> '' ORDER BY nama_kecamatan ASC"
    ) as any[];
    const kecamatanList = kecamatanRows.map((r: any) => r.nama_kecamatan);

    // Build query conditions
    const where: string[] = [];
    const params: any[] = [];

    if (q) {
      where.push('nama_desa LIKE ?');
      params.push(`%${q}%`);
    }
    if (kec) {
      where.push('nama_kecamatan = ?');
      params.push(kec);
    }
    if (sid === 'with') {
      where.push("TRIM(COALESCE(alamat_website,'')) <> ''");
    } else if (sid === 'without') {
      where.push("TRIM(COALESCE(alamat_website,'')) = ''");
    }
    if (dbf === 'sudah') {
      where.push("UPPER(TRIM(COALESCE(db_penduduk,''))) = 'SUDAH ADA'");
    } else if (dbf === 'belum') {
      where.push("(UPPER(TRIM(COALESCE(db_penduduk,''))) = 'BELUM ADA' OR TRIM(COALESCE(db_penduduk,'')) = '')");
    }

    const whereClause = where.length > 0 ? ` WHERE ${where.join(' AND ')}` : '';

    // Count query
    const countSql = `SELECT COUNT(*) AS total FROM desa${whereClause}`;
    const [countRows] = await db.query(countSql, params) as any[];
    const totalRows = countRows[0]?.total || 0;

    const totalPages = Math.ceil(totalRows / perPage);
    const offset = (page - 1) * perPage;

    // Fetch paginated rows
    const dataSql = `
      SELECT id, nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, developer 
      FROM desa${whereClause} 
      ORDER BY nama_kecamatan, nama_desa 
      LIMIT ? OFFSET ?
    `;
    const [rows] = await db.query(dataSql, [...params, perPage, offset]) as any[];

    // Fetch published news content to compute stars dynamically
    const [newsRows] = await db.query('SELECT judul, isi FROM berita WHERE published = 1') as any[];
    let newsContent = '';
    for (const row of newsRows) {
      newsContent += ' ' + (row.judul || '') + ' ' + (row.isi || '');
    }
    newsContent = newsContent.replace(/<[^>]*>/g, '').toLowerCase();

    // Map stars to rows
    const mappedRows = rows.map((r: any) => {
      const desaNorm = (r.nama_desa || '').toLowerCase().replace(/\s+/g, ' ').replace(/^desa\s+/i, '').trim();
      let stars = 0;
      const hasWebsite = !!(r.alamat_website && r.alamat_website.trim() !== '');
      const hasDb = r.db_penduduk && r.db_penduduk.toUpperCase() === 'SUDAH ADA';
      let hasNews = false;

      if (desaNorm !== '') {
        const escapedDesa = desaNorm.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
        const regex = new RegExp('\\b' + escapedDesa + '\\b', 'i');
        if (regex.test(newsContent)) {
          hasNews = true;
        }
      }

      if (hasWebsite) {
        stars = 1;
        if (hasDb) {
          stars = 2;
          if (hasNews) {
            stars = 3;
          }
        }
      }

      return {
        ...r,
        stars
      };
    });

    return NextResponse.json({
      success: true,
      data: mappedRows,
      pagination: {
        page,
        perPage,
        totalRows,
        totalPages
      },
      kecamatanList
    });
  } catch (error: any) {
    console.error('Error fetching villages:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
