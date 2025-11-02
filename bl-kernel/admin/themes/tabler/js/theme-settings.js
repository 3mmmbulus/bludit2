/**
 * Theme Settings Manager
 * Handles theme mode, color scheme, and border radius settings
 */
(function() {
	'use strict';
	
	const THEME_CONFIG = {
		theme: 'light',
		themeBase: 'gray',
		themePrimary: 'blue',
		themeRadius: '1'
	};
	
	const STORAGE_KEYS = {
		theme: 'tabler-theme',
		themeBase: 'tabler-theme-base',
		themePrimary: 'tabler-theme-primary',
		themeRadius: 'tabler-theme-radius'
	};
	
	/**
	 * Get stored theme setting
	 */
	function getStoredTheme(key) {
		return localStorage.getItem(STORAGE_KEYS[key]) || THEME_CONFIG[key];
	}
	
	/**
	 * Set theme setting
	 */
	function setThemeSetting(key, value) {
		localStorage.setItem(STORAGE_KEYS[key], value);
		applyTheme();
	}
	
	/**
	 * Apply current theme settings to document
	 */
	function applyTheme() {
		const theme = getStoredTheme('theme');
		const themeBase = getStoredTheme('themeBase');
		const themePrimary = getStoredTheme('themePrimary');
		const themeRadius = getStoredTheme('themeRadius');
		
		// Set theme attributes
		document.documentElement.setAttribute('data-bs-theme', theme);
		
		if (themeBase !== THEME_CONFIG.themeBase) {
			document.documentElement.setAttribute('data-bs-theme-base', themeBase);
		} else {
			document.documentElement.removeAttribute('data-bs-theme-base');
		}
		
		if (themePrimary !== THEME_CONFIG.themePrimary) {
			document.documentElement.setAttribute('data-bs-theme-primary', themePrimary);
		} else {
			document.documentElement.removeAttribute('data-bs-theme-primary');
		}
		
		if (themeRadius !== THEME_CONFIG.themeRadius) {
			document.documentElement.setAttribute('data-bs-theme-radius', themeRadius);
		} else {
			document.documentElement.removeAttribute('data-bs-theme-radius');
		}
		
		// Update sidebar theme (run after DOM is ready)
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', function() {
				updateSidebarTheme(theme);
			});
		} else {
			updateSidebarTheme(theme);
		}
		
		// Update active radio buttons
		updateRadioButtons();
	}
	
	/**
	 * Update sidebar theme based on color mode
	 */
	function updateSidebarTheme(theme) {
		const sidebar = document.querySelector('.navbar-vertical');
		if (sidebar) {
			if (theme === 'dark') {
				sidebar.setAttribute('data-bs-theme', 'dark');
				sidebar.classList.remove('navbar-light', 'navbar-transparent');
			} else {
				// 浅色模式使用透明导航栏
				sidebar.removeAttribute('data-bs-theme');
				sidebar.classList.add('navbar-light', 'navbar-transparent');
			}
		}
	}
	
	/**
	 * Update radio button states
	 */
	function updateRadioButtons() {
		const theme = getStoredTheme('theme');
		const themePrimary = getStoredTheme('themePrimary');
		const themeRadius = getStoredTheme('themeRadius');
		
		// Update theme mode radios
		document.querySelectorAll('input[name="theme-mode"]').forEach(radio => {
			radio.checked = (radio.value === theme);
		});
		
		// Update color scheme radios
		document.querySelectorAll('input[name="theme-scheme"]').forEach(radio => {
			radio.checked = (radio.value === themePrimary);
		});
		
		// Update border radius radios
		document.querySelectorAll('input[name="theme-radius"]').forEach(radio => {
			const radiusMap = {
				'none': '0',
				'default': '1',
				'large': '2'
			};
			radio.checked = (radiusMap[radio.value] === themeRadius);
		});
	}
	
	/**
	 * Reset theme to defaults
	 */
	function resetTheme() {
		Object.keys(STORAGE_KEYS).forEach(key => {
			localStorage.removeItem(STORAGE_KEYS[key]);
		});
		applyTheme();
	}
	
	/**
	 * Initialize theme settings
	 */
	function initTheme() {
		// Apply theme immediately (before DOM is ready to prevent flash)
		applyTheme();
		
		// Setup event listeners when DOM is ready
		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', setupEventListeners);
		} else {
			setupEventListeners();
		}
	}
	
	/**
	 * Setup event listeners for theme controls
	 */
	function setupEventListeners() {
		// Theme mode change
		document.querySelectorAll('input[name="theme-mode"]').forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					setThemeSetting('theme', this.value);
				}
			});
		});
		
		// Color scheme change
		document.querySelectorAll('input[name="theme-scheme"]').forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					setThemeSetting('themePrimary', this.value);
				}
			});
		});
		
		// Border radius change
		document.querySelectorAll('input[name="theme-radius"]').forEach(radio => {
			radio.addEventListener('change', function() {
				if (this.checked) {
					const radiusMap = {
						'none': '0',
						'default': '1',
						'large': '2'
					};
					setThemeSetting('themeRadius', radiusMap[this.value]);
				}
			});
		});
		
		// Reset button
		const resetBtn = document.getElementById('theme-reset');
		if (resetBtn) {
			resetBtn.addEventListener('click', function() {
				const confirmMsg = this.getAttribute('data-confirm') || 'Are you sure you want to reset to default theme settings?';
				if (confirm(confirmMsg)) {
					resetTheme();
				}
			});
		}
		
		// Handle theme toggle from toolbar (if exists)
		document.querySelectorAll('[data-theme-toggle]').forEach(toggle => {
			toggle.addEventListener('click', function(e) {
				e.preventDefault();
				const newTheme = this.getAttribute('data-theme-toggle');
				setThemeSetting('theme', newTheme);
			});
		});
		
		// Re-apply theme after Offcanvas is shown (ensures radio buttons are updated)
		const offcanvasElement = document.getElementById('offcanvasTheme');
		if (offcanvasElement) {
			offcanvasElement.addEventListener('shown.bs.offcanvas', function () {
				updateRadioButtons();
			});
		}
	}
	
	// Initialize immediately
	initTheme();
	
	// Export for external use
	window.themeSettings = {
		getTheme: () => getStoredTheme('theme'),
		setTheme: (theme) => setThemeSetting('theme', theme),
		reset: resetTheme,
		apply: applyTheme
	};
	
})();

