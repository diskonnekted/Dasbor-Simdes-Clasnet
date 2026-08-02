'use client';

import React, { useEffect, useState } from 'react';
import { Search, Globe, Users, Database, Star, Info, ChevronLeft, ChevronRight } from 'lucide-react';

interface DesaRow {
  id: number;
  nama_kecamatan: string;
  nama_desa: string;
  alamat_website: string;
  last_checked_at: string;
  jumlah_penduduk: number;
  db_penduduk: string;
  developer: string;
  stars: number;
}

export default function DesaPage() {
  const [data, setData] = useState<DesaRow[]>([]);
  const [kecList, setKecList] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);

  // Filters
  const [q, setQ] = useState('');
  const [kec, setKec] = useState('');
  const [sid, setSid] = useState('');
  const [dbStatus, setDbStatus] = useState('');
  const [page, setPage] = useState(1);
  const [per, setPer] = useState(25);
  const [pagination, setPagination] = useState({
    page: 1,
    perPage: 25,
    totalRows: 0,
    totalPages: 1,
  });

  const fetchData = async () => {
    setLoading(true);
    try {
      const queryParams = new URLSearchParams({
        q,
        kec,
        sid,
        db: dbStatus,
        page: page.toString(),
        per: per.toString(),
      });
      const res = await fetch(`/api/desa?${queryParams.toString()}`);
      const resData = await res.json();
      if (resData.success) {
        setData(resData.data);
        setPagination(resData.pagination);
        setKecList(resData.kecamatanList || []);
      }
    } catch (err) {
      console.error('Error fetching desa list:', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, [page, per, kec, sid, dbStatus]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setPage(1);
    fetchData();
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
      {/* Banner */}
      <div className="premium-gradient rounded-3xl text-white premium-shadow p-6 sm:p-8 relative overflow-hidden flex justify-between items-center">
        <div className="relative">
          <h1 className="text-2xl sm:text-3xl font-extrabold tracking-tight">Daftar Desa SID</h1>
          <p className="text-sm mt-1 opacity-90">Kelola dan saring data sistem informasi desa se-kabupaten.</p>
        </div>
        <Building2Icon className="relative w-12 h-12 opacity-80 hidden sm:block animate-float" />
      </div>

      {/* Filter panel */}
      <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-6">
        <form onSubmit={handleSearchSubmit} className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          {/* Search input */}
          <div className="relative">
            <Search className="absolute left-3.5 top-3.5 h-4.5 w-4.5 text-gray-400" />
            <input
              type="text"
              placeholder="Cari nama desa..."
              value={q}
              onChange={(e) => setQ(e.target.value)}
              className="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 transition bg-gray-50/50 font-semibold"
            />
          </div>

          {/* Kecamatan select */}
          <select
            value={kec}
            onChange={(e) => { setKec(e.target.value); setPage(1); }}
            className="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl outline-none bg-gray-50/50"
          >
            <option value="">Semua Kecamatan</option>
            {kecList.map((k) => (
              <option key={k} value={k}>
                {k}
              </option>
            ))}
          </select>

          {/* Website status select */}
          <select
            value={sid}
            onChange={(e) => { setSid(e.target.value); setPage(1); }}
            className="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl outline-none bg-gray-50/50"
          >
            <option value="">Status Website (Semua)</option>
            <option value="with">Memiliki Website</option>
            <option value="without">Belum Memiliki Website</option>
          </select>

          {/* Database status select */}
          <select
            value={dbStatus}
            onChange={(e) => { setDbStatus(e.target.value); setPage(1); }}
            className="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl outline-none bg-gray-50/50"
          >
            <option value="">Status Database (Semua)</option>
            <option value="sudah">DB Penduduk (Sudah Ada)</option>
            <option value="belum">DB Penduduk (Belum Ada)</option>
          </select>

          {/* Actions */}
          <button
            type="submit"
            className="w-full premium-gradient hover:premium-gradient-hover text-white font-bold py-2.5 px-4 rounded-xl text-sm transition-all duration-300 shadow-sm hover:shadow-md cursor-pointer"
          >
            Terapkan Filter
          </button>
        </form>
      </div>

      {/* Villages list table */}
      <div className="bg-white rounded-3xl premium-shadow border border-gray-100 overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-100 text-left text-sm text-gray-500">
            <thead className="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-400">
              <tr>
                <th className="px-6 py-4">No</th>
                <th className="px-6 py-4">Kecamatan</th>
                <th className="px-6 py-4">Desa</th>
                <th className="px-6 py-4">Bintang</th>
                <th className="px-6 py-4">Alamat Website</th>
                <th className="px-6 py-4">Penduduk (Jiwa)</th>
                <th className="px-6 py-4">Database</th>
                <th className="px-6 py-4">Developer</th>
                <th className="px-6 py-4">Last Checked</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100 bg-white">
              {loading ? (
                <tr>
                  <td colSpan={9} className="px-6 py-12 text-center text-gray-400">
                    <div className="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mb-2"></div>
                    <div>Memuat data desa...</div>
                  </td>
                </tr>
              ) : data.length > 0 ? (
                data.map((row, idx) => (
                  <tr key={row.id} className="hover:bg-gray-50/50 transition">
                    <td className="px-6 py-4 font-medium text-gray-900">
                      {pagination.perPage * (pagination.page - 1) + idx + 1}
                    </td>
                    <td className="px-6 py-4">{row.nama_kecamatan}</td>
                    <td className="px-6 py-4 font-semibold text-gray-900">{row.nama_desa}</td>
                    <td className="px-6 py-4">
                      <div className="flex gap-0.5">
                        {Array.from({ length: 3 }).map((_, i) => (
                          <Star
                            key={i}
                            className={`w-3.5 h-3.5 ${
                              i < row.stars ? 'fill-amber-500 text-amber-500' : 'text-gray-200'
                            }`}
                          />
                        ))}
                      </div>
                    </td>
                    <td className="px-6 py-4">
                      {row.alamat_website ? (
                        <a
                          href={row.alamat_website}
                          target="_blank"
                          rel="noreferrer"
                          className="text-blue-600 hover:underline font-medium inline-flex items-center gap-1"
                        >
                          <Globe className="w-3.5 h-3.5" />
                          Kunjungi Website
                        </a>
                      ) : (
                        <span className="text-gray-400">Belum ada</span>
                      )}
                    </td>
                    <td className="px-6 py-4">
                      {row.jumlah_penduduk ? row.jumlah_penduduk.toLocaleString('id-ID') : '-'}
                    </td>
                    <td className="px-6 py-4">
                      <span
                        className={`inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ${
                          row.db_penduduk?.toUpperCase() === 'SUDAH ADA'
                            ? 'bg-emerald-50 text-emerald-700'
                            : 'bg-rose-50 text-rose-700'
                        }`}
                      >
                        {row.db_penduduk || 'BELUM ADA'}
                      </span>
                    </td>
                    <td className="px-6 py-4 uppercase text-xs font-semibold tracking-wider text-gray-700">
                      {row.developer || '-'}
                    </td>
                    <td className="px-6 py-4 text-xs text-gray-400">
                      {row.last_checked_at ? new Date(row.last_checked_at).toLocaleDateString('id-ID') : '-'}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan={9} className="px-6 py-12 text-center text-gray-400">
                    Tidak ada data desa ditemukan.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Pagination Controls */}
      {pagination.totalPages > 1 && (
        <div className="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white border border-gray-100 rounded-2xl p-4 shadow-xs">
          <div className="text-sm text-gray-500">
            Menampilkan <span className="font-semibold text-gray-900">{data.length}</span> dari{' '}
            <span className="font-semibold text-gray-900">{pagination.totalRows}</span> baris
          </div>

          <div className="flex items-center gap-2">
            <button
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page === 1 || loading}
              className="p-2 border border-gray-200 rounded-xl hover:bg-gray-50 disabled:opacity-50 disabled:hover:bg-transparent transition text-gray-600"
            >
              <ChevronLeft className="w-5 h-5" />
            </button>
            <span className="text-sm font-semibold text-gray-900 px-4">
              Halaman {pagination.page} dari {pagination.totalPages}
            </span>
            <button
              onClick={() => setPage((p) => Math.min(pagination.totalPages, p + 1))}
              disabled={page === pagination.totalPages || loading}
              className="p-2 border border-gray-200 rounded-xl hover:bg-gray-50 disabled:opacity-50 disabled:hover:bg-transparent transition text-gray-600"
            >
              <ChevronRight className="w-5 h-5" />
            </button>
          </div>
        </div>
      )}
    </div>
  );
}

// Icon helper since lucide-react name is Building2
function Building2Icon(props: React.SVGProps<SVGSVGElement>) {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      strokeWidth="2"
      strokeLinecap="round"
      strokeLinejoin="round"
      {...props}
    >
      <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18" />
      <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
      <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
      <path d="M10 6h4" />
      <path d="M10 10h4" />
      <path d="M10 14h4" />
      <path d="M10 18h4" />
    </svg>
  );
}
