'use client';

import React, { useEffect, useState } from 'react';
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';
import { Bar } from 'react-chartjs-2';
import { Building2, Globe, CheckCircle2, Database, BarChart3, TrendingUp } from 'lucide-react';

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

interface StatData {
  totalDesa: number;
  withWebsite: number;
  withoutWebsite: number;
  websitePct: number;
  active: number;
  inactive: number;
  unknown: number;
  dbPendudukSudah: number;
  dbPendudukBelum: number;
  dbPendudukTotal: number;
  dbPct: number;
  kecamatanStats: Array<{
    kec: string;
    sid: number;
    nonsid: number;
    total: number;
  }>;
}

export default function StatistikPage() {
  const [stats, setStats] = useState<StatData | null>(null);
  const [loading, setLoading] = useState(true);
  const [filterKec, setFilterKec] = useState<string>('all');

  useEffect(() => {
    fetch('/api/statistik')
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setStats(data.stats);
        }
        setLoading(false);
      })
      .catch((err) => {
        console.error('Error fetching statistics:', err);
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-16 text-center">
        <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
        <p className="text-gray-500 text-sm">Memuat dashboard statistik...</p>
      </div>
    );
  }

  if (!stats) {
    return (
      <div className="max-w-7xl mx-auto px-4 py-16 text-center text-rose-500">
        Gagal memuat data statistik. Pastikan database Anda berjalan dan terhubung.
      </div>
    );
  }

  // Filter kecamatan stats for Chart
  const filteredKecStats =
    filterKec === 'all'
      ? stats.kecamatanStats
      : stats.kecamatanStats.filter((k) => k.kec === filterKec);

  const chartData = {
    labels: filteredKecStats.map((k) => k.kec),
    datasets: [
      {
        label: 'Memiliki SID',
        data: filteredKecStats.map((k) => k.sid),
        backgroundColor: 'rgba(59, 130, 246, 0.85)', // Blue
        borderRadius: 4,
      },
      {
        label: 'Belum Memiliki',
        data: filteredKecStats.map((k) => k.nonsid),
        backgroundColor: 'rgba(244, 63, 94, 0.85)', // Rose
        borderRadius: 4,
      },
    ],
  };

  const chartOptions = {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
        },
      },
    },
    plugins: {
      legend: {
        position: 'top' as const,
      },
    },
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      {/* Banner */}
      <div className="premium-gradient rounded-3xl text-white premium-shadow p-6 sm:p-8 relative overflow-hidden flex items-center justify-between">
        <div className="relative">
          <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight">Statistik SID Kabupaten</h1>
          <p className="text-sm mt-1 opacity-90">Gambaran cepat status adopsi website desa dan data penduduk secara real-time.</p>
        </div>
        <BarChart3 className="relative w-12 h-12 opacity-80 hidden sm:block animate-float" />
      </div>

      {/* Cards Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        {/* Total Desa */}
        <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-6 flex items-start justify-between hover:-translate-y-1 transition duration-300">
          <div className="space-y-2">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-400">Total Desa</span>
            <div className="text-4xl font-extrabold text-gray-900">{stats.totalDesa}</div>
            <p className="text-xs text-gray-500">Terdata di basis data SID</p>
          </div>
          <div className="p-3 rounded-2xl bg-indigo-50 text-indigo-600">
            <Building2 className="w-6 h-6" />
          </div>
        </div>

        {/* Memiliki Website */}
        <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-6 space-y-4 hover:-translate-y-1 transition duration-300">
          <div className="flex items-start justify-between">
            <div className="space-y-1">
              <span className="text-xs font-bold uppercase tracking-wider text-gray-400">Memiliki Website</span>
              <div className="text-3xl font-bold text-gray-900">{stats.withWebsite}</div>
            </div>
            <span className="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-lg">
              {stats.websitePct}%
            </span>
          </div>
          <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div className="h-full bg-blue-600 rounded-full" style={{ width: `${stats.websitePct}%` }}></div>
          </div>
          <p className="text-xs text-gray-500 font-semibold">Belum memiliki website: {stats.withoutWebsite}</p>
        </div>

        {/* Website Tanggap */}
        <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-6 space-y-4 hover:-translate-y-1 transition duration-300">
          <div className="flex items-start justify-between">
            <div className="space-y-1">
              <span className="text-xs font-bold uppercase tracking-wider text-gray-400">Website Aktif</span>
              <div className="text-3xl font-bold text-emerald-600">{stats.withWebsite}</div>
            </div>
            <span className="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">
              {stats.websitePct}%
            </span>
          </div>
          <div className="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div className="h-full bg-emerald-500 rounded-full" style={{ width: `${stats.websitePct}%` }}></div>
          </div>
          <p className="text-xs text-gray-500 font-semibold">Telah memiliki domain/URL aktif</p>
        </div>

        {/* Database Penduduk */}
        <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-6 space-y-3 hover:-translate-y-1 transition duration-300">
          <div className="flex justify-between items-center border-b border-gray-100/50 pb-2">
            <span className="text-xs font-bold uppercase tracking-wider text-gray-400 flex items-center gap-1.5">
              <Database className="w-3.5 h-3.5 text-indigo-500" /> DB Penduduk
            </span>
            <span className="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">
              {stats.dbPct}%
            </span>
          </div>
          <div className="space-y-1.5 text-xs font-semibold">
            <div className="flex justify-between">
              <span className="text-gray-500">Sudah Ada:</span>
              <span className="text-gray-900">{stats.dbPendudukSudah} desa</span>
            </div>
            <div className="flex justify-between">
              <span className="text-gray-500">Belum Ada:</span>
              <span className="text-gray-900">{stats.dbPendudukBelum} desa</span>
            </div>
          </div>
          <div className="h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div className="h-full bg-indigo-600 rounded-full" style={{ width: `${stats.dbPct}%` }}></div>
          </div>
        </div>
      </div>

      {/* District Analytics Section */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Chart Column */}
        <div className="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
              <h2 className="text-lg font-bold text-gray-900">Adopsi Website per Kecamatan</h2>
              <p className="text-xs text-gray-400">Distribusi kepemilikan URL website SID tingkat kecamatan.</p>
            </div>
            <div className="flex items-center gap-2">
              <label htmlFor="kecSelect" className="text-xs text-gray-500">Filter Kecamatan:</label>
              <select
                id="kecSelect"
                value={filterKec}
                onChange={(e) => setFilterKec(e.target.value)}
                className="text-xs font-medium border border-gray-200 rounded-lg px-2.5 py-1.5 outline-none bg-white"
              >
                <option value="all">Semua Kecamatan</option>
                {stats.kecamatanStats.map((k) => (
                  <option key={k.kec} value={k.kec}>
                    {k.kec}
                  </option>
                ))}
              </select>
            </div>
          </div>

          <div className="min-h-[300px]">
            <Bar data={chartData} options={chartOptions} />
          </div>
        </div>

        {/* Sidebar Summary */}
        <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col gap-6">
          <div>
            <h2 className="text-lg font-bold text-gray-900 mb-1">Ringkasan Adopsi</h2>
            <p className="text-xs text-gray-400">Analisis cepat kemajuan program digitalisasi desa.</p>
          </div>

          <div className="space-y-4">
            <div className="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 flex items-start gap-3">
              <CheckCircle2 className="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
              <div>
                <h3 className="text-xs font-bold text-emerald-800 uppercase tracking-wide">Pencapaian Website</h3>
                <p className="text-sm text-emerald-950 font-bold mt-0.5">{stats.withWebsite} Desa Mandiri Digital</p>
                <p className="text-xs text-emerald-600 mt-1">
                  Sebanyak {stats.withWebsite} desa telah berhasil mengaktifkan website layanannya.
                </p>
              </div>
            </div>

            <div className="p-4 bg-blue-50/50 rounded-xl border border-blue-100 flex items-start gap-3">
              <TrendingUp className="w-5 h-5 text-blue-600 shrink-0 mt-0.5" />
              <div>
                <h3 className="text-xs font-bold text-blue-800 uppercase tracking-wide">Kecamatan Terbanyak</h3>
                {(() => {
                  const topKec = [...stats.kecamatanStats].sort((a, b) => b.sid - a.sid)[0];
                  return topKec ? (
                    <>
                      <p className="text-sm text-blue-950 font-bold mt-0.5">{topKec.kec}</p>
                      <p className="text-xs text-blue-600 mt-1">
                        Kecamatan dengan tingkat adopsi tertinggi ({topKec.sid} dari {topKec.total} desa memiliki website).
                      </p>
                    </>
                  ) : (
                    <p className="text-xs text-blue-600">Data belum tersedia</p>
                  );
                })()}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
