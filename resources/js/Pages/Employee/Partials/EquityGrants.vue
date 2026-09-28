<style lang="scss" scoped>
.grant-row {
  border-bottom: 1px solid var(--color-steel-line);
  padding: 10px 0;

  &:last-child {
    border-bottom: 0;
  }
}

.vest-bar {
  height: 6px;
  border-radius: 999px;
  background: var(--color-steel-line);
  overflow: hidden;
  margin-top: 6px;
}

.vest-fill {
  height: 100%;
  background: var(--color-amber);
  box-shadow: var(--shadow-glow);
}
</style>

<template>
  <div v-if="permissions.can_see_equity" class="mb4 relative">
    <span class="db fw5 mb2">
      <span class="mr1">
        📈
      </span> {{ $t('employee.equity_title') }}
    </span>

    <div class="br3 box z-1 pa3">
      <p v-if="!loading && grants.length === 0" class="mv0 lh-copy f6">
        {{ $t('employee.equity_no_grants') }}
      </p>

      <div v-for="grant in grants" :key="grant.id" class="grant-row" :data-cy="'equity-grant-' + grant.id">
        <div class="flex justify-between items-center">
          <span class="fw5 f6">
            {{ grant.units.toLocaleString() }} {{ grant.grant_type === 'rsu' ? 'RSUs' : 'options' }}
          </span>
          <span class="f7" style="color: var(--color-ink-soft)">
            {{ grant.grant_date }}
          </span>
        </div>
        <p class="mv1 f7" style="color: var(--color-ink-soft)">
          {{ $t('employee.equity_vested', { percent: grant.vested_percent, units: grant.vested_units.toLocaleString() }) }}
        </p>
        <div class="vest-bar">
          <div class="vest-fill" :style="{ width: grant.vested_percent + '%' }"></div>
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
      loading: true,
      grants: [],
    };
  },

  created() {
    if (this.permissions.can_see_equity) {
      this.fetch();
    }
  },

  methods: {
    fetch() {
      this.loading = true;

      axios.get(`/${this.$page.props.auth.company.id}/employees/${this.employee.id}/equity`)
        .then(response => {
          this.grants = response.data.data;
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
    },
  },
};
</script>
