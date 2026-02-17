// resources/js/network-detector.js

/**
 * Network Detection and Adaptive Streaming
 * Detects user's network speed and sends it to the server
 */

class NetworkDetector {
    constructor() {
        this.networkStrength = 'medium';
        this.connection = null;
        this.init();
    }

    init() {
        // Get Network Information API if available
        this.connection = navigator.connection || 
                         navigator.mozConnection || 
                         navigator.webkitConnection;

        if (this.connection) {
            // Detect initial network strength
            this.detectNetworkStrength();

            // Listen for changes
            this.connection.addEventListener('change', () => {
                this.detectNetworkStrength();
                this.notifyServer();
            });
        } else {
            // Fallback: Estimate based on page load time
            this.estimateFromLoadTime();
        }

        // Store in session storage
        this.saveToStorage();

        // Send to server
        this.notifyServer();
    }

    detectNetworkStrength() {
        if (!this.connection) {
            return;
        }

        // Check effective type (4g, 3g, 2g, slow-2g)
        if (this.connection.effectiveType) {
            this.networkStrength = this.connection.effectiveType;
        } 
        // Fallback to downlink speed (Mbps)
        else if (this.connection.downlink !== undefined) {
            const downlink = this.connection.downlink;
            
            if (downlink > 10) {
                this.networkStrength = '5g';
            } else if (downlink > 5) {
                this.networkStrength = '4g';
            } else if (downlink > 1.5) {
                this.networkStrength = '3g';
            } else {
                this.networkStrength = '2g';
            }
        }

        console.log('Network strength detected:', this.networkStrength);
        return this.networkStrength;
    }

    estimateFromLoadTime() {
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
        
        if (loadTime < 1000) {
            this.networkStrength = 'fast';
        } else if (loadTime < 3000) {
            this.networkStrength = 'medium';
        } else {
            this.networkStrength = 'slow';
        }
    }

    getNetworkInfo() {
        if (!this.connection) {
            return null;
        }

        return {
            effectiveType: this.connection.effectiveType,
            downlink: this.connection.downlink,
            rtt: this.connection.rtt,
            saveData: this.connection.saveData,
        };
    }

    saveToStorage() {
        try {
            sessionStorage.setItem('networkStrength', this.networkStrength);
            
            const networkInfo = this.getNetworkInfo();
            if (networkInfo) {
                sessionStorage.setItem('networkInfo', JSON.stringify(networkInfo));
            }
        } catch (e) {
            console.error('Error saving network info:', e);
        }
    }

    notifyServer() {
        // Send network strength to server via Livewire
        if (typeof Livewire !== 'undefined') {
            Livewire.emit('networkStrengthChanged', this.networkStrength);
        }

        // Also set as default header for all AJAX requests
        this.setDefaultHeaders();
    }

    setDefaultHeaders() {
        // Set header for Axios if available
        if (typeof axios !== 'undefined') {
            axios.defaults.headers.common['X-Network-Strength'] = this.networkStrength;
        }

        // Set header for Fetch API (monkey patch)
        const originalFetch = window.fetch;
        window.fetch = (...args) => {
            if (args[1]) {
                args[1].headers = {
                    ...args[1].headers,
                    'X-Network-Strength': this.networkStrength,
                };
            } else {
                args[1] = {
                    headers: {
                        'X-Network-Strength': this.networkStrength,
                    },
                };
            }
            return originalFetch(...args);
        };
    }

    getNetworkStrength() {
        return this.networkStrength;
    }

    isSlowNetwork() {
        return ['slow-2g', '2g', 'slow'].includes(this.networkStrength);
    }

    isFastNetwork() {
        return ['4g', '5g', 'fast'].includes(this.networkStrength);
    }

    shouldLoadHighQuality() {
        return this.isFastNetwork();
    }

    shouldLoadLowQuality() {
        return this.isSlowNetwork();
    }

    /**
     * Get recommended video quality
     */
    getRecommendedVideoQuality() {
        const qualityMap = {
            'slow-2g': 'low',
            '2g': 'low',
            'slow': 'low',
            '3g': 'medium',
            'medium': 'medium',
            '4g': 'high',
            '5g': 'high',
            'fast': 'high',
        };

        return qualityMap[this.networkStrength] || 'medium';
    }

    /**
     * Get recommended image quality
     */
    getRecommendedImageQuality() {
        const qualityMap = {
            'slow-2g': 'auto:low',
            '2g': 'auto:low',
            'slow': 'auto:low',
            '3g': 'auto:good',
            'medium': 'auto:good',
            '4g': 'auto:best',
            '5g': 'auto:best',
            'fast': 'auto:best',
        };

        return qualityMap[this.networkStrength] || 'auto:good';
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.networkDetector = new NetworkDetector();
    });
} else {
    window.networkDetector = new NetworkDetector();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NetworkDetector;
}