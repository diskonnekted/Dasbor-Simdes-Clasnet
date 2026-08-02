'use client';

import React, { useEffect, useState } from 'react';
import { Lightbulb, Layers, Zap, Radio, LineChart } from 'lucide-react';

interface InovasiItem {
  id: number;
  gambar: string;
  judul: string;
  deskripsi: string;
}

export default function InovasiPage() {
  const [items, setItems] = useState<InovasiItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch('/api/inovasi')
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setItems(data.items);
        }
        setLoading(false);
      })
      .catch((err) => {
        console.error('Error fetching innovations:', err);
        setLoading(false);
      });
  }, []);

  // Icon mapping helper for cards to make them look premium
  const getIcon = (title: string) => {
    const t = title.toLowerCase();
    if (t.includes('lora')) return <Radio className="w-8 h-8 text-sky-500" />;
    if (t.includes('sensor') || t.includes('iot')) return <Zap className="w-8 h-8 text-amber-500" />;
    if (t.includes('dashboard') || t.includes('kinerja')) return <LineChart className="w-8 h-8 text-emerald-500" />;
    return <Layers className="w-8 h-8 text-indigo-500" />;
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      {/* Banner */}
      <div className="premium-gradient rounded-3xl text-white premium-shadow p-6 sm:p-8 relative overflow-hidden flex justify-between items-center">
        <div className="relative">
          <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight">Galeri Inovasi Desa</h1>
          <p className="text-sm mt-1 opacity-90">Kumpulan inovasi teknologi cerdas untuk kemandirian digital desa Anda.</p>
        </div>
        <Lightbulb className="relative w-12 h-12 opacity-80 hidden sm:block animate-float" />
      </div>

      {loading ? (
        <div className="text-center py-16">
          <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-3"></div>
          <p className="text-gray-500 text-sm">Memuat galeri inovasi...</p>
        </div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {items.map((item) => (
            <div
              key={item.id}
              className="bg-white rounded-3xl border border-gray-100 overflow-hidden premium-shadow hover:premium-shadow-hover transition-all duration-300 flex flex-col group hover:-translate-y-1.5"
            >
              {/* Image Frame */}
              <div className="h-48 w-full bg-gray-50 relative overflow-hidden shrink-0">
                {item.gambar ? (
                  <img
                    src={item.gambar}
                    alt={item.judul}
                    className="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                  />
                ) : (
                  <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50/50 to-blue-50/50">
                    {getIcon(item.judul)}
                  </div>
                )}
                {/* Decorative Icon badge */}
                <div className="absolute bottom-3 left-3 bg-white/95 backdrop-blur-md p-2.5 rounded-2xl shadow-md z-10 border border-white/20">
                  {getIcon(item.judul)}
                </div>
              </div>

              {/* Body */}
              <div className="p-6 flex-grow flex flex-col justify-between gap-4">
                <div className="space-y-2">
                  <h3 className="font-extrabold text-gray-900 leading-snug group-hover:text-indigo-600 transition">
                    {item.judul}
                  </h3>
                  <p className="text-xs text-gray-500 leading-relaxed line-clamp-3">
                    {item.deskripsi}
                  </p>
                </div>

                <div className="pt-3 border-t border-gray-100/50 flex items-center justify-between text-xs font-semibold">
                  <span className="text-gray-400 font-medium">Clasnet Ecosystem</span>
                  <span className="font-bold text-indigo-600 group-hover:underline cursor-pointer">
                    Pelajari lebih lanjut &rarr;
                  </span>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
