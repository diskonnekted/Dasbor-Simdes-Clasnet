import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET(request: Request) {
  console.log('LOG: API peta/berita triggered with url:', request.url);
  try {
    const { searchParams } = new URL(request.url);
    const desaParam = searchParams.get('desa') || '';
    const kecParam = searchParams.get('kec') || searchParams.get('kecamatan') || '';

    console.log('LOG: Query params:', { desaParam, kecParam });

    const cleanDesa = desaParam.replace(/^desa\s+/i, '').trim();
    if (!cleanDesa) {
      console.log('LOG: empty cleanDesa, returning empty items');
      return NextResponse.json({ items: [] });
    }

    const db = getDb();

    // 1. Find Desa ID and full names from DB
    let desaId = 0;
    let cleanKec = '';
    let findDesaSql = 'SELECT id, nama_kecamatan FROM desa WHERE nama_desa LIKE ?';
    const findDesaParams: any[] = [`%${cleanDesa}%`];

    if (kecParam) {
      cleanKec = kecParam.replace(/^(kec\.?|kecamatan)\s*/i, '').trim();
      findDesaSql += ' AND nama_kecamatan LIKE ?';
      findDesaParams.push(`%${cleanKec}%`);
    }
    findDesaSql += ' LIMIT 1';

    const [desaRows] = await db.query(findDesaSql, findDesaParams) as any[];
    if (desaRows.length > 0) {
      desaId = desaRows[0].id;
    }

    // 2. Check if village name is ambiguous (occurs in multiple subdistricts)
    let isAmbiguous = false;
    const [checkRows] = await db.query(
      'SELECT COUNT(DISTINCT nama_kecamatan) as cnt FROM desa WHERE nama_desa LIKE ?',
      [`%${cleanDesa}%`]
    ) as any[];
    if (checkRows.length > 0 && checkRows[0].cnt > 1) {
      isAmbiguous = true;
    }

    // 3. Query news
    let sql = 'SELECT id, judul, isi, gambar, dibuat_pada, author FROM berita WHERE published = 1 AND (';
    const queryParams: any[] = [];

    // Text match group (searching village name in title/body)
    sql += '((judul LIKE ? OR isi LIKE ?)';
    queryParams.push(`%${cleanDesa}%`, `%${cleanDesa}%`);

    if (isAmbiguous && cleanKec) {
      sql += ' AND (judul LIKE ? OR isi LIKE ?)';
      queryParams.push(`%${cleanKec}%`, `%${cleanKec}%`);
    }
    sql += ')';

    // ID match (related_desa column contains the ID)
    if (desaId > 0) {
      sql += ' OR FIND_IN_SET(?, related_desa) > 0';
      queryParams.push(desaId);
    }
    sql += ') ORDER BY dibuat_pada DESC LIMIT 5';

    const [newsRows] = await db.query(sql, queryParams) as any[];

    const items = newsRows.map((b: any) => {
      const plain = (b.isi || '').replace(/<[^>]*>/g, '');
      const excerpt = plain.length <= 120 ? plain : plain.substring(0, 120) + '…';
      return {
        id: b.id,
        judul: b.judul || '',
        dibuat_pada: b.dibuat_pada || '',
        author: b.author || '',
        gambar: b.gambar ? (b.gambar.startsWith('/') ? b.gambar : `/${b.gambar}`) : '',
        excerpt
      };
    });

    return NextResponse.json({ items });
  } catch (error: any) {
    console.error('Error fetching map news:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
