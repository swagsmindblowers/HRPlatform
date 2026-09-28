<style lang="scss" scoped>
.disclaimer {
  border: 1px solid #f0b0b0;
  background-color: #fdeeee;
  color: #8a1f1f;
  border-radius: 10px;
  padding: 12px 14px;
  font-size: 13px;
  font-weight: 600;
}

.template-row {
  border-bottom: 1px solid var(--color-steel-line);
  padding: 14px 0;

  &:last-child {
    border-bottom: 0;
  }
}

.template-body {
  white-space: pre-wrap;
  font-family: var(--font-mono);
  font-size: 12px;
  max-height: 260px;
  overflow-y: auto;
  background: var(--color-paper);
  border: 1px solid var(--color-steel-line);
  border-radius: 8px;
  padding: 10px;
  margin-top: 8px;
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
        {{ $t('policytemplates.title') }}
      </breadcrumb>

      <div class="mw7 center br3 mb5 box relative z-1">
        <div class="pa3 mt2">
          <h2 class="tc normal mb3">
            {{ $t('policytemplates.title') }}
          </h2>

          <p class="disclaimer mb4" data-cy="policy-disclaimer">
            {{ $t('policytemplates.disclaimer') }}
          </p>

          <div v-for="template in templates" :key="template.id" class="template-row" :data-cy="'policy-template-' + template.id">
            <div class="flex justify-between items-center">
              <span class="fw5 f6">
                {{ template.title }}
              </span>
              <a class="f7 pointer" @click.prevent="toggle(template.id)">
                {{ expanded === template.id ? $t('policytemplates.hide') : $t('policytemplates.view') }}
              </a>
            </div>
            <span class="f7" style="color: var(--color-ink-soft)">
              {{ template.jurisdiction }} · {{ template.category }}
            </span>

            <div v-if="expanded === template.id">
              <pre class="template-body">{{ template.body }}</pre>
              <a class="btn dib mt2" @click.prevent="copy(template)">{{ $t('policytemplates.copy') }}</a>
            </div>
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
    templates: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      expanded: null,
    };
  },

  methods: {
    toggle(id) {
      this.expanded = this.expanded === id ? null : id;
    },

    copy(template) {
      navigator.clipboard.writeText(template.body).then(() => {
        this.flash(this.$t('policytemplates.copied'), 'success');
      });
    },
  },
};
</script>
