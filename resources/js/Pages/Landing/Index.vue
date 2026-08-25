<style lang="scss" scoped>
.page {
  min-height: 100vh;
  background: var(--color-paper);
  color: var(--color-ink);
  overflow-x: hidden;
}

/* ---------- header ---------- */
.landing-nav {
  position: sticky;
  top: 0;
  z-index: 40;
  border-bottom: 1px solid var(--color-steel-line);
  background: color-mix(in srgb, var(--color-paper-raised) 82%, transparent);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

.brand-mark {
  background-color: var(--color-amber);
  box-shadow: var(--shadow-glow);
  width: 24px;
  height: 24px;
  border-radius: 6px;
  flex: none;
}

.brand-wordmark {
  font-family: var(--font-display);
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.01em;
  font-size: 18px;
  color: var(--color-ink);
}

.nav-cta {
  border-radius: 999px;
  padding: 8px 18px;
  font-weight: 600;
  font-size: 13.5px;
  background: var(--color-amber);
  color: #fff;
  box-shadow: var(--shadow-glow);
  text-decoration: none;
  transition: transform 0.15s cubic-bezier(0.2, 0, 0, 1);

  &:active {
    transform: scale(0.96);
  }
}

.nav-login {
  font-weight: 600;
  font-size: 13.5px;
  color: var(--color-ink-soft);
  text-decoration: none;

  &:hover {
    color: var(--color-ink);
  }
}

/* ---------- hero ---------- */
.hero {
  position: relative;
  padding: 96px 24px 120px;
  text-align: center;
  overflow: hidden;
}

.hero-glow {
  position: absolute;
  top: -220px;
  left: 50%;
  width: 900px;
  height: 900px;
  margin-left: -450px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(124, 92, 252, 0.35) 0%, rgba(124, 92, 252, 0) 65%);
  pointer-events: none;
  z-index: 0;
}

@media (prefers-reduced-motion: no-preference) {
  .hero-glow {
    animation: drift 14s ease-in-out infinite alternate;
  }
}

@keyframes drift {
  0% { transform: translate(-40px, 0) scale(1); }
  100% { transform: translate(40px, 30px) scale(1.08); }
}

.hero-content {
  position: relative;
  z-index: 1;
  max-width: 720px;
  margin: 0 auto;
}

.hero-eyebrow {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 6px 14px;
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  background: var(--color-amber-tint);
  color: var(--color-amber-ink);
  opacity: 0;
  animation: fade-up 0.7s ease forwards;
}

.hero-title {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: clamp(2.2rem, 5vw, 3.6rem);
  line-height: 1.08;
  letter-spacing: -0.01em;
  margin: 20px 0 18px;
  opacity: 0;
  animation: fade-up 0.7s ease 0.1s forwards;
}

.hero-subtitle {
  font-size: 17px;
  line-height: 1.6;
  color: var(--color-ink-soft);
  margin: 0 auto 32px;
  max-width: 560px;
  opacity: 0;
  animation: fade-up 0.7s ease 0.2s forwards;
}

.hero-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
  opacity: 0;
  animation: fade-up 0.7s ease 0.3s forwards;
}

@keyframes fade-up {
  from { opacity: 0; transform: translateY(14px); }
  to { opacity: 1; transform: translateY(0); }
}

.btn-primary {
  border-radius: 999px;
  padding: 13px 26px;
  font-weight: 700;
  font-size: 15px;
  background: var(--color-amber);
  color: #fff;
  box-shadow: var(--shadow-glow);
  text-decoration: none;
  transition: transform 0.15s cubic-bezier(0.2, 0, 0, 1);

  &:active { transform: scale(0.96); }
}

.btn-secondary {
  border-radius: 999px;
  padding: 13px 26px;
  font-weight: 700;
  font-size: 15px;
  border: 1px solid var(--color-steel-line);
  color: var(--color-ink);
  text-decoration: none;
  transition: background-color 0.15s ease, transform 0.15s cubic-bezier(0.2, 0, 0, 1);

  &:hover { background-color: var(--color-amber-tint); }
  &:active { transform: scale(0.96); }
}

/* ---------- features ---------- */
.features {
  padding: 40px 24px 100px;
  max-width: 1080px;
  margin: 0 auto;
}

.features-header {
  text-align: center;
  max-width: 620px;
  margin: 0 auto 56px;
}

.features-eyebrow {
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--color-amber-ink);
  margin-bottom: 10px;
  display: block;
}

.features-title {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: clamp(1.6rem, 3.4vw, 2.3rem);
  letter-spacing: -0.01em;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.feature-card {
  background: color-mix(in srgb, var(--color-paper-raised) 90%, transparent);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--color-steel-line);
  border-radius: 16px;
  box-shadow: var(--shadow-glass);
  padding: 26px;
  text-align: left;
  transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1), border-color 0.2s ease;

  &:hover {
    transform: translateY(-4px);
    border-color: var(--color-amber);
  }
}

.feature-icon {
  width: 40px;
  height: 40px;
  border-radius: 11px;
  background: var(--color-amber-tint);
  color: var(--color-amber-ink);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;

  svg { width: 20px; height: 20px; }
}

.feature-title {
  font-weight: 700;
  font-size: 16px;
  margin-bottom: 8px;
}

.feature-body {
  font-size: 14px;
  line-height: 1.55;
  color: var(--color-ink-soft);
}

/* ---------- scroll reveal ---------- */
.scroll-reveal {
  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.6s ease, transform 0.6s cubic-bezier(0.2, 0, 0, 1);
}

