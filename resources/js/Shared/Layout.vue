<style lang="scss" scoped>
@import 'vue-loaders/dist/vue-loaders.css';

.find-box {
  border: 1px solid rgba(27,31,35,.15);
  box-shadow: 0 3px 12px rgba(27,31,35,.15);
  top: 63px;
  width: 500px;
  left: 0;
  right: 0;
  margin: 0 auto;
}

.bg-modal-find {
  position: fixed;
  z-index: 100;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
}

.main-nav {
  position: sticky;
  top: 0;
  z-index: 40;
  border-bottom: 1px solid var(--color-steel-line);
  background: color-mix(in srgb, var(--color-paper-raised) 82%, transparent);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
}

.brand-mark {
  background-color: var(--color-amber);
  box-shadow: var(--shadow-glow);
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 6px;
  border-radius: 999px;
  padding: 7px 12px;
  font-size: 13.5px;
  font-weight: 600;
  color: var(--color-ink);
  text-decoration: none;
  transition: background-color 0.15s ease, color 0.15s ease;

  &:hover,
  &:active {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }

  &.is-active {
    background-color: var(--color-amber-tint);
    color: var(--color-amber-ink);
  }

  svg {
    width: 16px;
    height: 16px;
    flex: none;
  }
}

.mobile-nav {
  background: var(--color-paper-raised);
  border-bottom: 1px solid var(--color-steel-line);
}

.mobile-nav-toggle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  color: var(--color-ink);
  cursor: pointer;

  svg {
    width: 22px;
    height: 22px;
  }
}

.mobile-nav-panel {
  border-top: 1px solid var(--color-steel-line);
  background: var(--color-paper-raised);
}

.mobile-nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  font-size: 15px;
  font-weight: 600;
  color: var(--color-ink);
  text-decoration: none;
  border-bottom: 1px solid var(--color-steel-line);

  &.is-active {
    color: var(--color-amber-ink);
    background-color: var(--color-amber-tint);
  }

  svg {
    width: 18px;
    height: 18px;
    flex: none;
  }
}

.mobile-nav-utility {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
}

.demo-banner {
  border-bottom: 1px solid var(--color-steel-line);
  background-color: var(--color-amber-tint);
  color: var(--color-amber-ink);
}

.ball-pulse {
  right: 8px;
  top: 10px;
  position: absolute;
}
</style>

