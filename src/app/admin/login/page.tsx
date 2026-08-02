'use client';

import React, { useState, useEffect } from 'react';
import { useRouter } from 'next/navigation';
import { ShieldAlert, User, Lock, ArrowRight } from 'lucide-react';

export default function AdminLoginPage() {
  const router = useRouter();
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  // If already logged in, redirect to admin
  useEffect(() => {
    fetch('/api/auth/status')
      .then((res) => res.json())
      .then((data) => {
        if (data.isLoggedIn) {
          router.push('/admin');
        }
      });
  }, [router]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError('');

    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password }),
      });
      const data = await res.json();
      if (data.success) {
        router.push('/admin');
      } else {
        setError(data.error || 'Username atau password salah.');
      }
    } catch (err) {
      setError('Terjadi kesalahan sistem.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 flex items-center justify-center px-4">
      <div className="w-full max-w-sm bg-white rounded-3xl border border-gray-100 shadow-xl p-8 space-y-6">
        <div className="text-center space-y-2">
          <img src="/clasnet.png" alt="Clasnet Logo" className="mx-auto h-12 w-auto object-contain" />
          <h1 className="text-xl font-extrabold text-gray-900">Login Administrator</h1>
          <p className="text-xs text-gray-400">Masuk untuk mengelola data desa, berita, dan inovasi.</p>
        </div>

        {error && (
          <div className="bg-rose-50 border border-rose-100 text-rose-800 rounded-xl p-3 text-xs font-semibold flex items-center gap-2">
            <ShieldAlert className="w-4 h-4 text-rose-600 shrink-0" />
            <span>{error}</span>
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="space-y-1">
            <label className="text-xs font-bold text-gray-400 uppercase tracking-wider">Username</label>
            <div className="relative">
              <User className="absolute left-3.5 top-3.5 h-4.5 w-4.5 text-gray-400" />
              <input
                type="text"
                required
                value={username}
                onChange={(e) => setUsername(e.target.value)}
                placeholder="Masukkan username..."
                className="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
              />
            </div>
          </div>

          <div className="space-y-1">
            <label className="text-xs font-bold text-gray-400 uppercase tracking-wider">Password</label>
            <div className="relative">
              <Lock className="absolute left-3.5 top-3.5 h-4.5 w-4.5 text-gray-400" />
              <input
                type="password"
                required
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="Masukkan password..."
                className="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50/50"
              />
            </div>
          </div>

          <button
            type="submit"
            disabled={loading}
            className="w-full mt-4 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold py-3 px-4 rounded-xl text-sm transition flex items-center justify-center gap-1.5"
          >
            {loading ? 'Sedang masuk...' : 'Masuk ke Dasbor'}
            {!loading && <ArrowRight className="w-4 h-4" />}
          </button>
        </form>
      </div>
    </div>
  );
}