.scroll-reveal.is-visible {
  opacity: 1;
  transform: translateY(0);
}

@media (prefers-reduced-motion: reduce) {
  .scroll-reveal {
    opacity: 1;
    transform: none;
    transition: none;
  }

  .hero-eyebrow, .hero-title, .hero-subtitle, .hero-actions {
    opacity: 1;
    animation: none;
  }
}

/* ---------- cta band ---------- */
.cta-band {
  margin: 0 24px 100px;
  max-width: 1080px;
  margin-left: auto;
  margin-right: auto;
  border-radius: 24px;
  padding: 64px 32px;
  text-align: center;
  background: color-mix(in srgb, var(--color-amber) 12%, var(--color-paper-raised));
  border: 1px solid var(--color-steel-line);
}

.cta-title {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: clamp(1.6rem, 3.4vw, 2.2rem);
  margin-bottom: 12px;
}

.cta-subtitle {
  color: var(--color-ink-soft);
  margin-bottom: 28px;
}

/* ---------- footer ---------- */
.landing-footer {
  border-top: 1px solid var(--color-steel-line);
  padding: 28px 24px;
  text-align: center;
  color: var(--color-ink-soft);
  font-size: 13px;
}
</style>

<template>
  <div class="page">
    <nav class="landing-nav flex items-center justify-between px-3 py-2">
      <div class="flex items-center gap-2">
        <span class="brand-mark"></span>
        <span class="brand-wordmark">
          LaunchHR
        </span>
      </div>
      <div class="flex items-center gap-3">
        <a href="/login" class="nav-login">{{ $t('landing.nav_login') }}</a>
        <a href="/login" class="nav-cta">{{ $t('landing.nav_cta') }}</a>
      </div>
    </nav>

    <section class="hero">
      <div class="hero-glow"></div>
      <div class="hero-content">
        <span class="hero-eyebrow">
          {{ $t('landing.hero_eyebrow') }}
        </span>
        <h1 class="hero-title">
          {{ $t('landing.hero_title') }}
        </h1>
        <p class="hero-subtitle">{{ $t('landing.hero_subtitle') }}</p>
        <div class="hero-actions">
          <a href="/login" class="btn-primary">{{ $t('landing.hero_cta_primary') }}</a>
          <a href="/login" class="btn-secondary">{{ $t('landing.hero_cta_secondary') }}</a>
        </div>
      </div>
    </section>

    <section class="features">
      <div v-scroll-reveal class="features-header">
        <span class="features-eyebrow">
          {{ $t('landing.features_eyebrow') }}
        </span>
        <h2 class="features-title">
          {{ $t('landing.features_title') }}
        </h2>
      </div>

      <div class="feature-grid">
        <div v-for="(feature, index) in features" :key="index" v-scroll-reveal class="feature-card" :style="{ transitionDelay: (index * 60) + 'ms' }">
          <div class="feature-icon" v-html="feature.icon"></div>
          <div class="feature-title">
            {{ feature.title }}
          </div>
          <div class="feature-body">
            {{ feature.body }}
          </div>
        </div>
      </div>
    </section>

    <section v-scroll-reveal class="cta-band">
      <h2 class="cta-title">
        {{ $t('landing.cta_title') }}
      </h2>
      <p class="cta-subtitle">{{ $t('landing.cta_subtitle') }}</p>
      <a href="/login" class="btn-primary">{{ $t('landing.cta_button') }}</a>
    </section>

    <footer class="landing-footer">
      {{ $t('landing.footer_tagline') }}
    </footer>
  </div>
</template>

<script>
export default {
  computed: {
    features() {
      return [
        {
          icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a2 2 0 0 1 2 2v1h2a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V9a3 3 0 0 1 3-3h2V5a2 2 0 0 1 2-2Z" /><path d="M8 13h.01M16 13h.01M9 17h6" /></svg>',
          title: this.$t('landing.feature_ai_title'),
          body: this.$t('landing.feature_ai_body'),
        },
        {
          icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg>',
          title: this.$t('landing.feature_absence_title'),
          body: this.$t('landing.feature_absence_body'),
        },
        {
          icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m9 11 3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>',
          title: this.$t('landing.feature_onboarding_title'),
          body: this.$t('landing.feature_onboarding_body'),
        },
        {
          icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="6" r="2.5" /><circle cx="18" cy="6" r="2.5" /><circle cx="12" cy="18" r="2.5" /><path d="M6 8.5V12a2 2 0 0 0 2 2h1.5M18 8.5V12a2 2 0 0 1-2 2h-1.5" /></svg>',
          title: this.$t('landing.feature_orgchart_title'),
          body: this.$t('landing.feature_orgchart_body'),
        },
        {
          icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 17.3 6.2 20l1.1-6.5L2.5 9l6.5-1L12 2l3 6 6.5 1-4.8 4.5 1.1 6.5z" /></svg>',
          title: this.$t('landing.feature_performance_title'),
          body: this.$t('landing.feature_performance_body'),
        },
        {
          icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 5 6v5c0 4.4 3 7.7 7 9 4-1.3 7-4.6 7-9V6l-7-3Z" /><path d="M9.5 12 11 13.5 14.5 10" /></svg>',
          title: this.$t('landing.feature_compliance_title'),
          body: this.$t('landing.feature_compliance_body'),
        },
      ];
    },
  },

  mounted() {
    document.title = 'LaunchHR — HR for founders';
  },
};
</script>
