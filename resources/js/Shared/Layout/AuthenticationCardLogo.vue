<style lang="scss" scoped>
.logo {
  top: -46px;
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 18px;
  border-radius: 999px;
  background: color-mix(in srgb, var(--color-paper-raised) 90%, transparent);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid var(--color-steel-line);
  box-shadow: var(--shadow-glass);
}

.mark {
  width: 20px;
  height: 20px;
  border-radius: 6px;
  background-color: var(--color-amber);
  box-shadow: var(--shadow-glow);
  flex: none;
}

.wordmark {
  font-family: var(--font-display);
  font-weight: 800;
  text-transform: uppercase;
  font-size: 18px;
  letter-spacing: 0.01em;
  color: var(--color-ink);
  line-height: 1;
}
</style>

<template>
  <inertia-link :href="'/'" class="w-100 flex justify-center relative">
    <span class="logo absolute" aria-label="LaunchHR">
      <span class="mark"></span>
      <span class="wordmark">LaunchHR</span>
    </span>
  </inertia-link>
</template>
