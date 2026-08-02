'use client';

import { ExternalLink, Palette, Layout, Globe, Monitor, Smartphone } from 'lucide-react';

interface ThemeItem {
  id: number;
  judul: string;
  tipe: string;
  deskripsi: string;
  url: string;
  image: string;
  features: string[];
  color: string;
  bgHex: string;
}

export default function TemaPage() {
  const themes: ThemeItem[] = [
    {
      id: 1,
      judul: 'Tema Santika',
      tipe: 'Portal Integrasi Multidesa',
      deskripsi: 'Desain dasbor terpusat untuk menampilkan infografis, sebaran peta wilayah, berita gabungan, dan status adopsi sistem informasi desa di tingkat kecamatan atau kabupaten.',
      url: 'https://portal.clasnet.id/',
      image: '/images/tema-santika.JPG',
      color: 'indigo',
      bgHex: 'bg-indigo-50 text-indigo-700 border-indigo-200',
      features: ['Peta Sebaran Interaktif', 'Agregasi Berita Desa', 'Statistik Adopsi Real-time', 'Keamanan Tingkat Tinggi']
    },
    {
      id: 2,
      judul: 'Tema Pradipa',
      tipe: 'Website Desa Premium & Pelayanan',
      deskripsi: 'Tema resmi untuk website profil desa dengan fokus penyajian berita kegiatan warga, transparansi dana APBD, kelembagaan desa, serta galeri produk UMKM lokal.',
      url: 'https://aribaya-banjarnegara.desa.id/',
      image: '/images/tema-pradipa.JPG',
      color: 'blue',
      bgHex: 'bg-blue-50 text-blue-700 border-blue-200',
      features: ['Slide Banner Interaktif', 'Widget APBDes Transparan', 'Galeri Foto Kegiatan', 'Integrasi Media Sosial']
    },
    {
      id: 3,
      judul: 'Tema Widya',
      tipe: 'Layanan Mandiri & Administrasi',
      deskripsi: 'Desain portal desa cerdas yang dioptimalkan untuk mempermudah pelayanan surat-menyurat mandiri warga, manajemen kependudukan, pengaduan online, serta informasi IDM.',
      url: 'https://trimulyo.sleman-desa.id/',
      image: '/images/tema-widya.JPG',
      color: 'emerald',
      bgHex: 'bg-emerald-50 text-emerald-700 border-emerald-200',
      features: ['Anjungan Surat Mandiri', 'Integrasi Peta Dusun', 'Statistik IDM Terintegrasi', 'Ramah Akses Mobile']
    },
    {
      id: 4,
      judul: 'Tema Serayu',
      tipe: 'Informasi Publik & Publikasi Daerah',
      deskripsi: 'Layout modern dengan penekanan pada transparansi informasi publik desa, profil lengkap perangkat desa, struktur organisasi, regulasi, dan arsip dokumen resmi.',
      url: 'https://merden-banjarnegara.desa.id/',
      image: '/images/tema-serayu.JPG',
      color: 'amber',
      bgHex: 'bg-amber-50 text-amber-700 border-amber-200',
      features: ['Download Dokumen Regulasi', 'Profil Perangkat Desa', 'Peta Batas Administrasi', 'SEO Friendly & Cepat']
    }
  ];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      {/* Banner */}
      <div className="premium-gradient rounded-2xl text-white premium-shadow p-6 sm:p-8 relative overflow-hidden flex justify-between items-center">
        <div className="relative">
          <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight">Tema SID Clasnet</h1>
          <p className="text-sm mt-1 opacity-90">Koleksi desain website sistem informasi desa terbaik untuk wajah digital desa Anda.</p>
        </div>
        <Palette className="relative w-12 h-12 opacity-80 hidden sm:block" />
      </div>

      {/* Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
        {themes.map((theme) => (
          <div
            key={theme.id}
            className="bg-white rounded-2xl border border-gray-100 overflow-hidden premium-shadow hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group"
          >
            {/* Header Screenshot */}
            <div className="h-56 w-full relative bg-gray-50 border-b border-gray-100 overflow-hidden shrink-0">
              <img
                src={theme.image}
                alt={theme.judul}
                className="w-full h-full object-cover object-top group-hover:scale-102 transition duration-500"
              />
            </div>

            {/* Content Details */}
            <div className="p-6 flex-grow flex flex-col justify-between gap-6">
              <div className="space-y-4">
                <div>
                  <span className={`inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold border uppercase tracking-wider ${theme.bgHex}`}>
                    {theme.tipe}
                  </span>
                  <h2 className="text-xl font-extrabold text-gray-900 mt-2 group-hover:text-indigo-600 transition duration-300">
                    {theme.judul}
                  </h2>
                  <p className="text-xs text-gray-500 leading-relaxed mt-2">
                    {theme.deskripsi}
                  </p>
                </div>

                {/* Features List */}
                <div className="space-y-2">
                  <h4 className="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Fitur Unggulan</h4>
                  <div className="grid grid-cols-2 gap-2 text-xs font-semibold text-gray-600">
                    {theme.features.map((f, i) => (
                      <div key={i} className="flex items-center gap-1.5">
                        <span className="w-1.5 h-1.5 rounded-full bg-indigo-600 shrink-0"></span>
                        <span className="truncate">{f}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </div>

              {/* Call to Action button */}
              <a
                href={theme.url}
                target="_blank"
                rel="noopener noreferrer"
                className="w-full inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-sm"
              >
                Lihat Demo Live
                <ExternalLink className="w-3.5 h-3.5" />
              </a>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
