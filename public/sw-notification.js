// sw-notification.js - Service Worker untuk notifikasi pesanan selesai
const CACHE_NAME = 'kedai-coffee-notification-cache-v2'; // Versi baru
const urlsToCache = [
  '/audio/order-completed.mp3',
  '/audio/order-completed.wav',
  '/images/logo.png',
  '/js/notification-helper.js'
];

// Flag untuk melacak aktivitas
let isActive = false;

// Saat service worker diinstal
self.addEventListener('install', function(event) {
  console.log('[Service Worker] Installed');
  
  // Aktifkan service worker segera tanpa menunggu tab lama ditutup
  self.skipWaiting();
  
  // Cache aset yang dibutuhkan
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        console.log('[Service Worker] Caching files');
        return cache.addAll(urlsToCache);
      })
  );
});

// Saat service worker diaktifkan
self.addEventListener('activate', function(event) {
  console.log('[Service Worker] Activated');
  
  // Ambil kendali semua klien yang belum dikontrol
  event.waitUntil(clients.claim());
  
  // Hapus cache lama
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames
          .filter(function(cacheName) {
            return cacheName !== CACHE_NAME && cacheName.startsWith('kedai-coffee-notification-');
          })
          .map(function(cacheName) {
            console.log('[Service Worker] Deleting old cache:', cacheName);
            return caches.delete(cacheName);
          })
      );
    })
  );

  // Tandai service worker sebagai aktif
  isActive = true;
});

// Data state untuk melacak pesanan yang selesai
let completedOrders = {};
let notificationTimeout = {};

// Ketika menerima pesan dari halaman utama
self.addEventListener('message', function(event) {
  console.log('[Service Worker] Menerima pesan:', event.data);
  
  if (event.data && event.data.type === 'ORDER_COMPLETED') {
    const orderNumber = event.data.orderNumber;
    
    // Catat pesanan yang sudah selesai
    completedOrders[orderNumber] = true;
    
    // Tampilkan notifikasi dalam 1 detik untuk memberikan waktu service worker diinisialisasi
    setTimeout(() => {
      showCompletedNotification(orderNumber);
    }, 1000);
    
    // Atur pengingat berkala saat aplikasi dalam kondisi background
    scheduleReminderNotification(orderNumber);
    
    // Kirim konfirmasi ke klien
    if (event.source) {
      event.source.postMessage({
        type: 'NOTIFICATION_SCHEDULED',
        orderNumber: orderNumber,
        timestamp: Date.now()
      });
    }
  }
  
  // Ketika halaman akan ditutup
  else if (event.data && event.data.type === 'PAGE_UNLOADING') {
    const orderNumber = event.data.orderNumber;
    const isCompleted = event.data.completed;
    
    if (isCompleted) {
      completedOrders[orderNumber] = true;
      
      // Tampilkan notifikasi saat halaman ditutup
      setTimeout(() => {
        showCompletedNotification(orderNumber);
      }, 1000);
      
      // Atur pengingat untuk pesanan yang selesai
      scheduleReminderNotification(orderNumber);
    }
  }
  
  // Periksa apakah service worker masih aktif
  else if (event.data && event.data.type === 'CHECK_ACTIVE') {
    if (event.source) {
      event.source.postMessage({
        type: 'SERVICE_WORKER_STATUS',
        isActive: isActive
      });
    }
  }
});

// Fungsi untuk menampilkan notifikasi pesanan selesai
function showCompletedNotification(orderNumber) {
  const title = 'Pesanan Anda Siap!';
  const options = {
    body: `Pesanan #${orderNumber} telah selesai dan siap diambil!`,
    icon: '/images/logo.png',
    badge: '/images/logo-small.png', // Untuk notifikasi di mobile
    tag: `order-completed-${orderNumber}`,
    renotify: true,
    requireInteraction: true,
    vibrate: [200, 100, 200, 100, 200], // Pola getar untuk menandai urgensi
    actions: [
      {
        action: 'view',
        title: 'Lihat Pesanan'
      },
      {
        action: 'dismiss',
        title: 'Tutup'
      }
    ],
    data: {
      orderNumber: orderNumber,
      timestamp: Date.now()
    }
  };

  // Cek apakah kita memiliki izin untuk menampilkan notifikasi
  self.registration.showNotification(title, options)
    .then(() => {
      console.log(`[Service Worker] Notifikasi telah ditampilkan untuk pesanan ${orderNumber}`);
      
      // Simpan sementara di IndexedDB atau cache untuk pemulihan
      saveNotificationState(orderNumber);
    })
    .catch(err => {
      console.error(`[Service Worker] Error menampilkan notifikasi: ${err}`);
    });
}

