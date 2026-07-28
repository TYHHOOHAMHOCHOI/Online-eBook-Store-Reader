export function setWelcomeState(action) {
  action.textContent = 'Sẵn sàng để xây dựng!';
  action.disabled = true;
}

if (typeof document !== 'undefined') {
  document.addEventListener('DOMContentLoaded', () => {
    const action = document.querySelector('[data-welcome-action]');

    action?.addEventListener('click', () => setWelcomeState(action));
  });
}
