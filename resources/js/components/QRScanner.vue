<template>
  <div class="qr-scanner">
    <div v-if="!scanning" class="scanner-button text-center">
      <button 
        @click="startScanning" 
        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 flex items-center justify-center mx-auto"
      >
        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M12 12h.01M12 12v.01"></path>
        </svg>
        Scan QR Code
      </button>
    </div>
    
    <div v-if="scanning" class="scanner-view">
      <div class="relative">
        <video 
          ref="video" 
          autoplay 
          class="w-full max-w-md mx-auto rounded-lg"
        ></video>
        <canvas 
          ref="canvas" 
          style="display: none;"
        ></canvas>
        
        <div class="mt-4 text-center">
          <button 
            @click="stopScanning" 
            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
          >
            Stop Scanning
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="error" class="error-message text-red-600 text-center mt-4">
      {{ error }}
    </div>
  </div>
</template>

<script>
export default {
  name: 'QRScanner',
  emits: ['scan'],
  data() {
    return {
      scanning: false,
      error: null,
      stream: null
    }
  },
  methods: {
    async startScanning() {
      try {
        this.error = null;
        this.scanning = true;
        
        // Get camera stream
        this.stream = await navigator.mediaDevices.getUserMedia({ 
          video: { facingMode: 'environment' } 
        });
        
        this.$refs.video.srcObject = this.stream;
        
        // Start scanning for QR codes
        this.scanForQR();
      } catch (err) {
        this.error = 'Camera access denied or not available';
        this.scanning = false;
      }
    },
    
    stopScanning() {
      if (this.stream) {
        this.stream.getTracks().forEach(track => track.stop());
      }
      this.scanning = false;
      this.error = null;
    },
    
    scanForQR() {
      // Simple QR detection - in production, use a proper QR library like jsQR
      const canvas = this.$refs.canvas;
      const video = this.$refs.video;
      const context = canvas.getContext('2d');
      
      const scan = () => {
        if (!this.scanning) return;
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        // For demo purposes, simulate QR detection on click
        // In production, integrate with jsQR or similar library
        
        requestAnimationFrame(scan);
      };
      
      video.addEventListener('loadedmetadata', () => {
        scan();
      });
    },
    
    // Simulate QR code detection (for demo)
    simulateQRDetection(qrData) {
      this.$emit('scan', qrData);
      this.stopScanning();
    }
  },
  
  beforeUnmount() {
    this.stopScanning();
  }
}
</script>

<style scoped>
.qr-scanner {
  max-width: 500px;
  margin: 0 auto;
}

.scanner-view video {
  border: 2px solid #3b82f6;
}
</style>