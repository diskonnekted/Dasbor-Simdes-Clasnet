'use client';

import dynamic from 'next/dynamic';
import React from 'react';

// Dynamically load MapComponent with SSR disabled
const MapComponent = dynamic(() => import('@/components/MapComponent'), {
  ssr: false,
  loading: () => (
    <div className="max-w-7xl mx-auto px-4 py-16 text-center">
      <div className="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
      <p className="text-gray-500 text-sm">Memuat peta interaktif...</p>
    </div>
  ),
});

export default function HomePage() {
  return (
    <div className="bg-gray-50 min-h-screen">
      <MapComponent />
    </div>
  );
}
