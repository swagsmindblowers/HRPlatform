// Vue 3 custom directive: v-scroll-reveal
// Adds `scroll-reveal` (base hidden/offset state) then `is-visible` the
// first time the element enters the viewport, letting a CSS transition on
// `.scroll-reveal.is-visible` handle the actual animation. No dependency
// beyond the browser's native IntersectionObserver.
const observers = new WeakMap();

export default {
  mounted(el) {
    el.classList.add('scroll-reveal');

    if (typeof IntersectionObserver === 'undefined') {
      el.classList.add('is-visible');
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          el.classList.add('is-visible');
          observer.unobserve(el);
        }
      });
    }, { threshold: 0.15 });

    observer.observe(el);
    observers.set(el, observer);
  },

  unmounted(el) {
    const observer = observers.get(el);
    if (observer) {
      observer.disconnect();
      observers.delete(el);
    }
  },
};
