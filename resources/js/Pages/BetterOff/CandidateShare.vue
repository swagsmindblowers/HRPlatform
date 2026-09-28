<template>
  <div class="mw7 center pa3">
    <h2 class="fw4 mb1">Your relocation outlook</h2>
    <p class="f7 gray mb4">Shared by the employer. A cost estimate, not financial or tax advice.</p>

    <div v-if="result" class="bg-white br3 box pa3">
      <div class="flex justify-between f5 fw6 mb2">
        <span>Better off by</span>
        <span>£{{ result.better_off_by_per_month.toLocaleString() }} / month</span>
      </div>
      <div v-for="a in result.assumptions" :key="a" class="f7 gray mb1">{{ a }}</div>
      <p class="f7 gray mt2">As of {{ asOfDate }}, rates version {{ ratesVersionTag }}.</p>
    </div>
    <div v-else class="bg-white br3 box pa3 f6 gray">
      This link has expired or no candidate figures were included in this scenario.
    </div>
  </div>
</template>

<script>
export default {
  props: {
    result: {
      type: Object,
      default: null,
    },
    asOfDate: {
      type: String,
      default: null,
    },
    ratesVersionTag: {
      type: String,
      default: null,
    },
  },
};
</script>
