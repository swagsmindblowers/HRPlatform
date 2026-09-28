<template>
  <div class="mw7 center pa3">
    <h1 class="f3 fw4 mb1">Cost of hiring an overseas software engineer in the UK</h1>
    <p class="f7 gray mb4">
      Live figures from BetterOff.FYI's rates table, as of {{ asOfDate }} — not a static estimate, and not
      immigration, tax or legal advice.
    </p>

    <div class="bg-white br3 box pa3 mb4">
      <div class="flex justify-between mb2">
        <strong>UK hire total (example: £55,000 salary)</strong>
        <span>£{{ uk.total_cost.toLocaleString() }}</span>
      </div>
      <div class="flex justify-between mb3">
        <strong>Sponsored overseas hire total (same salary)</strong>
        <span>£{{ sponsored.total_cost.toLocaleString() }}</span>
      </div>
      <div class="flex justify-between f5 fw6">
        <span>Extra cost of sponsoring vs. hiring locally</span>
        <span>£{{ difference.toLocaleString() }}</span>
      </div>
    </div>

    <a href="/betteroff" class="pa2 ph3 br2 bn white bg-dark-blue dib">Run your own numbers</a>
  </div>
</template>

<script>
export default {
  props: {
    uk: { type: Object, required: true },
    sponsored: { type: Object, required: true },
    difference: { type: Number, required: true },
    asOfDate: { type: String, required: true },
  },
};
</script>
