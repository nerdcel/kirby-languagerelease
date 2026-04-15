<template>
  <k-section :label="label">
    <table class="k-languagerelease-tree-table">
      <thead>
        <tr>
          <th class="k-languagerelease-tree-page-col">
            {{ $t('nerdcel.languagerelease.tree-page') }}
          </th>
          <th
            v-for="lang in languages"
            :key="lang.code"
            class="k-languagerelease-tree-lang-col"
          >
            {{ lang.name }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="node in visiblePages"
          :key="node.id"
          class="k-languagerelease-tree-row"
        >
          <td>
            <div
              class="k-languagerelease-tree-page-cell"
              :style="{ paddingLeft: node.depth * 20 + 'px' }"
            >
              <button
                v-if="node.hasChildren"
                class="k-languagerelease-tree-toggle"
                @click.prevent="toggleNode(node.id)"
              >
                <k-icon :type="expandedIds[node.id] ? 'angle-down' : 'angle-right'" />
              </button>
              <span v-else class="k-languagerelease-tree-toggle-placeholder" />
              <a
                class="k-languagerelease-tree-page-link"
                :href="node.url"
                @click.prevent="$panel.open(node.url)"
              >
                {{ node.title }}
              </a>
            </div>
          </td>
          <td
            v-for="lang in languages"
            :key="lang.code"
            class="k-languagerelease-tree-status-cell"
          >
            <span
              v-if="node.statuses[lang.code]"
              class="k-languagerelease-tree-dot"
              :class="dotClass(node.statuses[lang.code])"
              :title="dotTitle(lang, node.statuses[lang.code])"
            />
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
      pages: [],
      expandedIds: {},
    };
  },
  computed: {
    visiblePages() {
      const visible = [];
      const collapsedParents = new Set();

      for (const page of this.pages) {
        // Check if any ancestor is collapsed
        let hidden = false;
        if (page.parentId) {
          let checkId = page.parentId;
          while (checkId) {
            if (collapsedParents.has(checkId) || !this.expandedIds[checkId]) {
              hidden = true;
              break;
            }
            const parent = this.pages.find((p) => p.id === checkId);
            checkId = parent ? parent.parentId : null;
          }
        }

        if (!hidden) {
          visible.push(page);
        }

        if (page.hasChildren && !this.expandedIds[page.id]) {
          collapsedParents.add(page.id);
        }
      }

      return visible;
    },
  },
  created() {
    this.fetch();
  },
  watch: {
    timestamp() {
      this.fetch();
    },
  },
  methods: {
    async fetch() {
      const response = await this.load();
      this.label = response.label || '';
      this.languages = response.languages || [];
      this.pages = response.pages || [];

      // Auto-expand top-level pages (depth 0 with children)
      const expanded = {};
      this.pages.forEach((page) => {
        if (page.hasChildren && page.depth === 0) {
          expanded[page.id] = true;
        }
      });
      this.expandedIds = expanded;
    },
    toggleNode(id) {
      if (this.expandedIds[id]) {
        const copy = { ...this.expandedIds };
        delete copy[id];
        this.expandedIds = copy;
      } else {
        this.expandedIds = { ...this.expandedIds, [id]: true };
      }
    },
    dotClass(status) {
      if (status.isDefault) return 'k-languagerelease-tree-dot--default';
      if (status.released) return 'k-languagerelease-tree-dot--released';
      return 'k-languagerelease-tree-dot--unreleased';
    },
    dotTitle(lang, status) {
      if (status.isDefault) {
        return lang.name + ': ' + this.$t('nerdcel.languagerelease.section-default');
      }
      if (status.released) {
        return lang.name + ': ' + this.$t('nerdcel.languagerelease.section-released');
      }
      return lang.name + ': ' + this.$t('nerdcel.languagerelease.section-unreleased');
    },
  },
};
</script>

<style>
.k-languagerelease-tree-table {
  width: 100%;
  border-collapse: collapse;
  font-size: var(--text-sm);
  line-height: 1.5;
}

.k-languagerelease-tree-table th {
  text-align: left;
  padding: var(--spacing-2) var(--spacing-3);
  font-weight: 600;
  color: var(--color-text-dimmed);
  border-bottom: 1px solid var(--color-border);
  white-space: nowrap;
}

.k-languagerelease-tree-page-col {
  width: 40%;
}

.k-languagerelease-tree-lang-col {
  text-align: center !important;
}

.k-languagerelease-tree-table td {
  padding: var(--spacing-2) var(--spacing-3);
  border-bottom: 1px solid var(--color-border);
}

.k-languagerelease-tree-table tbody tr:last-child td {
  border-bottom: none;
}

.k-languagerelease-tree-page-cell {
  display: flex;
  align-items: center;
  gap: var(--spacing-1);
}

.k-languagerelease-tree-toggle {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border: none;
  background: none;
  cursor: pointer;
  padding: 0;
  color: var(--color-text-dimmed);
  border-radius: var(--rounded-sm);
  flex-shrink: 0;
}

.k-languagerelease-tree-toggle:hover {
  background: var(--color-background);
  color: var(--color-text);
}

.k-languagerelease-tree-toggle-placeholder {
  display: inline-block;
  width: 20px;
  flex-shrink: 0;
}

.k-languagerelease-tree-page-link {
  color: var(--color-text);
  text-decoration: none;
  font-weight: 500;
}

.k-languagerelease-tree-page-link:hover {
  color: var(--color-focus);
}

.k-languagerelease-tree-status-cell {
  text-align: center;
}

.k-languagerelease-tree-dot {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.k-languagerelease-tree-dot--default {
  background: var(--color-text-dimmed);
}

.k-languagerelease-tree-dot--released {
  background: var(--color-green-550);
}

.k-languagerelease-tree-dot--unreleased {
  background: var(--color-red-550);
}
</style>

