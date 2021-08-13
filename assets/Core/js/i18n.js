import Vue from "vue";
import VueI18n from "vue-i18n";

Vue.use(VueI18n);

function loadLocaleMessages() {
  const locales = require.context(
    "~Translations",
    true,
    /[A-Za-z0-9-_,\s]+\.ya?ml$/i
  );
  const messages = {};
  locales.keys().forEach((key) => {
    const matched = key.match(/([A-Za-z0-9-_]+)\.([a-z]+)\./i);
    if (matched && matched.length > 1) {
      const locale = matched[2];
      const namespace = matched[1];
      const localeMessages = messages[locale];
      const namespaceMessages =
        localeMessages && localeMessages[namespace]
          ? localeMessages[namespace]
          : {};
      messages[locale] = {
        ...localeMessages,
        [namespace]: { ...namespaceMessages, ...locales(key) },
      };
    }
  });
  return messages;
}

export default new VueI18n({
  locale: Translator.locale,
  fallbackLocale: "en",
  messages: loadLocaleMessages(),
});
