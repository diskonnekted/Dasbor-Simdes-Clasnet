import { NextResponse } from 'next/server';
import { getDb } from '@/lib/db';
import { isAdminAuthenticated } from '@/lib/auth';
import { writeFile } from 'fs/promises';
import { join } from 'path';

export async function PUT(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  if (!await isAdminAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const { id } = await params;
  const newsId = parseInt(id);

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

    const db = getDb();
    
    // Check if we already have an image
    const [existing] = await db.query('SELECT gambar FROM berita WHERE id = ?', [newsId]) as any[];
    let gambarPath = existing[0]?.gambar || '';

    if (file && file.size > 0) {
      const bytes = await file.arrayBuffer();
      const buffer = Buffer.from(bytes);

      const uniqueFilename = `${Date.now()}_${file.name.replace(/\s+/g, '_')}`;
      const uploadDir = join(process.cwd(), 'public', 'uploads');
      const filePath = join(uploadDir, uniqueFilename);

      await writeFile(filePath, buffer);
      gambarPath = `/uploads/${uniqueFilename}`;
    }

    await db.query(
      `UPDATE berita 
       SET judul = ?, isi = ?, gambar = ?, author = ?, tags = ?, related_desa = ?, published = ? 
       WHERE id = ?`,
      [judul, isi, gambarPath, author, tags, related_desa, published, newsId]
    );

    return NextResponse.json({ success: true });
  } catch (error: any) {
    console.error('Error in news update:', error);
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}

export async function DELETE(
  request: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  if (!await isAdminAuthenticated()) {
    return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
  }

  const { id } = await params;
  const newsId = parseInt(id);

  try {
    const db = getDb();
    await db.query('DELETE FROM berita WHERE id = ?', [newsId]);
    return NextResponse.json({ success: true });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
