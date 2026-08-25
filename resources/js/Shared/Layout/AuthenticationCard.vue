<style lang="scss" scoped>
.auth-card {
  background: color-mix(in srgb, var(--color-paper-raised) 90%, transparent);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid var(--color-steel-line);
  box-shadow: var(--shadow-glass);
}
</style>

<template>
  <div class="ph2 ph0-ns">
    <div class="auth-card cf mt6 mw6 center br3 mb2 pa3">
      <div class="w-100 relative">
        <slot name="logo"></slot>
      </div>

      <div class="">
        <slot></slot>
      </div>
    </div>

    <div class="tc">
      <slot name="footer"></slot>
    </div>

    <toaster />
  </div>
</template>

<script>
import Toaster from '@/Shared/Toaster';

export default {
  components: {
    Toaster,
  },
};
</script>
