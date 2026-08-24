<style lang="scss" scoped>
.type-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 2px 9px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;

  &.holiday {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }

  &.sick {
    background-color: #fbdada;
    color: #8a1f1f;
  }

  &.pto {
    background-color: var(--color-green-tint);
    color: var(--color-green-ink);
  }
}

.absence-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 0;
  border-top: 1px solid var(--color-steel-line);

  &:first-of-type {
    border-top: none;
  }
}

.panel {
  background: var(--color-paper-raised, #fff);
}

table.sickness {
  width: 100%;
  border-collapse: collapse;

  th {
    text-align: left;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: var(--color-ink-soft);
    padding: 6px 0;
    border-bottom: 1px solid var(--color-steel-line);
  }

  td {
    padding: 8px 0;
    border-bottom: 1px solid var(--color-steel-line);
    font-size: 14px;
  }

  tr:last-child td {
    border-bottom: none;
  }
}
</style>

<template>
  <div class="mb5">
    <div class="cf mw7 center mb2 fw5 relative">
      <span class="mr1">
        🩺
      </span> {{ $t('dashboard.hr_absences_title') }}
    </div>

    <div class="mw7 center br3 mb3 panel box pa3">
      <!-- Off today -->
      <p class="f6 fw6 mb2">{{ $t('dashboard.hr_absences_off_today_title') }}</p>
      <div v-if="data.off_today.length > 0" class="mb4">
        <div v-for="item in data.off_today" :key="item.employee_id + '-' + item.type" class="absence-row">
          <span>{{ item.name }}</span>
          <span :class="['type-chip', item.type]">
            {{ item.type }}
          </span>
        </div>
      </div>
      <p v-else class="f7 grey mb4">{{ $t('dashboard.hr_absences_off_today_blank') }}</p>

      <!-- Upcoming -->
      <p class="f6 fw6 mb2">{{ $t('dashboard.hr_absences_upcoming_title') }}</p>
      <div v-if="data.upcoming.length > 0" class="mb4">
        <div v-for="(item, index) in data.upcoming" :key="item.employee_id + '-' + item.date + '-' + index" class="absence-row">
          <div>
            <span class="mr2">
              {{ item.name }}
            </span>
            <span class="f7 grey">
              {{ item.date }}
            </span>
          </div>
          <span :class="['type-chip', item.type]">
            {{ item.type }}
          </span>
        </div>
      </div>
      <p v-else class="f7 grey mb4">{{ $t('dashboard.hr_absences_upcoming_blank') }}</p>

      <!-- Sickness monitoring -->
      <p class="f6 fw6 mb2">{{ $t('dashboard.hr_absences_sickness_title') }}</p>
      <table v-if="data.sickness_monitoring.length > 0" class="sickness">
        <thead>
          <tr>
            <th>{{ $t('dashboard.hr_absences_sickness_name') }}</th>
            <th>{{ $t('dashboard.hr_absences_sickness_occurrences') }}</th>
            <th>{{ $t('dashboard.hr_absences_sickness_days') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in data.sickness_monitoring" :key="row.employee_id">
            <td>{{ row.name }}</td>
            <td>{{ row.occurrences }}</td>
            <td>{{ row.days }}</td>
          </tr>
        </tbody>
      </table>
      <p v-else class="f7 grey">{{ $t('dashboard.hr_absences_sickness_blank') }}</p>
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
