/**
 * System Initialization Page JavaScript
 * Handles form validation and password visibility toggle
 */

$(document).ready(function() {
	
	// 🔍 调试：页面加载
	console.log('✅ System Init JS loaded');
	console.log('Form found:', $('#jsformInit').length > 0);
	console.log('Current URL:', window.location.href);
	
	// ===========================================
	// Language Switcher
	// ===========================================
	$('#jsLanguageMenu a').on('click', function(e) {
		e.preventDefault();
		var lang = $(this).data('lang');
		var langText = $(this).text();
		
		console.log('🌐 Switching language to:', lang);
		
		// Update button text
		$('#jsCurrentLanguage').text(langText);
		
		// Save to localStorage
		localStorage.setItem('systemInitLanguage', lang);
		
		// Reload page with new language
		var currentUrl = window.location.pathname;
		var separator = currentUrl.includes('?') ? '&' : '?';
		window.location.href = currentUrl + separator + 'language=' + lang;
	});
	
	// Restore language from localStorage
	var savedLang = localStorage.getItem('systemInitLanguage');
	if (savedLang) {
		var langText = $('#jsLanguageMenu a[data-lang="' + savedLang + '"]').text();
		if (langText) {
			$('#jsCurrentLanguage').text(langText);
		}
	}
	
	// ===========================================
	// Theme Settings
	// ===========================================
	
	// Load theme from localStorage
	function loadTheme() {
		var themeMode = localStorage.getItem('themeMode') || 'auto';
		var themeColor = localStorage.getItem('themeColor') || 'blue';
		
		console.log('🎨 Loading theme:', themeMode, themeColor);
		
		// Set theme mode
		$('input[name="theme-mode"][value="' + themeMode + '"]').prop('checked', true);
		applyThemeMode(themeMode);
		
		// Set theme color
		$('input[name="theme-color"][value="' + themeColor + '"]').prop('checked', true);
		applyThemeColor(themeColor);
	}
	
	// Apply theme mode
	function applyThemeMode(mode) {
		var htmlElement = document.documentElement;
		
		if (mode === 'dark') {
			htmlElement.setAttribute('data-bs-theme', 'dark');
		} else if (mode === 'light') {
			htmlElement.setAttribute('data-bs-theme', 'light');
		} else {
			// Auto mode - detect system preference
			if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				htmlElement.setAttribute('data-bs-theme', 'dark');
			} else {
				htmlElement.setAttribute('data-bs-theme', 'light');
			}
		}
		
		console.log('Theme mode applied:', mode);
	}
	
	// Apply theme color
	function applyThemeColor(color) {
		// This would require CSS custom properties to be set
		// For now, just log it
		console.log('Theme color applied:', color);
		// TODO: Implement color switching via CSS variables
	}
	
	// Theme mode change handler
	$('input[name="theme-mode"]').on('change', function() {
		var mode = $(this).val();
		localStorage.setItem('themeMode', mode);
		applyThemeMode(mode);
	});
	
	// Theme color change handler
	$('input[name="theme-color"]').on('change', function() {
		var color = $(this).val();
		localStorage.setItem('themeColor', color);
		applyThemeColor(color);
	});
	
	// Initialize theme
	loadTheme();
	
	// ===========================================
	// Password Visibility Toggle
	// ===========================================
	$('#jstogglePassword').on('click', function(e) {
		e.preventDefault();
		var input = $('#jspassword');
		var type = input.attr('type');
		
		if (type === 'password') {
			input.attr('type', 'text');
			$(this).attr('title', pageL_hide_password || 'Hide password');
		} else {
			input.attr('type', 'password');
			$(this).attr('title', pageL_show_password || 'Show password');
		}
	});
	
	// Confirm Password Visibility Toggle
	$('#jstoggleConfirmPassword').on('click', function(e) {
		e.preventDefault();
		var input = $('#jsconfirmPassword');
		var type = input.attr('type');
		
		if (type === 'password') {
			input.attr('type', 'text');
			$(this).attr('title', pageL_hide_password || 'Hide password');
		} else {
			input.attr('type', 'password');
			$(this).attr('title', pageL_show_password || 'Show password');
		}
	});
	
	// Form Validation
	$('#jsformInit').on('submit', function(e) {
		e.preventDefault(); // 总是阻止默认提交，改用 AJAX
		
		var isValid = true;
		var firstError = null;
		
		console.log('🔍 Form submit triggered');
		
		// Get form values
		var username = $('input[name="username"]').val().trim();
		var password = $('input[name="password"]').val();
		var confirmPassword = $('input[name="confirm_password"]').val();
		
		console.log('📝 Form data:');
		console.log('  Username:', username);
		console.log('  Password length:', password.length);
		console.log('  Confirm password length:', confirmPassword.length);
		console.log('  Passwords match:', password === confirmPassword);
		
		// Remove previous error messages
		$('.invalid-feedback').remove();
		$('.is-invalid').removeClass('is-invalid');
		$('.alert-danger').remove();
		$('.alert-success').remove();
		$('.alert-info').remove();
		
		// Validate Username
		if (!username) {
			showError('input[name="username"]', pageL_username_required);
			isValid = false;
			if (!firstError) firstError = 'input[name="username"]';
			console.error('❌ Validation failed: username_required');
		} else if (!/^[a-zA-Z0-9_-]+$/.test(username)) {
			showError('input[name="username"]', pageL_username_invalid);
			isValid = false;
			if (!firstError) firstError = 'input[name="username"]';
			console.error('❌ Validation failed: username_invalid');
		} else if (username.length < 3 || username.length > 20) {
			showError('input[name="username"]', pageL_username_length);
			isValid = false;
			if (!firstError) firstError = 'input[name="username"]';
			console.error('❌ Validation failed: username_length');
		}
		
		// Validate Password
		if (!password) {
			showError('input[name="password"]', pageL_password_required);
			isValid = false;
			if (!firstError) firstError = 'input[name="password"]';
			console.error('❌ Validation failed: password_required');
		} else if (password.length < 8) {
			showError('input[name="password"]', pageL_password_too_short);
			isValid = false;
			if (!firstError) firstError = 'input[name="password"]';
			console.error('❌ Validation failed: password_too_short');
		} else if (!/[a-zA-Z]/.test(password) || !/[0-9]/.test(password)) {
			showError('input[name="password"]', pageL_password_weak);
			isValid = false;
			if (!firstError) firstError = 'input[name="password"]';
			console.error('❌ Validation failed: password_weak');
		}
		
		// Validate Confirm Password
		if (!confirmPassword) {
			showError('input[name="confirm_password"]', pageL_password_required);
			isValid = false;
			if (!firstError) firstError = 'input[name="confirm_password"]';
			console.error('❌ Validation failed: confirm_password_required');
		} else if (password !== confirmPassword) {
			showError('input[name="confirm_password"]', pageL_password_mismatch);
			isValid = false;
			if (!firstError) firstError = 'input[name="confirm_password"]';
			console.error('❌ Validation failed: password_mismatch');
		}
		
		// If validation failed, prevent submit and focus first error
		if (!isValid) {
			console.error('❌ Form validation failed - submission prevented');
			
			// Show alert at top of form
			showTopAlert('danger', pageL_validation_failed);
			
			if (firstError) {
				$(firstError).focus();
			}
			return false;
		}
		
		console.log('✅ Form validation passed');
		console.log('🚀 Submitting form via AJAX');
		
		// Show loading state
		var btn = $('#jsbtnSubmit');
		var originalText = btn.text();
		btn.addClass('btn-loading').prop('disabled', true).text(pageL_submitting);
		$('input').prop('disabled', true);
		
		// Show info alert
		showTopAlert('info', pageL_submitting_wait);
		
		// Prepare form data
		var formData = new FormData();
		formData.append('username', username);
		formData.append('password', password);
		formData.append('confirm_password', confirmPassword);
		
		// Debug: Log form data being submitted
		console.log('📤 Sending data via AJAX POST:');
		console.log('  username:', username);
		console.log('  password: [' + password.length + ' characters]');
		console.log('  confirm_password: [' + confirmPassword.length + ' characters]');
		
		// Submit via AJAX
		$.ajax({
			url: window.location.pathname,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response, status, xhr) {
				console.log('✅ AJAX success');
				console.log('Response status:', status);
				console.log('HTTP status code:', xhr.status);
				console.log('Response length:', response.length);
				console.log('Response preview:', response.substring(0, 500));
				
				// Check if response contains redirect or success indicators
				if (response.includes('login') || response.includes('Login')) {
					console.log('✅ Success - redirecting to login');
					showTopAlert('success', pageL_success_redirect);
					
					setTimeout(function() {
						window.location.href = '/admin/login';
					}, 2000);
				} else if (response.includes('alert-danger') || response.includes('alert-fail')) {
					console.error('❌ Server returned error');
					showTopAlert('danger', pageL_server_error);
					
					// Try to extract error message
					var tempDiv = $('<div>').html(response);
					var errorMsg = tempDiv.find('.alert-danger, .alert-fail').text();
					if (errorMsg) {
						console.error('Error message:', errorMsg);
						showTopAlert('danger', errorMsg);
					}
					
					// Re-enable form
					btn.removeClass('btn-loading').prop('disabled', false).text(originalText);
					$('input').prop('disabled', false);
				} else {
					console.warn('⚠️ Unexpected response');
					showTopAlert('warning', pageL_unexpected_response);
					
					// Re-enable form
					btn.removeClass('btn-loading').prop('disabled', false).text(originalText);
					$('input').prop('disabled', false);
				}
			},
			error: function(xhr, status, error) {
				console.error('❌ AJAX error');
				console.error('Status:', status);
				console.error('Error:', error);
				console.error('HTTP status code:', xhr.status);
				console.error('Response text:', xhr.responseText);
				
				showTopAlert('danger', pageL_submit_failed + error + ' (HTTP ' + xhr.status + ')');
				
				// Re-enable form
				btn.removeClass('btn-loading').prop('disabled', false).text(originalText);
				$('input').prop('disabled', false);
			}
		});
		
		return false;
	});
	
	// Helper function to show error message
	function showError(inputSelector, message) {
		var input = $(inputSelector);
		input.addClass('is-invalid');
		
		// Find the parent .mb-3 div
		var parent = input.closest('.mb-3');
		if (parent.find('.input-group').length > 0) {
			parent = parent.find('.input-group');
		}
		
		// Add error message
		parent.after('<div class="invalid-feedback d-block">' + message + '</div>');
	}
	
	// Helper function to show alert at top of form
	function showTopAlert(type, message) {
		// Remove existing alerts
		$('.alert').remove();
		
		var iconHtml = '';
		var titleText = '';
		
		if (type === 'danger') {
			iconHtml = '<circle cx="12" cy="12" r="9"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>';
			titleText = pageL_alert_error;
		} else if (type === 'success') {
			iconHtml = '<polyline points="20 6 9 17 4 12"></polyline>';
			titleText = pageL_alert_success;
		} else if (type === 'info') {
			iconHtml = '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>';
			titleText = pageL_alert_info;
		} else if (type === 'warning') {
			iconHtml = '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>';
			titleText = pageL_alert_warning;
		}
		
		var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
			'<div class="d-flex">' +
			'<div>' +
			'<svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">' +
			'<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>' +
			iconHtml +
			'</svg>' +
			'</div>' +
			'<div>' +
			'<h4 class="alert-title">' + titleText + '</h4>' +
			'<div class="text-muted">' + message + '</div>' +
			'</div>' +
			'</div>' +
			'<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
			'</div>';
		
		// Only insert into the first card-body (the form card)
		$('.card-body').first().prepend(alertHtml);
		
		// Scroll to top
		$('.card').first().get(0).scrollIntoView({ behavior: 'smooth', block: 'start' });
	}
	
	// Real-time validation (optional)
	$('input[name="username"]').on('blur', function() {
		var username = $(this).val().trim();
		$(this).removeClass('is-invalid');
		$(this).siblings('.invalid-feedback').remove();
		
		if (username && (!/^[a-zA-Z0-9_-]+$/.test(username) || username.length < 3 || username.length > 20)) {
			$(this).addClass('is-invalid');
		}
	});
	
	$('input[name="confirm_password"]').on('keyup', function() {
		var password = $('input[name="password"]').val();
		var confirmPassword = $(this).val();
		
		$(this).removeClass('is-invalid');
		$('.invalid-feedback').remove();
		
		if (confirmPassword && password !== confirmPassword) {
			$(this).addClass('is-invalid');
		}
	});
	
	// 🔍 页面加载后的状态检查
	setTimeout(function() {
		console.log('📊 Page state after 1 second:');
		console.log('  jQuery version:', $.fn.jquery);
		console.log('  Form exists:', $('#jsformInit').length);
		console.log('  Username field:', $('input[name="username"]').length);
		console.log('  Password field:', $('input[name="password"]').length);
		console.log('  Confirm field:', $('input[name="confirm_password"]').length);
		console.log('  Submit button:', $('#jsbtnSubmit').length);
	}, 1000);
	
});
