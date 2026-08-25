<style lang="scss" scoped>
.item-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-bottom: 1px solid var(--color-steel-line);

  &:last-child {
    border-bottom: 0;
  }
}

.status-chip {
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

  &.in_progress {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }

  &.complete {
    background-color: var(--color-green-tint);
    color: var(--color-green-ink);
  }
}
</style>

<template>
  <layout :notifications="notifications">
    <div class="ph2 ph0-ns">
      <breadcrumb :with-box="true"
                  :root-url="'/' + $page.props.auth.company.id + '/dashboard'"
                  :root="$t('app.breadcrumb_dashboard')"
                  :previous-url="'/' + $page.props.auth.company.id + '/account'"
                  :previous="$t('app.breadcrumb_account_home')"
                  :has-more="false"
      >
        {{ $t('compliance.title') }}
      </breadcrumb>

      <div class="mw7 center br3 mb5 box relative z-1">
        <div class="pa3 mt2">
          <h2 class="tc normal mb2">
            {{ $t('compliance.title') }}
          </h2>
          <p class="tc f7 mb4" style="color: var(--color-ink-soft)">{{ $t('compliance.subtitle') }}</p>

          <a class="btn add dib mb3" data-cy="add-compliance-item" @click.prevent="showForm = !showForm">
            {{ $t('compliance.add_item') }}
          </a>

          <form v-if="showForm" class="mb4" @submit.prevent="create">
            <input v-model="form.title" type="text" :placeholder="$t('compliance.item_title')" class="mb2 db w-100" required />
            <select v-model="form.category" class="mb2 db">
              <option value="insurance">
                Insurance
              </option>
              <option value="tax">
                Tax
              </option>
              <option value="pension">
                Pension
              </option>
              <option value="immigration">
                Immigration
              </option>
            </select>
            <input v-model="form.jurisdiction" type="text" placeholder="e.g. uk" class="mb2 db" required />
            <input v-model="form.due_date" type="date" class="mb2 db" />
            <button type="submit" class="btn add">
              {{ $t('compliance.save') }}
            </button>
          </form>

          <p v-if="localItems.length === 0" class="tc f7" style="color: var(--color-ink-soft)">
            {{ $t('compliance.no_items') }}
          </p>

          <div v-for="item in localItems" :key="item.id" class="item-row" :data-cy="'compliance-item-' + item.id">
            <div>
              <span class="fw5 f6">
                {{ item.title }}
              </span>
              <span class="db f7" style="color: var(--color-ink-soft)">
                {{ item.category }} · {{ item.jurisdiction }}<span v-if="item.due_date">
                  · {{ $t('compliance.due', { date: item.due_date }) }}
                </span>
              </span>
            </div>
            <select :value="item.status" :class="['status-chip', item.status]" @change="updateStatus(item, $event.target.value)">
              <option value="not_started">
                {{ $t('compliance.status_not_started') }}
              </option>
              <option value="in_progress">
                {{ $t('compliance.status_in_progress') }}
              </option>
              <option value="complete">
                {{ $t('compliance.status_complete') }}
              </option>
            </select>
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
    items: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      localItems: this.items,
      showForm: false,
      form: {
        title: '',
        category: 'insurance',
        jurisdiction: 'uk',
        due_date: null,
      },
    };
  },

  methods: {
    create() {
      axios.post(`/${this.$page.props.auth.company.id}/account/compliance`, this.form)
        .then(response => {
          this.localItems = response.data.data;
          this.showForm = false;
          this.form.title = '';
        });
    },

    updateStatus(item, status) {
      axios.post(`/${this.$page.props.auth.company.id}/account/compliance/${item.id}/status`, { status })
        .then(response => {
          this.localItems = response.data.data;
        });
    },
  },
};
</script>
