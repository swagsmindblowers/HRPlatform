<style lang="scss" scoped>
.hub-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 16px;
}

.hub-card {
  display: block;
  text-decoration: none;
  color: var(--color-ink);
  background: color-mix(in srgb, var(--color-paper-raised) 90%, transparent);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--color-steel-line);
  border-radius: 16px;
  box-shadow: var(--shadow-glass);
  padding: 22px;
  transition: transform 0.15s cubic-bezier(0.2, 0, 0, 1), border-color 0.15s ease;

  &:hover {
    transform: translateY(-2px);
    border-color: var(--color-amber);
    color: var(--color-ink);
  }
}

.hub-card-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: var(--color-amber-tint);
  color: var(--color-amber-ink);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 14px;

  svg { width: 18px; height: 18px; }
}

.hub-card-title {
  font-weight: 700;
  font-size: 15px;
  margin-bottom: 4px;
}

.hub-card-body {
  font-size: 13px;
  color: var(--color-ink-soft);
  line-height: 1.5;
}
</style>

<template>
  <layout :notifications="notifications">
    <div class="ph2 ph5-ns mt4">
      <breadcrumb :has-more="false">
        {{ $t('hub.compliance_title') }}
      </breadcrumb>

      <div class="mw8 center mb5">
        <h2 class="fw7 mb1">
          {{ $t('hub.compliance_title') }}
        </h2>
        <p class="mb4" style="color: var(--color-ink-soft)">{{ $t('hub.compliance_subtitle') }}</p>

        <div class="hub-grid">
          <inertia-link :href="'/' + $page.props.auth.company.id + '/account/compliance'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="M12 3 5 6v5c0 4.4 3 7.7 7 9 4-1.3 7-4.6 7-9V6l-7-3Z" /><path d="M9.5 12 11 13.5 14.5 10" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.compliance_checklist_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.compliance_checklist_body') }}</div>
          </inertia-link>

          <inertia-link :href="'/' + $page.props.auth.company.id + '/account/policytemplates'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="m9 11 3 3L22 4" /><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.policy_templates_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.policy_templates_body') }}</div>
          </inertia-link>

          <inertia-link :href="'/' + $page.props.auth.company.id + '/dashboard/hr'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="M12 8v4l3 2" /><circle cx="12" cy="12" r="9" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.onboarding_deadlines_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.onboarding_deadlines_body') }}</div>
          </inertia-link>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import Breadcrumb from '@/Shared/Layout/Breadcrumb';

export default {
  components: {
    Layout,
    Breadcrumb,
  },

  props: {
    notifications: {
      type: Array,
      default: null,
    },
  },
};
</script>
