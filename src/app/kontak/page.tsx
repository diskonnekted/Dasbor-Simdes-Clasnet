import React from 'react';
import { Phone, MessageSquare, MapPin, Globe, Shield, Star, CheckCircle, Smartphone } from 'lucide-react';

export default function KontakPage() {
  const contacts = [
    {
      role: 'Admin',
      phone: '+62 851-1704-1846',
      telLink: 'tel:+6285117041846',
      waLink: 'https://wa.me/6285117041846',
      gradient: 'from-blue-500 to-indigo-500',
    },
    {
      role: 'Support',
      phone: '+62 813-9236-2332',
      telLink: 'tel:+6281392362332',
      waLink: 'https://wa.me/6281392362332',
      gradient: 'from-emerald-500 to-teal-500',
    },
    {
      role: 'Nara Sumber',
      phone: '+62 822-2353-6812',
      telLink: 'tel:+6282223536812',
      waLink: 'https://wa.me/6282223536812',
      gradient: 'from-rose-500 to-fuchsia-500',
    },
  ];

  const plans = [
    {
      name: 'Lite',
      price: 'Rp 250.000',
      period: '/ bulan',
      image: '/uploads/lite.jpg',
      features: [
        'Domain desa.id',
        'Hosting & Bandwidth standar',
        'Update System Berkala',
        'Panduan Penggunaan Online',
      ],
      border: 'border-gray-100',
      badge: 'Basic',
      badgeClass: 'bg-gray-100 text-gray-700',
    },
    {
      name: 'Standar',
      price: 'Rp 500.000',
      period: '/ bulan',
      image: '/uploads/standar.jpg',
      features: [
        'Semua fasilitas Lite',
        'Kapasitas Hosting lebih besar',
        'Prioritas bantuan teknis',
        'Pendampingan input data awal',
        'Laporan bulanan otomatis',
      ],
      border: 'border-blue-500 ring-2 ring-blue-500/20',
      badge: 'Populer',
      badgeClass: 'bg-blue-600 text-white',
    },
    {
      name: 'VIP',
      price: 'Rp 1.000.000',
      period: '/ bulan',
      image: '/uploads/vip.jpg',
      features: [
        'Semua fasilitas Standar',
        'Dedicated Server & IP',
        'Bantuan teknis prioritas tinggi 24/7',
        'Pelatihan onsite petugas desa',
        'Fitur custom sesuai request',
      ],
      border: 'border-amber-500 ring-2 ring-amber-500/20',
      badge: 'Premium',
      badgeClass: 'bg-amber-500 text-white',
    },
  ];

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-12">
      {/* Banner */}
      <div className="premium-gradient rounded-3xl text-white premium-shadow p-6 sm:p-8 relative overflow-hidden flex justify-between items-center">
        <div className="relative">
          <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight">Kontak Tim Pendamping</h1>
          <p className="text-sm mt-1 opacity-90">Hubungi Clasnet Group untuk integrasi, instalasi, dan pendampingan Sistem Informasi Desa.</p>
          <p className="text-xs mt-2 opacity-80 flex items-center gap-1.5 font-semibold">
            <MapPin className="w-3.5 h-3.5" />
            Alamat: Jl. Serulingmas No. 30, Banjarnegara
          </p>
        </div>
        <Phone className="relative w-12 h-12 opacity-80 hidden sm:block animate-float" />
      </div>

      {/* Main Info Blocks */}
      <div className="bg-white rounded-3xl border border-gray-100 p-6 sm:p-8 premium-shadow grid grid-cols-1 md:grid-cols-3 gap-8">
        <div className="space-y-1">
          <h3 className="text-xs uppercase tracking-wider text-indigo-500 font-extrabold">Alamat Kantor</h3>
          <p className="text-lg font-bold text-gray-900">Jl. Serulingmas No. 30</p>
          <p className="text-sm text-gray-500">Banjarnegara, Jawa Tengah</p>
        </div>

        <div className="space-y-1">
          <h3 className="text-xs uppercase tracking-wider text-gray-400 font-bold">Layanan SID</h3>
          <p className="text-lg font-bold text-gray-900">Clasnet Group</p>
          <p className="text-sm text-gray-500">Pendamping Teknis Digitalisasi Desa</p>
        </div>

        <div className="space-y-2">
          <h3 className="text-xs uppercase tracking-wider text-gray-400 font-bold">Portal Utama</h3>
          <div className="flex flex-wrap gap-2">
            <a
              href="https://www.clasnet.co.id"
              target="_blank"
              rel="noreferrer"
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs font-semibold transition"
            >
              <Globe className="w-3.5 h-3.5" />
              www.clasnet.co.id
            </a>
            <a
              href="https://www.desaonline.cloud"
              target="_blank"
              rel="noreferrer"
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-xs font-semibold transition"
            >
              <Smartphone className="w-3.5 h-3.5" />
              www.desaonline.cloud
            </a>
          </div>
        </div>
      </div>

      {/* Team Contact Cards */}
      <div className="space-y-4">
        <div>
          <h2 className="text-xl font-bold text-gray-900">Hubungi Tim Kami</h2>
          <p className="text-xs text-gray-400">Tim kami siap membantu Anda melalui sambungan Telepon dan WhatsApp.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {contacts.map((c) => (
            <div
              key={c.role}
              className="relative overflow-hidden rounded-2xl bg-white border border-gray-100 shadow-xs p-6 hover:-translate-y-1 transition duration-300 group"
            >
              {/* Decorative top bar */}
              <div className={`absolute inset-x-0 top-0 h-1 bg-gradient-to-r ${c.gradient}`}></div>
              
              <div className="flex items-center gap-4 mb-4">
                <div className="p-3 bg-gray-50 rounded-xl group-hover:bg-blue-50 transition">
                  <Phone className="w-5 h-5 text-gray-600 group-hover:text-blue-600" />
                </div>
                <div>
                  <h3 className="font-bold text-gray-900">{c.role}</h3>
                  <p className="text-xs text-gray-500 font-semibold">{c.phone}</p>
                </div>
              </div>

              <div className="flex items-center gap-2 pt-2">
                <a
                  href={c.telLink}
                  className="flex-grow inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-700 text-xs font-bold transition"
                >
                  <Phone className="w-3.5 h-3.5" />
                  Telepon
                </a>
                <a
                  href={c.waLink}
                  target="_blank"
                  rel="noreferrer"
                  className="flex-grow inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-green-50 hover:bg-green-100 text-green-700 text-xs font-bold transition"
                >
                  <MessageSquare className="w-3.5 h-3.5" />
                  WhatsApp
                </a>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Service Packages */}
      {false && (
        <div className="space-y-6">
          <div className="text-center space-y-1">
            <h2 className="text-2xl font-bold text-gray-900">Pilihan Paket Layanan SID</h2>
            <p className="text-sm text-gray-500">Solusi terpercaya untuk kemudahan pengelolaan data dan website desa digital.</p>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {plans.map((p) => (
              <div
                key={p.name}
                className={`bg-white rounded-3xl border shadow-xs overflow-hidden flex flex-col justify-between ${p.border} relative hover:shadow-lg transition duration-300`}
              >
                {/* Image banner */}
                <div className="h-44 w-full relative bg-gray-50 overflow-hidden">
                  <img src={p.image} alt={p.name} className="w-full h-full object-cover" />
                  <div className={`absolute top-4 right-4 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase ${p.badgeClass}`}>
                    {p.badge}
                  </div>
                </div>

                {/* Package Details */}
                <div className="p-6 flex-grow flex flex-col justify-between gap-6">
                  <div className="space-y-4">
                    <div>
                      <h3 className="text-lg font-bold text-gray-900">Paket {p.name}</h3>
                      <div className="mt-2 flex items-baseline">
                        <span className="text-3xl font-extrabold text-gray-900">{p.price}</span>
                        <span className="text-gray-500 text-xs ml-1 font-semibold">{p.period}</span>
                      </div>
                    </div>

                    <ul className="space-y-2.5 text-xs text-gray-600 font-medium">
                      {p.features.map((f, i) => (
                        <li key={i} className="flex items-start gap-2">
                          <CheckCircle className="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                          <span>{f}</span>
                        </li>
                      ))}
                    </ul>
                  </div>

                  <a
                    href={`https://wa.me/6285117041846?text=Halo%20Admin%20Clasnet,%20saya%20tertarik%20dengan%20Paket%20Layanan%20SID%20${p.name}`}
                    target="_blank"
                    rel="noreferrer"
                    className="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs tracking-wider uppercase transition shadow-sm"
                  >
                    Pilih Paket
                  </a>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
