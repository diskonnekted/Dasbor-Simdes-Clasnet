import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';
import { isAdminAuthenticated } from '@/lib/auth';
import { writeFile } from 'fs/promises';
import { join } from 'path';

export const dynamic = 'force-dynamic';

export async function GET() {
  if (!await isAdminAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  try {
    const db = getDb();
    const [rows] = await db.query(
      'SELECT id, judul, deskripsi, gambar, published, dibuat_pada FROM inovasi ORDER BY dibuat_pada DESC'
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
    const formData = await request.formData();
    const judul = (formData.get('judul') as string || '').trim();
    const deskripsi = (formData.get('deskripsi') as string || '').trim();
    const published = formData.get('published') === 'false' ? 0 : 1;
    const file = formData.get('gambar') as File | null;

    if (!judul) return NextResponse.json({ error: 'Judul inovasi wajib diisi.' }, { status: 400 });

    let gambarPath = '';

    if (file && file.size > 0) {
      const bytes = await file.arrayBuffer();
      const buffer = Buffer.from(bytes);

      const uniqueFilename = `${Date.now()}_${file.name.replace(/\s+/g, '_')}`;
      const uploadDir = join(process.cwd(), 'public', 'uploads');
      const filePath = join(uploadDir, uniqueFilename);

      await writeFile(filePath, buffer);
      gambarPath = `/uploads/${uniqueFilename}`;
    }

    const db = getDb();
    const [result] = await db.query(
      'INSERT INTO inovasi (judul, deskripsi, gambar, published, dibuat_pada) VALUES (?, ?, ?, ?, NOW())',
      [judul, deskripsi, gambarPath, published]
    ) as any[];

    return NextResponse.json({ success: true, insertId: result.insertId });
  } catch (error: any) {
    console.error('Error in innovation post:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
