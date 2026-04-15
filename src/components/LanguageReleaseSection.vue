<template>
  <k-section :label="label">
    <table class="k-languagerelease-table">
      <thead>
        <tr>
          <th>{{ $t('nerdcel.languagerelease.section-language') }}</th>
          <th>{{ $t('nerdcel.languagerelease.section-code') }}</th>
          <th>{{ $t('nerdcel.languagerelease.section-status') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="lang in languages"
          :key="lang.code"
          class="k-languagerelease-row"
        >
          <td>
            <span class="k-languagerelease-name">{{ lang.name }}</span>
          </td>
          <td>
            <code class="k-languagerelease-code">{{ lang.code }}</code>
          </td>
          <td>
            <span
              v-if="lang.isDefault"
              class="k-languagerelease-badge k-languagerelease-badge--default"
            >
              {{ $t('nerdcel.languagerelease.section-default') }}
            </span>
            <span
              v-else-if="lang.released"
              class="k-languagerelease-badge k-languagerelease-badge--released"
            >
              {{ $t('nerdcel.languagerelease.section-released') }}
            </span>
            <span
              v-else
              class="k-languagerelease-badge k-languagerelease-badge--unreleased"
            >
              {{ $t('nerdcel.languagerelease.section-unreleased') }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>
  </k-section>
</template>

<script>
export default {
  mixins: ['section'],
  data() {
    return {
      label: '',
      languages: [],
    };
  },
  created() {
    this.fetch();
  },
  watch: {
    // Reload when the parent model changes (e.g. language switch)
    timestamp() {
      this.fetch();
    },
  },
  methods: {
    async fetch() {
      const response = await this.load();
      this.label = response.label || '';
      this.languages = response.languages || [];
    },
  },
};
</script>

<style>
.k-languagerelease-table {
  width: 100%;
  border-collapse: collapse;
  font-size: var(--text-sm);
  line-height: 1.5;
}

.k-languagerelease-table th {
  text-align: left;
  padding: var(--spacing-2) var(--spacing-3);
  font-weight: 600;
  color: var(--color-text-dimmed);
  border-bottom: 1px solid var(--color-border);
}

.k-languagerelease-row td {
  padding: var(--spacing-2) var(--spacing-3);
  border-bottom: 1px solid var(--color-border);
}

.k-languagerelease-row:last-child td {
  border-bottom: none;
}

.k-languagerelease-name {
  font-weight: 500;
}

.k-languagerelease-code {
  font-size: var(--text-xs);
  background: var(--color-background);
  padding: 2px 6px;
  border-radius: var(--rounded-sm);
}

.k-languagerelease-badge {
  display: inline-flex;
  align-items: center;
  gap: var(--spacing-1);
  font-size: var(--text-xs);
  font-weight: 500;
  padding: 2px 8px;
  border-radius: var(--rounded);
}

.k-languagerelease-badge::before {
  content: '';
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.k-languagerelease-badge--default {
  color: var(--color-text-dimmed);
}
.k-languagerelease-badge--default::before {
  background: var(--color-text-dimmed);
}

.k-languagerelease-badge--released {
  color: var(--color-green-550);
}
.k-languagerelease-badge--released::before {
  background: var(--color-green-550);
}

.k-languagerelease-badge--unreleased {
  color: var(--color-red-550);
}
.k-languagerelease-badge--unreleased::before {
  background: var(--color-red-550);
}
</style>

