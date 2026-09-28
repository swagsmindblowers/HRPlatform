<style lang="scss" scoped>
.item-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 8px 0;
  border-bottom: 1px solid var(--color-steel-line);

  &:last-child {
    border-bottom: 0;
  }

  &.completed {
    opacity: 0.55;
  }
}

.check {
  width: 18px;
  height: 18px;
  border-radius: 6px;
  border: 1px solid var(--color-steel-line);
  flex: none;
  margin-top: 2px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  transition: background-color 0.15s ease, border-color 0.15s ease;

  &.done {
    background: var(--color-green);
    border-color: var(--color-green);
    color: #06231a;
  }
}

.mandated-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 1px 8px;
  font-size: 10px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  background-color: #fbdada;
  color: #8a1f1f;
  margin-left: 6px;
}

.overdue {
  color: #b23b3b;
}
</style>

<template>
  <div class="mb4 relative">
    <span class="db fw5 mb2">
      <span class="mr1">
        📋
      </span> {{ $t('employee.onboarding_title') }}
    </span>

    <div class="br3 box z-1 pa3">
      <div v-if="!checklist && permissions.can_manage_equity" class="tc">
        <p class="f7 mb2" style="color: var(--color-ink-soft)">{{ $t('employee.onboarding_not_started') }}</p>
        <select v-model="jurisdiction" class="mb2">
          <option value="generic">
            Generic
          </option>
          <option value="uk">
            UK
          </option>
          <option value="us">
            US
          </option>
        </select>
        <a class="btn add dib" data-cy="start-onboarding" @click.prevent="start">
          {{ $t('employee.onboarding_start') }}
        </a>
      </div>

      <p v-else-if="!checklist" class="mv0 f7" style="color: var(--color-ink-soft)">
        {{ $t('employee.onboarding_not_started') }}
      </p>

      <div v-else>
        <div
          v-for="item in checklist.items" :key="item.id"
          class="item-row" :class="{ completed: item.completed_at }"
        >
          <span
            class="check" :class="{ done: item.completed_at }"
            data-cy="onboarding-item-toggle"
            @click="!item.completed_at && complete(item)"
          >
            <span v-if="item.completed_at">
              ✓
            </span>
          </span>
          <span class="f6">
            {{ item.title }}
            <span v-if="item.is_legally_mandated" class="mandated-chip">
              {{ $t('employee.onboarding_legally_mandated') }}
            </span>
            <span v-if="item.due_date" class="db f7" :class="{ overdue: item.is_overdue }" style="color: var(--color-ink-soft)">
              {{ item.completed_at ? $t('employee.onboarding_completed_on', { date: item.completed_at }) : $t('employee.onboarding_due_on', { date: item.due_date }) }}
            </span>
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    employee: {
      type: Object,
      default: null,
    },
    permissions: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      checklist: null,
      jurisdiction: 'generic',
    };
  },

  created() {
    this.fetch();
  },

  methods: {
    fetch() {
      axios.get(`/${this.$page.props.auth.company.id}/employees/${this.employee.id}/onboarding`)
        .then(response => {
          this.checklist = response.data.data;
        });
    },

    start() {
      axios.post(`/${this.$page.props.auth.company.id}/employees/${this.employee.id}/onboarding`, {
        jurisdiction: this.jurisdiction,
      })
        .then(response => {
          this.checklist = response.data.data;
        });
    },

    complete(item) {
      axios.post(`/${this.$page.props.auth.company.id}/employees/${this.employee.id}/onboarding/${item.id}/complete`)
        .then(response => {
          this.checklist = response.data.data;
        });
    },
  },
};
</script>
