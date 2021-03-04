const yargs = require("yargs")
const Parser = require('papaparse')
const yaml = require('js-yaml')
fs = require('fs')

const argv = yargs
  .usage('Usage : $0 <CSV translations>')
  .demandCommand(1)
  .argv

const csv_path = argv._[0]

function output_translations(translations, locale, key) {
  const output_path = `translations/${key}.${locale}.yml`
  console.log(`Output to ${output_path}`)
  const yamlStr = yaml.dump(translations[locale][key])
  fs.writeFileSync(output_path, yamlStr, 'utf8')
}

console.log(`Parsing ${csv_path}`)

fs.readFile(csv_path, 'utf8', function (err, data) {
  items = Parser.parse(data, { header: true, skipEmptyLines: true })
  translations = items.data.reduce((acc, item) => {
    acc.en.tables[item.table] = item.table_en
    acc.fr.tables[item.table] = item.table_fr

    if (!(item.table in acc.en.fields)) {
      acc.en.fields[item.table] = {}
      acc.fr.fields[item.table] = {}
    }
    acc.en.fields[item.table][item.field] = item.field_en
    acc.fr.fields[item.table][item.field] = item.field_fr

    return acc
  }, {
    en: {
      tables: {}, fields: {}
    },
    fr: {
      tables: {}, fields: {}
    },
  })

  const locales = ["en", "fr"]
  locales.forEach(locale => {
    ['tables', 'fields'].forEach(key => {
      output_translations(translations, locale, key)
    })
  });
})
