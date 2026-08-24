<style lang="scss" scoped>
.launcher {
  position: fixed;
  right: 24px;
  bottom: 24px;
  width: 52px;
  height: 52px;
  border-radius: 999px;
  background: var(--color-ink);
  color: var(--color-paper);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(29, 30, 39, 0.25);
  z-index: 60;
  transition: transform 0.15s cubic-bezier(0.2, 0, 0, 1);

  &:active {
    transform: scale(0.96);
  }

  svg {
    width: 22px;
    height: 22px;
  }
}

.panel {
  position: fixed;
  right: 24px;
  bottom: 88px;
  width: 360px;
  max-width: calc(100vw - 48px);
  height: 480px;
  max-height: calc(100vh - 140px);
  background: var(--color-paper-raised);
  border: 1px solid var(--color-steel-line);
  border-radius: 16px;
  box-shadow: 0 20px 48px rgba(29, 30, 39, 0.18);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  z-index: 60;
}

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px;
  border-bottom: 1px solid var(--color-steel-line);
  font-family: var(--font-display);
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.01em;
  font-size: 15px;
}

.close-btn {
  cursor: pointer;
  color: var(--color-ink-soft);
  font-size: 18px;
  line-height: 1;

  &:hover {
    color: var(--color-ink);
  }
}

.panel-body {
  flex: 1;
  overflow-y: auto;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.bubble {
  max-width: 85%;
  padding: 8px 12px;
  border-radius: 12px;
  font-size: 13.5px;
  line-height: 1.4;
  white-space: pre-wrap;

  &.user {
    align-self: flex-end;
    background: var(--color-amber);
    color: #1b1206;
  }

  &.assistant {
    align-self: flex-start;
    background: var(--color-paper);
    border: 1px solid var(--color-steel-line);
    color: var(--color-ink);
  }
}

.empty-state {
  color: var(--color-ink-soft);
  font-size: 13px;
  padding: 12px 0;
}

.panel-footer {
  border-top: 1px solid var(--color-steel-line);
  padding: 10px;
  display: flex;
  gap: 8px;
}

.chat-input {
  flex: 1;
  border: 1px solid var(--color-steel-line);
  border-radius: 999px;
  padding: 8px 14px;
  font-size: 13.5px;
  outline: none;

  &:focus {
    border-color: var(--color-amber);
  }
}

.send-btn {
  border: none;
  background: var(--color-amber);
  color: #1b1206;
  border-radius: 999px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  flex: none;

  &:active {
    transform: scale(0.96);
  }

  &:disabled {
    opacity: 0.5;
    cursor: default;
  }
}
</style>

<template>
  <div>
    <div class="launcher" data-cy="ai-assistant-launcher" @click="toggle">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
           stroke-linejoin="round"
      >
        <path d="M12 3a2 2 0 0 1 2 2v1h2a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V9a3 3 0 0 1 3-3h2V5a2 2 0 0 1 2-2Z" /><path d="M8 13h.01M16 13h.01M9 17h6" />
      </svg>
    </div>

    <div v-if="open" class="panel">
      <div class="panel-header">
        <span>{{ $t('ai.chat_title') }}</span>
        <span class="close-btn" @click="toggle">
          ✕
        </span>
      </div>

      <div ref="body" class="panel-body">
        <p v-if="messages.length === 0" class="empty-state">{{ $t('ai.chat_empty') }}</p>
        <div v-for="(message, index) in messages" :key="index" :class="['bubble', message.role]">
          {{ message.text }}
        </div>
        <div v-if="loading" class="bubble assistant">
          …
        </div>
      </div>

      <form class="panel-footer" @submit.prevent="send">
        <input v-model="draft" type="text" class="chat-input" :placeholder="$t('ai.chat_placeholder')" :disabled="loading" />
        <button type="submit" class="send-btn" :disabled="loading || !draft">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
               stroke-linejoin="round" width="16" height="16"
          >
            <path d="m5 12 14-7-7 14-2-5-5-2Z" />
          </svg>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      open: false,
      draft: '',
      loading: false,
      messages: [],
      history: [],
    };
  },

  methods: {
    toggle() {
      this.open = !this.open;
    },

    send() {
      const text = this.draft.trim();
      if (!text || this.loading) {
        return;
      }

      this.messages.push({ role: 'user', text });
      this.draft = '';
      this.loading = true;
      this.scrollToBottom();

      axios.post(`${this.$page.props.auth.company.id}/ai/chat`, {
        message: text,
        history: this.history,
      })
        .then(response => {
          this.loading = false;
          this.history = response.data.history;
          this.messages.push({ role: 'assistant', text: response.data.reply });
          this.scrollToBottom();
        })
        .catch(error => {
          this.loading = false;
          const message = (error.response && error.response.data && error.response.data.error)
            || this.$t('ai.chat_error');
          this.messages.push({ role: 'assistant', text: message });
          this.scrollToBottom();
        });
    },

    scrollToBottom() {
      this.$nextTick(() => {
        if (this.$refs.body) {
          this.$refs.body.scrollTop = this.$refs.body.scrollHeight;
        }
      });
    },
  },
};
</script>
