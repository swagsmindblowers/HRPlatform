<style lang="scss" scoped>
.hub-section {
  margin-bottom: 36px;
}

.hub-section-title {
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--color-ink-soft);
  margin-bottom: 12px;
}

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

  &.highlight {
    border-color: var(--color-amber);
    box-shadow: var(--shadow-glow);
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
        {{ $t('hub.time_and_pay_title') }}
      </breadcrumb>

      <div class="mw8 center mb5">
        <h2 class="fw7 mb1">
          {{ $t('hub.time_and_pay_title') }}
        </h2>
        <p class="mb4" style="color: var(--color-ink-soft)">{{ $t('hub.time_and_pay_subtitle') }}</p>

        <div class="hub-section">
          <div class="hub-section-title">
            {{ $t('hub.time_and_pay_mine') }}
          </div>
          <div class="hub-grid">
            <inertia-link :href="'/' + $page.props.auth.company.id + '/employees/' + employee.id + '/administration'" class="hub-card highlight" data-cy="time-off-hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.time_off_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.time_off_body') }}</div>
            </inertia-link>

            <inertia-link :href="'/' + $page.props.auth.company.id + '/dashboard/timesheet'" class="hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><rect x="3" y="4" width="18" height="17" rx="2" /><path d="M3 9h18M8 3v3M16 3v3" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.timesheets_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.timesheets_body') }}</div>
            </inertia-link>

            <inertia-link :href="'/' + $page.props.auth.company.id + '/employees/' + employee.id + '/administration/expenses'" class="hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><rect x="2" y="6" width="20" height="12" rx="2" /><circle cx="12" cy="12" r="2.5" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.expenses_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.expenses_body') }}</div>
            </inertia-link>

            <inertia-link :href="'/' + $page.props.auth.company.id + '/employees/' + employee.id + '/performance'" class="hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><path d="M12 17.3 6.2 20l1.1-6.5L2.5 9l6.5-1L12 2l3 6 6.5 1-4.8 4.5 1.1 6.5z" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.performance_equity_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.performance_equity_body') }}</div>
            </inertia-link>
          </div>
        </div>

        <div v-if="permissions.is_manager || permissions.is_hr_or_admin" class="hub-section">
          <div class="hub-section-title">
            {{ $t('hub.time_and_pay_team') }}
          </div>
          <div class="hub-grid">
            <inertia-link v-if="permissions.is_manager" :href="'/' + $page.props.auth.company.id + '/dashboard/manager'" class="hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><path d="M9 11V7a2 2 0 0 1 4 0v3" /><path d="M11 10.5V6a2 2 0 0 1 4 0v5" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.manager_approvals_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.manager_approvals_body') }}</div>
            </inertia-link>

            <inertia-link v-if="permissions.is_hr_or_admin" :href="'/' + $page.props.auth.company.id + '/dashboard/hr/timesheets'" class="hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><rect x="3" y="4" width="18" height="17" rx="2" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.hr_timesheets_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.hr_timesheets_body') }}</div>
            </inertia-link>

            <inertia-link v-if="permissions.is_hr_or_admin" :href="'/' + $page.props.auth.company.id + '/dashboard/expenses'" class="hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><rect x="2" y="6" width="20" height="12" rx="2" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.company_expenses_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.company_expenses_body') }}</div>
            </inertia-link>
          </div>
        </div>

        <div v-if="permissions.is_administrator" class="hub-section">
          <div class="hub-section-title">
            {{ $t('hub.time_and_pay_integrations') }}
          </div>
          <div class="hub-grid">
            <inertia-link :href="'/' + $page.props.auth.company.id + '/integrations'" class="hub-card highlight" data-cy="integrations-hub-card">
              <span class="hub-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                     stroke-linejoin="round"
                ><path d="M8 12h8M12 8v8" /><circle cx="12" cy="12" r="9" /></svg>
              </span>
              <div class="hub-card-title">{{ $t('hub.integrations_title') }}</div>
              <div class="hub-card-body">{{ $t('hub.integrations_body') }}</div>
            </inertia-link>
          </div>
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
    employee: {
      type: Object,
      default: () => ({}),
    },
    permissions: {
      type: Object,
      default: () => ({}),
    },
  },
};
</script>
