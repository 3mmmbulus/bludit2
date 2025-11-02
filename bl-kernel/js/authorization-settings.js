/**
 * Authorization Settings JavaScript
 */
(function() {
    'use strict';
    
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
                fetch('/bl-kernel/ajax/get-server-ip.php', {
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
                        
                        // Show success feedback
                        serverIpInput.classList.add('is-valid');
                        setTimeout(() => {
                            serverIpInput.classList.remove('is-valid');
                        }, 2000);
                    } else {
                        throw new Error(data.message || 'Failed to get server IP');
                    }
                })
                .catch(error => {
                    console.error('Error fetching server IP:', error);
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
        const form = document.querySelector('form');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const username = form.querySelector('[name="username"]');
                const licenseCode = form.querySelector('[name="license_code"]');
                const termsAgree = form.querySelector('#terms-agree');
                
                let isValid = true;
                let errorMessage = '';
                
                // Validate username
                if (!username || !username.value.trim()) {
                    isValid = false;
                    errorMessage = '请填写用户名';
                    if (username) {
                        username.classList.add('is-invalid');
                    }
                } else if (username) {
                    username.classList.remove('is-invalid');
                }
                
                // Validate license code
                if (!licenseCode || !licenseCode.value.trim()) {
                    isValid = false;
                    errorMessage = '请填写授权码';
                    if (licenseCode) {
                        licenseCode.classList.add('is-invalid');
                    }
                } else if (licenseCode.value.trim().length < 16) {
                    isValid = false;
                    errorMessage = '授权码长度不能少于16个字符';
                    if (licenseCode) {
                        licenseCode.classList.add('is-invalid');
                    }
                } else if (licenseCode) {
                    licenseCode.classList.remove('is-invalid');
                }
                
                // Validate terms agreement
                if (termsAgree && !termsAgree.checked) {
                    isValid = false;
                    errorMessage = '请同意服务条款';
                }
                
                // If validation fails, prevent form submission and show error
                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
                
                // Add loading state to submit button
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>处理中...';
                    
                    // Re-enable after 5 seconds (in case of error)
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
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
