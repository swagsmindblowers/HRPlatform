<style lang="scss" scoped>
.item-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 0;
  border-top: 1px solid var(--color-steel-line);

  &:first-of-type {
    border-top: none;
  }
}

.mandated-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 2px 9px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  background-color: #fbdada;
  color: #8a1f1f;
}

.panel {
  background: var(--color-paper-raised, #fff);
}
</style>

<template>
  <div class="mb5">
    <div class="cf mw7 center mb2 fw5 relative">
      <span class="mr1">
        📋
      </span> {{ $t('dashboard.hr_onboarding_title') }}
    </div>

    <div class="mw7 center br3 mb3 panel box pa3">
      <p class="f6 fw6 mb2">{{ $t('dashboard.hr_onboarding_overdue_title') }}</p>
      <div v-if="data.overdue.length > 0" class="mb4">
        <div v-for="item in data.overdue" :key="'overdue-' + item.id" class="item-row">
          <div>
            <inertia-link :href="item.url">{{ item.employee_name }}</inertia-link>
            <span class="f7 grey ml1">
              {{ item.title }}
            </span>
          </div>
          <span v-if="item.is_legally_mandated" class="mandated-chip">
            {{ $t('dashboard.hr_onboarding_legally_mandated') }}
          </span>
        </div>
      </div>
      <p v-else class="f7 grey mb4">{{ $t('dashboard.hr_onboarding_overdue_blank') }}</p>

      <p class="f6 fw6 mb2">{{ $t('dashboard.hr_onboarding_upcoming_title') }}</p>
      <div v-if="data.upcoming.length > 0">
        <div v-for="item in data.upcoming" :key="'upcoming-' + item.id" class="item-row">
          <div>
            <inertia-link :href="item.url">{{ item.employee_name }}</inertia-link>
            <span class="f7 grey ml1">
              {{ item.title }} — {{ item.due_date }}
            </span>
          </div>
          <span v-if="item.is_legally_mandated" class="mandated-chip">
            {{ $t('dashboard.hr_onboarding_legally_mandated') }}
          </span>
        </div>
      </div>
      <p v-else class="f7 grey">{{ $t('dashboard.hr_onboarding_upcoming_blank') }}</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>
