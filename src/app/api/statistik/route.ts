import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET() {
  try {
    const db = getDb();

    // 1. Total desa
    const [totalRows] = await db.query('SELECT COUNT(*) AS c FROM desa') as any[];
    const totalDesa = totalRows[0]?.c || 0;

    // 2. Website statistics
    const [webRows] = await db.query(`
      SELECT 
        SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) <> '' THEN 1 ELSE 0 END) AS w,
        SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) = '' THEN 1 ELSE 0 END) AS nw
      FROM desa
    `) as any[];
    const withWebsite = parseInt(webRows[0]?.w || '0');
    const withoutWebsite = parseInt(webRows[0]?.nw || '0');

    // 3. Website status statistics
    const [statusRows] = await db.query(`
      SELECT 
        SUM(CASE WHEN LOWER(TRIM(COALESCE(website_status,'')))='active' THEN 1 ELSE 0 END) AS a, 
        SUM(CASE WHEN LOWER(TRIM(COALESCE(website_status,'')))='inactive' THEN 1 ELSE 0 END) AS i, 
        SUM(CASE WHEN website_status IS NULL OR TRIM(COALESCE(website_status,''))='' THEN 1 ELSE 0 END) AS u 
      FROM desa
    `) as any[];
    const active = parseInt(statusRows[0]?.a || '0');
    const inactive = parseInt(statusRows[0]?.i || '0');
    const unknown = parseInt(statusRows[0]?.u || '0');

    // 4. District adoptions
    const [kecRows] = await db.query(`
      SELECT nama_kecamatan AS kec,
        SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) <> '' THEN 1 ELSE 0 END) AS sid,
        SUM(CASE WHEN TRIM(COALESCE(alamat_website,'')) = '' THEN 1 ELSE 0 END) AS nonsid,
        COUNT(*) AS total
      FROM desa 
      GROUP BY nama_kecamatan 
      ORDER BY nama_kecamatan
    `) as any[];

    const kecamatanStats = kecRows.map((row: any) => ({
      kec: row.kec || 'Tidak diketahui',
      sid: parseInt(row.sid || '0'),
      nonsid: parseInt(row.nonsid || '0'),
      total: parseInt(row.total || '0')
    }));

    // 5. Database penduduk statistics
    const [dbPendRows] = await db.query(`
      SELECT 
        SUM(CASE WHEN UPPER(COALESCE(db_penduduk,''))='SUDAH ADA' THEN 1 ELSE 0 END) AS sudah,
        SUM(CASE WHEN UPPER(COALESCE(db_penduduk,''))='BELUM ADA' THEN 1 ELSE 0 END) AS belum
      FROM desa
    `) as any[];
    const dbPendudukSudah = parseInt(dbPendRows[0]?.sudah || '0');
    const dbPendudukBelum = parseInt(dbPendRows[0]?.belum || '0');

    return NextResponse.json({
      success: true,
      stats: {
        totalDesa,
        withWebsite,
        withoutWebsite,
        websitePct: totalDesa > 0 ? Math.round((withWebsite / totalDesa) * 100) : 0,
        active,
        inactive,
        unknown,
        dbPendudukSudah,
        dbPendudukBelum,
        dbPendudukTotal: dbPendudukSudah + dbPendudukBelum,
        dbPct: (dbPendudukSudah + dbPendudukBelum) > 0 ? Math.round((dbPendudukSudah / (dbPendudukSudah + dbPendudukBelum)) * 100) : 0,
        kecamatanStats
      }
    });
  } catch (error: any) {
    console.error('Error fetching statistics:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
