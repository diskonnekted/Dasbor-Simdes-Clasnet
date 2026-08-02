import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';

export const dynamic = 'force-dynamic';

export async function GET() {
  try {
    const db = getDb();

    // Fetch all published news content to compute stars
    const [newsRows] = await db.query(
      'SELECT judul, isi FROM berita WHERE published = 1'
    ) as any[];

    let newsContent = '';
    for (const row of newsRows) {
      newsContent += ' ' + (row.judul || '') + ' ' + (row.isi || '');
    }
    // strip HTML tags
    newsContent = newsContent.replace(/<[^>]*>/g, '').toLowerCase();

    // Fetch all desa
    const [desaRows] = await db.query(
      'SELECT id, nama_kecamatan, nama_desa, alamat_website, jumlah_penduduk, db_penduduk, developer FROM desa'
    ) as any[];

    const desaData: Record<string, any> = {};

    for (const r of desaRows) {
      const desaRaw = (r.nama_desa || '').trim();
      const kecRaw = (r.nama_kecamatan || '').trim();

      // Normalize village and district names
      const desaNorm = desaRaw
        .toLowerCase()
        .replace(/\s+/g, ' ')
        .replace(/^desa\s+/i, '')
        .trim();

      const kecNorm = kecRaw
        .toLowerCase()
        .replace(/\s+/g, ' ')
        .replace(/^(kecamatan|kec\.?)\s+/i, '')
        .trim();

      let stars = 0;
      const hasWebsite = !!(r.alamat_website && r.alamat_website.trim() !== '');
      const hasDb = r.db_penduduk && r.db_penduduk.toUpperCase() === 'SUDAH ADA';
      let hasNews = false;

      if (desaNorm !== '') {
        // Regex word boundary equivalent
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

      r.stars = stars;
      const key = `${kecNorm}|${desaNorm}`;
      if (desaNorm !== '') {
        desaData[key] = r;
      }
    }

    return NextResponse.json({ success: true, desaData });
  } catch (error: any) {
    console.error('Error fetching peta data:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
