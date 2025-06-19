/**
 * notification-helper.js
 * Helper untuk mengatasi masalah autoplay audio pada browser modern
 */

// Kelas untuk mengelola pemutaran audio notifikasi
class AudioNotificationManager {
    constructor(options = {}) {
        this.audioElement = options.audioElement || null;
        this.audioContext = null;
        this.audioSource = null;
        this.audioBuffer = null;
        this.audioUrls = options.audioUrls || [];
        this.isPlaying = false;
        this.isInitialized = false;
        this.onPlayCallback = options.onPlay || (() => {});
        this.onErrorCallback = options.onError || (() => {});
        
        // Inisialisasi audio context setelah interaksi pengguna
        document.addEventListener('click', () => this.initAudioContext(), { once: true });
        document.addEventListener('touchstart', () => this.initAudioContext(), { once: true });
    }
    
    /**
     * Inisialisasi AudioContext
     */
    initAudioContext() {
        if (this.isInitialized) return;
        
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (AudioContext) {
                this.audioContext = new AudioContext();
                this.isInitialized = true;
                
                // Preload audio jika URL tersedia
                if (this.audioUrls.length > 0) {
                    this.preloadAudio();
                }
                
                console.log('Audio context initialized');
            }
        } catch (err) {
            console.error('Failed to initialize audio context:', err);
        }
    }
    
    /**
     * Preload audio untuk pemutaran instan
     */
    preloadAudio() {
        if (!this.audioContext) return;
        
        const url = this.audioUrls[0]; // Gunakan URL pertama
        
        fetch(url)
            .then(response => response.arrayBuffer())
            .then(arrayBuffer => this.audioContext.decodeAudioData(arrayBuffer))
            .then(audioBuffer => {
                this.audioBuffer = audioBuffer;
                console.log('Audio preloaded successfully');
            })
            .catch(err => {
                console.error('Error preloading audio:', err);
            });
    }
    
    /**
     * Putar audio menggunakan Web Audio API
     */
    playWebAudio(loop = true) {
        if (!this.audioContext || !this.audioBuffer) {
            console.warn('AudioContext or buffer not initialized');
            return false;
        }
        
        try {
            // Hentikan pemutaran sebelumnya jika ada
            if (this.audioSource) {
                this.audioSource.stop();
            }
            
            // Buat source baru
            this.audioSource = this.audioContext.createBufferSource();
            this.audioSource.buffer = this.audioBuffer;
            this.audioSource.loop = loop;
            this.audioSource.connect(this.audioContext.destination);
            this.audioSource.start(0);
            this.isPlaying = true;
            this.onPlayCallback();
            
            return true;
        } catch (err) {
            console.error('Failed to play audio with Web Audio API:', err);
            this.onErrorCallback(err);
            return false;
        }
    }
    
    /**
     * Putar audio menggunakan HTML5 Audio Element
     */
    playHtmlAudio(loop = true) {
        if (!this.audioElement) {
            console.warn('Audio element not provided');
            return false;
        }
        
        try {
            // Reset audio
            this.audioElement.currentTime = 0;
            this.audioElement.loop = loop;
            this.audioElement.muted = false;
            this.audioElement.volume = 1.0;
            
            const playPromise = this.audioElement.play();
            
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        this.isPlaying = true;
                        this.onPlayCallback();
                    })
                    .catch(err => {
                        console.error('Failed to play audio with HTML5 Audio:', err);
                        this.onErrorCallback(err);
                    });
            }
            
            return true;
        } catch (err) {
            console.error('Error playing HTML5 Audio:', err);
            this.onErrorCallback(err);
            return false;
        }
    }
    
    /**
     * Putar audio dengan metode terbaik yang tersedia
     * @param {boolean} forcePlay - Paksa pemutaran audio (dari interaksi pengguna)
     * @param {boolean} loop - Apakah audio harus diulang
     */
    play(forcePlay = false, loop = true) {
        if (!forcePlay && this.isPlaying) return true;
        
        let success = false;
        
        // Coba gunakan Web Audio API terlebih dahulu jika sudah diinisialisasi & preloaded
        if (this.audioContext && this.audioBuffer) {
            success = this.playWebAudio(loop);
        }
        
        // Jika Web Audio API gagal, gunakan HTML5 Audio
        if (!success && this.audioElement) {
            success = this.playHtmlAudio(loop);
        }
        
        // Jika kedua metode gagal dan forcePlay true, coba inisialisasi ulang AudioContext
        if (!success && forcePlay) {
            if (!this.audioContext) {
                this.initAudioContext();
            }
            
            // Resume AudioContext jika suspended
            if (this.audioContext && this.audioContext.state === 'suspended') {
                this.audioContext.resume()
                    .then(() => {
                        if (this.audioBuffer) {
                            this.playWebAudio(loop);
                        }
                    })
                    .catch(err => console.error('Error resuming AudioContext:', err));
            }
        }
        
        return success;
    }
    
    /**
     * Hentikan pemutaran audio
     */
    stop() {
        if (this.audioSource) {
            try {
                this.audioSource.stop();
            } catch (err) {
                console.warn('Error stopping Web Audio:', err);
            }
        }
        
        if (this.audioElement) {
            try {
                this.audioElement.pause();
                this.audioElement.currentTime = 0;
            } catch (err) {
                console.warn('Error stopping HTML Audio:', err);
            }
        }
        
        this.isPlaying = false;
    }
    
    /**
     * Hentikan pemutaran audio dan hapus sumber daya
     */
    dispose() {
        this.stop();
        
        if (this.audioContext) {
            try {
                this.audioContext.close();
            } catch (err) {
                console.warn('Error closing AudioContext:', err);
            }
        }
        
        this.audioSource = null;
        this.audioBuffer = null;
        this.audioContext = null;
        this.isInitialized = false;
    }
}

// Utilitas untuk menangani notifikasi pada halaman pelacakan pesanan
window.NotificationHelper = {
    // Minta izin notifikasi
    requestPermission() {
        if ('Notification' in window) {
            return Notification.requestPermission();
        }
        return Promise.resolve('denied');
    },
    
    // Buat instance AudioNotificationManager
    createAudioManager(audioElement, audioUrls) {
        return new AudioNotificationManager({
            audioElement,
            audioUrls,
            onPlay: () => console.log('Notification audio playing'),
            onError: (err) => console.error('Notification audio error:', err)
        });
    },
    
    // Tampilkan notifikasi browser
    showNotification(title, options = {}) {
        if ('Notification' in window && Notification.permission === 'granted') {
            try {
                return new Notification(title, {
                    requireInteraction: true,
                    vibrate: [200, 100, 200],
                    ...options
                });
            } catch (err) {
                console.error('Error showing notification:', err);
            }
        }
        return null;
    }
}; 