/**
 * Navigation State Persistence
 * 保存和恢复侧边栏菜单展开状态
 */
(function() {
	'use strict';
	
	const NAV_STATE_KEY = 'maigewan_nav_state';
	
	/**
	 * 保存导航状态到 localStorage
	 */
	function saveNavState() {
		const expandedMenus = [];
		document.querySelectorAll('.navbar-nav .nav-item.dropdown').forEach(item => {
			const navId = item.getAttribute('data-nav-id');
			const dropdownMenu = item.querySelector('.dropdown-menu');
			
			if (navId && dropdownMenu && dropdownMenu.classList.contains('show')) {
				expandedMenus.push(navId);
			}
		});
		
		try {
			localStorage.setItem(NAV_STATE_KEY, JSON.stringify(expandedMenus));
		} catch (e) {
			console.warn('Failed to save nav state:', e);
		}
	}
	
	/**
	 * 从 localStorage 恢复导航状态
	 */
	function restoreNavState() {
		try {
			const saved = localStorage.getItem(NAV_STATE_KEY);
			if (!saved) return;
			
			const expandedMenus = JSON.parse(saved);
			
			expandedMenus.forEach(navId => {
				const navItem = document.querySelector(`[data-nav-id="${navId}"]`);
				if (!navItem) return;
				
				const toggle = navItem.querySelector('.dropdown-toggle');
				const menu = navItem.querySelector('.dropdown-menu');
				
				// 如果菜单已经展开（服务器端渲染），跳过
				if (menu && menu.classList.contains('show')) {
					return;
				}
				
				// 只展开未展开的菜单
				if (toggle && menu) {
					// 使用 Bootstrap Dropdown API 展开菜单
					if (window.bootstrap && window.bootstrap.Dropdown) {
						const bsDropdown = bootstrap.Dropdown.getOrCreateInstance(toggle);
						bsDropdown.show();
					}
				}
			});
		} catch (e) {
			console.warn('Failed to restore nav state:', e);
		}
	}
	
	/**
	 * 初始化导航状态管理
	 */
	function initNavPersistence() {
		// 预初始化所有 dropdown，避免首次点击时的闪动
		const dropdownToggles = document.querySelectorAll('.navbar-nav .dropdown-toggle');
		dropdownToggles.forEach(toggle => {
			if (window.bootstrap && window.bootstrap.Dropdown) {
				// 预创建 Dropdown 实例
				bootstrap.Dropdown.getOrCreateInstance(toggle);
			}
			
			// 监听显示/隐藏事件
			toggle.addEventListener('shown.bs.dropdown', saveNavState);
			toggle.addEventListener('hidden.bs.dropdown', saveNavState);
		});
		
		// 页面加载时恢复状态
		restoreNavState();
	}
	
	// 在 DOM 加载完成后初始化
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initNavPersistence);
	} else {
		initNavPersistence();
	}
	
})();
