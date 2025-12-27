import { apiPost } from './api.js';

window.addEventListener('DOMContentLoaded', () => {
  const logoutBtn = document.querySelector('[data-logout]');
  logoutBtn?.addEventListener('click', async () => {
    const res = await apiPost('/api/auth/logout');
    if (res.ok) {
      window.location.href = '/login';
    }
  });
});