<template>
  <div>
    <div class="dn db-m db-l">
      <!-- DEMO MODE -->
      <nav v-if="$page.props.demo_mode" class="demo-banner text-center px-3 py-3">
        <span class="mr1">
          ⚠️
        </span> {{ $t('app.demo_mode_desc') }} <a href="" class="underline">{{ $t('app.demo_mode_read_more') }}</a>
      </nav>

      <nav class="main-nav flex items-center justify-between px-3">
        <div class="flex items-center py-2">
          <inertia-link href="/home" class="flex items-center gap-2 mr-4 no-underline">
            <span class="brand-mark h-6 w-6 rounded-md"></span>
            <span class="font-display font-extrabold uppercase tracking-tight text-lg leading-none" style="color: var(--color-ink)">LaunchHR</span>
          </inertia-link>

          <!-- MENU -->
          <div v-if="!noMenu" class="flex items-center gap-0.5">
            <inertia-link v-if="$page.props.auth.employee.display_welcome_message" :href="'/' + $page.props.auth.company.id + '/welcome'" data-cy="header-desktop-welcome-tab" class="nav-link" :class="{ 'is-active': isActive('/welcome') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="M7 11V7a2 2 0 0 1 4 0v3" /><path d="M11 10.5V6a2 2 0 0 1 4 0v5" /><path d="M15 10.5V8a2 2 0 0 1 4 0v6a6 6 0 0 1-6 6h-2a6 6 0 0 1-5-2.7L4 13a1.5 1.5 0 0 1 2.5-1.7L7 12" /></svg>
              {{ $t('app.header_welcome') }}
            </inertia-link>
            <inertia-link :href="'/' + $page.props.auth.company.id + '/dashboard'" class="nav-link" :class="{ 'is-active': isActive('/dashboard') || isActive('/home') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="M4 11.5 12 4l8 7.5" /><path d="M6 10v9a1 1 0 0 0 1 1h3v-5h4v5h3a1 1 0 0 0 1-1v-9" /></svg>
              {{ $t('app.header_home') }}
            </inertia-link>
            <inertia-link :href="'/' + $page.props.auth.company.id + '/people'" class="nav-link" data-cy="header-people-link" :class="{ 'is-active': isActive('/people') || isActive('/company') || isActive('/employees') || isActive('/teams') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 3.5-6 8-6s8 2 8 6" /></svg>
              {{ $t('app.header_people') }}
            </inertia-link>
            <inertia-link :href="'/' + $page.props.auth.company.id + '/time-and-pay'" class="nav-link" data-cy="header-time-and-pay-link" :class="{ 'is-active': isActive('/time-and-pay') || isActive('/dashboard/timesheet') || isActive('/dashboard/manager') || isActive('/dashboard/hr') || isActive('/dashboard/expenses') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg>
              {{ $t('app.header_time_and_pay') }}
            </inertia-link>
            <inertia-link v-if="$page.props.auth.employee.permission_level < 300" :href="'/' + $page.props.auth.company.id + '/recruiting/job-openings'" class="nav-link" :class="{ 'is-active': isActive('/recruiting') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><rect x="3" y="8" width="18" height="12" rx="1.5" /><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /><path d="M3 13h18" /></svg>
              {{ $t('app.header_recruiting') }}
            </inertia-link>
            <inertia-link v-if="$page.props.auth.employee.permission_level <= 100" :href="'/' + $page.props.auth.company.id + '/compliance'" class="nav-link" data-cy="header-compliance-link" :class="{ 'is-active': isActive('/compliance') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><path d="M12 3 5 6v5c0 4.4 3 7.7 7 9 4-1.3 7-4.6 7-9V6l-7-3Z" /><path d="M9.5 12 11 13.5 14.5 10" /></svg>
              {{ $t('app.header_compliance') }}
            </inertia-link>
            <a data-cy="header-find-link" class="nav-link pointer" @click="showFindModal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><circle cx="10.5" cy="10.5" r="6.5" /><path d="m20 20-4.8-4.8" /></svg>
              {{ $t('app.header_find') }}
            </a>
            <inertia-link v-if="$page.props.auth.company && $page.props.auth.employee.permission_level <= 200" :href="'/' + $page.props.auth.company.id + '/account'" data-cy="header-adminland-link" class="nav-link" :class="{ 'is-active': isActive('/account') }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
                   stroke-linejoin="round"
              ><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z" /></svg>
              {{ $t('app.header_settings') }}
            </inertia-link>
          </div>
        </div>
        <div class="py-2 flex items-center gap-1">
          <theme-toggle />

          <notifications-component :notifications="notifications" />

          <user-menu :show-help-on-page="showHelpOnPage" />
        </div>
      </nav>
    </div>

    <!-- FIND BOX -->
    <div v-show="modalFind" class="absolute z-max find-box">
      <div class="br2 bg-white tl pv3 ph3 bounceIn faster" @click.prevent="">
        <form @submit.prevent="search">
          <div class="relative">
            <input id="search" ref="search" v-model="form.searchTerm" type="text" name="search"
                   :placeholder="$t('app.header_search_placeholder')" class="br2 f5 w-100 ba b--black-40 pa2 outline-0" required @keydown.esc="modalFind = false" @keyup="search"
            />
            <ball-pulse-loader v-if="processingSearch" color="#5c7575" size="7px" />
            <loading-button :class="'btn add w-auto-ns w-100 mb2 pv2 ph3 absolute top-0 right-0'" :state="loadingState" :text="$t('app.search')" :cypress-selector="'header-find-submit'" />
          </div>
        </form>

        <!-- Search results -->
        <ul v-show="dataReturnedFromSearch" class="pl0 list ma0 mt3" data-cy="results">
          <!-- Employees -->
          <li class="b mb3">
            <span class="f6 mb2 dib">
              {{ $t('app.header_search_employees') }}
            </span>
            <ul v-if="employees.length > 0" class="list ma0 pl0">
              <li v-for="localEmployee in employees" :key="localEmployee.id" class="mb2">
                <inertia-link :href="'/' + $page.props.auth.company.id + '/employees/' + localEmployee.id">
                  {{ localEmployee.name }}
                </inertia-link>
              </li>
            </ul>
            <div v-else class="silver">
              {{ $t('app.header_search_no_employee_found') }}
            </div>
          </li>

          <!-- Teams -->
          <li class="fw5">
            <span class="f6 mb2 dib">
              {{ $t('app.header_search_teams') }}
            </span>
            <ul v-if="teams.length > 0" class="list ma0 pl0">
              <li v-for="team in teams" :key="team.id" class="mb2">
                <inertia-link :href="'/' + $page.props.auth.company.id + '/teams/' + team.id">
                  {{ team.name }}
                </inertia-link>
              </li>
            </ul>
            <div v-else class="silver">
              {{ $t('app.header_search_no_team_found') }}
            </div>
          </li>
        </ul>
      </div>
    </div>

    <!-- MOBILE MENU -->
    <header class="mobile-nav dn-ns mb3 relative">
      <div class="ph3 pv2 w-100 flex items-center justify-between">
        <inertia-link href="/home" class="flex items-center gap-2 no-underline">
          <span class="brand-mark h-6 w-6 rounded-md" style="display:inline-block"></span>
          <span class="font-display font-extrabold uppercase tracking-tight text-lg leading-none" style="color: var(--color-ink)">LaunchHR</span>
        </inertia-link>

        <div class="flex items-center gap-1">
          <theme-toggle v-if="!noMenu" />
          <notifications-component v-if="!noMenu" :notifications="notifications" />
          <span v-if="!noMenu" class="mobile-nav-toggle" data-cy="mobile-nav-toggle" @click="mobileMenuOpen = !mobileMenuOpen">
            <svg v-if="!mobileMenuOpen" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                 stroke-linecap="round" stroke-linejoin="round"
            >
              <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"
                 stroke-linecap="round" stroke-linejoin="round"
            >
              <path d="M6 6l12 12M18 6 6 18" />
            </svg>
          </span>
        </div>
      </div>

      <div v-if="!noMenu && mobileMenuOpen" class="mobile-nav-panel">
        <inertia-link v-if="$page.props.auth.employee.display_welcome_message" :href="'/' + $page.props.auth.company.id + '/welcome'" class="mobile-nav-link" :class="{ 'is-active': isActive('/welcome') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><path d="M7 11V7a2 2 0 0 1 4 0v3" /><path d="M11 10.5V6a2 2 0 0 1 4 0v5" /><path d="M15 10.5V8a2 2 0 0 1 4 0v6a6 6 0 0 1-6 6h-2a6 6 0 0 1-5-2.7L4 13a1.5 1.5 0 0 1 2.5-1.7L7 12" /></svg>
          {{ $t('app.header_welcome') }}
        </inertia-link>
        <inertia-link :href="'/' + $page.props.auth.company.id + '/dashboard'" class="mobile-nav-link" :class="{ 'is-active': isActive('/dashboard') || isActive('/home') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><path d="M4 11.5 12 4l8 7.5" /><path d="M6 10v9a1 1 0 0 0 1 1h3v-5h4v5h3a1 1 0 0 0 1-1v-9" /></svg>
          {{ $t('app.header_home') }}
        </inertia-link>
        <inertia-link :href="'/' + $page.props.auth.company.id + '/people'" class="mobile-nav-link" :class="{ 'is-active': isActive('/people') || isActive('/company') || isActive('/employees') || isActive('/teams') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 3.5-6 8-6s8 2 8 6" /></svg>
          {{ $t('app.header_people') }}
        </inertia-link>
        <inertia-link :href="'/' + $page.props.auth.company.id + '/time-and-pay'" class="mobile-nav-link" :class="{ 'is-active': isActive('/time-and-pay') || isActive('/dashboard/timesheet') || isActive('/dashboard/manager') || isActive('/dashboard/hr') || isActive('/dashboard/expenses') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 3" /></svg>
          {{ $t('app.header_time_and_pay') }}
        </inertia-link>
        <inertia-link v-if="$page.props.auth.employee.permission_level < 300" :href="'/' + $page.props.auth.company.id + '/recruiting/job-openings'" class="mobile-nav-link" :class="{ 'is-active': isActive('/recruiting') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><rect x="3" y="8" width="18" height="12" rx="1.5" /><path d="M8 8V6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /><path d="M3 13h18" /></svg>
          {{ $t('app.header_recruiting') }}
        </inertia-link>
        <inertia-link v-if="$page.props.auth.employee.permission_level <= 100" :href="'/' + $page.props.auth.company.id + '/compliance'" class="mobile-nav-link" :class="{ 'is-active': isActive('/compliance') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><path d="M12 3 5 6v5c0 4.4 3 7.7 7 9 4-1.3 7-4.6 7-9V6l-7-3Z" /><path d="M9.5 12 11 13.5 14.5 10" /></svg>
          {{ $t('app.header_compliance') }}
        </inertia-link>
        <a class="mobile-nav-link pointer" @click="mobileMenuOpen = false; showFindModal()">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><circle cx="10.5" cy="10.5" r="6.5" /><path d="m20 20-4.8-4.8" /></svg>
          {{ $t('app.header_find') }}
        </a>
        <inertia-link v-if="$page.props.auth.company && $page.props.auth.employee.permission_level <= 200" :href="'/' + $page.props.auth.company.id + '/account'" class="mobile-nav-link" :class="{ 'is-active': isActive('/account') }" @click="mobileMenuOpen = false">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"
               stroke-linejoin="round"
          ><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z" /></svg>
          {{ $t('app.header_settings') }}
        </inertia-link>
        <div class="mobile-nav-utility">
          <user-menu :show-help-on-page="showHelpOnPage" />
        </div>
      </div>
    </header>

    <div :class="[ modalFind ? 'bg-modal-find' : '' ]" @click.prevent="modalFind = false"></div>

    <main>
      <slot></slot>
    </main>

    <toaster />

    <ai-assistant v-if="!noMenu && $page.props.auth.company" />

    <div class="mt5 mb4 cf mw7 center tc f7">
      <ul class="list ma0">
        <li class="di">Thanks for using LaunchHR!</li>
      </ul>
    </div>
  </div>
