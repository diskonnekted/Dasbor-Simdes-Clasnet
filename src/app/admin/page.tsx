'use client';

import React, { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import {
  Building2,
  Newspaper,
  Lightbulb,
  LogOut,
  Plus,
  Edit2,
  Trash2,
  Globe,
  Users,
  Database,
  CheckCircle,
  X,
  FileText,
  Upload,
} from 'lucide-react';

interface Desa {
  id: number;
  nama_kecamatan: string;
  nama_desa: string;
  alamat_website: string;
  last_checked_at: string;
  jumlah_penduduk: number;
  db_penduduk: string;
  sosialisasi: string;
  berita_desa: string;
  developer: string;
}

interface Berita {
  id: number;
  judul: string;
  isi: string;
  gambar: string;
  dibuat_pada: string;
  published: number;
  author: string;
  tags: string;
  related_desa: string;
}

interface Inovasi {
  id: number;
  judul: string;
  deskripsi: string;
  gambar: string;
  published: number;
}

export default function AdminDashboard() {
  const router = useRouter();
  const [authorized, setAuthorized] = useState(false);
  const [activeTab, setActiveTab] = useState<'desa' | 'berita' | 'inovasi'>('desa');

  // Lists
  const [desaList, setDesaList] = useState<Desa[]>([]);
  const [beritaList, setBeritaList] = useState<Berita[]>([]);
  const [inovasiList, setInovasiList] = useState<Inovasi[]>([]);
  const [loading, setLoading] = useState(true);

  // Modals / Form States
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [modalType, setModalType] = useState<'add' | 'edit'>('add');
  const [modalData, setModalData] = useState<any>({});
  const [selectedFile, setSelectedFile] = useState<File | null>(null);
  const [formError, setFormError] = useState('');

  // Check auth status
  useEffect(() => {
    fetch('/api/auth/status')
      .then((res) => res.json())
      .then((data) => {
        if (!data.isLoggedIn) {
          router.push('/admin/login');
        } else {
          setAuthorized(true);
        }
      });
  }, [router]);

  // Load lists depending on active tab
  const loadData = async () => {
    if (!authorized) return;
    setLoading(true);
    try {
      const res = await fetch(`/api/admin/${activeTab}`);
      const data = await res.json();
      if (data.success) {
        if (activeTab === 'desa') setDesaList(data.data);
        if (activeTab === 'berita') setBeritaList(data.data);
        if (activeTab === 'inovasi') setInovasiList(data.data);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadData();
  }, [authorized, activeTab]);

  const handleLogout = async () => {
    await fetch('/api/auth/logout', { method: 'POST' });
    router.push('/admin/login');
  };

  // Open Form modal
  const openFormModal = (type: 'add' | 'edit', item?: any) => {
    setModalType(type);
    setSelectedFile(null);
    setFormError('');
    if (type === 'edit' && item) {
      setModalData(item);
    } else {
      setModalData(
        activeTab === 'desa'
          ? {
              nama_kecamatan: '',
              nama_desa: '',
              alamat_website: '',
              jumlah_penduduk: '',
              db_penduduk: 'TIDAK DIKETAHUI',
              sosialisasi: 'belum',
              berita_desa: 'tidak ada',
              developer: '',
              last_checked_at: new Date().toISOString().split('T')[0],
            }
          : activeTab === 'berita'
          ? {
              judul: '',
              isi: '',
              author: 'Clasnet Group',
              tags: '',
              related_desa: '',
              published: 1,
            }
          : {
              judul: '',
              deskripsi: '',
              published: 1,
            }
      );
    }
    setIsModalOpen(true);
  };

  // Save changes
  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setFormError('');

    try {
      let res;
      if (activeTab === 'desa') {
        const url = modalType === 'edit' ? `/api/admin/desa/${modalData.id}` : '/api/admin/desa';
        const method = modalType === 'edit' ? 'PUT' : 'POST';
        res = await fetch(url, {
          method,
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(modalData),
        });
      } else {
        // Handle news / innovations with multipart file upload
        const formData = new FormData();
        Object.keys(modalData).forEach((key) => {
          formData.append(key, modalData[key]);
        });
        if (selectedFile) {
          formData.append('gambar', selectedFile);
        }

        const url =
          modalType === 'edit' ? `/api/admin/${activeTab}/${modalData.id}` : `/api/admin/${activeTab}`;
        const method = modalType === 'edit' ? 'PUT' : 'POST';

        res = await fetch(url, {
          method,
          body: formData,
        });
      }

      const resData = await res.json();
      if (resData.success) {
        setIsModalOpen(false);
        loadData();
      } else {
        setFormError(resData.error || 'Terjadi kesalahan menyimpan data.');
      }
    } catch (err) {
      setFormError('Gagal menyimpan data.');
    }
  };

  // Delete handler
  const handleDelete = async (id: number) => {
    if (!confirm('Apakah Anda yakin ingin menghapus data ini?')) return;
    try {
      const res = await fetch(`/api/admin/${activeTab}/${id}`, {
        method: 'DELETE',
      });
      const data = await res.json();
      if (data.success) {
        loadData();
      } else {
        alert(data.error || 'Gagal menghapus data.');
      }
    } catch (err) {
      alert('Gagal menghapus data.');
    }
  };

  if (!authorized) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-gray-50">
        <p className="text-gray-500 text-sm">Memvalidasi sesi...</p>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
      {/* Sidebar Panel */}
      <aside className="w-full lg:w-64 bg-white border-r border-gray-100 flex flex-col shrink-0">
        <div className="p-6 border-b border-gray-50 flex items-center gap-3">
          <img src="/clasnet.png" alt="Logo" className="w-10 h-10 rounded object-contain" />
          <div>
            <h1 className="font-bold text-gray-900 text-sm">Dashboard Admin</h1>
            <span className="text-[10px] text-gray-400 font-semibold uppercase">SID Ecosystem</span>
          </div>
        </div>

        <nav className="flex-grow p-4 space-y-1">
          <button
            onClick={() => setActiveTab('desa')}
            className={`w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition ${
              activeTab === 'desa'
                ? 'bg-blue-50 text-blue-600'
                : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'
            }`}
          >
            <Building2 className="w-5 h-5 opacity-80" />
            Kelola Desa
          </button>

          <button
            onClick={() => setActiveTab('berita')}
            className={`w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition ${
              activeTab === 'berita'
                ? 'bg-blue-50 text-blue-600'
                : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'
            }`}
          >
            <Newspaper className="w-5 h-5 opacity-80" />
            Kelola Berita
          </button>

          <button
            onClick={() => setActiveTab('inovasi')}
            className={`w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-xl transition ${
              activeTab === 'inovasi'
                ? 'bg-blue-50 text-blue-600'
                : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'
            }`}
          >
            <Lightbulb className="w-5 h-5 opacity-80" />
            Kelola Inovasi
          </button>
        </nav>

        <div className="p-4 border-t border-gray-50">
          <button
            onClick={handleLogout}
            className="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition"
          >
            <LogOut className="w-5 h-5" />
            Keluar Sesi
          </button>
        </div>
      </aside>

      {/* Main Content Pane */}
      <main className="flex-grow p-6 lg:p-10 space-y-6 overflow-x-hidden">
        {/* Header toolbar */}
        <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-100 pb-5">
          <div>
            <h2 className="text-xl sm:text-2xl font-bold text-gray-900 capitalize">
              {activeTab === 'desa' ? 'Kelola Data Desa' : activeTab === 'berita' ? 'Kelola Berita & Kegiatan' : 'Kelola Galeri Inovasi'}
            </h2>
            <p className="text-xs text-gray-400 mt-1">Tambahkan, edit, atau hapus entitas di database SID.</p>
          </div>

          <button
            onClick={() => openFormModal('add')}
            className="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2.5 rounded-xl text-sm transition shadow-sm"
          >
            <Plus className="w-4 h-4" />
            Tambah Baru
          </button>
        </div>

        {/* Content list representation */}
        {loading ? (
          <div className="text-center py-16">
            <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
            <p className="text-gray-500 text-sm">Memuat data dari database...</p>
          </div>
        ) : (
          <div className="bg-white rounded-2xl shadow-xs border border-gray-100 overflow-hidden">
            <div className="overflow-x-auto">
              {activeTab === 'desa' && (
                <table className="min-w-full divide-y divide-gray-100 text-left text-sm text-gray-500">
                  <thead className="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-400">
                    <tr>
                      <th className="px-6 py-4">Kecamatan</th>
                      <th className="px-6 py-4">Desa</th>
                      <th className="px-6 py-4">Website</th>
                      <th className="px-6 py-4">Database</th>
                      <th className="px-6 py-4">Developer</th>
                      <th className="px-6 py-4">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100 bg-white">
                    {desaList.map((desa) => (
                      <tr key={desa.id} className="hover:bg-gray-50/50 transition">
                        <td className="px-6 py-4">{desa.nama_kecamatan}</td>
                        <td className="px-6 py-4 font-semibold text-gray-900">{desa.nama_desa}</td>
                        <td className="px-6 py-4 truncate max-w-40">{desa.alamat_website || '-'}</td>
                        <td className="px-6 py-4">
                          <span
                            className={`inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ${
                              desa.db_penduduk?.toUpperCase() === 'SUDAH ADA'
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700'
                            }`}
                          >
                            {desa.db_penduduk || 'BELUM ADA'}
                          </span>
                        </td>
                        <td className="px-6 py-4 uppercase text-xs font-semibold">{desa.developer}</td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2">
                            <button
                              onClick={() => openFormModal('edit', desa)}
                              className="p-1.5 hover:bg-gray-100 rounded-lg text-gray-600 transition"
                            >
                              <Edit2 className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => handleDelete(desa.id)}
                              className="p-1.5 hover:bg-rose-50 rounded-lg text-rose-600 transition"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}

              {activeTab === 'berita' && (
                <table className="min-w-full divide-y divide-gray-100 text-left text-sm text-gray-500">
                  <thead className="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-400">
                    <tr>
                      <th className="px-6 py-4">Gambar</th>
                      <th className="px-6 py-4">Judul Berita</th>
                      <th className="px-6 py-4">Author</th>
                      <th className="px-6 py-4">Status</th>
                      <th className="px-6 py-4">Tanggal</th>
                      <th className="px-6 py-4">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100 bg-white">
                    {beritaList.map((berita) => (
                      <tr key={berita.id} className="hover:bg-gray-50/50 transition">
                        <td className="px-6 py-4">
                          <div className="w-12 h-10 rounded overflow-hidden bg-gray-50 border">
                            {berita.gambar ? (
                              <img src={berita.gambar} alt="Thumbnail" className="w-full h-full object-cover" />
                            ) : (
                              <div className="w-full h-full flex items-center justify-center text-gray-300">
                                <FileText className="w-4 h-4" />
                              </div>
                            )}
                          </div>
                        </td>
                        <td className="px-6 py-4 font-semibold text-gray-900 max-w-xs truncate">
                          {berita.judul}
                        </td>
                        <td className="px-6 py-4">{berita.author}</td>
                        <td className="px-6 py-4">
                          <span
                            className={`inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ${
                              berita.published
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-amber-50 text-amber-700'
                            }`}
                          >
                            {berita.published ? 'Published' : 'Draft'}
                          </span>
                        </td>
                        <td className="px-6 py-4 text-xs text-gray-400">
                          {new Date(berita.dibuat_pada).toLocaleDateString('id-ID')}
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2">
                            <button
                              onClick={() => openFormModal('edit', berita)}
                              className="p-1.5 hover:bg-gray-100 rounded-lg text-gray-600 transition"
                            >
                              <Edit2 className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => handleDelete(berita.id)}
                              className="p-1.5 hover:bg-rose-50 rounded-lg text-rose-600 transition"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}

              {activeTab === 'inovasi' && (
                <table className="min-w-full divide-y divide-gray-100 text-left text-sm text-gray-500">
                  <thead className="bg-gray-50 text-xs font-bold uppercase tracking-wider text-gray-400">
                    <tr>
                      <th className="px-6 py-4">Gambar</th>
                      <th className="px-6 py-4">Judul Inovasi</th>
                      <th className="px-6 py-4">Deskripsi</th>
                      <th className="px-6 py-4">Status</th>
                      <th className="px-6 py-4">Aksi</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-100 bg-white">
                    {inovasiList.map((inovasi) => (
                      <tr key={inovasi.id} className="hover:bg-gray-50/50 transition">
                        <td className="px-6 py-4">
                          <div className="w-12 h-10 rounded overflow-hidden bg-gray-50 border">
                            {inovasi.gambar ? (
                              <img src={inovasi.gambar} alt="Thumbnail" className="w-full h-full object-cover" />
                            ) : (
                              <div className="w-full h-full flex items-center justify-center text-gray-300">
                                <FileText className="w-4 h-4" />
                              </div>
                            )}
                          </div>
                        </td>
                        <td className="px-6 py-4 font-semibold text-gray-900 max-w-xs truncate">
                          {inovasi.judul}
                        </td>
                        <td className="px-6 py-4 max-w-sm truncate">{inovasi.deskripsi}</td>
                        <td className="px-6 py-4">
                          <span
                            className={`inline-flex px-2 py-0.5 text-xs font-semibold rounded-full ${
                              inovasi.published
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-amber-50 text-amber-700'
                            }`}
                          >
                            {inovasi.published ? 'Published' : 'Draft'}
                          </span>
                        </td>
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-2">
                            <button
                              onClick={() => openFormModal('edit', inovasi)}
                              className="p-1.5 hover:bg-gray-100 rounded-lg text-gray-600 transition"
                            >
                              <Edit2 className="w-4 h-4" />
                            </button>
                            <button
                              onClick={() => handleDelete(inovasi.id)}
                              className="p-1.5 hover:bg-rose-50 rounded-lg text-rose-600 transition"
                            >
                              <Trash2 className="w-4 h-4" />
                            </button>
                          </div>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              )}
            </div>
          </div>
        )}
      </main>

      {/* CRUD Form Modal */}
      {isModalOpen && (
        <div className="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4 backdrop-blur-xs overflow-y-auto">
          <div className="bg-white rounded-3xl shadow-2xl border border-gray-100 max-w-xl w-full p-6 space-y-6 max-h-[90vh] overflow-y-auto relative">
            <div className="flex justify-between items-center border-b border-gray-50 pb-4">
              <h3 className="text-lg font-bold text-gray-900 capitalize">
                {modalType === 'edit' ? 'Edit Data' : 'Tambah Baru'}
              </h3>
              <button
                onClick={() => setIsModalOpen(false)}
                className="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            {formError && (
              <div className="bg-rose-50 border border-rose-100 text-rose-800 rounded-xl p-3 text-xs font-semibold">
                {formError}
              </div>
            )}

            <form onSubmit={handleSave} className="space-y-4">
              {/* DESA FIELDS */}
              {activeTab === 'desa' && (
                <>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Kecamatan</label>
                      <input
                        type="text"
                        required
                        value={modalData.nama_kecamatan || ''}
                        onChange={(e) =>
                          setModalData({ ...modalData, nama_kecamatan: e.target.value })
                        }
                        className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
                      />
                    </div>
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Desa</label>
                      <input
                        type="text"
                        required
                        value={modalData.nama_desa || ''}
                        onChange={(e) => setModalData({ ...modalData, nama_desa: e.target.value })}
                        className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
                      />
                    </div>
                  </div>

                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">Alamat Website URL</label>
                    <input
                      type="url"
                      value={modalData.alamat_website || ''}
                      onChange={(e) => setModalData({ ...modalData, alamat_website: e.target.value })}
                      placeholder="https://desa.id"
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Jumlah Penduduk</label>
                      <input
                        type="number"
                        value={modalData.jumlah_penduduk || ''}
                        onChange={(e) =>
                          setModalData({ ...modalData, jumlah_penduduk: e.target.value })
                        }
                        className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
                      />
                    </div>
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Status DB</label>
                      <select
                        value={modalData.db_penduduk || 'TIDAK DIKETAHUI'}
                        onChange={(e) => setModalData({ ...modalData, db_penduduk: e.target.value })}
                        className="w-full px-3 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                      >
                        <option value="SUDAH ADA">Sudah Ada</option>
                        <option value="BELUM ADA">Belum Ada</option>
                        <option value="TIDAK DIKETAHUI">Tidak Diketahui</option>
                      </select>
                    </div>
                  </div>

                  <div className="grid grid-cols-3 gap-4">
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Sosialisasi</label>
                      <select
                        value={modalData.sosialisasi || 'belum'}
                        onChange={(e) => setModalData({ ...modalData, sosialisasi: e.target.value })}
                        className="w-full px-3 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                      >
                        <option value="sudah">Sudah</option>
                        <option value="belum">Belum</option>
                      </select>
                    </div>
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Berita Desa</label>
                      <select
                        value={modalData.berita_desa || 'tidak ada'}
                        onChange={(e) => setModalData({ ...modalData, berita_desa: e.target.value })}
                        className="w-full px-3 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                      >
                        <option value="update">Update</option>
                        <option value="tidak update">Tidak Update</option>
                        <option value="tidak ada">Tidak Ada</option>
                      </select>
                    </div>
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Developer</label>
                      <select
                        value={modalData.developer || ''}
                        onChange={(e) => setModalData({ ...modalData, developer: e.target.value })}
                        className="w-full px-3 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                      >
                        <option value="">Lainnya</option>
                        <option value="clasnet">Clasnet</option>
                        <option value="digitaldesa">DigitalDesa</option>
                        <option value="opendesa">OpenDesa</option>
                        <option value="parso rtik">Parso RTIK</option>
                        <option value="supri rtik">Supri RTIK</option>
                        <option value="sraya">Sraya</option>
                      </select>
                    </div>
                  </div>

                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">Terakhir Diperiksa</label>
                    <input
                      type="date"
                      value={
                        modalData.last_checked_at
                          ? new Date(modalData.last_checked_at).toISOString().split('T')[0]
                          : ''
                      }
                      onChange={(e) =>
                        setModalData({ ...modalData, last_checked_at: e.target.value })
                      }
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                    />
                  </div>
                </>
              )}

              {/* BERITA FIELDS */}
              {activeTab === 'berita' && (
                <>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">Judul Berita</label>
                    <input
                      type="text"
                      required
                      value={modalData.judul || ''}
                      onChange={(e) => setModalData({ ...modalData, judul: e.target.value })}
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">Isi Berita</label>
                    <textarea
                      required
                      rows={5}
                      value={modalData.isi || ''}
                      onChange={(e) => setModalData({ ...modalData, isi: e.target.value })}
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50 resize-y"
                    ></textarea>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Penulis (Author)</label>
                      <input
                        type="text"
                        value={modalData.author || ''}
                        onChange={(e) => setModalData({ ...modalData, author: e.target.value })}
                        className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                      />
                    </div>
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Tags</label>
                      <input
                        type="text"
                        value={modalData.tags || ''}
                        onChange={(e) => setModalData({ ...modalData, tags: e.target.value })}
                        placeholder="contoh: bansos covid"
                        className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                      />
                    </div>
                  </div>

                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">ID Desa Terkait</label>
                    <input
                      type="text"
                      value={modalData.related_desa || ''}
                      onChange={(e) => setModalData({ ...modalData, related_desa: e.target.value })}
                      placeholder="Contoh: 12,45 (pisahkan dengan koma)"
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none bg-gray-50/50"
                    />
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Status Publikasi</label>
                      <select
                        value={modalData.published !== undefined ? modalData.published.toString() : '1'}
                        onChange={(e) =>
                          setModalData({ ...modalData, published: e.target.value === '1' ? 1 : 0 })
                        }
                        className="w-full px-3 py-2 text-sm border rounded-xl bg-gray-50/50"
                      >
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                      </select>
                    </div>

                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Ganti Gambar Banner</label>
                      <div className="flex items-center gap-2">
                        <label className="flex-grow flex items-center justify-center gap-2 px-3 py-2 border border-dashed rounded-xl bg-gray-50 cursor-pointer text-xs text-gray-600 hover:bg-gray-100 transition">
                          <Upload className="w-4 h-4" />
                          <span>{selectedFile ? selectedFile.name : 'Pilih Berkas'}</span>
                          <input
                            type="file"
                            accept="image/*"
                            onChange={(e) =>
                              setSelectedFile(e.target.files ? e.target.files[0] : null)
                            }
                            className="hidden"
                          />
                        </label>
                      </div>
                    </div>
                  </div>
                </>
              )}

              {/* INOVASI FIELDS */}
              {activeTab === 'inovasi' && (
                <>
                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">Judul Inovasi</label>
                    <input
                      type="text"
                      required
                      value={modalData.judul || ''}
                      onChange={(e) => setModalData({ ...modalData, judul: e.target.value })}
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
                    />
                  </div>

                  <div className="space-y-1">
                    <label className="text-xs font-bold text-gray-400 uppercase">Deskripsi</label>
                    <textarea
                      rows={4}
                      value={modalData.deskripsi || ''}
                      onChange={(e) => setModalData({ ...modalData, deskripsi: e.target.value })}
                      className="w-full px-3.5 py-2 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50 resize-y"
                    ></textarea>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Status</label>
                      <select
                        value={modalData.published !== undefined ? modalData.published.toString() : '1'}
                        onChange={(e) =>
                          setModalData({ ...modalData, published: e.target.value === '1' ? 1 : 0 })
                        }
                        className="w-full px-3 py-2 text-sm border rounded-xl bg-gray-50/50"
                      >
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                      </select>
                    </div>

                    <div className="space-y-1">
                      <label className="text-xs font-bold text-gray-400 uppercase">Ganti Gambar</label>
                      <label className="flex items-center justify-center gap-2 px-3 py-2 border border-dashed rounded-xl bg-gray-50 cursor-pointer text-xs text-gray-600 hover:bg-gray-100 transition">
                        <Upload className="w-4 h-4" />
                        <span>{selectedFile ? selectedFile.name : 'Pilih Berkas'}</span>
                        <input
                          type="file"
                          accept="image/*"
                          onChange={(e) =>
                            setSelectedFile(e.target.files ? e.target.files[0] : null)
                          }
                          className="hidden"
                        />
                      </label>
                    </div>
                  </div>
                </>
              )}

              <div className="pt-4 border-t border-gray-50 flex items-center justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  className="px-4 py-2 text-sm font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 text-sm font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition flex items-center gap-1.5"
                >
                  <CheckCircle className="w-4 h-4" />
                  Simpan Data
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
