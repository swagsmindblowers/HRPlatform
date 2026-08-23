<style lang="scss" scoped>
.auth-card {
  background-color: var(--color-paper-raised);
  border: 1px solid var(--color-steel-line);
  box-shadow: 0 1px 2px rgba(29, 30, 39, 0.04), 0 16px 40px rgba(29, 30, 39, 0.08);
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
