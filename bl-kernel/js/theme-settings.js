/**
 * MAIGEWAN Theme Settings
 * Manages backend theme customization (color mode, scheme, radius)
 * Settings are saved to localStorage for persistence
 */

(function() {
	'use strict';
	
	// Default theme configuration
	const DEFAULT_SETTINGS = {
		mode: 'light',        // light | dark
		scheme: 'blue',       // blue | azure | indigo | purple | pink | red | orange | yellow | lime | green | teal | cyan
		radius: 'default'     // none | default | large
	};
	
	// Storage key
	const STORAGE_KEY = 'maigewan_theme_settings';
	
	/**
	 * Get saved theme settings from localStorage
	 */
	function getSettings() {
		try {
			const saved = localStorage.getItem(STORAGE_KEY);
			return saved ? JSON.parse(saved) : DEFAULT_SETTINGS;
		} catch (e) {
			console.warn('Failed to load theme settings:', e);
			return DEFAULT_SETTINGS;
		}
	}
	
	/**
	 * Save theme settings to localStorage
	 */
	function saveSettings(settings) {
		try {
			localStorage.setItem(STORAGE_KEY, JSON.stringify(settings));
		} catch (e) {
			console.error('Failed to save theme settings:', e);
		}
	}
	
	/**
	 * Apply theme settings to the document
	 */
	function applySettings(settings) {
		const body = document.body;
		
		// Apply color mode
		if (settings.mode === 'dark') {
			body.setAttribute('data-bs-theme', 'dark');
		} else {
			body.removeAttribute('data-bs-theme');
		}
		
		// Apply color scheme (via CSS custom property)
		const root = document.documentElement;
		root.setAttribute('data-theme-scheme', settings.scheme);
		
		// Apply border radius
		root.setAttribute('data-theme-radius', settings.radius);
		
		// Update radio button states
		updateRadioStates(settings);
	}
	
	/**
	 * Update radio button checked states based on settings
	 */
	function updateRadioStates(settings) {
		// Mode
		const modeRadios = document.querySelectorAll('input[name="theme-mode"]');
		modeRadios.forEach(radio => {
			radio.checked = (radio.value === settings.mode);
		});
		
		// Scheme
		const schemeRadios = document.querySelectorAll('input[name="theme-scheme"]');
		schemeRadios.forEach(radio => {
			radio.checked = (radio.value === settings.scheme);
		});
		
		// Radius
		const radiusRadios = document.querySelectorAll('input[name="theme-radius"]');
		radiusRadios.forEach(radio => {
			radio.checked = (radio.value === settings.radius);
		});
	}
	
	/**
	 * Initialize theme settings on page load
	 */
	function init() {
		// Load and apply saved settings
		const settings = getSettings();
		applySettings(settings);
		
		// Listen for mode changes
		const modeRadios = document.querySelectorAll('input[name="theme-mode"]');
		modeRadios.forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					const settings = getSettings();
					settings.mode = this.value;
					saveSettings(settings);
					applySettings(settings);
				}
			});
		});
		
		// Listen for scheme changes
		const schemeRadios = document.querySelectorAll('input[name="theme-scheme"]');
		schemeRadios.forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					const settings = getSettings();
					settings.scheme = this.value;
					saveSettings(settings);
					applySettings(settings);
				}
			});
		});
		
		// Listen for radius changes
		const radiusRadios = document.querySelectorAll('input[name="theme-radius"]');
		radiusRadios.forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					const settings = getSettings();
					settings.radius = this.value;
					saveSettings(settings);
					applySettings(settings);
				}
			});
		});
		
		// Reset button
		const resetBtn = document.getElementById('theme-reset');
		if (resetBtn) {
			resetBtn.addEventListener('click', function() {
				if (confirm('Reset theme settings to default?')) {
					saveSettings(DEFAULT_SETTINGS);
					applySettings(DEFAULT_SETTINGS);
				}
			});
		}
	}
	
	// Initialize when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
	
})();
