<style lang="scss" scoped>
.grey {
  color: #6e6e71;
}

.range {
  display: block;
  height: 5px;
  width: 100%;
  border-top: 1px solid #e8e8e8;
  border-left: 1px solid #e8e8e8;
  border-right: 1px solid #e8e8e8;
}

.days-left {
  float: right;
}

.progress {
  background-color: #edf2f7;
  border-radius: 3px;

  .inside {
    background-color: #CAD5E1;
    border-top-left-radius: 3px;
    border-bottom-left-radius: 3px;
    height: 16px;
  }

  .holiday {
    background-color: #68D391;
    height: 16px;
    top: 0;
  }
}

.date {
  color: #999999;
}

.type-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  padding: 2px 9px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;

  &.holiday {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }

  &.sick {
    background-color: #fbdada;
    color: #8a1f1f;
  }

  &.pto {
    background-color: var(--color-green-tint);
    color: var(--color-green-ink);
  }
}

.upcoming-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 0;
  border-top: 1px solid var(--color-steel-line);

  &:first-of-type {
    border-top: none;
  }
}

.cancel-btn {
  color: var(--color-ink-soft);
  cursor: pointer;

  &:hover {
    color: #8a1f1f;
  }
}
</style>

<template>
  <div class="mb4 relative">
    <span class="db fw5 mb2">
      <span class="mr1">
        🌴
      </span> {{ $t('employee.holidays_title') }}
    </span>
    <img v-show="canManage" loading="lazy" src="/img/plus_button.svg" class="box-plus-button absolute br-100 pa2 bg-white pointer" data-cy="add-holiday-button"
         width="22"
         height="22" alt="add button"
         @click.prevent="openModal"
    />

    <div class="br3 bg-white box z-1 pa3">
      <!-- Available balance -->
      <div class="flex justify-between mb4 mt3">
        <div class="w-50 f4 fw3">
          {{ $t('employee.holidays_available_balance') }}
        </div>
        <div class="w-50 tr f3">
          {{ employee.holidays.current_balance_round }} days
        </div>
      </div>

      <!-- Number of holidays total in year -->
      <p class="f7 grey tc mb1">
        {{ employee.holidays.amount_of_allowed_holidays }} days of holidays earned in a year
      </p>
      <div class="range mb1"></div>

      <!-- Days left to earn -->
      <div class="cf">
        <div class="fl" :style="'width: ' + employee.holidays.percent_year_completion_rate + '%'">
          &nbsp;
        </div>
        <div class="fl" :style="'width: ' + employee.holidays.reverse_percent_year_completion_rate + '%'">
          <p class="f7 grey tc mb1 mt1">
            {{ employee.holidays.number_holidays_left_to_earn_this_year }} days left to earn
          </p>
          <div class="range mb1"></div>
        </div>
      </div>

      <!-- Progress bar -->
      <div class="progress relative">
        <div class="inside" :style="'width: ' + employee.holidays.percent_year_completion_rate + '%'"></div>
      </div>
      <div class="flex justify-between mb4">
        <div class="w-50 f7 date mt2">
          Jan 1
        </div>
        <div class="w-50 f7 date mt2 tr">
          Dec 31
        </div>
      </div>

      <!-- Holidays statistics -->
      <div class="flex items-start-ns flex-wrap flex-nowrap-ns mb3">
        <div class="mb1 w-third-ns w-50 mr4-ns">
          <p class="db mb2 mt0 f3 fw3">
            {{ employee.holidays.days_taken_so_far_this_year }} days
          </p>
          <p class="f7 mt0 fw3 grey lh-copy">
            {{ $t('employee.holidays_taken_so_far') }}
          </p>
        </div>
        <div class="mb1 w-third-ns w-50 mr4-ns">
          <p class="db mb2 mt0 f3">
            {{ employee.holidays.holidays_earned_each_month }} days
          </p>
          <p class="f7 mt0 fw3 grey lh-copy">
            {{ $t('employee.holidays_earned_each_month') }}
          </p>
        </div>
        <div class="mb1 w-third-ns w-50">
          <p class="db mb2 mt0 f3 fw3">
            {{ employee.holidays.estimated_balance_end_of_year }} days
          </p>
          <p class="f7 mt0 fw3 grey lh-copy">
            {{ $t('employee.holidays_estimated_balance') }}
          </p>
        </div>
      </div>

      <!-- Upcoming time off -->
      <div v-if="upcoming.length > 0" class="mt2">
        <p class="f6 fw6 mb2">{{ $t('employee.holidays_upcoming') }}</p>
        <div v-for="item in upcoming" :key="item.id" class="upcoming-row">
          <div>
            <span class="mr2">
              {{ item.date }}
            </span>
            <span :class="['type-chip', item.type]">
              {{ item.type }}
            </span>
            <span v-if="!item.full" class="f7 grey ml2">
              {{ $t('employee.holidays_half_day') }}
            </span>
          </div>
          <span v-if="canManage" class="cancel-btn f6" @click.prevent="cancel(item)">
            ✕
          </span>
        </div>
      </div>

      <p v-else class="f7 grey tc mt2">{{ $t('employee.holidays_no_upcoming') }}</p>
    </div>

    <!-- Request time off modal -->
    <dialog-modal :show="showModal" @close="closeModal">
      <template #title>
        {{ $t('employee.holidays_modal_title') }}
      </template>

      <template #content>
        <errors :errors="form.errors" />

        <div class="mb3">
          <label class="db fw5 mb1" for="timeoff-date">
            {{ $t('employee.holidays_modal_date') }}
          </label>
          <input id="timeoff-date" v-model="form.date" type="date" class="w-100 pa2 br2 ba b--black-20" required />
        </div>

        <div class="mb3">
          <label class="db fw5 mb1" for="timeoff-type">
            {{ $t('employee.holidays_modal_type') }}
          </label>
          <select id="timeoff-type" v-model="form.type" class="w-100 pa2 br2 ba b--black-20">
            <option value="holiday">
              {{ $t('employee.holidays_type_holiday') }}
            </option>
            <option value="sick">
              {{ $t('employee.holidays_type_sick') }}
            </option>
            <option value="pto">
              {{ $t('employee.holidays_type_pto') }}
            </option>
          </select>
        </div>

        <div class="mb3">
          <label class="db fw5 mb1">
            <input v-model="form.full" type="checkbox" class="mr2" />
            {{ $t('employee.holidays_modal_full_day') }}
          </label>
        </div>
      </template>

      <template #footer>
        <button class="btn mr2" data-cy="cancel-timeoff-modal" @click.prevent="closeModal">
          {{ $t('app.cancel') }}
        </button>
        <loading-button type="button" :class="'btn add'" :state="loadingState" :text="$t('employee.holidays_modal_submit')" data-cy="submit-timeoff"
                        @click="submit"
        />
      </template>
    </dialog-modal>
  </div>
