<style lang="scss" scoped>
.provider-card {
  background: color-mix(in srgb, var(--color-paper-raised) 90%, transparent);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid var(--color-steel-line);
  border-radius: 16px;
  box-shadow: var(--shadow-glass);
  padding: 24px;
  margin-bottom: 16px;
}

.status-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  background-color: #fbdada;
  color: #8a1f1f;

  &.connected {
    background-color: var(--color-green-tint);
    color: var(--color-green-ink);
  }

  &.error {
    background-color: #fbdada;
    color: #8a1f1f;
  }
}

.error-note {
  font-size: 12.5px;
  color: #b23b3b;
  margin-top: 8px;
}
</style>

<template>
  <layout :notifications="notifications">
    <div class="ph2 ph5-ns mt4">
      <breadcrumb :has-more="false">
        {{ $t('integrations.title') }}
      </breadcrumb>

      <div class="mw7 center mb5">
        <h2 class="fw7 mb1">
          {{ $t('integrations.title') }}
        </h2>
        <p class="mb4" style="color: var(--color-ink-soft)">{{ $t('integrations.subtitle') }}</p>

        <div class="provider-card" data-cy="xero-integration-card">
          <div class="flex justify-between items-center mb2">
            <span class="fw6 f5">
              Xero
            </span>
            <span :class="['status-chip', integrations.xero.status]">
              {{ integrations.xero.status }}
            </span>
          </div>
          <p class="f7 mb3" style="color: var(--color-ink-soft)">{{ $t('integrations.xero_body') }}</p>
          <a v-if="integrations.xero.status !== 'connected'" class="btn add dib" :href="'/' + $page.props.auth.company.id + '/integrations/xero/connect'">
            {{ $t('integrations.connect') }}
          </a>
          <a v-else class="btn dib" @click.prevent="disconnect('xero')">
            {{ $t('integrations.disconnect') }}
          </a>
          <p v-if="integrations.xero.last_error" class="error-note">{{ integrations.xero.last_error }}</p>
        </div>

        <div class="provider-card" data-cy="deel-integration-card">
          <div class="flex justify-between items-center mb2">
            <span class="fw6 f5">
              Deel
            </span>
            <span :class="['status-chip', integrations.deel.status]">
              {{ integrations.deel.status }}
            </span>
          </div>
          <p class="f7 mb3" style="color: var(--color-ink-soft)">{{ $t('integrations.deel_body') }}</p>
          <a v-if="integrations.deel.status !== 'connected'" class="btn add dib" :href="'/' + $page.props.auth.company.id + '/integrations/deel/connect'">
            {{ $t('integrations.connect') }}
          </a>
          <a v-else class="btn dib" @click.prevent="disconnect('deel')">
            {{ $t('integrations.disconnect') }}
          </a>
          <p v-if="integrations.deel.last_error" class="error-note">{{ integrations.deel.last_error }}</p>
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
    integrations: {
      type: Object,
      default: () => ({}),
    },
  },

  methods: {
    disconnect(provider) {
      this.$inertia.post(`/${this.$page.props.auth.company.id}/integrations/${provider}/disconnect`);
    },
  },
};
</script>
