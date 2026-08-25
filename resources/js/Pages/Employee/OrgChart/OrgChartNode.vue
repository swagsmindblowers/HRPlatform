<style lang="scss" scoped>
.node {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  vertical-align: top;
  padding: 0 14px;
}

.card {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  border-radius: 12px;
  background: color-mix(in srgb, var(--color-paper-raised) 90%, transparent);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid var(--color-steel-line);
  box-shadow: var(--shadow-glass);
  white-space: nowrap;
  text-decoration: none;
  color: var(--color-ink);
  transition: transform 0.15s cubic-bezier(0.2, 0, 0, 1), border-color 0.15s ease;

  &:hover {
    border-color: var(--color-amber);
    color: var(--color-ink);
  }

  &:active {
    transform: scale(0.97);
  }
}

.avatar-img {
  border-radius: 999px;
  width: 28px;
  height: 28px;
  flex: none;
}

.name {
  font-weight: 600;
  font-size: 13px;
}

.position {
  font-size: 11px;
  color: var(--color-ink-soft);
}

.children {
  position: relative;
  display: flex;
  padding-top: 24px;
  margin-top: 4px;

  &:before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    width: 1px;
    height: 24px;
    background: var(--color-steel-line);
  }
}

.child-branch {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;

  &:before {
    content: '';
    position: absolute;
    top: -24px;
    left: 50%;
    width: 1px;
    height: 24px;
    background: var(--color-steel-line);
  }
}
</style>

<template>
  <div class="node">
    <inertia-link :href="node.url" class="card" :data-cy="'org-chart-node-' + node.id">
      <img v-if="node.avatar" :src="node.avatar" class="avatar-img" loading="lazy" alt="" />
      <span>
        <span class="name db">{{ node.name }}</span>
        <span v-if="node.position" class="position db">{{ node.position }}</span>
      </span>
    </inertia-link>

    <div v-if="node.children && node.children.length > 0" class="children">
      <div v-for="child in node.children" :key="child.id" class="child-branch">
        <org-chart-node :node="child" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'OrgChartNode',

  props: {
    node: {
      type: Object,
      required: true,
    },
  },
};
</script>
