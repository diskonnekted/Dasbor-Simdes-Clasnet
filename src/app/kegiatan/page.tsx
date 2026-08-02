'use client';

import React, { useEffect, useState, Suspense } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import Link from 'next/link';
import { Calendar, User, Tag, ChevronLeft, ArrowLeft, Image as ImageIcon, MapPin, Search } from 'lucide-react';

interface BeritaItem {
  id: number;
  judul: string;
  isi: string;
  gambar: string;
  dibuat_pada: string;
  author: string;
  tags: string;
}

interface RelatedDesa {
  id: number;
  nama_desa: string;
  nama_kecamatan: string;
}

interface PhotoGallery {
  id: number;
  path: string;
}

function KegiatanContent() {
  const searchParams = useSearchParams();
  const router = useRouter();
  const newsId = searchParams.get('id');

  // List States
  const [list, setList] = useState<BeritaItem[]>([]);
  const [q, setQ] = useState(searchParams.get('q') || '');
  const [page, setPage] = useState(parseInt(searchParams.get('page') || '1'));
  const [totalPages, setTotalPages] = useState(1);
  const [loadingList, setLoadingList] = useState(true);

  // Detail States
  const [post, setPost] = useState<BeritaItem | null>(null);
  const [relatedDesa, setRelatedDesa] = useState<RelatedDesa[]>([]);
  const [gallery, setGallery] = useState<PhotoGallery[]>([]);
  const [loadingDetail, setLoadingDetail] = useState(false);
  const [errorDetail, setErrorDetail] = useState('');
  const [activeLightboxImage, setActiveLightboxImage] = useState<string | null>(null);

  // Load news list
  const fetchList = async () => {
    setLoadingList(true);
    try {
      const res = await fetch(`/api/kegiatan?q=${encodeURIComponent(q)}&page=${page}`);
      const data = await res.json();
      if (data.success) {
        setList(data.items);
        setTotalPages(data.pagination.totalPages);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoadingList(false);
    }
  };

  // Load single news detail
  const fetchDetail = async (id: string) => {
    setLoadingDetail(true);
    setErrorDetail('');
    try {
      const res = await fetch(`/api/kegiatan/${id}`);
      const data = await res.json();
      if (data.success) {
        setPost(data.post);
        setRelatedDesa(data.relatedDesaList);
        setGallery(data.gallery);
      } else {
        setErrorDetail(data.error || 'Berita tidak ditemukan');
      }
    } catch (err) {
      setErrorDetail('Terjadi kesalahan memuat berita.');
    } finally {
      setLoadingDetail(false);
    }
  };

  useEffect(() => {
    if (newsId) {
      fetchDetail(newsId);
    } else {
      fetchList();
    }
  }, [newsId, page]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setPage(1);
    router.push(`/kegiatan?q=${encodeURIComponent(q)}`);
    fetchList();
  };

  // RENDER SINGLE NEWS DETAIL VIEW
  if (newsId) {
    if (loadingDetail) {
      return (
        <div className="max-w-3xl mx-auto px-4 py-16 text-center">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
          <p className="text-gray-500 text-sm">Memuat konten berita...</p>
        </div>
      );
    }

    if (errorDetail || !post) {
      return (
        <div className="max-w-3xl mx-auto px-4 py-16 text-center">
          <div className="bg-white rounded-2xl shadow border border-gray-100 p-8">
            <h2 className="text-xl font-bold text-gray-900 mb-2">Gagal memuat berita</h2>
            <p className="text-sm text-gray-500 mb-6">{errorDetail || 'Berita tidak ditemukan'}</p>
            <button
              onClick={() => router.push('/kegiatan')}
              className="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition"
            >
              <ArrowLeft className="w-4 h-4" /> Kembali ke Kegiatan
            </button>
          </div>
        </div>
      );
    }

    return (
      <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <button
          onClick={() => router.push('/kegiatan')}
          className="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 hover:text-gray-900 mb-6 transition"
        >
          <ArrowLeft className="w-4 h-4" /> Kembali ke Daftar Kegiatan
        </button>

        <article className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
          {/* Main Image */}
          {post.gambar && (
            <div
              className="w-full aspect-video cursor-pointer relative group overflow-hidden bg-gray-50 border-b"
              onClick={() => setActiveLightboxImage(post.gambar)}
            >
              <img src={post.gambar} alt={post.judul} className="w-full h-full object-cover group-hover:scale-101 transition duration-300" />
            </div>
          )}

          {/* Details */}
          <div className="p-6 sm:p-8 space-y-6">
            <div className="space-y-4">
              <h1 className="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
                {post.judul}
              </h1>

              {/* Meta */}
              <div className="flex flex-wrap items-center gap-4 text-xs text-gray-400 font-semibold border-b pb-4 border-gray-50">
                <span className="flex items-center gap-1">
                  <Calendar className="w-3.5 h-3.5" />
                  {new Date(post.dibuat_pada).toLocaleDateString('id-ID', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric',
                  })}
                </span>
                <span className="flex items-center gap-1">
                  <User className="w-3.5 h-3.5" />
                  {post.author || 'Clasnet Group'}
                </span>
              </div>
            </div>

            {/* Content body */}
            <div
              className="prose max-w-none text-gray-700 text-sm sm:text-base leading-relaxed space-y-4"
              dangerouslySetInnerHTML={{ __html: post.isi }}
            ></div>

            {/* Tags */}
            {post.tags && (
              <div className="flex flex-wrap gap-2 pt-4">
                {post.tags
                  .replace(/[,#]/g, ' ')
                  .split(' ')
                  .filter((t) => t.trim() !== '')
                  .map((tag) => (
                    <span
                      key={tag}
                      className="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-600"
                    >
                      <Tag className="w-3 h-3" />
                      #{tag}
                    </span>
                  ))}
              </div>
            )}

            {/* Related Villages */}
            {relatedDesa.length > 0 && (
              <div className="pt-6 border-t border-gray-100">
                <h3 className="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3 flex items-center gap-1.5">
                  <MapPin className="w-4 h-4 text-rose-500" />
                  Desa Terkait Kegiatan Ini
                </h3>
                <div className="flex flex-wrap gap-2">
                  {relatedDesa.map((d) => (
                    <Link
                      key={d.id}
                      href={`/desa?q=${encodeURIComponent(d.nama_desa)}`}
                      className="text-xs font-semibold px-3 py-1.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg border border-gray-100 transition"
                    >
                      {d.nama_desa} ({d.nama_kecamatan})
                    </Link>
                  ))}
                </div>
              </div>
            )}

            {/* Gallery Photos */}
            {gallery.length > 0 && (
              <div className="pt-6 border-t border-gray-100">
                <h3 className="text-xs uppercase tracking-wider text-gray-400 font-bold mb-3 flex items-center gap-1.5">
                  <ImageIcon className="w-4 h-4 text-emerald-500" />
                  Galeri Foto Dokumentasi
                </h3>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3">
                  {gallery.map((g) => (
                    <div
                      key={g.id}
                      onClick={() => setActiveLightboxImage(g.path)}
                      className="aspect-square bg-gray-50 rounded-xl overflow-hidden border cursor-pointer hover:opacity-90 group relative"
                    >
                      <img src={g.path} alt="Dokumentasi" className="w-full h-full object-cover group-hover:scale-105 transition" />
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        </article>

        {/* Lightbox */}
        {activeLightboxImage && (
          <div
            onClick={() => setActiveLightboxImage(null)}
            className="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4 cursor-zoom-out backdrop-blur-sm"
          >
            <div className="relative max-w-5xl max-h-[85vh]">
              <img src={activeLightboxImage} alt="Preview" className="rounded-xl object-contain shadow-2xl" />
            </div>
          </div>
        )}
      </div>
    );
  }

  // RENDER NEWS LIST VIEW
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      {/* Banner & Search bar */}
      <div className="premium-gradient rounded-3xl text-white premium-shadow p-6 sm:p-8 relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div className="relative">
          <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight">Kegiatan & Berita SID</h1>
          <p className="text-sm mt-1 opacity-90">Dokumentasi pengembangan, sosialiasi, dan pendampingan desa digital.</p>
        </div>
        <form onSubmit={handleSearchSubmit} className="relative w-full md:w-80 z-10">
          <Search className="absolute left-3.5 top-3.5 h-4.5 w-4.5 text-gray-400" />
          <input
            type="text"
            placeholder="Cari berita kegiatan..."
            value={q}
            onChange={(e) => setQ(e.target.value)}
            className="w-full pl-10 pr-20 py-2.5 rounded-xl border-0 bg-white/10 text-white placeholder-white/60 ring-1 ring-white/20 focus:ring-2 focus:ring-white/50 focus:bg-white/20 sm:text-sm transition-all duration-300 backdrop-blur-xs outline-none font-semibold"
          />
          <button type="submit" className="absolute right-1.5 top-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-white rounded-lg hover:bg-blue-50 transition shadow-xs cursor-pointer">
            Cari
          </button>
        </form>
      </div>

      {loadingList ? (
        <div className="text-center py-16">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
          <p className="text-gray-500 text-sm">Memuat daftar berita...</p>
        </div>
      ) : list.length > 0 ? (
        <div className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {list.map((item) => (
              <Link
                key={item.id}
                href={`/kegiatan?id=${item.id}`}
                className="bg-white rounded-3xl border border-gray-100 overflow-hidden premium-shadow hover:premium-shadow-hover hover:-translate-y-1 transition-all duration-300 flex flex-col group"
              >
                {/* Image */}
                <div className="aspect-video bg-gray-50 relative overflow-hidden shrink-0 border-b border-gray-50">
                  {item.gambar ? (
                    <img src={item.gambar} alt={item.judul} className="w-full h-full object-cover group-hover:scale-102 transition duration-500" />
                  ) : (
                    <div className="w-full h-full bg-gradient-to-br from-indigo-50/30 to-blue-50/30 flex items-center justify-center text-indigo-500">
                      <ImageIcon className="w-12 h-12 opacity-40 animate-pulse" />
                    </div>
                  )}
                </div>

                {/* Body */}
                <div className="p-6 flex-grow flex flex-col justify-between gap-4">
                  <div className="space-y-2">
                    <div className="text-[10px] font-bold text-gray-400 flex items-center justify-between uppercase tracking-wider">
                      <span>{new Date(item.dibuat_pada).toLocaleDateString('id-ID')}</span>
                      <span>Oleh: {item.author || 'Clasnet'}</span>
                    </div>
                    <h3 className="font-extrabold text-gray-900 group-hover:text-indigo-600 transition leading-snug line-clamp-2">
                      {item.judul}
                    </h3>
                    <p className="text-xs text-gray-500 leading-relaxed line-clamp-3">
                      {item.isi.replace(/<[^>]*>/g, '')}
                    </p>
                  </div>

                  {item.tags && (
                    <div className="flex flex-wrap gap-1.5 pt-2">
                      {item.tags
                        .replace(/[,#]/g, ' ')
                        .split(' ')
                        .filter((t) => t.trim() !== '')
                        .slice(0, 3)
                        .map((tag) => (
                          <span key={tag} className="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">
                            #{tag}
                          </span>
                        ))}
                    </div>
                  )}
                </div>
              </Link>
            ))}
          </div>

          {/* Pagination */}
          {totalPages > 1 && (
            <div className="flex justify-center items-center gap-2 pt-6">
              <button
                onClick={() => setPage((p) => Math.max(1, p - 1))}
                disabled={page === 1}
                className="p-2 border rounded-xl hover:bg-gray-50 disabled:opacity-50 transition"
              >
                <ChevronLeft className="w-5 h-5" />
              </button>
              <span className="text-sm font-semibold text-gray-900 px-4">
                Halaman {page} dari {totalPages}
              </span>
              <button
                onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
                disabled={page === totalPages}
                className="p-2 border rounded-xl hover:bg-gray-50 disabled:opacity-50 transition"
              >
                <ChevronLeft className="w-5 h-5 rotate-180" />
              </button>
            </div>
          )}
        </div>
      ) : (
        <div className="text-center py-16 bg-white border rounded-2xl">
          <p className="text-gray-500 font-semibold">Belum ada berita kegiatan yang dipublikasikan.</p>
        </div>
      )}
    </div>
  );
}

export default function KegiatanPage() {
  return (
    <Suspense fallback={
      <div className="max-w-7xl mx-auto px-4 py-16 text-center">
        <p className="text-gray-500 text-sm">Memuat halaman kegiatan...</p>
      </div>
    }>
      <KegiatanContent />
    </Suspense>
  );
}
