<style lang="scss" scoped>
.line-item {
  border-bottom: 1px solid #eee;
}
.warning {
  background: #fff8e1;
  border-left: 3px solid #f0ad4e;
}
</style>

<template>
  <div class="mw7 center pa3">
    <h2 class="fw4 mb1">BetterOff.FYI — hiring cost calculator</h2>
    <p class="f7 gray mb4">
      A cost estimate, not immigration, tax or legal advice. Figures come from a versioned rates table —
      every line below shows the rate key and as-at date it used.
    </p>

    <div class="bg-white br3 box pa3 mb4">
      <h3 class="f5 fw6 mb3">Employer: UK hire vs. sponsored overseas hire</h3>

      <label class="db mb2 f6">Annual salary (GBP)
        <input v-model.number="employer.annual_salary" type="number" class="db w-100 pa2 ba b--black-20 br2" />
      </label>

      <label class="db mb2 f6">SOC code (optional, for the going-rate check)
        <input v-model="employer.soc_code" type="text" placeholder="e.g. 2136" class="db w-100 pa2 ba b--black-20 br2" />
      </label>

      <label class="db mb2 f6">
        <input v-model="employer.has_sponsor_licence" type="checkbox" /> Company already holds a sponsor licence
      </label>

      <label class="db mb3 f6">Employer size
        <select v-model="employer.employer_size_class" class="db w-100 pa2 ba b--black-20 br2">
          <option value="small">Small / medium</option>
          <option value="large">Large</option>
        </select>
      </label>

      <button class="pa2 ph3 br2 bn white bg-dark-blue" @click="calculateBoth">Compare UK vs sponsored</button>

      <div v-if="ukResult && sponsoredResult" class="mt4">
        <div class="flex justify-between mb2">
          <strong>UK hire total</strong>
          <span>£{{ ukResult.total_cost.toLocaleString() }}</span>
        </div>
        <div class="flex justify-between mb3">
          <strong>Sponsored hire total</strong>
          <span>£{{ sponsoredResult.total_cost.toLocaleString() }}</span>
        </div>
        <div class="flex justify-between mb3 f5 fw6">
          <span>Difference</span>
          <span>£{{ (sponsoredResult.total_cost - ukResult.total_cost).toLocaleString() }}</span>
        </div>

        <div v-for="w in sponsoredResult.warnings" :key="w" class="warning pa2 mb2 f7">{{ w }}</div>

        <h4 class="f6 fw6 mt3 mb2">Sponsored hire, line by line</h4>
        <div v-for="item in sponsoredResult.line_items" :key="item.key" class="line-item flex justify-between pv2 f7">
          <span>{{ item.key.replace(/_/g, ' ') }} <span v-if="item.note" class="gray">— {{ item.note }}</span></span>
          <span>£{{ item.amount.toLocaleString() }}</span>
        </div>
        <p class="f7 gray mt2">As of {{ sponsoredResult.as_of_date }}. Every figure comes from a sourced, dated rates table — not a live gov.uk lookup.</p>
      </div>
    </div>

    <div class="bg-white br3 box pa3 mb4">
      <h3 class="f5 fw6 mb3">Candidate: are they better off?</h3>

      <label class="db mb2 f6">Current annual salary (GBP)
        <input v-model.number="candidate.current_annual_salary" type="number" class="db w-100 pa2 ba b--black-20 br2" />
      </label>
      <label class="db mb2 f6">Current location
        <select v-model="candidate.current_location_cost_index_key" class="db w-100 pa2 ba b--black-20 br2">
          <option value="cost_of_living.uk.manchester">Manchester (estimate)</option>
          <option value="cost_of_living.overseas.default">Overseas — default (estimate)</option>
        </select>
      </label>
      <label class="db mb2 f6">New annual salary (GBP)
        <input v-model.number="candidate.new_annual_salary" type="number" class="db w-100 pa2 ba b--black-20 br2" />
      </label>
      <label class="db mb3 f6">New location
        <select v-model="candidate.new_location_cost_index_key" class="db w-100 pa2 ba b--black-20 br2">
          <option value="cost_of_living.uk.london">London (estimate)</option>
          <option value="cost_of_living.uk.manchester">Manchester (estimate)</option>
        </select>
      </label>

      <button class="pa2 ph3 br2 bn white bg-dark-blue" @click="calculateCandidate">Calculate</button>

      <div v-if="candidateResult" class="mt4">
        <div class="flex justify-between f5 fw6 mb2">
          <span>Better off by</span>
          <span>£{{ candidateResult.better_off_by_per_month.toLocaleString() }} / month</span>
        </div>
        <div v-for="a in candidateResult.assumptions" :key="a" class="f7 gray mb1">{{ a }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      employer: {
        annual_salary: 55000,
        soc_code: '2136',
        has_sponsor_licence: false,
        employer_size_class: 'small',
      },
      candidate: {
        current_annual_salary: 45000,
        current_location_cost_index_key: 'cost_of_living.uk.manchester',
        new_annual_salary: 60000,
        new_location_cost_index_key: 'cost_of_living.uk.london',
      },
      ukResult: null,
      sponsoredResult: null,
      candidateResult: null,
    };
  },

  methods: {
    async calculateBoth() {
      const uk = await axios.post('/betteroff/calculate', { ...this.employer, hire_type: 'uk' });
      const sponsored = await axios.post('/betteroff/calculate', { ...this.employer, hire_type: 'sponsored' });
      this.ukResult = uk.data.result;
      this.sponsoredResult = sponsored.data.result;
    },

    async calculateCandidate() {
      const response = await axios.post('/betteroff/calculate/candidate', this.candidate);
      this.candidateResult = response.data.result;
    },
  },
};
</script>
