<template>
  <div v-if="currentLanguage !== defaultLanguage">
    <k-button
      :icon="released ? 'check' : 'globe'"
      variant="filled"
      :theme="released ? 'positive' : 'love'"
      size="sm"
      :dialog="dialog"
    >
      {{ !released ? $t('nerdcel.languagerelease.release-language') : $t('nerdcel.languagerelease.unrelease-language') }}
    </k-button>
  </div>
</template>

<script>
export default {
  props: {
    defaultLanguage: {
      type: String,
      required: true,
    },
    currentLanguage: {
      type: String,
      required: true,
    },
    languages: {
      type: Array,
      required: true,
    },
    released: {
      type: Boolean,
      required: true,
    },
    dialog: {
      type: String,
      required: true,
    },
  },
  data () {
    return {
      toggle: false,
    };
  },
  mounted () {
    this.toggle = this.released;
  },
};
</script>
