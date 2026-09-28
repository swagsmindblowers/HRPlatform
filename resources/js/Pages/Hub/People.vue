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
        {{ $t('hub.people_title') }}
      </breadcrumb>

      <div class="mw8 center mb5">
        <h2 class="fw7 mb1">
          {{ $t('hub.people_title') }}
        </h2>
        <p class="mb4" style="color: var(--color-ink-soft)">{{ $t('hub.people_subtitle') }}</p>

        <div class="hub-grid">
          <inertia-link :href="'/' + $page.props.auth.company.id + '/employees'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 3.5-6 8-6s8 2 8 6" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.people_directory_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.people_directory_body') }}</div>
          </inertia-link>

          <inertia-link :href="'/' + $page.props.auth.company.id + '/employees/org-chart'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><circle cx="6" cy="6" r="2.5" /><circle cx="18" cy="6" r="2.5" /><circle cx="12" cy="18" r="2.5" /><path d="M6 8.5V12a2 2 0 0 0 2 2h1.5M18 8.5V12a2 2 0 0 1-2 2h-1.5" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.people_orgchart_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.people_orgchart_body') }}</div>
          </inertia-link>

          <inertia-link :href="'/' + $page.props.auth.company.id + '/teams'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><rect x="3" y="4" width="18" height="7" rx="1.5" /><rect x="3" y="13" width="18" height="7" rx="1.5" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.people_teams_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.people_teams_body') }}</div>
          </inertia-link>

          <inertia-link :href="'/' + $page.props.auth.company.id + '/company'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><rect x="4" y="4" width="7" height="16" rx="1" /><rect x="13" y="9" width="7" height="11" rx="1" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.people_company_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.people_company_body') }}</div>
          </inertia-link>

          <inertia-link v-if="permissions.is_hr_or_admin" :href="'/' + $page.props.auth.company.id + '/account/employees'" class="hub-card">
            <span class="hub-card-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="M12 3 5 6v5c0 4.4 3 7.7 7 9 4-1.3 7-4.6 7-9V6l-7-3Z" /></svg>
            </span>
            <div class="hub-card-title">{{ $t('hub.people_manage_title') }}</div>
            <div class="hub-card-body">{{ $t('hub.people_manage_body') }}</div>
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
    permissions: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
