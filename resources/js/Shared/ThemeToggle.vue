<style lang="scss" scoped>
.toggle-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 999px;
  color: var(--color-ink-soft);
  cursor: pointer;
  transition: background-color 0.15s ease, color 0.15s ease;

  &:hover {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }

  svg {
    width: 17px;
    height: 17px;
  }
}
</style>

<template>
  <span class="toggle-btn" data-cy="theme-toggle" @click="toggle">
    <svg v-if="isLight" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
         stroke-linecap="round" stroke-linejoin="round"
    >
      <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" />
    </svg>
    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
         stroke-linecap="round" stroke-linejoin="round"
    >
      <circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
    </svg>
  </span>
</template>

<script>
export default {
  data() {
    return {
      isLight: true,
    };
  },

  created() {
    this.isLight = document.documentElement.getAttribute('data-theme') !== 'dark';
  },

  methods: {
    toggle() {
      this.isLight = !this.isLight;

      if (this.isLight) {
        document.documentElement.removeAttribute('data-theme');
        localStorage.setItem('betteroff_theme', 'light');
      } else {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('betteroff_theme', 'dark');
      }
    },
  },
};
</script>
