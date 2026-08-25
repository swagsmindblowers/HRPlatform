<style lang="scss" scoped>
.chart-scroller {
  overflow-x: auto;
  padding: 40px 20px;
}

.forest {
  display: flex;
  justify-content: center;
  gap: 60px;
  width: max-content;
  margin: 0 auto;
}
</style>

<template>
  <layout :notifications="notifications">
    <div class="ph2 ph5-ns">
      <breadcrumb
        :root-url="'/' + $page.props.auth.company.id + '/employees'"
        :root="$t('app.breadcrumb_employee_list')"
        :has-more="false"
      >
        {{ $t('employee.orgchart_title') }}
      </breadcrumb>

      <div class="mw9 center br3 mb5 box z-1 relative">
        <div v-if="tree.length === 0" class="pa4 tc f6" style="color: var(--color-ink-soft)">
          {{ $t('employee.orgchart_empty') }}
        </div>

        <div v-else class="chart-scroller">
          <div class="forest">
            <org-chart-node v-for="root in tree" :key="root.id" :node="root" />
          </div>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import Breadcrumb from '@/Shared/Layout/Breadcrumb';
import OrgChartNode from '@/Pages/Employee/OrgChart/OrgChartNode';

export default {
  components: {
    Layout,
    Breadcrumb,
    OrgChartNode,
  },

  props: {
    notifications: {
      type: Array,
      default: null,
    },
    tree: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
