'use client';

import React, { useEffect, useRef, useState } from 'react';
import Link from 'next/link';
import { MapPin, Globe, Users, Database, Star, Info, Newspaper, X, Filter, ChevronRight, Building } from 'lucide-react';
import 'ol/ol.css';

// Type definitions
interface DesaInfo {
  id: number;
  nama_kecamatan: string;
  nama_desa: string;
  alamat_website: string;
  jumlah_penduduk: number;
  db_penduduk: string;
  developer: string;
  stars: number;
}

interface NewsItem {
  id: number;
  judul: string;
  dibuat_pada: string;
  author: string;
  gambar: string;
  excerpt: string;
}

export default function MapComponent() {
  const mapRef = useRef<HTMLDivElement>(null);
  const popupRef = useRef<HTMLDivElement>(null);
  const [olLoaded, setOlLoaded] = useState(false);
  const [desaData, setDesaData] = useState<Record<string, DesaInfo>>({});
  
  // Selection states
  const [selectedDesaKey, setSelectedDesaKey] = useState<string>('');
  const [selectedDesaName, setSelectedDesaName] = useState<string>('');
  const [selectedKecName, setSelectedKecName] = useState<string>('');
  const [selectedDesaInfo, setSelectedDesaInfo] = useState<DesaInfo | null>(null);
  const [relatedNews, setRelatedNews] = useState<NewsItem[]>([]);
  const [loadingNews, setLoadingNews] = useState<boolean>(false);

  // Checkbox Indicators
  const [showOnlyWebsite, setShowOnlyWebsite] = useState(false);
  const [showOnlyDb, setShowOnlyDb] = useState(false);
  const [showOnlyClasnet, setShowOnlyClasnet] = useState(false);

  // Search autocomplete
  const [searchQuery, setSearchQuery] = useState<string>('');
  const [searchResults, setSearchResults] = useState<DesaInfo[]>([]);

  const mapInstanceRef = useRef<any>(null);
  const vectorSourceRef = useRef<any>(null);

  // Fetch initial Map Stars database mapping
  useEffect(() => {
    fetch('/api/peta')
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.desaData) {
          setDesaData(data.desaData);
          setOlLoaded(true);
        }
      })
      .catch((err) => console.error('Error fetching map db:', err));
  }, []);

  // Initialize and Update Map (Reacts to changes in filters and selection instantly!)
  useEffect(() => {
    if (!olLoaded || !mapRef.current) return;

    const initOL = async () => {
      const { default: Map } = await import('ol/Map');
      const { default: View } = await import('ol/View');
      const { default: TileLayer } = await import('ol/layer/Tile');
      const { default: VectorLayer } = await import('ol/layer/Vector');
      const { default: OSM } = await import('ol/source/OSM');
      const { default: VectorSource } = await import('ol/source/Vector');
      const { default: GeoJSON } = await import('ol/format/GeoJSON');
      const { default: Overlay } = await import('ol/Overlay');
      const { Style, Fill, Stroke, Text } = await import('ol/style');
      const { fromLonLat } = await import('ol/proj');

      const vectorSource = new VectorSource({
        format: new GeoJSON({ dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857' }),
        url: '/peta_desa.geojson',
      });
      vectorSourceRef.current = vectorSource;

      const vectorLayer = new VectorLayer({
        declutter: true,
        source: vectorSource,
        style: function (feature: any) {
          const name = feature.get('Nama_Desa_') || feature.get('nama') || feature.get('Name') || '';
          const kec = feature.get('Nama_Kec') || feature.get('kecamatan') || feature.get('Kecamatan') || '';
          const normDesa = name.toLowerCase().trim().replace(/^desa\s+/, '').replace(/\s+/g, ' ');
          const normKec = kec.toLowerCase().trim().replace(/^kec(\.|amatan)?\s*/, '').replace(/\s+/g, ' ');
          const key = `${normKec}|${normDesa}`;

          let hasWebsite = false;
          let starCount = 0;
          let isClasnet = false;
          
          let data = desaData[key];
          if (!data) {
            const potentialMatches = Object.keys(desaData).filter((k) => k.endsWith('|' + normDesa));
            if (potentialMatches.length === 1) {
              data = desaData[potentialMatches[0]];
            }
          }

          if (data) {
            hasWebsite = !!(data.alamat_website && data.alamat_website.trim() !== '');
            starCount = data.stars || 0;
            if (data.developer && data.developer.toLowerCase().includes('clasnet')) {
              isClasnet = true;
            }
          }

          // Checkbox indicator filters
          let matchesFilter = true;
          if (showOnlyWebsite && !hasWebsite) matchesFilter = false;
          if (showOnlyDb && (!data || data.db_penduduk?.toUpperCase() !== 'SUDAH ADA')) matchesFilter = false;
          if (showOnlyClasnet && !isClasnet) matchesFilter = false;

          let fillColor, strokeColor, strokeWidth = 1;
          const isSelected = selectedDesaKey === key;
          
          if (isSelected) {
            fillColor = 'rgba(99, 102, 241, 0.7)'; // Vibrant selection color
            strokeColor = '#4f46e5';
            strokeWidth = 3;
          } else if (!matchesFilter) {
            fillColor = 'rgba(244, 244, 245, 0.05)'; // Faded out
            strokeColor = 'rgba(228, 228, 231, 0.15)';
          } else if (isClasnet) {
            fillColor = 'rgba(56, 189, 248, 0.45)'; // Sky Blue
            strokeColor = '#0284c7';
          } else if (hasWebsite) {
            fillColor = 'rgba(16, 185, 129, 0.25)'; // Green
            strokeColor = '#10b981';
          } else {
            fillColor = 'rgba(244, 63, 94, 0.15)'; // Red
            strokeColor = '#ef4444';
          }

          let labelText = name;
          if (starCount > 0) labelText += ' ' + '★'.repeat(starCount);

          return new Style({
            fill: new Fill({ color: fillColor }),
            stroke: new Stroke({ color: strokeColor, width: strokeWidth }),
            text: name && matchesFilter
              ? new Text({
                  text: labelText,
                  font: isSelected ? 'bold 11px sans-serif' : '10px sans-serif',
                  fill: new Fill({ color: isSelected ? '#ffffff' : '#0f172a' }),
                  stroke: new Stroke({ color: isSelected ? '#4f46e5' : '#ffffff', width: 2 }),
                })
              : undefined,
          });
        },
      });

      const map = new Map({
        target: mapRef.current!,
        layers: [new TileLayer({ source: new OSM() }), vectorLayer],
        view: new View({ center: fromLonLat([109.7, -7.35]), zoom: 10 }),
      });
      mapInstanceRef.current = map;

      vectorSource.once('change', function () {
        if (vectorSource.getState() === 'ready') {
          const extent = vectorSource.getExtent();
          if (extent) map.getView().fit(extent, { padding: [40, 40, 40, 40], duration: 300 });
        }
      });

      const popupOverlay = new Overlay({
        element: popupRef.current!,
        positioning: 'bottom-center',
        stopEvent: true,
        offset: [0, -10],
      });
      map.addOverlay(popupOverlay);

      map.on('click', function (evt: any) {
        const feature = map.forEachFeatureAtPixel(evt.pixel, (f: any) => f);
        if (feature) {
          const name = feature.get('Nama_Desa_') || feature.get('nama') || feature.get('Name') || '';
          const kec = feature.get('Nama_Kec') || feature.get('kecamatan') || feature.get('Kecamatan') || '';
          
          const normDesa = name.toLowerCase().trim().replace(/^desa\s+/, '').replace(/\s+/g, ' ');
          const normKec = kec.toLowerCase().trim().replace(/^kec(\.|amatan)?\s*/, '').replace(/\s+/g, ' ');
          const key = `${normKec}|${normDesa}`;
          
          setSelectedDesaKey(key);
          setSelectedDesaName(name);
          setSelectedKecName(kec);

          const coordinate = evt.coordinate;
          popupOverlay.setPosition(coordinate);
          popupRef.current!.style.display = 'block';

          triggerDesaSelect(name, kec, key);
        } else {
          popupOverlay.setPosition(undefined);
          popupRef.current!.style.display = 'none';
          setSelectedDesaKey('');
          setSelectedDesaInfo(null);
        }
      });
    };

    initOL();

    return () => {
      if (mapInstanceRef.current) {
        mapInstanceRef.current.setTarget(undefined);
      }
    };
  }, [olLoaded, desaData, showOnlyWebsite, showOnlyDb, showOnlyClasnet, selectedDesaKey]);

  // Load details
  const triggerDesaSelect = async (desaName: string, kecName: string, key: string) => {
    let data = desaData[key];
    if (!data) {
      const normDesa = desaName.toLowerCase().trim().replace(/^desa\s+/, '').replace(/\s+/g, ' ');
      const potentialMatches = Object.keys(desaData).filter((k) => k.endsWith('|' + normDesa));
      if (potentialMatches.length === 1) {
        data = desaData[potentialMatches[0]];
      }
    }

    if (data) {
      setSelectedDesaInfo(data);
    } else {
      setSelectedDesaInfo({
        id: 0,
        nama_desa: desaName,
        nama_kecamatan: kecName,
        alamat_website: '',
        jumlah_penduduk: 0,
        db_penduduk: 'TIDAK DIKETAHUI',
        developer: '',
        stars: 0,
      });
    }

    setLoadingNews(true);
    try {
      const res = await fetch(`/api/peta/berita?desa=${encodeURIComponent(desaName)}&kec=${encodeURIComponent(kecName)}`);
      if (!res.ok) {
        console.error('LOG: Related news API failed with status:', res.status);
        setRelatedNews([]);
        return;
      }
      const resData = await res.json();
      setRelatedNews(resData.items || []);
    } catch (err) {
      console.error(err);
      setRelatedNews([]);
    } finally {
      setLoadingNews(false);
    }
  };

  const handleSelectSearchResult = (desa: DesaInfo) => {
    setSearchQuery('');
    setSearchResults([]);

    const normDesa = desa.nama_desa.toLowerCase().trim().replace(/^desa\s+/, '').replace(/\s+/g, ' ');
    const normKec = desa.nama_kecamatan.toLowerCase().trim().replace(/^kec(\.|amatan)?\s*/, '').replace(/\s+/g, ' ');
    const key = `${normKec}|${normDesa}`;

    setSelectedDesaKey(key);
    setSelectedDesaName(desa.nama_desa);
    setSelectedKecName(desa.nama_kecamatan);
    triggerDesaSelect(desa.nama_desa, desa.nama_kecamatan, key);

    if (mapInstanceRef.current && vectorSourceRef.current) {
      const features = vectorSourceRef.current.getFeatures();
      const targetFeature = features.find((f: any) => {
        const fDesa = (f.get('Nama_Desa_') || f.get('nama') || '').toLowerCase().trim().replace(/^desa\s+/, '').replace(/\s+/g, ' ');
        const fKec = (f.get('Nama_Kec') || f.get('kecamatan') || '').toLowerCase().trim().replace(/^kec(\.|amatan)?\s*/, '').replace(/\s+/g, ' ');
        return fDesa === normDesa && fKec === normKec;
      });

      if (targetFeature) {
        const geometry = targetFeature.getGeometry();
        const extent = geometry.getExtent();
        mapInstanceRef.current.getView().fit(extent, { padding: [100, 100, 100, 100], duration: 500, maxZoom: 15 });

        let centerCoord = geometry.getCoordinates();
        if (geometry.getType() === 'MultiPolygon') {
          centerCoord = geometry.getInteriorPoints().getCoordinates()[0];
        } else if (geometry.getType() === 'Polygon') {
          centerCoord = geometry.getInteriorPoint().getCoordinates();
        }

        const overlays = mapInstanceRef.current.getOverlays();
        if (overlays.getLength() > 0) {
          overlays.item(0).setPosition(centerCoord);
          popupRef.current!.style.display = 'block';
        }
      }
    }
  };

  const handleSearchChange = (val: string) => {
    setSearchQuery(val);
    if (!val.trim()) {
      setSearchResults([]);
      return;
    }

    const query = val.toLowerCase();
    const matches = Object.values(desaData)
      .filter((d) => d.nama_desa.toLowerCase().includes(query) || d.nama_kecamatan.toLowerCase().includes(query))
      .slice(0, 8);
    setSearchResults(matches);
  };

  return (
    <div className="w-full px-4 sm:px-6 lg:px-8 py-8 space-y-6">
      {/* Search Header Banner */}
      <div className="premium-gradient rounded-3xl text-white premium-shadow p-6 relative overflow-hidden">
        <div className="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
          <div className="space-y-1">
            <h1 className="text-xl sm:text-2xl font-extrabold tracking-tight">Peta Sebaran SID</h1>
            <p className="text-xs opacity-90">Sistem Informasi Geografis sebaran layanan website desa dan data penduduk.</p>
          </div>
          <div className="relative w-full md:w-80 z-20">
            <div className="bg-white/95 rounded-2xl shadow-sm border border-gray-100 flex items-center p-1 text-gray-800">
              <input
                type="text"
                placeholder="Cari desa atau kecamatan..."
                value={searchQuery}
                onChange={(e) => handleSearchChange(e.target.value)}
                className="w-full px-3 py-1.5 outline-none text-xs bg-transparent placeholder-gray-400 font-semibold"
              />
              {searchQuery && (
                <button onClick={() => { setSearchQuery(''); setSearchResults([]); }} className="p-1.5 text-gray-400 hover:text-gray-600">
                  <X className="w-4 h-4" />
                </button>
              )}
            </div>

            {/* Autocomplete list */}
            {searchResults.length > 0 && (
              <div className="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl premium-shadow border border-gray-100 overflow-hidden text-gray-800 z-50">
                {searchResults.map((d) => (
                  <button
                    key={`${d.nama_kecamatan}-${d.nama_desa}`}
                    onClick={() => handleSelectSearchResult(d)}
                    className="w-full text-left px-4 py-2.5 text-xs hover:bg-indigo-50/50 flex items-center justify-between border-b border-gray-50 last:border-0"
                  >
                    <div>
                      <div className="font-extrabold text-gray-900">{d.nama_desa}</div>
                      <div className="text-[10px] text-gray-400 font-semibold">{d.nama_kecamatan}</div>
                    </div>
                    {d.stars > 0 && (
                      <span className="flex items-center gap-0.5 text-amber-500 text-[10px] font-bold bg-amber-50 px-2.5 py-0.5 rounded-lg">
                        <Star className="w-3 h-3 fill-current" />
                        {d.stars}
                      </span>
                    )}
                  </button>
                ))}
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Grid: Left Filter (2/12), Center Map (7/12), Right Details (3/12) */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {/* Left Column: Filter Indicators */}
        <div className="lg:col-span-2 flex flex-col gap-6">
          <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-5 space-y-4">
            <div className="flex items-center gap-2 border-b border-gray-100/50 pb-2">
              <Filter className="w-4 h-4 text-indigo-500" />
              <h2 className="text-xs uppercase tracking-wider text-indigo-500 font-extrabold">Filter Indikator</h2>
            </div>

            <div className="space-y-4">
              {/* Filter 1 */}
              <label className="flex items-start gap-3 cursor-pointer group">
                <input
                  type="checkbox"
                  checked={showOnlyWebsite}
                  onChange={(e) => setShowOnlyWebsite(e.target.checked)}
                  className="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/20 w-4 h-4 cursor-pointer"
                />
                <div className="flex-grow select-none">
                  <div className="text-xs font-bold text-gray-700 group-hover:text-gray-950 transition">Website Aktif</div>
                  <div className="text-[10px] text-gray-400">Memiliki URL aktif</div>
                </div>
                <span className="w-2.5 h-2.5 rounded bg-emerald-500 border border-emerald-400 mt-1 shrink-0"></span>
              </label>

              {/* Filter 2 */}
              <label className="flex items-start gap-3 cursor-pointer group">
                <input
                  type="checkbox"
                  checked={showOnlyDb}
                  onChange={(e) => setShowOnlyDb(e.target.checked)}
                  className="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/20 w-4 h-4 cursor-pointer"
                />
                <div className="flex-grow select-none">
                  <div className="text-xs font-bold text-gray-700 group-hover:text-gray-950 transition">Database Penduduk</div>
                  <div className="text-[10px] text-gray-400">Database 'Sudah Ada'</div>
                </div>
                <span className="w-2.5 h-2.5 rounded bg-amber-500/70 border border-amber-400 mt-1 shrink-0"></span>
              </label>

              {/* Filter 3 */}
              <label className="flex items-start gap-3 cursor-pointer group">
                <input
                  type="checkbox"
                  checked={showOnlyClasnet}
                  onChange={(e) => setShowOnlyClasnet(e.target.checked)}
                  className="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500/20 w-4 h-4 cursor-pointer"
                />
                <div className="flex-grow select-none">
                  <div className="text-xs font-bold text-gray-700 group-hover:text-gray-950 transition">Mitra Clasnet</div>
                  <div className="text-[10px] text-gray-400">Pendampingan Clasnet</div>
                </div>
                <span className="w-2.5 h-2.5 rounded bg-sky-400 border border-sky-300 mt-1 shrink-0"></span>
              </label>
            </div>
          </div>

          {/* Legends guide */}
          <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-5 space-y-3 text-xs">
            <h3 className="font-bold text-gray-900 border-b border-gray-50 pb-1.5 uppercase text-[10px] tracking-wider text-gray-400">Panduan Warna</h3>
            <div className="space-y-2 font-semibold text-gray-600">
              <div className="flex items-center gap-2">
                <span className="w-3.5 h-3.5 rounded border border-rose-500 bg-rose-500/20 shadow-sm"></span>
                <span>Belum Ada Website</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="w-3.5 h-3.5 rounded border border-emerald-500 bg-emerald-500/20 shadow-sm"></span>
                <span>Website Aktif</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="w-3.5 h-3.5 rounded border border-sky-500 bg-sky-500/40 shadow-sm"></span>
                <span>Layanan Clasnet</span>
              </div>
              <div className="flex items-center gap-2 border-t border-gray-200 pt-2">
                <span className="w-3.5 h-3.5 rounded border border-indigo-500 bg-indigo-500/70 shadow-sm animate-pulse"></span>
                <span className="text-indigo-600 font-bold">Wilayah Terpilih</span>
              </div>
            </div>
          </div>
        </div>

        {/* Center Column: Interactive Map */}
        <div className="lg:col-span-7 bg-white rounded-3xl premium-shadow border border-gray-100 p-3 relative h-[65vh] min-h-[500px]">
          <div ref={mapRef} className="absolute inset-0 w-full h-full rounded-3xl overflow-hidden"></div>
          
          {/* Map Popup overlay */}
          <div ref={popupRef} className="ol-popup hidden bg-white shadow-xl rounded-2xl border border-gray-100 p-3 min-w-48 text-xs select-none">
            <div className="font-bold text-gray-900">{selectedDesaName}</div>
            <div className="text-gray-500">{selectedKecName}</div>
          </div>
        </div>

        {/* Right Column: Village detail and related news */}
        <div className="lg:col-span-3 flex flex-col gap-6">
          {selectedDesaInfo ? (
            <>
              {/* Village details info */}
              <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-5 space-y-4">
                <div className="flex justify-between items-start">
                  <div>
                    <h3 className="text-sm font-bold text-gray-900 leading-snug">{selectedDesaInfo.nama_desa}</h3>
                    <p className="text-[10px] text-gray-400 font-semibold">{selectedDesaInfo.nama_kecamatan}</p>
                  </div>
                  <button
                    onClick={() => {
                      setSelectedDesaKey('');
                      setSelectedDesaInfo(null);
                    }}
                    className="p-1 hover:bg-gray-100 rounded-full text-gray-400 hover:text-gray-600 transition"
                  >
                    <X className="w-4 h-4" />
                  </button>
                </div>

                <div className="flex items-center gap-1">
                  {Array.from({ length: 3 }).map((_, i) => (
                    <Star
                      key={i}
                      className={`w-3.5 h-3.5 ${
                        i < selectedDesaInfo.stars ? 'fill-amber-500 text-amber-500' : 'text-gray-200'
                      }`}
                    />
                  ))}
                  <span className="text-[10px] text-gray-400 font-bold tracking-wider ml-1">
                    Level {selectedDesaInfo.stars}
                  </span>
                </div>

                <div className="space-y-2 text-xs font-semibold pt-3 border-t border-gray-50 text-gray-600">
                  <div className="flex items-center justify-between py-1 border-b border-gray-50/50">
                    <span className="flex items-center gap-1.5">
                      <Globe className="w-3.5 h-3.5 text-blue-500" /> Website
                    </span>
                    {selectedDesaInfo.alamat_website ? (
                      <a
                        href={selectedDesaInfo.alamat_website}
                        target="_blank"
                        rel="noreferrer"
                        className="text-blue-600 hover:underline font-bold"
                      >
                        Buka
                      </a>
                    ) : (
                      <span className="text-gray-400">Belum ada</span>
                    )}
                  </div>

                  <div className="flex items-center justify-between py-1 border-b border-gray-50/50">
                    <span className="flex items-center gap-1.5">
                      <Users className="w-3.5 h-3.5 text-emerald-500" /> Penduduk
                    </span>
                    <span className="text-gray-900">
                      {selectedDesaInfo.jumlah_penduduk ? selectedDesaInfo.jumlah_penduduk.toLocaleString('id-ID') : '-'}
                    </span>
                  </div>

                  <div className="flex items-center justify-between py-1 border-b border-gray-50/50">
                    <span className="flex items-center gap-1.5">
                      <Database className="w-3.5 h-3.5 text-indigo-500" /> Database
                    </span>
                    <span
                      className={`px-1.5 py-0.5 text-[9px] font-bold rounded-full ${
                        selectedDesaInfo.db_penduduk?.toUpperCase() === 'SUDAH ADA'
                          ? 'bg-emerald-50 text-emerald-700'
                          : 'bg-rose-50 text-rose-700'
                      }`}
                    >
                      {selectedDesaInfo.db_penduduk || 'BELUM'}
                    </span>
                  </div>

                  <div className="flex items-center justify-between py-1">
                    <span className="flex items-center gap-1.5">
                      <Info className="w-3.5 h-3.5 text-cyan-500" /> Developer
                    </span>
                    <span className="text-gray-900 uppercase text-[9px] font-bold truncate max-w-28">
                      {selectedDesaInfo.developer || '-'}
                    </span>
                  </div>
                </div>
              </div>

              {/* Related news cards with graphics */}
              <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-5 space-y-3">
                <div className="flex items-center gap-2 border-b border-gray-50 pb-2">
                  <Newspaper className="w-4 h-4 text-indigo-500" />
                  <h3 className="text-xs uppercase tracking-wider text-indigo-500 font-extrabold">Berita Terkait</h3>
                </div>

                <div className="space-y-3 max-h-[250px] overflow-y-auto pr-1">
                  {loadingNews ? (
                    <div className="text-xs text-gray-400 italic py-1">Memuat berita...</div>
                  ) : relatedNews.length > 0 ? (
                    relatedNews.map((news) => (
                      <Link
                        key={news.id}
                        href={`/kegiatan?id=${news.id}`}
                        className="flex items-center gap-2.5 p-1.5 rounded-2xl hover:bg-indigo-50/50 border border-gray-50 hover:border-indigo-100 transition duration-300 bg-white group"
                      >
                        <div className="w-14 h-11 rounded-lg overflow-hidden bg-gray-50 shrink-0 border border-gray-100 relative">
                          {news.gambar ? (
                            <img src={news.gambar} alt="Thumbnail" className="w-full h-full object-cover group-hover:scale-105 transition" />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                              <Newspaper className="w-3.5 h-3.5" />
                            </div>
                          )}
                        </div>

                        <div className="flex-grow min-w-0">
                          <h4 className="font-extrabold text-[10px] text-gray-800 leading-snug line-clamp-2 group-hover:text-indigo-600 transition">
                            {news.judul}
                          </h4>
                          <span className="text-[8px] text-gray-400 font-semibold">
                            {new Date(news.dibuat_pada).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })}
                          </span>
                        </div>
                        <ChevronRight className="w-3 h-3 text-gray-300 shrink-0 mr-0.5" />
                      </Link>
                    ))
                  ) : (
                    <div className="text-xs text-gray-400 italic py-1">Tidak ada berita terkait.</div>
                  )}
                </div>
              </div>
            </>
          ) : (
            <div className="bg-white rounded-3xl premium-shadow border border-gray-100 p-6 text-center space-y-2">
              <MapPin className="w-8 h-8 text-indigo-400 mx-auto animate-bounce" />
              <h3 className="font-bold text-xs text-gray-800">Pilih Desa di Peta</h3>
              <p className="text-[10px] text-gray-400 leading-relaxed">
                Klik wilayah desa di peta untuk melihat informasi kependudukan, status website, dan dokumentasi berita wilayah secara instan.
              </p>
            </div>
          )}
        </div>

      </div>
    </div>
  );
}