</template>

<script>
import DialogModal from '@/Shared/DialogModal';
import Errors from '@/Shared/Errors';
import LoadingButton from '@/Shared/LoadingButton';

export default {
  components: {
    DialogModal,
    Errors,
    LoadingButton,
  },

  props: {
    employee: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      showModal: false,
      loadingState: '',
      upcoming: this.employee.holidays.upcoming || [],
      form: {
        date: null,
        type: 'holiday',
        full: true,
        errors: [],
      },
    };
  },

  computed: {
    canManage() {
      return this.employee.is_current_user || this.$page.props.auth.employee.permission_level <= 200;
    },
  },

  methods: {
    openModal() {
      this.form.date = null;
      this.form.type = 'holiday';
      this.form.full = true;
      this.form.errors = [];
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
    },

    submit() {
      if (!this.form.date) {
        this.form.errors = { date: [this.$t('employee.holidays_modal_date_required')] };
        return;
      }

      this.loadingState = 'loading';

      axios.post(`${this.$page.props.auth.company.id}/employees/${this.employee.id}/timeoff`, {
        date: this.form.date,
        type: this.form.type,
        full: this.form.full,
      })
        .then(response => {
          this.loadingState = null;
          this.showModal = false;
          this.upcoming.push({
            id: response.data.data.id,
            date: this.form.date,
            raw_date: this.form.date,
            type: this.form.type,
            full: this.form.full,
          });
          this.upcoming.sort((a, b) => a.raw_date.localeCompare(b.raw_date));
          this.flash(this.$t('employee.holidays_modal_success'), 'success');
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = { date: [error.response.data.error || this.$t('app.error_try_again')] };
        });
    },

    cancel(item) {
      axios.delete(`${this.$page.props.auth.company.id}/employees/${this.employee.id}/timeoff/${item.id}`)
        .then(() => {
          this.upcoming = this.upcoming.filter(entry => entry.id !== item.id);
          this.flash(this.$t('employee.holidays_cancel_success'), 'success');
        })
        .catch(error => {
          this.flash(error.response.data.error || this.$t('app.error_try_again'), 'error');
        });
    },
  },
};
</script>
