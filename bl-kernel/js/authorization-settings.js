/**
 * Authorization Settings JavaScript
 * Version: 2024-11-03 - Fixed user_identity field validation
 */
(function() {
    'use strict';
    
    console.log('Authorization Settings JS loaded - Version 2024-11-03');
    
    // ====================
    // Toast Notification Functions
    // ====================
    
    /**
     * 显示提示消息
     * @param {string} message 消息内容
     * @param {string} type 类型：success, error, info, warning
     * @param {number} duration 持续时间（毫秒）
     */
    function showToast(message, type = 'info', duration = 3000) {
        // 创建 toast 容器（如果不存在）
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
            document.body.appendChild(container);
        }
        
        // 创建 toast 元素
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show`;
        toast.style.cssText = 'min-width: 250px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 10px;';
        toast.setAttribute('role', 'alert');
        
        const iconMap = {
            success: '✓',
            error: '✗',
            info: 'ℹ',
            warning: '⚠'
        };
        
        toast.innerHTML = `
            <strong>${iconMap[type] || 'ℹ'}</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        container.appendChild(toast);
        
        // 自动移除
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 150);
        }, duration);
    }
    
    /**
     * 显示成功消息
     */
    function showSuccessMessage(message) {
        showToast(message, 'success', 2000);
    }
    
    /**
     * 显示错误消息
     */
    function showErrorMessage(message) {
        showToast(message, 'error', 3000);
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        
        // ====================
        // IP Refresh Functionality
        // ====================
        const refreshBtn = document.getElementById('refresh-ip');
        const serverIpInput = document.getElementById('server-ip');
        
        if (refreshBtn && serverIpInput) {
            refreshBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Add loading state
                const icon = this.querySelector('i');
                if (!icon) return;
                
                this.disabled = true;
                icon.classList.add('rotating');
                
                // AJAX request to get server IP
                fetch(HTML_PATH_ADMIN_ROOT + 'ajax/get-server-ip', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success' && data.ip) {
                        serverIpInput.value = data.ip;
                        
                        // 显示成功提示
                        showSuccessMessage(window.authL10n?.ipRefreshed || 'IP address refreshed');
                        
                        // 显示成功反馈
                        serverIpInput.classList.add('is-valid');
                        setTimeout(() => {
                            serverIpInput.classList.remove('is-valid');
                        }, 2000);
                        
                        // 可选：控制台输出详细信息（调试用）
                        if (data.details) {
                            console.log('IP检测详情:', data.details);
                        }
                    } else {
                        throw new Error(data.message || 'Failed to get server IP');
                    }
                })
                .catch(error => {
                    console.error('Error fetching server IP:', error);
                    
                    // 显示错误提示
                    showErrorMessage(window.authL10n?.ipRefreshError || 'Failed to refresh IP');
                    
                    serverIpInput.classList.add('is-invalid');
                    setTimeout(() => {
                        serverIpInput.classList.remove('is-invalid');
                    }, 2000);
                })
                .finally(() => {
                    this.disabled = false;
                    icon.classList.remove('rotating');
                });
            });
        }
        
        // ====================
        // Form Validation
        // ====================
        const form = document.getElementById('license-form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const userIdentity = form.querySelector('[name="user_identity"]');
                const licenseCode = form.querySelector('[name="license_code"]');
                const termsAgree = form.querySelector('#terms-agree');
                
                console.log('Form submit - userIdentity field found:', !!userIdentity);
                console.log('Form submit - userIdentity value:', userIdentity ? userIdentity.value : 'FIELD NOT FOUND');
                console.log('Form submit - licenseCode value:', licenseCode ? licenseCode.value : 'FIELD NOT FOUND');
                console.log('Form submit - termsAgree checked:', termsAgree ? termsAgree.checked : 'FIELD NOT FOUND');
                
                let isValid = true;
                let errorMessage = '';
                
                // Validate terms agreement FIRST (most common mistake)
                if (termsAgree && !termsAgree.checked) {
                    isValid = false;
                    errorMessage = window.authL10n?.errorTerms || 'Please agree to the terms';
                    termsAgree.parentElement.classList.add('is-invalid');
                    termsAgree.focus();
                }
                
                // Validate user identity (username or email)
                if (!userIdentity || !userIdentity.value.trim()) {
                    isValid = false;
                    errorMessage = window.authL10n?.errorUserIdentity || 'Please enter username or email';
                    console.error('Validation failed: user_identity is empty');
                    if (userIdentity) {
                        userIdentity.classList.add('is-invalid');
                        if (!termsAgree || termsAgree.checked) {
                            userIdentity.focus();
                        }
                    }
                } else if (userIdentity) {
                    userIdentity.classList.remove('is-invalid');
                }
                
                // Validate license code
                if (!licenseCode || !licenseCode.value.trim()) {
                    isValid = false;
                    errorMessage = window.authL10n?.errorLicenseCode || 'Please enter license code';
                    if (licenseCode) {
                        licenseCode.classList.add('is-invalid');
                        if (!termsAgree || termsAgree.checked) {
                            if (userIdentity && userIdentity.value.trim()) {
                                licenseCode.focus();
                            }
                        }
                    }
                } else if (licenseCode.value.trim().length < 5) {
                    isValid = false;
                    errorMessage = window.authL10n?.errorLicenseMin || 'License code must be at least 5 characters';
                    if (licenseCode) {
                        licenseCode.classList.add('is-invalid');
                        licenseCode.focus();
                    }
                } else if (licenseCode.value.trim().length > 200) {
                    isValid = false;
                    errorMessage = window.authL10n?.errorLicenseMax || 'License code cannot exceed 200 characters';
                    if (licenseCode) {
                        licenseCode.classList.add('is-invalid');
                        licenseCode.focus();
                    }
                } else if (licenseCode) {
                    licenseCode.classList.remove('is-invalid');
                }
                
                // If validation fails, prevent form submission and show error
                if (!isValid) {
                    e.preventDefault();
                    e.stopPropagation();
                    showErrorMessage(errorMessage);
                    return false;
                }
                
                // Show progress bar
                const progressBar = document.getElementById('auth-progress');
                if (progressBar) {
                    progressBar.style.display = 'block';
                }
                
                // Add loading state to submit button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>处理中...';
                    
                    // Note: Progress bar will be hidden automatically on page reload
                    // If submission fails, re-enable button and hide progress
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        if (progressBar) {
                            progressBar.style.display = 'none';
                        }
                    }, 10000); // 10 seconds timeout
                }
            });
            
            // Remove invalid class on input
            const inputs = form.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
            
            // Remove invalid class when checkbox is checked
            const termsCheckbox = form.querySelector('#terms-agree');
            if (termsCheckbox) {
                termsCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        this.parentElement.classList.remove('is-invalid');
                    }
                });
            }
        }
        
        // ====================
        // Auto-dismiss alerts
        // ====================
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                const closeBtn = alert.querySelector('.btn-close');
                if (closeBtn) {
                    closeBtn.click();
                }
            }, 5000);
        });
        
        console.log('Authorization Settings initialized');
    });
})();
