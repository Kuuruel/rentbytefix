(function () {
  'use strict';

  const DEBUG = false;
  function log(...args) { if (DEBUG) console.log('[user.js]', ...args); }

  function safe(id) { return document.getElementById(id) || null; }
  function qAll(sel) { return Array.from(document.querySelectorAll(sel || '')) || []; }

  function escapeHtml(s) {
    return String(s || '').replace(/[&<>"'`=\/]/g, function (c) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '/': '&#47;',
        '`': '&#96;',
        '=': '&#61;'
      }[c];
    });
  }

  function showNotification(message, type = 'success') {
    document.querySelectorAll('.notification-toast').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = 'notification-toast';
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        width: 380px;
        max-width: calc(100vw - 40px);
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
    `;
    
    const colors = {
        success: {
            bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            shadow: '0 10px 25px rgba(16, 185, 129, 0.3)',
            icon: 'ph:check-circle-fill'
        },
        error: {
            bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
            shadow: '0 10px 25px rgba(239, 68, 68, 0.3)',
            icon: 'ph:warning-circle-fill'
        }
    };
    
    const config = colors[type] || colors.success;
    
    notification.innerHTML = `
        <div style="
            background: ${config.bg};
            border-radius: 12px;
            box-shadow: ${config.shadow};
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
        ">
            <div style="padding: 16px;">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="
                        width: 32px;
                        height: 32px;
                        background: rgba(255, 255, 255, 0.2);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-shrink: 0;
                        margin-top: 2px;
                    ">
                        <iconify-icon icon="${config.icon}" style="
                            font-size: 18px;
                            color: white;
                        "></iconify-icon>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <h4 style="
                            color: white;
                            font-weight: 600;
                            font-size: 14px;
                            margin: 0 0 4px 0;
                            line-height: 1.2;
                        ">${type === 'success' ? 'Success!' : 'Error!'}</h4>
                        <p style="
                            color: rgba(255, 255, 255, 0.9);
                            font-size: 13px;
                            margin: 0;
                            line-height: 1.4;
                        ">${message}</p>
                    </div>
                    <button onclick="this.closest('.notification-toast').remove()" style="
                        background: rgba(255, 255, 255, 0.1);
                        border: none;
                        color: rgba(255, 255, 255, 0.7);
                        width: 24px;
                        height: 24px;
                        border-radius: 4px;
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s;
                        flex-shrink: 0;
                    " onmouseover="this.style.background='rgba(255,255,255,0.2)'; this.style.color='white'" 
                       onmouseout="this.style.background='rgba(255,255,255,0.1)'; this.style.color='rgba(255,255,255,0.7)'">
                        <iconify-icon icon="ph:x" style="font-size: 14px;"></iconify-icon>
                    </button>
                </div>
            </div>
            <div style="
                height: 3px;
                background: rgba(255, 255, 255, 0.3);
            ">
                <div class="notification-progress" style="
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    width: 100%;
                    transition: width 4s linear;
                "></div>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 10);

    const progressBar = notification.querySelector('.notification-progress');
    if (progressBar) {
        setTimeout(() => {
            progressBar.style.width = '0%';
        }, 100);
    }

    const timeout = type === 'error' ? 6000 : 4500;
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 400);
        }
    }, timeout);
  }

  function preloadFileAndSetPreview(file, onLoad, onError) {
    const reader = new FileReader();
    reader.onload = function (e) { onLoad(e.target.result); };
    reader.onerror = onError;
    reader.readAsDataURL(file);
  }

  function preloadImageUrl(url, onLoad, onError) {
    if (!url) return onError && onError();
    const img = new Image();
    img.onload = function () { onLoad(url); };
    img.onerror = function () { onError && onError(); };
    img.src = url;
  }

  function createInitialMarkup(initial) {
    const safeInitial = escapeHtml(initial || 'U');
    return '<div class="w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center rounded-xl"><span class="text-3xl font-bold text-white">' + safeInitial + '</span></div>';
  }

  function createInitialMarkupSmall(initial) {
    const safeInitial = escapeHtml(initial || 'U');
    return '<div class="w-full h-full bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center rounded-full"><span class="text-2xl font-bold text-white">' + safeInitial + '</span></div>';
  }

  let isEditMode = false;
  let pendingImageData = null;

  document.addEventListener('DOMContentLoaded', function () {
    try {
      const tabButtons = qAll('.tab-button');
      const tabContents = qAll('.tab-content');

      const enableEditBtn = safe('enable-edit');
      const cancelEditBtn = safe('cancel-edit');
      const formButtons = safe('form-buttons');
      const editButtonContainer = safe('edit-button-container');

      const enablePasswordEditBtn = safe('enable-password-edit');
      const cancelPasswordBtn = safe('cancel-password');
      const passwordButtons = safe('password-buttons');
      const passwordButtonContainer = safe('password-button-container');

      const imgInput = safe('img');
      const imagePreview = safe('imagePreview');
      const profileCardAvatar = safe('profileCardAvatar');
      const editProfileForm = safe('edit-profile-form');
      const passwordForm = safe('password-form');

      const profile = window.Profile || {};

      const userName = profile.name || '';
      const userEmail = profile.email || '';
      const userImg = profile.img || '';
      const userInitial = profile.initial || (userName && userName[0] ? userName[0].toUpperCase() : 'U');

      function setPreviewToInitial() {
        if (imagePreview) {
          imagePreview.style.backgroundImage = '';
          imagePreview.innerHTML = createInitialMarkup(userInitial);
        }
        if (profileCardAvatar) {
          profileCardAvatar.innerHTML = createInitialMarkupSmall(userInitial);
        }
      }

      function setPreviewToImageUrl(imageUrl, updateCard = true) {
        if (imagePreview) {
          imagePreview.style.backgroundImage = "url('" + imageUrl + "')";
          imagePreview.style.backgroundSize = 'cover';
          imagePreview.style.backgroundPosition = 'center';
          imagePreview.style.backgroundRepeat = 'no-repeat';
          imagePreview.innerHTML = '';
        }
        if (updateCard && profileCardAvatar) {
          profileCardAvatar.innerHTML = `<img src="${imageUrl}" alt="${userName}" class="w-full h-full object-cover rounded-full">`;
        }
      }

      function trySetRemoteImage(url) {
        preloadImageUrl(url, function () {
          setPreviewToImageUrl(url);
        }, function () {
          log('remote image failed, using initial fallback');
          setPreviewToInitial();
        });
      }

      function resetProfileForm() {
        isEditMode = false;
        pendingImageData = null;
        
        const profileInputs = document.querySelectorAll('#edit-profile-form input:not([type="file"]):not([type="hidden"]), #edit-profile-form select');
        profileInputs.forEach(input => {
          try {
            input.readOnly = true;
            input.disabled = true;
          } catch (e) { }
        });

        if (formButtons) formButtons.classList.add('hidden');
        if (editButtonContainer) editButtonContainer.classList.remove('hidden');

        if (userImg) {
          trySetRemoteImage(userImg);
        } else {
          setPreviewToInitial();
        }

        const nameEl = safe('name');
        const emailEl = safe('email');

        if (nameEl) nameEl.value = userName;
        if (emailEl) emailEl.value = userEmail;

        if (imgInput) {
          try { 
            imgInput.value = ''; 
            imgInput.disabled = true;
          } catch (e) { }
        }

        const uploadLabel = document.querySelector('label[for="img"]');
        if (uploadLabel) {
          uploadLabel.classList.add('opacity-50', 'cursor-not-allowed');
          uploadLabel.classList.remove('cursor-pointer', 'hover:shadow-md');
        }
      }

      function resetPasswordForm() {
        const passwordInputs = document.querySelectorAll('#password-form input:not([type="hidden"])');
        passwordInputs.forEach(input => {
          input.disabled = true;
          input.value = '';
          input.setAttribute('type', 'password');
        });

        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
          const icon = button.querySelector('iconify-icon');
          if (icon) {
            icon.setAttribute('icon', 'ph:eye');
          }
        });

        if (passwordButtons) passwordButtons.classList.add('hidden');
        if (passwordButtonContainer) passwordButtonContainer.classList.remove('hidden');
      }

      function showTab(id) {
        tabContents.forEach(content => {
          if (content.id === id) {
            content.classList.remove('hidden');
            content.setAttribute('aria-hidden', 'false');
          } else {
            content.classList.add('hidden');
            content.setAttribute('aria-hidden', 'true');
          }
        });

        tabButtons.forEach(btn => {
          if (btn.getAttribute('data-tab') === id) {
            btn.setAttribute('aria-selected', 'true');
          } else {
            btn.setAttribute('aria-selected', 'false');
          }
        });

        resetProfileForm();
        resetPasswordForm();
      }

      if (tabButtons.length && tabContents.length) {
        const initialTab = tabButtons[0].getAttribute('data-tab') || 'edit-profile';
        showTab(initialTab);

        tabButtons.forEach(button => {
          button.addEventListener('click', function () {
            const targetTab = this.getAttribute('data-tab');
            if (!targetTab) return;
            showTab(targetTab);
          });
        });
      } else {
        resetProfileForm();
        resetPasswordForm();
      }

      if (enableEditBtn) {
        enableEditBtn.addEventListener('click', function () {
          isEditMode = true;
          const inputs = document.querySelectorAll('#edit-profile-form input:not([type="file"]):not([type="hidden"]), #edit-profile-form select');
          inputs.forEach(input => {
            try {
              input.readOnly = false;
              input.disabled = false;
            } catch (e) { }
          });
          
          if (imgInput) {
            imgInput.disabled = false;
          }
          
          const uploadLabel = document.querySelector('label[for="img"]');
          if (uploadLabel) {
            uploadLabel.classList.remove('opacity-50', 'cursor-not-allowed');
            uploadLabel.classList.add('cursor-pointer', 'hover:shadow-md');
          }
          
          if (formButtons) formButtons.classList.remove('hidden');
          if (editButtonContainer) editButtonContainer.classList.add('hidden');
        });
      }

      if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function () {
          resetProfileForm();
        });
      }

      if (enablePasswordEditBtn) {
        enablePasswordEditBtn.addEventListener('click', function () {
          const inputs = document.querySelectorAll('#password-form input:not([type="hidden"])');
          inputs.forEach(input => input.disabled = false);
          if (passwordButtons) passwordButtons.classList.remove('hidden');
          if (passwordButtonContainer) passwordButtonContainer.classList.add('hidden');
        });
      }

      if (cancelPasswordBtn) {
        cancelPasswordBtn.addEventListener('click', function () {
          resetPasswordForm();
        });
      }

      if (imgInput && imagePreview) {
        imgInput.addEventListener('change', function () {
          if (!isEditMode) {
            showNotification('Please click "Edit Profile" button first to enable editing.', 'error');
            try { this.value = ''; } catch (e) {}
            return;
          }

          if (this.files && this.files[0]) {
            const file = this.files[0];

            const maxSize = 2 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

            if (file.size > maxSize) {
              showNotification('Image too large. Maximum size allowed is 2MB.', 'error');
              try { this.value = ''; } catch (e) {}
              return;
            }

            if (allowedTypes.indexOf(file.type) === -1) {
              showNotification('Invalid image type. Please use JPG, PNG or GIF format.', 'error');
              try { this.value = ''; } catch (e) {}
              return;
            }

            preloadFileAndSetPreview(file, function (dataUrl) {
              pendingImageData = dataUrl;
              setPreviewToImageUrl(dataUrl, false);
              if (profileCardAvatar) {
                profileCardAvatar.innerHTML = `<img src="${dataUrl}" alt="Preview" class="w-full h-full object-cover rounded-full">`;
              }
            }, function () {
              showNotification('Failed to read image file.', 'error');
            });
          }
        });
      }

      const togglePasswordButtons = qAll('.toggle-password');
      togglePasswordButtons.forEach(button => {
        button.addEventListener('click', function () {
          const targetId = this.getAttribute('data-target');
          const targetInput = targetId ? safe(targetId) : null;
          if (!targetInput) return;

          const newType = targetInput.getAttribute('type') === 'password' ? 'text' : 'password';
          targetInput.setAttribute('type', newType);

          const icon = this.querySelector('iconify-icon');
          if (!icon) return;

          if (newType === 'text') {
            icon.setAttribute('icon', 'ph:eye-slash');
            this.setAttribute('aria-pressed', 'true');
          } else {
            icon.setAttribute('icon', 'ph:eye');
            this.setAttribute('aria-pressed', 'false');
          }
        });
      });

      const profileImgs = document.querySelectorAll('[data-fallback-img]');
      profileImgs.forEach(img => {
        img.addEventListener('error', function () {
          const fallback = img.nextElementSibling;
          if (fallback) {
            img.style.display = 'none';
            fallback.style.display = 'flex';
          }
        });
      });

      if (editProfileForm) {
        editProfileForm.addEventListener('submit', function (e) {
          const nameEl = safe('name');
          const emailEl = safe('email');
          const name = nameEl ? nameEl.value.trim() : '';
          const email = emailEl ? emailEl.value.trim() : '';

          if (!name) {
            e.preventDefault();
            showNotification('Please enter your full name.', 'error');
            return;
          }

          if (!email) {
            e.preventDefault();
            showNotification('Please enter your email address.', 'error');
            return;
          }

          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(email)) {
            e.preventDefault();
            showNotification('Please enter a valid email address.', 'error');
            return;
          }

          const submitBtn = this.querySelector('button[type="submit"]');
          const submitText = submitBtn?.querySelector('.submit-text');
          const submitLoading = submitBtn?.querySelector('.submit-loading');
          
          if (submitText && submitLoading && submitBtn) {
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            submitBtn.disabled = true;
          }
        });
      }

      if (passwordForm) {
        passwordForm.addEventListener('submit', function (e) {
          const passwordEl = safe('password');
          const confirmEl = safe('password_confirmation');
          const password = passwordEl ? passwordEl.value : '';
          const confirmPassword = confirmEl ? confirmEl.value : '';

          if (!password || !confirmPassword) {
            e.preventDefault();
            showNotification('Please fill in both password fields.', 'error');
            return;
          }
          
          if (password.length < 6) {
            e.preventDefault();
            showNotification('Password must be at least 6 characters long.', 'error');
            return;
          }
          
          if (password !== confirmPassword) {
            e.preventDefault();
            showNotification('Passwords do not match.', 'error');
            return;
          }

          const submitBtn = this.querySelector('button[type="submit"]');
          const submitText = submitBtn?.querySelector('.submit-text');
          const submitLoading = submitBtn?.querySelector('.submit-loading');
          
          if (submitText && submitLoading && submitBtn) {
            submitText.classList.add('hidden');
            submitLoading.classList.remove('hidden');
            submitBtn.disabled = true;
          }
        });
      }

      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('success')) {
        const message = urlParams.get('success');
        if (message === 'profile') {
          showNotification('Profile updated successfully!', 'success');
        } else if (message === 'password') {
          showNotification('Password changed successfully!', 'success');
        }
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
      }

      const flash = window.Flash || {};
      if (flash.success) {
        if (flash.success === 'profile') {
          showNotification('Profile updated successfully!', 'success');
        } else if (flash.success === 'password') {
          showNotification('Password changed successfully!', 'success');
        } else {
          showNotification(String(flash.success), 'success');
        }
        window.Flash.success = null;
      }
      if (flash.error) {
        showNotification(String(flash.error), 'error');
        window.Flash.error = null;
      }

      log('user.js initialized successfully');

    } catch (err) {
      if (window && window.console) console.error('user.js error', err);
    }
  });
})()