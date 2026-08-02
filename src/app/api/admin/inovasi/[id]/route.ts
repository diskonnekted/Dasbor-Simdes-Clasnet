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
  const inovasiId = parseInt(id);

  try {
    const formData = await request.formData();
    const judul = (formData.get('judul') as string || '').trim();
    const deskripsi = (formData.get('deskripsi') as string || '').trim();
    const published = formData.get('published') === 'false' ? 0 : 1;
    const file = formData.get('gambar') as File | null;

    if (!judul) return NextResponse.json({ error: 'Judul inovasi wajib diisi.' }, { status: 400 });

    const db = getDb();
    const [existing] = await db.query('SELECT gambar FROM inovasi WHERE id = ?', [inovasiId]) as any[];
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
      `UPDATE inovasi 
       SET judul = ?, deskripsi = ?, gambar = ?, published = ? 
       WHERE id = ?`,
      [judul, deskripsi, gambarPath, published, inovasiId]
    );

    return NextResponse.json({ success: true });
  } catch (error: any) {
    console.error('Error in innovation update:', error);
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
  const inovasiId = parseInt(id);

  try {
    const db = getDb();
    await db.query('DELETE FROM inovasi WHERE id = ?', [inovasiId]);
    return NextResponse.json({ success: true });
  } catch (error: any) {
    return NextResponse.json({ success: false, error: error.message }, { status: 500 });
  }
}
