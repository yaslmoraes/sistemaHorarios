import { createApp, h } from 'vue'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'

import App from './components/App.vue'

import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'
import { pt } from 'vuetify/locale'

const customPt = {
  ...pt,
  dataIterator: {
    ...pt.dataIterator,
    itemsPerPage: 'Itens por página:', 
  }
}

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi',
  },
  locale: {
    locale: 'pt',
    messages: {
      pt: customPt,
    },
  },
})


const app = createApp({
  render: () => h(App)
})

app.use(vuetify)

app.mount('#app')

