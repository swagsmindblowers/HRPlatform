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
  background-color: var(--color-amber-tint);
  color: var(--color-amber-ink);
}
</style>

<template>
  <div class="mb3 relative">
    <span v-if="!editing" class="f6">
      <span class="type-chip">
        {{ localValue === 'contractor' ? $t('employee.employment_type_contractor') : $t('employee.employment_type_employee') }}
      </span>
      <a v-show="permissions.can_manage_status" data-cy="edit-employment-type-button" class="bb b--dotted bt-0 bl-0 br-0 pointer di f7 ml2" @click.prevent="editing = true">{{ $t('app.edit') }}</a>
    </span>

    <span v-else>
      <select v-model="localValue" data-cy="employment-type-select" @change="save">
        <option value="employee">
          {{ $t('employee.employment_type_employee') }}
        </option>
        <option value="contractor">
          {{ $t('employee.employment_type_contractor') }}
        </option>
      </select>
    </span>
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
      editing: false,
      localValue: this.employee.employment_type || 'employee',
    };
  },

  methods: {
    save() {
      axios.post(`/${this.$page.props.auth.company.id}/employees/${this.employee.id}/employmenttype`, {
        employment_type: this.localValue,
      })
        .then(() => {
          this.editing = false;
          this.flash(this.$t('employee.employment_type_saved'), 'success');
        });
    },
  },
};
</script>
