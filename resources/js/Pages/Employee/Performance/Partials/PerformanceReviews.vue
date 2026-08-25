<style lang="scss" scoped>
.review-row {
  padding: 12px 0;
  border-bottom: 1px solid var(--color-steel-line);

  &:last-child {
    border-bottom: 0;
  }
}

.status-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 2px 9px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
  background-color: var(--color-green-tint);
  color: var(--color-green-ink);

  &.draft {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }
}
</style>

<template>
  <div class="mb4 relative">
    <span class="db fw5 mb2">
      <span class="mr1">
        ⭐
      </span> {{ $t('employee.performance_reviews_title') }}
    </span>

    <div class="br3 box z-1 pa3">
      <div v-if="permissions.can_see_performance_tab" class="mb3">
        <a class="btn add dib" data-cy="add-performance-review" @click.prevent="showForm = !showForm">
          {{ $t('employee.performance_reviews_add') }}
        </a>
      </div>

      <form v-if="showForm" class="mb4" @submit.prevent="create">
        <select v-model="form.review_type" class="mb2 db">
          <option value="30_day">
            30-day
          </option>
          <option value="60_day">
            60-day
          </option>
          <option value="90_day">
            90-day
          </option>
          <option value="ad_hoc">
            Ad hoc
          </option>
        </select>
        <input v-model="form.review_date" type="date" class="mb2 db" required />
        <textarea v-model="form.strengths" :placeholder="$t('employee.performance_reviews_strengths')" class="mb2 db w-100"></textarea>
        <textarea v-model="form.areas_for_improvement" :placeholder="$t('employee.performance_reviews_areas')" class="mb2 db w-100"></textarea>
        <button type="submit" class="btn add">
          {{ $t('employee.performance_reviews_save') }}
        </button>
      </form>

      <p v-if="localReviews.length === 0" class="mv0 f7" style="color: var(--color-ink-soft)">
        {{ $t('employee.performance_reviews_none') }}
      </p>

      <div v-for="review in localReviews" :key="review.id" class="review-row" :data-cy="'performance-review-' + review.id">
        <div class="flex justify-between items-center mb1">
          <span class="fw5 f6">
            {{ review.review_type }} — {{ review.review_date }}
          </span>
          <span :class="['status-chip', review.status]">
            {{ review.status }}
          </span>
        </div>
        <p v-if="review.strengths" class="f7 mv1">{{ $t('employee.performance_reviews_strengths') }}: {{ review.strengths }}</p>
        <p v-if="review.areas_for_improvement" class="f7 mv1">{{ $t('employee.performance_reviews_areas') }}: {{ review.areas_for_improvement }}</p>
        <p class="f7 mv1" style="color: var(--color-ink-soft)">{{ $t('employee.performance_reviews_by', { name: review.reviewer_name }) }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    employee: {
      type: Object,
      default: null,
    },
    permissions: {
      type: Object,
      default: null,
    },
    reviews: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      localReviews: this.reviews,
      showForm: false,
      form: {
        review_type: '30_day',
        review_date: null,
        strengths: '',
        areas_for_improvement: '',
      },
    };
  },

  methods: {
    create() {
      axios.post(`/${this.$page.props.auth.company.id}/employees/${this.employee.id}/performance/reviews`, this.form)
        .then(response => {
          this.localReviews = response.data.data;
          this.showForm = false;
        });
    },
  },
};
</script>
