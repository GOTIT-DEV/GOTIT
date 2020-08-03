import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n)

function loadLocaleMessages() {
  const locales = require.context('../../../translations', true, /[A-Za-z0-9-_,\s]+\.ya?ml$/i)
  const messages = {}
  locales.keys().forEach(key => {
    const matched = key.match(/([A-Za-z0-9-_]+)\.([a-z]+)\./i)
    if (matched && matched.length > 1) {
      const locale = matched[2]
      const namespace = matched[1]
      messages[locale] = { ...messages[locale], [namespace]: locales(key) }
    }
  })
  return messages
}

export default new VueI18n({
  locale: Translator.locale,
  fallbackLocale: 'en',
  messages: loadLocaleMessages()
})

