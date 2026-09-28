<style lang="scss" scoped>
.line-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid var(--color-steel-line);
}
.warning {
  background-color: var(--color-amber-tint);
  color: var(--color-amber-ink);
  padding: 8px;
  border-radius: 4px;
  margin-bottom: 8px;
  font-size: 12px;
}
</style>

<template>
  <layout :notifications="notifications">
    <div class="ph2 ph0-ns mw7 center">
      <h2 class="fw4 mb1">Hiring cost panel</h2>
      <p class="f7 gray mb3" v-if="jobOpening">For role: {{ jobOpening.title }}</p>

      <div class="bg-white br3 box pa3 mb3">
        <label class="db mb2 f6">Hire type
          <select v-model="form.hire_type" class="db w-100 pa2 ba b--black-20 br2">
            <option value="uk">UK hire</option>
            <option value="sponsored">Sponsored overseas hire</option>
          </select>
        </label>

        <label class="db mb2 f6">Annual salary (GBP)
          <input v-model.number="form.annual_salary" type="number" class="db w-100 pa2 ba b--black-20 br2" />
        </label>

        <template v-if="form.hire_type === 'sponsored'">
          <label class="db mb2 f6">SOC code
            <input v-model="form.soc_code" type="text" placeholder="e.g. 2136" class="db w-100 pa2 ba b--black-20 br2" />
          </label>
          <label class="db mb2 f6">
            <input v-model="form.has_sponsor_licence" type="checkbox" /> Company already holds a sponsor licence
          </label>
          <label class="db mb2 f6">Employer's share of visa fees (%)
            <input v-model.number="form.employer_visa_cost_share_percent" type="number" min="0" max="100" class="db w-100 pa2 ba b--black-20 br2" />
          </label>
        </template>

        <button class="pa2 ph3 br2 bn white bg-dark-blue mr2" @click="calculate">Calculate</button>
        <button v-if="result" class="pa2 ph3 br2 ba b--black-20 bg-white" @click="save">Save scenario</button>
      </div>

      <div v-if="result" class="bg-white br3 box pa3 mb3">
        <div v-for="w in result.warnings" :key="w" class="warning">{{ w }}</div>

        <div v-for="item in result.line_items" :key="item.key" class="line-item f7">
          <span>{{ item.key.replace(/_/g, ' ') }} <span v-if="item.note" class="gray">— {{ item.note }}</span></span>
          <span>£{{ item.amount.toLocaleString() }}</span>
        </div>
        <div class="line-item f5 fw6">
          <span>Total</span>
          <span>£{{ result.total_cost.toLocaleString() }}</span>
        </div>
        <p class="f7 gray mt2">As of {{ result.as_of_date }}, rates version {{ ratesVersionTag }}.</p>
      </div>

      <div v-if="savedScenario" class="bg-white br3 box pa3 mb3">
        <p class="f6 mb2">Scenario saved.</p>
        <button class="pa2 ph3 br2 ba b--black-20 bg-white" @click="share">Get candidate share link</button>
        <p v-if="shareUrl" class="f7 gray mt2 break-word">{{ shareUrl }}</p>
      </div>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';

export default {
  components: {
    Layout,
  },

  props: {
    notifications: {
      type: Array,
      default: null,
    },
    jobOpening: {
      type: Object,
      default: null,
    },
    companySettings: {
      type: Object,
      default: () => ({}),
    },
  },

  data() {
    return {
      form: {
        hire_type: 'uk',
        annual_salary: 50000,
        soc_code: '',
        has_sponsor_licence: this.companySettings.hasSponsorLicence || false,
        employer_size_class: this.companySettings.employerSizeClass || 'small',
        employer_visa_cost_share_percent: this.companySettings.defaultEmployerVisaSharePercent ?? 100,
      },
      result: null,
      ratesVersionTag: null,
      savedScenario: null,
      shareUrl: null,
    };
  },

  computed: {
    companyId() {
      return this.$page.props.auth.company.id;
    },
  },

  methods: {
    async calculate() {
      const response = await axios.post(`/${this.companyId}/betteroff/calculate`, this.form);
      this.result = response.data.result;
      this.ratesVersionTag = response.data.rates_version_tag;
      this.savedScenario = null;
      this.shareUrl = null;
    },

    async save() {
      const response = await axios.post(`/${this.companyId}/betteroff/scenarios`, {
        hire_type: this.form.hire_type,
        as_of_date: this.result.as_of_date,
        employer_inputs: this.form,
        employer_result: this.result,
        job_opening_id: this.jobOpening ? this.jobOpening.id : null,
      });
      this.savedScenario = response.data.data;
    },

    async share() {
      const response = await axios.post(`/${this.companyId}/betteroff/scenarios/${this.savedScenario.id}/share`);
      this.shareUrl = response.data.share_url;
    },
  },
};
</script>
