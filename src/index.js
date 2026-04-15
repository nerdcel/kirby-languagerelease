import LanguageRelease from './components/LanguageRelease.vue';
import LanguageReleaseSection from './components/LanguageReleaseSection.vue';
import LanguageReleaseTreeSection from './components/LanguageReleaseTreeSection.vue';

panel.plugin('nerdcel/languagerelease', {
  components: {
    'k-languagerelease-view-button': LanguageRelease,
  },
  sections: {
    languagerelease: LanguageReleaseSection,
    'languagerelease-tree': LanguageReleaseTreeSection,
  },
});