</template>

<script>
import UserMenu from '@/Shared/UserMenu';
import LoadingButton from '@/Shared/LoadingButton';
import NotificationsComponent from '@/Shared/Notifications';
import Toaster from '@/Shared/Toaster';
import AiAssistant from '@/Shared/AiAssistant';
import ThemeToggle from '@/Shared/ThemeToggle';
import BallPulseLoader from 'vue-loaders/dist/loaders/ball-pulse';

export default {
  components: {
    UserMenu,
    LoadingButton,
    NotificationsComponent,
    Toaster,
    AiAssistant,
    ThemeToggle,
    'ball-pulse-loader': BallPulseLoader.component,
  },

  props: {
    title: {
      type: String,
      default: '',
    },
    noMenu: {
      type: Boolean,
      default: false,
    },
    notifications: {
      type: Array,
      default: null,
    },
    showHelpOnPage: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      submit: null,
      loadingState: '',
      modalFind: false,
      mobileMenuOpen: false,
      showModalNotifications: true,
      dataReturnedFromSearch: false,
      processingSearch: false,
      form: {
        searchTerm: null,
        errors: [],
      },
      employees: [],
      teams: [],
      cache: [],
    };
  },

  watch: {
    title(title) {
      this.updatePageTitle(title);
    }
  },

  mounted() {
    this.updatePageTitle(this.title);
    this.submit = _.debounce((text) => {
      const vm = this;
      vm.processingSearch = true;
      vm.loadingState = 'loading';

      Promise.all([
        axios.post('/search/employees', { searchTerm: text }),
        axios.post('/search/teams', { searchTerm: text })
      ])
        .then(results => {
          vm.cache[text] = {
            employees: results[0].data.data,
            teams: results[1].data.data,
          };
          vm.displayItems(text);
        })
        .catch(error => {
          vm.loadingState = null;
          vm.processingSearch = false;
          vm.form.errors = error.response.data;
        });
    }, 500);
  },


  methods: {
    isActive(path) {
      return this.$page.url.includes(path);
    },

    updatePageTitle(title) {
      document.title = title ? `${title} | LaunchHR` : 'LaunchHR';
    },

    showFindModal() {
      this.dataReturnedFromSearch = false;
      this.form.searchTerm = null;
      this.employees = [];
      this.teams = [];
      this.modalFind = !this.modalFind;

      this.$nextTick(() => {
        this.$refs.search.focus();
      });
    },

    search: function () {
      let text = _.trim(this.form.searchTerm);
      if (text === null || text === undefined || text === '') {
        return;
      }

      if (this.cache[text] === undefined) {
        this.submit(text);
      } else {
        this.submit.cancel();
        this.displayItems(text);
      }
    },

    displayItems (text) {
      var data = this.cache[text];

      this.dataReturnedFromSearch = true;
      this.processingSearch = false;
      this.loadingState = null;

      this.employees = data.employees;
      this.teams = data.teams;
    },
  },
};
</script>
