'use client';

import { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { Menu, X, Map, BarChart3, Building2, Newspaper, Lightbulb, Phone, Shield, Palette } from 'lucide-react';

export default function Navbar() {
  const pathname = usePathname();
  const [isOpen, setIsOpen] = useState(false);

  const navigation = [
    { name: 'Peta Sebaran', href: '/', icon: Map },
    { name: 'Statistik', href: '/statistik', icon: BarChart3 },
    { name: 'Daftar Desa', href: '/desa', icon: Building2 },
    { name: 'Kegiatan', href: '/kegiatan', icon: Newspaper },
    { name: 'Inovasi', href: '/inovasi', icon: Lightbulb },
    { name: 'Tema', href: '/tema', icon: Palette },
    { name: 'Kontak', href: '/kontak', icon: Phone },
  ];

  return (
    <nav className="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100/50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between h-16">
          <div className="flex items-center">
            <Link href="/" className="flex-shrink-0 flex items-center gap-3">
              <img src="/clasnet.png" alt="Logo Clasnet" className="w-10 h-10 object-contain" />
              <div>
                <span className="font-extrabold text-indigo-950 block text-sm leading-tight sm:text-base tracking-tight">
                  Dasbor SID
                </span>
                <span className="text-[10px] text-gray-400 font-bold block -mt-0.5 tracking-wider uppercase">
                  Clasnet Group
                </span>
              </div>
            </Link>
            <div className="hidden md:ml-8 md:flex md:space-x-1 lg:space-x-2">
              {navigation.map((item) => {
                const isActive = pathname === item.href;
                const Icon = item.icon;
                return (
                  <Link
                    key={item.name}
                    href={item.href}
                    className={`inline-flex items-center px-3 py-2 text-xs lg:text-sm font-semibold rounded-xl transition-all duration-300 ${
                      isActive
                        ? 'bg-indigo-50 text-indigo-600 shadow-xs'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-950'
                    }`}
                  >
                    <Icon className="w-4 h-4 mr-1.5 opacity-80" />
                    {item.name}
                  </Link>
                );
              })}
            </div>
          </div>
          <div className="hidden md:flex md:items-center">
            <Link
              href="/admin"
              className="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 hover:text-gray-950 rounded-xl transition-all duration-300 border border-gray-100"
            >
              <Shield className="w-3.5 h-3.5 text-indigo-500" />
              Admin Panel
            </Link>
          </div>
          <div className="flex items-center md:hidden">
            <button
              onClick={() => setIsOpen(!isOpen)}
              className="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none"
            >
              {isOpen ? <X className="block h-6 w-6" /> : <Menu className="block h-6 w-6" />}
            </button>
          </div>
        </div>
      </div>

      {/* Mobile menu */}
      {isOpen && (
        <div className="md:hidden border-b border-gray-100 bg-white/95 backdrop-blur-md">
          <div className="pt-2 pb-3 space-y-1 px-4">
            {navigation.map((item) => {
              const isActive = pathname === item.href;
              const Icon = item.icon;
              return (
                <Link
                  key={item.name}
                  href={item.href}
                  onClick={() => setIsOpen(false)}
                  className={`flex items-center px-4 py-3 rounded-xl text-base font-bold transition-all ${
                    isActive
                      ? 'bg-indigo-50 text-indigo-600'
                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-950'
                  }`}
                >
                  <Icon className="w-5 h-5 mr-3 opacity-80" />
                  {item.name}
                </Link>
              );
            })}
            <div className="border-t border-gray-100 pt-4 pb-2">
              <Link
                href="/admin"
                onClick={() => setIsOpen(false)}
                className="flex items-center px-4 py-3 rounded-xl text-base font-bold text-gray-600 hover:bg-gray-50 hover:text-gray-950"
              >
                <Shield className="w-5 h-5 mr-3 text-indigo-500" />
                Admin Panel
              </Link>
            </div>
          </div>
        </div>
      )}
    </nav>
  );
}
