export function initToasts() {
  const stack = document.querySelector('[data-toast-stack]');
  if (!stack) {
    return;
  }

  const toasts = Array.from(stack.querySelectorAll('.toast'));
  if (!toasts.length) {
    return;
  }

  toasts.forEach((toast) => {
    const timeoutValue = Number.parseInt(toast.dataset.timeout || '3500', 10);
    const timeout = Number.isNaN(timeoutValue) ? 3500 : timeoutValue;
    const closeButton = toast.querySelector('[data-toast-close]');
    let timeoutId = null;

    const dismiss = () => {
      toast.classList.remove('is-visible');
      window.setTimeout(() => {
        toast.remove();
      }, 300);
    };

    if (closeButton) {
      closeButton.addEventListener('click', () => {
        if (timeoutId) {
          window.clearTimeout(timeoutId);
        }
        dismiss();
      });
    }

    requestAnimationFrame(() => {
      toast.classList.add('is-visible');
    });

    if (timeout > 0) {
      timeoutId = window.setTimeout(() => {
        dismiss();
      }, timeout);
    }
  });
}
