import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';
import { isAdminAuthenticated } from '@/lib/auth';

export const dynamic = 'force-dynamic';

export async function GET() {
  if (!await isAdminAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  try {
    const db = getDb();
    const [rows] = await db.query(
      'SELECT id, nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer FROM desa ORDER BY nama_kecamatan, nama_desa'
    ) as any[];

    return NextResponse.json({ success: true, data: rows });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function POST(request: Request) {
  if (!await isAdminAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  try {
    const body = await request.json();
    const nama_kecamatan = (body.nama_kecamatan || '').trim();
    const nama_desa = (body.nama_desa || '').trim();
    const alamat_website = (body.alamat_website || '').trim();
    const db_penduduk = (body.db_penduduk || 'TIDAK DIKETAHUI').trim();
    const sosialisasi = (body.sosialisasi || '').trim().toLowerCase();
    const berita_desa = (body.berita_desa || '').trim().toLowerCase();
    const developer = (body.developer || '').trim().toLowerCase();
    const jumlah_penduduk = body.jumlah_penduduk === '' || body.jumlah_penduduk === null ? null : parseInt(body.jumlah_penduduk);
    const last_checked_at = body.last_checked_at || null;

    if (!nama_kecamatan) return NextResponse.json({ error: 'Nama kecamatan wajib diisi.' }, { status: 400 });
    if (!nama_desa) return NextResponse.json({ error: 'Nama desa wajib diisi.' }, { status: 400 });

    const db = getDb();

    // Check duplicate
    const [dupRows] = await db.query(
      'SELECT COUNT(*) AS c FROM desa WHERE nama_kecamatan = ? AND nama_desa = ?',
      [nama_kecamatan, nama_desa]
    ) as any[];
    if (dupRows[0]?.c > 0) {
      return NextResponse.json({ error: `Duplikat terdeteksi: kombinasi kecamatan "${nama_kecamatan}" dan desa "${nama_desa}" sudah ada.` }, { status: 400 });
    }

    const [result] = await db.query(
      `INSERT INTO desa (nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer) 
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [nama_kecamatan, nama_desa, alamat_website, last_checked_at, jumlah_penduduk, db_penduduk, sosialisasi, berita_desa, developer]
    ) as any[];

    return NextResponse.json({ success: true, insertId: result.insertId });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
