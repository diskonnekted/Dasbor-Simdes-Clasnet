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
      'SELECT id, judul, isi, gambar, dibuat_pada, published, author, tags, related_desa FROM berita ORDER BY dibuat_pada DESC'
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
    const isi = (formData.get('isi') as string || '').trim();
    const author = (formData.get('author') as string || 'Clasnet Group').trim();
    const tags = (formData.get('tags') as string || '').trim();
    const related_desa = (formData.get('related_desa') as string || '').trim();
    const published = formData.get('published') === 'false' ? 0 : 1;
    const file = formData.get('gambar') as File | null;

    if (!judul) return NextResponse.json({ error: 'Judul berita wajib diisi.' }, { status: 400 });
    if (!isi) return NextResponse.json({ error: 'Isi berita wajib diisi.' }, { status: 400 });

    let gambarPath = '';

    if (file && file.size > 0) {
      const bytes = await file.arrayBuffer();
      const buffer = Buffer.from(bytes);

      // Create a unique filename
      const uniqueFilename = `${Date.now()}_${file.name.replace(/\s+/g, '_')}`;
      const uploadDir = join(process.cwd(), 'public', 'uploads');
      const filePath = join(uploadDir, uniqueFilename);

      await writeFile(filePath, buffer);
      gambarPath = `/uploads/${uniqueFilename}`;
    }

    const db = getDb();
    const [result] = await db.query(
      'INSERT INTO berita (judul, isi, gambar, author, tags, related_desa, published, dibuat_pada) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
      [judul, isi, gambarPath, author, tags, related_desa, published]
    ) as any[];

    return NextResponse.json({ success: true, insertId: result.insertId });
  } catch (error: any) {
    console.error('Error in news post:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
