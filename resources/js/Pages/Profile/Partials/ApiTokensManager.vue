<style lang="scss" scoped>
.token-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 0;
  border-top: 1px solid var(--color-steel-line);

  &:first-of-type {
    border-top: none;
  }
}

.token-name {
  font-weight: 600;
}

.token-meta {
  color: var(--color-ink-soft);
  font-size: 12.5px;
}

.token-value {
  font-family: var(--font-mono);
  font-size: 13px;
  background: var(--color-paper);
  border: 1px solid var(--color-steel-line);
  border-radius: 8px;
  padding: 10px 12px;
  word-break: break-all;
  margin-bottom: 10px;
}

.revoke-link {
  color: var(--color-ink-soft);
  cursor: pointer;
  font-size: 13px;

  &:hover {
    color: #8a1f1f;
  }
}
</style>

<template>
  <form-section @submitted="create">
    <template #title>
      {{ $t('ai.tokens_title') }}
    </template>

    <template #description>
      {{ $t('ai.tokens_description') }}
    </template>

    <template #form>
      <!-- Newly created token, shown once -->
      <div v-if="newToken" class="mb3">
        <p class="f7 fw6 mb1">{{ $t('ai.tokens_created_title') }}</p>
        <p class="f7 grey mb2">{{ $t('ai.tokens_created_warning') }}</p>
        <div class="token-value">
          {{ newToken }}
        </div>
        <button type="button" class="btn mr2" @click.prevent="copyToken">
          {{ copied ? $t('ai.tokens_copied') : $t('ai.tokens_copy') }}
        </button>
        <button type="button" class="btn" @click.prevent="newToken = null">
          {{ $t('ai.tokens_done') }}
        </button>
      </div>

      <template v-else>
        <text-input v-model="form.name"
                    :name="'name'"
                    :errors="form.errors.name"
                    :label="$t('ai.tokens_name_label')"
                    :placeholder="$t('ai.tokens_name_placeholder')"
        />
      </template>

      <div v-if="tokens.length > 0" class="mt3">
        <div v-for="token in tokens" :key="token.id" class="token-row">
          <div>
            <div class="token-name">
              {{ token.name }}
            </div>
            <div class="token-meta">
              {{ token.last_used_at ? $t('ai.tokens_last_used', { time: token.last_used_at }) : $t('ai.tokens_never_used') }}
              &middot; {{ token.created_at }}
            </div>
          </div>
          <span class="revoke-link" @click.prevent="revoke(token)">
            {{ $t('ai.tokens_revoke') }}
          </span>
        </div>
      </div>
      <p v-else class="f7 grey mt3">{{ $t('ai.tokens_empty') }}</p>
    </template>

    <template v-if="!newToken" #actions>
      <loading-button :class="'btn add'" :state="loadingState" :text="$t('ai.tokens_create')" />
    </template>
  </form-section>
</template>

<script>
import FormSection from '@/Shared/Layout/FormSection';
import TextInput from '@/Shared/TextInput';
import LoadingButton from '@/Shared/LoadingButton';

export default {
  components: {
    FormSection,
    TextInput,
    LoadingButton,
  },

  data() {
    return {
      tokens: [],
      newToken: null,
      copied: false,
      loadingState: '',
      form: {
        name: null,
        errors: [],
      },
    };
  },

  mounted() {
    this.load();
  },

  methods: {
    load() {
      axios.get('/user/api-tokens')
        .then(response => {
          this.tokens = response.data.data;
        });
    },

    create() {
      this.loadingState = 'loading';
      this.form.errors = [];

      axios.post('/user/api-tokens', { name: this.form.name })
        .then(response => {
          this.loadingState = null;
          this.newToken = response.data.data.plain_text_token;
          this.form.name = null;
          this.flash(this.$t('ai.tokens_created_success'), 'success');
          this.load();
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = error.response.data.errors || {};
        });
    },

    copyToken() {
      navigator.clipboard.writeText(this.newToken);
      this.copied = true;
      setTimeout(() => { this.copied = false; }, 2000);
    },

    revoke(token) {
      if (!window.confirm(this.$t('ai.tokens_revoke_confirm'))) {
        return;
      }

      axios.delete(`/user/api-tokens/${token.id}`)
        .then(() => {
          this.tokens = this.tokens.filter(entry => entry.id !== token.id);
          this.flash(this.$t('ai.tokens_revoked_success'), 'success');
        });
    },
  },
};
</script>