// Simpan status notifikasi untuk pemulihan
function saveNotificationState(orderNumber) {
  // Gunakan Cache API untuk menyimpan status notifikasi
  caches.open('notification-state').then(cache => {
    const stateBlob = new Blob([JSON.stringify({
      orderNumber: orderNumber,
      timestamp: Date.now(),
      completed: true
    })], { type: 'application/json' });
    
    cache.put(`/notification-state/${orderNumber}`, new Response(stateBlob));
  });
}

// Fungsi untuk menjadwalkan notifikasi pengingat
function scheduleReminderNotification(orderNumber) {
  // Batalkan notifikasi sebelumnya jika ada
  if (notificationTimeout[orderNumber]) {
    clearTimeout(notificationTimeout[orderNumber]);
  }
  
  // Jadwalkan notifikasi pengingat setiap 60 detik
  notificationTimeout[orderNumber] = setInterval(() => {
    // Cek apakah pesanan masih dicatat sebagai selesai
    if (completedOrders[orderNumber]) {
      showCompletedNotification(orderNumber);
    } else {
      clearInterval(notificationTimeout[orderNumber]);
      delete notificationTimeout[orderNumber];
    }
  }, 60000); // 60 detik
}

// Ketika notifikasi diklik
self.addEventListener('notificationclick', function(event) {
  console.log('[Service Worker] Notification click received', event.notification);
  
  const orderNumber = event.notification.data?.orderNumber || '';
  
  // Tutup notifikasi
  event.notification.close();
  
  // Tindakan berdasarkan tombol yang diklik
  if (event.action === 'view' || !event.action) {
    // Buka halaman tracking dengan parameter hash untuk trigger audio
    const trackUrl = `/track/${orderNumber}#notification`;
    
    // Cari dan fokus ke client/tab yang sudah terbuka
    const urlToOpen = new URL(trackUrl, self.location.origin).href;
    
    event.waitUntil(
      clients.matchAll({
        type: 'window',
        includeUncontrolled: true
      })
      .then(function(clientList) {
        // Periksa semua klien terbuka
        for (const client of clientList) {
          // Cocokan berdasarkan path URL (lebih fleksibel)
          const url = new URL(client.url);
          if (url.pathname.includes(`/track/${orderNumber}`)) {
            // Kirim pesan ke client agar memainkan suara
            client.postMessage({
              type: 'NOTIFICATION_CLICKED',
              orderNumber: orderNumber,
              timestamp: Date.now()
            });
            return client.focus();
          }
        }
        
        // Jika tidak ada, buka tab baru
        return clients.openWindow(urlToOpen).then(newClient => {
          if (newClient) {
            // Tunggu hingga tab baru siap, lalu kirim pesan
            setTimeout(() => {
              newClient.postMessage({
                type: 'NOTIFICATION_CLICKED',
                orderNumber: orderNumber,
                timestamp: Date.now()
              });
            }, 2000);
          }
          return newClient;
        });
      })
    );
  } else if (event.action === 'dismiss') {
    // Bersihkan atau kurangi frekuensi reminder notifikasi
    const lastNotification = Date.now();
    if (notificationTimeout[orderNumber]) {
      clearInterval(notificationTimeout[orderNumber]);
      notificationTimeout[orderNumber] = setTimeout(() => {
        scheduleReminderNotification(orderNumber);
      }, 300000); // Tunda reminder selama 5 menit (300000 ms) jika user menutupnya
    }
  }
});

// Strategi cache untuk permintaan fetch
self.addEventListener('fetch', function(event) {
  // Untuk permintaan ke file audio, gunakan strategi cache-first
  if (event.request.url.includes('/audio/')) {
    event.respondWith(
      caches.match(event.request)
        .then(function(response) {
          if (response) {
            return response;
          }
          
          // Jika tidak ditemukan di cache, ambil dari jaringan
          return fetch(event.request).then(
            function(response) {
              // Jika respons tidak valid, kembalikan saja
              if(!response || response.status !== 200) {
                return response;
              }
              
              // Clone respons karena body hanya bisa digunakan sekali
              var responseToCache = response.clone();
              
              caches.open(CACHE_NAME)
                .then(function(cache) {
                  cache.put(event.request, responseToCache);
                });
              
              return response;
            }
          );
        })
    );
  } else if (event.request.url.includes('/js/notification-helper.js')) {
    // Cache notification helper juga dengan strategi yang sama
    event.respondWith(
      caches.match(event.request)
        .then(function(response) {
          return response || fetch(event.request).then(function(response) {
            if (!response || response.status !== 200) return response;
            
            const responseToCache = response.clone();
            caches.open(CACHE_NAME).then(function(cache) {
              cache.put(event.request, responseToCache);
            });
            
            return response;
          });
        })
    );
  }
}); 